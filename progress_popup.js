// Script pour afficher une popup de progression lors du démarrage du serveur
function showServerStartProgress(service) {
    // Créer l'élément de popup
    const popup = document.createElement('div');
    popup.className = 'progress-popup';
    popup.style.position = 'fixed';
    popup.style.top = '50%';
    popup.style.left = '50%';
    popup.style.transform = 'translate(-50%, -50%)';
    popup.style.backgroundColor = '#2a2a2a';
    popup.style.border = '2px solid #ffcc00';
    popup.style.borderRadius = '10px';
    popup.style.padding = '20px';
    popup.style.zIndex = '1000';
    popup.style.width = '500px';
    popup.style.maxWidth = '90%';
    popup.style.boxShadow = '0 0 20px rgba(0, 0, 0, 0.7)';
    
    // Titre
    const title = document.createElement('h3');
    title.textContent = service === 'auth' ? 'Démarrage du serveur Auth...' : 'Démarrage des serveurs...';
    title.style.color = '#ffcc00';
    title.style.textAlign = 'center';
    title.style.marginBottom = '20px';
    
    // Conteneur de la barre de progression
    const progressContainer = document.createElement('div');
    progressContainer.style.backgroundColor = '#333';
    progressContainer.style.borderRadius = '5px';
    progressContainer.style.height = '25px';
    progressContainer.style.marginBottom = '15px';
    progressContainer.style.overflow = 'hidden';
    
    // Barre de progression
    const progressBar = document.createElement('div');
    progressBar.style.backgroundColor = '#ffcc00';
    progressBar.style.height = '100%';
    progressBar.style.width = '0%';
    progressBar.style.transition = 'width 0.5s ease';
    
    // Message de statut
    const statusMessage = document.createElement('p');
    statusMessage.textContent = 'Initialisation...';
    statusMessage.style.textAlign = 'center';
    statusMessage.style.fontSize = '18px';
    statusMessage.style.fontWeight = 'bold';
    statusMessage.style.margin = '15px 0';
    
    // Message détaillé
    const detailedMessage = document.createElement('div');
    detailedMessage.style.textAlign = 'center';
    detailedMessage.style.backgroundColor = 'rgba(0,0,0,0.2)';
    detailedMessage.style.padding = '15px';
    detailedMessage.style.borderRadius = '5px';
    detailedMessage.style.marginBottom = '15px';
    detailedMessage.style.minHeight = '50px';
    detailedMessage.textContent = 'Préparation du démarrage des serveurs...';
    
    // Journal de démarrage
    const log = document.createElement('div');
    log.style.maxHeight = '150px';
    log.style.overflowY = 'auto';
    log.style.backgroundColor = 'rgba(0,0,0,0.3)';
    log.style.borderRadius = '5px';
    log.style.padding = '10px';
    log.style.fontFamily = 'monospace';
    log.style.marginBottom = '15px';
    
    // Bouton de fermeture
    const closeButton = document.createElement('button');
    closeButton.textContent = 'Fermer';
    closeButton.style.display = 'none';
    closeButton.style.padding = '10px 20px';
    closeButton.style.backgroundColor = '#4caf50';
    closeButton.style.color = 'white';
    closeButton.style.border = 'none';
    closeButton.style.borderRadius = '5px';
    closeButton.style.cursor = 'pointer';
    closeButton.style.margin = '0 auto';
    closeButton.style.display = 'block';
    closeButton.onclick = () => {
        document.body.removeChild(popup);
        window.location.reload();
    };
    
    // Ajouter les éléments à la popup
    progressContainer.appendChild(progressBar);
    popup.appendChild(title);
    popup.appendChild(progressContainer);
    popup.appendChild(statusMessage);
    popup.appendChild(detailedMessage);
    popup.appendChild(log);
    popup.appendChild(closeButton);
    
    // Ajouter la popup au document
    document.body.appendChild(popup);
    
    // Fonction pour ajouter une entrée au journal
    function addLogEntry(message, isSuccess = false, isError = false) {
        const entry = document.createElement('div');
        entry.style.padding = '5px';
        entry.style.borderLeft = isError ? '3px solid #ff3333' : 
                                isSuccess ? '3px solid #4caf50' : 
                                '3px solid #ffcc00';
        entry.style.marginBottom = '5px';
        
        const time = document.createElement('span');
        time.style.color = '#aaa';
        time.style.marginRight = '10px';
        const now = new Date();
        time.textContent = `[${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}]`;
        
        const text = document.createElement('span');
        text.textContent = message;
        if (isSuccess) text.style.color = '#4caf50';
        if (isError) text.style.color = '#ff3333';
        
        entry.appendChild(time);
        entry.appendChild(text);
        log.appendChild(entry);
        
        // Faire défiler vers le bas
        log.scrollTop = log.scrollHeight;
    }
    
    // Ajouter la première entrée
    addLogEntry('Initialisation du processus de démarrage...');
    
    // Initialiser la progression
    let progress = 0;
    progressBar.style.width = progress + '%';
    statusMessage.textContent = 'Initialisation...';
    detailedMessage.textContent = 'Initialisation du démarrage des serveurs...';
    addLogEntry('Initialisation du démarrage des serveurs...');
    
    // Fonction pour vérifier le statut réel du serveur
    function checkServerStatus() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'real_server_status.php?t=' + new Date().getTime(), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    // Mettre à jour la barre de progression
                    progress = response.progress;
                    progressBar.style.width = progress + '%';
                    statusMessage.textContent = response.message;
                    
                    // Mettre à jour le message détaillé
                    if (response.status === 'error') {
                        detailedMessage.textContent = 'Erreur: ' + response.message;
                        detailedMessage.style.color = '#ff6666';
                        closeButton.style.display = 'block';
                    } else if (response.status === 'completed') {
                        detailedMessage.textContent = 'Tous les serveurs sont démarrés et prêts à recevoir des connexions !';
                        closeButton.style.display = 'block';
                    } else {
                        detailedMessage.textContent = response.message;
                    }
                    
                    // Vider le journal et ajouter les nouvelles entrées
                    log.innerHTML = '';
                    if (response.logs && response.logs.length > 0) {
                        response.logs.forEach(function(logEntry) {
                            // Déterminer si c'est une erreur ou un succès
                            const isError = logEntry.includes('ERREUR');
                            const isSuccess = logEntry.includes('succès') || logEntry.includes('prêt');
                            addLogEntry(logEntry, isSuccess, isError);
                        });
                    }
                    
                    // Continuer à vérifier si le serveur n'est pas encore prêt
                    if (response.status !== 'completed' && response.status !== 'error') {
                        setTimeout(checkServerStatus, 1000);
                    }
                } catch (e) {
                    console.error('Erreur lors de l\'analyse de la réponse:', e);
                    setTimeout(checkServerStatus, 1000);
                }
            } else if (xhr.readyState === 4) {
                // Erreur de communication avec le serveur
                detailedMessage.textContent = 'Erreur de communication avec le serveur';
                detailedMessage.style.color = '#ff6666';
                setTimeout(checkServerStatus, 2000);
            }
        };
        xhr.send();
    }
    
    // Démarrer la vérification du statut
    setTimeout(checkServerStatus, 1000);
}

