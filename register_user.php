<?php
include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);

$eesnimi = $mysqli->real_escape_string($data['eesnimi']);
$perenimi = $mysqli->real_escape_string($data['perenimi']);

$stmt = $mysqli->prepare("INSERT INTO HAALETAJAD (Eesnimi, Perenimi) VALUES (?, ?)");
$stmt->bind_param("ss", $eesnimi, $perenimi);

if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Viga andmebaasi salvestamisel.";
}
?>
