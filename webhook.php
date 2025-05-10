<?php

$envPath = '/home/vepso/.env';

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

$secret = $_ENV['WEBHOOK_SECRET'];
if ($_GET['secret'] !== $secret) {
    http_response_code(403);
    exit('Forbidden');
}
shell_exec('/home/vepso/git-pull.sh 2>&1');
