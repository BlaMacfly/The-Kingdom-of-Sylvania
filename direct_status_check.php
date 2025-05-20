<?php
// Script de vérification directe de l'état des serveurs
// Ce script effectue une vérification en temps réel de l'état des services

// Désactiver la mise en cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Fonction pour exécuter une commande système
function runCommand($command) {
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);
    return $returnCode === 0;
}

// Vérifier l'état des services
$mysql_status = runCommand("systemctl is-active mysql");
$auth_status = runCommand("pgrep -f authserver");
$world_status = runCommand("pgrep -f worldserver");

// Préparer la réponse
$response = [
    'success' => true,
    'timestamp' => time(),
    'results' => [
        'mysql' => ['status' => $mysql_status],
        'auth' => ['status' => $auth_status],
        'world' => ['status' => $world_status]
    ]
];

// Envoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
