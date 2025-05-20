<?php
/**
 * The Kingdom of Sylvania - Page d'inscription
 * Basé sur AzerothCore-RegistrationWeb
 */

// Configuration de la journalisation
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/inscription.log');

// Créer le fichier de log s'il n'existe pas
if (!file_exists('/tmp/inscription.log')) {
    touch('/tmp/inscription.log');
    chmod('/tmp/inscription.log', 0666);
}

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Configuration reCAPTCHA v2
$recaptcha_site_key = "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI";
$recaptcha_secret_key = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";

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
    'recaptcha_error' => 'Veuillez valider le reCAPTCHA.',
    'success' => 'Compte créé avec succès ! Vous pouvez maintenant vous connecter au jeu.'
];

// Initialisation des variables
$username = $email = $password = $confirm_password = '';
$error = '';
$success = false;
$success_message = '';

// Fonction pour vérifier le reCAPTCHA
function verifyRecaptcha($recaptcha_response, $secret_key) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result)->success;
}

// Fonction pour générer les clés SRP6
function calculateSRP6Verifier($username, $password, &$salt, &$verifier) {
    // Génération du salt (32 bytes)
    $salt = random_bytes(32);
    
    // Conversion du nom d'utilisateur en majuscules
    $username = strtoupper($username);
    
    // Calcul de la clé x (première étape SRP6)
    $h1 = sha1($username . ':' . strtoupper($password), true);
    $x = gmp_import(sha1($salt . $h1, true), 1, GMP_LSW_FIRST);
    
    // Constantes SRP6 utilisées par AzerothCore
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    
    // Calcul du vérificateur v = g^x % N
    $verifier = gmp_powm($g, $x, $N);
    
    // Conversion en format binaire (32 octets exactement)
    $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);
    
    // Assurez-vous que le verifier fait exactement 32 octets
    $verifier = str_pad($verifier, 32, "\0", STR_PAD_LEFT);
    
    // Vérification des tailles
    if (strlen($salt) !== 32 || strlen($verifier) !== 32) {
        error_log("ERREUR: Taille incorrecte (salt: " . strlen($salt) . ", verifier: " . strlen($verifier) . ")");
        return false;
    }
    
    return true;
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
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
    } elseif (empty($recaptcha_response)) {
        $error = $error_messages['recaptcha_error'];
    } elseif (!verifyRecaptcha($recaptcha_response, $recaptcha_secret_key)) {
        $error = $error_messages['recaptcha_error'];
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
                    // Génération des clés SRP6
                    $salt = '';
                    $verifier = '';
                    
                    // Génération du salt (32 bytes)
                    $salt = random_bytes(32);
                    
                    if (!calculateSRP6Verifier($username, $password, $salt, $verifier)) {
                        throw new Exception("Erreur lors de la génération des clés SRP6");
                    }
                    
                    // Insertion du compte
                    $stmt = $pdo->prepare("INSERT INTO account (username, salt, verifier, email, reg_mail, joindate, expansion) VALUES (?, ?, ?, ?, ?, NOW(), 2)");
                    $stmt->bindValue(1, strtoupper($username), PDO::PARAM_STR);
                    $stmt->bindValue(2, $salt, PDO::PARAM_LOB);
                    $stmt->bindValue(3, $verifier, PDO::PARAM_LOB);
                    $stmt->bindValue(4, $email, PDO::PARAM_STR);
                    $stmt->bindValue(5, $email, PDO::PARAM_STR);
                    
                    if ($stmt->execute()) {
                        $success = true;
                        $success_message = "Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.";
                    } else {
                        throw new Exception("Erreur lors de l'insertion du compte");
                    }
                }
            }
        } catch (PDOException $e) {
            $error = $error_messages['db_error'] . ' ' . $e->getMessage();
        } catch (Exception $e) {
            $error = "Erreur lors de la création du compte: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Kingdom of Sylvania - Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --background: #121212;
            --card-bg: #1e1e1e;
            --text: #ffffff;
            --border: #4a90e2;
            --blue-border: #4a90e2;
            --hover-border: #357abd;
            --success-bg: #218838;
            --danger-bg: #c82333;
        }

        body {
            background-color: var(--background);
            color: var(--text);
        }

        .card {
            background-color: var(--card-bg);
            border: 2px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: #2a2a2a;
            border-bottom: 2px solid var(--border);
            padding: 1.5rem;
            border-radius: 10px 10px 0 0;
        }

        .g-recaptcha > div {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--blue-border) !important;
        }

        .form-text {
            color: var(--text) !important;
            font-size: 0.875rem;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border: 2px solid var(--primary-color) !important;
            color: white !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
        }

        .btn-primary:hover {
            background-color: var(--hover-border) !important;
            border-color: var(--hover-border) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3) !important;
        }

        .card-header h4 {
            color: white !important;
            margin: 0 !important;
            font-size: 1.5rem !important;
            font-weight: 600 !important;
        }

        .form-control {
            background-color: #2a2a2a !important;
            border: 2px solid var(--border) !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 0.75rem !important;
            transition: all 0.3s ease !important;
        }

        .form-control:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25) !important;
        }

        .alert {
            border-radius: 8px !important;
            border: 2px solid var(--border) !important;
        }

        .alert-success {
            border-color: var(--success-bg) !important;
        }

        .alert-danger {
            border-color: var(--danger-bg) !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Bouton retour -->
                <a href="home.php" class="btn btn-secondary w-100 mb-4">Retour à l'accueil</a>
                <?php if (isset($error) && !empty($error)): ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Erreur !</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Création de compte</h4>
                    </div>
                    <div class="card-body">
                        <!-- Messages -->
                        <?php if ($success): ?>
                            <div class="alert alert-success mb-4">
                                <strong>Succès !</strong> <?= htmlspecialchars($success_message) ?>
                            </div>
                        <?php elseif (!empty($error)): ?>
                            <div class="alert alert-danger mb-4">
                                <strong>Erreur !</strong> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Formulaire d'inscription -->
                        <form action="inscription.php" method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($username); ?>" required>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($error_messages['username_length']); ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email); ?>" required>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($error_messages['email_invalid']); ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($error_messages['password_length']); ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($error_messages['password_mismatch']); ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Créer le compte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
                            <li>Remplacez le contenu par : <code>set realmlist sylvania.servegame.com</code></li>
                            <li>Lancez le jeu et connectez-vous avec vos identifiants</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
