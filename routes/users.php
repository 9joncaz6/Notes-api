<?php
// routes/users.php
// Routes liées à l'inscription et la connexion

require_once __DIR__ . '/../controllers/UsersController.php';

$controller = new UsersController();

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

$data = json_decode(file_get_contents("php://input"), true);


// REGISTER
if ($path === '/register' && $method === 'POST') {
    echo json_encode($controller->register($data));
    exit;
}


// LOGIN
if ($path === '/login' && $method === 'POST') {
    echo json_encode($controller->login($data));
    exit;
}
