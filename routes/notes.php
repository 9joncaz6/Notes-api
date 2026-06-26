<?php

// routes/notes.php
// Fichier responsable de router toutes les requêtes liées aux notes.

// On charge le contrôleur des notes
require_once __DIR__ . '/../controllers/NotesController.php';

// On charge le validateur des notes
require_once __DIR__ . '/../validators/NoteValidator.php';

// On charge le middleware d'authentification
require_once __DIR__ . '/../auth.php';


// Déclaration des variables (déjà définies dans index.php)
/** @var string $method */
/** @var string $path */
/** @var MongoDB\Database $db */
/** @var array $currentUser */  // vient de auth.php


// On instancie le contrôleur des notes
$controller = new NotesController($db);



// ======================================================
//  GET notes
// ======================================================
if ($method === 'GET' && $path === 'notes') {

    $page   = $_GET['page']   ?? 1;
    $limit  = $_GET['limit']  ?? 10;
    $search = $_GET['search'] ?? null;

    $controller->getAll($page, $limit, $search);
    exit;
}



// ======================================================
//  GET notes/:id
// ======================================================
if ($method === 'GET' && preg_match('#^notes/([a-f0-9]{24})$#', $path, $matches)) {
    $controller->getOne($matches[1]);
    exit;
}



// ======================================================
//  POST notes
// ======================================================
if ($method === 'POST' && $path === 'notes') {

    $input = json_decode(file_get_contents('php://input'), true);

    $validated = NoteValidator::validate($input);

    if (isset($validated["error"])) {
        http_response_code(400);
        echo json_encode(["error" => $validated["error"]]);
        exit;
    }

    $controller->create($validated);
    exit;
}



// ======================================================
//  PUT notes/:id
// ======================================================
if ($method === 'PUT' && preg_match('#^notes/([a-f0-9]{24})$#', $path, $matches)) {

    $input = json_decode(file_get_contents('php://input'), true);

    $validated = NoteValidator::validate($input);

    if (isset($validated["error"])) {
        http_response_code(400);
        echo json_encode(["error" => $validated["error"]]);
        exit;
    }

    $controller->update($matches[1], $validated);
    exit;
}



// ======================================================
//  PATCH notes/:id
// ======================================================
if ($method === 'PATCH' && preg_match('#^notes/([a-f0-9]{24})$#', $path, $matches)) {

    $input = json_decode(file_get_contents('php://input'), true);

    $validated = NoteValidator::validatePartial($input);

    if (isset($validated["error"])) {
        http_response_code(400);
        echo json_encode(["error" => $validated["error"]]);
        exit;
    }

    $controller->patch($matches[1], $validated);
    exit;
}



// ======================================================
//  DELETE notes/:id (admin only)
// ======================================================
if ($method === 'DELETE' && preg_match('#^notes/([a-f0-9]{24})$#', $path, $matches)) {

    if (($currentUser["role"] ?? "user") !== "admin") {
        http_response_code(403);
        echo json_encode(["error" => "Accès réservé aux administrateurs"]);
        exit;
    }

    $controller->delete($matches[1]);
    exit;
}
