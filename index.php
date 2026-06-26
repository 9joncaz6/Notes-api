<?php

// index.php
// Routeur principal de l'API Notes

header('Content-Type: application/json');

// Connexion à la base MongoDB
$db = require __DIR__ . '/database.php';

// Méthode HTTP (GET, POST, PUT, DELETE, PATCH)
$method = $_SERVER['REQUEST_METHOD'];

// Nettoyage du chemin demandé
// Exemple : "/notes/123" → "notes/123"
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');


// ======================================================
//  ROUTES UTILISATEURS (login / register)
// ======================================================
if ($path === 'login' || $path === 'register') {
    require __DIR__ . '/routes/users.php';
    exit;
}


// ======================================================
//  ROUTES NOTES
// ======================================================
if (str_starts_with($path, 'notes')) {
    require __DIR__ . '/routes/notes.php';
    exit;
}


// ======================================================
//  SI AUCUNE ROUTE NE CORRESPOND
// ======================================================
http_response_code(404);
echo json_encode(["error" => "Endpoint not found"]);
