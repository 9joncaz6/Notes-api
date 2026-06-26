<?php

// generate_hash.php
// Script simple pour générer un hash de mot de passe

$password = "user123"; // le mot de passe 

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe : " . $password . PHP_EOL;
echo "Hash généré : " . $hash . PHP_EOL;
