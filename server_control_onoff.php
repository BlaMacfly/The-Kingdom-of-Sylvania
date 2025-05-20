<?php
// Contrôleur de serveur simplifié - Version tmux
// Utilise tmux pour gérer les serveurs AzerothCore de manière fiable

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est authentifié et a un niveau GM entre 1 et 3
$isAuthorized = isset($_SESSION['account_username']) && isset($_SESSION['account_role']) && 
               in_array($_SESSION['account_role'], ['moderator', 'gm', 'admin']);

// Rediriger vers la page d'authentification si nécessaire
if (!$isAuthorized) {
    header("Location: server_auth_simple.php");
    exit;
}

// Définir un nom d'utilisateur par défaut pour les logs
if (!isset($_SESSION['account_username'])) {
    $_SESSION['account_username'] = 'Visiteur';
}

// Configuration tmux
$authSession = "ac_auth";
$worldSession = "ac_world";
$azerothcorePath = "/home/mccloud/wow_3.3.5/mccloud/azerothcore/bin";
$configPath = "/home/mccloud/wow_3.3.5/mccloud/azerothcore/etc";
$dataPath = "/home/mccloud/wow_3.3.5/mccloud/azerothcore/data";

// Vérifier si les chemins existent
if (!file_exists($azerothcorePath)) {
    error_log("[Server Control] ERREUR: Le chemin du bin n'existe pas: $azerothcorePath");
}

if (!file_exists($configPath)) {
    error_log("[Server Control] ERREUR: Le chemin de configuration n'existe pas: $configPath");
}

// Utiliser error_log de PHP au lieu d'un fichier spécifique pour éviter les problèmes de permission
function logAction($message) {
    error_log("[Server Control] " . $message);
}

// Fonction pour vérifier si un processus est en cours d'exécution
function isProcessRunning($processName) {
    $output = [];
    $returnCode = 0;
    exec("ps aux | grep -v grep | grep '$processName'", $output, $returnCode);
    return ($returnCode === 0 && !empty($output));
}

// Fonction pour vérifier si une session tmux est active
function isTmuxSessionRunning($session) {
    // Vérifier d'abord avec tmux
    $output = shell_exec("tmux has-session -t $session 2>&1");
    $tmuxRunning = strpos($output, 'no server running') === false && strpos($output, 'failed to connect') === false;
    
    // Si tmux dit que c'est en cours d'exécution, vérifier aussi avec ps
    if ($tmuxRunning) {
        if ($session === 'ac_auth') {
            return isProcessRunning('authserver');
        } elseif ($session === 'ac_world') {
            return isProcessRunning('worldserver');
        }
    }
    
    return false;
}

// Fonction pour démarrer les serveurs directement
function startServers($authSession, $worldSession, $azerothcorePath, $configPath) {
    // Journalisation du début
    logAction("Début du démarrage des serveurs par " . $_SESSION['account_username']);
    
    // Utiliser le script de démarrage web
    $scriptPath = "/home/mccloud/wow_3.3.5/sylvania-web/start_servers_web.sh";
    
    // Exécuter le script avec sudo comme pour l'arrêt
    logAction("Exécution du script de démarrage avec sudo");
    $output = shell_exec("sudo $scriptPath 2>&1");
    
    // Journaliser la sortie du script
    logAction("Résultat du script: " . trim($output));
    
    // Vérifier si les serveurs sont en cours d'exécution
    sleep(5); // Attendre un peu pour que les serveurs démarrent
    
    $authRunning = isProcessRunning('authserver');
    $worldRunning = isProcessRunning('worldserver');
    
    if ($authRunning) {
        logAction("authserver démarré avec succès");
    } else {
        logAction("ERREUR: authserver n'a pas démarré");
    }
    
    if ($worldRunning) {
        logAction("worldserver démarré avec succès");
    } else {
        logAction("ERREUR: worldserver n'a pas démarré");
    }
    
    return ($authRunning && $worldRunning);
}

// Fonction pour arrêter les serveurs
function stopServers($authSession, $worldSession) {
    // Journalisation du début
    logAction("Début de l'arrêt des serveurs par " . $_SESSION['account_username']);
    
    // Utiliser le script d'arrêt amélioré
    $scriptPath = "/home/mccloud/wow_3.3.5/sylvania-web/improved_stop_servers.sh";
    
    // Exécuter le script avec sudo
    logAction("Exécution du script d'arrêt amélioré");
    $output = shell_exec("sudo $scriptPath 2>&1");
    
    // Journaliser la sortie du script
    logAction("Résultat du script: " . trim($output));
    
    // Vérifier si les serveurs sont arrêtés
    sleep(2);
    
    $worldStopped = !isProcessRunning('worldserver');
    $authStopped = !isProcessRunning('authserver');
    
    if ($worldStopped) {
        logAction("worldserver arrêté avec succès");
    } else {
        logAction("ERREUR: Impossible d'arrêter worldserver");
    }
    
    if ($authStopped) {
        logAction("authserver arrêté avec succès");
    } else {
        logAction("ERREUR: Impossible d'arrêter authserver");
    }
    
    // Vérification finale
    if ($worldStopped && $authStopped) {
        logAction("Tous les serveurs arrêtés avec succès");
        return true;
    } else {
        logAction("ATTENTION: Certains serveurs n'ont pas pu être arrêtés");
        return false;
    }
}

