<?php
/**
 * Configuration du site The Kingdom of Sylvania
 */

// Configuration de la base de données
$db_host = "localhost"; // Utilisation de l'adresse locale pour accès à la base de données
$db_port = 3306;
$db_username = "blamacfly"; // Corrigé en minuscules
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
