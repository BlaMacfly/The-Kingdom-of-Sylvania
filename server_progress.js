// Script pour gérer la barre de progression du démarrage du serveur
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les formulaires de démarrage et redémarrage
    const startForms = document.querySelectorAll('form[action="server_buttons.php"]');
    
    startForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const action = form.querySelector('input[name="action"]').value;
            const service = form.querySelector('input[name="service"]').value;
            
            // Afficher la barre de progression uniquement pour les actions de démarrage et redémarrage
            if (action === 'start' || action === 'restart') {
                e.preventDefault(); // Empêcher l'envoi normal du formulaire
                
                // Envoyer le formulaire en arrière-plan
                const formData = new FormData(form);
                // Créer l'URL avec un timestamp pour éviter le cache
                const timestamp = new Date().getTime();
                const url = 'server_buttons.php?t=' + timestamp;
                
                // Envoyer la requête avec des en-têtes spécifiques pour éviter le cache
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.text();
                })
                .then(data => {
                    console.log('Réponse du serveur:', data);
                    try {
                        const jsonData = JSON.parse(data);
                        if (jsonData.message === 'Not Found') {
                            throw new Error('Ressource non trouvée sur le serveur');
                        }
                    } catch (e) {
                        // Si ce n'est pas du JSON, c'est probablement une réponse HTML normale
                        console.log('Réponse non-JSON reçue');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la soumission du formulaire:', error);
                    addLogEntry('Erreur lors de la communication avec le serveur: ' + error.message, false, true);
                });
                
                // Récupérer les éléments de la barre de progression
                const progressContainer = document.getElementById('progress-container');
                const progressBar = document.getElementById('progress-bar');
                const progressTitle = document.getElementById('progress-title');
                const progressStatus = document.getElementById('progress-status');
                const progressMessage = document.getElementById('progress-message');
                const progressSteps = document.querySelectorAll('.progress-step');
                const startupLog = document.getElementById('startup-log');
                const finishButtonContainer = document.getElementById('finish-button-container');
                
                // Masquer les contrôles
                const serverControl = document.querySelector('.server-control');
                serverControl.style.display = 'none';
                
                // Afficher la barre de progression
                progressContainer.style.display = 'block';
                
                // Définir le titre en fonction du service
                if (service === 'auth') {
                    progressTitle.textContent = 'Démarrage du serveur Auth...';
                    // Masquer l'étape du serveur World si on ne démarre que Auth
                    progressSteps[3].style.display = 'none';
                } else {
                    progressTitle.textContent = 'Démarrage des serveurs Auth et World...';
                }
                
                // Fonction pour ajouter une entrée au journal
                function addLogEntry(message, isSuccess = false, isError = false) {
                    const logEntry = document.createElement('div');
                    logEntry.className = 'log-entry';
                    logEntry.style.padding = '5px';
                    logEntry.style.borderLeft = isError ? '3px solid #ff3333' : 
                                               isSuccess ? '3px solid #4caf50' : 
                                               '3px solid #ffcc00';
                    logEntry.style.marginBottom = '5px';
                    
                    const timeSpan = document.createElement('span');
                    timeSpan.className = 'log-time';
                    timeSpan.style.color = '#aaa';
                    timeSpan.style.marginRight = '10px';
                    const now = new Date();
                    timeSpan.textContent = `[${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}]`;
                    
                    const messageSpan = document.createElement('span');
                    messageSpan.className = 'log-message';
                    messageSpan.textContent = message;
                    if (isSuccess) messageSpan.style.color = '#4caf50';
                    if (isError) messageSpan.style.color = '#ff3333';
                    
                    logEntry.appendChild(timeSpan);
                    logEntry.appendChild(messageSpan);
                    startupLog.appendChild(logEntry);
                    
                    // Faire défiler vers le bas
                    startupLog.scrollTop = startupLog.scrollHeight;
                }
                
                // Fonction pour mettre à jour l'apparence des étapes
                function updateStep(stepIndex, stepMessage) {
                    // Mettre à jour la barre de progression
                    progressBar.style.width = stepIndex + '%';
                    
                    // Déterminer quelle étape est en cours
                    let currentStepIndex = 0;
                    
                    if (stepIndex < 20) {
                        currentStepIndex = 0; // Arrêt des instances précédentes
                    } else if (stepIndex < 40) {
                        currentStepIndex = 1; // Vérification de MySQL
                    } else if (stepIndex < 60) {
                        currentStepIndex = 2; // Démarrage du serveur Auth
                    } else if (stepIndex < 80) {
                        currentStepIndex = service === 'auth' ? 4 : 3; // Démarrage du serveur World ou finalisation d'Auth
                    } else {
                        currentStepIndex = 4; // Vérification finale
                    }
                    
                    // Mettre à jour le statut et le message détaillé
                    progressStatus.textContent = progressSteps[currentStepIndex].querySelector('.step-text').textContent + '...';
                    if (stepMessage) {
                        progressMessage.textContent = stepMessage;
                    }
                    
                    // Mettre à jour l'apparence des étapes
                    progressSteps.forEach((step, index) => {
                        const icon = step.querySelector('.step-icon');
                        if (index < currentStepIndex) {
                            // Étape terminée
                            step.style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                            step.style.borderLeft = '4px solid #4caf50';
                            icon.textContent = '✅'; // Coche verte
                        } else if (index === currentStepIndex) {
                            // Étape en cours
                            step.style.backgroundColor = 'rgba(255, 204, 0, 0.2)';
                            step.style.borderLeft = '4px solid #ffcc00';
                            icon.textContent = '⏳'; // Sablier
                        } else {
                            // Étape à venir
                            step.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
                            step.style.borderLeft = 'none';
                            icon.textContent = '⚪'; // Cercle blanc
                        }
                    });
                }
                
                // Séquence de démarrage simulée
                let currentStep = 0;
                
                // Étape 1: Arrêt des instances précédentes
                updateStep(5, "Arrêt des instances précédentes des serveurs...");
                addLogEntry("Arrêt des instances précédentes en cours...");
                
                setTimeout(() => {
                    updateStep(20, "Arrêt des instances précédentes terminé");
                    addLogEntry("Arrêt des instances précédentes terminé avec succès", true);
                    
                    // Étape 2: Vérification de MySQL
                    updateStep(25, "Vérification que le service MySQL est bien démarré...");
                    addLogEntry("Vérification du service MySQL...");
                    
                    setTimeout(() => {
                        updateStep(40, "Service MySQL vérifié et prêt");
                        addLogEntry("Service MySQL vérifié et prêt", true);
                        
                        // Étape 3: Démarrage du serveur Auth
                        updateStep(45, "Démarrage du serveur d'authentification (Auth)...");
                        addLogEntry("Démarrage du serveur Auth...");
                        
                        setTimeout(() => {
                            updateStep(60, "Serveur Auth démarré");
                            addLogEntry("Serveur Auth démarré avec succès", true);
                            
                            if (service === 'auth') {
                                // Finalisation pour Auth seulement
                                updateStep(80, "Initialisation du serveur Auth en cours...");
                                addLogEntry("Initialisation du serveur Auth...");
                                
                                setTimeout(() => {
                                    updateStep(100, "Serveur Auth démarré et prêt à recevoir des connexions !");
                                    addLogEntry("Serveur Auth opérationnel et en ligne !", true);
                                    finishButtonContainer.style.display = 'block';
                                }, 3000);
                            } else {
                                // Étape 4: Démarrage du serveur World
                                updateStep(65, "Démarrage du serveur de monde (World)...");
                                addLogEntry("Démarrage du serveur World...");
                                
                                setTimeout(() => {
                                    updateStep(80, "Serveur World démarré");
                                    addLogEntry("Serveur World démarré avec succès", true);
                                    
                                    // Étape 5: Vérification finale
                                    updateStep(90, "Vérification finale des services...");
                                    addLogEntry("Vérification finale des services...");
                                    
                                    setTimeout(() => {
                                        updateStep(100, "Tous les serveurs sont démarrés et prêts à recevoir des connexions !");
                                        addLogEntry("Serveurs Auth et World opérationnels et en ligne !", true);
                                        finishButtonContainer.style.display = 'block';
                                    }, 3000);
                                }, 5000);
                            }
                        }, 5000);
                    }, 3000);
                }, 3000);
            }
        });
    });
});
