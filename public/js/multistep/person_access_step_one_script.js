// CONFIGURATION
const radioGroups = [
    'person_access_step_one_form[access_duration]',
    'person_access_step_one_form[access_type]',
    'person_access_step_one_form[duplicate_reason]'
];
const checkboxGroups = [
    'person_access_step_one_form[access_locations][]'
];
const simpleFields = [
    'person_access_step_one_form[request_date]',
    'person_access_step_one_form[company_name]',
    'person_access_step_one_form[address]',
    'person_access_step_one_form[postal_code]',
    'person_access_step_one_form[city]',
    'person_access_step_one_form[country]',
    'person_access_step_one_form[siren]',
    'person_access_step_one_form[naf]',
    'person_access_step_one_form[siret]',
    'person_access_step_one_form[vat_number]',
    'person_access_step_one_form[access_purpose]',
    'person_access_step_one_form[security_officer_name]',
    'person_access_step_one_form[security_officer_position]',
    'person_access_step_one_form[security_officer_email]',
    'person_access_step_one_form[security_officer_phone]'
];

// 1️⃣ Définir les sélecteurs des champs obligatoires
const requiredFields = [
    '#person_access_step_one_form_company_name',
    '#person_access_step_one_form_siren',
    '#person_access_step_one_form_siret',
    '#person_access_step_one_form_naf',
    '#person_access_step_one_form_address',
    '#person_access_step_one_form_postal_code',
    '#person_access_step_one_form_city',
    '#person_access_step_one_form_country',
    '#person_access_step_one_form_access_purpose',
    '#person_access_step_one_form_security_officer_name',
    '#person_access_step_one_form_security_officer_position',
    '#person_access_step_one_form_security_officer_email',
    '#person_access_step_one_form_security_officer_phone'
];

document.addEventListener('DOMContentLoaded', function() {
    requiredFields.forEach(sel => {
        const input = document.querySelector(sel);
        if (input) {
            const label = document.querySelector(`label[for="${input.id}"]`);
            if (label && !label.innerHTML.includes('*')) {
                label.innerHTML += ' <sup>*</sup>';
            }
        }
    });
});

function validateRequiredFields(e) {
    let ok = true;

    // 2️⃣ Vérifier tous les champs texte
    requiredFields.forEach(sel => {
        const el = document.querySelector(sel);
        if (el && el.value.trim() === '') {
            showError(el, 'Ce champ est obligatoire.' + el.getAttribute('id'));
            ok = false;
        }
    });

    // 3️⃣ Vérifier la radio access_duration
    const accessDuration = document.querySelector('input[name="person_access_step_one_form[access_duration]"]:checked');
    if (!accessDuration) {
        showError($('#person_access_step_one_form_access_duration'), 'Veuillez sélectionner une durée d\'intervention.');
        ok = false;
    }

    // 4️⃣ Vérifier la radio access_type
    const accessType = document.querySelector('input[name="person_access_step_one_form[access_type]"]:checked');
    if (!accessType) {
        showError($('#person_access_step_one_form_access_type'), 'Veuillez sélectionner un type d\'accès.');
        ok = false;
    }

    // 5️⃣ Si access_type == duplicate => duplicate_reason obligatoire
    if (accessType && accessType.value === '2') {
        const duplicateReason = document.querySelector('input[name="person_access_step_one_form[duplicate_reason]"]:checked');
        if (!duplicateReason) {
            showError($('#person_access_step_one_form_duplicate_reason'), 'Veuillez sélectionner un motif de duplicata.');
            ok = false;
        }
    }

    // 6️⃣ Vérifier au moins un checkbox access_locations
    const accessLocations = document.querySelectorAll('input[name="person_access_step_one_form[access_locations][]"]:checked');
    if (accessLocations.length === 0) {
        showError($('#person_access_step_one_form_access_locations'), 'Veuillez sélectionner au moins un lieu d\'accès.');
        ok = false;
    }

    if (!ok) {
        e.preventDefault();
    }

    return ok;
}

