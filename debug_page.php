<?php
// Script de débogage pour afficher le code HTML de la page d'accueil
$url = "http://localhost";
$html = file_get_contents($url);
echo "<pre>" . htmlspecialchars($html) . "</pre>";
?>
