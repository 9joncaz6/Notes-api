<?php

require_once __DIR__ . '/../controllers/NotesController.php';
require_once __DIR__ . '/../validators/NoteValidator.php';



// Déclaration des variable pour éviter les faux positifs
/** @var string $method */
/** @var string $path */
/** @var MongoDB\Database $db */



$controller = new NotesController($db);

// GET /notes
if ($method === 'GET' && $path === '/notes') {
    $controller->getAll();
    exit;
}

// GET /notes/:id
if ($method === 'GET' && preg_match('#^/notes/([a-f0-9]{24})$#', $path, $matches)) {
    $controller->getOne($matches[1]);
    exit;
}

// GET /notes?page=1&limit=10&search=mot
if ($method === 'GET' && $path === '/notes') {

    // Récupération des paramètres GET
    $page   = $_GET['page']   ?? 1;
    $limit  = $_GET['limit']  ?? 10;
    $search = $_GET['search'] ?? null;

    $controller->getAll($page, $limit, $search);
    exit;
}


// POST /notes
if ($method === 'POST' && $path === '/notes') {
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

// PUT /notes/:id
if ($method === 'PUT' && preg_match('#^/notes/([a-f0-9]{24})$#', $path, $matches)) {
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

// DELETE /notes/:id
if ($method === 'DELETE' && preg_match('#^/notes/([a-f0-9]{24})$#', $path, $matches)) {
    $controller->delete($matches[1]);
    exit;
}

// PATCH /notes/:id
if ($method === 'PATCH' && preg_match('#^/notes/([a-f0-9]{24})$#', $path, $matches)) {

    $input = json_decode(file_get_contents('php://input'), true);

    // Validation partielle
    $validated = NoteValidator::validatePartial($input);

    if (isset($validated["error"])) {
        http_response_code(400);
        echo json_encode(["error" => $validated["error"]]);
        exit;
    }

    $controller->patch($matches[1], $validated);
    exit;
}
