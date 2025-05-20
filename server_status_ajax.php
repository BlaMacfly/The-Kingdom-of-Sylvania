<?php
// Fonction pour vérifier si un service est en cours d'exécution
function isServiceRunning($service) {
    $output = [];
    $return_var = 0;
    
    if ($service === 'auth') {
        exec("pgrep -f authserver", $output, $return_var);
    } elseif ($service === 'world') {
        exec("pgrep -f worldserver", $output, $return_var);
    }
    
    return ($return_var === 0 && !empty($output));
}

// Vérifier l'état des serveurs
$auth_running = isServiceRunning('auth');
$world_running = isServiceRunning('world');

// Préparer la réponse JSON
$response = [
    'auth' => $auth_running,
    'world' => $world_running,
    'timestamp' => date('Y-m-d H:i:s')
];

// Envoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
