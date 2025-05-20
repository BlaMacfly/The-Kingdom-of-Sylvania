<?php
// Fichier pour vérifier l'état réel des serveurs via AJAX
header('Content-Type: application/json');

// Fonction pour vérifier si un processus est en cours d'exécution
function isProcessRunning($processName) {
    $command = "pgrep -f $processName";
    exec($command, $output, $returnCode);
    return $returnCode === 0 && !empty($output);
}

// Fonction pour lire les dernières lignes d'un fichier de log
function getLastLogLines($logFile, $lines = 5) {
    if (!file_exists($logFile)) {
        return "Fichier de log non trouvé";
    }
    
    $command = "tail -n $lines $logFile";
    exec($command, $output);
    return implode("\n", $output);
}

// Vérifier l'état des serveurs
$authRunning = isProcessRunning("authserver");
$worldRunning = isProcessRunning("worldserver");

// Lire le fichier de statut si disponible
$statusFile = '/home/mccloud/wow_3.3.5/sylvania-web/server_status.txt';
$status = file_exists($statusFile) ? file_get_contents($statusFile) : '';

// Lire les dernières lignes des logs
$authLog = getLastLogLines('/home/mccloud/wow_3.3.5/mccloud/azerothcore/env/dist/bin/Auth.log');
$worldLog = getLastLogLines('/home/mccloud/wow_3.3.5/mccloud/azerothcore/env/dist/bin/Server.log');

// Déterminer l'étape actuelle et le pourcentage de progression
$currentStep = 0;
$progressPercent = 0;

if ($status === 'auth_starting') {
    if (!$authRunning) {
        $currentStep = 1; // Arrêt des instances précédentes
        $progressPercent = 20;
    } else {
        $currentStep = 3; // Auth démarré
        $progressPercent = 80;
        
        // Mettre à jour le statut si auth est bien démarré
        file_put_contents($statusFile, 'auth_started');
    }
} elseif ($status === 'auth_started') {
    $currentStep = 4; // Auth initialisé
    $progressPercent = 100;
} elseif ($status === 'servers_starting') {
    if (!$authRunning && !$worldRunning) {
        $currentStep = 1; // Arrêt des instances précédentes
        $progressPercent = 20;
    } elseif ($authRunning && !$worldRunning) {
        $currentStep = 2; // Auth démarré, world pas encore
        $progressPercent = 40;
    } elseif ($authRunning && $worldRunning) {
        $currentStep = 4; // Les deux serveurs sont démarrés
        $progressPercent = 80;
        
        // Mettre à jour le statut si les deux serveurs sont bien démarrés
        file_put_contents($statusFile, 'servers_started');
    }
} elseif ($status === 'servers_started') {
    $currentStep = 5; // Tous les serveurs sont initialisés
    $progressPercent = 100;
}

// Préparer la réponse
$response = [
    'auth_running' => $authRunning,
    'world_running' => $worldRunning,
    'current_step' => $currentStep,
    'progress_percent' => $progressPercent,
    'status' => $status,
    'auth_log' => $authLog,
    'world_log' => $worldLog
];

echo json_encode($response);
