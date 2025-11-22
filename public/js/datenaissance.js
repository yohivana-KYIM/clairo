
function validateDateOfBirth(inputElement) {
    const inputValue = inputElement.value;
    const birthday = new Date(inputValue);
    const eighteenYearsAgo = new Date();
    eighteenYearsAgo.setFullYear(eighteenYearsAgo.getFullYear() - 18);
    if (birthday > eighteenYearsAgo) {
        inputElement.classList.remove('is-valid');
        inputElement.classList.add('is-invalid');
    } else {
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
    }

    if (inputValue.trim() === '') {
        inputElement.classList.remove('is-valid');
        inputElement.classList.add('is-invalid');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const inputElement = document.getElementById('etat_civil_dateNaissance');
    validateDateOfBirth(inputElement);
});

const inputElement = document.getElementById('etat_civil_dateNaissance');
inputElement.addEventListener('input', function (e) {
    validateDateOfBirth(e.target);
});
