<?php
// Script simple pour afficher l'état réel des serveurs
header('Content-Type: application/json');

// Exécuter le script de vérification pour mettre à jour le statut
exec("sudo /home/mccloud/wow_3.3.5/server_status_check.sh");

// Lire le fichier de statut
$statusFile = '/home/mccloud/wow_3.3.5/sylvania-web/real_server_status.json';
if (file_exists($statusFile)) {
    $status = file_get_contents($statusFile);
    echo $status;
} else {
    // Fallback si le fichier n'existe pas
    $response = [
        'auth_running' => false,
        'world_running' => false,
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => 'Fichier de statut non trouvé'
    ];
    echo json_encode($response);
}
?>
