/**
 * Form controls: flatpickr dates, AutoNumeric money, noUiSlider, Tom Select.
 * All controllers implement init(el) / destroy(el) for registry.
 */

import { register } from './registry.js';
import flatpickr from 'flatpickr';
import { Vietnamese } from 'flatpickr/dist/l10n/vn.js';
import AutoNumeric from 'autonumeric';
import noUiSlider from 'nouislider';
import TomSelect from 'tom-select';

// -----------------------------------------------------------------------
// Date / Datetime / Time pickers
// -----------------------------------------------------------------------

const DATEPICKER_INSTANCES = new WeakMap();

register('[data-datepicker]', {
    init(el) {
        const mode = el.dataset.mode || 'date';
        const modeConfig = {
            date: { enableTime: false, noCalendar: false, dateFormat: 'Y-m-d', altFormat: 'd/m/Y' },
            datetime: { enableTime: true, time_24hr: true, noCalendar: false, dateFormat: 'Y-m-d H:i', altFormat: 'd/m/Y H:i' },
            time: { enableTime: true, time_24hr: true, noCalendar: true, dateFormat: 'H:i', altFormat: 'H:i' },
        };

        const instance = flatpickr(el, {
            ...(modeConfig[mode] || modeConfig.date),
            locale: Vietnamese,
            altInput: mode !== 'time',
            allowInput: true,
            minDate: el.dataset.minDate || undefined,
            maxDate: el.dataset.maxDate || undefined,
        });

        DATEPICKER_INSTANCES.set(el, instance);
    },
    destroy(el) {
        DATEPICKER_INSTANCES.get(el)?.destroy();
        DATEPICKER_INSTANCES.delete(el);
    },
});

// -----------------------------------------------------------------------
// AutoNumeric money fields
// -----------------------------------------------------------------------

const MONEY_INSTANCES = new WeakMap();

register('[data-money]', {
    init(el) {
        const instance = new AutoNumeric(el, {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            unformatOnSubmit: true,
            minimumValue: '0',
        });
        MONEY_INSTANCES.set(el, instance);
    },
    destroy(el) {
        MONEY_INSTANCES.get(el)?.remove();
        MONEY_INSTANCES.delete(el);
    },
});

// -----------------------------------------------------------------------
// noUiSlider range
// -----------------------------------------------------------------------

const SLIDER_INSTANCES = new WeakMap();

register('[data-range-slider]', {
    init(el) {
        const min = Number(el.dataset.min ?? 0);
        const max = Number(el.dataset.max ?? 100);
        const step = Number(el.dataset.step ?? 1);
        const from = Number(el.dataset.from ?? min);
        const isRange = 'to' in el.dataset;
        const to = isRange ? Number(el.dataset.to) : undefined;
        const hiddenName = el.dataset.name;
        const hiddenInput = hiddenName
            ? el.closest('div')?.querySelector(`input[name="${hiddenName}"]`)
            : null;

        const slider = noUiSlider.create(el, {
            start: isRange ? [from, to] : [from],
            connect: isRange ? [false, true, false] : [true, false],
            range: { min, max },
            step,
            format: {
                to: (v) => Math.round(v),
                from: (v) => Number(v),
            },
        });

        slider.on('update', (values) => {
            if (hiddenInput) {
                hiddenInput.value = isRange ? values.join(',') : values[0];
            }
        });

        SLIDER_INSTANCES.set(el, slider);
    },
    destroy(el) {
        SLIDER_INSTANCES.get(el)?.destroy();
        SLIDER_INSTANCES.delete(el);
    },
});

// -----------------------------------------------------------------------
// Tom Select
// -----------------------------------------------------------------------

const TOM_SELECT_INSTANCES = new WeakMap();

register('[data-select-search]', {
    init(el) {
        const sourceUrl = el.dataset.source;
        const isRemote = Boolean(sourceUrl);

        const options = {
            plugins: el.multiple ? ['remove_button'] : [],
            placeholder: el.querySelector('option[value=""]')?.textContent || '— Tìm kiếm —',
            create: false,
            valueField: 'id',
            labelField: 'text',
            searchField: ['text'],
            shouldLoad: (q) => q.length >= 2,
            load: isRemote
                ? (query, callback) => {
                    fetch(`${sourceUrl}?q=${encodeURIComponent(query)}`)
                        .then((r) => r.json())
                        .then((data) => callback(data.results || []))
                        .catch(() => callback([]));
                }
                : undefined,
            items: el.value ? [el.value] : [],
        };

        const instance = new TomSelect(el, options);
        TOM_SELECT_INSTANCES.set(el, instance);
    },
    destroy(el) {
        TOM_SELECT_INSTANCES.get(el)?.destroy();
        TOM_SELECT_INSTANCES.delete(el);
    },
});
