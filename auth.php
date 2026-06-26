<?php
// auth.php
// Middleware d'authentification : vérifie le token envoyé par le front

require_once __DIR__ . '/controllers/UsersController.php';

$headers = getallheaders();

if (!isset($headers["Authorization"])) {
    http_response_code(401);
    echo json_encode(["error" => "Token manquant"]);
    exit;
}

$token = $headers["Authorization"];

$usersController = new UsersController();
$currentUser = $usersController->getUserByToken($token);

if (!$currentUser) {
    http_response_code(401);
    echo json_encode(["error" => "Token invalide"]);
    exit;
}
