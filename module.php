<?php
// Fichier de description des modules installés sur le serveur The Kingdom of Sylvania
// Créé le 17 mai 2025

// Tableau des modules installés
$modules = array();

// Module ARAC (All Races All Classes)
$modules[] = array(
    'name' => 'All Races All Classes (ARAC)',
    'description' => 'Permet à toutes les races de jouer toutes les classes, offrant une liberté totale dans la création de personnages.',
    'enabled' => true,
    'features' => array(
        'Supprime les restrictions de classe par race',
        'Permet de créer des combinaisons uniques comme des Taurens Voleurs ou des Gnomes Druides',
        'Compatible avec toutes les races personnalisées',
        'Patch client disponible pour téléchargement'
    )
);

// Module Transmog
$modules[] = array(
    'name' => 'Transmogrification',
    'description' => 'Permet aux joueurs de modifier l\'apparence de leurs équipements tout en conservant leurs statistiques.',
    'enabled' => true,
    'features' => array(
        'PNJ de transmogrification disponible dans les capitales',
        'Prévisualisation des apparences avant application',
        'Sauvegarde des ensembles d\'apparence',
        'Compatible avec tous les types d\'équipement'
    )
);

// Module SoloCraft
$modules[] = array(
    'name' => 'SoloCraft',
    'description' => 'Ajuste les statistiques des joueurs pour les donjons et raids en fonction du nombre de joueurs présents.',
    'enabled' => true,
    'features' => array(
        'Permet de faire des donjons et raids en solo ou en petit groupe',
        'Ajustement automatique de la puissance selon le nombre de joueurs',
        'Équilibrage pour toutes les classes',
        'Compatible avec tous les donjons et raids'
    )
);

// Module Premium
$modules[] = array(
    'name' => 'Premium',
    'description' => 'Ajoute des fonctionnalités premium pour les joueurs VIP.',
    'enabled' => true,
    'features' => array(
        'Banque mobile',
        'Hôtel des ventes mobile',
        'Téléportation vers des lieux importants',
        'Changement de race/faction',
        'Personnalisation avancée des personnages'
    )
);

// Module Random Enchants
$modules[] = array(
    'name' => 'Random Enchants',
    'description' => 'Ajoute aléatoirement des enchantements sur les objets obtenus par butin, quêtes ou artisanat.',
    'enabled' => true,
    'features' => array(
        'Enchantements aléatoires sur les objets de butin',
        'Préfixes et suffixes variés (du Loup, de l\'Aigle, etc.)',
        'Différentes qualités d\'enchantements',
        'Compatible avec tous les types d\'objets'
    )
);

// Module AuctionHouseBot (nouveau)
$modules[] = array(
    'name' => 'AuctionHouseBot',
    'description' => 'Un bot qui remplit automatiquement l\'hôtel des ventes avec des objets pour assurer un marché actif sur le serveur.',
    'enabled' => true,
    'features' => array(
        'Remplit l\'hôtel des ventes avec des objets de différentes qualités',
        'Configurable pour différents types d\'objets (armes, armures, consommables, etc.)',
        'Ajuste automatiquement les prix en fonction de l\'offre et de la demande',
        'Maintient un marché actif même avec peu de joueurs',
        'Cycle d\'objets pour renouveler régulièrement les offres'
    )
);

// Fonction pour afficher les modules
function displayModules() {
    global $modules;
    
    echo '<div class="modules-container">';
    
    foreach ($modules as $module) {
        echo '<div class="module-card">';
        echo '<h3>' . htmlspecialchars($module['name']) . '</h3>';
        echo '<p class="module-description">' . htmlspecialchars($module['description']) . '</p>';
        
        echo '<div class="module-status">';
        if ($module['enabled']) {
            echo '<span class="status-enabled">Activé</span>';
        } else {
            echo '<span class="status-disabled">Désactivé</span>';
        }
        echo '</div>';
        
        echo '<div class="module-features">';
        echo '<h4>Fonctionnalités:</h4>';
        echo '<ul>';
        foreach ($module['features'] as $feature) {
            echo '<li>' . htmlspecialchars($feature) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
        
        echo '</div>';
    }
    
    echo '</div>';
}
?>

<!-- CSS pour l'affichage des modules -->
<style>
.modules-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.module-card {
    background-color: #2c2c2c;
    border-radius: 8px;
    padding: 20px;
    width: calc(50% - 20px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.module-card:hover {
    transform: translateY(-5px);
}

.module-card h3 {
    color: #ffcc00;
    margin-top: 0;
    border-bottom: 1px solid #444;
    padding-bottom: 10px;
}

.module-description {
    color: #ccc;
    margin-bottom: 15px;
}

.module-status {
    margin-bottom: 15px;
}

.status-enabled {
    background-color: #2ecc71;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.status-disabled {
    background-color: #e74c3c;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.module-features h4 {
    color: #3498db;
    margin-bottom: 10px;
}

.module-features ul {
    padding-left: 20px;
}

.module-features li {
    color: #bbb;
    margin-bottom: 5px;
}

@media (max-width: 768px) {
    .module-card {
        width: 100%;
    }
}
</style>
