<?php
/**
 * The Kingdom of Sylvania - Page d'inscription (FIXÉE)
 * Basé sur la méthode exacte utilisée pour créer le compte arkineos82
 */

// Configuration de la base de données
$db_host = "localhost";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Configuration du site
$site_title = "The Kingdom of Sylvania";
$site_description = "Serveur WoW 3.3.5a Wrath of the Lich King en français";
$discord_link = "https://discord.gg/pDKTE7MtGB";

// Messages d'erreur
$error_messages = [
    'empty_fields' => 'Tous les champs sont obligatoires.',
    'username_exists' => 'Ce nom d\'utilisateur existe déjà.',
    'email_exists' => 'Cette adresse e-mail est déjà utilisée.',
    'password_mismatch' => 'Les mots de passe ne correspondent pas.',
    'username_length' => 'Le nom d\'utilisateur doit comporter entre 3 et 20 caractères.',
    'password_length' => 'Le mot de passe doit comporter au moins 6 caractères.',
    'email_invalid' => 'Adresse e-mail invalide.',
    'username_invalid' => 'Le nom d\'utilisateur ne peut contenir que des lettres et des chiffres.',
    'db_error' => 'Erreur de connexion à la base de données.',
    'success' => 'Compte créé avec succès ! Vous pouvez maintenant vous connecter au jeu.'
];

// Initialisation des variables
$username = $email = $password = $confirm_password = '';
$error = '';
$success = false;

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation des champs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = $error_messages['empty_fields'];
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = $error_messages['username_length'];
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error = $error_messages['username_invalid'];
    } elseif (strlen($password) < 6) {
        $error = $error_messages['password_length'];
    } elseif ($password !== $confirm_password) {
        $error = $error_messages['password_mismatch'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $error_messages['email_invalid'];
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
                $error = $error_messages['username_exists'];
            } else {
                // Vérification si l'email existe déjà
                $stmt = $pdo->prepare("SELECT 1 FROM account WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetchColumn()) {
                    $error = $error_messages['email_exists'];
                } else {
                    // Génération des clés SRP6 selon la méthode exacte d'AzerothCore
                    $username_upper = strtoupper($username);
                    
                    // Générer un sel aléatoire
                    $salt = random_bytes(32);
                    $salt_hex = bin2hex($salt);
                    
                    // Calculer le vérificateur selon la méthode exacte d'AzerothCore
                    $g = 7;
                    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
                    
                    // Calcul du hash h1 = SHA1(USERNAME:password)
                    $h1 = sha1($username_upper . ':' . $password);
                    
                    // Calcul de x = SHA1(salt_hex + h1)
                    $x = sha1($salt_hex . $h1);
                    
                    // Convertir x en nombre GMP
                    $x = gmp_init($x, 16);
                    
                    // Calculer v = g^x % N
                    $v = gmp_powm($g, $x, $N);
                    
                    // Convertir v en format binaire
                    $v_hex = gmp_strval($v, 16);
                    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT);
                    $verifier = hex2bin($v_hex);
                    
                    // Insertion du compte dans la base de données
                    $stmt = $pdo->prepare("
                        INSERT INTO account 
                        (username, salt, verifier, email, reg_mail, joindate) 
                        VALUES 
                        (:username, :salt, :verifier, :email, :reg_mail, NOW())
                    ");
                    
                    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
                    $stmt->bindParam(':salt', $salt, PDO::PARAM_LOB);
                    $stmt->bindParam(':verifier', $verifier, PDO::PARAM_LOB);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    // Succès
                    $success = true;
                    $username = $email = $password = $confirm_password = '';
                }
            }
        } catch (PDOException $e) {
            $error = $error_messages['db_error'] . ' ' . $e->getMessage();
        }
    }
}
?>

<!-- Formulaire d'inscription -->
<div class="registration-form">
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $error_messages['success']; ?>
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
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="form-text">Au moins 6 caractères.</div>
        </div>
        
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
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
