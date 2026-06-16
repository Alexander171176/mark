<script setup>
import { defineProps, ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router, useForm } from '@inertiajs/vue3';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue';

const { t } = useI18n();

const props = defineProps({
    log: String,
    files: Array,
    selectedFile: String,
});

const clearForm = useForm({});
const searchQuery = ref('');

const logLines = computed(() => props.log.split('\n'));

const filteredLines = computed(() => {
    if (!searchQuery.value) return logLines.value;
    return logLines.value.filter(line => line.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const changeFile = (fileKey) => {
    router.get(route('admin.logs.index', { file: fileKey }));
};

const clearLog = () => {
    if (confirm('Очистить выбранный лог?')) {
        clearForm.delete(route('admin.logs.clear', { file: props.selectedFile }));
    }
};

const getLineColor = (line) => {
    if (/ERROR|exception|critical/i.test(line)) return 'text-red-500 dark:text-red-200 font-bold';
    if (/WARN|warning/i.test(line)) return 'text-yellow-600 font-semibold';
    if (/INFO/i.test(line)) return 'text-indigo-800 dark:text-indigo-200';
    if (/DEBUG/i.test(line)) return 'text-fuchsia-800 dark:text-fuchsia-200 font-bold';
    return 'text-gray-900 dark:text-gray-100';
};

const copyLog = async () => {
    const text = filteredLines.value.join('\n')

    if (!text.trim()) {
        alert('Нет данных для копирования.')
        return
    }

    try {
        await navigator.clipboard.writeText(text)
        alert('Лог скопирован в буфер обмена.')
    } catch (error) {
        console.error(error)
        alert('Не удалось скопировать лог.')
    }
}

const downloadLog = () => {
    window.location.href = route('admin.logs.download', { file: props.selectedFile });
};
</script>

<template>
    <AdminLayout :title="t('logs')">
        <template #header>
            <TitlePage>{{ t('logs') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500
                        dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">

                <div class="p-6 bg-slate-100 dark:bg-slate-600 rounded shadow">

                    <h1 class="text-slate-700 dark:text-slate-100
                               text-center text-xl font-semibold mb-4">
                        {{ t('viewingLogs') }}
                    </h1>

                    <div class="flex flex-wrap gap-4 mb-4">

                        <select @change="e => changeFile(e.target.value)"
                                :value="props.selectedFile"
                                class="w-32 border rounded-sm h-8 py-0 px-2
                                       bg-slate-100 dark:bg-slate-900 font-semibold
                                       text-slate-700 dark:text-slate-50">
                            <option
                                v-for="file in props.files"
                                :key="file"
                                :value="file"
                                class="text-md">
                                {{ file }}
                            </option>
                        </select>

                        <input v-model="searchQuery"
                               :placeholder="`${ t('search') }...`"
                               class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-50
                                      border rounded-sm h-8 py-0 px-2 flex-1 font-semibold" />

                        <button @click="clearLog"
                                :disabled="clearForm.processing"
                                class="h-8 py-0 px-2 bg-rose-500 text-white rounded-sm
                                       hover:bg-amber-500 disabled:opacity-50"
                                :title="t('clearLog')">
                            <svg
                                class="h-4 w-4 fill-current"
                                viewBox="0 0 512 512">
                                <path d="M10.8 247.2C-.7 251.8-3.7 266.7 5 275.4l54.8 54.8 73.2-24.4-24.4 73.2 128 128c8.8 8.8 23.6 5.7 28.2-5.8l98.1-243.7-108.4-108.4-243.7 98.1zM507.3 27.3L484.7 4.7c-6.2-6.3-16.4-6.3-22.6 0L359.8 106.9l-40.4-40.4c-4.2-4.3-11.4-3-13.9 2.5l-27.2 58.6 106.1 106.1 58.6-27.2c5.4-2.5 6.7-9.7 2.5-13.9l-40.4-40.4L507.3 49.9c6.3-6.2 6.3-16.3 0-22.6z" />
                            </svg>
                        </button>

                        <button
                            @click="copyLog"
                            class="h-8 py-0 px-2 bg-blue-500 text-white rounded-sm
                                   hover:bg-indigo-500"
                            :title="t('copy')"
                        >
                            <svg
                                class="h-4 w-4 fill-current"
                                viewBox="0 0 512 512"
                            >
                                <path d="M464 0c26.51 0 48 21.49 48 48v288c0 26.51-21.49 48-48 48H176c-26.51 0-48-21.49-48-48V48c0-26.51 21.49-48 48-48h288M176 416c-44.112 0-80-35.888-80-80V128H48c-26.51 0-48 21.49-48 48v288c0 26.51 21.49 48 48 48h288c26.51 0 48-21.49 48-48v-48H176z" />
                            </svg>
                        </button>

                        <button @click="downloadLog"
                                class="h-8 py-0 px-2 bg-green-500 text-white rounded-sm
                                hover:bg-teal-500" :title="t('download')">
                            <svg
                                class="h-4 w-4 fill-current"
                                viewBox="0 0 384 512">
                                <path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z" />
                            </svg>
                        </button>
                    </div>

                    <div class="border rounded p-4 bg-gray-50 dark:bg-gray-700
                                overflow-auto max-h-[600px] text-sm font-mono">
                        <div v-if="filteredLines.length === 0"
                             class="text-slate-700 dark:text-slate-100">
                            {{ t('noData') }}
                        </div>
                        <template v-else>
                            <div
                                v-for="(line, idx) in filteredLines"
                                :key="idx"
                                :id="'line-' + idx"
                                :class="['group flex hover:bg-yellow-100 dark:hover:bg-slate-900 transition-colors duration-200', getLineColor(line)]"
                            >
                                <span class="font-semibold text-blue-500 dark:text-blue-200
                                             inline-block w-12 text-right mr-2 select-none">
                                    {{ idx + 1 }}.
                                </span>
                                <pre class="whitespace-pre-wrap flex-1">{{ line }}</pre>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
