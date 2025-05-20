<?php
/**
 * Script pour créer un compte en clonant les valeurs d'arkineos82
 */

// Configuration de la base de données
$db_host = "sylvania.servegame.com";
$db_port = 3306;
$db_username = "blamacfly";
$db_password = "ferwyn8289";
$db_auth = "acore_auth";

// Nom d'utilisateur pour le nouveau compte
$new_username = "PANDORIX82";
$new_email = "pandorix82@example.com";

try {
    // Connexion à la base de données
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_auth;charset=utf8";
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Supprimer le compte s'il existe déjà
    $stmt = $pdo->prepare("DELETE FROM account WHERE username = :username");
    $stmt->execute(['username' => $new_username]);
    
    // Insérer le nouveau compte avec les mêmes valeurs que arkineos82
    $stmt = $pdo->prepare("
        INSERT INTO account 
        (username, salt, verifier, email, reg_mail, joindate) 
        VALUES 
        (:username, UNHEX('6B404F56DCD0F5440D590DDCDC9C12C3389501E7A12841B6B557D4AEDD8BCF17'), 
         UNHEX('BB1C8704E782F2441C2BE1D2154A5153D79954AB4F3893A83C0E2E0E7456C288'), 
         :email, :email, NOW())
    ");
    
    $stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
    $stmt->execute();
    
    echo "Compte $new_username créé avec succès.<br>";
    echo "Utilisez le mot de passe : arkineos82<br>";
    echo "Ce compte a exactement les mêmes valeurs de sel et de vérificateur que le compte arkineos82.";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
