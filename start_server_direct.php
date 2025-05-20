<?php
// Script direct pour démarrer les serveurs AzerothCore
session_start();

// Vérifier si l'utilisateur est authentifié
$isAuthorized = isset($_SESSION['account_username']) && isset($_SESSION['account_role']) && 
               in_array($_SESSION['account_role'], ['moderator', 'gm', 'admin']);

if (!$isAuthorized) {
    header("Location: server_auth_simple.php");
    exit;
}

// Journalisation
$log_file = "/home/mccloud/wow_3.3.5/sylvania-web/server_action.log";
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Début du démarrage des serveurs par " . $_SESSION['account_username'] . "\n", FILE_APPEND);

// Arrêter les instances précédentes si elles existent
exec("pkill -f authserver");
exec("pkill -f worldserver");
sleep(1);

// Exécuter le script de démarrage
$script_path = "/home/mccloud/wow_3.3.5/start_server_simple.sh";
exec("nohup bash $script_path > /home/mccloud/wow_3.3.5/sylvania-web/server_start.log 2>&1 &");

// Journalisation
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Commande de démarrage envoyée\n", FILE_APPEND);

// Rediriger vers la page de contrôle
header("Location: server_control_onoff.php?message=" . urlencode("Démarrage des serveurs en cours..."));
exit;
?>
