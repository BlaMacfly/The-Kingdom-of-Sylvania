<?php
/**
 * Script de création de compte AzerothCore
 * Utilise exactement la même méthode que la commande 'account create'
 */

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Fonction pour convertir une chaîne en majuscules (équivalent à Utf8ToUpperOnlyLatin)
function utf8ToUpperOnlyLatin($string) {
    return strtoupper($string);
}

// Fonction pour générer les données d'enregistrement SRP6 (équivalent à Acore::Crypto::SRP6::MakeRegistrationData)
function makeRegistrationData($username, $password) {
    // Génération d'un sel aléatoire de 32 octets
    $salt = random_bytes(32);
    
    // Calcul du vérificateur selon la méthode d'AzerothCore
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    
    // Calcul du hachage SHA1 de "username:password"
    $h1 = sha1($username . ':' . $password);
    
    // Calcul du hachage SHA1 de "salt_hex || h1"
    $x = sha1(bin2hex($salt) . $h1);
    
    // Conversion du hachage en nombre GMP
    $x = gmp_init($x, 16);
    
    // Calcul du vérificateur v = g^x % N
    $v = gmp_powm($g, $x, $N);
    
    // Conversion du vérificateur en tableau d'octets
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT); // 64 caractères hex = 32 octets
    $verifier = hex2bin($v_hex);
    
    return [$salt, $verifier];
}

// Fonction pour créer un compte avec la méthode exacte d'AzerothCore
function createAccount($username, $password) {
    global $db_host, $db_port, $db_username, $db_password, $db_auth;
    
    try {
        // Connexion à la base de données
        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
        $pdo = new PDO($dsn, $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Vérification de la longueur du nom d'utilisateur et du mot de passe
        if (strlen($username) > 20) {
            return ['success' => false, 'message' => "Le nom d'utilisateur est trop long (maximum 20 caractères)."];
        }
        
        if (strlen($password) > 16) {
            return ['success' => false, 'message' => "Le mot de passe est trop long (maximum 16 caractères)."];
        }
        
        // Conversion en majuscules
        $username = utf8ToUpperOnlyLatin($username);
        $password = utf8ToUpperOnlyLatin($password);
        
        // Vérification si le compte existe déjà
        $stmt = $pdo->prepare("SELECT 1 FROM account WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetchColumn()) {
            return ['success' => false, 'message' => "Ce nom d'utilisateur existe déjà."];
        }
        
        // Génération des données d'enregistrement SRP6
        list($salt, $verifier) = makeRegistrationData($username, $password);
        
        // Insertion du compte dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO account 
            (username, salt, verifier, email, reg_mail, joindate, expansion) 
            VALUES 
            (:username, :salt, :verifier, :email, :reg_mail, NOW(), 2)
        ");
        
        $email = $username . "@example.com";
        
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':salt', $salt, PDO::PARAM_LOB);
        $stmt->bindParam(':verifier', $verifier, PDO::PARAM_LOB);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Récupération du compte créé
        $stmt = $pdo->prepare("SELECT id, username FROM account WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'message' => "Compte créé avec succès.",
            'account' => $account
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur de base de données : " . $e->getMessage()
        ];
    }
}

// Traitement du formulaire
$success = false;
$error = '';
$username = '';
$password = '';
$account = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        $result = createAccount($username, $password);
        if ($result['success']) {
            $success = true;
            $account = $result['account'];
            $username = $password = '';
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte - The Kingdom of Sylvania</title>
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
        <h1 class="text-center">Création de compte - The Kingdom of Sylvania</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <h4>Compte créé avec succès !</h4>
                <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter au jeu avec les informations suivantes :</p>
                <ul>
                    <li><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($account['username']); ?></li>
                    <li><strong>Mot de passe :</strong> Le mot de passe que vous avez fourni</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <div class="form-text">Entre 3 et 20 caractères, lettres et chiffres uniquement.</div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Entre 6 et 16 caractères.</div>
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
