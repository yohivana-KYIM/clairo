// Helpers
const isValidEmail = email => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
const isValidCP = cp => /^\d{5}$/.test(cp);
const isValidPhone = phone => /^0[67]\d{8}$/.test(phone);
const isValidISODate = date => /^\d{4}-\d{2}-\d{2}$/.test(date);


// — 1) CONFIGURATION —
// Noms exacts de vos champs Radio (le "name" sans [] pour les radios)
const radioGroups = [
    'person_access_step_two_form[gender]',
    'person_access_step_two_form[resident_situation]',
    'person_access_step_two_form[contract_type]',
    'person_access_step_two_form[cni_type]'
];

// Tous les autres MicroCésame “simples” (texte, date, textarea, autocomplete…)
const simpleFields = [
    'person_access_step_two_form[employee_first_name]',
    'person_access_step_two_form[employee_last_name]',
    'person_access_step_two_form[matricule]',
    'person_access_step_two_form[numero_cni]',
    //'person_access_step_two_form[maiden_name]',
    'person_access_step_two_form[employee_birthdate]',
    'person_access_step_two_form[employee_birthplace]',
    'person_access_step_two_form[employee_birth_postale_code]',
    'person_access_step_two_form[employee_birth_district]',
    'person_access_step_two_form[nationality]',
    'person_access_step_two_form[social_security_number]',
    'person_access_step_two_form[employee_email]',
    'person_access_step_two_form[employee_phone]',
    'person_access_step_two_form[section_employee_address]',
    'person_access_step_two_form[postal_code]',
    'person_access_step_two_form[city]',
    'person_access_step_two_form[country]',
    'person_access_step_two_form[father_name]',
    'person_access_step_two_form[father_first_name]',
    'person_access_step_two_form[mother_maiden_name]',
    'person_access_step_two_form[mother_first_name]',
    'person_access_step_two_form[employee_function]',
    'person_access_step_two_form[employment_date]',
    'person_access_step_two_form[contract_end_date]'
];

const textChecks = [
    { id: '#person_access_step_two_form_matricule', max: 80 },
    { id: '#person_access_step_two_form_numero_cni', max: 80 },
    { id: '#person_access_step_two_form_employee_first_name', max: 80 },
    { id: '#person_access_step_two_form_employee_last_name', max: 80 },
    { id: '#person_access_step_two_form_employee_birthdate'},
    { id: '#person_access_step_two_form_employee_birthplace', max: 80 },
    { id: '#person_access_step_two_form_nationality', max: 80 },
    { id: '#person_access_step_two_form_employee_email', max: 80, regex: isValidEmail },
    { id: '#person_access_step_two_form_employee_phone', max: 80, regex: isValidPhone },
    { id: '#person_access_step_two_form_section_employee_address', max: 80 },
    { id: '#person_access_step_two_form_postal_code', max: 5, regex: isValidCP },
    { id: '#person_access_step_two_form_city', max: 80 },
    { id: '#person_access_step_two_form_country', max: 80 },
    { id: '#person_access_step_two_form_father_name', max: 80 },
    { id: '#person_access_step_two_form_father_first_name', max: 80 },
    { id: '#person_access_step_two_form_mother_first_name', max: 80 },
    { id: '#person_access_step_two_form_employee_function', max: 80 },
    { id: '#person_access_step_two_form_pincode', max: 7 },
    // { id: '#person_access_step_two_form_social_security_number', max: 15 }
];

document.addEventListener('DOMContentLoaded', function() {
    textChecks.forEach(sel => {
        const input = document.querySelector(sel.id);
        if (input) {
            const label = document.querySelector(`label[for="${input.id}"]`);
            if (label && !label.innerHTML.includes('*')) {
                label.innerHTML += ' <sup>*</sup>';
            }
        }
    });
});

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
        const id = $el.attr('id');
        if (!id) return;
        const $label = $('label[for="' + id + '"]');
        if ($label.length) injectSup($label);
    });
});

let form = document.getElementById('person_access_step_two_form');
let today = new Date().toISOString().slice(0,10);

// Show custom error (same as before)
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
    el.classList.add('is-invalid'); // Bootstrap class
    el.classList.add('invalid');    // custom fallback
}

