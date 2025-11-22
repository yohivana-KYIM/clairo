(function () {
    "use strict";

    // ---------- Singleton / Idempotency ----------
    const GLOBAL_KEY = "__RO_LOCKER__V1__";
    if (window[GLOBAL_KEY] && typeof window[GLOBAL_KEY].scan === "function") {
        try { window[GLOBAL_KEY].scan(); } catch (_) {}
        return;
    }

    // ---------- Config ----------
    const READONLY_CLASS = "read_only";
    const HIDDEN_FLAG_ATTR = "data-synthetic-readonly";
    const LOCK_FLAG_ATTR = "data-ro-locked";
    const CLONE_SCOPE_ATTR = "data-ro-scope-id";

    // ---------- Polyfills (idempotents) ----------
    if (typeof window.CSS === "undefined") window.CSS = {};
    if (typeof window.CSS.escape !== "function") {
        window.CSS.escape = function (val) {
            return String(val).replace(/([^\w-])/g, "\\$1");
        };
    }

    // ---------- Utils ----------
    let scopeSeq = 0;
    function newScopeId() { return "ro-scope-" + (++scopeSeq); }

    // NOTE: on N'EXCLUT PLUS les champs déjà lockés => ils doivent être clonés au submit
    function isReadOnlyCandidate(el) {
        if (!el || !el.name) return false;
        if (el.type === "hidden") return false; // pas d'UI à verrouiller
        if (el.classList && el.classList.contains(READONLY_CLASS)) return true;
        return !!(el.closest && el.closest("." + READONLY_CLASS));
    }

    function getReadonlyFieldsWithin(root) {
        const nodes = root.querySelectorAll("input, select, textarea");
        return Array.from(nodes).filter(isReadOnlyCandidate);
    }

    function lockFieldForUI(field) {
        // Idempotence UI : si déjà locké, on ne re-stylise pas
        if (field[LOCK_FLAG_ATTR] === "1") return;

        const wasDisabled = field.disabled;

        field.setAttribute("aria-readonly", "true");
        field.setAttribute("aria-disabled", "true");

        // Style non destructif
        if (!field.style.backgroundColor) field.style.backgroundColor = "#f5f5f5";
        if (!field.style.cursor) field.style.cursor = "not-allowed";

        // Eviter la navigation via tab
        if (field.tabIndex !== -1) field.tabIndex = -1;

        // On désactive l'UI (les valeurs seront clonées)
        field.disabled = true;

        field[LOCK_FLAG_ATTR] = "1";
        field.classList.add("is-readonly");

        if (wasDisabled) field.disabled = true;
    }

    function makeHidden(name, value, scopeId) {
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = name;
        hidden.value = value ?? "";
        hidden.setAttribute(HIDDEN_FLAG_ATTR, "1");
        hidden.setAttribute(CLONE_SCOPE_ATTR, scopeId);
        return hidden;
    }

    function removeOldClones(form, scopeId) {
        const sel = `input[type="hidden"][${HIDDEN_FLAG_ATTR}="1"]` +
            (scopeId ? `[${CLONE_SCOPE_ATTR}="${scopeId}"]` : "");
        form.querySelectorAll(sel).forEach(h => h.remove());
    }

    function cloneValueForSubmission(field, form, scopeId) {
        const tag = field.tagName;
        const type = (field.type || "").toLowerCase();
        const name = field.name;
        if (!name) return;

        if (type === "file") return; // ne pas cloner les fichiers

        if (type === "radio") { if (field.checked) form.appendChild(makeHidden(name, field.value, scopeId)); return; }
        if (type === "checkbox") { if (field.checked) form.appendChild(makeHidden(name, field.value || "on", scopeId)); return; }

        if (tag === "SELECT") {
            if (field.multiple) {
                Array.from(field.selectedOptions).forEach(opt => form.appendChild(makeHidden(name, opt.value, scopeId)));
            } else {
                form.appendChild(makeHidden(name, field.value, scopeId));
            }
            return;
        }

        form.appendChild(makeHidden(name, field.value, scopeId));
    }

    function lockFormReadonlyFields(form) {
        const roFields = getReadonlyFieldsWithin(form);
        roFields.forEach(lockFieldForUI);
        return roFields;
    }

    function prepareSubmissionClones(form, roFields, scopeId) {
        // Idempotent : purge des anciens clones de ce formulaire/scope
        removeOldClones(form, scopeId);
        // Re-clone l’état courant
        roFields.forEach(field => cloneValueForSubmission(field, form, scopeId));
    }

    // --- Routine unique appelée AVANT toute soumission ---
    function preSubmit(form) {
        // scope stable
        let scopeId = form.getAttribute(CLONE_SCOPE_ATTR);
        if (!scopeId) {
            scopeId = newScopeId();
            form.setAttribute(CLONE_SCOPE_ATTR, scopeId);
        }
        // Re-lock (idempotent) + clones frais
        const ro = lockFormReadonlyFields(form);
        prepareSubmissionClones(form, ro, scopeId);
    }

    function attachFormHandlers(form) {
        // Idempotence : attacher une seule fois par formulaire
        if (form._roHandlersAttached) return;
        form._roHandlersAttached = true;

        // Attribuer un scope id (stable par form)
        let scopeId = form.getAttribute(CLONE_SCOPE_ATTR);
        if (!scopeId) {
            scopeId = newScopeId();
            form.setAttribute(CLONE_SCOPE_ATTR, scopeId);
        }

        // 1) Lock initial (idempotent)
        lockFormReadonlyFields(form);

        // 2) submit : injecter clones (capture pour passer avant d'autres listeners)
        const submitHandler = function () { preSubmit(form); };
        form.addEventListener("submit", submitHandler, { capture: true });

        // 3) formdata : couvrir FormData/fetch()
        const formdataHandler = function (e) {
            try {
                const fd = e.formData;
                // Recalcule l'ensemble des champs RO et nettoie d'abord
                const ro = lockFormReadonlyFields(form);

                const names = new Set();
                ro.forEach(f => { if (f.name) names.add(f.name); });
                names.forEach(n => { try { fd.delete(n); } catch (_) {} });

                // Réinjecter les valeurs courantes
                ro.forEach(field => {
                    const tag = field.tagName;
                    const type = (field.type || "").toLowerCase();

                    if (!field.name) return;
                    if (type === "file") return;

                    if (type === "radio") { if (field.checked) fd.append(field.name, field.value); return; }
                    if (type === "checkbox") { if (field.checked) fd.append(field.name, field.value || "on"); return; }

                    if (tag === "SELECT") {
                        if (field.multiple) {
                            Array.from(field.selectedOptions).forEach(opt => fd.append(field.name, opt.value));
                        } else {
                            fd.append(field.name, field.value);
                        }
                        return;
                    }
                    fd.append(field.name, field.value);
                });
            } catch (_) { /* fallback: clones sur submit suffisent */ }
        };
        form.addEventListener("formdata", formdataHandler);

        // 4) MutationObserver : auto-lock des champs ajoutés dynamiquement
        if (!form._roObserver) {
            const mo = new MutationObserver(mutations => {
                let needRelock = false;
                for (const m of mutations) {
                    if (m.type === "childList" && m.addedNodes && m.addedNodes.length) {
                        m.addedNodes.forEach(node => {
                            if (!(node instanceof Element)) return;

                            if (node.matches && node.matches("input,select,textarea") && isReadOnlyCandidate(node)) {
                                lockFieldForUI(node);
                                needRelock = true;
                            }
                            const newly = getReadonlyFieldsWithin(node);
                            if (newly.length) {
                                newly.forEach(lockFieldForUI);
                                needRelock = true;
                            }
                        });
                    }
                    if (m.type === "attributes" && (m.attributeName === "class" || m.attributeName === "disabled")) {
                        needRelock = true;
                    }
                }
                if (needRelock) lockFormReadonlyFields(form);
            });

            mo.observe(form, { subtree: true, childList: true, attributes: true, attributeFilter: ["class", "disabled"] });
            form._roObserver = mo;

            // Nettoyage (idempotent)
            window.addEventListener("beforeunload", function cleanupOnce() {
                try { mo.disconnect(); } catch (_) {}
            }, { once: true });
        }
    }

    function scanAllForms() {
        document.querySelectorAll("form").forEach(attachFormHandlers);
    }

    function initOnce() {
        // 1) Attacher aux formulaires présents
        scanAllForms();

        // 2) Observer la page pour les formulaires ajoutés après coup (idempotent : un seul observer global)
        if (!document.documentElement._roPageObserver) {
            const pageObserver = new MutationObserver(muts => {
                muts.forEach(m => {
                    m.addedNodes && m.addedNodes.forEach(node => {
                        if (!(node instanceof Element)) return;
                        if (node.tagName === "FORM") {
                            attachFormHandlers(node);
                        } else {
                            node.querySelectorAll && node.querySelectorAll("form").forEach(attachFormHandlers);
                        }
                    });
                });
            });
            pageObserver.observe(document.documentElement, { childList: true, subtree: true });
            document.documentElement._roPageObserver = pageObserver;

            window.addEventListener("beforeunload", function cleanupOnce() {
                try { pageObserver.disconnect(); } catch (_) {}
                document.documentElement._roPageObserver = null;
            }, { once: true });
        }
    }

    // --- Couvre submit() / requestSubmit() programmatiques ---
    (function patchNativeSubmitters(){
        const proto = HTMLFormElement.prototype;
        if (proto.__ro_patched__) return;
        proto.__ro_patched__ = true;

        const nativeSubmit = proto.submit;
        const nativeRequestSubmit = proto.requestSubmit;

        proto.submit = function () {
            try { attachFormHandlers(this); preSubmit(this); } catch (_) {}
            return nativeSubmit.call(this);
        };

        if (nativeRequestSubmit) {
            proto.requestSubmit = function () {
                try { attachFormHandlers(this); preSubmit(this); } catch (_) {}
                return nativeRequestSubmit.apply(this, arguments);
            };
        }
    })();

    // Exposer une API idempotente
    window[GLOBAL_KEY] = {
        scan: function () { scanAllForms(); }
    };

    // DOM ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initOnce, { once: true });
    } else {
        initOnce();
    }
})();
