<?php
// Contrôleur de serveur AzerothCore utilisant tmux
session_start();

// Vérifier si l'utilisateur est authentifié
$isAuthorized = isset($_SESSION['account_username']) && isset($_SESSION['account_role']) && 
               in_array($_SESSION['account_role'], ['moderator', 'gm', 'admin']);

if (!$isAuthorized) {
    header("Location: server_auth_simple.php");
    exit;
}

// Configuration
$authSession = "ac_auth";
$worldSession = "ac_world";
$azerothcorePath = "/home/mccloud/wow_3.3.5/mccloud/azerothcore/env/dist/bin";
$configPath = "/home/mccloud/wow_3.3.5/mccloud/azerothcore/env/dist/etc";
$logFile = "/home/mccloud/wow_3.3.5/sylvania-web/server_action.log";

// Fonction pour vérifier si une session tmux est active
function isTmuxSessionRunning($session) {
    $output = shell_exec("tmux has-session -t $session 2>&1");
    return strpos($output, 'no server running') === false && strpos($output, 'failed to connect') === false;
}

// Fonction pour démarrer les serveurs
function startServers($authSession, $worldSession, $azerothcorePath, $configPath, $logFile) {
    // Arrêter les sessions existantes si elles existent
    shell_exec("tmux kill-session -t $authSession 2>/dev/null");
    shell_exec("tmux kill-session -t $worldSession 2>/dev/null");
    
    // Attendre que les sessions soient bien fermées
    sleep(2);
    
    // Démarrer authserver dans une session tmux
    $authCommand = "cd $azerothcorePath && ./authserver -c $configPath/authserver.conf";
    shell_exec("tmux new-session -d -s $authSession '$authCommand'");
    
    // Attendre que authserver soit bien démarré
    sleep(3);
    
    // Démarrer worldserver dans une session tmux
    $worldCommand = "cd $azerothcorePath && ./worldserver -c $configPath/worldserver.conf -DFOREGROUND";
    shell_exec("tmux new-session -d -s $worldSession '$worldCommand'");
    
    // Journalisation
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Démarrage des serveurs avec tmux\n", FILE_APPEND);
    
    return true;
}

// Fonction pour arrêter les serveurs
function stopServers($authSession, $worldSession, $logFile) {
    // Arrêter d'abord worldserver
    shell_exec("tmux kill-session -t $worldSession 2>/dev/null");
    sleep(2);
    
    // Ensuite arrêter authserver
    shell_exec("tmux kill-session -t $authSession 2>/dev/null");
    
    // Journalisation
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Arrêt des serveurs avec tmux\n", FILE_APPEND);
    
    return true;
}

// Traiter les actions
$message = "";
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'start') {
        startServers($authSession, $worldSession, $azerothcorePath, $configPath, $logFile);
        $message = "Démarrage des serveurs AzerothCore initié avec tmux";
    } elseif ($action === 'stop') {
        stopServers($authSession, $worldSession, $logFile);
        $message = "Arrêt des serveurs AzerothCore initié avec tmux";
    }
    
    // Journalisation de l'action
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message par " . $_SESSION['account_username'] . "\n", FILE_APPEND);
}

// Vérifier l'état actuel des serveurs
$auth_running = isTmuxSessionRunning($authSession);
$world_running = isTmuxSessionRunning($worldSession);
$servers_running = $auth_running && $world_running;

// Message si passé en paramètre
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrôle du serveur - The Kingdom of Sylvania</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .status {
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .online {
            background-color: #d4edda;
            color: #155724;
        }
        .offline {
            background-color: #f8d7da;
            color: #721c24;
        }
        .controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-on {
            background-color: #28a745;
            color: white;
        }
        .btn-off {
            background-color: #dc3545;
            color: white;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #cce5ff;
            color: #004085;
            text-align: center;
        }
        .refresh {
            text-align: center;
            margin-top: 20px;
        }
        .refresh a {
            color: #007bff;
            text-decoration: none;
        }
        .refresh a:hover {
            text-decoration: underline;
        }
        .tmux-info {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contrôle du serveur Sylvania WoW</h1>
        
        <?php if (!empty($message)): ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <h2>État des serveurs</h2>
        
        <div class="status <?php echo $auth_running ? 'online' : 'offline'; ?>">
            Serveur Auth: <?php echo $auth_running ? 'EN LIGNE' : 'HORS LIGNE'; ?>
        </div>
        
        <div class="status <?php echo $world_running ? 'online' : 'offline'; ?>">
            Serveur World: <?php echo $world_running ? 'EN LIGNE' : 'HORS LIGNE'; ?>
        </div>
        
        <div class="controls">
            <a href="server_control_tmux.php?action=start" class="btn btn-on">Démarrer</a>
            <a href="server_control_tmux.php?action=stop" class="btn btn-off">Arrêter</a>
        </div>
        
        <div class="tmux-info">
            <h3>Sessions tmux</h3>
            <p>Les serveurs sont gérés via tmux. Pour voir les logs en direct :</p>
            <ul>
                <li>Auth Server: <code>tmux attach -t <?php echo $authSession; ?></code></li>
                <li>World Server: <code>tmux attach -t <?php echo $worldSession; ?></code></li>
            </ul>
            <p>Pour quitter une session tmux sans l'arrêter : <code>Ctrl+B puis D</code></p>
        </div>
        
        <div class="refresh">
            <p>Dernière vérification: <?php echo date('Y-m-d H:i:s'); ?></p>
            <a href="server_control_tmux.php">Actualiser la page</a>
        </div>
    </div>
</body>
</html>