function addSup($el){
    if (!$el.find('sup.micro').length) {
        $el.append(' <sup class="micro">m</sup>');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 1) RADIOS
    radioGroups.forEach(name => {
        const $inputs = $('[name="' + name + '"]');
        if (!$inputs.length) return;
        const $group = $inputs.first().closest('.form-check-group');

        // on skippe si le groupe est masqué
        if (!$group.is(':visible')) return;

        // required sur chaque radio
        $inputs.prop('required', true);

        // sup sur le titre
        const $title = $group.find('.form-check-title').first();
        if ($title.length) addSup($title);
    });

    // 2) CHECKBOXES
    checkboxGroups.forEach(name => {
        const $inputs = $('[name="' + name + '"]');
        if (!$inputs.length) return;
        const $group = $inputs.first().closest('.form-check-group');
        if (!$group.is(':visible')) return;

        // sup sur le titre
        const $title = $group.find('.form-check-title').first();
        if ($title.length) addSup($title);

        // message d'erreur inline
        if (!$title.next('.error-message').length) {
            $('<div class="error-message" style="color:red;display:none;font-size:.85em;">Veuillez cocher au moins une option.</div>')
                .insertAfter($title);
        }
    });

    // 3) CHAMPS SIMPLES
    simpleFields.forEach(name => {
        const $el = $('[name="' + name + '"]');
        if (!$el.length || !$el.is(':visible')) return;
        $el.prop('required', true);
        const id = $el.attr('id');
        if (!id) return;
        const $label = $('label[for="' + id + '"]');
        if ($label.length) addSup($label);
    });

});

// 4) VALIDATION CHECKBOXES À LA SOUMISSION
let form = $('form[name="person_access_step_one_form"]');
let today = new Date().toISOString().slice(0,10);

function showError(el, message) {
    console.log(message);
    let err = el.parentNode.querySelector('.error-message');
    if (!err) {
        err = document.createElement('div');
        err.className = 'error-message';
        err.style.color = 'red';
        err.style.fontSize = '0.9em';
        el.parentNode.appendChild(err);
    }
    err.textContent = message;
    el.classList.add('invalid');
}
function clearErrors() {
    $('.error-message').remove();
    $('.invalid').removeClass('invalid');
}

$('form[name^="person_access_step_one_form"]').on('submit', function(e){
    const btn = e.submitter; // 🚩 le bouton cliqué
    if (btn && btn.hasAttribute('formnovalidate')) {
        return; // ⏭️ Skip le contrôle
    }
    validateRequiredFields(e);
    let valid = true;

    // === VALIDATION CHECKBOX GROUPS ===
    checkboxGroups.forEach(name => {
        const $inputs = $('[name="'+name+'"]');
        const $group = $inputs.first().closest('.form-check-group');
        if (!$group.is(':visible')) return;
        const any = $inputs.is(':checked');
        const $msg = $group.find('.error-message');
        if (!any) {
            valid = false;
            $msg.show();
        } else {
            $msg.hide();
        }
    });
    if (!valid) {
        e.preventDefault();
        const $first = $('.error-message:visible').first();
        $('html,body').animate({scrollTop: $first.offset().top - 20},200);
    }
});

