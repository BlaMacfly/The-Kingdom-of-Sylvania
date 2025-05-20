<?php
// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";
$db_characters = "acore_characters";
$db_world = "acore_world";

// Fonction pour obtenir les joueurs en ligne avec leurs coordonnées
function getPlayerPositions() {
    global $db_host, $db_port, $db_username, $db_password, $db_characters;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Récupérer tous les joueurs (en ligne ou non) pour avoir plus de données à afficher
        $query = "SELECT 
                    c.guid, 
                    c.name, 
                    c.race, 
                    c.class, 
                    c.level, 
                    c.zone, 
                    c.map, 
                    c.position_x, 
                    c.position_y, 
                    c.position_z,
                    c.online
                FROM 
                    characters c
                ORDER BY 
                    c.online DESC, c.name ASC
                LIMIT 20"; // Limiter à 20 joueurs pour l'exemple
        
        $stmt = $conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return [];
    }
}

// Obtenir les positions des joueurs
$players = getPlayerPositions();

// Fonction pour obtenir le nom de la zone
function getZoneName($zoneId) {
    // Tableau des zones les plus courantes de WoW 3.3.5a
    $zoneNames = [
        1 => "Dun Morogh",
        3 => "Bois de la Pénombre",
        4 => "Forêt d'Élwyn",
        8 => "Marécage des Chagrins",
        10 => "Terres foudroyées",
        11 => "Marche de l'Ouest",
        12 => "Les Carmines",
        14 => "Durotar",
        85 => "Tirisfal",
        130 => "Silithus",
        1519 => "Stormwind City",
        1537 => "Ironforge",
        1637 => "Orgrimmar",
        1638 => "Thunder Bluff",
        1657 => "Darnassus",
        1658 => "Undercity",
        3430 => "Hellfire Peninsula",
        3537 => "Toundra Boréenne",
        4395 => "Dalaran"
    ];
    
    return isset($zoneNames[$zoneId]) ? $zoneNames[$zoneId] : "Zone #" . $zoneId;
}

// Noms des cartes
$mapNames = [
    0 => "Royaumes de l'Est",
    1 => "Kalimdor",
    530 => "Outreterre",
    571 => "Norfendre"
];

// Noms des classes
$classNames = [
    1 => 'Guerrier',
    2 => 'Paladin',
    3 => 'Chasseur',
    4 => 'Voleur',
    5 => 'Prêtre',
    6 => 'Chevalier de la mort',
    7 => 'Chaman',
    8 => 'Mage',
    9 => 'Démoniste',
    11 => 'Druide'
];

// Noms des races
$raceNames = [
    1 => 'Humain',
    2 => 'Orc',
    3 => 'Nain',
    4 => 'Elfe de la nuit',
    5 => 'Mort-vivant',
    6 => 'Tauren',
    7 => 'Gnome',
    8 => 'Troll',
    10 => 'Elfe de sang',
    11 => 'Draeneï'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test des positions des joueurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1f2a;
            color: #eee;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #EABA28;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.3);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background-color: #2c3347;
            color: #EABA28;
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .online {
            color: #4CAF50;
            font-weight: bold;
        }
        .offline {
            color: #f44336;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            color: #EABA28;
            text-decoration: none;
            text-align: center;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Test des positions des joueurs</h1>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Niveau</th>
                <th>Race</th>
                <th>Classe</th>
                <th>Statut</th>
                <th>Carte</th>
                <th>Zone</th>
                <th>Position X</th>
                <th>Position Y</th>
                <th>Position Z</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?php echo $player['name']; ?></td>
                    <td><?php echo $player['level']; ?></td>
                    <td><?php echo isset($raceNames[$player['race']]) ? $raceNames[$player['race']] : 'Inconnu'; ?></td>
                    <td><?php echo isset($classNames[$player['class']]) ? $classNames[$player['class']] : 'Inconnu'; ?></td>
                    <td class="<?php echo $player['online'] ? 'online' : 'offline'; ?>">
                        <?php echo $player['online'] ? 'En ligne' : 'Hors ligne'; ?>
                    </td>
                    <td><?php echo isset($mapNames[$player['map']]) ? $mapNames[$player['map']] : 'Carte #' . $player['map']; ?></td>
                    <td><?php echo getZoneName($player['zone']); ?></td>
                    <td><?php echo round($player['position_x'], 2); ?></td>
                    <td><?php echo round($player['position_y'], 2); ?></td>
                    <td><?php echo round($player['position_z'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="map.php" class="back-link">Retour à la carte des joueurs</a>
</body>
</html>
