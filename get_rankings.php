<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_characters = "acore_characters";
$db_auth = "acore_auth";

// Fonctions utilitaires
function getRaceName($raceId) {
    $races = [
        1 => "Humain",
        2 => "Orc",
        3 => "Nain",
        4 => "Elfe de la nuit",
        5 => "Tauren",
        6 => "Gnome",
        7 => "Troll",
        8 => "Gobelin",
        9 => "Elfe de sang",
        10 => "Draenei",
        11 => "Tauren",
        22 => "Worgen"
    ];
    return $races[$raceId] ?? "Inconnu";
}

function getClassName($classId) {
    $classes = [
        1 => "Guerrier",
        2 => "Paladin",
        3 => "Chasseur",
        4 => "Voleur",
        5 => "Prêtre",
        6 => "Démoniste",
        7 => "Chaman",
        8 => "Mage",
        9 => "Druide",
        11 => "Chevalier de la mort"
    ];
    return $classes[$classId] ?? "Inconnu";
}

function getFactionName($raceId) {
    // Races de l'Alliance (1, 3, 4, 7, 11)
    // Races de la Horde (2, 5, 6, 8, 9, 22)
    return in_array($raceId, [1, 3, 4, 7, 11]) ? "Alliance" : "Horde";
}

// Gestion des erreurs
function log_error($message) {
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, '/tmp/get_rankings.log');
}

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si le type est défini
    if (!isset($_GET['type'])) {
        throw new Exception("Type de classement non spécifié");
    }
    
    $type = $_GET['type'];
    
    switch ($type) {
        case 'level':
            log_error("get_rankings.php - Requête pour le classement par niveau");
            $stmt = $pdo->query("
                SELECT 
                    c.guid,
                    c.name,
                    c.level,
                    c.race,
                    c.class,
                    c.guildid,
                    g.name as guild_name
                FROM characters c
                LEFT JOIN guild_member gm ON c.guid = gm.guid
                LEFT JOIN guild g ON gm.guildid = g.guildid
                WHERE c.level > 0
                ORDER BY c.level DESC, c.name ASC
                LIMIT 100
            ");
            
            if (!$stmt) {
                throw new Exception("Erreur dans la requête SQL pour le classement par niveau");
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                throw new Exception("Aucun joueur trouvé dans la base de données");
            }
            
            log_error("get_rankings.php - Nombre de résultats pour le classement par niveau: " . count($results));
            
            // Préparer les données pour le classement
            $ranking = array_map(function($player) {
                return [
                    'name' => $player['name'],
                    'level' => $player['level'],
                    'race' => getRaceName($player['race']),
                    'class' => getClassName($player['class']),
                    'faction' => getFactionName($player['race']),
                    'guild' => $player['guild_name'] ?? 'Aucune'
                ];
            }, $results);
            
            break;
            
        case 'pvp':
            log_error("get_rankings.php - Requête pour le classement PvP");
            $stmt = $pdo->query("
                SELECT 
                    c.guid,
                    c.name,
                    c.honorpoints,
                    c.pvpkills,
                    c.race,
                    c.class,
                    c.guildid,
                    g.name as guild_name
                FROM characters c
                LEFT JOIN guild_member gm ON c.guid = gm.guid
                LEFT JOIN guild g ON gm.guildid = g.guildid
                WHERE c.honorpoints > 0
                ORDER BY c.honorpoints DESC, c.name ASC
                LIMIT 100
            ");
            
            if (!$stmt) {
                throw new Exception("Erreur dans la requête SQL pour le classement PvP");
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                throw new Exception("Aucun joueur avec des points PvP trouvés");
            }
            
            log_error("get_rankings.php - Nombre de résultats pour le classement PvP: " . count($results));
            
            $ranking = array_map(function($player) {
                return [
                    'name' => $player['name'],
                    'honorpoints' => $player['honorpoints'],
                    'pvpkills' => $player['pvpkills'],
                    'race' => getRaceName($player['race']),
                    'class' => getClassName($player['class']),
                    'faction' => getFactionName($player['race']),
                    'guild' => $player['guild_name'] ?? 'Aucune'
                ];
            }, $results);
            
            break;
            
        case 'guild':
            log_error("get_rankings.php - Requête pour le classement des guildes");
            $stmt = $pdo->query("
                SELECT 
                    g.guildid,
                    g.name,
                    g.leaderguid,
                    g.EmblemStyle,
                    g.EmblemColor,
                    g.BorderStyle,
                    g.BorderColor,
                    g.BackgroundColor,
                    g.Info,
                    g.Motd,
                    g.creation_date,
                    (
                        SELECT COUNT(*)
                        FROM guild_member
                        WHERE guildid = g.guildid
                    ) as member_count,
                    (
                        SELECT AVG(c.level)
                        FROM characters c
                        JOIN guild_member gm ON c.guid = gm.guid
                        WHERE gm.guildid = g.guildid
                    ) as avg_level
                FROM guild g
                ORDER BY member_count DESC, g.name ASC
                LIMIT 100
            ");
            
            if (!$stmt) {
                throw new Exception("Erreur dans la requête SQL pour le classement des guildes");
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            log_error("get_rankings.php - Nombre de résultats pour le classement des guildes: " . count($results));
            
            // Récupérer les noms des chefs de guilde
            $leaderNames = [];
            if (!empty($results)) {
                $leaderIds = array_column($results, 'leaderguid');
                $stmt = $pdo->prepare("SELECT guid, name FROM characters WHERE guid IN (" . implode(",", array_fill(0, count($leaderIds), '?')) . ")");
                $stmt->execute($leaderIds);
                $leaders = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                foreach ($results as &$guild) {
                    $guild['leader_name'] = $leaders[$guild['leaderguid']] ?? 'Inconnu';
                }
            }
            
            $ranking = array_map(function($guild) {
                return [
                    'name' => $guild['name'],
                    'leader' => $guild['leader_name'],
                    'members' => $guild['member_count'],
                    'avg_level' => round($guild['avg_level']),
                    'faction' => 'Mixte', // Les guildes peuvent avoir des membres des deux factions
                    'creation_date' => date('d/m/Y', strtotime($guild['creation_date']))
                ];
            }, $results);
            
            break;
            
        default:
            throw new Exception("Type de classement invalide: $type");
    }
    
    // Retourner les données au format JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'ranking' => $ranking]);

} catch (Exception $e) {
    log_error("Erreur dans get_rankings.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
