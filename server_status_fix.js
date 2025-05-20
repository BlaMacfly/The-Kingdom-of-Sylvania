// Script JavaScript pour mettre à jour l'état des serveurs en temps réel
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour mettre à jour l'état des serveurs
    function updateServerStatus() {
        // Appeler le script de vérification d'état
        fetch('real_status.php')
            .then(response => response.json())
            .then(data => {
                // Mettre à jour l'état d'Auth
                const authStatus = document.getElementById('auth-status');
                if (authStatus) {
                    if (data.auth_running === "running") {
                        authStatus.textContent = 'EN LIGNE';
                        authStatus.className = 'status online';
                    } else {
                        authStatus.textContent = 'HORS LIGNE';
                        authStatus.className = 'status offline';
                    }
                }
                
                // Mettre à jour l'état de World
                const worldStatus = document.getElementById('world-status');
                if (worldStatus) {
                    if (data.world_running === "running") {
                        worldStatus.textContent = 'EN LIGNE';
                        worldStatus.className = 'status online';
                    } else {
                        worldStatus.textContent = 'HORS LIGNE';
                        worldStatus.className = 'status offline';
                    }
                }
                
                // Mettre à jour l'horodatage
                const timestamp = document.getElementById('status-timestamp');
                if (timestamp) {
                    timestamp.textContent = 'Dernière vérification: ' + data.timestamp;
                }
            })
            .catch(error => {
                console.error('Erreur lors de la vérification du statut:', error);
            });
    }
    
    // Mettre à jour l'état immédiatement
    updateServerStatus();
    
    // Mettre à jour l'état toutes les 5 secondes
    setInterval(updateServerStatus, 5000);
});
