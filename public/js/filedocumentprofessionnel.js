function handleFileInputChange(inputId, outputId) {
    const fileInput = document.getElementById(inputId);
    const fileNameOutput = document.getElementById(outputId);

    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            fileNameOutput.textContent = 'Document modifié : ' + fileInput.files[0].name;
        } else {
            fileNameOutput.textContent = '';
        }
    });
}

// function checkDatesValidityGies() {
//     const dateDebutElementGies = document.getElementById('document_professionnel_dateGies0Debut');
//     const dateFinElementGies = document.getElementById('document_professionnel_dateGies0Fin');
//     const dateDebut = dateDebutElementGies.value;
//     const dateFin = dateFinElementGies.value;

//     if (dateDebut && dateFin) {
//         if (dateDebut <= dateFin) {
//             dateDebutElementGies.classList.remove('is-invalid');
//             dateFinElementGies.classList.remove('is-invalid');
//             dateDebutElementGies.classList.add('is-valid');
//             dateFinElementGies.classList.add('is-valid');
//         } else {
//             dateDebutElementGies.classList.remove('is-valid');
//             dateFinElementGies.classList.remove('is-valid');
//             dateDebutElementGies.classList.add('is-invalid');
//             dateFinElementGies.classList.add('is-invalid');
//         }
//     } else {
//         dateDebutElementGies.classList.remove('is-valid');
//         dateFinElementGies.classList.remove('is-valid');
//         dateDebutElementGies.classList.add('is-invalid');
//         dateFinElementGies.classList.add('is-invalid');
//     }
// }



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

// function checkDatesValidityAtex() {
//     const dateDebutElementAtex = document.getElementById('document_professionnel_dateAtex0Debut');
//     const dateFinElementAtex = document.getElementById('document_professionnel_dateAtex0Fin');
//     const dateDebut = dateDebutElementAtex.value;
//     const dateFin = dateFinElementAtex.value;

//     if (dateDebut && dateFin) {
//         if (dateDebut <= dateFin) {
//             dateDebutElementAtex.classList.remove('is-invalid');
//             dateFinElementAtex.classList.remove('is-invalid');
//             dateDebutElementAtex.classList.add('is-valid');
//             dateFinElementAtex.classList.add('is-valid');
//         } else {
//             dateDebutElementAtex.classList.remove('is-valid');
//             dateFinElementAtex.classList.remove('is-valid');
//             dateDebutElementAtex.classList.add('is-invalid');
//             dateFinElementAtex.classList.add('is-invalid');
//         }
//     }
// }

document.addEventListener('DOMContentLoaded', function() {
    handleFileInputChange('document_professionnel_gies0', 'fileNameGies0');
    handleFileInputChange('document_professionnel_gies1', 'fileNameGies1');
    handleFileInputChange('document_professionnel_gies2', 'fileNameGies2');
    handleFileInputChange('document_professionnel_atex0', 'fileNameAtex0');
    handleFileInputChange('document_professionnel_autre', 'fileNameAutre');

    // const dateDebutElementGies = document.getElementById('document_professionnel_dateGies0Debut');
    const dateFinElementGies = document.getElementById('document_professionnel_dateGies0Fin');

    // dateDebutElementGies.addEventListener('input', checkDatesValidityGies);
    dateFinElementGies.addEventListener('input', checkDatesValidityGies);

    // const dateDebutElementAtex = document.getElementById('document_professionnel_dateAtex0Debut');
    const dateFinElementAtex = document.getElementById('document_professionnel_dateAtex0Fin');

    // dateDebutElementAtex.addEventListener('input', checkDatesValidityAtex);
    dateFinElementAtex.addEventListener('input', checkDatesValidityAtex);
});
