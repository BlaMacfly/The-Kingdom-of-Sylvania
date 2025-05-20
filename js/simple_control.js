// Script simplifié pour le contrôle du serveur
document.addEventListener('DOMContentLoaded', function() {
    // Intercepter tous les liens qui pointent vers server_auth.php
    const serverAuthLinks = document.querySelectorAll('a[href*="server_auth.php"]');
    
    serverAuthLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Si l'utilisateur est déjà authentifié, rediriger vers le nouveau contrôleur
            if (document.cookie.indexOf('PHPSESSID=') !== -1) {
                e.preventDefault();
                window.location.href = 'server_control_unified.php';
            }
        });
    });
    
    // Ajouter des gestionnaires d'événements aux boutons de contrôle
    const controlButtons = document.querySelectorAll('.control-button button, button#power-button, button#restart-button');
    
    controlButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Déterminer l'action en fonction de la classe ou du texte du bouton
            let action = 'start';
            if (this.classList.contains('stop') || this.textContent.trim().toLowerCase() === 'arrêter') {
                action = 'stop';
            } else if (this.id === 'restart-button' || this.textContent.trim().toLowerCase() === 'redémarrer') {
                action = 'restart';
            }
            
            // Déterminer le service (par défaut 'world' pour contrôler les deux serveurs)
            const service = 'world';
            
            console.log(`Action: ${action} pour le service: ${service}`);
            
            // Rediriger vers le script de contrôle direct
            window.location.href = `direct_control.php?action=${action}&service=${service}`;
        });
    });
});
