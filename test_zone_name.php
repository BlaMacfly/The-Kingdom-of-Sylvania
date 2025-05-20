<?php
// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";
$db_characters = "acore_characters";
$db_world = "acore_world";

// Fonction pour obtenir le nom de la zone
function getZoneName($zoneId) {
    global $db_host, $db_port, $db_username, $db_password, $db_world;
    
    try {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_world", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Utiliser la table areatable_dbc au lieu de area_template
        // et récupérer le nom en français (AreaName_Lang_frFR)
        $query = "SELECT AreaName_Lang_frFR FROM areatable_dbc WHERE ID = :zoneId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':zoneId', $zoneId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['AreaName_Lang_frFR'])) {
            return $result['AreaName_Lang_frFR'];
        } else {
            // Si le nom français n'est pas disponible, essayer avec le nom anglais
            $query = "SELECT AreaName_Lang_enUS FROM areatable_dbc WHERE ID = :zoneId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':zoneId', $zoneId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['AreaName_Lang_enUS'])) {
                return $result['AreaName_Lang_enUS'];
            } else {
                return "Zone inconnue";
            }
        }
    } catch (PDOException $e) {
        return "Zone inconnue (Erreur: " . $e->getMessage() . ")";
    }
}

// Tester la fonction avec quelques IDs de zones courantes
$zoneIds = [1, 12, 14, 85, 130, 1519, 1637, 2817, 3430, 3537, 4080];

echo "<h1>Test de la fonction getZoneName</h1>";
echo "<table border='1'>";
echo "<tr><th>ID de Zone</th><th>Nom de Zone</th></tr>";

foreach ($zoneIds as $zoneId) {
    $zoneName = getZoneName($zoneId);
    echo "<tr><td>$zoneId</td><td>$zoneName</td></tr>";
}

echo "</table>";
?>
