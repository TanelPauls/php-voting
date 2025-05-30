<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$imageIndex = (int)($data['imageIndex'] ?? -1);
$choice = $data['choice'] ?? '';

if (!$name || $imageIndex < 0 || !in_array($choice, ['AI', 'Paris'])) {
    http_response_code(400);
    echo "Missing or invalid input";
    exit;
}

list($firstName, $lastName) = explode(' ', $name, 2);

// 1. Get Haaletaja_id
$stmt = $mysqli->prepare("SELECT Haaletaja_id FROM HAALETAJAD WHERE Eesnimi = ? AND Perenimi = ?");
$stmt->bind_param("ss", $firstName, $lastName);
$stmt->execute();
$res = $stmt->get_result();
if (!$user = $res->fetch_assoc()) {
    http_response_code(404);
    echo "User not found";
    exit;
}
$haaletaja_id = $user['Haaletaja_id'];

// 2. Get Pildi_id
$res = $mysqli->query("SELECT Pildi_id FROM PILDID LIMIT 1 OFFSET $imageIndex");
if (!$image = $res->fetch_assoc()) {
    http_response_code(404);
    echo "Image not found";
    exit;
}
$pildi_id = $image['Pildi_id'];

// 3. Call CAST_VOTE with real choice
try {
    $stmt = $mysqli->prepare("CALL CAST_VOTE(?, ?, ?)");
    $stmt->bind_param("iis", $haaletaja_id, $pildi_id, $choice);
    $stmt->execute();
    echo "OK";
} catch (mysqli_sql_exception $e) {
    $errorMessage = $e->getMessage();

    if (strpos($errorMessage, 'Voting period expired') !== false) {
        echo "TOO_LATE";
    } elseif (strpos($errorMessage, 'Hääletus lõppenud') !== false) {
        echo $errorMessage;
    } else {
        error_log("CAST_VOTE error: " . $errorMessage);
        http_response_code(500);
        echo "DB error";
    }
}
