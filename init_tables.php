<?php
require_once 'config.php';

$sql = "
DROP TABLE IF EXISTS LOGI;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: LOGI." . $mysqli->error);
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
    error_log("Error deleting table: TULEMUSED." . $mysqli->error);
}

$sql = "
DROP TABLE IF EXISTS HAALETAJAD;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: HAALETAJAD." . $mysqli->error);
}

$sql = "
DROP TABLE IF EXISTS PILDID;
";

if (!$mysqli->query($sql)) {
    error_log("Error deleting table: PILDID." . $mysqli->error);
}





$sql = "
CREATE TABLE IF NOT EXISTS PILDID (
    Pildi_id INT AUTO_INCREMENT PRIMARY KEY,
    URL VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating PILDID table: " . $mysqli->error);
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
    Pildi_id INT NOT NULL,
    H_alguse_aeg DATETIME NOT NULL,
    Haaletuse_aeg DATETIME NOT NULL,
    Otsus ENUM('AI', 'Paris') NOT NULL,
    FOREIGN KEY (Haaletaja_id) REFERENCES HAALETAJAD(Haaletaja_id) ON DELETE CASCADE,
    FOREIGN KEY (Pildi_id) REFERENCES PILDID(Pildi_id) ON DELETE CASCADE
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating HAALETUS table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS TULEMUSED (
    Pildi_id INT PRIMARY KEY,
    Haaletajate_arv INT NOT NULL,    
    H_alguse_aeg DATETIME NOT NULL,
    Poolt INT,
    Vastu INT,
    FOREIGN KEY (Pildi_id) REFERENCES PILDID(Pildi_id) ON DELETE CASCADE
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating TULEMUSED table: " . $mysqli->error);
}

$sql = "
CREATE TABLE IF NOT EXISTS LOGI (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Haaletaja_id INT NOT NULL,
    Pildi_id INT NOT NULL,
    H_alguse_aeg DATETIME NOT NULL,
    Haale_andmise_aeg DATETIME NOT NULL,
    Haale_suund ENUM('poolt', 'vastu') NOT NULL,
    FOREIGN KEY (Haaletaja_id) REFERENCES HAALETAJAD(Haaletaja_id),
    FOREIGN KEY (Pildi_id) REFERENCES PILDID(Pildi_id)
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating LOGI table: " . $mysqli->error);
}

$sql = "
INSERT IGNORE INTO PILDID (URL) VALUES
('https://cdn.mos.cms.futurecdn.net/44kXT82VEHfqTG6uQ9kHVh-1200-80.jpg'),
('https://c8.alamy.com/comp/RC0T0N/funny-caricature-of-mona-lisa-painting-RC0T0N.jpg');
";

if (!$mysqli->query($sql)) {
    error_log("Error inserting to table PILDID: " . $mysqli->error);
}
?>