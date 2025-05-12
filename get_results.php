<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$imageIndex = (int)($data['imageIndex'] ?? 0);

$result = $mysqli->query("SELECT Pildi_id FROM PILDID LIMIT 1 OFFSET $imageIndex");
if (!$row = $result->fetch_assoc()) {
    http_response_code(404);
    echo json_encode(["error" => "Image not found"]);
    exit;
}
$pildi_id = $row['Pildi_id'];

$stmt = $mysqli->prepare("SELECT AI, Paris FROM TULEMUSED WHERE Pildi_id = ?");
$stmt->bind_param("i", $pildi_id);
$stmt->execute();
$res = $stmt->get_result();

if ($votes = $res->fetch_assoc()) {
    echo json_encode($votes);
} else {
    echo json_encode(["AI" => 0, "Paris" => 0]); // Default if no votes yet
}