// Fonction pour vérifier si l'utilisateur est authentifié
function checkAuth(callback, service) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'check_auth.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.authenticated) {
                    callback(service);
                } else {
                    // Rediriger vers la page d'authentification
                    window.location.href = 'server_auth.php';
                }
            } else {
                // En cas d'erreur, rediriger vers la page d'authentification
                window.location.href = 'server_auth.php';
            }
        }
    };
    xhr.send();
}

// Fonction pour démarrer le serveur
function startServer(service) {
    // Vérifier l'authentification avant de démarrer le serveur
    checkAuth(function(service) {
        // Afficher la popup de progression
        showServerStartProgress(service);
        
        // Envoyer la requête en arrière-plan
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'server_buttons.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('action=start&service=' + service);
    }, service);
}

// Fonction pour redémarrer le serveur
function restartServer(service) {
    // Vérifier l'authentification avant de redémarrer le serveur
    checkAuth(function(service) {
        // Afficher la popup de progression
        showServerStartProgress(service);
        
        // Envoyer la requête en arrière-plan
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'server_buttons.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('action=restart&service=' + service);
    }, service);
}

// Fonction pour arrêter le serveur
function stopServer(service) {
    // Envoyer la requête en arrière-plan
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'server_buttons.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('action=stop&service=' + service);
    
    // Recharger la page après un court délai
    setTimeout(() => {
        window.location.reload();
    }, 2000);
}
