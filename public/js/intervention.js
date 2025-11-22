document.addEventListener('DOMContentLoaded', function () {
    const motifField = document.querySelector('.motif_principal');
    const autreMotifField = document.querySelector('.autre_motif');
    const dureeField = document.querySelector('.duree_temporaire');
    const dateField = document.querySelector('.date_temporaire');

    function toggleAutreMotifField() {
        const selectedOption = motifField.options[motifField.selectedIndex];
        if (selectedOption.value === 'autre') {
            autreMotifField.style.display = 'block';
            autreMotifField.required = true;
        } else {
            autreMotifField.style.display = 'none';
            autreMotifField.required = false;
        }
    }

    function toggleDateInterventionField() {
        const selectedOption = dureeField.options[dureeField.selectedIndex];
        const selectedValue = selectedOption.value;

        if (selectedOption.value === 'temporaire') {
            dateField.style.display = 'block';
            dateField.required = true;
            sessionStorage.setItem("duree", selectedValue);
        } else if (selectedOption.value === 'permanent') {
            sessionStorage.setItem("duree", selectedValue);
            dateField.style.display = 'none';
            dateField.required = false;
        } else {
            sessionStorage.removeItem("duree", selectedValue);
        }
    }

    function toggleReadyOrNot() {
        if (autreMotifField.value.trim() !== '') {
            autreMotifField.classList.remove('is-invalid');
            autreMotifField.classList.add('is-valid');
        } else {
            autreMotifField.classList.remove('is-valid');
            autreMotifField.classList.add('is-invalid');
        }
    }

    toggleAutreMotifField();
    toggleDateInterventionField();
    toggleReadyOrNot();

    motifField.addEventListener('change', toggleAutreMotifField);
    dureeField.addEventListener('change', toggleDateInterventionField);
    autreMotifField.addEventListener('input', toggleReadyOrNot);
});
