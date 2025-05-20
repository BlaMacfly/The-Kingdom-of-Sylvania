<?php
// Script de test pour vérifier l'état des serveurs

// Fonction pour vérifier si un service est en cours d'exécution
function checkServiceRunning($service) {
    $output = [];
    $returnCode = 0;
    
    if ($service === 'mysql') {
        exec("systemctl is-active --quiet mysql", $output, $returnCode);
        $running = ($returnCode === 0);
        echo "MySQL: " . ($running ? "EN LIGNE" : "HORS LIGNE") . "<br>";
        return $running;
    } elseif ($service === 'auth') {
        exec("ps aux | grep -v grep | grep -v server_status_test | grep authserver", $output, $returnCode);
        $running = ($returnCode === 0 && !empty($output));
        echo "Auth Server: " . ($running ? "EN LIGNE" : "HORS LIGNE") . "<br>";
        echo "Commande: ps aux | grep -v grep | grep -v server_status_test | grep authserver<br>";
        echo "Code de retour: $returnCode<br>";
        echo "Sortie: <pre>" . print_r($output, true) . "</pre><br>";
        return $running;
    } elseif ($service === 'world') {
        exec("ps aux | grep -v grep | grep -v server_status_test | grep worldserver", $output, $returnCode);
        $running = ($returnCode === 0 && !empty($output));
        echo "World Server: " . ($running ? "EN LIGNE" : "HORS LIGNE") . "<br>";
        echo "Commande: ps aux | grep -v grep | grep -v server_status_test | grep worldserver<br>";
        echo "Code de retour: $returnCode<br>";
        echo "Sortie: <pre>" . print_r($output, true) . "</pre><br>";
        return $running;
    }
    
    return false;
}

// Exécuter les vérifications
echo "<h1>Test de vérification de l'état des serveurs</h1>";
echo "<p>Date et heure: " . date('Y-m-d H:i:s') . "</p>";

$mysql_running = checkServiceRunning('mysql');
$auth_running = checkServiceRunning('auth');
$world_running = checkServiceRunning('world');

// Vérification directe avec exec
echo "<h2>Vérification directe avec exec</h2>";
$output = [];
exec("ps aux | grep -E 'authserver|worldserver' | grep -v grep", $output);
echo "<pre>" . implode("\n", $output) . "</pre>";
?>
