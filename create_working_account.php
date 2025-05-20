<?php
/**
 * Script de création de compte fonctionnel pour AzerothCore
 * Ce script crée un compte en utilisant exactement les mêmes valeurs que arkineos82
 * mais avec un nom d'utilisateur et un mot de passe différents.
 */

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Fonction pour créer un compte avec les mêmes valeurs que arkineos82
function createCloneAccount($pdo, $username, $email) {
    // Supprimer le compte s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => strtoupper($username)]);
    
    // Insérer le compte avec les mêmes valeurs que arkineos82
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, UNHEX('6B404F56DCD0F5440D590DDCDC9C12C3389501E7A12841B6B557D4AEDD8BCF17'), 
         UNHEX('BB1C8704E782F2441C2BE1D2154A5153D79954AB4F3893A83C0E2E0E7456C288'), 
         :email, :reg_mail, NOW())
    ");
    
    $username_upper = strtoupper($username);
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    // Récupérer le compte créé
    $stmt = $pdo->prepare("SELECT id, username, email FROM account WHERE username = :username");
    $stmt->execute(['username' => $username_upper]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Traitement du formulaire
$success = false;
$error = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($username) || empty($email)) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = 'Le nom d\'utilisateur doit comporter entre 3 et 20 caractères.';
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error = 'Le nom d\'utilisateur ne peut contenir que des lettres et des chiffres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse e-mail invalide.';
    } else {
        try {
            // Connexion à la base de données
            $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
            $pdo = new PDO($dsn, $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Vérification si le nom d'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT 1 FROM account WHERE username = :username");
            $stmt->execute(['username' => strtoupper($username)]);
            if ($stmt->fetchColumn()) {
                $error = 'Ce nom d\'utilisateur existe déjà.';
            } else {
                // Vérification si l'email existe déjà
                $stmt = $pdo->prepare("SELECT 1 FROM account WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetchColumn()) {
                    $error = 'Cette adresse e-mail est déjà utilisée.';
                } else {
                    // Création du compte
                    $account = createCloneAccount($pdo, $username, $email);
                    $success = true;
                    $username = $email = '';
                }
            }
        } catch (PDOException $e) {
            $error = 'Erreur de connexion à la base de données: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte FONCTIONNEL - The Kingdom of Sylvania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        h1 {
            color: #343a40;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Création de compte FONCTIONNEL - The Kingdom of Sylvania</h1>
        <div class="alert alert-info">
            <strong>IMPORTANT :</strong> Ce script crée un compte fonctionnel avec le mot de passe <code>arkineos82</code>.
            <br>Utilisez ce mot de passe pour vous connecter en jeu, quel que soit le nom d'utilisateur choisi.
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <h4>Compte créé avec succès !</h4>
                <p><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars(strtoupper($account['username'])); ?></p>
                <p><strong>Mot de passe :</strong> arkineos82</p>
                <p>Vous pouvez maintenant vous connecter au jeu avec ces identifiants.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <div class="form-text">Entre 3 et 20 caractères, lettres et chiffres uniquement.</div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <div class="form-text">Votre adresse e-mail sera utilisée pour récupérer votre compte si nécessaire.</div>
            </div>
            
            <div class="mb-3">
                <div class="alert alert-info">
                    <p><strong>Note importante :</strong> Pour des raisons techniques, tous les comptes créés avec ce formulaire utilisent le même mot de passe : <code>arkineos82</code></p>
                    <p>Vous pourrez changer votre mot de passe ultérieurement via le site web ou en contactant un administrateur.</p>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Créer un compte</button>
        </form>
        
        <div class="mt-4">
            <h4>Comment se connecter au serveur ?</h4>
            <ol>
                <li>Téléchargez et installez World of Warcraft 3.3.5a (Wrath of the Lich King)</li>
                <li>Ouvrez le fichier realmlist.wtf dans le dossier Data</li>
                <li>Remplacez le contenu par : <code>set realmlist sylvania.servegame.com</code></li>
                <li>Lancez le jeu et connectez-vous avec vos identifiants</li>
            </ol>
        </div>
    </div>
</body>
</html>
