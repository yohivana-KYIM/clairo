document.addEventListener('DOMContentLoaded', function () {
    const nationaliteField = document.querySelector('.francais_etranger');


    function toggleDateInterventionField() {
        const selectedOption = nationaliteField.options[nationaliteField.selectedIndex];
        const selectedValue = selectedOption.value;

        if (selectedOption.value === 'francais') {
            sessionStorage.setItem("nationalite", selectedValue);
        } else if (selectedOption.value === 'etranger') {
            sessionStorage.setItem("nationalite", selectedValue);
        } else {
            sessionStorage.removeItem("nationalite", selectedValue);
        }
    }

    toggleDateInterventionField();

    nationaliteField.addEventListener('change', toggleDateInterventionField);
});





