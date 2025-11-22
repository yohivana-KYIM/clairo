document.addEventListener('DOMContentLoaded', function () {
    const gies0 = document.querySelectorAll('.gies0');
    const dateDebutGies0 = document.querySelectorAll('.dateDebutGies0');
    const dateFinGies0 = document.querySelectorAll('.dateFinGies0');
    const atex0 = document.querySelectorAll('.atex0');
    const dateDebutAtex0 = document.querySelectorAll('.dateDebutAtex0');
    const dateFinAtex0 = document.querySelectorAll('.dateFinAtex0');

        var currentURL = window.location.pathname;
        if (currentURL.endsWith("/edit")) {
                gies0.forEach(gies0 => {
                    gies0.required = false;
                });
                dateDebutGies0.forEach(dateDebutGies0 => {
                    dateDebutGies0.required = false;
                });
                dateFinGies0.forEach(dateFinGies0 => {
                    dateFinGies0.required = false;
                });
                atex0.forEach(atex0 => {
                    atex0.required = false;
                });
                dateDebutAtex0.forEach(dateDebutAtex0 => {
                    dateDebutAtex0.required = false;
                });
                dateFinAtex0.forEach(dateFinAtex0 => {
                    dateFinAtex0.required = false;
                });
        } else {
            gies0.forEach(gies0 => {
                gies0.required = false;
            });
            dateDebutGies0.forEach(dateDebutGies0 => {
                dateDebutGies0.required = false;
            });
            dateFinGies0.forEach(dateFinGies0 => {
                dateFinGies0.required = false;
            });
            atex0.forEach(atex0 => {
                atex0.required = false;
            });
            dateDebutAtex0.forEach(dateDebutAtex0 => {
                dateDebutAtex0.required = false;
            });
            dateFinAtex0.forEach(dateFinAtex0 => {
                dateFinAtex0.required = false;
            });
        }
});

