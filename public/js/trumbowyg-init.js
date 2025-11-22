(function() {
    'use strict';

    // === UTF-8–safe Base64 encode/decode with wrapping ===
    function encodeContent(str, className = 'encoded-content') {
        // 1) UTF-8 encode
        const utf8 = new TextEncoder().encode(str);
        // 2) binary string
        let bin = '';
        for (let b of utf8) {
            bin += String.fromCharCode(b);
        }
        // 3) Base64
        const b64 = btoa(bin);
        // 4) wrap in <span class="...">...</span>
        return `<span class="${className}">${b64}</span>`;
    }

    function decodeContent(input, className = 'encoded-content') {
        let b64 = input;
        // 1) if wrapped in the expected <span>, unwrap it
        const wrapperRegex = new RegExp(
            `^<span\\s+class=["']${className}["']>([A-Za-z0-9+/=]+)<\\/span>$`
        );
        const match = input.match(wrapperRegex);
        if (match) {
            b64 = match[1];
        }
        // 2) Base64 decode to binary string
        const bin = atob(b64);
        // 3) convert to Uint8Array
        const bytes = Uint8Array.from(bin, c => c.charCodeAt(0));
        // 4) UTF-8 decode
        return new TextDecoder().decode(bytes);
    }

    // === CSRF token helper ===
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : null;
    }

    // === Initialize Trumbowyg on all <textarea:not([readonly])> ===
    function initEditors() {
        const csrf = getCsrfToken();

        document.querySelectorAll('textarea:not([readonly])').forEach((ta, idx) => {
            if (!ta.id) ta.id = 'trumbowyg_' + idx;

            // Preload saved Base64 content
            let raw = ta.value.trim();

            // 1) If wrapped in <span class="encoded-content">...</span>, unwrap it:
            const wrapMatch = raw.match(
                /^<span\s+class=["']encoded-content["']>([A-Za-z0-9+/=]+)<\/span>$/
            );
            if (wrapMatch) raw = wrapMatch[1];

            // 2) Quick Base64 sanity-check (only [A-Za-z0-9+/=], len mod 4 === 0)
            const isLikelyB64 = /^[A-Za-z0-9+/=]+$/.test(raw) && raw.length % 4 === 0;

            if (isLikelyB64) {
                try {
                    ta.value = decodeContent(raw);
                } catch (e) {
                    console.warn('Failed to Base64-decode content for', ta.id, e);
                    // leave ta.value as it was
                }
            }

            // Build Trumbowyg config
            const cfg = {
                lang: 'fr',
                autogrow: true,
                removeformatPasted: true,
                btns: [
                    ['undo', 'redo'],
                    ['formatting'],
                    ['strong', 'em', 'underline', 'del'],
                    ['link'],
                    ['unorderedList', 'orderedList'],
                    ['insertImage', 'upload'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ],
                plugins: {}
            };

            // File upload plugin config
            if (window.trumbowyg && trumbowyg.plugins.upload) {
                cfg.plugins.upload = {
                    serverPath: '/secure-uploader/upload',
                    fileFieldName: 'file',
                    headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {}
                };
            }

            // Initialize
            try {
                if (ta.id) {
                    $('#' + ta.id)
                        .trumbowyg(cfg)
                        // After init, force default alignment:
                        .on('tbwinit', function () {
                            var $box = $(this).closest('.trumbowyg-box'),
                                $editor = $box.find('.trumbowyg-editor');
                            $editor.css('text-align', 'justify');
                        });
                }
            } catch (err) {
                console.error('Trumbowyg init error for', ta.id, err);
            }
        });
    }

    // === On form submit, Base64-encode editor data ===
    function bindFormSubmit() {
        document.querySelectorAll('form').forEach(form => {
            if (form._trumbowygBound) return;
            form._trumbowygBound = true;

            form.addEventListener('submit', () => {
                document.querySelectorAll('textarea:not([readonly])').forEach(ta => {
                    if (ta.id) {
                        const $ta = $('#' + ta.id);

                        if (!$ta.data('trumbowyg')) return;  // not a Trumbowyg editor
                        const html = $ta.trumbowyg('html');
                        const b64 = encodeContent(html);

                        let hidden = form.querySelector(`input[name="${ta.name}_encoded"]`);
                        if (!hidden) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = ta.name + '_encoded';
                            form.appendChild(hidden);
                        }
                        hidden.value = b64;
                    }
                });
            });
        });
    }

    // === DOM Ready ===
    function onReady() {
        initEditors();
        bindFormSubmit();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }

})();
