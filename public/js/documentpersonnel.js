const nationalite = "{{ nationalite }}";
const duree = "{{ duree }}";

document.addEventListener('DOMContentLoaded', function () {
    const arrondissementNaissanceField = document.querySelectorAll('.arrondissement_naissance');
    const identityField = document.querySelectorAll('.identity');
    const PhotoField = document.querySelectorAll('.photo');
    const CasierJudiciaireField = document.querySelectorAll('.casier_judiciaire');
    const acteNaissanceField = document.querySelectorAll('.acte_naissance');
    const domicileField = document.querySelectorAll('.domicile');
    const hebergementField = document.querySelectorAll('.hebergement');
    const IdentHebergentField = document.querySelectorAll('.ident_hebergent');
    const sejourField = document.querySelectorAll('.sejour');

        var currentURL = window.location.pathname;
        if (currentURL.endsWith("/edit")) {
            if (duree === 'permanent' && nationalite === 'etranger') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'none';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = false;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = false;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'block';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'block';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'block';
                    domicileField.required = false;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'block';
                    hebergementField.required = false;
                });
                IdentHeabergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'block';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'block';
                    sejourField.required = false;
                });
            } if (duree === 'permanent' && nationalite === 'francais') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'none';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = false;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = false;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'none';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'none';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'block';
                    domicileField.required = false;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'block';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'block';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'none';
                    sejourField.required = false;
                });
            } if (duree === 'temporaire' && nationalite === 'etranger') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'none';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = false;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = false;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'none';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'none';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'none';
                    domicileField.required = false;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'none';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'none';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'block';
                    sejourField.required = false;
                });
            } if (duree === 'temporaire' && nationalite === 'francais') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'block';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = false;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = false;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'none';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'none';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'none';
                    domicileField.required = false;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'none';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'none';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'none';
                    sejourField.required = false;
                });
            }
        } else {
            if (duree === 'permanent' && nationalite === 'etranger') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'none';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = true;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = true;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'block';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'block';
                    acteNaissanceField.required = true;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'block';
                    domicileField.required = true;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'block';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'block';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'block';
                    sejourField.required = false;
                });
            } if (duree === 'permanent' && nationalite === 'francais') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'none';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = true;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = true;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'none';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'none';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'block';
                    domicileField.required = true;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'block';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'block';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'none';
                    sejourField.required = false;
                });
            } if (duree === 'temporaire' && nationalite === 'etranger') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'none';
                    arrondissementNaissanceField.required = false;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = true;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = true;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'none';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'none';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'none';
                    domicileField.required = false;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'none';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'none';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'block';
                    sejourField.required = false;
                });
            } if (duree === 'temporaire' && nationalite === 'francais') {
                arrondissementNaissanceField.forEach(arrondissementNaissanceField => {
                    arrondissementNaissanceField.style.display = 'block';
                    arrondissementNaissanceField.required = true;
                });
                identityField.forEach(identityField => {
                    identityField.style.display = 'block';
                    identityField.required = true;
                });
                PhotoField.forEach(PhotoField => {
                    PhotoField.style.display = 'block';
                    PhotoField.required = true;
                });
                CasierJudiciaireField.forEach(CasierJudiciaireField => {
                    CasierJudiciaireField.style.display = 'none';
                    CasierJudiciaireField.required = false;
                });
                acteNaissanceField.forEach(acteNaissanceField => {
                    acteNaissanceField.style.display = 'none';
                    acteNaissanceField.required = false;
                });
                domicileField.forEach(domicileField => {
                    domicileField.style.display = 'none';
                    domicileField.required = false;
                });
                hebergementField.forEach(hebergementField => {
                    hebergementField.style.display = 'none';
                    hebergementField.required = false;
                });
                IdentHebergentField.forEach(IdentHebergentField => {
                    IdentHebergentField.style.display = 'none';
                    IdentHebergentField.required = false;
                });
                sejourField.forEach(sejourField => {
                    sejourField.style.display = 'none';
                    sejourField.required = false;
                });
            }
        }
});