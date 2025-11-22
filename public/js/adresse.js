
const inputElement = document.getElementById('adresse_cp');

inputElement.addEventListener('input', function (e) {
    const inputValue = e.target.value;
    const numericPattern = /^\d*$/;
    if (!numericPattern.test(inputValue)) {
        e.target.value = inputValue.replace(/\D/g, '');
    }
});