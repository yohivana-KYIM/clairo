function initFileUploadPreviewAndValidation({
                                                requiredFields = [],
                                                uploadedFiles = {},
                                                certType = null,
                                                accessType = null
                                            }) {

    /* ============================================================
       1. RÈGLES CONDITIONNELLES : gies / duplication
    ============================================================ */
    let conditionalRules = {
        gies_sticker_copy: certType === 'gies',
        old_circulation_card: accessType === 'duplication',
        declaration_form: accessType === 'duplication'
    };

    Object.entries(conditionalRules).forEach(([fieldName, shouldShow]) => {
        const selector = `[name="vehicle_access_step_five_form[${fieldName}]"]`;
        const $input = $(selector);
        if (!$input.length) return;
        const $wrapper = $input.closest('.mb-3');

        if (shouldShow) {
            $wrapper.show();
        } else {
            $wrapper.hide();
            requiredFields = requiredFields.filter(x => !x.includes(fieldName));
        }
    });

    /* ============================================================
       2. GESTION DES CHAMPS REQUIS + PREVIEW DES FICHIERS EXISTANTS
    ============================================================ */
    requiredFields.forEach(name => {
        const $input = $(`[name="${name}"]`);
        if (!$input.length || !$input.is(':visible')) return;

        const id = $input.attr('id');
        const fieldName = id.replace('vehicle_access_step_five_form_', '');
        const alreadyUploaded = uploadedFiles.hasOwnProperty(fieldName);

        const $label = $(`label[for="${id}"]`);
        $label.find('sup.micro').remove();

        $input.prop('required', !alreadyUploaded);

        if (!alreadyUploaded) {
            $label.append(' <sup class="micro">m</sup>');
        }

        if (!alreadyUploaded) return;

        const base = window.APP_PUBLIC_PATH || '/srv/app/public';
        const url = uploadedFiles[fieldName].replace(base, '');

        const $preview = $('<div class="existing-file mt-2">');
        if (/\.(jpe?g|png|gif)$/i.test(url)) {
            $preview.append(
                $('<img>', {
                    src: url,
                    alt: fieldName,
                    css: {
                        maxWidth: '200px',
                        border: '1px solid #ccc',
                        borderRadius: '6px',
                        marginTop: '5px'
                    }
                })
            );
        } else {
            $preview.append(
                $('<a>', {
                    href: url,
                    text: '📄 Voir le fichier déjà envoyé',
                    target: '_blank',
                    css: { display: 'inline-block', marginTop: '5px' }
                })
            );
        }

        $input.parent().prepend(
            $('<input>', {
                type: 'hidden',
                name: $input.attr('name'),
                value: url
            })
        );

        $input.parent().append($preview);
    });

    /* ============================================================
       3. GROUPES OR-REQUIRED
    ============================================================ */
    const groupRegistry = {};

    $('[class*="or-required-"]').each(function () {
        const $field = $(this);
        const cls = $field.attr('class').split(/\s+/);
        const grp = cls.find(c => c.startsWith('or-required-'));
        if (!grp) return;

        groupRegistry[grp] = groupRegistry[grp] || [];
        groupRegistry[grp].push($field);
    });

    function updateOrGroups() {
        Object.entries(groupRegistry).forEach(([grp, fields]) => {
            const $visible = fields.filter($f => $f.is(':visible'));
            if ($visible.length < 2) return;

            let oneFilled = false;

            $visible.forEach($f => {
                if ($f.is('[type="file"]')) {
                    if ($f[0].files.length) oneFilled = true;
                } else if ($f.val().trim()) {
                    oneFilled = true;
                }
                $f.prop('required', false);
            });

            // Remove previous warnings
            $(`.${grp}-warning-message`).remove();

            if (!oneFilled) {
                const labels = $visible.map($f => {
                    const id = $f.attr('id');
                    const $l = $(`label[for="${id}"]`);
                    return $l.length ? $l.text().replace(' m', '').trim() : '[Champ inconnu]';
                }).get().join(', ');

                const $warn = $('<div>')
                    .addClass(`warning-message warning expired ${grp}-warning-message`)
                    .text(`Veuillez remplir au moins un de ces champs (${labels}).`);

                $visible.forEach($f => $f.after($warn.clone()));
            }

            // Add micro "m" to labels
            $visible.forEach($f => {
                const id = $f.attr('id');
                const $label = $(`label[for="${id}"]`);
                if ($label && !$label.find('sup.micro').length) {
                    $label.append(' <sup class="micro">m</sup>');
                }
            });
        });
    }

    updateOrGroups();
    $(document).on('input change', '[class*="or-required-"]', updateOrGroups);

    /* ============================================================
       4. VALIDATION FINALE AVANT SUBMIT
    ============================================================ */
    const $form = $('form[name="vehicle_access_step_five_form"]');
    if (!$form.length) return;

    let isSubmitting = false;

    $form.on('submit', function (e) {
        const submitter = e.originalEvent?.submitter;
        if (submitter && submitter.hasAttribute('formnovalidate')) return;

        if (isSubmitting) {
            e.preventDefault();
            return;
        }

        e.preventDefault();

        let valid = true;

        // Nettoyer anciens messages
        $form.find('.error-message').remove();
        $form.find('.invalid').removeClass('invalid');

        // Vérifier required classiques
        requiredFields.forEach(name => {
            const $input = $(`[name="${name}"]`).last();
            if (!$input.length || !$input.is(':visible')) return;

            const id = $input.attr('id');
            const fieldName = id.replace('vehicle_access_step_five_form_', '');
            const hasExisting = uploadedFiles.hasOwnProperty(fieldName);

            if (!$input[0].files.length && !hasExisting) {
                valid = false;
                $input.addClass('invalid');
                $input.after('<div class="error-message" style="color:red;font-size:0.85em">Document obligatoire.</div>');
            }
        });

        // Vérifier OR groups
        Object.entries(groupRegistry).forEach(([grp, fields]) => {
            const visible = fields.filter($f => $f.is(':visible'));
            if (visible.length < 2) return;

            const oneFilled = visible.some($f => {
                if ($f.is('[type="file"]')) return $f[0].files.length > 0;
                return !!$f.val().trim();
            });

            if (!oneFilled) {
                valid = false;
                visible.forEach($f => {
                    $f.addClass('invalid');
                    $f.after(
                        `<div class="error-message" style="color:red;font-size:0.85em">Veuillez fournir un document pour au moins un champ du groupe.</div>`
                    );
                });
            }
        });

        if (!valid) {
            const $first = $form.find('.invalid').first();
            if ($first.length) {
                $first[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        isSubmitting = true;
        $form[0].submit();
    });
}
initFileUploadPreviewAndValidation({
    requiredFields: [
        'vehicle_access_step_five_form[signature]',
        'vehicle_access_step_five_form[card_copy]',
        'vehicle_access_step_five_form[gies_sticker_copy]',
        'vehicle_access_step_five_form[old_circulation_card]',
        'vehicle_access_step_five_form[declaration_form]'
    ],
    uploadedFiles: stepValues,
    certType: stepValues.certification_type,       // ex. "gies" ou autre
    accessType: stepValues.access_type              // ex. "duplication" ou autre
    // orGroups détectés automatiquement
});

/* ============================================================
   5. VALIDATION AJAX DES FICHIERS À LA VOLÉE
============================================================ */
$('[type="file"]').on('change', function () {

    const fileInput = this;
    const file = fileInput.files[0];
    const $input = $(fileInput);

    // Nettoyage des erreurs locales
    $input.removeClass('invalid');
    $input.next('.error-message').remove();

    if (!file) return;

    let formKey = $input.attr('name').replace('vehicle_access_step_five_form[', '').replace(']', '');

    let formData = new FormData();
    formData.append('file', file);
    formData.append('key', formKey);

    fetch('/ajax/validate-upload', {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(resp => {
            if (!resp.success) {
                // ❌ Erreurs : on empêche le submit
                $input.addClass('invalid');

                resp.errors.forEach(msg => {
                    $input.after(
                        `<div class="error-message" style="color:red;font-size:0.85em">${msg}</div>`
                    );
                });

                // Supprimer le fichier invalide
                fileInput.value = "";
            }
        })
        .catch(() => {
            $input.after(`<div class="error-message" style="color:red">Erreur serveur.</div>`);
        });
});
