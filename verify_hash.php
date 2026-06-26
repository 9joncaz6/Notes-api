<?php

$password = "user123"; // mot de passe en clair
$hash = '$2y$10$uzIhecPerahv3JEMHp07FOjaFtw0A.xoN3WdWb/arst1wKa2DdjIa'; // ton hash

if (password_verify($password, $hash)) {
    echo "OK : le mot de passe correspond au hash";
} else {
    echo "ERREUR : le mot de passe NE correspond PAS au hash";
}
