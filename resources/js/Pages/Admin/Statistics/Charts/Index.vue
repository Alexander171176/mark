<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue';
import EntityBarChart from '@/Components/Admin/Statistic/Chart/Entity/EntityBarChart001.vue';
import EntityLineChart from '@/Components/Admin/Statistic/Chart/Entity/EntityLineChart001.vue';

const { t } = useI18n();

const props = defineProps({
    entities: {
        type: Array,
        default: () => []
    },
    metrics: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    chart: {
        type: Object,
        default: () => ({
            title: '',
            data: []
        })
    }
});

const selectedEntity = ref(props.filters.entity || '');
const selectedMetric = ref(props.filters.metric || '');

// Изменить сущность
const changeEntity = () => {
    router.get(route('admin.charts.index'), {
        entity: selectedEntity.value,
    }, {
        preserveScroll: true,
        preserveState: false,
    });
};

// Изменить метрику
const changeMetric = () => {
    router.get(route('admin.charts.index'), {
        entity: selectedEntity.value,
        metric: selectedMetric.value,
    }, {
        preserveScroll: true,
        preserveState: false,
    });
};
</script>

<template>
    <AdminLayout :title="t('charts')">
        <template #header>
            <TitlePage>
                {{ t('charts') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
                    <select
                        v-model="selectedEntity"
                        @change="changeEntity"
                        class="w-56 px-3 py-1 bg-white dark:bg-gray-200
                               text-gray-700 dark:text-gray-900 border border-slate-400
                               dark:border-slate-600 rounded-sm shadow-sm"
                    >
                        <option
                            v-for="entity in props.entities"
                            :key="entity.value"
                            :value="entity.value"
                        >
                            {{ entity.label }}
                        </option>
                    </select>

                    <select
                        v-model="selectedMetric"
                        @change="changeMetric"
                        class="w-56 px-3 py-1 bg-white dark:bg-gray-200
                               text-gray-700 dark:text-gray-900 border border-slate-400
                               dark:border-slate-600 rounded-sm shadow-sm"
                    >
                        <option
                            v-for="metric in props.metrics"
                            :key="metric.value"
                            :value="metric.value"
                        >
                            {{ metric.label }}
                        </option>
                    </select>
                </div>

                <h2 class="text-center text-lg font-semibold text-slate-700 dark:text-slate-100 mb-4">
                    {{ props.chart.title }}
                </h2>

                <div v-if="!props.chart.data || !props.chart.data.length" class="text-center text-gray-500">
                    {{ t('noData') }}
                </div>

                <div v-else>
                    <div class="p-3 bg-white dark:bg-slate-800 border rounded shadow">
                        <EntityBarChart
                            :items="props.chart.data"
                            :title="props.chart.title"
                            :height="400"
                        />
                    </div>

                    <div class="p-3 bg-white dark:bg-slate-800 border rounded shadow">
                        <EntityLineChart
                            :items="props.chart.data"
                            :title="props.chart.title"
                            :height="400"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
