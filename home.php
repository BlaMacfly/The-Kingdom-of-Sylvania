<?php
// Désactiver l'authentification forcée
require_once('disable_auth.php');

// Configuration du site
$site_title = "The Kingdom of Sylvania";
$site_description = "Serveur WoW 3.3.5a Wrath of the Lich King en français";
$discord_link = "https://discord.gg/znmcNmXbQw";

// Vérification de l'état du serveur
function checkServerStatus($host, $port) {
    $connection = @fsockopen($host, $port, $errno, $errstr, 1);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

$auth_status = checkServerStatus('sylvania.servegame.com', 3724);
$world_status = checkServerStatus('sylvania.servegame.com', 8085);

// Déterminer la page active
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

// Vérifier si l'utilisateur est authentifié
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isAdmin = isset($_SESSION['account_role']) && in_array($_SESSION['account_role'], ['moderator', 'gm', 'admin']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?> - <?php echo ucfirst($page); ?></title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_home.css">
    <link rel="stylesheet" href="css/server_control.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/logo_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/golden_text.css">
    <link rel="stylesheet" href="css/enhanced_layout.css">
    <link rel="stylesheet" href="css/modern_stats.css?v=<?php echo time(); ?>">
    <style>
        .server-status-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .server-status-card {
            display: flex;
            align-items: center;
            background-color: rgba(26, 31, 42, 0.8);
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            min-width: 250px;
            transition: transform 0.3s ease;
        }
        
        .server-status-card:hover {
            transform: translateY(-5px);
        }
        
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f44336; /* Rouge par défaut (offline) */
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        
        .status-icon.online {
            background-color: #4CAF50; /* Vert quand online */
        }
        
        .status-info {
            flex-grow: 1;
        }
        
        .status-name {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 5px;
        }
        
        .status-value {
            font-size: 16px;
            font-weight: bold;
            color: #f44336; /* Rouge par défaut (offline) */
        }
        
        .status-value.online {
            color: #4CAF50; /* Vert quand online */
        }
        
        /* Style pour les indicateurs de statut améliorés */
        .service-status {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }
        
        .service-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 20px;
            transition: transform 0.3s ease;
        }
        
        .service-item:hover {
            transform: scale(1.1);
        }
        
        .status-dot.enhanced {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #f44336; /* Rouge par défaut */
            margin-bottom: 10px;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
            transition: all 0.3s ease;
        }
        
        .status-dot.active {
            background-color: #4CAF50; /* Vert quand actif */
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        
        .service-name.enhanced {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }
    </style>
    <script src="js/simple_control.js?v=<?php echo time(); ?>"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="home.php" class="logo"><img src="images/sylvania_logo.png" alt="The Kingdom of Sylvania" style="max-height: 180px; margin-top: -40px; margin-bottom: -40px;"></a>
                <nav class="nav">
                    <a href="home.php" class="nav-item <?php echo $page === 'accueil' ? 'active' : ''; ?>">Accueil</a>
                    <a href="home.php?page=inscription" class="nav-item <?php echo $page === 'inscription' ? 'active' : ''; ?>">Inscription</a>
                    <a href="index.php" class="nav-item <?php echo $page === 'carte' ? 'active' : ''; ?>">Carte des joueurs</a>
                    <a href="server_auth_simple.php" class="nav-item <?php echo $page === 'controle' ? 'active' : ''; ?>">Contrôle Serveur</a>
                    <a href="https://legacy-wow.com/wotlk-addons/" target="_blank" class="nav-item">Addons</a>
                    <a href="modules.php" class="nav-item <?php echo $page === 'modules' ? 'active' : ''; ?>">Modules</a>
                    <a href="<?php echo $discord_link; ?>" target="_blank" class="nav-item">Discord</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if ($page === 'accueil'): ?>
            <!-- Indicateur de statut du serveur simplifié et centré -->
            <div style="display: flex; justify-content: center; width: 100%;">
                <div class="server-status-container" style="width: 350px;">
                    <div class="server-status-indicator" style="display: flex; justify-content: center; align-items: center;">
                        <div class="server-status-label">Statut du serveur:</div>
                        <div id="server-status" class="server-status-message">Vérification...</div>
                    </div>
                </div>
            </div>

            <div class="welcome-section">
                <div class="card main-card">
                    <div class="card-header">
                        <h2><i class="fas fa-crown"></i> Bienvenue sur <?php echo $site_title; ?></h2>
                    </div>
                    <div class="card-body">
                        <p class="lead"><?php echo $site_description; ?></p>
                        <p>Bienvenue dans <strong>The Kingdom of Sylvania</strong>, un serveur WoW 3.3.5a offrant une expérience de jeu authentique et unique :</p>
                        <div class="features-list">
                            <ul style="list-style-type: none; padding-left: 0; margin-top: 15px;">
                                <li><i class="fas fa-check" style="color: #EABA28;"></i> <strong>Taux d'XP x1</strong> - Progression fidèle à l'expérience vanilla</li>
                                <li><i class="fas fa-check" style="color: #EABA28;"></i> <strong>Toutes Races, Toutes Classes</strong> - Créez des combinaisons uniques et originales</li>
                                <li><i class="fas fa-check" style="color: #EABA28;"></i> <strong>Communauté francophone</strong> - Ambiance conviviale et entraide</li>
                                <li><i class="fas fa-check" style="color: #EABA28;"></i> <strong>Serveur stable et optimisé</strong> - Basé sur AzerothCore pour une performance maximale</li>
                                <li><i class="fas fa-check" style="color: #EABA28;"></i> <strong>Contenu fidèle</strong> - Donjons, raids et quêtes comme à l'origine</li>
                                <li><i class="fas fa-power-off" style="color: #EABA28;"></i> <strong>Serveur à démarrage manuel</strong> - <em>On-demand server</em> qui ne démarre que lorsqu'un joueur en fait la demande</li>
                            </ul>
                        </div>
                    
                    <div class="alert alert-warning" style="background-color: rgba(26, 31, 42, 0.9); border-color: #EABA28; color: #EABA28; text-align: center;">
                        <h4><i class="fas fa-star"></i> Nouvelle fonctionnalité : Toutes Races, Toutes Classes !</h4>
                        <p>Sur The Kingdom of Sylvania, vous pouvez jouer n'importe quelle race avec n'importe quelle classe ! Créez des combinaisons uniques comme un Gnome Druide ou un Tauren Mage.</p>
                        <a href="arac.php" class="btn" style="background-color: #EABA28; color: #0f1218; font-weight: bold;">En savoir plus</a>
                    </div>
                    
                    <div class="alert alert-warning" style="background-color: rgba(26, 31, 42, 0.9); border-color: #EABA28; color: #EABA28; text-align: center; margin-top: 15px;">
                        <h4><i class="fas fa-robot"></i> Nouvelle Fonctionnalité : AIPlayerBots !</h4>
                        <p>Découvrez un monde plus vivant avec des bots IA qui se comportent comme de vrais joueurs ! Explorez, combattez et formez des groupes avec des bots intelligents qui rendent l'expérience plus immersive.</p>
                        <p><small>Les AIPlayerBots sont des personnages contrôlés par l'IA qui peuplent le monde, participent aux donjons et interagissent avec vous.</small></p>
                        <a href="modules.php" class="btn" style="background-color: #EABA28; color: #0f1218; font-weight: bold;">En savoir plus</a>
                    </div>
                    
                    <div class="server-stats mt-4 mb-4">
                        <div class="row justify-content-center">
                            <!-- Nouvelle carte Comptes -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="modern-stat-card">
                                    <div class="icon-container">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="modern-icon">
                                            <path fill="#0d6efd" d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM504 312V248H440c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V136c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H552v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                                        </svg>
                                    </div>
                                    <div class="stat-dots">...</div>
                                    <div class="stat-label">Comptes</div>
                                    <div class="stat-value" id="accounts-count">...</div>
                                </div>
                            </div>
                            
                            <!-- Nouvelle carte Personnages -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="modern-stat-card">
                                    <div class="icon-container">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="modern-icon">
                                            <path fill="#0d6efd" d="M192 64C86 64 0 150 0 256S86 448 192 448H448c106 0 192-86 192-192s-86-192-192-192H192zM496 168a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM392 304a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM168 200c0-13.3 10.7-24 24-24s24 10.7 24 24v32h32c13.3 0 24 10.7 24 24s-10.7 24-24 24H216v32c0 13.3-10.7 24-24 24s-24-10.7-24-24V280H136c-13.3 0-24-10.7-24-24s10.7-24 24-24h32V200z"/>
                                        </svg>
                                    </div>
                                    <div class="stat-dots">...</div>
                                    <div class="stat-label">Personnages</div>
                                    <div class="stat-value" id="characters-count">...</div>
                                </div>
                            </div>
                            
                            <!-- Nouvelle carte Joueurs en ligne -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="modern-stat-card">
                                    <div class="icon-container">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="modern-icon">
                                            <path fill="#0d6efd" d="M144 160A80 80 0 1 0 144 0a80 80 0 1 0 0 160zm368 0A80 80 0 1 0 512 0a80 80 0 1 0 0 160zM0 298.7C0 310.4 9.6 320 21.3 320H234.7c.2 0 .4 0 .7 0c-26.6-23.5-43.3-57.8-43.3-96c0-7.6 .7-15 1.9-22.3c-13.6-6.3-28.7-9.7-44.6-9.7H106.7C47.8 192 0 239.8 0 298.7zM320 320c24 0 45.9-8.8 62.7-23.3c2.5-3.7 5.2-7.3 8-10.7c2.7-3.3 5.7-6.1 9-8.3C410 262.3 416 243.9 416 224c0-53-43-96-96-96s-96 43-96 96s43 96 96 96zm65.4 60.2c-10.3-5.9-18.1-16.2-20.8-28.2H261.3C187.7 352 128 411.7 128 485.3c0 14.7 11.9 26.7 26.7 26.7H455.2c-2.1-5.2-3.2-10.9-3.2-16.4v-3c-1.3-.7-2.7-1.5-4-2.3l-2.6 1.5c-16.8 9.7-40.5 8-54.7-9.7c-4.5-5.6-8.6-11.5-12.4-17.6l-.1-.2c-9.2-15.5-21.7-28.1-37.3-37.1l-2.4-1.4zM407 480.7l14.9 7.4c12.2 6.1 27.1 .6 33.2-11.6l1.5-3c6.1-12.2 .6-27.1-11.6-33.2l-14.9-7.4c-17.1-8.5-37.3-8.5-54.4 0l-14.9 7.4c-12.2 6.1-17.7 21-11.6 33.2l1.5 3c6.1 12.2 21 17.7 33.2 11.6l14.9-7.4c2.7-1.4 5.8-1.4 8.5 0z"/>
                                        </svg>
                                    </div>
                                    <div class="stat-dots">...</div>
                                    <div class="stat-label">Joueurs en ligne</div>
                                    <div class="stat-value" id="online-players">...</div>
                                </div>
                            </div>
                            
                            <!-- Nouvelle carte Répartition -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="modern-stat-card">
                                    <div class="stat-dots">...</div>
                                    <div class="stat-label">Répartition des factions</div>
                                    <div class="faction-container">
                                        <div class="faction-distribution">
                                            <div class="alliance">
                                                <img src="https://wow.zamimg.com/images/wow/icons/large/inv_misc_tournaments_banner_human.jpg" alt="Alliance" class="faction-icon alliance-icon">
                                                <span id="alliance-percent">...</span>
                                            </div>
                                            <div class="horde">
                                                <img src="https://wow.zamimg.com/images/wow/icons/large/inv_misc_tournaments_banner_orc.jpg" alt="Horde" class="faction-icon horde-icon">
                                                <span id="horde-percent">...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h2>Commencer à jouer</h2>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Créez un compte sur la page d'inscription</li>
                                    <li>Téléchargez le client WoW 3.3.5a (Wrath of the Lich King)</li>
                                    <li>Modifiez le fichier realmlist.wtf dans le dossier Data avec : <code>set realmlist sylvania.servegame.com</code></li>
                                    <li>Lancez le jeu et connectez-vous avec vos identifiants</li>
                                </ol>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="home.php?page=inscription" class="btn">Créer un compte</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h2>Communauté</h2>
                            </div>
                            <div class="card-body">
                                <p>Rejoignez notre communauté sur Discord pour :</p>
                                <ul>
                                    <li>Obtenir de l'aide et du support</li>
                                    <li>Participer aux événements</li>
                                    <li>Trouver des compagnons d'aventure</li>
                                    <li>Rester informé des dernières nouvelles</li>
                                </ul>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="<?php echo $discord_link; ?>" target="_blank" class="btn btn-discord">Rejoindre le Discord</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h2>Carte des joueurs</h2>
                            </div>
                            <div class="card-body">
                                <p>Découvrez où se trouvent les autres joueurs en temps réel sur notre carte interactive.</p>
                                <p>Vous pouvez voir la position de tous les joueurs connectés, leur niveau, leur classe et leur faction.</p>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="index.php" class="btn">Voir la carte</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'inscription'): ?>
            <div class="card">
                <div class="card-header">
                    <h2>Créer un compte</h2>
                </div>
                <div class="card-body">
                    <p>Remplissez le formulaire ci-dessous pour créer votre compte sur <?php echo $site_title; ?>.</p>
                    <?php include('inscription.php'); ?>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_title; ?> - Tous droits réservés</p>
            <div class="footer-links">
                <a href="#">Conditions d'utilisation</a>
                <a href="#">Politique de confidentialité</a>
                <a href="#">Mentions légales</a>
                <a href="<?php echo $discord_link; ?>" target="_blank">Discord</a>
            </div>
        </div>
    </footer>

    <!-- La fenêtre modale d'authentification a été supprimée pour permettre un accès sans authentification -->

    <!-- Overlay de chargement -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
        <div id="loading-message" class="loading-message">Chargement...</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/server_control.js"></script>
    <script src="js/server_status_update.js"></script>
    <script src="progress_popup.js?v=<?php echo time(); ?>"></script>
    <script>
        // Script pour charger les statistiques du serveur
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour charger le statut du serveur
            function loadServerStatus() {
                fetch('get_server_stats.php')
                    .then(response => response.json())
                    .then(data => {
                        const serverStatusElement = document.getElementById('server-status');
                        
                        console.log('Server status data:', data);
                        
                        // Vérifier si les deux serveurs sont en ligne
                        if (data.auth_status === 'online' && data.world_status === 'online') {
                            serverStatusElement.textContent = 'Serveur en ligne';
                            serverStatusElement.className = 'server-status-message online';
                        } else {
                            // Déterminer quel serveur est hors ligne
                            let statusMessage = 'Serveur hors ligne';
                            if (data.auth_status === 'offline' && data.world_status === 'online') {
                                statusMessage = 'Serveur Auth hors ligne';
                            } else if (data.auth_status === 'online' && data.world_status === 'offline') {
                                statusMessage = 'Serveur World hors ligne';
                            }
                            
                            serverStatusElement.textContent = statusMessage;
                            serverStatusElement.className = 'server-status-message offline';
                        }
                        
                        // Mettre à jour les statistiques dans les cartes modernes
                        if (document.getElementById('accounts-count')) {
                            // Remplacer les points de suspension par les valeurs réelles
                            document.getElementById('accounts-count').textContent = data.accounts_count || '0';
                            document.getElementById('characters-count').textContent = data.characters_count || '0';
                            document.getElementById('online-players').textContent = data.online_players || '0';
                            document.getElementById('alliance-percent').textContent = data.alliance_percent + '%';
                            document.getElementById('horde-percent').textContent = data.horde_percent + '%';
                            
                            // Mettre à jour les points de suspension dans les cartes
                            const statDots = document.querySelectorAll('.stat-dots');
                            statDots.forEach(dot => {
                                dot.style.display = 'none'; // Masquer les points de suspension une fois les données chargées
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        // En cas d'erreur, afficher un message d'erreur
                        const serverStatusElement = document.getElementById('server-status');
                        serverStatusElement.textContent = 'Erreur de connexion';
                        serverStatusElement.className = 'server-status-message offline';
                    });
            }
            
            // Charger le statut du serveur au chargement de la page
            loadServerStatus();
            

        });
    </script>
</body>
</html>
