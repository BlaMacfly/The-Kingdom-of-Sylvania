<?php
// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";
$db_characters = "acore_characters";
$db_world = "acore_world";

// Configuration du site
$site_title = "The Kingdom of Sylvania";
$site_description = "Carte des joueurs en temps réel";

// Fonction pour obtenir les joueurs en ligne
function getOnlinePlayers() {
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
                    c.zone, 
                    c.map, 
                    c.position_x, 
                    c.position_y, 
                    c.position_z,
                    g.name as guild_name,
                    CASE 
                        WHEN c.race IN (1, 3, 4, 7, 11) THEN 'Alliance' 
                        ELSE 'Horde' 
                    END as faction
                FROM 
                    characters c
                LEFT JOIN 
                    guild_member gm ON c.guid = gm.guid
                LEFT JOIN 
                    guild g ON gm.guildid = g.guildid
                WHERE 
                    c.online = 1
                ORDER BY 
                    c.name";
        
        $stmt = $conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    } catch (PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir le nom de la zone
function getZoneName($zoneId) {
    // Tableau des zones les plus courantes de WoW 3.3.5a (Wrath of the Lich King)
    // ID => Nom de la zone en français
    $zoneNames = [
        // Royaumes de l'Est
        1 => "Dun Morogh",
        3 => "Bois de la Pénombre",
        4 => "Forêt d'Élwyn",
        8 => "Marécage des Chagrins",
        10 => "Terres foudroyées",
        11 => "Marche de l'Ouest",
        12 => "Les Carmines",
        14 => "Durotar",
        15 => "Dustwallow Marsh",
        16 => "Azshara",
        17 => "Les Tarides",
        28 => "Défilé de Deuillevent",
        33 => "Strangleronce",
        36 => "Terres ingrates",
        38 => "Loch Modan",
        40 => "Marche de l'Ouest",
        41 => "Contreforts de Hautebrande",
        44 => "Terres foudroyées",
        45 => "Hautes-terres d'Arathi",
        46 => "Terres brulées",
        47 => "Les Hinterlands",
        51 => "Marécage d'Aprefange",
        85 => "Tirisfal",
        130 => "Silithus",
        139 => "Teldrassil",
        141 => "Teldrassil",
        148 => "Sombrivage",
        215 => "Mulgore",
        267 => "Cratère d'Un'Goro",
        331 => "Gangrebois",
        357 => "Feralas",
        361 => "Reflet-de-Lune",
        400 => "Mille pointes",
        405 => "Berceau-de-l'Hiver",
        406 => "Berceau-de-l'Hiver",
        440 => "Tanaris",
        490 => "Un'Goro",
        493 => "Cratère de Moonglade",
        618 => "Winterspring",
        1377 => "Silithus",
        1519 => "Stormwind City",
        1537 => "Ironforge",
        1637 => "Orgrimmar",
        1638 => "Thunder Bluff",
        1657 => "Darnassus",
        1658 => "Undercity",
        1977 => "Zul'Gurub",
        2017 => "Stratholme",
        2057 => "Scholomance",
        2100 => "Maraudon",
        2437 => "Ragefire Chasm",
        2557 => "Dire Maul",
        
        // Outreterre
        3430 => "Hellfire Peninsula",
        3433 => "Nagrand",
        3483 => "Hellfire Peninsula",
        3518 => "Nagrand",
        3519 => "Terokkar Forest",
        3520 => "Shadowmoon Valley",
        3521 => "Zangarmarsh",
        3522 => "Blade's Edge Mountains",
        3523 => "Netherstorm",
        3524 => "Azuremyst Isle",
        3525 => "Bloodmyst Isle",
        3526 => "Ghostlands",
        3527 => "Eversong Woods",
        3537 => "Borean Tundra",
        3557 => "The Exodar",
        3703 => "Shattrath City",
        4080 => "Isle of Quel'Danas",
        
        // Norfendre
        3537 => "Toundra Boréenne",
        65 => "Fjord Hurlant",
        394 => "Grizzly Hills",
        495 => "Howling Fjord",
        2817 => "Crystalsong Forest",
        4197 => "Wintergrasp",
        4395 => "Dalaran",
        4742 => "Hrothgar's Landing",
        4812 => "Icecrown Citadel",
    ];
    
    // Si l'ID de zone est dans notre tableau, retourner le nom correspondant
    if (isset($zoneNames[$zoneId])) {
        return $zoneNames[$zoneId];
    }
    
    // Sinon, essayer de récupérer le nom depuis la base de données
    try {
        global $db_host, $db_port, $db_username, $db_password, $db_world;
        
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_world", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Essayer plusieurs tables possibles
        $tables = [
            ["areatable_dbc", "ID", "AreaName_Lang_frFR"],
            ["areatable_dbc", "ID", "AreaName_Lang_enUS"]
        ];
        
        foreach ($tables as $tableInfo) {
            list($table, $idColumn, $nameColumn) = $tableInfo;
            
            $query = "SELECT $nameColumn FROM $table WHERE $idColumn = :zoneId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':zoneId', $zoneId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result[$nameColumn])) {
                return $result[$nameColumn];
            }
        }
        
        // Si aucune correspondance n'est trouvée, retourner une valeur par défaut
        return "Zone #" . $zoneId;
        
    } catch (PDOException $e) {
        // En cas d'erreur, retourner l'ID de la zone
        return "Zone #" . $zoneId;
    }
}

