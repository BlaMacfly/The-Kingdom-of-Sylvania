<?php
// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_characters = "acore_characters";

// Récupérer quelques joueurs pour analyser leurs coordonnées
try {
    $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_characters", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "SELECT 
                name, 
                map, 
                zone, 
                position_x, 
                position_y, 
                position_z
            FROM 
                characters
            WHERE 
                level > 1
            ORDER BY 
                level DESC
            LIMIT 20";
    
    $stmt = $conn->query($query);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    $players = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test des coordonnées</title>
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background-color: #2c3347;
            color: #EABA28;
        }
        .map-info {
            margin-top: 30px;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Analyse des coordonnées des joueurs</h1>
    
    <p>Cette page affiche les coordonnées brutes de quelques joueurs pour nous aider à comprendre comment les convertir correctement en positions sur la carte.</p>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Map ID</th>
                <th>Zone ID</th>
                <th>Position X</th>
                <th>Position Y</th>
                <th>Position Z</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?php echo $player['name']; ?></td>
                    <td><?php echo $player['map']; ?></td>
                    <td><?php echo $player['zone']; ?></td>
                    <td><?php echo round($player['position_x'], 2); ?></td>
                    <td><?php echo round($player['position_y'], 2); ?></td>
                    <td><?php echo round($player['position_z'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="map-info">
        <h2>Informations sur les cartes</h2>
        <p><strong>Map ID 0</strong>: Royaumes de l'Est (Eastern Kingdoms)</p>
        <p><strong>Map ID 1</strong>: Kalimdor</p>
        <p><strong>Map ID 530</strong>: Outreterre (Outland)</p>
        <p><strong>Map ID 571</strong>: Norfendre (Northrend)</p>
        
        <h3>Dimensions des cartes dans l'interface</h3>
        <p>Largeur: 966px, Hauteur: 732px</p>
        <p>Centre X: 483px, Centre Y: 366px</p>
    </div>
</body>
</html>
