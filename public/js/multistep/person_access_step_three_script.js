// Liste des names de vos champs à marquer “MicroCésame”
const microFields = [
    'person_access_step_three_form[fluxel_training]',
    'person_access_step_three_form[gies]',
    'person_access_step_three_form[atex]',
    'person_access_step_three_form[zar]',
    'person_access_step_three_form[health]'
];

// Affiche un message d'erreur sous l'élément
function showError(el, msg) {
    if (!el) return;
    let box = el.parentNode;
    let err = box.querySelector('.error-message');
    if (!err) {
        err = document.createElement('div');
        err.className = 'error-message';
        err.style.color = 'red';
        err.style.fontSize = '0.85em';
        box.appendChild(err);
    }
    err.textContent = msg;
    el.classList.add('invalid');
}

// Marquer les labels des champs MicroCésame
microFields.forEach(name => {
    const $input = $('[name="' + name + '"]');
    if (!$input.length || !$input.is(':visible')) return;

    const id = $input.attr('id');
    if (!id) return;
    const $label = $('label[for="' + id + '"]');
    if ($label.length && !$label.find('sup.micro').length) {
        $label.append(' <sup class="micro">m</sup>');
    }
});

// Gestion des dates : avertissements expiration / bientôt expirée
document.addEventListener("DOMContentLoaded", function () {
    // notifyMonths = fenêtre d'alerte avant la date de fin de validité
    const rules = {
        'person_access_step_three_form_health': { notifyMonths: 6, label: 'Visite médicale' },
        'person_access_step_three_form_zar':    { notifyMonths: 6, label: 'ZAR' },
        'person_access_step_three_form_atex':   { notifyMonths: 0, label: 'ATEX' },
        'person_access_step_three_form_gies':   { notifyMonths: 0, label: 'GIES' },
        'person_access_step_three_form_fluxel_training': { notifyMonths: 1, label: 'Accueil sécurité' }
    };

    // La valeur saisie EST la date de fin de validité
    const getStatus = (expiryDate, notifyMonths) => {
        if (!(expiryDate instanceof Date) || isNaN(expiryDate)) return null;
        const now = new Date();
        const notify = new Date(expiryDate);
        notify.setMonth(notify.getMonth() - notifyMonths);

        if (now >= expiryDate) return 'expired';
        if (now >= notify)     return 'soon';
        return null;
    };

    const createMessage = (label, status) => {
        const message = document.createElement("div");
        message.classList.add("warning-message", "warning", status);
        message.textContent =
            status === 'expired'
                ? `${label} : validité expirée.`
                : `${label} : validité bientôt expirée.`;
        return message;
    };

    Object.entries(rules).forEach(([fieldId, { notifyMonths, label }]) => {
        const input = document.getElementById(fieldId);
        if (!input) return;

        const updateWarning = () => {
            const container = input.parentNode;
            const existing = container.querySelector(".warning-message");
            if (existing) existing.remove();

            if (!input.value) return;

            const expiry = new Date(input.value);
            const status = getStatus(expiry, notifyMonths);
            if (!status) return;

            const message = createMessage(label, status);
            container.appendChild(message);
        };

        updateWarning();
        input.addEventListener("change", updateWarning);
    });
});

// Ajout * sur les labels des champs obligatoires (MAJ : fluxel + atex)
document.addEventListener('DOMContentLoaded', function () {
    const requiredFields = [
        'person_access_step_three_form_fluxel_training',
        'person_access_step_three_form_atex',
        'person_access_step_three_form_gies',
        'person_access_step_three_form_health',
    ];

    requiredFields.forEach(function (id) {
        const label = document.querySelector('label[for="' + id + '"]');
        if (label && !label.innerHTML.includes('*')) {
            label.innerHTML += ' <sup class="text-danger">*</sup>';
        }
    });
});

// Validation à la soumission (MAJ : gies unique, atex, fin de validité)
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[name="person_access_step_three_form"]');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const btn = e.submitter;
        if (btn && btn.hasAttribute('formnovalidate')) {
            return; // Skip le contrôle
        }

        const fluxel = document.getElementById('person_access_step_three_form_fluxel_training');
        const atex   = document.getElementById('person_access_step_three_form_atex');
        const gies   = document.getElementById('person_access_step_three_form_gies');

        [fluxel, atex, gies].forEach(input => {
            if (!input) return;
            const err = input.parentNode.querySelector('.error-message');
            if (err) err.remove();
            input.classList.remove('invalid');
        });

        let hasError = false;

        if (!gies || gies.value.trim() === '') {
            showError(gies, "Le champ Gies (date de fin de validité) est obligatoire.");
            hasError = true;
        }

        if (!fluxel || fluxel.value.trim() === '') {
            showError(fluxel, "Le champ Accueil sécurité Fluxel (date de fin de validité) est obligatoire.");
            hasError = true;
        }

        if (!atex || atex.value.trim() === '') {
            showError(atex, "Le champ ATEX (date de fin de validité) est obligatoire.");
            hasError = true;
        }

        let expiredCertificate = document.querySelectorAll('.warning-message.expired').length;
        if (expiredCertificate > 0) {
            showError(fluxel, 'certaines dates ont expiré. Veuillez les corriger.');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
        }
    });
});
