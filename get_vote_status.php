<?php
include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$imageIndex = (int)($data['imageIndex'] ?? 0);

if (!$name || $imageIndex < 0) {
    http_response_code(400);
    echo json_encode(["error" => "Missing input"]);
    exit;
}

list($firstName, $lastName) = explode(' ', $name, 2);
if (!$firstName || !$lastName) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid name"]);
    exit;
}

// Get user ID
$stmt = $mysqli->prepare("SELECT Haaletaja_id FROM HAALETAJAD WHERE Eesnimi=? AND Perenimi=?");
$stmt->bind_param("ss", $firstName, $lastName);
$stmt->execute();
$result = $stmt->get_result();
if (!$user = $result->fetch_assoc()) {
    echo json_encode(["status" => "no_user"]);
    exit;
}
$haaletaja_id = $user['Haaletaja_id'];

// Get Pildi_id by index
$result = $mysqli->query("SELECT Pildi_id FROM PILDID LIMIT 1 OFFSET $imageIndex");
if (!$image = $result->fetch_assoc()) {
    echo json_encode(["status" => "no_image"]);
    exit;
}
$pildi_id = $image['Pildi_id'];

// Check if vote started
$stmt = $mysqli->prepare("
    SELECT H_alguse_aeg, Otsus
    FROM HAALETUS
    WHERE Haaletaja_id = ? AND Pildi_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $haaletaja_id, $pildi_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    echo json_encode(["status" => "not_started"]);
} else {
    echo json_encode([
        "status" => "started",
        "start_time" => $row["H_alguse_aeg"],
        "otsus" => $row["Otsus"] ?? null
    ]);
}
