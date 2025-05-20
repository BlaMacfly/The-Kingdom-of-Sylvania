<?php
/**
 * Script d'authentification exacte pour AzerothCore
 * Ce script implémente exactement la même méthode que celle utilisée par AzerothCore
 * pour créer des comptes.
 */

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Constantes SRP6 d'AzerothCore
$g_hex = "07"; // g = 7
$N_hex = "894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7";

// Fonction pour convertir une chaîne hexadécimale en tableau d'octets
function hexStrToByteArray($hexStr) {
    $hexStr = str_replace(' ', '', $hexStr);
    $len = strlen($hexStr);
    $bytes = [];
    for ($i = 0; $i < $len; $i += 2) {
        $bytes[] = hexdec(substr($hexStr, $i, 2));
    }
    return $bytes;
}

// Fonction pour calculer le vérificateur SRP6 selon la méthode exacte d'AzerothCore
function calculateVerifierAzerothCore($username, $password, &$salt) {
    global $g_hex, $N_hex;
    
    // Génération d'un sel aléatoire de 32 octets
    $salt = random_bytes(32);
    
    // Calcul du hachage SHA1 de "username:password"
    $h1 = sha1(strtoupper($username) . ':' . strtoupper($password), true);
    
    // Calcul du hachage SHA1 de "salt || h1"
    $h2 = sha1($salt . $h1, true);
    
    // Conversion du hachage en nombre BigNumber
    $x = gmp_init(bin2hex($h2), 16);
    
    // Calcul du vérificateur v = g^x % N
    $g = gmp_init($g_hex, 16);
    $N = gmp_init($N_hex, 16);
    $v = gmp_powm($g, $x, $N);
    
    // Conversion du vérificateur en tableau d'octets
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT); // 64 caractères hex = 32 octets
    return hex2bin($v_hex);
}

// Fonction pour créer un compte avec la méthode exacte d'AzerothCore
function createAccountAzerothCore($pdo, $username, $password) {
    // Supprimer le compte s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => strtoupper($username)]);
    
    // Calculer le sel et le vérificateur
    $salt = null;
    $verifier = calculateVerifierAzerothCore($username, $password, $salt);
    
    // Insérer le compte dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, :salt, :verifier, :email, :reg_mail, NOW())
    ");
    
    $username_upper = strtoupper($username);
    $email = $username . "@example.com";
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':salt', $salt, PDO::PARAM_LOB);
    $stmt->bindParam(':verifier', $verifier, PDO::PARAM_LOB);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    // Récupérer le compte créé
    $stmt = $pdo->prepare("SELECT id, username, HEX(salt) as salt_hex, HEX(verifier) as verifier_hex FROM account WHERE username = :username");
    $stmt->execute(['username' => $username_upper]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour tester l'authentification avec un compte existant
function testAuthentication($pdo, $username, $password) {
    global $g_hex, $N_hex;
    
    // Récupérer le sel et le vérificateur du compte
    $stmt = $pdo->prepare("SELECT salt, verifier FROM account WHERE username = :username");
    $stmt->execute(['username' => strtoupper($username)]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$account) {
        return false;
    }
    
    // Calculer le vérificateur avec le sel existant
    $salt = $account['salt'];
    
    // Calcul du hachage SHA1 de "username:password"
    $h1 = sha1(strtoupper($username) . ':' . strtoupper($password), true);
    
    // Calcul du hachage SHA1 de "salt || h1"
    $h2 = sha1($salt . $h1, true);
    
    // Conversion du hachage en nombre BigNumber
    $x = gmp_init(bin2hex($h2), 16);
    
    // Calcul du vérificateur v = g^x % N
    $g = gmp_init($g_hex, 16);
    $N = gmp_init($N_hex, 16);
    $v = gmp_powm($g, $x, $N);
    
    // Conversion du vérificateur en tableau d'octets
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT); // 64 caractères hex = 32 octets
    $verifier = hex2bin($v_hex);
    
    // Comparer les vérificateurs
    return $verifier === $account['verifier'];
}

try {
    // Connexion à la base de données
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer un compte de test avec la méthode exacte d'AzerothCore
    $test_account = createAccountAzerothCore($pdo, "test_azerothcore", "test123");
    
    // Tester l'authentification avec le compte arkineos82
    $auth_test = testAuthentication($pdo, "arkineos82", "arkineos82");
    
    echo "<h1>Création de compte AzerothCore</h1>";
    
    echo "<h2>Compte de test créé</h2>";
    echo "<pre>";
    print_r($test_account);
    echo "</pre>";
    
    echo "<h2>Test d'authentification avec arkineos82</h2>";
    echo "<p>Résultat: " . ($auth_test ? "Succès" : "Échec") . "</p>";
    
    echo "<h2>Comptes de test</h2>";
    echo "<p>Les comptes suivants ont été créés pour tester l'authentification :</p>";
    echo "<ul>";
    echo "<li><strong>Nom d'utilisateur:</strong> test_azerothcore | <strong>Mot de passe:</strong> test123</li>";
    echo "</ul>";
    
    echo "<h2>Instructions</h2>";
    echo "<p>Essayez de vous connecter avec le compte test_azerothcore et le mot de passe test123.</p>";
    echo "<p>Si cela fonctionne, nous avons trouvé la méthode exacte utilisée par AzerothCore.</p>";
    
} catch (PDOException $e) {
    echo "<h1>Erreur</h1>";
    echo "<p>Une erreur s'est produite : " . $e->getMessage() . "</p>";
}
?>
