<?php
// Script de test simple pour vérifier l'accès au serveur web
echo "Le serveur web fonctionne correctement!";
echo "<br>Date et heure: " . date('Y-m-d H:i:s');
echo "<br>Serveur: " . $_SERVER['SERVER_NAME'];
echo "<br>PHP version: " . phpversion();
?>
