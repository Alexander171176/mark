<script setup>
import {
    ref,
    onMounted,
    onUnmounted,
    watch,
} from 'vue'

import { useI18n } from 'vue-i18n'

import {
    Chart,
    LineController,
    LineElement,
    Filler,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend,
} from 'chart.js'

import '@/utils/ChartjsConfig'
import { tailwindConfig } from '@/utils/Utils'

Chart.register(
    LineController,
    LineElement,
    Filler,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend
)

const { t, locale } = useI18n()

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: '',
    },
    width: {
        type: [Number, String],
        default: 600,
    },
    height: {
        type: [Number, String],
        default: 400,
    },
})

const canvas = ref(null)

let chart = null

/*
|--------------------------------------------------------------------------
| Данные
|--------------------------------------------------------------------------
*/

// Подготовить локализованную подпись
const translatedLabel = (item) => {
    if (item.translate) {
        return t(item.label)
    }

    return item.label
}

// Подготовить подпись элемента
const itemLabel = (item) => {
    const label = translatedLabel(item)

    if (
        item.id !== null &&
        item.id !== undefined
    ) {
        return `ID ${item.id}: ${label}`
    }

    return label
}

// Отсортировать элементы
const sortedItems = () => {
    return [...props.items].sort(
        (a, b) => {
            if (
                a.id !== null &&
                a.id !== undefined &&
                b.id !== null &&
                b.id !== undefined
            ) {
                return a.id - b.id
            }

            return String(
                translatedLabel(a)
            ).localeCompare(
                String(
                    translatedLabel(b)
                ),
                locale.value
            )
        }
    )
}

// Подготовить данные графика
const chartData = () => {
    const items = sortedItems()

    return {
        labels: items.map(
            item => itemLabel(item)
        ),

        datasets: [
            {
                label: props.title,

                data: items.map(
                    item =>
                        Number(item.value) ||
                        0
                ),

                fill: false,

                borderColor:
                    tailwindConfig()
                        .theme
                        .colors
                        .blue[500],

                backgroundColor:
                    tailwindConfig()
                        .theme
                        .colors
                        .blue[100],

                tension: 0.4,

                pointRadius: 4,

                pointBackgroundColor:
                    tailwindConfig()
                        .theme
                        .colors
                        .blue[500],
            },
        ],
    }
}

/*
|--------------------------------------------------------------------------
| Chart
|--------------------------------------------------------------------------
*/

// Уничтожить график
const destroyChart = () => {
    if (!chart) {
        return
    }

    chart.destroy()

    chart = null
}

// Создать график
const createChart = () => {
    destroyChart()

    if (
        !canvas.value ||
        !props.items.length
    ) {
        return
    }

    chart = new Chart(
        canvas.value,
        {
            type: 'line',

            data: chartData(),

            options: {
                responsive: true,

                maintainAspectRatio: false,

                layout: {
                    padding: 20,
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        title: {
                            display: true,
                            text: t('value'),
                        },
                    },

                    x: {
                        title: {
                            display: true,
                            text: t('entities'),
                        },
                    },
                },

                plugins: {
                    legend: {
                        display: true,
                    },

                    tooltip: {
                        callbacks: {
                            title: context =>
                                context[0]
                                    ?.label || '',

                            label: context =>
                                `${context.dataset.label}: ${context.parsed.y}`,
                        },
                    },
                },
            },
        }
    )
}

onMounted(
    createChart
)

onUnmounted(
    destroyChart
)

watch(
    [
        () => props.items,
        () => props.title,
        locale,
    ],
    createChart,
    {
        deep: true,
    }
)
</script>

<template>
    <div class="grow">
        <canvas
            ref="canvas"
            :width="width"
            :height="height"
        ></canvas>
    </div>
</template>

<style scoped>
canvas {
    max-height: 400px;
}
</style>
