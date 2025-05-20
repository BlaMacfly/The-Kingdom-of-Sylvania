<?php
/**
 * Script de création de compte de test pour AzerothCore
 * Ce script crée un compte avec les mêmes valeurs de sel et de vérificateur que arkineos82
 */

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Nom d'utilisateur et mot de passe pour le compte de test
$test_username = "TEST_ACCOUNT";
$test_email = "test@example.com";

try {
    // Connexion à la base de données
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Supprimer le compte de test s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => $test_username]);
    
    // Insérer le compte de test avec les mêmes valeurs que arkineos82
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, UNHEX('6B404F56DCD0F5440D590DDCDC9C12C3389501E7A12841B6B557D4AEDD8BCF17'), 
         UNHEX('BB1C8704E782F2441C2BE1D2154A5153D79954AB4F3893A83C0E2E0E7456C288'), 
         :email, :reg_mail, NOW())
    ");
    
    $stmt->bindParam(':username', $test_username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $test_email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $test_email, PDO::PARAM_STR);
    $stmt->execute();
    
    echo "<h1>Compte de test créé avec succès</h1>";
    echo "<p>Un compte de test a été créé avec les mêmes valeurs de sel et de vérificateur que arkineos82.</p>";
    echo "<p><strong>Nom d'utilisateur:</strong> TEST_ACCOUNT</p>";
    echo "<p><strong>Mot de passe:</strong> arkineos82</p>";
    echo "<p>Essayez de vous connecter au jeu avec ces identifiants.</p>";
    
    // Créer un autre compte de test avec la méthode utilisée par AzerothCore
    // Récupérer le mot de passe de arkineos82 (qui est probablement "arkineos82")
    $password = "arkineos82";
    $username = "TEST_ACCOUNT2";
    
    // Supprimer le compte de test s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => $username]);
    
    // Générer un nouveau sel
    $salt = random_bytes(32);
    
    // Calculer le vérificateur avec la méthode d'AzerothCore
    $username_upper = strtoupper($username);
    $h1 = sha1($username_upper . ':' . $password);
    $salt_hex = bin2hex($salt);
    $x = sha1($salt_hex . $h1);
    $x_gmp = gmp_init($x, 16);
    $g = 7;
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    $v = gmp_powm($g, $x_gmp, $N);
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT);
    $verifier = hex2bin($v_hex);
    
    // Insérer le compte de test
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, :salt, :verifier, :email, :reg_mail, NOW())
    ");
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':salt', $salt, PDO::PARAM_LOB);
    $stmt->bindParam(':verifier', $verifier, PDO::PARAM_LOB);
    $stmt->bindParam(':email', $test_email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $test_email, PDO::PARAM_STR);
    $stmt->execute();
    
    echo "<h1>Deuxième compte de test créé avec succès</h1>";
    echo "<p>Un deuxième compte de test a été créé avec la méthode d'AzerothCore.</p>";
    echo "<p><strong>Nom d'utilisateur:</strong> TEST_ACCOUNT2</p>";
    echo "<p><strong>Mot de passe:</strong> arkineos82</p>";
    echo "<p>Essayez de vous connecter au jeu avec ces identifiants.</p>";
    
    // Créer un troisième compte de test avec une autre méthode
    $username = "TEST_ACCOUNT3";
    $password = "arkineos82";
    
    // Supprimer le compte de test s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => $username]);
    
    // Générer un nouveau sel
    $salt = random_bytes(32);
    
    // Calculer le vérificateur avec une autre méthode
    $username_upper = strtoupper($username);
    $h1 = sha1(strtoupper($username_upper . ':' . $password));
    $salt_hex = bin2hex($salt);
    $x = sha1($salt_hex . $h1);
    $x_gmp = gmp_init($x, 16);
    $g = 7;
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    $v = gmp_powm($g, $x_gmp, $N);
    $v_hex = gmp_strval($v, 16);
    $v_hex = str_pad($v_hex, 64, '0', STR_PAD_LEFT);
    $verifier = hex2bin($v_hex);
    
    // Insérer le compte de test
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, :salt, :verifier, :email, :reg_mail, NOW())
    ");
    
    $stmt->bindParam(':username', $username_upper, PDO::PARAM_STR);
    $stmt->bindParam(':salt', $salt, PDO::PARAM_LOB);
    $stmt->bindParam(':verifier', $verifier, PDO::PARAM_LOB);
    $stmt->bindParam(':email', $test_email, PDO::PARAM_STR);
    $stmt->bindParam(':reg_mail', $test_email, PDO::PARAM_STR);
    $stmt->execute();
    
    echo "<h1>Troisième compte de test créé avec succès</h1>";
    echo "<p>Un troisième compte de test a été créé avec une méthode alternative.</p>";
    echo "<p><strong>Nom d'utilisateur:</strong> TEST_ACCOUNT3</p>";
    echo "<p><strong>Mot de passe:</strong> arkineos82</p>";
    echo "<p>Essayez de vous connecter au jeu avec ces identifiants.</p>";
    
} catch (PDOException $e) {
    echo "<h1>Erreur</h1>";
    echo "<p>Une erreur s'est produite : " . $e->getMessage() . "</p>";
}
?>
