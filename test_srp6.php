<?php
/**
 * Script de test pour l'algorithme SRP6 d'AzerothCore
 */

// Fonction pour générer les clés SRP6 selon la méthode exacte d'AzerothCore
function calculateSRP6Verifier($username, $password, &$salt, &$verifier) {
    // Conversion du nom d'utilisateur en majuscules
    $username = strtoupper($username);
    
    // Génération du sel (salt) - 32 octets aléatoires
    $salt = random_bytes(32);
    
    // Constantes SRP6 utilisées par AzerothCore
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    
    // Calcul de la clé x selon l'algorithme d'AzerothCore
    // 1. Concaténer le sel et le SHA1 de "USERNAME:password" (en majuscules)
    $h1 = sha1(strtoupper($username . ':' . $password));
    // 2. Calculer le SHA1 de la concaténation du sel (en hexa) et du h1 (en hexa)
    $x = sha1(bin2hex($salt) . $h1);
    // 3. Convertir x en nombre GMP (big-endian)
    $x = gmp_init($x, 16);
    
    // Calcul du vérificateur v = g^x % N
    $v = gmp_powm($g, $x, $N);
    
    // Convertir le vérificateur en format binaire (32 octets)
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT); // 64 caractères hex = 32 octets
    $verifier = hex2bin($v_hex);
}

// Fonction pour créer un compte de test directement avec des requêtes SQL
function createTestAccount($db_host, $db_port, $db_username, $db_password, $db_auth, $account_username, $account_password) {
    try {
        // Connexion à la base de données
        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
        $pdo = new PDO($dsn, $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Génération des clés SRP6
        $salt_bin = '';
        $verifier_bin = '';
        calculateSRP6Verifier($account_username, $account_password, $salt_bin, $verifier_bin);
        
        // Suppression du compte s'il existe déjà
        $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
        $stmt->execute(['username' => strtoupper($account_username)]);
        
        // Insertion du compte dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO account 
            (username, salt, verifier, email, reg_mail, joindate) 
            VALUES 
            (:username, :salt, :verifier, :email, :reg_mail, NOW())
        ");
        
        $email = "test@example.com";
        $username_upper = strtoupper($account_username);
        
        $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
        $stmt->bindParam(':salt', $salt_bin, PDO::PARAM_LOB);
        $stmt->bindParam(':verifier', $verifier_bin, PDO::PARAM_LOB);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Récupération du compte créé
        $stmt = $pdo->prepare("SELECT id, username, HEX(salt) as salt_hex, HEX(verifier) as verifier_hex FROM account WHERE username = :username");
        $stmt->execute(['username' => $username_upper]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'message' => "Compte créé avec succès",
            'account' => $account
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Erreur: " . $e->getMessage()
        ];
    }
}

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Création d'un compte de test
$test_username = "testaccount";
$test_password = "password123";

$result = createTestAccount($db_host, $db_port, $db_username, $db_password, $db_auth, $test_username, $test_password);

// Affichage des résultats
echo "<h1>Test de l'algorithme SRP6 pour AzerothCore</h1>";

if ($result['success']) {
    echo "<p style='color: green;'>✓ " . $result['message'] . "</p>";
    echo "<h2>Détails du compte</h2>";
    echo "<pre>";
    print_r($result['account']);
    echo "</pre>";
    
    echo "<p>Pour tester la connexion, utilisez les identifiants suivants dans le jeu :</p>";
    echo "<ul>";
    echo "<li><strong>Nom d'utilisateur:</strong> " . $test_username . "</li>";
    echo "<li><strong>Mot de passe:</strong> " . $test_password . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ " . $result['message'] . "</p>";
}

// Créer également le compte pandorix82 pour tester
$pandorix_username = "pandorix82";
$pandorix_password = "pandorix82"; // Remplacez par le vrai mot de passe si connu

$result2 = createTestAccount($db_host, $db_port, $db_username, $db_password, $db_auth, $pandorix_username, $pandorix_password);

echo "<h2>Création du compte pandorix82</h2>";
if ($result2['success']) {
    echo "<p style='color: green;'>✓ " . $result2['message'] . "</p>";
    echo "<h2>Détails du compte</h2>";
    echo "<pre>";
    print_r($result2['account']);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ " . $result2['message'] . "</p>";
}
?>
