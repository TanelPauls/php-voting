<?php

$envPath = '/home/vepso/.env';

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

$databaseHost = $_ENV['DB_HOST'];
$databaseUsername = $_ENV['CPANEL_USER'];
$databasePassword = $_ENV['CPANEL_PASS'];
$databaseName = $_ENV['DB_NAME'];

$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if ($mysqli->connect_error) {
    error_log("Database connection failed: " . $mysqli->connect_error);
    die("Unable to connect to the database.");
}

$mysqli->set_charset("utf8mb4");

?>