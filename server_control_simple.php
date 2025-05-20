<?php
// Contrôleur de serveur simplifié - Version directe
session_start();

// Vérifier si l'utilisateur est authentifié
$isAuthorized = isset($_SESSION['account_username']) && isset($_SESSION['account_role']) && 
               in_array($_SESSION['account_role'], ['moderator', 'gm', 'admin']);

if (!$isAuthorized) {
    header("Location: server_auth_simple.php");
    exit;
}

// Fonction pour vérifier si un service est en cours d'exécution
function isProcessRunning($processName) {
    $command = "ps aux | grep -v grep | grep '$processName'";
    exec($command, $output, $returnCode);
    return $returnCode === 0 && !empty($output);
}

// Vérifier l'état des serveurs
$auth_running = isProcessRunning("authserver");
$world_running = isProcessRunning("worldserver");

// Message si un message est passé en paramètre
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
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
            <a href="start_server_direct.php" class="btn btn-on">Démarrer</a>
            <a href="stop_server_direct.php" class="btn btn-off">Arrêter</a>
        </div>
        
        <div class="refresh">
            <p>Dernière vérification: <?php echo date('Y-m-d H:i:s'); ?></p>
            <a href="server_control_simple.php">Actualiser la page</a>
        </div>
    </div>
</body>
</html>
