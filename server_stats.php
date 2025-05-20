<?php
// Configuration de la connexion à la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";
$db_characters = "acore_characters";

// Fonction pour obtenir le nombre de comptes
function getAccountsCount() {
    global $db_host, $db_port, $db_username, $db_password, $db_auth;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_auth", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM account");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}

// Fonction pour obtenir le nombre de personnages
function getCharactersCount() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM characters");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}

// Fonction pour obtenir le nombre de joueurs en ligne
function getOnlinePlayersCount() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM characters WHERE online = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}

// Fonction pour obtenir la répartition des factions
function getFactionDistribution() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->query("SELECT race, COUNT(*) as count FROM characters GROUP BY race");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $alliance = 0;
        $horde = 0;
        
        foreach ($results as $result) {
            // Races de l'Alliance: 1 (Humain), 3 (Nain), 4 (Elfe de la nuit), 7 (Gnome), 11 (Draeneï)
            // Races de la Horde: 2 (Orc), 5 (Mort-vivant), 6 (Tauren), 8 (Troll), 10 (Elfe de sang)
            if (in_array($result['race'], [1, 3, 4, 7, 11])) {
                $alliance += $result['count'];
            } else {
                $horde += $result['count'];
            }
        }
        
        $total = $alliance + $horde;
        
        if ($total > 0) {
            $alliance_percent = round(($alliance / $total) * 100);
            $horde_percent = round(($horde / $total) * 100);
        } else {
            $alliance_percent = 0;
            $horde_percent = 0;
        }
        
        return [
            'alliance' => $alliance,
            'horde' => $horde,
            'alliance_percent' => $alliance_percent,
            'horde_percent' => $horde_percent
        ];
    } catch (PDOException $e) {
        return [
            'alliance' => 0,
            'horde' => 0,
            'alliance_percent' => 0,
            'horde_percent' => 0
        ];
    }
}

// Obtenir les statistiques
$accounts_count = getAccountsCount();
$characters_count = getCharactersCount();
$online_players_count = getOnlinePlayersCount();
$faction_distribution = getFactionDistribution();

// Retourner les données au format JSON
header('Content-Type: application/json');
echo json_encode([
    'accounts_count' => $accounts_count,
    'characters_count' => $characters_count,
    'online_players_count' => $online_players_count,
    'faction_distribution' => $faction_distribution
]);
?>
