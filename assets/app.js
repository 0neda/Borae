import './app.css';
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import focus from '@alpinejs/focus';

window.Alpine = Alpine;
Alpine.plugin(mask);
Alpine.plugin(focus);
Alpine.start();

// Toast/alerta listener para eventos customizados
window.addEventListener('alerta', function (e) {
	if (!e.detail) return;
	const type = e.detail.type || 'info';
	const message = e.detail.message || '';
	const icon = getAlertaIcon(type);
	// Cria apenas o alerta, sem o container toast/toast-start
	const alerta = document.createElement('div');
	alerta.innerHTML = `
        <div x-data="{ alertaVisivel: true }" x-show="alertaVisivel" x-init="setTimeout(() => alertaVisivel = false, 3000)" role="alert" class="alert alert-${type} shadow-lg mb-2" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <i class=\"ph ${icon} text-2xl text-${type}-content\"></i>
            <span class=\"ml-2 text-sm font-semibold text-${type}-content\">${message}</span>
            <div>
                <button type=\"button\" @click=\"alertaVisivel = false\" class=\"btn btn-ghost btn-xs btn-circle border-0 hover:bg-white/10 text-white\" aria-label=\"Dispensar alerta\">
                    <i class=\"ph ph-x text-md\"></i>
                </button>
            </div>
        </div>
    `;
	const alertasContainer = document.getElementById('alertas');
	// Limita a 4 alertas visíveis, removendo o mais antigo se necessário
	while (alertasContainer.children.length >= 4) {
		alertasContainer.removeChild(alertasContainer.firstElementChild);
	}
	// Adiciona o novo alerta no final (empilha para cima)
	alertasContainer.appendChild(alerta.firstElementChild);
	// Re-inicializa Alpine para o novo alerta
	window.Alpine && window.Alpine.initTree(alertasContainer.lastElementChild);
});

function getAlertaIcon(type) {
	switch (type) {
		case 'success':
			return 'ph-check-circle';
		case 'warning':
			return 'ph-warning';
		case 'error':
			return 'ph-siren';
		case 'info':
			return 'ph-info';
		default:
			return 'ph-dots-three-circle';
	}
}

console.log('Boraê Inicializado!');
