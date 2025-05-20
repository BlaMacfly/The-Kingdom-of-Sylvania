<?php
// Fonction pour générer les clés SRP6 comme dans AzerothCore
function calculateSRP6Verifier($username, $password, &$salt_bin, &$verifier_bin) {
    // Génération du sel (salt)
    $salt_bin = random_bytes(32);
    
    // Conversion du nom d'utilisateur en majuscules (requis par AzerothCore)
    $username = strtoupper($username);
    
    // Calcul de la clé x (première étape SRP6)
    // IMPORTANT: Le mot de passe doit également être en majuscules pour AzerothCore
    $h1 = sha1($username . ':' . strtoupper($password), true);
    $x = gmp_import(sha1($salt_bin . $h1, true), 1, GMP_LSW_FIRST);
    
    // Constantes SRP6 utilisées par AzerothCore
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    
    // Calcul du vérificateur v = g^x % N
    $verifier_bin = gmp_powm($g, $x, $N);
    
    // Conversion en format binaire (32 octets exactement)
    $verifier_bin = gmp_export($verifier_bin, 1, GMP_LSW_FIRST);
    
    // Assurez-vous que le verifier fait exactement 32 octets
    $verifier_bin = str_pad($verifier_bin, 32, "\0", STR_PAD_LEFT);
    
    // Vérification de la taille des données
    if (strlen($salt_bin) !== 32 || strlen($verifier_bin) !== 32) {
        error_log("ERREUR: Taille incorrecte (salt: " . strlen($salt_bin) . ", verifier: " . strlen($verifier_bin) . ")");
        return false;
    }
    
    // Logs pour débogage
    error_log("Username: " . $username);
    error_log("Salt: " . bin2hex($salt_bin));
    error_log("Verifier: " . bin2hex($verifier_bin));
    
    return true;
}
?>
