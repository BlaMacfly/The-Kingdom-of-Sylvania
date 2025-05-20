<?php
// Fichier pour vérifier si l'utilisateur est authentifié
session_start();

// Vérifier si l'utilisateur est autorisé (admin, GM ou modérateur)
$isAuthorized = isset($_SESSION['account_role']) && in_array($_SESSION['account_role'], ['admin', 'gm', 'moderator']);

// Retourner le résultat au format JSON
header('Content-Type: application/json');
echo json_encode(['authenticated' => $isAuthorized]);
?>
