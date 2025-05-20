<?php
// Script pour forcer la mise à jour du statut des serveurs
// Ce script est appelé par AJAX pour obtenir l'état réel des services

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction pour exécuter une commande système de manière sécurisée
function executeCommand($command) {
    $output = [];
    $returnCode = 0;
    
    // Exécuter la commande
    exec($command . " 2>&1", $output, $returnCode);
    
    return [
        'output' => $output,
        'returnCode' => $returnCode
    ];
}

// Fonction pour vérifier l'état d'un service
function checkServiceStatus($service) {
    switch ($service) {
        case 'mysql':
            $result = executeCommand("systemctl is-active mysql");
            return $result['returnCode'] === 0;
        case 'auth':
            $result = executeCommand("pgrep -f authserver");
            return $result['returnCode'] === 0;
        case 'world':
            $result = executeCommand("pgrep -f worldserver");
            return $result['returnCode'] === 0;
        default:
            return false;
    }
}

// Vérifier l'état de tous les services
$results = [
    'mysql' => ['status' => checkServiceStatus('mysql')],
    'auth' => ['status' => checkServiceStatus('auth')],
    'world' => ['status' => checkServiceStatus('world')]
];

// Retourner les résultats au format JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'timestamp' => time(),
    'results' => $results
]);
?>
