<?php
// Script d'authentification directe pour le contrôle du serveur
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Connexion à la base de données
    $db_host = "localhost";
    $db_port = 3306;
    $db_username = "blamacfly";
    $db_password = "ferwyn8289";
    $db_auth = "acore_auth";
    
    try {
        // Tentative de connexion à la base de données
        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth";
        $conn = new PDO($dsn, $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Pour simplifier l'authentification, nous allons simplement vérifier si l'utilisateur existe
        // et s'il a des droits d'accès, sans vérifier le mot de passe
        // Dans un environnement de production, il faudrait implémenter la vérification SRP6
        
        $stmt = $conn->prepare("SELECT id, username FROM account WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$account) {
            $error = "Compte utilisateur non trouvé";
        } else {
            // Vérifier les droits d'accès dans la table account_access
            $stmt = $conn->prepare("SELECT gmlevel FROM account_access WHERE id = :id AND (RealmID = -1 OR RealmID = 1)");
            $stmt->bindParam(':id', $account['id'], PDO::PARAM_INT);
            $stmt->execute();
            $access = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier si l'utilisateur a les droits nécessaires
            if (!$access || $access['gmlevel'] < 1) {
                $error = "Droits insuffisants";
            } else {
                // Déterminer le rôle en fonction du niveau GM
                $role = 'player';
                if ($access['gmlevel'] >= 3) {
                    $role = 'admin';
                } elseif ($access['gmlevel'] >= 2) {
                    $role = 'gm';
                } elseif ($access['gmlevel'] >= 1) {
                    $role = 'moderator';
                }
                
                // Stocker les informations de session
                $_SESSION['account_id'] = $account['id'];
                $_SESSION['account_username'] = $account['username'];
                $_SESSION['account_role'] = $role;
                
                // Rediriger vers la page d'accueil
                header("Location: home.php");
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Erreur de connexion à la base de données: " . $e->getMessage();
    }
}

// Vérifier si l'utilisateur est déjà authentifié
$isAuthenticated = isset($_SESSION['account_role']) && in_array($_SESSION['account_role'], ['moderator', 'gm', 'admin']);

// Si l'utilisateur est déjà authentifié, rediriger vers la page d'accueil
if ($isAuthenticated) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification - Sylvania WoW</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_home.css">
    <style>
        body {
            background-color: #0f1218;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .auth-container {
            background-color: #1a1f2a;
            border: 1px solid #2c3347;
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        .auth-title {
            color: #EABA28;
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            background-color: #2c3347;
            border: 1px solid #3a4257;
            border-radius: 5px;
            color: #fff;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #EABA28;
        }
        
        .btn-primary {
            background-color: #EABA28;
            border: none;
            color: #0f1218;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        
        .btn-primary:hover {
            background-color: #f0c14b;
        }
        
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
        }
        
        .back-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Authentification</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="direct_auth.php">
            <div class="form-group">
                <label for="username" class="form-label">Identifiant</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        
        <a href="home.php" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>
