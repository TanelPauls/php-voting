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
    URL VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
    Oige_vastus VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE
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
    AI INT,
    Paris INT,
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
    Haale_suund ENUM('AI', 'Paris') NOT NULL,
    FOREIGN KEY (Haaletaja_id) REFERENCES HAALETAJAD(Haaletaja_id),
    FOREIGN KEY (Pildi_id) REFERENCES PILDID(Pildi_id)
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
";

if (!$mysqli->query($sql)) {
    error_log("Error creating LOGI table: " . $mysqli->error);
}

$sql = "
INSERT IGNORE INTO PILDID (URL, Oige_vastus) VALUES
('https://vso24pauls.ita.voco.ee/Pildid/art/01.webp', 'Vincent_Van_Gogh-Starry_Night_1889'),
('https://vso24pauls.ita.voco.ee/Pildid/art/02.webp', 'Vincent_van_Gogh-Madame_Roulin_and_Her_Baby_1888'),
('https://vso24pauls.ita.voco.ee/Pildid/art/03.webp', 'AI'),
('https://vso24pauls.ita.voco.ee/Pildid/art/04.webp', 'Albrech_Dürer-Stag_Beetle_1505'),
('https://vso24pauls.ita.voco.ee/Pildid/art/05.webp', 'Albrecht_Dürer-The_Large_Horse_1505'),
('https://vso24pauls.ita.voco.ee/Pildid/art/06.webp', 'AI'),
('https://vso24pauls.ita.voco.ee/Pildid/art/07.webp', 'Andre_Derain-Les_Voiles_rouges_1906'),
('https://vso24pauls.ita.voco.ee/Pildid/art/08.webp', 'AI'),
('https://vso24pauls.ita.voco.ee/Pildid/art/09.webp', 'Andre_Derain-Matisse_et_Terrus_1905'),
('https://vso24pauls.ita.voco.ee/Pildid/art/10.webp', 'AI'),
('https://vso24pauls.ita.voco.ee/Pildid/art/11.webp', 'Claude_Monet-Eine_Allee_in_Monets_Garten_in_Giverny_1902'),
('https://vso24pauls.ita.voco.ee/Pildid/art/12.webp', 'Claude_Monet-Woman_with_a_Parasol_1875'),
('https://vso24pauls.ita.voco.ee/Pildid/art/13.webp', 'Rembrandt_van_Rijn-Rembrandt_Self_portrait_1637'),
('https://vso24pauls.ita.voco.ee/Pildid/art/14.webp', 'Rembrandt_van_Rijn-The_Good_Samaritian_1630'),
('https://vso24pauls.ita.voco.ee/Pildid/art/15.webp', 'AI');
";

if (!$mysqli->query($sql)) {
    error_log("Error inserting to table PILDID: " . $mysqli->error);
}

