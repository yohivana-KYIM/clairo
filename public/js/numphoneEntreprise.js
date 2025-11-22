document.addEventListener('DOMContentLoaded', function () {

    const inputElement = document.getElementById('entreprise_numTelephone');

    inputElement.addEventListener('input', function (e) {
        let inputValue = e.target.value;
        const phoneRegex = /^(?:(?:\+|00)[1-9]\d{0,2}[-\s]?)?(?:(?:\d{1,5}[-\s]?)?\d{1,12})$/;
        const invalidCharRegex = /[^0-9+\-\s]/g;
        inputValue = inputValue.replace(invalidCharRegex, '');
        e.target.value = inputValue;
    });
});