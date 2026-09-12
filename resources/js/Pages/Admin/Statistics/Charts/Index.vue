<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import EntityBarChart from '@/Components/Admin/Statistic/Chart/Entity/EntityBarChart001.vue'
import EntityLineChart from '@/Components/Admin/Statistic/Chart/Entity/EntityLineChart001.vue'

const { t } = useI18n()

const props = defineProps({
    entities: {
        type: Array,
        default: () => [],
    },
    metrics: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    chart: {
        type: Object,
        default: () => ({
            entity_label_key: '',
            metric_label_key: '',
            entity: '',
            metric: '',
            data: [],
            pagination: null,
        }),
    },
})

const selectedEntity = ref(
    props.filters.entity || ''
)

const selectedMetric = ref(
    props.filters.metric || ''
)

/*
|--------------------------------------------------------------------------
| Названия графиков
|--------------------------------------------------------------------------
*/

const chartTitle = computed(() => {
    const entity = props.chart.entity_label_key
        ? t(props.chart.entity_label_key)
        : ''

    const metric = props.chart.metric_label_key
        ? t(props.chart.metric_label_key)
        : ''

    if (!entity) {
        return metric
    }

    if (!metric) {
        return entity
    }

    return `${entity} — ${metric}`
})

/*
|--------------------------------------------------------------------------
| Пагинация
|--------------------------------------------------------------------------
*/

const pagination = computed(() => {
    return props.chart.pagination || null
})

// Переключить страницу
const changePage = (page) => {
    if (
        !pagination.value ||
        page < 1 ||
        page > pagination.value.last_page ||
        page === pagination.value.current_page
    ) {
        return
    }

    router.get(
        route('admin.charts.index'),
        {
            entity: selectedEntity.value,
            metric: selectedMetric.value,
            page,
        },
        {
            preserveScroll: true,
            preserveState: false,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Общие классы
|--------------------------------------------------------------------------
*/

const selectClass =
    'w-56 px-3 py-1 border border-slate-500 ' +
    'font-semibold text-sm rounded-sm shadow-sm ' +
    'focus:border-indigo-500 focus:ring-indigo-300 ' +
    'dark:bg-cyan-800 dark:text-slate-100'

const paginationButtonClass =
    'px-3 py-1 border border-slate-400 ' +
    'font-semibold text-sm rounded-sm shadow-sm ' +
    'bg-white text-slate-700 ' +
    'hover:bg-slate-100 ' +
    'disabled:opacity-40 disabled:cursor-not-allowed ' +
    'dark:bg-slate-800 dark:text-slate-100 ' +
    'dark:hover:bg-slate-700'

/*
|--------------------------------------------------------------------------
| Фильтры
|--------------------------------------------------------------------------
*/

// Изменить сущность
const changeEntity = () => {
    router.get(
        route('admin.charts.index'),
        {
            entity: selectedEntity.value,
        },
        {
            preserveScroll: true,
            preserveState: false,
        }
    )
}

// Изменить метрику
const changeMetric = () => {
    router.get(
        route('admin.charts.index'),
        {
            entity: selectedEntity.value,
            metric: selectedMetric.value,
        },
        {
            preserveScroll: true,
            preserveState: false,
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('charts')">
        <template #header>
            <TitlePage>
                {{ t('charts') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Фильтры -->
                <div
                    class="flex flex-wrap gap-2
                           items-center justify-between mb-3"
                >
                    <select
                        v-model="selectedEntity"
                        :class="selectClass"
                        @change="changeEntity"
                    >
                        <option
                            v-for="entity in props.entities"
                            :key="entity.value"
                            :value="entity.value"
                        >
                            {{ t(entity.label_key) }}
                        </option>
                    </select>

                    <select
                        v-model="selectedMetric"
                        :class="selectClass"
                        @change="changeMetric"
                    >
                        <option
                            v-for="metric in props.metrics"
                            :key="metric.value"
                            :value="metric.value"
                        >
                            {{ t(metric.label_key) }}
                        </option>
                    </select>
                </div>

                <!-- Заголовок -->
                <h2
                    class="text-center text-lg font-semibold
                           text-slate-700 dark:text-slate-100
                           mb-3"
                >
                    {{ chartTitle }}
                </h2>

                <!-- Нет данных -->
                <div
                    v-if="
                        !props.chart.data ||
                        !props.chart.data.length
                    "
                    class="text-center text-gray-500
                           dark:text-gray-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Графики -->
                <div
                    v-else
                    class="space-y-3"
                >
                    <div
                        class="p-3 bg-white dark:bg-slate-800
                               border border-slate-400
                               rounded shadow"
                    >
                        <EntityBarChart
                            :items="props.chart.data"
                            :title="chartTitle"
                            :height="400"
                        />
                    </div>

                    <div
                        class="p-3 bg-white dark:bg-slate-800
                               border border-slate-400
                               rounded shadow"
                    >
                        <EntityLineChart
                            :items="props.chart.data"
                            :title="chartTitle"
                            :height="400"
                        />
                    </div>

                    <!-- Пагинация -->
                    <div
                        v-if="
                            pagination &&
                            pagination.last_page > 1
                        "
                        class="flex flex-wrap gap-2
                               items-center justify-center
                               pt-2"
                    >
                        <button
                            type="button"
                            :class="paginationButtonClass"
                            :disabled="
                                pagination.current_page <= 1
                            "
                            @click="
                                changePage(
                                    pagination.current_page - 1
                                )
                            "
                        >
                            ←
                        </button>

                        <div
                            class="text-sm font-semibold
                                   text-slate-700
                                   dark:text-slate-100"
                        >
                            {{ pagination.from }}
                            –
                            {{ pagination.to }}
                            /
                            {{ pagination.total }}

                            <span class="mx-2">
                                ·
                            </span>

                            {{ pagination.current_page }}
                            /
                            {{ pagination.last_page }}
                        </div>

                        <button
                            type="button"
                            :class="paginationButtonClass"
                            :disabled="
                                pagination.current_page >=
                                pagination.last_page
                            "
                            @click="
                                changePage(
                                    pagination.current_page + 1
                                )
                            "
                        >
                            →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
