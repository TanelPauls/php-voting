<?php
include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);

$eesnimi = $mysqli->real_escape_string($data['eesnimi']);
$perenimi = $mysqli->real_escape_string($data['perenimi']);

// Check if user already exists
$check = $mysqli->prepare("SELECT Haaletaja_id FROM HAALETAJAD WHERE Eesnimi = ? AND Perenimi = ?");
$check->bind_param("ss", $eesnimi, $perenimi);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    // Insert only if not found
    $stmt = $mysqli->prepare("INSERT INTO HAALETAJAD (Eesnimi, Perenimi) VALUES (?, ?)");
    $stmt->bind_param("ss", $eesnimi, $perenimi);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo "Viga andmebaasi salvestamisel.";
        exit;
    }
}

echo "OK";
?>
