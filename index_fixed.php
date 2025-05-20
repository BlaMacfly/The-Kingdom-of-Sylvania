<?php
// Page d'accueil corrigée avec des liens directs vers le nouveau contrôleur de serveur
session_start();

// Vérifier si l'utilisateur est déjà authentifié
$isLoggedIn = isset($_SESSION['account_id']) && isset($_SESSION['account_username']);

// Rediriger vers la page d'authentification si l'utilisateur n'est pas connecté
if (!$isLoggedIn && isset($_GET['control'])) {
    header("Location: server_auth_simple.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Kingdom of Sylvania - Serveur WoW 3.3.5a</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <script src="js/server_status_update.js?v=<?php echo time(); ?>"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo.png" alt="The Kingdom of Sylvania">
        </div>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="arac.php">Races & Classes</a></li>
                <li><a href="modules.php">Modules</a></li>
                <li><a href="rankings.php">Classements</a></li>
                <li><a href="map.php">Carte des joueurs</a></li>
                <?php if ($isLoggedIn && in_array($_SESSION['account_role'], ['admin', 'gm', 'moderator'])): ?>
                <li><a href="server_control_onoff.php" class="admin-link">Contrôle Serveur</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="server-status">
            <div class="status-indicator"></div>
            <div class="status-text">Serveur d'authentification: Vérification...</div>
            <div class="status-indicator"></div>
            <div class="status-text">Serveur de jeu: Vérification...</div>
        </div>
    </header>
    
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Bienvenue sur The Kingdom of Sylvania</h1>
                <p>Un serveur WoW 3.3.5a avec des fonctionnalités uniques</p>
                <div class="cta-buttons">

                    <?php if ($isLoggedIn && in_array($_SESSION['account_role'], ['admin', 'gm', 'moderator'])): ?>
                    <a href="server_control_onoff.php" class="cta-button admin">Contrôle Serveur</a>
                    <?php else: ?>
                    <a href="server_auth_simple.php" class="cta-button">Connexion Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <section class="features">
            <h2>Caractéristiques du serveur</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">🧙‍♂️</div>
                    <h3>Toutes Races, Toutes Classes</h3>
                    <p>Jouez n'importe quelle classe avec n'importe quelle race grâce à notre module ARAC.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚔️</div>
                    <h3>SoloCraft</h3>
                    <p>Affrontez les donjons et raids en solo ou en petit groupe avec des statistiques ajustées.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✨</div>
                    <h3>Transmogrification</h3>
                    <p>Personnalisez l'apparence de votre équipement tout en conservant ses statistiques.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎲</div>
                    <h3>Enchantements aléatoires</h3>
                    <p>Découvrez des objets avec des enchantements aléatoires pour plus de variété.</p>
                </div>
            </div>
        </section>
        
        <section class="discord-section">
            <h2>Rejoignez notre communauté Discord</h2>
            <p>Discutez avec d'autres joueurs, obtenez de l'aide et restez informé des dernières mises à jour.</p>
            <a href="https://discord.gg/pDKTE7MtGB" class="discord-button" target="_blank">Rejoindre le Discord</a>
        </section>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>The Kingdom of Sylvania</h3>
                <p>Un serveur WoW 3.3.5a privé dédié à offrir une expérience de jeu unique et équilibrée.</p>
            </div>
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="arac.php">Races & Classes</a></li>
                    <li><a href="modules.php">Modules</a></li>
                    <li><a href="rankings.php">Classements</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Nous contacter</h3>
                <p>Discord: <a href="https://discord.gg/pDKTE7MtGB" target="_blank">Serveur Discord</a></p>
                <p>Email: contact@sylvania-wow.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 The Kingdom of Sylvania. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
