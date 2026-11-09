import Chart from 'chart.js/auto';

window.Chart = Chart;

// Global helper for opening / closing modals
window.openModal = function(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
};

window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};

// Auto close flash message after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.4s ease';
            setTimeout(() => alert.remove(), 400);
        }, 4500);
    });
});
