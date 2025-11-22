document.addEventListener('DOMContentLoaded', function () {
    const inputElements = document.querySelectorAll(".formvalidate");

    inputElements.forEach(inputElement => {
        inputElement.addEventListener("input", () => {
            if (inputElement.value.trim() !== '') {
                inputElement.classList.remove('is-invalid');
                inputElement.classList.add('is-valid');
            } else {
                inputElement.classList.remove('is-valid');
                inputElement.classList.add('is-invalid');
            }
        });

        if (inputElement.value.trim() !== '') {
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
        }
    });
});