$procedureExists = $mysqli->query("SHOW PROCEDURE STATUS WHERE Db = '{$databaseName}' AND Name = 'CAST_VOTE'");
if ($procedureExists && $procedureExists->num_rows === 0) {
    $procedureSQL = <<<SQL
CREATE PROCEDURE CAST_VOTE (
    IN in_Haaletaja_id INT,
    IN in_Pildi_id INT,
    IN in_Otsus ENUM('AI', 'Paris')
)
BEGIN
    DECLARE existing_id INT DEFAULT NULL;
    DECLARE existing_H_alguse DATETIME;
    DECLARE minutes_diff INT;
    DECLARE now_time DATETIME;

    SET now_time = NOW();

    SELECT id, H_alguse_aeg INTO existing_id, existing_H_alguse
    FROM HAALETUS
    WHERE Haaletaja_id = in_Haaletaja_id AND Pildi_id = in_Pildi_id
    LIMIT 1;

    IF existing_id IS NULL THEN
        INSERT INTO HAALETUS (Haaletaja_id, Pildi_id, H_alguse_aeg, Haaletuse_aeg, Otsus)
        VALUES (in_Haaletaja_id, in_Pildi_id, now_time, now_time, in_Otsus);

        INSERT INTO LOGI (Haaletaja_id, Pildi_id, H_alguse_aeg, Haale_andmise_aeg, Haale_suund)
        VALUES (in_Haaletaja_id, in_Pildi_id, now_time, now_time, in_Otsus);
    ELSE
        SET minutes_diff = TIMESTAMPDIFF(MINUTE, existing_H_alguse, now_time);

        IF minutes_diff < 5 THEN
            UPDATE HAALETUS
            SET Haaletuse_aeg = now_time,
                Otsus = in_Otsus
            WHERE id = existing_id;

            INSERT INTO LOGI (Haaletaja_id, Pildi_id, H_alguse_aeg, Haale_andmise_aeg, Haale_suund)
            VALUES (in_Haaletaja_id, in_Pildi_id, existing_H_alguse, now_time, in_Otsus);
        ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Voting period has expired. No changes allowed.';
        END IF;
    END IF;
END
SQL;

    try {
        $mysqli->query($procedureSQL);
    } catch (mysqli_sql_exception $e) {
        error_log("Failed to create CAST_VOTE: " . $e->getMessage());
    }
}

try {
    $triggerExists = $mysqli->query("
        SELECT TRIGGER_NAME 
        FROM information_schema.TRIGGERS 
        WHERE TRIGGER_SCHEMA = '{$databaseName}' AND TRIGGER_NAME = 'update_tulemused_after_vote'
    ");

    if ($triggerExists->num_rows === 0) {
        $createTriggerSQL = "
        CREATE TRIGGER update_tulemused_after_vote
AFTER INSERT ON HAALETUS
FOR EACH ROW
BEGIN
    DECLARE exists_count INT DEFAULT 0;

    SELECT COUNT(*) INTO exists_count
    FROM TULEMUSED
    WHERE Pildi_id = NEW.Pildi_id;

    IF exists_count = 0 THEN
        INSERT INTO TULEMUSED (Pildi_id, Haaletajate_arv, H_alguse_aeg, AI, Paris)
        VALUES (
            NEW.Pildi_id,
            1,
            NEW.H_alguse_aeg,
            IF(NEW.Otsus = 'AI', 1, 0),
            IF(NEW.Otsus = 'Paris', 1, 0)
        );
    ELSE
        UPDATE TULEMUSED
        SET 
            Haaletajate_arv = Haaletajate_arv + 1,
            AI = AI + IF(NEW.Otsus = 'AI', 1, 0),
            Paris = Paris + IF(NEW.Otsus = 'Paris', 1, 0)
        WHERE Pildi_id = NEW.Pildi_id;
    END IF;
END

        ";

        $mysqli->query($createTriggerSQL);
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error creating trigger update_tulemused_after_vote: " . $e->getMessage());
}

try {
    $triggerExists = $mysqli->query("
        SELECT TRIGGER_NAME 
        FROM information_schema.TRIGGERS 
        WHERE TRIGGER_SCHEMA = '{$databaseName}' AND TRIGGER_NAME = 'update_tulemused_after_update'
    ");

    if ($triggerExists->num_rows === 0) {
        $createUpdateTriggerSQL = <<<SQL
CREATE TRIGGER update_tulemused_after_update
AFTER UPDATE ON HAALETUS
FOR EACH ROW
BEGIN
    IF NEW.Otsus != OLD.Otsus THEN
        UPDATE TULEMUSED
        SET 
            AI = AI - IF(OLD.Otsus = 'AI', 1, 0) + IF(NEW.Otsus = 'AI', 1, 0),
            Paris = Paris - IF(OLD.Otsus = 'Paris', 1, 0) + IF(NEW.Otsus = 'Paris', 1, 0)
        WHERE Pildi_id = NEW.Pildi_id;
    END IF;
END
SQL;

        $mysqli->query($createUpdateTriggerSQL);
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error creating update trigger: " . $e->getMessage());
}


?>