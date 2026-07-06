<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const { t } = useI18n()

const props = defineProps({
    visitorLogs: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const filters = ref({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    module: props.filters.module || '',
    event_type: props.filters.event_type || '',
    user_id: props.filters.user_id || '',
    visitor_uuid: props.filters.visitor_uuid || '',
    url: props.filters.url || '',
    country: props.filters.country || '',
    city: props.filters.city || '',
    device_type: props.filters.device_type || '',
    browser: props.filters.browser || '',
    os: props.filters.os || '',
    per_page: props.filters.per_page || '50',
    sort_direction: props.filters.sort_direction || 'desc',
})

const applyFilters = () => {
    router.get(route('admin.analyticsVisitorLogs.index'), filters.value, {
        preserveScroll: true,
        preserveState: true,
    })
}

const resetFilters = () => {
    router.get(route('admin.analyticsVisitorLogs.index'), {}, {
        preserveScroll: true,
        preserveState: false,
    })
}

const formatDate = (value) => {
    if (!value) {
        return '—'
    }

    return new Date(value).toLocaleString()
}
</script>

<template>
    <AdminLayout title="Analytics Visitor Logs">
        <template #header>
            <TitlePage>
                {{ t('analyticsLogs') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">

                    <input v-model="filters.date_from" type="date"
                           class="px-2 py-1 border border-slate-500 font-semibold text-sm
                                  focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                                  shadow-sm dark:bg-cyan-800 dark:text-slate-100" />

                    <input v-model="filters.date_to" type="date"
                           class="px-2 py-1 border border-slate-500 font-semibold text-sm
                                  focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                                  shadow-sm dark:bg-cyan-800 dark:text-slate-100" />

                    <input v-model="filters.module" type="text" placeholder="module"
                           class="px-2 py-1 border border-slate-500 font-semibold text-sm
                                  focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                                  shadow-sm dark:bg-cyan-800 dark:text-slate-100" />

                    <input v-model="filters.event_type" type="text" placeholder="event_type"
                           class="px-2 py-1 border border-slate-500 font-semibold text-sm
                                  focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                                  shadow-sm dark:bg-cyan-800 dark:text-slate-100" />

                    <input v-model="filters.user_id" type="text" placeholder="user_id"
                           class="px-2 py-1 border border-slate-500 font-semibold text-sm
                                  focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                                  shadow-sm dark:bg-cyan-800 dark:text-slate-100" />

                    <input v-model="filters.url" type="text" placeholder="url"
                           class="px-2 py-1 border border-slate-500 font-semibold text-sm
                                  focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                                  shadow-sm dark:bg-cyan-800 dark:text-slate-100" />
                </div>

                <div class="flex items-center justify-between gap-2 mb-4">

                    <select
                        v-model="filters.per_page"
                        @change="applyFilters"
                        class="px-7 py-0.5 border border-slate-500 font-semibold text-sm
                               focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                               shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                    </select>

                    <div class="flex items-center justify-center gap-2">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="px-2 py-0.5 rounded flex items-center justify-center gap-2
                               bg-blue-600 hover:bg-blue-700"
                        >
                            <svg class="shrink-0 h-3 w-3"
                                 viewBox="0 0 512 512">
                                <path class="fill-current text-white"
                                      d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-white">
                            {{ t('filter') }}
                        </span>
                        </button>

                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-2 py-0.5 rounded flex items-center justify-center gap-2
                               border border-gray-400 dark:border-gray-500
                               bg-slate-200 hover:bg-slate-300
                               dark:bg-slate-800 dark:hover:bg-slate-900"
                        >
                            <svg class="shrink-0 h-4 w-4"
                                 viewBox="0 0 352 512">
                                <path class="fill-current text-slate-700 dark:text-slate-300"
                                      d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ t('resetFilter') }}
                        </span>
                        </button>
                    </div>

                    <select
                        v-model="filters.sort_direction"
                        @change="applyFilters"
                        class="px-7 py-0.5 border border-slate-500 font-semibold text-sm
                               focus:border-indigo-500 focus:ring-indigo-300 rounded-sm
                               shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                    >
                        <option value="desc">ID ↓</option>
                        <option value="asc">ID ↑</option>
                    </select>
                </div>

                <div class="overflow-x-auto bg-white dark:bg-slate-800 border rounded">
                    <table class="min-w-full text-xs border border-gray-400">
                        <thead class="bg-gray-100 dark:bg-slate-900
                                      text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">Дата</th>
                            <th class="px-2 py-2 text-left">User</th>
                            <th class="px-2 py-2 text-left">Module</th>
                            <th class="px-2 py-2 text-left">Event</th>
                            <th class="px-2 py-2 text-left">Title</th>
                            <th class="px-2 py-2 text-left">URL</th>
                            <th class="px-2 py-2 text-left">Time</th>
                            <th class="px-2 py-2 text-left">Scroll</th>
                            <th class="px-2 py-2 text-left">Clicks</th>
                            <th class="px-2 py-2 text-left">Show</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr
                            v-for="log in props.visitorLogs.data"
                            :key="log.id"
                            class="border-t dark:border-slate-700 text-gray-700 dark:text-gray-200"
                        >
                            <td class="px-2 py-2">
                                {{ log.id }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                {{ formatDate(log.visited_at) }}
                            </td>
                            <td class="px-2 py-2">
                                {{ log.user_id || 'guest' }}
                            </td>
                            <td class="px-2 py-2">
                                {{ log.module || '—' }}
                            </td>
                            <td class="px-2 py-2">
                                {{ log.event_type || '—' }}
                            </td>
                            <td class="px-2 py-2 max-w-xs truncate">
                                {{ log.page_title || '—' }}
                            </td>
                            <td class="px-2 py-2 max-w-md truncate">
                                {{ log.url || '—' }}
                            </td>
                            <td class="px-2 py-2">
                                {{ log.time_on_page ?? '—' }}
                            </td>
                            <td class="px-2 py-2">
                                {{ log.scroll_depth ?? '—' }}
                            </td>
                            <td class="px-2 py-2">
                                {{ log.clicks_count ?? 0 }}
                            </td>
                            <td class="px-2 py-2">
                                <Link
                                    :href="route('admin.analyticsVisitorLogs.show', log.id)"
                                    :title="t('open')"
                                    class="flex items-center justify-center"
                                >
                                    <svg class="shrink-0 h-4 w-4"
                                         viewBox="0 0 576 512">
                                        <path class="fill-current text-blue-600 dark:text-blue-400"
                                            d="M576 24v127.984c0 21.461-25.96 31.98-40.971 16.971l-35.707-35.709-243.523 243.523c-9.373 9.373-24.568 9.373-33.941 0l-22.627-22.627c-9.373-9.373-9.373-24.569 0-33.941L442.756 76.676l-35.703-35.705C391.982 25.9 402.656 0 424.024 0H552c13.255 0 24 10.745 24 24zM407.029 270.794l-16 16A23.999 23.999 0 0 0 384 303.765V448H64V128h264a24.003 24.003 0 0 0 16.97-7.029l16-16C376.089 89.851 365.381 64 344 64H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V287.764c0-21.382-25.852-32.09-40.971-16.97z" />
                                    </svg>
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="!props.visitorLogs.data || !props.visitorLogs.data.length">
                            <td colspan="11" class="px-2 py-6 text-center text-gray-500">
                                {{ t('noData') }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-center flex-wrap gap-2 mt-4">
                    <Link
                        :href="props.visitorLogs.links?.prev || '#'"
                        class="px-3 py-1 text-xs border rounded"
                        :class="{
        'text-slate-700 dark:text-slate-100 pointer-events-none': !props.visitorLogs.links?.prev,
        'bg-sky-200 dark:bg-cyan-800': props.visitorLogs.links?.prev,
                        }"
                    >
                        ← {{ t('previous') }}
                    </Link>

                    <Link
                        :href="props.visitorLogs.links?.next || '#'"
                        class="px-3 py-1 text-xs border rounded"
                        :class="{
        'text-slate-700 dark:text-slate-100 pointer-events-none': !props.visitorLogs.links?.next,
        'bg-sky-200 dark:bg-cyan-800': props.visitorLogs.links?.next,
                        }"
                    >
                        {{ t('next') }} →
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
