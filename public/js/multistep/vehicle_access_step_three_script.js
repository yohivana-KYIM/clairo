// Récupération des éléments
const fosCheckbox   = document.getElementById('vehicle_access_step_three_form_fos_port_access');
const laveraCheckbox = document.getElementById('vehicle_access_step_three_form_lavera_port_access');

const fosReason     = document.getElementById('vehicle_access_step_three_form_fos_access_reason');
const laveraReason  = document.getElementById('vehicle_access_step_three_form_lavera_access_reason');

// Fonction de bascule du required
function toggleRequired(checkbox, textarea) {
    textarea.required = checkbox.checked;
}

// Attacher les écouteurs
fosCheckbox.addEventListener('change', () => {
    toggleRequired(fosCheckbox, fosReason);
});

laveraCheckbox.addEventListener('change', () => {
    toggleRequired(laveraCheckbox, laveraReason);
});

// Au chargement de la page, ajuster l’état initial (pour la validation HTML5)
document.addEventListener('DOMContentLoaded', () => {
    toggleRequired(fosCheckbox, fosReason);
    toggleRequired(laveraCheckbox, laveraReason);
});