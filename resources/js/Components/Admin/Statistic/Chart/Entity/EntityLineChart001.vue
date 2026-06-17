<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
    Chart, LineController, LineElement, Filler, PointElement,
    LinearScale, CategoryScale, Tooltip, Legend
} from 'chart.js';

import '@/utils/ChartjsConfig';
import { tailwindConfig } from '@/utils/Utils';

Chart.register(LineController, LineElement, Filler, PointElement, LinearScale, CategoryScale, Tooltip, Legend);

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    },
    title: {
        type: String,
        default: ''
    },
    width: {
        type: [Number, String],
        default: 600
    },
    height: {
        type: [Number, String],
        default: 400
    }
});

const canvas = ref(null);
let chart = null;

// Отсортировать элементы
const sortedItems = () => {
    return [...props.items].sort((a, b) => {
        if (a.id && b.id) return a.id - b.id;

        return String(a.label).localeCompare(String(b.label));
    });
};

// Подготовить подпись
const itemLabel = (item) => {
    return item.id ? `ID ${item.id}: ${item.label}` : item.label;
};

// Подготовить данные
const chartData = () => {
    const items = sortedItems();

    return {
        labels: items.map(item => itemLabel(item)),
        datasets: [
            {
                label: props.title,
                data: items.map(item => item.value || 0),
                fill: false,
                borderColor: tailwindConfig().theme.colors.blue[500],
                backgroundColor: tailwindConfig().theme.colors.blue[100],
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: tailwindConfig().theme.colors.blue[500]
            }
        ]
    };
};

// Создать график
const createChart = () => {
    if (!canvas.value || !props.items.length) return;

    if (chart) chart.destroy();

    chart = new Chart(canvas.value, {
        type: 'line',
        data: chartData(),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 20
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Значение'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Сущности'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        title: ctx => ctx[0]?.label || '',
                        label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y}`
                    }
                }
            }
        }
    });
};

onMounted(createChart);
onUnmounted(() => chart && chart.destroy());

watch(() => props.items, createChart, {
    deep: true
});
</script>

<template>
    <div class="grow">
        <canvas ref="canvas" :width="width" :height="height"></canvas>
    </div>
</template>

<style scoped>
canvas {
    max-height: 400px;
}
</style>
