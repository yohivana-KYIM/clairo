const form = document.getElementById('vehicle_access_step_six_form');
const today = new Date().toISOString().slice(0,10);

// Helper to show a message under a field or group
function showError(container, message) {
    let err = container.querySelector('.error-message');
    if (!err) {
        err = document.createElement('div');
        err.className = 'error-message';
        err.style.color = 'red';
        err.style.fontSize = '0.85em';
        container.appendChild(err);
    }
    err.textContent = message;
    // highlight first input inside container
    const field = container.querySelector('input, textarea');
    if (field) field.classList.add('invalid');
}

// Remove all existing errors
function clearErrors() {
    form.querySelectorAll('.error-message').forEach(e => e.remove());
    form.querySelectorAll('.invalid').forEach(f => f.classList.remove('invalid'));
}

form.addEventListener('submit', function(e){
    clearErrors();
    let valid = true;

    // 1) Acceptation des CGU
    const termsBox = document.getElementById('vehicle_access_step_six_form_accept_terms').parentNode;
    const terms = document.getElementById('vehicle_access_step_six_form_accept_terms');
    if (!terms.checked) {
        showError(termsBox, 'Vous devez accepter les conditions générales.');
        valid = false;
    }

    // 2) Signature (fichier)
    const signInput = document.getElementById('vehicle_access_step_six_form_signature');
    if (!signInput.files || signInput.files.length === 0) {
        showError(signInput.parentNode, 'Veuillez télécharger votre signature.');
        valid = false;
    }

    // 3) Lieu de retrait (radio)
    const cardGroup = document.getElementById('vehicle_access_step_six_form_card_place');
    const cardChoice = form.querySelector('input[name="vehicle_access_step_six_form[card_place]"]:checked');
    if (!cardChoice) {
        showError(cardGroup, 'Veuillez choisir un lieu de retrait.');
        valid = false;
    }

    // 4) Validation des documents
    const docBox = document.getElementById('vehicle_access_step_six_form_document_validation').parentNode;
    const docCheck = document.getElementById('vehicle_access_step_six_form_document_validation');
    if (!docCheck.checked) {
        showError(docBox, 'Vous devez confirmer que tous les documents sont validés.');
        valid = false;
    }

    // 5) Décision d'accès
    const decGroup = document.getElementById('vehicle_access_step_six_form_access_decision');
    const decision = form.querySelector('input[name="vehicle_access_step_six_form[access_decision]"]:checked');
    if (!decision) {
        showError(decGroup, 'Veuillez sélectionner une décision d’accès.');
        valid = false;
    }

    // 6) Durée de validité (date)
    const durationInput = document.getElementById('vehicle_access_step_six_form_access_duration');
    if (!durationInput.value) {
        showError(durationInput.parentNode, 'Veuillez indiquer la durée de validité.');
        valid = false;
    } else if (durationInput.value < today) {
        showError(durationInput.parentNode, 'La date doit être aujourd’hui ou ultérieure.');
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
        // scroll to first error
        const firstErr = form.querySelector('.invalid');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

function initFormPrefillAndPreview({
                                       uploadedFiles = {},
                                       formPrefix = 'vehicle_access_step_six_form',
                                       basePathToRemove = window.APP_PUBLIC_PATH || '/srv/app/public'
                                   }) {
    Object.entries(uploadedFiles).forEach(([field, value]) => {
        const fieldId = `${formPrefix}_${field}`;
        const $input = $('#' + fieldId);

        if (!$input.length) return;

        // Gestion des cases à cocher
        if ($input.attr('type') === 'checkbox') {
            if (value === '1' || value === true) {
                $input.prop('checked', true);
            }
            return;
        }

        // Gestion des fichiers (signature, etc.)
        if ($input.attr('type') === 'file') {
            if (value) {
                const displayUrl = value.replace(basePathToRemove, '');
                const ext = value.split('.').pop().toLowerCase();

                const preview = $('<div>').addClass('existing-file mt-2');
                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    preview.append($('<img>', {
                        src: displayUrl,
                        alt: field,
                        css: {
                            maxWidth: '200px',
                            border: '1px solid #ccc',
                            borderRadius: '6px',
                            marginTop: '5px'
                        }
                    }));
                } else {
                    preview.append($('<a>', {
                        href: displayUrl,
                        text: '📄 Voir le fichier déjà envoyé',
                        target: '_blank',
                        css: {
                            display: 'inline-block',
                            marginTop: '5px'
                        }
                    }));
                }
                $input.parent().prepend($('<input>', {
                    type: 'hidden',
                    name: $input.attr('name'),
                    value: displayUrl
                }));
                $input.parent().append(preview);
            }
            return;
        }

        // Gestion des champs texte et textarea
        if ($input.is('input[type="text"], textarea, select')) {
            $input.val(value);
        }
    });
}

$(function () {
    initFormPrefillAndPreview({
        uploadedFiles: stepValues,
        formPrefix: 'vehicle_access_step_six_form'
    });
});

