<?php
include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$imageIndex = (int)($data['imageIndex'] ?? 0);
$choice = $data['choice'] ?? '';

if (!$name || $imageIndex < 0 || !in_array($choice, ['AI', 'Paris'])) {
    http_response_code(400);
    echo "Invalid input";
    exit;
}

list($firstName, $lastName) = explode(' ', $name, 2);

// Get user ID
$stmt = $mysqli->prepare("SELECT Haaletaja_id FROM HAALETAJAD WHERE Eesnimi=? AND Perenimi=?");
$stmt->bind_param("ss", $firstName, $lastName);
$stmt->execute();
$res = $stmt->get_result();
if (!$user = $res->fetch_assoc()) {
    http_response_code(404);
    echo "User not found";
    exit;
}
$haaletaja_id = $user['Haaletaja_id'];

// Get image ID
$res = $mysqli->query("SELECT Pildi_id FROM PILDID LIMIT 1 OFFSET $imageIndex");
if (!$image = $res->fetch_assoc()) {
    http_response_code(404);
    echo "Image not found";
    exit;
}
$pildi_id = $image['Pildi_id'];

// Get voting record
$stmt = $mysqli->prepare("SELECT id, H_alguse_aeg FROM HAALETUS WHERE Haaletaja_id=? AND Pildi_id=?");
$stmt->bind_param("ii", $haaletaja_id, $pildi_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    http_response_code(403);
    echo "Vote not started";
    exit;
}

$start = strtotime($row['H_alguse_aeg']);
$now = time();
if ($now - $start > 300) {
    echo "TOO_LATE";
    exit;
}

// Update vote
$stmt = $mysqli->prepare("UPDATE HAALETUS SET Haaletuse_aeg=NOW(), Otsus=? WHERE id=?");
$stmt->bind_param("si", $choice, $row['id']);
if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "DB error";
}
