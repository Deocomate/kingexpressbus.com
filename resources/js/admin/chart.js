/**
 * Chart.js wrapper with destroy() support for DOM swaps.
 * Dynamic import to keep initial bundle smaller.
 */

import { register } from './registry.js';

const CHART_INSTANCES = new WeakMap();

register('[data-chart]', {
    async init(el) {
        const { Chart, registerables } = await import('chart.js');
        Chart.register(...registerables);

        const canvas = el.querySelector('canvas');
        if (!canvas) return;

        const type = el.dataset.chartType || 'line';
        let data = {};
        let options = {};

        try {
            data = JSON.parse(el.dataset.chartData || '{}');
            options = JSON.parse(el.dataset.chartOptions || '{}');
        } catch (e) {
            console.error('[chart] failed to parse data/options', e);
        }

        const chart = new Chart(canvas, {
            type,
            data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                ...options,
            },
        });

        CHART_INSTANCES.set(el, chart);
    },
    destroy(el) {
        CHART_INSTANCES.get(el)?.destroy();
        CHART_INSTANCES.delete(el);
    },
});
