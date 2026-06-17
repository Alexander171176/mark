<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
import {
    Chart, BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend
} from 'chart.js';

import '@/utils/ChartjsConfig';
import { tailwindConfig } from '@/utils/Utils';

Chart.register(BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend);

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

// Подготовить подпись
const itemLabel = (item) => {
    return item.id ? `ID ${item.id}: ${item.label}` : item.label;
};

// Подготовить данные
const chartData = () => {
    const labels = props.items.map(item => itemLabel(item));
    const values = props.items.map(item => item.value || 0);

    return {
        labels,
        datasets: [
            {
                label: props.title,
                data: values,
                backgroundColor: tailwindConfig().theme.colors.blue[400],
                borderRadius: 6,
                barThickness: 28
            }
        ]
    };
};

// Создать график
const createChart = () => {
    if (!canvas.value || !props.items.length) return;

    if (chart) chart.destroy();

    chart = new Chart(canvas.value, {
        type: 'bar',
        data: chartData(),
        options: {
            layout: {
                padding: 20,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
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
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: ctx => ctx[0]?.label || '',
                        label: ctx => `Значение: ${ctx.raw}`
                    }
                }
            },
            maintainAspectRatio: false,
            resizeDelay: 200,
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
