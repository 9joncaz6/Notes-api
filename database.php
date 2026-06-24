<?php

// Charger les librairies Composer
require __DIR__ . '/vendor/autoload.php';

use MongoDB\Client; // Importe la classe Client de la librairie MongoDB
use Dotenv\Dotenv; // Importe la classe pour lire le fichier .env

// Charger les variables d'environnement (.env)
$dotenv = Dotenv::createImmutable(__DIR__); // Indique où se trouve .env
$dotenv->load(); // Charge les variables d’environnement dans $_ENV

// Récupérer les valeurs du .env
$mongoUri = $_ENV['MONGO_URI']; // Récupère l’URI du cluster Atlas
$mongoDb  = $_ENV['MONGO_DB'];

// Créer le client MongoDB
$client = new Client($mongoUri); // Crée la connexion MongoDB

// Retourner la base de données
return $client->selectDatabase($mongoDb);// Retourne la base de données prête à l’emploi
