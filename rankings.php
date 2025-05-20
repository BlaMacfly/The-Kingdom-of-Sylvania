<?php
// Configuration de la connexion à la base de données
$db_host = "localhost";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_characters = "acore_characters";

// Fonction pour obtenir le classement par niveau
function getLevelRanking() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "SELECT 
                    c.guid, 
                    c.name, 
                    c.race, 
                    c.class, 
                    c.level, 
                    g.name as guild_name
                FROM 
                    characters c
                LEFT JOIN 
                    guild_member gm ON c.guid = gm.guid
                LEFT JOIN 
                    guild g ON gm.guildid = g.guildid
                ORDER BY 
                    c.level DESC, 
                    c.totaltime DESC
                LIMIT 25";
        
        $stmt = $conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir le classement PvP
function getPvPRanking() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "SELECT 
                    c.guid, 
                    c.name, 
                    c.race, 
                    c.class, 
                    c.totalKills, 
                    c.totalHonorPoints
                FROM 
                    characters c
                ORDER BY 
                    c.totalHonorPoints DESC, 
                    c.totalKills DESC
                LIMIT 25";
        
        $stmt = $conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir le classement des guildes
function getGuildRanking() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Requête pour obtenir les guildes avec le nombre de membres et le chef de guilde
        $query = "SELECT 
                    g.guildid,
                    g.name,
                    c.name as leader_name,
                    (SELECT COUNT(*) FROM guild_member gm WHERE gm.guildid = g.guildid) as member_count,
                    (SELECT AVG(c2.level) FROM characters c2 
                     JOIN guild_member gm2 ON c2.guid = gm2.guid 
                     WHERE gm2.guildid = g.guildid) as avg_level
                FROM 
                    guild g
                JOIN 
                    characters c ON g.leaderguid = c.guid
                ORDER BY 
                    member_count DESC, 
                    avg_level DESC
                LIMIT 25";
        
        $stmt = $conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    } catch (PDOException $e) {
        return [];
    }
}

// Obtenir les données de classement en fonction du type demandé
$type = isset($_GET['type']) ? $_GET['type'] : 'level';

switch ($type) {
    case 'level':
        $data = getLevelRanking();
        break;
    case 'pvp':
        $data = getPvPRanking();
        break;
    case 'guild':
        $data = getGuildRanking();
        break;
    default:
        $data = [];
}

// Retourner les données au format JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
