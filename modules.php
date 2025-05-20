<?php
// Configuration du site
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?> - Modules du Serveur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style_home.css">
    <link rel="stylesheet" href="css/logo_style.css">
    <link rel="stylesheet" href="css/golden_text.css">
    <link rel="stylesheet" href="css/modules.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="home.php" class="logo"><img src="images/sylvania_logo.png" alt="The Kingdom of Sylvania"></a>
                <nav class="nav">
                    <a href="home.php" class="nav-item">Accueil</a>
                    <a href="home.php?page=inscription" class="nav-item">Inscription</a>
                    <a href="map.php" class="nav-item">Carte des joueurs</a>
                    <a href="home.php?page=classement" class="nav-item">Classement</a>
                    <a href="arac.php" class="nav-item">Races & Classes</a>
                    <a href="modules.php" class="nav-item active">Modules</a>
                    <a href="<?php echo $discord_link; ?>" target="_blank" class="nav-item">Discord</a>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="module-container">
        <h1 class="text-center mb-4">Modules du Serveur The Kingdom of Sylvania</h1>
        <p class="lead text-center mb-5">Découvrez les fonctionnalités spéciales disponibles sur notre serveur pour enrichir votre expérience de jeu.</p>
        
        <!-- Navigation par catégories -->
        <div class="module-categories">
            <button class="category-btn active" data-category="all">Tous les modules</button>
            <button class="category-btn" data-category="gameplay">Gameplay</button>
            <button class="category-btn" data-category="customization">Personnalisation</button>
            <button class="category-btn" data-category="convenience">Confort de jeu</button>
            <button class="category-btn" data-category="social">Social</button>
        </div>
        
        <!-- Section Personnalisation -->
        <div class="module-section" data-category="customization" id="customization">
            <h2 class="section-title"><i class="fas fa-palette"></i> Personnalisation</h2>
            
            <!-- Module ARAC -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/inv_misc_book_11.jpg" alt="ARAC" class="module-icon">
                    <h2 class="module-title">Toutes Races, Toutes Classes (ARAC)</h2>
                </div>
                <div class="module-description">
                    <p>Ce module vous permet de jouer n'importe quelle race avec n'importe quelle classe. Créez des combinaisons uniques comme un Gnome Druide ou un Tauren Mage !</p>
                    <p>Pour utiliser cette fonctionnalité, vous devez installer un patch client. <a href="arac.php" class="golden-text">Consultez la page dédiée</a> pour plus d'informations.</p>
                    
                    <div class="module-commands">
                        <div class="command-title">Instructions d'installation :</div>
                        <ol>
                            <li>Téléchargez le fichier patch-Z.mpq depuis la <a href="arac.php" class="golden-text">page dédiée</a></li>
                            <li>Copiez le fichier patch-Z.mpq dans le dossier Data de votre client WoW (généralement C:\Program Files\World of Warcraft\Data\)</li>
                            <li>Assurez-vous que le fichier est bien nommé "patch-Z.mpq" (respectez la casse)</li>
                            <li>Redémarrez votre client WoW si celui-ci était déjà lancé</li>
                        </ol>
                        <p>Une fois le patch installé, rendez-vous à Dalaran et trouvez le portail vers l'Île de Morza près de l'Archimage Morza (situé près du centre de la ville).</p>
                        <p><strong>Note importante</strong> : Si vous rencontrez des problèmes avec le téléchargement, essayez de faire un clic droit sur le lien et sélectionnez "Enregistrer la cible sous..."</p>
                    </div>
                </div>
            </div>
            <!-- Module Transmogrification -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/inv_enchant_voidsphere.jpg" alt="Transmogrification" class="module-icon">
                    <h2 class="module-title">Transmogrification</h2>
                </div>
                <div class="module-description">
                    <p>La transmogrification vous permet de modifier l'apparence de votre équipement sans en changer les statistiques. Personnalisez votre look tout en conservant vos meilleures pièces d'équipement !</p>
                    <p>Pour utiliser cette fonctionnalité, rendez-vous auprès du PNJ de Transmogrification dans les capitales principales.</p>
                    
                    <div class="module-commands">
                        <div class="command-title">Pour les administrateurs :</div>
                        <code>.npc add 190010</code> - Ajouter un PNJ de Transmogrification
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Gameplay -->
        <div class="module-section" data-category="gameplay" id="gameplay">
            <h2 class="section-title"><i class="fas fa-gamepad"></i> Gameplay</h2>
        
            <!-- Module SoloCraft -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/ability_warrior_challange.jpg" alt="SoloCraft" class="module-icon">
                    <h2 class="module-title">SoloCraft</h2>
                </div>
                <div class="module-description">
                    <p>SoloCraft ajuste automatiquement vos statistiques lorsque vous entrez dans un donjon ou un raid en fonction du nombre de joueurs dans votre groupe. Cela vous permet de relever des défis conçus pour des groupes même si vous jouez seul ou avec un petit groupe d'amis.</p>
                    <p>Cette fonctionnalité est automatiquement activée lorsque vous entrez dans un donjon ou un raid. Aucune action supplémentaire n'est requise de votre part.</p>
                </div>
            </div>
            
            <!-- Module Solo LFG -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/achievement_dungeon_heroic_glory.jpg" alt="Solo LFG" class="module-icon">
                    <h2 class="module-title">Solo LFG (Looking For Group)</h2>
                </div>
                <div class="module-description">
                    <p>Le module Solo LFG vous permet de rejoindre le système de recherche de groupe (LFG) en solo, sans avoir besoin d'un groupe complet. Vous pouvez ainsi profiter du système de matchmaking automatique pour les donjons même si vous jouez seul.</p>
                    <p>Pour utiliser cette fonctionnalité :</p>
                    <ol>
                        <li>Ouvrez l'interface LFG comme d'habitude (touche "I" par défaut)</li>
                        <li>Sélectionnez les donjons qui vous intéressent</li>
                        <li>Cliquez sur "Rejoindre" - le système vous placera dans la file d'attente même si vous êtes seul</li>
                    </ol>
                    <p>Une fois qu'assez de joueurs auront rejoint la file d'attente, vous serez automatiquement placé dans un groupe et téléporté au donjon.</p>
                </div>
            </div>
            
            <!-- Module Autobalance -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/spell_holy_auraoflight.jpg" alt="Autobalance" class="module-icon">
                    <h2 class="module-title">Autobalance</h2>
                </div>
                <div class="module-description">
                    <p>Le module Autobalance ajuste automatiquement la difficulté des donjons et des raids en fonction du nombre de joueurs présents dans votre groupe. Contrairement à SoloCraft qui se concentre sur les joueurs solo, Autobalance est conçu pour équilibrer l'expérience pour tous les groupes, quelle que soit leur taille.</p>
                    <p>Caractéristiques principales :</p>
                    <ul>
                        <li>Ajustement dynamique de la santé et des dégâts des monstres</li>
                        <li>Équilibrage intelligent en fonction du nombre et du niveau des joueurs</li>
                        <li>Configuration spécifique pour les raids avec un nombre minimum de joueurs requis</li>
                        <li>Compatibilité avec tous les donjons et raids du jeu</li>
                    </ul>
                    <p>Cette fonctionnalité est entièrement automatique et s'active dès que vous entrez dans un donjon ou un raid.</p>
                    
                    <div class="module-commands">
                        <div class="command-title">Commandes joueur :</div>
                        <code>.ab info</code> - Affiche les informations sur l'équilibrage actuel
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Confort de jeu -->
        <div class="module-section" data-category="convenience" id="convenience">
            <h2 class="section-title"><i class="fas fa-magic"></i> Confort de jeu</h2>
        
            <!-- Module Premium -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/inv_misc_coin_17.jpg" alt="Premium" class="module-icon">
                    <h2 class="module-title">Fonctionnalités Premium</h2>
                </div>
                <div class="module-description">
                    <p>Le module Premium offre des fonctionnalités pratiques pour les joueurs qui soutiennent le serveur. Ces fonctionnalités incluent :</p>
                    <ul>
                        <li>Banque mobile - Accédez à votre banque n'importe où</li>
                        <li>Hôtel des ventes mobile - Consultez et utilisez l'hôtel des ventes de n'importe où</li>
                        <li>Morphing - Changez temporairement votre apparence</li>
                        <li>Invocation de vendeurs et formateurs</li>
                        <li>Et plus encore !</li>
                    </ul>
                    <p>Pour utiliser ces fonctionnalités, vous devez posséder un objet premium qui vous sera attribué par les administrateurs du serveur.</p>
                    
                    <div class="module-commands">
                        <div class="command-title">Pour les administrateurs :</div>
                        <code>.additem 9017</code> - Ajouter l'objet Premium à un joueur
                    </div>
                </div>
            </div>
            
            <!-- Module Random Enchants -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/inv_enchant_shardglowingpurple.jpg" alt="Random Enchants" class="module-icon">
                    <h2 class="module-title">Enchantements Aléatoires</h2>
                </div>
                <div class="module-description">
                    <p>Ce module ajoute parfois des enchantements aléatoires sur les objets que vous obtenez par butin, récompenses de quêtes ou création d'artisanat. Ces enchantements peuvent rendre vos objets plus puissants et uniques !</p>
                    <p>Cette fonctionnalité est entièrement automatique. Lorsque vous obtenez un nouvel objet, il y a une chance qu'il reçoive un enchantement aléatoire supplémentaire.</p>
                </div>
            </div>
            
            <!-- Module AuctionHouseBot -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/inv_misc_coin_01.jpg" alt="AuctionHouseBot" class="module-icon">
                    <h2 class="module-title">AuctionHouseBot</h2>
                </div>
                <div class="module-description">
                    <p>Le module AuctionHouseBot remplit automatiquement l'hôtel des ventes avec des objets variés pour assurer un marché actif sur le serveur, même en période de faible affluence.</p>
                    <p>Caractéristiques principales :</p>
                    <ul>
                        <li>Remplit l'hôtel des ventes avec des objets de différentes qualités (commun, rare, épique...)</li>
                        <li>Configurable pour différents types d'objets (armes, armures, consommables, matériaux d'artisanat...)</li>
                        <li>Ajuste automatiquement les prix en fonction de l'offre et de la demande</li>
                        <li>Cycle d'objets pour renouveler régulièrement les offres disponibles</li>
                        <li>Maintient un marché actif même avec peu de joueurs</li>
                    </ul>
                    <p>Cette fonctionnalité est entièrement automatique et ne nécessite aucune action de la part des joueurs. Rendez-vous simplement à l'hôtel des ventes de n'importe quelle capitale pour profiter d'un large choix d'objets disponibles à l'achat.</p>
                </div>
            </div>
        </div>
        
        <!-- Section Social -->
        <div class="module-section" data-category="social" id="social">
            <h2 class="section-title"><i class="fas fa-users"></i> Social</h2>
        
            <!-- Module AIPlayerBots -->
            <div class="module-card">
                <div class="module-header">
                    <img src="https://wow.zamimg.com/images/wow/icons/large/ability_hunter_beastcall.jpg" alt="AIPlayerBots" class="module-icon">
                    <h2 class="module-title">AIPlayerBots</h2>
                </div>
                <div class="module-description">
                    <p>AIPlayerBots est un module avancé qui ajoute des personnages contrôlés par l'IA au monde de jeu. Ces bots se comportent comme de véritables joueurs, rendant l'expérience plus immersive même avec peu de joueurs réels en ligne.</p>
                    
                    <p>Caractéristiques principales :</p>
                    <ul>
                        <li>Des bots qui se déplacent, combattent et interagissent comme de vrais joueurs</li>
                        <li>Possibilité de former des groupes avec des bots pour explorer des donjons</li>
                        <li>Les bots répondent aux commandes et peuvent vous suivre ou vous aider</li>
                        <li>Population dynamique qui s'adapte au nombre de joueurs en ligne</li>
                        <li>Différentes classes et races de bots avec des comportements spécifiques</li>
                    </ul>
                    
                    <p>Les AIPlayerBots sont actifs en permanence sur le serveur et ne nécessitent aucune installation spéciale de la part des joueurs.</p>
                    
                    <div class="module-commands">
                        <div class="command-title">Commandes joueur :</div>
                        <code>.bot add</code> - Ajoute un bot à votre groupe<br>
                        <code>.bot remove</code> - Retire un bot de votre groupe<br>
                        <code>.bot follow</code> - Ordonne aux bots de vous suivre<br>
                        <code>.bot stay</code> - Ordonne aux bots de rester sur place<br>
                        <code>.bot attack</code> - Ordonne aux bots d'attaquer votre cible
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="home.php" class="btn btn-secondary me-2">Retour à l'accueil</a>
            <a href="<?php echo $discord_link; ?>" target="_blank" class="btn btn-primary">Rejoindre notre Discord</a>
        </div>
    </div>
    
    <!-- Bouton retour en haut -->
    <a href="#" class="back-to-top" aria-label="Retour en haut de page">
        <i class="fas fa-arrow-up"></i>
    </a>
    
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="js/modules.js"></script>
</body>
</html>
