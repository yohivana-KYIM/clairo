
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

document.addEventListener('DOMContentLoaded', function() {
    handleFileInputChange('document_personnel_identity', 'fileNameIdentity');
    handleFileInputChange('document_personnel_Photo', 'fileNamePhoto');
    handleFileInputChange('document_personnel_domicile', 'fileNameDomicile');
    handleFileInputChange('document_personnel_hebergement', 'fileNameHebergement');
    handleFileInputChange('document_personnel_IdentHebergent', 'fileNameIdentHebergent');
    handleFileInputChange('document_personnel_acteNaiss', 'fileNameActenaiss')
    handleFileInputChange('document_personnel_sejour', 'fileNameSejour');
    handleFileInputChange('document_personnel_Casier', 'fileNameCasier')
});