
document.addEventListener('DOMContentLoaded', function () {
    const inputElement = document.getElementById('etat_civil_cpNaissance');

    function validateInput() {
        let inputValue = inputElement.value;
        const regex = /^\d{4,10}$/;

        inputValue = inputValue.replace(/\D/g, '');

        if (regex.test(inputValue)) {
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
        } else {
            inputElement.classList.remove('is-valid');
            inputElement.classList.add('is-invalid');
        }

        inputElement.value = inputValue;
    }

    inputElement.addEventListener('input', function (e) {
        validateInput();
    });

    validateInput();
});

document.addEventListener('DOMContentLoaded', function () {
    const inputElement = document.getElementById('adresse_cp');

    function validateInput() {
        let inputValue = inputElement.value;
        const regex = /^\d{4,10}$/;

        inputValue = inputValue.replace(/\D/g, '');

        if (regex.test(inputValue)) {
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
        } else {
            inputElement.classList.remove('is-valid');
            inputElement.classList.add('is-invalid');
        }

        inputElement.value = inputValue;
    }

    inputElement.addEventListener('input', function (e) {
        validateInput();
    });

    validateInput();
});
