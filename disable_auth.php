<?php
// Script pour désactiver l'authentification forcée
// Ce script doit être inclus au début de chaque page

// Vérifier si une authentification HTTP est en cours
if (isset($_SERVER['PHP_AUTH_USER'])) {
    // Effacer les en-têtes d'authentification
    header('WWW-Authenticate:');
    header('HTTP/1.1 200 OK');
}

// Désactiver toute redirection vers la page d'authentification
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['bypass_auth'] = true;
?>
