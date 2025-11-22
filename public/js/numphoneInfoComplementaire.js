document.addEventListener('DOMContentLoaded', function () {

    const inputElement = document.getElementById('info_complementaire_numTelephone');
    if (inputElement.value.trim() !== '') {
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
    }
    inputElement.addEventListener('input', function (e) {
        let inputValue = e.target.value;
        const phoneRegex = /^(?:(?:\+|00)[1-9]\d{0,2}[-\s]?)?(?:(?:\d{1,5}[-\s]?)?\d{1,12})$/;
        const invalidCharRegex = /[^0-9+\-\s]/g;
        inputValue = inputValue.replace(invalidCharRegex, '');

        if (phoneRegex.test(inputValue)) {
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        }
        if (inputValue.trim() === '') {
            e.target.classList.remove('is-valid');
        }
        e.target.value = inputValue;
    });
});