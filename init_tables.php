<?php
require_once 'config.php';

$sql = "
DROP TABLE IF EXISTS KUSIMUSED;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: KUSIMUSED." . $mysqli->error);
}

$sql = "
DROP TABLE IF EXISTS HAALETAJAD;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: HAALETAJAD." . $mysqli->error);
}


$sql = "
DROP TABLE IF EXISTS HAALETUS;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: HAALETUS." . $mysqli->error);
}


$sql = "
DROP TABLE IF EXISTS TULEMUSED;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: TULEMUS." . $mysqli->error);
}


$sql = "
DROP TABLE IF EXISTS LOGI;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: LOGI." . $mysqli->error);
}


$sql = "
CREATE TABLE IF NOT EXISTS KUSIMUSED (
    Kusimus_id INT AUTO_INCREMENT PRIMARY KEY,
    Kusimus VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    H_alguse_aeg DATETIME NOT NULL
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating KUSIMUSED table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS HAALETAJAD (
    Haaletaja_id INT AUTO_INCREMENT PRIMARY KEY,
    Eesnimi VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    Perenimi VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating HAALETAJAD table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS HAALETUS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Haaletaja_id INT NOT NULL,
    Kusimus_id INT NOT NULL,
    Haaletuse_aeg DATETIME NOT NULL,
    Otsus ENUM('poolt', 'vastu') NOT NULL,
    FOREIGN KEY (Haaletaja_id) REFERENCES HAALETAJAD(Haaletaja_id) ON DELETE CASCADE,
    FOREIGN KEY (Kusimus_id) REFERENCES KUSIMUSED(Kusimus_id) ON DELETE CASCADE
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating HAALETUS table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS TULEMUSED (
    Kusimus_id INT PRIMARY KEY,
    Haaletajate_arv INT NOT NULL,    
    H_alguse_aeg DATETIME NOT NULL,
    Poolt INT,
    Vastu INT,
    FOREIGN KEY (Kusimus_id) REFERENCES KUSIMUSED(Kusimus_id) ON DELETE CASCADE
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating TULEMUSED table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS LOGI (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Haaletaja_id INT NOT NULL,
    Kusimus_id INT NOT NULL,
    H_alguse_aeg DATETIME NOT NULL,
    Haale_andmise_aeg DATETIME NOT NULL,
    Haale_suund ENUM('poolt', 'vastu') NOT NULL,
    FOREIGN KEY (Haaletaja_id) REFERENCES HAALETAJAD(Haaletaja_id),
    FOREIGN KEY (Kusimus_id) REFERENCES KUSIMUSED(Kusimus_id)
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