// Fonction pour convertir les coordonnées du jeu en coordonnées de la carte
function convertCoordinates($x, $y, $map) {
    // Facteurs d'échelle et de décalage pour une meilleure précision
    // Ces valeurs sont basées sur une analyse des coordonnées du jeu par rapport aux cartes
    switch ($map) {
        case 0: // Royaumes de l'Est (Eastern Kingdoms)
            // Ajustement pour les Royaumes de l'Est
            $mapX = 483 + ($x / 18);
            $mapY = 366 - ($y / 18);
            break;
        case 1: // Kalimdor
            // Ajustement pour Kalimdor (inversé par rapport aux Royaumes de l'Est)
            $mapX = 483 - ($x / 18);
            $mapY = 366 - ($y / 18);
            break;
        case 530: // Outreterre (Outland)
            // Ajustement pour l'Outreterre
            $mapX = 483 + ($x / 12);
            $mapY = 366 + ($y / 12);
            break;
        case 571: // Norfendre (Northrend)
            // Ajustement pour Norfendre
            $mapX = 483 + ($x / 15);
            $mapY = 366 - ($y / 15);
            break;
        default:
            // Valeurs par défaut pour les autres cartes
            $mapX = 483;
            $mapY = 366;
    }
    
    return ['x' => round($mapX), 'y' => round($mapY)];
}

// Obtenir les joueurs en ligne
$onlinePlayers = getOnlinePlayers();

