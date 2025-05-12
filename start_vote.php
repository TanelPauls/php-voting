<?php
include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);
$imageIndex = (int)$data['imageIndex'];

$result = $mysqli->query("SELECT Pildi_id FROM PILDID LIMIT 1 OFFSET $imageIndex");

if ($row = $result->fetch_assoc()) {
    $pildi_id = $row['Pildi_id'];
    $stmt = $mysqli->prepare("UPDATE PILDID SET H_alguse_aeg = NOW() WHERE Pildi_id = ?");
    $stmt->bind_param("i", $pildi_id);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "DB_ERROR";
    }
} else {
    http_response_code(400);
    echo "INVALID_INDEX";
}
?>