// Fonction pour vérifier si un service est en cours d'exécution (pour compatibilité)
function checkServiceRunning($service) {
    global $authSession, $worldSession;
    
    if ($service === 'mysql') {
        // Vérifier si MySQL est en cours d'exécution
        return isProcessRunning('mysql') || isProcessRunning('mysqld');
    } elseif ($service === 'auth') {
        // Vérifier si authserver est en cours d'exécution
        return isProcessRunning('authserver');
    } elseif ($service === 'world') {
        // Vérifier si worldserver est en cours d'exécution
        return isProcessRunning('worldserver');
    }
    
    return false;
}

// Fonction pour obtenir les dernières lignes du log du worldserver - supprimée car non utilisée

// Traiter les actions si l'utilisateur est autorisé
if ($isAuthorized && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Exécuter l'action demandée
    switch ($action) {
        case 'start':
            // Enregistrer le début du démarrage dans le log
            logAction("Début du démarrage des serveurs par " . $_SESSION['account_username']);
            
            // Démarrer les serveurs avec tmux
            $success = startServers($authSession, $worldSession, $azerothcorePath, $configPath);
            
            // Message de succès
            $message = "Démarrage des serveurs AzerothCore initié avec tmux";
            
            // Enregistrer l'action dans le log
            logAction("Commande de démarrage tmux envoyée");
            
            // Rediriger pour éviter que la page ne reste bloquée
            header("Location: server_control_onoff.php?message=" . urlencode("Démarrage des serveurs en cours..."));
            exit;
            break;
            
        case 'stop':
            // Enregistrer le début de l'arrêt dans le log
            logAction("Début de l'arrêt des serveurs par " . $_SESSION['account_username']);
            
            // Arrêter les serveurs
            $success = stopServers($authSession, $worldSession);
            
            // Message de succès
            $message = "Arrêt des serveurs AzerothCore initié avec tmux";
            
            // Enregistrer l'action dans le log
            logAction("Commande d'arrêt tmux envoyée");
            
            // Rediriger pour éviter que la page ne reste bloquée
            header("Location: server_control_onoff.php?message=" . urlencode("Arrêt des serveurs en cours..."));
            exit;
            break;
    }
    
    // Enregistrer l'action dans un fichier de log
    file_put_contents('/home/mccloud/wow_3.3.5/sylvania-web/server_action.log', 
                     date('Y-m-d H:i:s') . " - " . $message . " par " . $_SESSION['account_username'] . "\n", 
                     FILE_APPEND);
}

// Forcer une vérification précise de l'état des services
function forceServiceCheck($service) {
    // Vider le cache des vérifications précédentes
    clearstatcache();
    
    if ($service === 'mysql') {
        // Vérification directe de MySQL
        exec("systemctl is-active mysql", $output, $returnCode);
        return (isset($output[0]) && $output[0] === 'active');
    } elseif ($service === 'auth') {
        // Vérification directe du processus authserver
        exec("ps aux | grep -v grep | grep 'authserver'", $output);
        return !empty($output);
    } elseif ($service === 'world') {
        // Vérification directe du processus worldserver
        exec("ps aux | grep -v grep | grep 'worldserver'", $output);
        return !empty($output);
    }
    
    return false;
}

// Obtenir l'état actuel des services avec vérification forcée
$mysql_running = checkServiceRunning('mysql');
$auth_running = checkServiceRunning('auth');
$world_running = checkServiceRunning('world');

// Enregistrer les résultats pour débogage
file_put_contents('/tmp/service_status.log', date('Y-m-d H:i:s') . " - Status check: MySQL: " . ($mysql_running ? 'running' : 'stopped') . ", Auth: " . ($auth_running ? 'running' : 'stopped') . ", World: " . ($world_running ? 'running' : 'stopped') . "\n", FILE_APPEND);

// Déterminer si les serveurs de jeu sont en cours d'exécution
$game_servers_running = $auth_running || $world_running;