// Constantes pour les noms de classes et races
$CLASS_NAMES = [
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

$RACE_NAMES = [
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

// Noms des cartes
$MAP_NAMES = [
    0 => "Royaumes de l'Est",
    1 => "Kalimdor",
    530 => "Outreterre",
    571 => "Norfendre"
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?> - Carte des joueurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #000000;
            color: #EABA28;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: rgba(15, 18, 24, 0.9);
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #2c3347;
        }
        .header h1 {
            margin: 0;
            color: #fff;
            font-size: 24px;
        }
        .server-info {
            background-color: rgba(44, 51, 71, 0.8);
            padding: 10px;
            text-align: center;
        }
        .map-container {
            width: 966px;
            height: 732px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .map-tabs {
            display: flex;
            justify-content: center;
            background-color: rgba(15, 18, 24, 0.9);
            padding: 10px;
        }
        .map-tab {
            padding: 8px 15px;
            margin: 0 5px;
            background-color: rgba(44, 51, 71, 0.5);
            color: #EABA28;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .map-tab.active {
            background-color: rgba(44, 51, 71, 0.9);
            color: #fff;
        }
        .back-link {
            display: block;
            margin: 20px auto;
            text-align: center;
            color: #EABA28;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .map {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
        }
        #azeroth-map {
            background-image: url('playermap/img/map/azeroth.jpg');
            display: block;
        }
        #outland-map {
            background-image: url('playermap/img/map/outland.jpg');
            display: none;
        }
        #northrend-map {
            background-image: url('playermap/img/map/northrend.jpg');
            display: none;
        }
        .player-marker {
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
            transition: transform 0.2s ease;
        }
        .player-marker:hover {
            transform: scale(1.5);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }
        .alliance {
            background-color: #0078ff;
            border: 2px solid #ffffff;
        }
        .horde {
            background-color: #ff0000;
            border: 2px solid #ffffff;
        }
        .player-tooltip {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8);
            border: 1px solid #2c3347;
            padding: 10px;
            border-radius: 5px;
            color: #fff;
            font-size: 12px;
            z-index: 200;
            display: none;
            min-width: 200px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .player-tooltip h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #EABA28;
        }
        .player-tooltip p {
            margin: 5px 0;
        }
        .player-tooltip .coords {
            color: #4CAF50;
            font-weight: bold;
        }
        .player-count {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 5px 10px;
            border-radius: 5px;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 14px;
        }
        .player-list {
            background-color: rgba(15, 18, 24, 0.9);
            border: 1px solid #2c3347;
            border-radius: 5px;
            padding: 10px;
            margin-top: 20px;
            max-width: 966px;
            margin-left: auto;
            margin-right: auto;
        }
        .player-list h2 {
            color: #EABA28;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .player-table {
            width: 100%;
            color: #fff;
            border-collapse: collapse;
        }
        .player-table th, .player-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #2c3347;
        }
        .player-table th {
            color: #EABA28;
        }
        .coords-column {
            font-family: monospace;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Carte des joueurs en temps réel</h1>
    </div>
    
    <div class="server-info">
        <p>Cette page affiche la carte des joueurs actuellement connectés sur le serveur <?php echo $site_title; ?>.</p>
        <p>Serveur: sylvania.servegame.com | Base de données: acore_characters</p>
    </div>
    
    <div class="map-tabs">
        <button class="map-tab active" onclick="showMap('azeroth')">Azeroth</button>
        <button class="map-tab" onclick="showMap('outland')">Outreterre</button>
        <button class="map-tab" onclick="showMap('northrend')">Norfendre</button>
    </div>
    
    <div class="map-container">
        <div id="azeroth-map" class="map">
            <div class="player-count">Joueurs en ligne: <span id="azeroth-count">0</span></div>
            <?php
            $azerothCount = 0;
            foreach ($onlinePlayers as $player) {
                if ($player['map'] == 0 || $player['map'] == 1) {
                    $azerothCount++;
                    $coords = convertCoordinates($player['position_x'], $player['position_y'], $player['map']);
                    $zoneName = getZoneName($player['zone']);
                    $className = isset($CLASS_NAMES[$player['class']]) ? $CLASS_NAMES[$player['class']] : 'Inconnu';
                    $raceName = isset($RACE_NAMES[$player['race']]) ? $RACE_NAMES[$player['race']] : 'Inconnu';
                    $mapName = isset($MAP_NAMES[$player['map']]) ? $MAP_NAMES[$player['map']] : 'Carte #' . $player['map'];
                    
                    echo '<div class="player-marker ' . strtolower($player['faction']) . '" style="left: ' . $coords['x'] . 'px; top: ' . $coords['y'] . 'px;" onmouseover="showTooltip(' . $player['guid'] . ')" onmouseout="hideTooltip(' . $player['guid'] . ')"></div>';
                    
                    echo '<div id="tooltip-' . $player['guid'] . '" class="player-tooltip">';
                    echo '<h3>' . $player['name'] . '</h3>';
                    echo '<p>Niveau ' . $player['level'] . ' ' . $raceName . ' ' . $className . '</p>';
                    echo '<p>Faction: ' . $player['faction'] . '</p>';
                    if (!empty($player['guild_name'])) {
                        echo '<p>Guilde: ' . $player['guild_name'] . '</p>';
                    }
                    echo '<p>Zone: ' . $zoneName . '</p>';
                    echo '<p>Carte: ' . $mapName . '</p>';
                    echo '<p class="coords">Coordonnées: X=' . round($player['position_x'], 1) . ', Y=' . round($player['position_y'], 1) . ', Z=' . round($player['position_z'], 1) . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <div id="outland-map" class="map">
            <div class="player-count">Joueurs en ligne: <span id="outland-count">0</span></div>
            <?php
            $outlandCount = 0;
            foreach ($onlinePlayers as $player) {
                if ($player['map'] == 530) {
                    $outlandCount++;
                    $coords = convertCoordinates($player['position_x'], $player['position_y'], $player['map']);
                    $zoneName = getZoneName($player['zone']);
                    $className = isset($CLASS_NAMES[$player['class']]) ? $CLASS_NAMES[$player['class']] : 'Inconnu';
                    $raceName = isset($RACE_NAMES[$player['race']]) ? $RACE_NAMES[$player['race']] : 'Inconnu';
                    $mapName = isset($MAP_NAMES[$player['map']]) ? $MAP_NAMES[$player['map']] : 'Carte #' . $player['map'];
                    
                    echo '<div class="player-marker ' . strtolower($player['faction']) . '" style="left: ' . $coords['x'] . 'px; top: ' . $coords['y'] . 'px;" onmouseover="showTooltip(' . $player['guid'] . ')" onmouseout="hideTooltip(' . $player['guid'] . ')"></div>';
                    
                    echo '<div id="tooltip-' . $player['guid'] . '" class="player-tooltip">';
                    echo '<h3>' . $player['name'] . '</h3>';
                    echo '<p>Niveau ' . $player['level'] . ' ' . $raceName . ' ' . $className . '</p>';
                    echo '<p>Faction: ' . $player['faction'] . '</p>';
                    if (!empty($player['guild_name'])) {
                        echo '<p>Guilde: ' . $player['guild_name'] . '</p>';
                    }
                    echo '<p>Zone: ' . $zoneName . '</p>';
                    echo '<p>Carte: ' . $mapName . '</p>';
                    echo '<p class="coords">Coordonnées: X=' . round($player['position_x'], 1) . ', Y=' . round($player['position_y'], 1) . ', Z=' . round($player['position_z'], 1) . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <div id="northrend-map" class="map">
            <div class="player-count">Joueurs en ligne: <span id="northrend-count">0</span></div>
            <?php
            $northrendCount = 0;
            foreach ($onlinePlayers as $player) {
                if ($player['map'] == 571) {
                    $northrendCount++;
                    $coords = convertCoordinates($player['position_x'], $player['position_y'], $player['map']);
                    $zoneName = getZoneName($player['zone']);
                    $className = isset($CLASS_NAMES[$player['class']]) ? $CLASS_NAMES[$player['class']] : 'Inconnu';
                    $raceName = isset($RACE_NAMES[$player['race']]) ? $RACE_NAMES[$player['race']] : 'Inconnu';
                    $mapName = isset($MAP_NAMES[$player['map']]) ? $MAP_NAMES[$player['map']] : 'Carte #' . $player['map'];
                    
                    echo '<div class="player-marker ' . strtolower($player['faction']) . '" style="left: ' . $coords['x'] . 'px; top: ' . $coords['y'] . 'px;" onmouseover="showTooltip(' . $player['guid'] . ')" onmouseout="hideTooltip(' . $player['guid'] . ')"></div>';
                    
                    echo '<div id="tooltip-' . $player['guid'] . '" class="player-tooltip">';
                    echo '<h3>' . $player['name'] . '</h3>';
                    echo '<p>Niveau ' . $player['level'] . ' ' . $raceName . ' ' . $className . '</p>';
                    echo '<p>Faction: ' . $player['faction'] . '</p>';
                    if (!empty($player['guild_name'])) {
                        echo '<p>Guilde: ' . $player['guild_name'] . '</p>';
                    }
                    echo '<p>Zone: ' . $zoneName . '</p>';
                    echo '<p>Carte: ' . $mapName . '</p>';
                    echo '<p class="coords">Coordonnées: X=' . round($player['position_x'], 1) . ', Y=' . round($player['position_y'], 1) . ', Z=' . round($player['position_z'], 1) . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
    
    <div class="player-list">
        <h2>Joueurs en ligne (<?php echo count($onlinePlayers); ?>)</h2>
        <table class="player-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Niveau</th>
                    <th>Race</th>
                    <th>Classe</th>
                    <th>Faction</th>
                    <th>Zone</th>
                    <th>Coordonnées</th>
                    <th>Guilde</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($onlinePlayers) > 0) {
                    foreach ($onlinePlayers as $player) {
                        $zoneName = getZoneName($player['zone']);
                        $className = isset($CLASS_NAMES[$player['class']]) ? $CLASS_NAMES[$player['class']] : 'Inconnu';
                        $raceName = isset($RACE_NAMES[$player['race']]) ? $RACE_NAMES[$player['race']] : 'Inconnu';
                        
                        echo '<tr>';
                        echo '<td>' . $player['name'] . '</td>';
                        echo '<td>' . $player['level'] . '</td>';
                        echo '<td>' . $raceName . '</td>';
                        echo '<td>' . $className . '</td>';
                        echo '<td>' . $player['faction'] . '</td>';
                        echo '<td>' . $zoneName . '</td>';
                        echo '<td class="coords-column">X:' . round($player['position_x'], 1) . ' Y:' . round($player['position_y'], 1) . '</td>';
                        echo '<td>' . (!empty($player['guild_name']) ? $player['guild_name'] : '-') . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" style="text-align: center;">Aucun joueur en ligne</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <a href="home.php" class="back-link">← Retour à l'accueil</a>

    <script>
    // Mettre à jour les compteurs de joueurs
    document.getElementById('azeroth-count').textContent = '<?php echo $azerothCount; ?>';
    document.getElementById('outland-count').textContent = '<?php echo $outlandCount; ?>';
    document.getElementById('northrend-count').textContent = '<?php echo $northrendCount; ?>';
    
    function showMap(mapName) {
        // Masquer toutes les cartes
        document.getElementById('azeroth-map').style.display = 'none';
        document.getElementById('outland-map').style.display = 'none';
        document.getElementById('northrend-map').style.display = 'none';
        
        // Afficher la carte sélectionnée
        document.getElementById(mapName + '-map').style.display = 'block';
        
        // Mettre à jour les onglets actifs
        var tabs = document.getElementsByClassName('map-tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }
        
        // Trouver l'onglet correspondant à la carte et le marquer comme actif
        if (mapName === 'azeroth') {
            tabs[0].classList.add('active');
        } else if (mapName === 'outland') {
            tabs[1].classList.add('active');
        } else if (mapName === 'northrend') {
            tabs[2].classList.add('active');
        }
    }
    
    function showTooltip(playerId) {
        var tooltip = document.getElementById('tooltip-' + playerId);
        if (tooltip) {
            tooltip.style.display = 'block';
            
            // Positionner l'infobulle près du marqueur du joueur
            var marker = event.target;
            var rect = marker.getBoundingClientRect();
            var mapContainer = document.querySelector('.map-container');
            var mapRect = mapContainer.getBoundingClientRect();
            
            tooltip.style.left = (rect.left - mapRect.left + 20) + 'px';
            tooltip.style.top = (rect.top - mapRect.top - 10) + 'px';
        }
    }
    
    function hideTooltip(playerId) {
        var tooltip = document.getElementById('tooltip-' + playerId);
        if (tooltip) {
            tooltip.style.display = 'none';
        }
    }
    
    // Recharger la page toutes les 30 secondes pour mettre à jour les positions des joueurs
    setTimeout(function() {
        location.reload();
    }, 30000);
    </script>
</body>
</html>
