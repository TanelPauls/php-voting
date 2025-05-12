<?php
include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$imageIndex = (int)($data['imageIndex'] ?? 0);

if (!$name || $imageIndex < 0) {
    http_response_code(400);
    echo "Missing input";
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

// Check if vote already exists
$stmt = $mysqli->prepare("SELECT id FROM HAALETUS WHERE Haaletaja_id=? AND Pildi_id=?");
$stmt->bind_param("ii", $haaletaja_id, $pildi_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->fetch_assoc()) {
    echo "Already started";
    exit;
}

// Insert new voting session
$stmt = $mysqli->prepare("INSERT INTO HAALETUS (Haaletaja_id, Pildi_id, H_alguse_aeg, Haaletuse_aeg, Otsus)
                          VALUES (?, ?, NOW(), NULL, NULL)");
$stmt->bind_param("ii", $haaletaja_id, $pildi_id);
if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "DB error";
}