// Message de succès si une action a été effectuée ou si un message est passé en paramètre
$success_message = '';
if (isset($message)) {
    $success_message = $message;
} elseif (isset($_GET['message'])) {
    $success_message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrôle du serveur - The Kingdom of Sylvania</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        h1 {
            color: #ffcc00;
            text-align: center;
            margin-bottom: 30px;
        }
        .server-status {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #333;
            border-radius: 5px;
        }
        .status-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 15px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            min-width: 100px;
            text-align: center;
        }
        .status-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #666;
            margin-bottom: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .status-dot.active {
            background-color: #4caf50;
            box-shadow: 0 0 10px #4caf50;
            border-color: rgba(76, 175, 80, 0.3);
        }
        .status-dot.inactive {
            background-color: #f44336;
            box-shadow: 0 0 10px #f44336;
            border-color: rgba(244, 67, 54, 0.3);
        }
        .status-text {
            font-size: 12px;
            margin-top: 5px;
            font-weight: bold;
            color: #aaa;
        }
        .status-text:empty:before {
            content: '\00a0';
        }
        .server-control {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .control-button {
            margin: 20px 0;
            text-align: center;
        }
        .power-button {
            display: inline-block;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #f44336; /* Rouge par défaut (OFF) */
            color: white;
            text-align: center;
            line-height: 80px;
            font-size: 40px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            border: 4px solid #333;
        }
        .power-button.on {
            background-color: #4CAF50; /* Vert quand ON */
        }
        .power-button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }
        .power-button:active {
            transform: scale(0.95);
        }
        .power-button i {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .power-status {
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .server-icon {
            font-size: 48px;
            margin-bottom: 10px;
            color: #ffcc00;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ffcc00;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .success-message {
            background-color: rgba(76, 175, 80, 0.2);
            border-left: 4px solid #4caf50;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        .unauthorized-message {
            text-align: center;
            padding: 20px;
            background-color: #333;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        /* Styles de la console supprimés car non utilisés */
        /* Styles du bouton d'actualisation supprimés car non utilisés */
        .auto-refresh {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
    <script>
        // Fonction pour actualiser la page automatiquement
        function setupAutoRefresh() {
            const checkbox = document.getElementById('auto-refresh');
            let refreshInterval;
            
            function toggleAutoRefresh() {
                if (checkbox.checked) {
                    refreshInterval = setInterval(() => {
                        window.location.reload();
                    }, 5000); // Actualiser toutes les 5 secondes
                } else {
                    clearInterval(refreshInterval);
                }
            }
            
            if (checkbox) {
                checkbox.addEventListener('change', toggleAutoRefresh);
                toggleAutoRefresh(); // Initialiser selon l'état initial
            }
        }
        
        // Exécuter lorsque la page est chargée
        document.addEventListener('DOMContentLoaded', function() {
            setupAutoRefresh();
            
            // Faire défiler la console jusqu'en bas
            const consoleOutput = document.querySelector('.console-output');
            if (consoleOutput) {
                consoleOutput.scrollTop = consoleOutput.scrollHeight;
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Contrôle du serveur Sylvania WoW</h1>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="server-status">
            <div class="status-item">
                <div class="status-dot <?php echo $mysql_running ? 'active' : 'inactive'; ?>"></div>
                <span>MySQL</span>
                <span class="status-text"><?php echo $mysql_running ? 'En ligne' : 'Hors ligne'; ?></span>
            </div>
            <div class="status-item">
                <div class="status-dot <?php echo $auth_running ? 'active' : 'inactive'; ?>"></div>
                <span>Auth</span>
                <span class="status-text"><?php echo $auth_running ? 'En ligne' : 'Hors ligne'; ?></span>
            </div>
            <div class="status-item">
                <div class="status-dot <?php echo $world_running ? 'active' : 'inactive'; ?>"></div>
                <span>World</span>
                <span class="status-text"><?php echo $world_running ? 'En ligne' : 'Hors ligne'; ?></span>
            </div>
        </div>
        
        <?php if ($isAuthorized): ?>
            <div class="server-control">
                <div class="control-button">
                    <!-- Bouton d'alimentation pour démarrer ou arrêter le serveur -->
                    <a href="server_control_onoff.php?action=<?php echo $game_servers_running ? 'stop' : 'start'; ?>">
                        <div class="power-button <?php echo $game_servers_running ? 'on' : ''; ?>">
                            <i class="fas fa-power-off"></i>
                        </div>
                        <div class="power-status">
                            <?php echo $game_servers_running ? 'Serveur en ligne' : 'Serveur hors ligne'; ?>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="auto-refresh">
                <label>
                    <input type="checkbox" id="auto-refresh" checked> Actualisation automatique (5s)
                </label>
            </div>
        <?php endif; ?>
        
        
        <a href="home.php" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>
