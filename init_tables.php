<?php
require_once 'config.php';

$sql = "
DROP TABLE IF EXISTS KUSIMUSED;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating KUSIMUSED table: " . $mysqli->error);
}
$sql = "
DROP TABLE IF EXISTS HAALETUS;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating HAALETUS table: " . $mysqli->error);
}
$sql = "
DROP TABLE IF EXISTS TULEMUSED;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating TULEMUSED table: " . $mysqli->error);
}
$sql = "
DROP TABLE IF EXISTS LOGI;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating LOGI table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS KUSIMUSED (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Kusimus VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating KUSIMUSED table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS HAALETUS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Eesnimi VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    Perenimi VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    Haaletuse_aeg TIME NOT NULL,
    Otsus INT NOT NULL
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating HAALETUS table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS TULEMUSED (
    Haaletajate_arv INT,
    H_alguse_aeg TIME NOT NULL,
    Poolt INT,
    Vastu INT
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating TULEMUSED table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS LOGI (
    Haaletaja_id INT,
    H_alguse_aeg TIME NOT NULL,
    Haale_andmise_aeg TIME NOT NULL,
    Haale_suund INT
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating LOGI table: " . $mysqli->error);
}


$sql = "
INSERT INTO KUSIMUSED (Kusimus) VALUES
('Kas peaks seda veebilehte edasi arendama?');
";

if (!$mysqli->query($sql)) {
    error_log("Error inserting to table KUSIMUSED: " . $mysqli->error);
}

?>