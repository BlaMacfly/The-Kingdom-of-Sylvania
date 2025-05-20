<?php
// API simple pour vérifier l'état des serveurs
// Utilisé par la page d'accueil pour afficher l'état des serveurs sans recharger la page

// Fonction pour vérifier si un processus est en cours d'exécution
function isProcessRunning($processName) {
    $output = [];
    $returnCode = 0;
    exec("ps aux | grep -v grep | grep '$processName'", $output, $returnCode);
    return ($returnCode === 0 && !empty($output));
}

// Vérifier l'état des serveurs
$auth_running = isProcessRunning('authserver');
$world_running = isProcessRunning('worldserver');
$mysql_running = isProcessRunning('mysql') || isProcessRunning('mysqld');

// Préparer la réponse JSON
$response = [
    'auth' => $auth_running,
    'world' => $world_running,
    'mysql' => $mysql_running,
    'timestamp' => time()
];

// Envoyer la réponse
header('Content-Type: application/json');
echo json_encode($response);