// Clear error for one input (robust)
function clearError(input) {
    // Remove classes
    input.classList.remove('is-invalid');
    input.classList.remove('invalid');

    // Remove custom error-message if present
    const box = input.parentNode;
    const customErr = box.querySelector('.error-message');
    if (customErr) {
        customErr.remove();
    }

    // Remove bootstrap invalid-feedback if next sibling
    const bootstrapErr = input.nextElementSibling;
    if (bootstrapErr && bootstrapErr.classList.contains('invalid-feedback')) {
        bootstrapErr.remove();
    }

    // Reset HTML5 native validation bubble (if browser still marks it invalid)
    input.setCustomValidity('');
}


// 🔍 Vérifie SSN complet (format + clé)
function isValidFrenchSSN(ssn) {
    // Nationalité → si étrangère : SSN facultatif et contrôle assoupli
    const natEl  = document.getElementById('person_access_step_two_form_nationality');
    const natRaw = (natEl?.value || '').trim();
    const nat    = natRaw.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

    // Tous les libellés acceptés comme "français"
    const frenchVariants = [
        'fr', 'fra', 'france', 'francais', 'francaise', 'french',
        // DOM / TOM / COM
        'guadeloupe', 'gp',
        'guyane francaise', 'gf',
        'martinique', 'mq',
        'reunion', 're',
        'mayotte', 'yt',
        'nouvelle caledonie', 'nc',
        'polynesie francaise', 'pf',
        'wallis et futuna', 'wf',
        'saint pierre et miquelon', 'pm',
        'saint barthelemy', 'bl',
        'saint martin', 'mf',
        'terres australes et antarctiques francaises', 'tf'
    ];

    const isFrench = frenchVariants.includes(nat);


    const v = (ssn || '').trim();

    if (!isFrench) {
        // Étranger : champ non obligatoire ; si renseigné, contrôle léger (9–15 alpha-num)
        if (v === '') return true;
        return /^[0-9A-Za-z]{9,15}$/.test(v);
    }

    // Français : contrôle strict NIR (15 chiffres clé)
    const clean = v.replace(/\s+/g, '').replace(/-/g, '');
    if (!/^\d{15}$/.test(clean)) return false;

    let body = clean.substring(0, 13);
    const key = parseInt(clean.substring(13, 15), 10);
    if (!/^[12]\d{12}$/.test(body)) return false;

    // Gérer Corse si le département est 2A ou 2B (pas de lettres ici mais utile si alphanum à l'origine)
    body = body.replace(/2A/, '19').replace(/2B/, '18');

    const number = parseInt(body, 10);
    const computedKey = 97 - (number % 97);

    return key === computedKey;
}

// 🔍 Vérifie CNI / Passeport / Titre séjour
function isValidIdNumber(value) {
    return true
}

