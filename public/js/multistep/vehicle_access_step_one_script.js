// CONFIGURATION
const radioGroups = [
    'vehicle_access_step_one_form[access_type]',
    'vehicle_access_step_one_form[duplicate_reason]'
];
const simpleFields = [
    'vehicle_access_step_one_form[request_date]',
    'vehicle_access_step_one_form[company_name]',
    'vehicle_access_step_one_form[address]',
    'vehicle_access_step_one_form[postal_code]',
    'vehicle_access_step_one_form[city]',
    'vehicle_access_step_one_form[country]',
    'vehicle_access_step_one_form[siren]',
    'vehicle_access_step_one_form[naf]',
    'vehicle_access_step_one_form[siret]',
    'vehicle_access_step_one_form[vat_number]',
    'vehicle_access_step_one_form[security_officer_name]',
    'vehicle_access_step_one_form[security_officer_position]',
    'vehicle_access_step_one_form[security_officer_email]',
    'vehicle_access_step_one_form[security_officer_phone]'
];

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

let form = $('form[name="vehicle_access_step_one_form"]');
let today = new Date().toISOString().slice(0,10);

// crée ou met à jour un message d'erreur sous un champ
function showError(el, message) {
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

// supprime tous les messages d'erreur
function clearErrors() {
    // 1) on supprime uniquement les boîtes d’erreur
    $('.error-message')
        .each(err => err.remove());

    // 2) on enlève la classe “invalid” sans toucher aux inputs
    $('.invalid')
        .each(el => el.classList.remove('invalid'));
}

form.submit(function(e){
    clearErrors();
    let ok = true;

    // 1) Téléphone (id="employee_phone")
    let phone = document.getElementById('vehicle_access_step_one_form_security_officer_phone');
    if (!phone.value.match(/^0[67]\d{8}$/)) {
        showError(phone, 'Le numéro de téléphone doit être valide et commencer par 06 ou 07.');
        ok = false;
    }

    // 2) Email (id="employee_email")
    let email = document.getElementById('vehicle_access_step_one_form_security_officer_email');
    if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showError(email, 'Adresse email non valide.');
        ok = false;
    }

    // 7) Date de la demande (id="vehicle_access_step_one_form_request_date")
    let reqDate = document.getElementById('vehicle_access_step_one_form_request_date');
    if (reqDate.value < today) {
        showError(reqDate, 'La date de la demande ne peut pas être antérieure à aujourd\'hui.');
        ok = false;
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
    const typeRadios     = Array.from(document.querySelectorAll('input[name="vehicle_access_step_one_form[access_type]"]'));
    const duplicateGroup = document.getElementById('vehicle_access_step_one_form_duplicate_reason');

    // Define which type-values are allowed per duration
    const allowedByDuration = {
        permanent:   ['0','1','2'],  // première édition, renouvellement, duplicata
        temporaire: ['0','2']            // première édition, duplicata
    };

    // Show/hide the "Motif du duplicata" block
    function updateDuplicateReason() {
        if (typeRadios.length === 0) return;
        if (!typeRadios.find(r => r.checked)) return;
        const selectedType = typeRadios.find(r => r.checked).value;
        const isDuplicate = (selectedType === 'duplicate');

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
    typeRadios.forEach(r => r.addEventListener('change', updateDuplicateReason));
    updateDuplicateReason();
});