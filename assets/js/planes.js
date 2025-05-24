// Eliminar notificación PHP después de 5 segundos
const phpNotification = document.getElementById('php-notification');
    if (phpNotification) {
        setTimeout(() => {
            phpNotification.classList.add('hide');
            setTimeout(() => phpNotification.remove(), 300);
        }, 5000);
    }

        // Búsqueda
const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        document.querySelectorAll('.plan-card').forEach(card => {
        const titulo = card.getAttribute('data-titulo');
        card.style.display = titulo.includes(term) ? 'block' : 'none';
        });
    });

        
//2
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detail-modal');
    const closeBtn = document.querySelector('.close-modal');
    
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const planCard = this.closest('.plan-card');
            
            document.getElementById('modal-title').textContent = 
                planCard.querySelector('.plan-header h3').textContent;
            document.getElementById('modal-status').textContent = 
                planCard.querySelector('.badge').textContent;
            document.getElementById('modal-desc').textContent = 
                planCard.querySelector('p:nth-of-type(1) .full-text').textContent;
            document.getElementById('modal-obj').textContent = 
                planCard.querySelector('p:nth-of-type(2) .full-text').textContent;
            
            const meta = planCard.querySelectorAll('.plan-meta span');
            document.getElementById('modal-year').textContent = meta[0].textContent;
            document.getElementById('modal-resp').textContent = meta[1].textContent;
            document.getElementById('modal-budget').textContent = meta[2].textContent;
            
            modal.style.display = 'block';
        });
    });
    
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

//3
document.addEventListener('DOMContentLoaded', function() {
    const viewModal = document.getElementById('view-plan-modal');
    const closeModal = document.querySelector('.close-modal');
    
    // En planes.js, actualiza la parte del modal view-plan-modal

    document.querySelectorAll('.view-plan-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const planId = this.getAttribute('data-id');
            const planCard = this.closest('.plan-card');
            
            // Llenar el modal con los datos del plan
            document.getElementById('plan-modal-title').textContent = 
                planCard.querySelector('.plan-header h3').textContent;
            
            const statusBadge = planCard.querySelector('.badge');
            document.getElementById('plan-modal-status').textContent = statusBadge.textContent;
            document.getElementById('plan-modal-status').className = 'badge' + statusBadge.className;
            
            document.getElementById('plan-modal-description').textContent = 
                planCard.querySelector('p:nth-of-type(1)').textContent.replace('Descripción:', '').trim();
            
            document.getElementById('plan-modal-objective').textContent = 
                planCard.querySelector('p:nth-of-type(2)').textContent.replace('Objetivo:', '').trim();
            
            const metaItems = planCard.querySelectorAll('.plan-meta span');
            document.getElementById('plan-modal-year').textContent = metaItems[0].textContent;
            document.getElementById('plan-modal-responsible').textContent = metaItems[1].textContent;
            document.getElementById('plan-modal-budget').textContent = metaItems[2].textContent;
            
            // Mostrar el modal con animación
            const viewModal = document.getElementById('view-plan-modal');
            viewModal.classList.add('show');
        });
    });
    
    // Cerrar modal
    document.querySelector('#view-plan-modal .close-modal').addEventListener('click', function() {
        document.getElementById('view-plan-modal').classList.remove('show');
    });
    
    // Cerrar al hacer clic fuera del modal
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('view-plan-modal')) {
            document.getElementById('view-plan-modal').classList.remove('show');
        }
    });
});