<?php
// Fichier pour suivre le statut réel du démarrage du serveur
header('Content-Type: application/json');

$log_file = '/home/mccloud/wow_3.3.5/sylvania-web/server_startup.log';

// Vérifier si le fichier de log existe
if (!file_exists($log_file)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Le fichier de log n\'existe pas',
        'progress' => 0,
        'logs' => []
    ]);
    exit;
}

// Lire le fichier de log
$logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($logs)) {
    echo json_encode([
        'status' => 'initializing',
        'message' => 'Initialisation...',
        'progress' => 0,
        'logs' => []
    ]);
    exit;
}

// Analyser les logs pour déterminer le statut et la progression
$status = 'running';
$message = 'Démarrage en cours...';
$progress = 0;
$error = false;

foreach ($logs as $log) {
    // Vérifier s'il y a une erreur
    if (strpos($log, 'ERREUR') !== false) {
        $status = 'error';
        $message = substr($log, strpos($log, 'ERREUR:') + 7);
        $error = true;
        break;
    }
    
    // Calculer la progression en fonction des étapes
    if (strpos($log, 'Démarrage du script') !== false) {
        $progress = 5;
    } elseif (strpos($log, 'Arrêt des instances précédentes') !== false) {
        $progress = 10;
    } elseif (strpos($log, 'Vérification de MySQL') !== false) {
        $progress = 20;
    } elseif (strpos($log, 'MySQL est en cours d\'exécution') !== false) {
        $progress = 30;
    } elseif (strpos($log, 'Démarrage du serveur Auth') !== false) {
        $progress = 40;
        $message = 'Démarrage du serveur Auth...';
    } elseif (strpos($log, 'Serveur Auth démarré avec succès') !== false) {
        $progress = 60;
        $message = 'Serveur Auth démarré avec succès';
    } elseif (strpos($log, 'Démarrage du serveur World') !== false) {
        $progress = 70;
        $message = 'Démarrage du serveur World...';
    } elseif (strpos($log, 'Serveur World démarré avec succès') !== false) {
        $progress = 90;
        $message = 'Serveur World démarré avec succès';
    } elseif (strpos($log, 'Tous les serveurs sont démarrés et prêts') !== false) {
        $progress = 100;
        $message = 'Tous les serveurs sont démarrés et prêts';
        $status = 'completed';
    } elseif (strpos($log, 'Script terminé avec succès') !== false) {
        $progress = 100;
        $message = 'Démarrage terminé avec succès';
        $status = 'completed';
    }
}

// Vérifier si les serveurs sont réellement en cours d'exécution
$auth_running = false;
$world_running = false;

exec("pgrep -f authserver", $auth_output, $auth_return);
$auth_running = ($auth_return === 0 && !empty($auth_output));

exec("pgrep -f worldserver", $world_output, $world_return);
$world_running = ($world_return === 0 && !empty($world_output));

// Si le script indique que tout est terminé, mais que les serveurs ne sont pas en cours d'exécution
if ($status === 'completed' && (!$auth_running || ($progress === 100 && !$world_running))) {
    $status = 'error';
    $message = 'Les serveurs ne sont pas en cours d\'exécution malgré un démarrage apparemment réussi';
    $error = true;
}

// Retourner le résultat
echo json_encode([
    'status' => $status,
    'message' => $message,
    'progress' => $progress,
    'logs' => $logs,
    'auth_running' => $auth_running,
    'world_running' => $world_running,
    'error' => $error
]);
?>
