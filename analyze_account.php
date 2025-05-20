<?php
/**
 * Script d'analyse et de création de compte AzerothCore
 * Ce script analyse un compte existant et crée un nouveau compte avec la même méthode
 */

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Fonction pour analyser un compte existant
function analyzeAccount($pdo, $username) {
    $stmt = $pdo->prepare("SELECT id, username, HEX(salt) as salt_hex, HEX(verifier) as verifier_hex FROM account WHERE username = :username");
    $stmt->execute(['username' => strtoupper($username)]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour créer un compte avec la méthode Trinity/AzerothCore
function createTrinityAccount($pdo, $username, $password) {
    // Supprimer le compte s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => strtoupper($username)]);
    
    // Générer un sel aléatoire
    $salt = random_bytes(32);
    
    // Calculer le vérificateur selon la méthode Trinity/AzerothCore
    $g = 7;
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    
    // Nom d'utilisateur en majuscules
    $username_upper = strtoupper($username);
    
    // Calcul du hash h1 = SHA1(USERNAME:password)
    $h1 = sha1($username_upper . ':' . $password);
    
    // Calcul de x = SHA1(salt_hex + h1)
    $salt_hex = bin2hex($salt);
    $x = sha1($salt_hex . $h1);
    
    // Convertir x en nombre GMP
    $x = gmp_init($x, 16);
    
    // Calculer v = g^x % N
    $v = gmp_powm($g, $x, $N);
    
    // Convertir v en format binaire
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT);
    $verifier = hex2bin($v_hex);
    
    // Insérer le compte dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, :salt, :verifier, :email, :reg_mail, NOW())
    ");
    
    $email = $username . "@example.com";
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':salt', $salt, PDO::PARAM_LOB);
    $stmt->bindParam(':verifier', $verifier, PDO::PARAM_LOB);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    // Récupérer le compte créé
    return analyzeAccount($pdo, $username);
}

// Fonction pour créer un compte avec la méthode exacte d'arkineos82
function createExactAccount($pdo, $username, $password) {
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
    $email = $username . "@example.com";
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    // Récupérer le compte créé
    return analyzeAccount($pdo, $username);
}

// Fonction pour créer un compte avec la méthode exacte d'arkineos82 mais avec un mot de passe personnalisé
function createCloneAccount($pdo, $username, $password, $reference_username) {
    // Obtenir les informations du compte de référence
    $reference = analyzeAccount($pdo, $reference_username);
    
    if (!$reference) {
        return false;
    }
    
    // Supprimer le compte s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => strtoupper($username)]);
    
    // Insérer le compte avec les mêmes valeurs que le compte de référence
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, UNHEX(:salt), UNHEX(:verifier), :email, :reg_mail, NOW())
    ");
    
    $username_upper = strtoupper($username);
    $email = $username . "@example.com";
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':salt', $reference['salt_hex'], PDO::PARAM_STR);
    $stmt->bindParam(':verifier', $reference['verifier_hex'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    // Récupérer le compte créé
    return analyzeAccount($pdo, $username);
}

try {
    // Connexion à la base de données
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Analyser le compte arkineos82
    $arkineos = analyzeAccount($pdo, 'arkineos82');
    
    // Créer un nouveau compte pandorix82 avec la méthode Trinity/AzerothCore
    $pandorix_trinity = createTrinityAccount($pdo, 'pandorix82', 'pandorix82');
    
    // Créer un nouveau compte pandorix_exact avec la méthode exacte d'arkineos82
    $pandorix_exact = createExactAccount($pdo, 'pandorix_exact', 'pandorix82');
    
    // Créer un nouveau compte pandorix_clone avec la méthode exacte d'arkineos82
    $pandorix_clone = createCloneAccount($pdo, 'pandorix_clone', 'pandorix82', 'arkineos82');
    
    // Afficher les résultats
    echo "<h1>Analyse des comptes AzerothCore</h1>";
    
    echo "<h2>Compte arkineos82 (référence)</h2>";
    echo "<pre>";
    print_r($arkineos);
    echo "</pre>";
    
    echo "<h2>Compte pandorix82 (méthode Trinity/AzerothCore)</h2>";
    echo "<pre>";
    print_r($pandorix_trinity);
    echo "</pre>";
    
    echo "<h2>Compte pandorix_exact (méthode exacte d'arkineos82)</h2>";
    echo "<pre>";
    print_r($pandorix_exact);
    echo "</pre>";
    
    echo "<h2>Compte pandorix_clone (clone d'arkineos82)</h2>";
    echo "<pre>";
    print_r($pandorix_clone);
    echo "</pre>";
    
    echo "<h2>Instructions de connexion</h2>";
    echo "<p>Essayez de vous connecter avec les comptes suivants :</p>";
    echo "<ul>";
    echo "<li><strong>Nom d'utilisateur:</strong> pandorix82 | <strong>Mot de passe:</strong> pandorix82</li>";
    echo "<li><strong>Nom d'utilisateur:</strong> pandorix_exact | <strong>Mot de passe:</strong> pandorix82</li>";
    echo "<li><strong>Nom d'utilisateur:</strong> pandorix_clone | <strong>Mot de passe:</strong> arkineos82 (même mot de passe que arkineos82)</li>";
    echo "</ul>";
    
    echo "<p>Le compte qui fonctionne nous indiquera quelle méthode est correcte.</p>";
    
} catch (PDOException $e) {
    echo "<h1>Erreur</h1>";
    echo "<p>Une erreur s'est produite : " . $e->getMessage() . "</p>";
}
?>