// === VALIDATION AVANCÉE ===
form.submit(function(e){
    clearErrors();
    let ok = true;

    // 1) Téléphone (id="employee_phone")
    let phone = document.getElementById('person_access_step_one_form_security_officer_phone');
    if (!phone.value.match(/^0[67]\d{8}$/)) {
        showError(phone, 'Le numéro de téléphone doit être valide et commencer par 06 ou 07 (10 chiffres).');
        ok = false;
    }

    if (phone.value.length > 10) {
        showError(phone, 'Le numéro de téléphone ne doit pas dépasser 10 chiffres.');
        ok = false;
    }

    // 2) Email (id="employee_email")
    let email = document.getElementById('person_access_step_one_form_security_officer_email');
    if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showError(email, 'Adresse email non valide.');
        ok = false;
    }

    // 7) Date de la demande (id="person_access_step_one_form_request_date")
    let reqDate = document.getElementById('person_access_step_one_form_request_date');
    if (reqDate.value < today) {
        showError(reqDate, 'La date de la demande ne peut pas être antérieure à aujourd\'hui.');
        ok = false;
    }

    let companyName = document.getElementById('person_access_step_one_form_company_name');
    if (companyName.value.trim().length > 80) {
        showError(companyName, 'Raison sociale trop longue.');
        ok = false;
    }

    let siren = document.getElementById('person_access_step_one_form_siren');
    if (!siren.value.match(/^\d{9}$/)) {
        showError(siren, 'SIREN invalide.');
        ok = false;
    }

    let siret = document.getElementById('person_access_step_one_form_siret');
    if (!siret.value.match(/^\d{14}$/) || !siret.value.startsWith(siren.value)) {
        showError(siret, 'SIRET invalide ou ne commence pas par le SIREN.');
        ok = false;
    }

    let naf = document.getElementById('person_access_step_one_form_naf');
    if (!naf.value.match(/^\d{2}[.]?\d{2}[A-Z]$/)) {
        showError(naf, 'Code NAF invalide.');
        ok = false;
    }

    // LIB_08
    let postalInput = document.getElementById('person_access_step_one_form_postal_code');
    let postal = document.getElementById('person_access_step_one_form_postal_code').value.trim();
    let cityInput = document.getElementById('person_access_step_one_form_city');
    let city = document.getElementById('person_access_step_one_form_city').value.trim();
    let lib08 = `${postal} ${city}`.trim();
    if (lib08.length > 80) {
        showError(postalInput, 'LIB_08 (Code Postal + Ville) trop long.');
        showError(cityInput, 'LIB_08 (Code Postal + Ville) trop long.');
        ok = false;
    }
    if (!postal.match(/^\d{5}$/)) {
        showError(postalInput, 'Code postal invalide.');
        ok = false;
    }

    // Comment
    let accessPurposeInput = document.getElementById('person_access_step_one_form_access_purpose');
    let accessPurpose = document.getElementById('person_access_step_one_form_access_purpose').value.trim();
    let comment = `${accessPurpose} ATT TC TEMP`;
    if (comment.length > 250) {
        showError(accessPurposeInput, 'Motif d\'accès trop long.');
        ok = false;
    }

    // Workplace
    let wp = [];
    let accessLocationsInput = document.getElementById('person_access_step_one_form_access_locations');
    $('[name="person_access_step_one_form[access_locations][]"]:checked').each(function(){
        wp.push(this.value);
    });
    let joined = wp.join(',');
    if (joined.length > 80) {
        showError(accessLocationsInput, 'Workplace trop long.');
        ok = false;
    }
    if (!wp.every(loc => ['fos','lavera','hq'].includes(loc))) {
        showError(accessLocationsInput, 'Lieu d\'accès non autorisé.');
        ok = false;
    }

    // Duplicate reason
    let accessDuplicateInput= document.getElementById('person_access_step_one_form_duplicate_reason');
    let type = $('input[name="person_access_step_one_form[access_type]"]:checked').val();
    if (type === '2') {
        const hasDuplicate = $('input[name="person_access_step_one_form[duplicate_reason]"]:checked').length > 0;
        if (!hasDuplicate) {
            showError(accessDuplicateInput, 'Motif duplicata requis.');
            ok = false;
        }
    }

    if (!ok) {
        e.preventDefault();
        // scroll sur la première erreur visible
        let first = $('.invalid');
        if (first) first.scrollIntoView({behavior:'smooth', block:'center'});
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Grab all the relevant controls
    const durationRadios = Array.from(document.querySelectorAll('input[name="person_access_step_one_form[access_duration]"]'));
    const typeRadios     = Array.from(document.querySelectorAll('input[name="person_access_step_one_form[access_type]"]'));
    const duplicateGroup = document.getElementById('person_access_step_one_form_duplicate_reason');

    // Define which type-values are allowed per duration
    const allowedByDuration = {
        permanent:   ['0','1','2','3'],  // première édition, renouvellement, duplicata, changement d'entreprise
        temporaire: ['0','2']            // première édition, duplicata
    };

    // Show/hide & (de)select type options based on duration
    function updateTypeOptions() {
        const selected = durationRadios.find(r => r.checked);
        if (!selected) return;
        const selectedDuration = durationRadios.find(r => r.checked).value;
        const allowed = allowedByDuration[selectedDuration];

        // Iterate each type-radio, hide or show
        typeRadios.forEach(radio => {
            const wrapper = radio.closest('.form-check');
            if (allowed.includes(radio.value)) {
                wrapper.style.display = '';
                radio.disabled = false;
            } else {
                wrapper.style.display = 'none';
                radio.disabled = true;
                if (radio.checked) {
                    radio.checked = false;
                }
            }
        });

        // If no type is selected (because the previous one was just hidden),
        // pick the first allowed option
        if (!typeRadios.some(r => r.checked)) {
            const firstAllowed = typeRadios.find(r => allowed.includes(r.value));
            if (firstAllowed) {
                firstAllowed.checked = true;
            }
        }
    }

    // Show/hide the "Motif du duplicata" block
    function updateDuplicateReason() {
        const selected = typeRadios.find(r => r.checked);
        if (!selected) return;
        const selectedType = typeRadios.find(r => r.checked).value;
        const isDuplicate = (selectedType === '2');

        Array.from(duplicateGroup.querySelectorAll('input')).forEach(inp => {
            if (isDuplicate) {
                inp.disabled = false;
                inp.required = true;    // only make it required when visible
            } else {
                inp.checked = false;
                inp.disabled = true;    // disabled fields are ignored by validation
                inp.required = false;   // just in case
            }
        });

        duplicateGroup.style.display = isDuplicate ? '' : 'none';
    }

    // Wire up event listeners
    durationRadios.forEach(r => r.addEventListener('change', () => {
        updateTypeOptions();
        updateDuplicateReason();
    }));
    typeRadios.forEach(r => r.addEventListener('change', updateDuplicateReason));

    // Initialize on page load
    updateTypeOptions();
    updateDuplicateReason();

    // Cherche chaque champ une fois seulement
    const siretInput = document.querySelector('#person_access_step_one_form_siret');
    const nameInput = document.querySelector('#person_access_step_one_form_security_officer_name');
    const positionInput = document.querySelector('#person_access_step_one_form_security_officer_position');
    const emailInput = document.querySelector('#person_access_step_one_form_security_officer_email');
    const phoneInput = document.querySelector('#person_access_step_one_form_security_officer_phone');

    const alternateNameInput = document.querySelector('#person_access_step_one_form_alternate_referent_name');
    const alternatePositionInput = document.querySelector('#person_access_step_one_form_alternate_referent_position');
    const alternateEmailInput = document.querySelector('#person_access_step_one_form_alternate_referent_email');
    const alternatePhoneInput = document.querySelector('#person_access_step_one_form_alternate_referent_phone');

    if (!siretInput) {
        console.warn('Champ SIRET introuvable (#company_siret)');
        return;
    }
    if (!nameInput || !positionInput || !emailInput || !phoneInput) {
        console.warn('Un ou plusieurs champs Référent Sûreté introuvables.');
        return;
    }

    /**
     * Fonction pour appeler l’API proprement
     */
    function fetchReferentData(siret) {
        if (!siret || siret.length < 8) {
            console.info('SIRET vide ou trop court.');
            return;
        }

        const url = `/api/referent/${encodeURIComponent(siret)}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    console.warn(`API erreur HTTP ${response.status}`);
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;

                if (data.error) {
                    console.info(`API répond : ${data.error}`);
                    return;
                }

                // Remplir chaque champ de façon sûre
                nameInput.value = data.responsable.name ?? '';
                positionInput.value = data.responsable.position ?? '';
                emailInput.value = data.responsable.email ?? '';
                phoneInput.value = data.responsable.phone ?? '';

                alternateNameInput.value = data.suppleant.name ?? '';
                alternatePositionInput.value = data.suppleant.position ?? '';
                alternateEmailInput.value = data.suppleant.email ?? '';
                phoneInput.value = data.suppleant.phone ?? '';
            })
            .catch(err => {
                console.error('Erreur réseau ou JSON :', err);
            });
    }

    /**
     * Déclenche sur changement ou perte de focus
     */
    const handler = function () {
        const siret = siretInput.value.trim();
        fetchReferentData(siret);
    };

    siretInput.addEventListener('change', handler);
    siretInput.addEventListener('blur', handler);

    if (siretInput.value) {
        fetchReferentData(siretInput.value);
    }
});
