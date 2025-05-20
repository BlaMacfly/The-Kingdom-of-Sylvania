<?php
/**
 * Script pour obtenir les statistiques du serveur The Kingdom of Sylvania
 * Ce fichier est appelé via AJAX pour mettre à jour les statistiques en temps réel
 */

// Configuration de la base de données
$db_host = "localhost";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";
$db_chars = "acore_characters";

// Fonction pour vérifier si un service est en cours d'exécution
function checkServiceRunning($service) {
    if ($service === 'auth') {
        // Vérifier si authserver est en cours d'exécution
        exec("ps aux | grep -v grep | grep authserver", $output, $returnCode);
        $running = ($returnCode === 0 && !empty($output));
        return $running;
    } elseif ($service === 'world') {
        // Vérifier si worldserver est en cours d'exécution
        exec("ps aux | grep -v grep | grep worldserver", $output, $returnCode);
        $running = ($returnCode === 0 && !empty($output));
        return $running;
    }
    
    return false;
}

// Fonction pour se connecter à une base de données
function connectToDatabase($host, $port, $username, $password, $dbname) {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// Obtenir l'état actuel des services
$auth_running = checkServiceRunning('auth');
$world_running = checkServiceRunning('world');

// Initialiser les statistiques
$stats = [
    'auth_status' => $auth_running ? 'online' : 'offline',
    'world_status' => $world_running ? 'online' : 'offline',
    'accounts_count' => 0,
    'characters_count' => 0,
    'online_players' => 0,
    'alliance_percent' => 0,
    'horde_percent' => 0
];

// Récupérer les statistiques depuis les bases de données
try {
    // Connexion à la base de données auth
    $auth_db = connectToDatabase($db_host, $db_port, $db_username, $db_password, $db_auth);
    if ($auth_db) {
        // Compter le nombre de comptes
        $query = $auth_db->query("SELECT COUNT(*) FROM account");
        $stats['accounts_count'] = $query->fetchColumn();
    }
    
    // Connexion à la base de données characters
    $chars_db = connectToDatabase($db_host, $db_port, $db_username, $db_password, $db_chars);
    if ($chars_db) {
        // Compter le nombre de personnages
        $query = $chars_db->query("SELECT COUNT(*) FROM characters");
        $stats['characters_count'] = $query->fetchColumn();
        
        // Compter le nombre de joueurs en ligne
        $query = $chars_db->query("SELECT COUNT(*) FROM characters WHERE online = 1");
        $stats['online_players'] = $query->fetchColumn();
        
        // Calculer la répartition des factions
        // Dans WoW 3.3.5a, les races 1, 3, 4, 7, 11 sont Alliance, les autres sont Horde
        $query = $chars_db->query("SELECT 
            SUM(CASE WHEN race IN (1, 3, 4, 7, 11) THEN 1 ELSE 0 END) as alliance_count,
            SUM(CASE WHEN race IN (2, 5, 6, 8, 10) THEN 1 ELSE 0 END) as horde_count
        FROM characters");
        $faction_data = $query->fetch(PDO::FETCH_ASSOC);
        
        $total_chars = $faction_data['alliance_count'] + $faction_data['horde_count'];
        if ($total_chars > 0) {
            $stats['alliance_percent'] = round(($faction_data['alliance_count'] / $total_chars) * 100);
            $stats['horde_percent'] = round(($faction_data['horde_count'] / $total_chars) * 100);
        }
    }
} catch (Exception $e) {
    // En cas d'erreur, ajouter un message d'erreur aux statistiques
    $stats['error'] = $e->getMessage();
}

// Renvoyer les statistiques au format JSON
header('Content-Type: application/json');
echo json_encode($stats);
