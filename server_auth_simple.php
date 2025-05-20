<?php
// Version simplifiée du fichier d'authentification pour le contrôle du serveur
session_start();

// Activer la journalisation des erreurs dans un fichier
ini_set('display_errors', 1); // Afficher les erreurs pour le débogage
ini_set('log_errors', 1);
ini_set('error_log', '/home/mccloud/wow_3.3.5/sylvania-web/auth_error_simple.log');

// Fonction pour enregistrer des messages de débogage
function debug_log($message) {
    error_log(date('Y-m-d H:i:s') . ' - ' . $message);
}

debug_log("Début du script d'authentification");

// Vérifier si le formulaire a été soumis
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    debug_log("Tentative de connexion pour l'utilisateur: " . $username);
    
    try {
        // Connexion à la base de données MySQL
        $db_host = 'localhost';
        $db_user = 'blamacfly';
        $db_pass = 'ferwyn8289';
        $db_name = 'acore_auth';
        
        debug_log("Tentative de connexion à la base de données");
        
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn->connect_error) {
            debug_log("Erreur de connexion à la base de données: " . $conn->connect_error);
            throw new Exception("Erreur de connexion à la base de données: " . $conn->connect_error);
        }
        
        debug_log("Connexion à la base de données réussie");
        
        // Requête pour vérifier si l'utilisateur existe et obtenir son niveau d'accès
        $sql = "SELECT a.id, a.username, COALESCE(aa.gmlevel, 0) as gmlevel 
               FROM account a 
               LEFT JOIN account_access aa ON a.id = aa.id 
               WHERE a.username = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            debug_log("Erreur de préparation de la requête: " . $conn->error);
            throw new Exception("Erreur de préparation de la requête: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            debug_log("Utilisateur trouvé: " . $user['username'] . " avec niveau GM: " . $user['gmlevel']);
            
            // Déterminer le rôle en fonction du niveau GM
            $role = 'player';
            if ($user['gmlevel'] >= 3) {
                $role = 'admin';
            } elseif ($user['gmlevel'] == 2) {
                $role = 'gm';
            } elseif ($user['gmlevel'] == 1) {
                $role = 'moderator';
            }
            
            // Vérifier si l'utilisateur a un niveau GM entre 1 et 3
            if ($user['gmlevel'] >= 1 && $user['gmlevel'] <= 3) {
                // Stocker les informations de l'utilisateur dans la session
                $_SESSION['account_id'] = $user['id'];
                $_SESSION['account_username'] = $user['username'];
                $_SESSION['account_role'] = $role;
                
                debug_log("Authentification réussie pour l'utilisateur: " . $user['username'] . " avec le rôle: " . $role);
                
                // Rediriger vers la page de contrôle du serveur ON/OFF
                header("Location: server_control_onoff.php");
                exit;
            } else {
                debug_log("Accès refusé pour l'utilisateur: " . $user['username'] . " (niveau GM: " . $user['gmlevel'] . ")");
                $error_message = "Vous n'avez pas les droits suffisants pour accéder au contrôle du serveur. Niveau GM requis: 1-3.";
            }
        } else {
            debug_log("Utilisateur non trouvé: " . $username);
            $error_message = "Identifiants incorrects. Veuillez réessayer.";
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        debug_log("Exception: " . $e->getMessage());
        $error_message = "Une erreur est survenue lors de l'authentification. Veuillez réessayer plus tard.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification - Contrôle du serveur Sylvania WoW</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #0f1218;
            background-image: url('images/dragon-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }
        
        .auth-container {
            max-width: 400px;
            padding: 30px;
            background-color: rgba(26, 31, 42, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(234, 186, 40, 0.3);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h1 {
            color: #EABA28;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            letter-spacing: 1px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #EABA28;
            font-weight: 500;
            font-size: 16px;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.4);
            color: #f0f0f0;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-sizing: border-box;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(234, 186, 40, 0.5);
            background-color: rgba(0, 0, 0, 0.6);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background-color: #EABA28;
            color: #0f1218;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        
        button:hover {
            background-color: #f0c840;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }
        
        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        
        .error-message {
            color: #ff6666;
            margin-bottom: 20px;
            text-align: center;
            background-color: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 8px;
            border-left: 3px solid #ff6666;
        }
        
        .access-notice {
            color: #ff6666;
            margin: 20px 0;
            text-align: center;
            background-color: rgba(255, 0, 0, 0.1);
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #ff6666;
            font-weight: 500;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #EABA28;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: #f0c840;
            text-decoration: underline;
        }
        
        .icon-input {
            position: relative;
        }
        
        .icon-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #EABA28;
        }
        
        .icon-input input {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1><i class="fas fa-shield-alt"></i> Authentification</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="access-notice">
            <i class="fas fa-exclamation-triangle"></i> <strong>Attention:</strong> Seuls les utilisateurs ayant un niveau d'accès GM 1-3 peuvent utiliser le panneau de contrôle du serveur.
        </div>
        
        <form method="post" action="server_auth_simple.php">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                <div class="icon-input">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Votre nom d'utilisateur" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                <div class="icon-input">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
                </div>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
        </form>
        
        <a href="home.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
    </div>
</body>
</html>