// Nettoie toutes les erreurs
function clearErrors() {
    const form = document.querySelector('form[name="person_access_step_two_form"]');
    form.querySelectorAll('.error-message').forEach(e => e.remove());
    form.querySelectorAll('.invalid').forEach(i => i.classList.remove('invalid'));
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementsByTagName('form')[0].addEventListener('submit', function(e){
        const btn = e.submitter; // 🚩 le bouton cliqué
        if (btn && btn.hasAttribute('formnovalidate')) {
            return; // ⏭️ Skip le contrôle
        }
        clearErrors();
        let valid = true;

        // 1) Genre obligatoire
        let genders = document.getElementsByName('person_access_step_two_form[gender]');
        if (![...genders].some(r => r.checked)) {
            showError(genders[0].closest('.form-check-group'), 'Le genre est obligatoire.');
            valid = false;
        }

        // 1) Genre obligatoire
        let cni_types = document.getElementsByName('person_access_step_two_form[cni_type]');
        if (![...cni_types].some(r => r.checked)) {
            showError(cni_types[0].closest('.form-check-group'), 'Le type de CNI est obligatoire.');
            valid = false;
        }


        // 2) Date de naissance obligatoire
        let bdate = document.getElementById('person_access_step_two_form_employee_birthdate');
        if (!bdate.value) {
            showError(bdate, 'Date de naissance requise.');
            valid = false;
        } else if (bdate.value > today) {
            showError(bdate, 'Date de naissance invalide.');
            valid = false;
        }

        // 3) Lieu de naissance + arrondissement
        let bplace = document.getElementById('person_access_step_two_form_employee_birthplace');
        let bdistrict = document.getElementById('person_access_step_two_form_employee_birth_district');
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
        let email = document.getElementById('person_access_step_two_form_employee_email');
        if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            showError(email, 'Adresse email non valide.');
            valid = false;
        }

        // 5) Téléphone perso (06/07 et 10 chiffres)
        let phone = document.getElementById('person_access_step_two_form_employee_phone');
        if (!phone.value.match(/^0[67]\d{8}$/)) {
            showError(phone, 'Le numéro doit commencer par 06 ou 07 et comporter 10 chiffres.');
            valid = false;
        }

        // 6) Numéro de sécurité sociale (15 chiffres)
        let ssn = document.getElementById('person_access_step_two_form_social_security_number');
        if (!ssn.value.match(/^\d{15}$/)) {
            showError(ssn, 'Numéro de sécurité sociale invalide (15 chiffres attendus).');
            valid = false;
        }

        // 7) Contrat : si CDD, date de fin obligatoire et > date d'embauche
        let ctype = document.querySelector('input[name="person_access_step_two_form[contract_type]"]:checked');
        let start = document.getElementById('person_access_step_two_form_employment_date');
        let end   = document.getElementById('person_access_step_two_form_contract_end_date');
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
            } else {
                const today  = new Date().toISOString().split('T')[0];
                const startD = new Date(start.value);
                const endD   = new Date(end.value);
                const todayD = new Date(today);

                if (endD <= startD) {
                    showError(end, 'La fin du contrat doit être après la date d\'embauche.');
                    valid = false;
                } else if (endD < todayD) {
                    showError(end, 'La date de fin ne peut pas être antérieure à aujourd\'hui.');
                    valid = false;
                }
            }
        }

        // CDI → end date optionnelle

        if (!valid) {
            e.preventDefault();
            if (form) {
                let firstErr = form.querySelector('.invalid');
                if (firstErr) firstErr.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        }
    });

    // --- Mapping CP vers arrondissement pour Paris, Lyon, Marseille
    const arrondissementMap = {
        paris: {
            '75001': '1ᵉʳ arrondissement',
            '75002': '2ᵉ arrondissement',
            '75003': '3ᵉ arrondissement',
            '75004': '4ᵉ arrondissement',
            '75005': '5ᵉ arrondissement',
            '75006': '6ᵉ arrondissement',
            '75007': '7ᵉ arrondissement',
            '75008': '8ᵉ arrondissement',
            '75009': '9ᵉ arrondissement',
            '75010': '10ᵉ arrondissement',
            '75011': '11ᵉ arrondissement',
            '75012': '12ᵉ arrondissement',
            '75013': '13ᵉ arrondissement',
            '75014': '14ᵉ arrondissement',
            '75015': '15ᵉ arrondissement',
            '75016': '16ᵉ arrondissement',
            '75017': '17ᵉ arrondissement',
            '75018': '18ᵉ arrondissement',
            '75019': '19ᵉ arrondissement',
            '75020': '20ᵉ arrondissement'
        },
        lyon: {
            '69001': '1ᵉʳ arrondissement',
            '69002': '2ᵉ arrondissement',
            '69003': '3ᵉ arrondissement',
            '69004': '4ᵉ arrondissement',
            '69005': '5ᵉ arrondissement',
            '69006': '6ᵉ arrondissement',
            '69007': '7ᵉ arrondissement',
            '69008': '8ᵉ arrondissement',
            '69009': '9ᵉ arrondissement'
        },
        marseille: {
            '13001': '1ᵉʳ arrondissement',
            '13002': '2ᵉ arrondissement',
            '13003': '3ᵉ arrondissement',
            '13004': '4ᵉ arrondissement',
            '13005': '5ᵉ arrondissement',
            '13006': '6ᵉ arrondissement',
            '13007': '7ᵉ arrondissement',
            '13008': '8ᵉ arrondissement',
            '13009': '9ᵉ arrondissement',
            '13010': '10ᵉ arrondissement',
            '13011': '11ᵉ arrondissement',
            '13012': '12ᵉ arrondissement',
            '13013': '13ᵉ arrondissement',
            '13014': '14ᵉ arrondissement',
            '13015': '15ᵉ arrondissement',
            '13016': '16ᵉ arrondissement'
        }
    };

    function setupPostalAutocomplete(cpSelector, citySelector, context = '') {
        const cpField = document.getElementById(cpSelector);
        const cityField = document.getElementById(citySelector);

        if (!cpField || !cityField) return;

        const container = cityField.parentNode;
        let dropdown;

        cpField.addEventListener('blur', () => {
            const cp = cpField.value.trim();
            if (cp.length !== 5 || !/^\d{5}$/.test(cp)) return;

            fetch(`https://geo.api.gouv.fr/communes?codePostal=${cp}&fields=nom&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (!data || data.length === 0) return;

                    if (data.length === 1) {
                        cityField.value = data[0].nom;
                        removeDropdown();
                    } else {
                        showDropdown(data.map(item => item.nom), cityField);
                    }

                    // Auto-remplissage de l'arrondissement si applicable
                    const ville = data[0].nom.toLowerCase();
                    const arrInput = document.getElementById('person_access_step_two_form_employee_birth_district');

                    if (context === 'birth' && arrInput && arrondissementMap[ville] && arrondissementMap[ville][cp]) {
                        arrInput.value = arrondissementMap[ville][cp];
                        arrInput.readOnly = true;
                        arrInput.required = true;
                    } else if (context === 'birth' && arrInput) {
                        arrInput.value = '';
                        arrInput.readOnly = true;
                        arrInput.required = false;
                    }

                })
                .catch(console.error);
        });

        function showDropdown(options, targetField) {
            removeDropdown();

            dropdown = document.createElement('ul');
            dropdown.className = 'postal-dropdown';
            dropdown.style.position = 'absolute';
            dropdown.style.zIndex = '1000';
            dropdown.style.background = '#fff';
            dropdown.style.border = '1px solid #ccc';
            dropdown.style.marginTop = '2px';
            dropdown.style.padding = '0';
            dropdown.style.listStyle = 'none';
            dropdown.style.width = targetField.offsetWidth + 'px';
            dropdown.style.maxHeight = '150px';
            dropdown.style.overflowY = 'auto';

            options.forEach(option => {
                const li = document.createElement('li');
                li.textContent = option;
                li.style.padding = '6px';
                li.style.cursor = 'pointer';
                li.addEventListener('mousedown', () => {
                    targetField.value = option;
                    removeDropdown();
                });
                dropdown.appendChild(li);
            });

            container.style.position = 'relative';
            container.appendChild(dropdown);
        }

        function removeDropdown() {
            if (dropdown && dropdown.parentNode) {
                dropdown.parentNode.removeChild(dropdown);
                dropdown = null;
            }
        }

        document.addEventListener('click', function (e) {
            if (dropdown && !dropdown.contains(e.target)) {
                removeDropdown();
            }
        });
    }

    // Liaisons principales
    setupPostalAutocomplete('person_access_step_two_form_postal_code', 'person_access_step_two_form_city');
    setupPostalAutocomplete('person_access_step_two_form_employee_birth_postale_code', 'person_access_step_two_form_employee_birthplace', 'birth');

    // Ville de naissance → gestion du champ arrondissement
    const cityInput = document.getElementById('person_access_step_two_form_employee_birthplace');
    const arrInput = document.getElementById('person_access_step_two_form_employee_birth_district');

    if (cityInput && arrInput) {
        cityInput.addEventListener('blur', () => {
            const ville = cityInput.value.trim().toLowerCase();
            if (['paris', 'lyon', 'marseille'].includes(ville)) {
                arrInput.readOnly = false;
                arrInput.required = true;
                if (!arrInput.value.trim()) {
                    arrInput.value = '1ᵉʳ arrondissement';
                }
            } else {
                arrInput.value = '';
                arrInput.readOnly = true;
                arrInput.required = false;
                arrInput.placeholder = 'Non applicable';
            }
        });
    }
});

function validateStepTwo(e) {
    let ok = true;

    const birthdate = document.querySelector('#person_access_step_two_form_employee_birthdate');
    const employmentDate = document.querySelector('#person_access_step_two_form_employment_date');
    const contractEnd = document.querySelector('#person_access_step_two_form_contract_end_date');
    const gender = document.querySelector('input[name="person_access_step_two_form[gender]"]:checked');
    const cni_type = document.querySelector('input[name="person_access_step_two_form[cni_type]"]:checked');
    const contractType = document.querySelector('input[name="person_access_step_two_form[contract_type]"]:checked');
    const resident = document.querySelector('input[name="person_access_step_two_form[resident_situation]"]:checked');
    const fatherName = document.querySelector('#person_access_step_two_form_father_name');
    const motherMaiden = document.querySelector('#person_access_step_two_form_mother_maiden_name');
    const postalCode = document.querySelector('#person_access_step_two_form_postal_code');

    clearError(birthdate);
    clearError(employmentDate);
    clearError(contractEnd);
    clearError(gender);
    clearError(cni_type);
    clearError(contractType);
    clearError(resident);
    clearError(fatherName);
    clearError(motherMaiden);
    clearError(postalCode);

    textChecks.forEach(field => {
        const el = document.querySelector(field.id);
        if (!el) return;
        clearError(el);
        if (el.value.trim() === '') {
            showError(el, 'Champ requis.');
            ok = false;
        } else if (el.value.trim().length > field.max) {
            showError(el, `Max ${field.max} caractères.`);
            ok = false;
        } else if (field.regex && !field.regex(el.value.trim())) {
            showError(el, 'Format invalide.');
            ok = false;
        }
    });

    if (!birthdate.value || !isValidISODate(birthdate.value)) {
        showError(birthdate, 'Date ISO requise.');
        ok = false;
    }

    if (!employmentDate.value || !isValidISODate(employmentDate.value)) {
        showError(employmentDate, 'Date ISO requise.');
        ok = false;
    }

    if (contractEnd.value) {
        if (new Date(contractEnd.value) <= new Date(employmentDate.value)) {
            showError(contractEnd, 'La date de fin doit être > date embauche.');
            ok = false;
        }
    }

    if (!gender) {
        const container = document.querySelector('#person_access_step_two_form_gender');
        showError(container, 'Veuillez sélectionner un genre.');
        ok = false;
    }

    if (!cni_type) {
        const container = document.querySelector('#person_access_step_two_form_cni_type');
        showError(container, 'Veuillez sélectionner un type de CNI.');
        ok = false;
    }

    if (!contractType) {
        const container = document.querySelector('#person_access_step_two_form_contract_type');
        showError(container, 'Veuillez sélectionner un type de contrat.');
        ok = false;
    }

    if (!resident) {
        const container = document.querySelector('#person_access_step_two_form_resident_situation');
        showError(container, 'Veuillez sélectionner votre situation de logement.');
        ok = false;
    }

    // ✅ Champs CONCAT :
    // LIB_09 = employment_date + contract_type
    if (employmentDate.value && contractType) {
        const concat09 = `${employmentDate.value.trim()} ${contractType.value.trim()}`;
        if (concat09.length > 80) {
            showError(employmentDate, 'LIB_09 dépasse 80 caractères.');
            ok = false;
        }
    }

    // LIB_11 = father_name + father_first_name
    const fatherFirstName = document.querySelector('#person_access_step_two_form_father_first_name');
    if (fatherName.value && fatherFirstName.value) {
        const concat11 = `${fatherName.value.trim()} ${fatherFirstName.value.trim()}`;
        if (concat11.length > 80) {
            showError(fatherName, 'LIB_11 dépasse 80 caractères.');
            ok = false;
        }
    }

    // LIB_12 = mother_maiden_name + mother_first_name
    const motherFirstName = document.querySelector('#person_access_step_two_form_mother_first_name');
    if (motherMaiden.value && motherFirstName.value) {
        const concat12 = `${motherMaiden.value.trim()} ${motherFirstName.value.trim()}`;
        if (concat12.length > 80) {
            showError(motherMaiden, 'LIB_12 dépasse 80 caractères.');
            ok = false;
        }
    }

    // LIB_08 = postal_code + city
    const city = document.querySelector('#person_access_step_two_form_city');
    if (postalCode.value && city.value) {
        const concat08 = `${postalCode.value.trim()} ${city.value.trim()}`;
        if (concat08.length > 80) {
            showError(postalCode, 'LIB_08 dépasse 80 caractères.');
            ok = false;
        }
    }

    if (!ok) e.preventDefault();
    return ok;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('button.btn-next').addEventListener('click', validateStepTwo);

    const form = document.querySelector('form[name="person_access_step_two_form"]');

    form.addEventListener('submit', function(e) {
        const btn = e.submitter; // 🚩 le bouton cliqué
        if (btn && btn.hasAttribute('formnovalidate')) {
            return; // ⏭️ Skip le contrôle
        }
        let ok = true;

        const ssnInput = document.getElementById('person_access_step_two_form_social_security_number');
        const birthdateInput = document.getElementById('person_access_step_two_form_employee_birthdate');
        const genderInputs = document.querySelectorAll('input[name="person_access_step_two_form[gender]"]:checked');
        const cniTypeInputs = document.querySelectorAll('input[name="person_access_step_two_form[cni_type]"]:checked');
        const idNumberInput = document.getElementById('person_access_step_two_form_numero_cni');

        // Nettoyage visuel
        clearError(ssnInput);
        clearError(birthdateInput);
        clearError(idNumberInput);

        const ssn = ssnInput.value.trim();
        const birthdate = birthdateInput.value.trim();
        const gender = genderInputs.length > 0 ? genderInputs[0].value : null;
        const cni_type = cniTypeInputs.length > 0 ? cniTypeInputs[0].value : null;
        const idNumber = idNumberInput.value.trim();

        // Vérifier format date naissance
        if (!/^\d{4}-\d{2}-\d{2}$/.test(birthdate)) {
            showError(birthdateInput, 'La date de naissance est invalide.');
            ok = false;
        }

        if (ok) {
            // Vérifier cohérence année
            const birthYear = birthdate.substring(2, 4);
            const ssnYear = ssn.substring(1, 3);

            if (birthYear !== ssnYear) {
                showError(ssnInput, `L'année de naissance (${birthYear}) ne correspond pas à l'année du numéro de sécurité sociale (${ssnYear}).`);
                ok = false;
            }

            // Nationalité → si étrangère : SSN facultatif et contrôle assoupli
            const natEl  = document.getElementById('person_access_step_two_form_nationality');
            const natRaw = (natEl?.value || '').trim();
            const nat    = natRaw.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

            // Tous les libellés acceptés comme "français"
            const frenchVariants = [
                'fr', 'fra', 'france', 'francais', 'francaise', 'french',
                // DOM / TOM / COM
                'guadeloupe', 'gp',
                'guyane francaise', 'gf',
                'martinique', 'mq',
                'reunion', 're',
                'mayotte', 'yt',
                'nouvelle caledonie', 'nc',
                'polynesie francaise', 'pf',
                'wallis et futuna', 'wf',
                'saint pierre et miquelon', 'pm',
                'saint barthelemy', 'bl',
                'saint martin', 'mf',
                'terres australes et antarctiques francaises', 'tf'
            ];

            const isFrench = frenchVariants.includes(nat);

            if (isFrench) {
                // Vérifier SSN format + clé
                if (!isValidFrenchSSN(ssn)) {
                    showError(ssnInput, 'Le numéro de sécurité sociale est invalide (format ou clé incorrecte).');
                    ok = false;
                }

                // Vérifier cohérence genre
                const ssnGender = ssn.substring(0, 1);
                let expectedGender = null;

                if (gender === 'm') {
                    expectedGender = '1';
                } else if (gender === 'mme') {
                    expectedGender = '2';
                } else {
                    expectedGender = null;
                }

                if (expectedGender && ssnGender !== expectedGender) {
                    showError(ssnInput, `Le genre sélectionné ne correspond pas au numéro de sécurité sociale (commence par ${ssnGender}).`);
                    ok = false;
                }
            }
        }

        // Vérifier CNI/Passeport/Titre séjour
        if (!isValidIdNumber(idNumber)) {
            showError(idNumberInput, 'Le numéro de CNI, passeport ou titre de séjour est invalide.');
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
        }
    });
});
