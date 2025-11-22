// — 1) CONFIGURATION —
// Noms exacts de vos champs Radio (le "name" sans [] pour les radios)
const radioGroups = [
    'vehicle_access_step_two_form[gender]',
    'vehicle_access_step_two_form[resident_situation]',
    'vehicle_access_step_two_form[contract_type]'
];

// Tous les autres MicroCésame “simples” (texte, date, textarea, autocomplete…)
const simpleFields = [
    'vehicle_access_step_two_form[employee_first_name]',
    'vehicle_access_step_two_form[employee_last_name]',
    'vehicle_access_step_two_form[matricule]',
    'vehicle_access_step_two_form[numero_cni]',
    'vehicle_access_step_two_form[maiden_name]',
    'vehicle_access_step_two_form[employee_birthdate]',
    'vehicle_access_step_two_form[employee_birthplace]',
    'vehicle_access_step_two_form[employee_birth_postale_code]',
    'vehicle_access_step_two_form[employee_birth_district]',
    'vehicle_access_step_two_form[nationality]',
    'vehicle_access_step_two_form[social_security_number]',
    'vehicle_access_step_two_form[employee_email]',
    'vehicle_access_step_two_form[employee_phone]',
    'vehicle_access_step_two_form[section_employee_address]',
    'vehicle_access_step_two_form[postal_code]',
    'vehicle_access_step_two_form[city]',
    'vehicle_access_step_two_form[country]',
    'vehicle_access_step_two_form[father_name]',
    'vehicle_access_step_two_form[father_first_name]',
    'vehicle_access_step_two_form[mother_maiden_name]',
    'vehicle_access_step_two_form[mother_first_name]',
    'vehicle_access_step_two_form[employee_function]',
    'vehicle_access_step_two_form[employment_date]',
    'vehicle_access_step_two_form[contract_end_date]'
];

// utilitaire pour injecter le <sup class="micro">m</sup> s'il n'existe pas
function injectSup($container){
    if (!$container.find('sup.micro').length){
        $container.append(' <sup class="micro">m</sup>');
    }
}

document.addEventListener('DOMContentLoaded', function() {

    // — 2) TRAITEMENT DES RADIOS —
    radioGroups.forEach(name => {
        const $inputs = $('[name="' + name + '"]');
        if (!$inputs.length) return;
        // skip si le groupe caché
        const $group = $inputs.first().closest('.form-check-group');
        if ($group.length && !$group.is(':visible')) return;
        // required sur chaque option
        $inputs.prop('required', true);
        // exposant sur le titre de groupe
        if ($group.length) {
            const $title = $group.find('.form-check-title').first();
            if ($title.length) injectSup($title);
        }
    });

    // — 4) TRAITEMENT DES CHAMPS SIMPLES —
    simpleFields.forEach(name => {
        const $el = $('[name="' + name + '"]');
        if (!$el.length || !$el.is(':visible')) return;
        $el.prop('required', true);
        const id = $el.attr('id');
        if (!id) return;
        const $label = $('label[for="' + id + '"]');
        if ($label.length) injectSup($label);
    });
});

let form = document.getElementById('vehicle_access_step_two_form');
let today = new Date().toISOString().slice(0,10);

// Affiche un message d'erreur sous l'élément
function showError(el, msg) {
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

// Nettoie toutes les erreurs
function clearErrors() {
    form.querySelectorAll('.error-message').forEach(e => e.remove());
    form.querySelectorAll('.invalid').forEach(i => i.classList.remove('invalid'));
}

document.getElementsByTagName('form')[0].addEventListener('submit', function(e){
    clearErrors();
    let valid = true;

    // 1) Genre obligatoire
    let genders = document.getElementsByName('vehicle_access_step_two_form[gender]');
    if (![...genders].some(r => r.checked)) {
        showError(genders[0].closest('.form-check-group'), 'Le genre est obligatoire.');
        valid = false;
    }

    // 2) Date de naissance obligatoire
    let bdate = document.getElementById('vehicle_access_step_two_form_employee_birthdate');
    if (!bdate.value) {
        showError(bdate, 'Date de naissance requise.');
        valid = false;
    } else if (bdate.value > today) {
        showError(bdate, 'Date de naissance invalide.');
        valid = false;
    }

    // 3) Lieu de naissance + arrondissement
    let bplace = document.getElementById('vehicle_access_step_two_form_employee_birthplace');
    let bdistrict = document.getElementById('vehicle_access_step_two_form_employee_birth_district');
    if (!bplace.value.trim()) {
        showError(bplace, 'Lieu de naissance obligatoire.');
        valid = false;
    } else {
        let town = bplace.value.trim().toLowerCase();
        if (['paris','lyon','marseille'].includes(town) && !bdistrict.value.trim()) {
            showError(bdistrict, 'Arrondissement obligatoire pour Paris, Lyon ou Marseille.');
            valid = false;
        }
    }

    // 4) Email perso
    let email = document.getElementById('vehicle_access_step_two_form_employee_email');
    if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showError(email, 'Adresse email non valide.');
        valid = false;
    }

    // 5) Téléphone perso (06/07 et 10 chiffres)
    let phone = document.getElementById('vehicle_access_step_two_form_employee_phone');
    if (!phone.value.match(/^0[67]\d{8}$/)) {
        showError(phone, 'Le numéro doit commencer par 06 ou 07 et comporter 10 chiffres.');
        valid = false;
    }

    // 6) Numéro de sécurité sociale (15 chiffres)
    let ssn = document.getElementById('vehicle_access_step_two_form_social_security_number');
    if (!ssn.value.match(/^\d{15}$/)) {
        showError(ssn, 'Numéro de sécurité sociale invalide (15 chiffres attendus).');
        valid = false;
    }

    // 7) Contrat : si CDD, date de fin obligatoire et > date d'embauche
    let ctype = document.querySelector('input[name="vehicle_access_step_two_form[contract_type]"]:checked');
    let start = document.getElementById('vehicle_access_step_two_form_employment_date');
    let end   = document.getElementById('vehicle_access_step_two_form_contract_end_date');
    if (!start.value) {
        showError(start, 'Date d\'embauche obligatoire.');
        valid = false;
    } else if (start.value > today) {
        showError(start, 'Date d\'embauche ne peut pas être dans le futur.');
        valid = false;
    }

    if (ctype && ctype.value === 'cdd') {
        if (!end.value) {
            showError(end, 'Veuillez indiquer la date de fin du contrat.');
            valid = false;
        } else if (end.value <= start.value) {
            showError(end, 'La fin du contrat doit être après la date d\'embauche.');
            valid = false;
        }
    }
    // CDI → end date optionnelle

    if (!valid) {
        e.preventDefault();
        let firstErr = form.querySelector('.invalid');
        if (firstErr) firstErr.scrollIntoView({behavior:'smooth', block:'center'});
    }
});