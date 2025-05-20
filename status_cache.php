<?php
// Script pour mettre en cache l'état des services
// Ce script est utilisé pour stocker et récupérer l'état des services entre les rafraîchissements de page

// Définir le chemin du fichier de cache
$cache_file = __DIR__ . '/cache/server_status.json';

// Fonction pour lire l'état des services depuis le cache
function readStatusCache() {
    global $cache_file;
    
    // Créer le répertoire de cache s'il n'existe pas
    if (!file_exists(dirname($cache_file))) {
        mkdir(dirname($cache_file), 0755, true);
    }
    
    // Vérifier si le fichier de cache existe
    if (file_exists($cache_file)) {
        $cache_data = file_get_contents($cache_file);
        $cache = json_decode($cache_data, true);
        
        // Vérifier si le cache est valide
        if ($cache && isset($cache['timestamp']) && isset($cache['results'])) {
            // Vérifier si le cache n'est pas trop ancien (max 10 secondes)
            if (time() - $cache['timestamp'] <= 10) {
                return $cache;
            }
        }
    }
    
    // Retourner un cache par défaut si le cache n'existe pas ou est invalide
    return [
        'success' => true,
        'timestamp' => time(),
        'results' => [
            'mysql' => ['status' => true],  // MySQL est toujours actif
            'auth' => ['status' => false],
            'world' => ['status' => false]
        ]
    ];
}

// Fonction pour écrire l'état des services dans le cache
function writeStatusCache($data) {
    global $cache_file;
    
    // Créer le répertoire de cache s'il n'existe pas
    if (!file_exists(dirname($cache_file))) {
        mkdir(dirname($cache_file), 0755, true);
    }
    
    // Écrire les données dans le fichier de cache
    file_put_contents($cache_file, json_encode($data));
}

// Fonction pour exécuter une commande système de manière sécurisée
function executeCommand($command) {
    $output = [];
    $returnCode = 0;
    
    // Exécuter la commande
    exec($command . " 2>&1", $output, $returnCode);
    
    return [
        'output' => $output,
        'returnCode' => $returnCode
    ];
}

// Fonction pour vérifier l'état d'un service
function checkServiceStatus($service) {
    switch ($service) {
        case 'mysql':
            $result = executeCommand("systemctl is-active mysql");
            return $result['returnCode'] === 0;
        case 'auth':
            $result = executeCommand("pgrep -f authserver");
            return $result['returnCode'] === 0;
        case 'world':
            $result = executeCommand("pgrep -f worldserver");
            return $result['returnCode'] === 0;
        default:
            return false;
    }
}

// Lire l'état des services depuis le cache
$cached_status = readStatusCache();

// Vérifier l'état actuel de tous les services
$current_status = [
    'success' => true,
    'timestamp' => time(),
    'results' => [
        'mysql' => ['status' => checkServiceStatus('mysql')],
        'auth' => ['status' => checkServiceStatus('auth')],
        'world' => ['status' => checkServiceStatus('world')]
    ]
];

// Écrire l'état actuel dans le cache
writeStatusCache($current_status);

// Retourner les résultats au format JSON
header('Content-Type: application/json');
echo json_encode($current_status);
?>
