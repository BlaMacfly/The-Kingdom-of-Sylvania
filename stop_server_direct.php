<?php
// Script direct pour arrêter les serveurs AzerothCore
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
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Début de l'arrêt des serveurs par " . $_SESSION['account_username'] . "\n", FILE_APPEND);

// Arrêter les serveurs avec sudo
// 1. D'abord le serveur World
exec("sudo pkill -f worldserver");
sleep(2);
exec("sudo pkill -9 -f worldserver");
sleep(1);

// 2. Ensuite le serveur Auth
exec("sudo pkill -f authserver");
sleep(1);
exec("sudo pkill -9 -f authserver");

// Journalisation
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Commandes d'arrêt envoyées\n", FILE_APPEND);

// Rediriger vers la page de contrôle
header("Location: server_control_onoff.php?message=" . urlencode("Arrêt des serveurs en cours..."));
exit;
?>
