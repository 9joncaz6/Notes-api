<?php
// controllers/UsersController.php
// Contrôleur responsable de l'inscription, connexion et gestion des rôles

require_once __DIR__ . '/../database.php';

class UsersController
{

    private $collection;

    public function __construct()
    {
        global $db;
        $this->collection = $db->selectCollection('users');
    }


    // ============================
    //  REGISTER
    // ============================
    public function register($data)
    {

        $email = $data["email"];
        $password = $data["password"];
        $role = $data["role"] ?? "user"; // par défaut : user

        // Hash sécurisé
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertion
        $this->collection->insertOne([
            "email" => $email,
            "password" => $hashedPassword,
            "role" => $role
        ]);

        return ["message" => "Utilisateur créé"];
    }


    // ============================
    //  LOGIN
    // ============================
    public function login($data)
    {

        $email = $data["email"];
        $password = $data["password"];

        $user = $this->collection->findOne(["email" => $email]);

        if (!$user) {
            http_response_code(401);
            return ["error" => "Email incorrect"];
        }

        if (!password_verify($password, $user["password"])) {
            http_response_code(401);
            return ["error" => "Mot de passe incorrect"];
        }

        // Génération d’un token sécurisé
        $token = bin2hex(random_bytes(32));

        // Stockage du token
        $this->collection->updateOne(
            ["_id" => $user["_id"]],
            ['$set' => ["token" => $token]]
        );

        return [
            "token" => $token,
            "role" => $user["role"] ?? "user"
        ];
    }


    // ============================
    //  Vérification du token
    // ============================
    public function getUserByToken($token)
    {
        return $this->collection->findOne(["token" => $token]);
    }
}
