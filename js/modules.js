document.addEventListener('DOMContentLoaded', function() {
    // Gestion des catégories
    const categoryButtons = document.querySelectorAll('.category-btn');
    const moduleCards = document.querySelectorAll('.module-card');
    const sections = document.querySelectorAll('.module-section');
    
    // Fonction pour filtrer les modules par catégorie
    function filterModules(category) {
        if (category === 'all') {
            sections.forEach(section => {
                section.style.display = 'block';
            });
        } else {
            sections.forEach(section => {
                if (section.getAttribute('data-category') === category) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        }
        
        // Mettre à jour les boutons actifs
        categoryButtons.forEach(btn => {
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
    
    // Ajouter des écouteurs d'événements aux boutons de catégorie
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            filterModules(category);
        });
    });
    
    // Gestion du bouton "Retour en haut"
    const backToTopBtn = document.querySelector('.back-to-top');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
    
    backToTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
