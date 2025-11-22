(function () {
    'use strict';

    // ✅ POLYFILLS SÛRS
    if (!NodeList.prototype.forEach) {
        NodeList.prototype.forEach = Array.prototype.forEach;
    }
    if (!Element.prototype.matches) {
        Element.prototype.matches =
            Element.prototype.msMatchesSelector ||
            Element.prototype.webkitMatchesSelector ||
            Element.prototype.mozMatchesSelector ||
            Element.prototype.oMatchesSelector;
    }
    if (!Element.prototype.closest) {
        Element.prototype.closest = function (selector) {
            var el = this;
            while (el && el.nodeType === 1) {
                if (el.matches(selector)) return el;
                el = el.parentElement || el.parentNode;
            }
            return null;
        };
    }

    // ✅ showError blindé
    function showError(el, msg) {
        if (!el || !msg) return;
        var box = el.parentNode;
        if (!box) return;

        var err = box.querySelector('.error-message');
        if (!err) {
            err = document.createElement('div');
            err.className = 'error-message';
            err.style.color = 'red';
            err.style.fontSize = '0.85em';
            box.appendChild(err);
        }
        err.textContent = msg;
        if (el.className.indexOf('invalid') === -1) {
            el.className += ' invalid';
        }
    }

    // ✅ Après DOM chargé
    document.addEventListener('DOMContentLoaded', function () {
        var stepValues = window.stepValues || JSON.parse($('#step_values').val()) || {};
        var requiredFields = ['person_access_step_five_form[signature]'];
        var groupRegistry = {};

        // ✅ Enregistrement des groupes or-required
        document.querySelectorAll('[class*="or-required-"]').forEach(function (el) {
            if (!el || !el.className) return;
            var cls = el.className.split(/\s+/);
            var grp = null;
            for (var i = 0; i < cls.length; i++) {
                if (cls[i].indexOf('or-required-') === 0) {
                    grp = cls[i];
                    break;
                }
            }
            if (grp) {
                if (!groupRegistry[grp]) groupRegistry[grp] = [];
                groupRegistry[grp].push(el);
            }
        });

        // ✅ Générer previews fichiers existants
        for (var key in stepValues) {
            if (!stepValues.hasOwnProperty(key)) continue;
            var val = stepValues[key];
            if (!val) continue;

            var basePathToRemove = window.APP_PUBLIC_PATH || '/srv/app/public';
            var cleanUrl = val.replace(basePathToRemove, '');
            var input = document.querySelector('[name="person_access_step_five_form[' + key + ']"]');
            if (!input || input.offsetParent === null) continue;

            var parent = input.parentNode;
            if (!parent) continue;

            var existing = parent.querySelector('.existing-file');
            if (existing) existing.remove();

            var preview = document.createElement('div');
            preview.className = 'existing-file mt-2';

            if (/\.(jpe?g|png|gif)$/i.test(cleanUrl)) {
                var img = document.createElement('img');
                img.src = cleanUrl;
                img.alt = key;
                img.style.maxWidth = '200px';
                img.style.border = '1px solid #ccc';
                img.style.borderRadius = '6px';
                img.style.marginTop = '5px';
                preview.appendChild(img);
            } else {
                var a = document.createElement('a');
                a.href = cleanUrl;
                a.target = '_blank';
                a.textContent = '📄 Voir le fichier déjà envoyé';
                a.style.display = 'inline-block';
                a.style.marginTop = '5px';
                preview.appendChild(a);
            }

            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = input.name;
            hidden.value = cleanUrl;

            parent.insertBefore(hidden, parent.firstChild);
            parent.appendChild(preview);
        }

        // ✅ Champs obligatoires simples
        requiredFields.forEach(function (fieldName) {
            var input = document.querySelector('[name="' + fieldName + '"]');
            if (!input || input.offsetParent === null) return;

            var id = input.id || '';
            var simpleName = id.replace('person_access_step_five_form_', '');
            var isUploaded = stepValues.hasOwnProperty(simpleName);
            input.required = !isUploaded;

            var label = document.querySelector('label[for="' + id + '"]');
            if (label) {
                var sup = label.querySelector('sup.micro');
                if (sup) sup.remove();
                if (!isUploaded) {
                    var s = document.createElement('sup');
                    s.className = 'micro';
                    s.textContent = 'm';
                    label.appendChild(document.createTextNode(' '));
                    label.appendChild(s);
                }
            }
        });

        // ✅ Fonction mise à jour groupes or-required
        function updateOrGroups() {
            for (var grp in groupRegistry) {
                if (!groupRegistry.hasOwnProperty(grp)) continue;

                var visibles = [];
                groupRegistry[grp].forEach(function (el) {
                    if (el && el.offsetParent !== null) visibles.push(el);
                });

                if (visibles.length < 2) continue;

                var oneFilled = false;
                visibles.forEach(function (el) {
                    var p = el.closest('.mb-3');
                    var h = p ? p.querySelector('input[type="hidden"]') : null;
                    var exist = (h && h.value.trim()) || (p && p.querySelector('.existing-file'));
                    if ((el.files && el.files.length) || exist) oneFilled = true;
                    el.required = false;
                });

                document.querySelectorAll('.' + grp + '-warning-message').forEach(function (w) {
                    if (w && w.parentNode) w.parentNode.removeChild(w);
                });

                if (!oneFilled) {
                    var labels = [];
                    visibles.forEach(function (el) {
                        var id = el.id;
                        var l = id ? document.querySelector('label[for="' + id + '"]') : null;
                        labels.push(l ? l.textContent.replace(' m', '').trim() : '[Champ inconnu]');
                    });
                }

                visibles.forEach(function (el) {
                    var id = el.id;
                    var l = id ? document.querySelector('label[for="' + id + '"]') : null;
                    if (l && !l.querySelector('sup.micro')) {
                        var s = document.createElement('sup');
                        s.className = 'micro';
                        s.textContent = 'm';
                        l.appendChild(document.createTextNode(' '));
                        l.appendChild(s);
                    }
                });
            }
        }

        document.addEventListener('input', function (e) {
            var t = e.target || e.srcElement;
            if (t && t.className && t.className.indexOf('or-required-') > -1) updateOrGroups();
        });

        updateOrGroups();

        // ✅ Marquer sections obligatoires
        var sections = ["Pièces justificatives obligatoires", "Pièces supplémentaires"];
        document.querySelectorAll('h3').forEach(function (h) {
            if (sections.indexOf(h.textContent.trim()) === -1) return;
            var c = h.parentElement ? h.parentElement.nextElementSibling : null;
            while (c && c.className.indexOf('form-separator') === -1) {
                c.querySelectorAll('input[type="file"]').forEach(function (input) {
                    var label = c.querySelector('label[for="' + input.id + '"]');
                    if (label && label.textContent.indexOf('*') === -1) label.textContent += ' *';
                });
                c = c.nextElementSibling;
            }
        });

        // ✅ Submit protégé
        var form = document.querySelector('form[name="person_access_step_five_form"]');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            var btn = e.submitter || document.activeElement;
            if (btn && btn.hasAttribute && btn.hasAttribute('formnovalidate')) return;

            e.preventDefault();
            var ok = true;

            document.querySelectorAll('h3').forEach(function (h) {
                if (sections.indexOf(h.textContent.trim()) === -1) return;

                var c = h.parentElement ? h.parentElement.nextElementSibling : null;
                while (c && c.className.indexOf('form-separator') === -1) {
                    c.querySelectorAll('input[type="file"]').forEach(function (i) {
                        for (var j = 0; j < i.classList.length; j++) {
                            if (i.classList[j].indexOf('or-required-') === 0) return;
                        }
                        var p = i.closest('.mb-3');
                        var h = p ? p.querySelector('input[type="hidden"]') : null;
                        var exist = (h && h.value.trim()) || (p && p.querySelector('.existing-file'));
                        var old = p && p.querySelector('.error-message');
                        if (old) old.remove();
                        i.className = i.className.replace(' invalid', '');
                        if (!i.files.length && !exist) {
                            ok = false;
                            showError(i, 'Ce document est obligatoire.');
                        }
                    });
                    c = c.nextElementSibling;
                }
            });

            var seen = {};
            document.querySelectorAll('[class*="or-required-"]').forEach(function (i) {
                var grp = null;
                for (var k = 0; k < i.classList.length; k++) {
                    if (i.classList[k].indexOf('or-required-') === 0) {
                        grp = i.classList[k]; break;
                    }
                }
                if (!grp || seen[grp]) return;
                seen[grp] = true;

                var grpIns = [];
                document.querySelectorAll('.' + grp).forEach(function (x) {
                    if (x && x.offsetParent !== null) grpIns.push(x);
                });

                var one = grpIns.some(function (x) {
                    var p = x.closest('.mb-3');
                    var h = p ? p.querySelector('input[type="hidden"]') : null;
                    var exist = (h && h.value.trim()) || (p && p.querySelector('.existing-file'));
                    return x.files.length || exist;
                });

                grpIns.forEach(function (x) {
                    var p = x.closest('.mb-3');
                    var old = p && p.querySelector('.error-message');
                    if (old) old.remove();
                    x.className = x.className.replace(' invalid', '');
                });

                if (!one) {
                    ok = false;
                    grpIns.forEach(function (x) {
                        showError(x, 'Veuillez fournir au moins un document pour ce groupe.');
                    });
                }
            });

            if (ok) {
                form.submit();
            } else {
                var first = form.querySelector('.invalid');
                if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
})();
