/**
 * public/js/autocomplete.js
 * Robust autocomplete component with pagination, caching, and keyboard control.
 * Works with Symfony Encore or vanilla asset inclusion.
 * 2025-11 – Stable production version
 */
(() => {
    // -------------------------------------------------------------------------
    // Internal helper functions
    // -------------------------------------------------------------------------
    const utils = {
        /**
         * Compute TVA (INSEE) from SIREN
         */
        calculateTVA(siren) {
            const n = parseInt(siren, 10);
            if (isNaN(n)) return '';
            const check = String((97 - (n % 97))).padStart(2, '0');
            return 'FR' + check + siren;
        },

        /**
         * Normalize APE / NAF code
         */
        formatAPE(nafCode) {
            return (nafCode || '').replace(/\./g, '').toUpperCase();
        },

        /**
         * Resolve dotted or functional paths like "uniteLegale.denominationUniteLegale"
         * or "calculateTVA(siren)"
         */
        resolveKey(obj, keyPath) {
            if (!keyPath) return '';
            const functions = { calculateTVA: utils.calculateTVA, formatAPE: utils.formatAPE };

            return keyPath
                .split('_')
                .map(segment => {
                    const constMatch = segment.match(/^\[\[(.+)\]\]$/);
                    if (constMatch) return constMatch[1];

                    const fnMatch = segment.match(/^([$\w]+)\((.*)\)$/);
                    if (fnMatch) {
                        const [, fnName, argList] = fnMatch;
                        const fn = functions[fnName];
                        if (typeof fn !== 'function') return null;
                        const args = argList
                            .split(',')
                            .map(a => a.trim())
                            .filter(Boolean)
                            .map(a => {
                                const cm = a.match(/^\[\[(.+)\]\]$/);
                                if (cm) return cm[1];
                                return a.split('.').reduce((o, k) => (o == null ? null : o[k]), obj);
                            });
                        return fn(...args);
                    }

                    return segment
                        .split('.')
                        .reduce((o, k) => (o == null ? null : o[k]), obj);
                })
                .filter(v => v != null)
                .join(' ');
        },

        debounce(fn, wait = 300) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        },

        clear(el) {
            if (el) el.innerHTML = '';
        }
    };

    // -------------------------------------------------------------------------
    // DOM construction helpers
    // -------------------------------------------------------------------------
    const paginationState = new WeakMap();

    const buildOption = (item, keyMap) => {
        const el = document.createElement('div');
        el.className = 'autocomplete-item';
        el._item = item;
        el.textContent = item !== Object(item)
            ? item
            : utils.resolveKey(item, keyMap.value || 'value');
        return el;
    };

    const buildPagination = (page, total) => {
        const div = document.createElement('div');
        div.className = 'autocomplete-pagination';
        div.innerHTML = `
            <button type="button" class="first">⏮ Début</button>
            <button type="button" class="prev">← Précédent</button>
            <input type="number" min="1" value="${page}" max="${total}" class="autocomplete-page-input">
            <span class="autocomplete-page-info"> / ${total}</span>
            <button type="button" class="next">Suivant →</button>
            <button type="button" class="last">⏭ Fin</button>
        `;
        return div;
    };

    // -------------------------------------------------------------------------
    // Rendering and interaction
    // -------------------------------------------------------------------------
    function renderList(listEl, items, keyMap, page = 1, perPage = 10) {
        const totalPages = Math.ceil(items.length / perPage);
        if (!totalPages) return utils.clear(listEl);

        page = Math.max(1, Math.min(page, totalPages));
        paginationState.set(listEl, page);

        // Reset list
        utils.clear(listEl);
        listEl.classList.remove('fade-in');
        void listEl.offsetWidth;

        // Build fragment
        const frag = document.createDocumentFragment();
        const start = (page - 1) * perPage;
        const end = start + perPage;
        items.slice(start, end).forEach(it => frag.appendChild(buildOption(it, keyMap)));
        listEl.appendChild(frag);

        // Pagination
        const pagination = buildPagination(page, totalPages);
        listEl.appendChild(pagination);

        // Disable buttons
        pagination.querySelector('.first').disabled = page === 1;
        pagination.querySelector('.prev').disabled = page === 1;
        pagination.querySelector('.next').disabled = page === totalPages;
        pagination.querySelector('.last').disabled = page === totalPages;

        // Prevent bubbling to click handler
        pagination.addEventListener('click', e => e.stopPropagation());

        const gotoPage = newPage => {
            if (newPage < 1 || newPage > totalPages) return;
            renderList(listEl, items, keyMap, newPage, perPage);
        };

        // Wire controls
        pagination.querySelector('.first').onclick = () => gotoPage(1);
        pagination.querySelector('.prev').onclick = () => gotoPage(page - 1);
        pagination.querySelector('.next').onclick = () => gotoPage(page + 1);
        pagination.querySelector('.last').onclick = () => gotoPage(totalPages);

        const input = pagination.querySelector('.autocomplete-page-input');
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const newPage = parseInt(input.value, 10);
                if (newPage >= 1 && newPage <= totalPages) gotoPage(newPage);
                else input.value = page;
            }
        });

        // Fade animation
        requestAnimationFrame(() => listEl.classList.add('fade-in'));
        listEl.scrollTop = 0;
    }

    // -------------------------------------------------------------------------
    // Fill targets
    // -------------------------------------------------------------------------
    function fillTargets(item, keyMap, targetMap) {
        Object.entries(keyMap).forEach(([name, path]) => {
            if (name === 'value') return;
            const sel = targetMap[name];
            if (!sel) return;
            const els = Array.from(document.querySelectorAll(sel));
            if (!els.length) return;

            const val = utils.resolveKey(item, path);

            if (els[0].type === 'radio') {
                els.forEach(r => (r.checked = r.value === val));
            } else if ('value' in els[0]) {
                els.forEach(el => (el.value = val));
            } else {
                els.forEach(el => (el.textContent = val));
            }
        });
    }

    // -------------------------------------------------------------------------
    // Fetching and caching
    // -------------------------------------------------------------------------
    function makeFetcher(url, listEl, keyMap, cache, controllerRef, lastTermRef) {
        return term => {
            const q = term.trim();
            if (q.length < 3) return utils.clear(listEl);

            if (cache.has(q)) {
                lastTermRef.value = q;
                renderList(listEl, cache.get(q), keyMap, 1);
                return;
            }

            if (q === lastTermRef.value) return;

            lastTermRef.value = q;
            if (controllerRef.current) controllerRef.current.abort();
            controllerRef.current = new AbortController();

            fetch(url.replace(':query', encodeURIComponent(q)), { signal: controllerRef.current.signal })
                .then(r => (r.ok ? r.json() : []))
                .then(data => {
                    cache.set(q, Array.isArray(data) ? data : []);
                    renderList(listEl, cache.get(q), keyMap, 1);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') console.error('Autocomplete fetch error:', err);
                });
        };
    }

    // -------------------------------------------------------------------------
    // Initialization per input
    // -------------------------------------------------------------------------
    function initAutocomplete(input) {
        const url = input.dataset.sourceUrl;
        if (!url) return;

        const listEl = (() => {
            const div = document.createElement('div');
            div.classList.add('autocomplete-list');
            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(div);
            return div;
        })();

        const data = input.dataset;
        const keyMap = {};
        const targetMap = {};
        for (const [k, v] of Object.entries(data)) {
            if (k.startsWith('key')) keyMap[k.slice(3).toLowerCase()] = v;
            if (k.startsWith('target')) targetMap[k.slice(6).toLowerCase()] = v;
        }

        const cache = new Map();
        const controllerRef = { current: null };
        const lastTermRef = { value: '' };

        const fetcher = makeFetcher(url, listEl, keyMap, cache, controllerRef, lastTermRef);
        input.addEventListener('input', utils.debounce(() => fetcher(input.value), 300));

        // Keyboard navigation ← →
        input.addEventListener('keydown', e => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            const term = input.value.trim();
            const items = cache.get(term) || cache.get(lastTermRef.value);
            if (!items) return;

            const perPage = 10;
            const totalPages = Math.ceil(items.length / perPage);
            const page = paginationState.get(listEl) || 1;

            if (e.key === 'ArrowRight' && page < totalPages) {
                e.preventDefault();
                renderList(listEl, items, keyMap, page + 1, perPage);
            } else if (e.key === 'ArrowLeft' && page > 1) {
                e.preventDefault();
                renderList(listEl, items, keyMap, page - 1, perPage);
            }
        });

        // Dismiss list on outside click
        document.addEventListener('click', e => {
            if (!input.contains(e.target) && !listEl.contains(e.target)) utils.clear(listEl);
        });

        // Select suggestion
        listEl.addEventListener('click', e => {
            if (e.target.closest('.autocomplete-pagination')) return;
            const opt = e.target.closest('.autocomplete-item');
            if (!opt) return;
            input.value = opt.textContent;
            if (opt._item && typeof opt._item === 'object') fillTargets(opt._item, keyMap, targetMap);
            utils.clear(listEl);
        });
    }

    // -------------------------------------------------------------------------
    // Bootstrap
    // -------------------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.api-js-autocomplete').forEach(initAutocomplete);
    });
})();
