<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$imageIndex = (int)($data['imageIndex'] ?? -1);
$choice = $data['choice'] ?? '';

if (!$name || $imageIndex < 0 || !in_array($choice, ['AI', 'Paris'])) {
    http_response_code(400);
    echo "Invalid input";
    exit;
}

list($firstName, $lastName) = explode(' ', $name, 2);

// 1. Get Haaletaja_id
$stmt = $mysqli->prepare("SELECT Haaletaja_id FROM HAALETAJAD WHERE Eesnimi = ? AND Perenimi = ?");
$stmt->bind_param("ss", $firstName, $lastName);
$stmt->execute();
$result = $stmt->get_result();

if (!$user = $result->fetch_assoc()) {
    http_response_code(404);
    echo "User not found";
    exit;
}
$haaletaja_id = $user['Haaletaja_id'];

// 2. Get Pildi_id from index
$result = $mysqli->query("SELECT Pildi_id FROM PILDID LIMIT 1 OFFSET $imageIndex");
if (!$image = $result->fetch_assoc()) {
    http_response_code(404);
    echo "Image not found";
    exit;
}
$pildi_id = $image['Pildi_id'];

// 3. Call CAST_VOTE stored procedure
try {
    $sql = "CALL CAST_VOTE(?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iis", $haaletaja_id, $pildi_id, $choice);
    $stmt->execute();

    echo "OK";
} catch (mysqli_sql_exception $e) {
    // Check if this is the timeout signal
    if (strpos($e->getMessage(), 'Voting period has expired') !== false) {
        echo "TOO_LATE";
    } else {
        error_log("CAST_VOTE error: " . $e->getMessage());
        http_response_code(500);
        echo "DB error";
    }
}
