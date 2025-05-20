<?php
// Activer CORS pour permettre l'accès à la carte des joueurs
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

// Si c'est une requête OPTIONS, renvoyer seulement les en-têtes et terminer
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Inclure le fichier d'origine
include 'index.php';
?>
