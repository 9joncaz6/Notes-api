<?php

header('Content-Type: application/json');

$db = require __DIR__ . '/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

require __DIR__ . '/routes/notes.php';

http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
