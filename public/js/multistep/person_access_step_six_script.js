document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[name="person_access_step_six_form"]');
    const checkbox = document.getElementById('person_access_step_six_form_accept_terms');

    form.addEventListener('submit', function (e) {
        const btn = e.submitter;
        if (btn && btn.hasAttribute('formnovalidate')) {
            return; // skip contrôle
        }
        if (!checkbox.checked) {
            e.preventDefault();
            e.stopPropagation();
            alert("⚠️ Vous devez accepter les conditions générales pour continuer.");
            checkbox.focus();
        }
    });
});
