// Constantes pour les noms de classes et races
const CLASS_NAMES = {
    1: 'Guerrier',
    2: 'Paladin',
    3: 'Chasseur',
    4: 'Voleur',
    5: 'Prêtre',
    6: 'Chevalier de la mort',
    7: 'Chaman',
    8: 'Mage',
    9: 'Démoniste',
    11: 'Druide'
};

const RACE_NAMES = {
    1: 'Humain',
    2: 'Orc',
    3: 'Nain',
    4: 'Elfe de la nuit',
    5: 'Mort-vivant',
    6: 'Tauren',
    7: 'Gnome',
    8: 'Troll',
    10: 'Elfe de sang',
    11: 'Draeneï'
};

// Fonction pour obtenir l'icône de classe
function getClassIcon(classId) {
    return `https://wow.zamimg.com/images/wow/icons/small/class_${classId}.jpg`;
}

// Fonction pour obtenir l'icône de race
function getRaceIcon(raceId, gender = 0) {
    return `https://wow.zamimg.com/images/wow/icons/small/race_${raceId}_${gender}.jpg`;
}

// Fonction pour charger le classement par niveau
function loadLevelRanking() {
    fetch('rankings.php?type=level')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('level-ranking');
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun joueur trouvé</td></tr>';
                return;
            }
            
            let html = '';
            data.forEach((player, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <img src="${getClassIcon(player.class)}" alt="${CLASS_NAMES[player.class]}" class="class-icon" />
                            ${player.name}
                        </td>
                        <td>
                            <img src="${getRaceIcon(player.race)}" alt="${RACE_NAMES[player.race]}" class="race-icon" />
                            ${RACE_NAMES[player.race] || 'Inconnu'}
                        </td>
                        <td>${CLASS_NAMES[player.class] || 'Inconnu'}</td>
                        <td>${player.level}</td>
                        <td>${player.guild_name || '-'}</td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur lors du chargement du classement par niveau:', error);
            document.getElementById('level-ranking').innerHTML = '<tr><td colspan="6" class="text-center">Erreur lors du chargement des données</td></tr>';
        });
}

// Fonction pour charger le classement PvP
function loadPvPRanking() {
    fetch('rankings.php?type=pvp')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('pvp-ranking');
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun joueur trouvé</td></tr>';
                return;
            }
            
            let html = '';
            data.forEach((player, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <img src="${getClassIcon(player.class)}" alt="${CLASS_NAMES[player.class]}" class="class-icon" />
                            ${player.name}
                        </td>
                        <td>
                            <img src="${getRaceIcon(player.race)}" alt="${RACE_NAMES[player.race]}" class="race-icon" />
                            ${RACE_NAMES[player.race] || 'Inconnu'}
                        </td>
                        <td>${CLASS_NAMES[player.class] || 'Inconnu'}</td>
                        <td>${player.totalKills}</td>
                        <td>${player.totalHonorPoints}</td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur lors du chargement du classement PvP:', error);
            document.getElementById('pvp-ranking').innerHTML = '<tr><td colspan="6" class="text-center">Erreur lors du chargement des données</td></tr>';
        });
}

// Fonction pour charger le classement des guildes
function loadGuildRanking() {
    fetch('rankings.php?type=guild')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('guild-ranking');
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Aucune guilde trouvée</td></tr>';
                return;
            }
            
            let html = '';
            data.forEach((guild, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${guild.name}</td>
                        <td>${guild.leader_name}</td>
                        <td>${guild.member_count}</td>
                        <td>${Math.round(guild.avg_level)}</td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur lors du chargement du classement des guildes:', error);
            document.getElementById('guild-ranking').innerHTML = '<tr><td colspan="5" class="text-center">Erreur lors du chargement des données</td></tr>';
        });
}

// Charger les classements au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page de classement
    if (document.getElementById('rankingTabs')) {
        // Charger le classement par niveau par défaut
        loadLevelRanking();
        
        // Ajouter des écouteurs d'événements pour les onglets
        document.getElementById('level-tab').addEventListener('click', loadLevelRanking);
        document.getElementById('pvp-tab').addEventListener('click', loadPvPRanking);
        document.getElementById('guild-tab').addEventListener('click', loadGuildRanking);
    }
});
