<?php
// Fichier de contrôle du serveur simplifié
session_start();

// Vérifier si l'utilisateur est autorisé (admin, GM ou modérateur)
$isAuthorized = isset($_SESSION['account_role']) && in_array($_SESSION['account_role'], ['admin', 'gm', 'moderator']);

// Traiter les actions si l'utilisateur est autorisé
if ($isAuthorized && isset($_POST['action']) && isset($_POST['service'])) {
    $action = $_POST['action'];
    $service = $_POST['service'];
    
    // Exécuter l'action demandée
    switch ($action) {
        case 'start':
            // Démarrer le serveur en utilisant le script start_sylvania.sh
            exec("/home/mccloud/wow_3.3.5/start_sylvania.sh > /home/mccloud/wow_3.3.5/logs/start_server.log 2>&1 &");
            
            // Enregistrer l'action dans un fichier de log
            file_put_contents('/home/mccloud/wow_3.3.5/sylvania-web/server_action.log', date('Y-m-d H:i:s') . " - Démarrage du serveur The Kingdom of Sylvania\n", FILE_APPEND);
            break;
            
        case 'stop':
            if ($service === 'auth') {
                exec("pkill -f authserver 2>/dev/null");
                file_put_contents('/home/mccloud/wow_3.3.5/sylvania-web/server_action.log', date('Y-m-d H:i:s') . " - Arrêt du serveur Auth\n", FILE_APPEND);
            } elseif ($service === 'world') {
                exec("pkill -f worldserver 2>/dev/null");
                exec("pkill -f authserver 2>/dev/null");
                file_put_contents('/home/mccloud/wow_3.3.5/sylvania-web/server_action.log', date('Y-m-d H:i:s') . " - Arrêt des serveurs World et Auth\n", FILE_APPEND);
            }
            break;
    }
    
    // Rediriger vers la même page pour actualiser l'état
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fonction pour vérifier si un service est en cours d'exécution
function checkServiceRunning($service) {
    if ($service === 'mysql') {
        // Vérifier si MySQL est en cours d'exécution
        exec("systemctl is-active --quiet mysql", $output, $returnCode);
        return $returnCode === 0;
    } elseif ($service === 'auth') {
        // Vérifier si authserver est en cours d'exécution
        exec("pgrep -f authserver", $output, $returnCode);
        $running = ($returnCode === 0 && !empty($output));
        
        // Enregistrer le statut dans un fichier de log
        file_put_contents('/home/mccloud/wow_3.3.5/sylvania-web/server_status.log', 
                          date('Y-m-d H:i:s') . " - Auth Server: " . ($running ? "Running" : "Stopped") . "\n", 
                          FILE_APPEND);
        return $running;
    } elseif ($service === 'world') {
        // Vérifier si worldserver est en cours d'exécution
        exec("pgrep -f worldserver", $output, $returnCode);
        $running = ($returnCode === 0 && !empty($output));
        
        // Enregistrer le statut dans un fichier de log
        file_put_contents('/home/mccloud/wow_3.3.5/sylvania-web/server_status.log', 
                          date('Y-m-d H:i:s') . " - World Server: " . ($running ? "Running" : "Stopped") . "\n", 
                          FILE_APPEND);
        return $running;
    }
    
    return false;
}

// Obtenir l'état actuel des services
$mysql_running = checkServiceRunning('mysql');
$auth_running = checkServiceRunning('auth');
$world_running = checkServiceRunning('world');

// Déterminer si les serveurs de jeu sont en cours d'exécution
$game_servers_running = $auth_running || $world_running;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrôle du serveur Sylvania WoW</title>
    <script src="server_progress.js?v=<?php echo time(); ?>"></script>
    <script src="js/simple_control.js?v=<?php echo time(); ?>"></script>
    <!-- Force l'actualisation des fichiers PHP -->
    <script>
        // Remplacer la fonction fetch native pour ajouter un timestamp à toutes les requêtes
        const originalFetch = window.fetch;
        window.fetch = function(url, options) {
            if (typeof url === 'string') {
                const timestamp = new Date().getTime();
                const separator = url.indexOf('?') !== -1 ? '&' : '?';
                url = url + separator + 't=' + timestamp;
            }
            return originalFetch(url, options);
        };
    </script>
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
            align-items: center;
        }
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #666;
            margin-right: 10px;
        }
        .status-dot.active {
            background-color: #4caf50;
            box-shadow: 0 0 5px #4caf50;
        }
        .server-control {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .control-button {
            text-align: center;
            margin: 10px;
        }
        .control-button form {
            margin-top: 10px;
        }
        .control-button button {
            padding: 8px 15px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .control-button button.stop {
            background-color: #f44336;
        }
        .control-button button:hover {
            opacity: 0.9;
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
        
        /* Styles pour les utilisateurs non autorisés */
        .unauthorized-message {
            text-align: center;
            padding: 20px;
            background-color: #333;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contrôle du serveur Sylvania WoW</h1>
        
        <!-- Barre de progression (cachée par défaut) -->
        <div id="progress-container" style="display: none; margin: 20px 0;">
            <h3 id="progress-title" style="color: #ffcc00; text-align: center;">Démarrage du serveur...</h3>
            <div style="background-color: #333; border-radius: 5px; height: 25px; margin: 10px 0; overflow: hidden;">
                <div id="progress-bar" style="background-color: #ffcc00; height: 100%; width: 0%; transition: width 0.5s ease;"></div>
            </div>
            <p id="progress-status" style="text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0;">Initialisation...</p>
            
            <!-- Message détaillé -->
            <div id="progress-message" style="text-align: center; background-color: rgba(0,0,0,0.2); padding: 15px; border-radius: 5px; margin: 10px 0; min-height: 50px;">Préparation du démarrage des serveurs...</div>
            
            <!-- Journal de démarrage -->
            <div id="startup-log" style="margin-top: 20px; max-height: 200px; overflow-y: auto; background-color: rgba(0,0,0,0.3); border-radius: 5px; padding: 10px; font-family: monospace;">
                <div class="log-entry" style="padding: 5px; border-left: 3px solid #ffcc00;">
                    <span class="log-time" style="color: #aaa; margin-right: 10px;">[<?php echo date('H:i:s'); ?>]</span>
                    <span class="log-message">Initialisation du processus de démarrage...</span>
                </div>
            </div>
            
            <div id="progress-steps" style="margin-top: 20px;">
                <div class="progress-step" style="margin-bottom: 10px; padding: 8px; border-radius: 5px; background-color: rgba(255,255,255,0.1);">
                    <span class="step-icon" style="display: inline-block; width: 20px; margin-right: 10px;">⚪</span>
                    <span class="step-text">Arrêt des instances précédentes</span>
                </div>
                <div class="progress-step" style="margin-bottom: 10px; padding: 8px; border-radius: 5px; background-color: rgba(255,255,255,0.1);">
                    <span class="step-icon" style="display: inline-block; width: 20px; margin-right: 10px;">⚪</span>
                    <span class="step-text">Vérification de MySQL</span>
                </div>
                <div class="progress-step" style="margin-bottom: 10px; padding: 8px; border-radius: 5px; background-color: rgba(255,255,255,0.1);">
                    <span class="step-icon" style="display: inline-block; width: 20px; margin-right: 10px;">⚪</span>
                    <span class="step-text">Démarrage du serveur Auth</span>
                </div>
                <div class="progress-step" style="margin-bottom: 10px; padding: 8px; border-radius: 5px; background-color: rgba(255,255,255,0.1);">
                    <span class="step-icon" style="display: inline-block; width: 20px; margin-right: 10px;">⚪</span>
                    <span class="step-text">Démarrage du serveur World</span>
                </div>
                <div class="progress-step" style="margin-bottom: 10px; padding: 8px; border-radius: 5px; background-color: rgba(255,255,255,0.1);">
                    <span class="step-icon" style="display: inline-block; width: 20px; margin-right: 10px;">⚪</span>
                    <span class="step-text">Vérification finale</span>
                </div>
            </div>
            
            <!-- Bouton pour terminer -->
            <div id="finish-button-container" style="text-align: center; margin-top: 20px; display: none;">
                <button id="finish-button" style="padding: 10px 20px; background-color: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;" onclick="window.location.reload();">Terminer</button>
            </div>
        </div>
        
        <div class="server-status">
            <div class="status-item">
                <div class="status-dot <?php echo $mysql_running ? 'active' : ''; ?>"></div>
                <span>MySQL</span>
            </div>
            <div class="status-item">
                <div class="status-dot <?php echo $auth_running ? 'active' : ''; ?>"></div>
                <span>Auth</span>
            </div>
            <div class="status-item">
                <div class="status-dot <?php echo $world_running ? 'active' : ''; ?>"></div>
                <span>World</span>
            </div>
        </div>
        
        <?php if ($isAuthorized): ?>
            <div class="server-control">
                <div class="control-button">
                    <form method="post" action="server_buttons.php">
                        <input type="hidden" name="action" value="<?php echo $game_servers_running ? 'stop' : 'start'; ?>">
                        <input type="hidden" name="service" value="world">
                        <div class="server-icon">🌐</div>
                        <div>ON/OFF</div>
                        <button type="submit" class="<?php echo $game_servers_running ? 'stop' : ''; ?>"><?php echo $game_servers_running ? 'OFF' : 'ON'; ?></button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="unauthorized-message">
                <p>Vous n'êtes pas autorisé à contrôler le serveur.</p>
                <p>Veuillez vous connecter avec un compte administrateur, GM ou modérateur.</p>
            </div>
        <?php endif; ?>
        
        <a href="home.php" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>
