<?php
// Configuration du site
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?> - Toutes Races Toutes Classes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_home.css">
    <link rel="stylesheet" href="css/logo_style.css">
    <link rel="stylesheet" href="css/golden_text.css">
    <style>
        .arac-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .race-class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .race-class-item {
            background-color: rgba(26, 31, 42, 0.8);
            border: 1px solid #2c3347;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        
        .race-class-item img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        
        .installation-steps {
            background-color: rgba(26, 31, 42, 0.8);
            border: 1px solid #2c3347;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .installation-steps ol {
            padding-left: 20px;
        }
        
        .installation-steps li {
            margin-bottom: 15px;
        }
        
        .download-button {
            background-color: #EABA28;
            color: #0f1218;
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        
        .download-button:hover {
            background-color: #f0c14b;
            color: #0f1218;
        }
    </style>
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
                    <a href="<?php echo $discord_link; ?>" target="_blank" class="nav-item">Discord</a>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="arac-container">
        <div class="card">
            <div class="card-header">
                <h2>Toutes Races, Toutes Classes (ARAC)</h2>
            </div>
            <div class="card-body">
                <p class="lead">Sur The Kingdom of Sylvania, vous pouvez jouer n'importe quelle race avec n'importe quelle classe !</p>
                
                <p>Vous avez toujours rêvé de jouer un Gnome Druide ? Un Tauren Mage ? Un Mort-vivant Paladin ? Maintenant c'est possible ! Notre serveur utilise la fonctionnalité ARAC (All Races All Classes) qui vous permet de créer des combinaisons race/classe uniques et originales.</p>
                
                <h3>Combinaisons possibles</h3>
                <p>Voici quelques exemples de combinaisons inédites que vous pouvez créer :</p>
                
                <div class="race-class-grid">
                    <div class="race-class-item">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/race_gnome_male.jpg" alt="Gnome">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/class_druid.jpg" alt="Druide">
                        <div>Gnome Druide</div>
                    </div>
                    <div class="race-class-item">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/race_tauren_male.jpg" alt="Tauren">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/class_mage.jpg" alt="Mage">
                        <div>Tauren Mage</div>
                    </div>
                    <div class="race-class-item">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/race_undead_male.jpg" alt="Mort-vivant">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/class_paladin.jpg" alt="Paladin">
                        <div>Mort-vivant Paladin</div>
                    </div>
                    <div class="race-class-item">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/race_bloodelf_female.jpg" alt="Elfe de sang">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/class_shaman.jpg" alt="Chaman">
                        <div>Elfe de sang Chaman</div>
                    </div>
                    <div class="race-class-item">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/race_draenei_male.jpg" alt="Draeneï">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/class_warlock.jpg" alt="Démoniste">
                        <div>Draeneï Démoniste</div>
                    </div>
                    <div class="race-class-item">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/race_orc_female.jpg" alt="Orc">
                        <img src="https://wow.zamimg.com/images/wow/icons/large/class_priest.jpg" alt="Prêtre">
                        <div>Orc Prêtre</div>
                    </div>
                </div>
                
                <div class="installation-steps">
                    <h3>Comment utiliser cette fonctionnalité</h3>
                    <p>Pour pouvoir créer ces combinaisons uniques, vous devez installer un petit patch sur votre client WoW :</p>
                    
                    <ol>
                        <li>Téléchargez le fichier <strong>Patch-A.MPQ</strong> en cliquant sur le bouton ci-dessous.</li>
                        <li>Placez ce fichier dans le dossier <strong>Data</strong> de votre client WoW 3.3.5a (Wrath of the Lich King).<br>
                        <code>C:\Program Files (x86)\World of Warcraft\Data\</code> ou l'emplacement où vous avez installé WoW.</li>
                        <li>Assurez-vous que le fichier est bien nommé <strong>Patch-A.MPQ</strong> (respectez la casse).</li>
                        <li>Lancez le jeu et connectez-vous à notre serveur.</li>
                        <li>Lors de la création de personnage, vous verrez que toutes les classes sont disponibles pour toutes les races !</li>
                    </ol>
                    
                    <div class="text-center">
                        <a href="downloads/Patch-A.MPQ" class="download-button" download>
                            <i class="fas fa-download"></i> Télécharger Patch-A.MPQ
                        </a>
                    </div>
                    
                    <p class="mt-3"><strong>Note :</strong> Ce patch ne modifie que l'interface de création de personnage et n'affecte pas les autres aspects du jeu. Il est 100% compatible avec notre serveur et ne vous empêchera pas de vous connecter.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
