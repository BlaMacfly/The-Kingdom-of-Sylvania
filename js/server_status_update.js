// Script de mise à jour de l'état des serveurs
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM pour les indicateurs de statut
    const authStatusIndicator = document.querySelector('.server-status .status-indicator:first-of-type');
    const authStatusText = document.querySelector('.server-status .status-text:first-of-type');
    const worldStatusIndicator = document.querySelector('.server-status .status-indicator:last-of-type');
    const worldStatusText = document.querySelector('.server-status .status-text:last-of-type');
    
    // Fonction pour mettre à jour l'état des serveurs
    function updateServerStatus() {
        // Utiliser l'API existante pour vérifier l'état des services
        fetch('server_control_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'status',
                service: 'all'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                // Mettre à jour l'état du serveur d'authentification
                const authServerRunning = data.results.auth && data.results.auth.status;
                
                // Mettre à jour les indicateurs dans l'en-tête
                authStatusIndicator.classList.toggle('offline', !authServerRunning);
                authStatusText.classList.toggle('online', authServerRunning);
                authStatusText.classList.toggle('offline', !authServerRunning);
                authStatusText.textContent = 'Serveur d\'authentification: ' + (authServerRunning ? 'En ligne' : 'Hors ligne');
                
                // Mettre à jour l'état du serveur de jeu
                const worldServerRunning = data.results.world && data.results.world.status;
                
                // Mettre à jour les indicateurs dans l'en-tête
                worldStatusIndicator.classList.toggle('offline', !worldServerRunning);
                worldStatusText.classList.toggle('online', worldServerRunning);
                worldStatusText.classList.toggle('offline', !worldServerRunning);
                worldStatusText.textContent = 'Serveur de jeu: ' + (worldServerRunning ? 'En ligne' : 'Hors ligne');
                
                // Journaliser l'état des services pour le débogage
                console.log('État des services (statut):', {
                    auth: authServerRunning ? 'En ligne' : 'Hors ligne',
                    world: worldServerRunning ? 'En ligne' : 'Hors ligne',
                    timestamp: new Date().toLocaleTimeString()
                });
            }
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour de l\'état des serveurs:', error);
        });
    }
    
    // Mettre à jour l'état des serveurs immédiatement
    updateServerStatus();
    
    // Mettre à jour l'état des serveurs toutes les 2 secondes
    setInterval(updateServerStatus, 2000);
});
