const selectPays = document.getElementById("adresse_pays");
    const inputCodePostal = document.getElementById("adresse_cp");

    selectPays.addEventListener("change", function() {
        if (selectPays.value === "FR") {
            inputCodePostal.addEventListener("input", function(event) {
                const sanitizedValue = event.target.value.replace(/\D/g, '');
                inputCodePostal.value = sanitizedValue;

                if (/^\d{5}$/.test(sanitizedValue)) {
                    inputCodePostal.classList.remove("is-invalid");
                    inputCodePostal.classList.add("is-valid");
                } else {
                    inputCodePostal.classList.remove("is-valid");
                    inputCodePostal.classList.add("is-invalid");
                }
            });
        } else {
            inputCodePostal.classList.remove("is-invalid", "is-valid");
            inputCodePostal.removeEventListener("input", null);
        }
    });
