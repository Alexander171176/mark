<script setup>
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import { useTableExport } from '@/composables/useTableExport'

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

/*
|--------------------------------------------------------------------------
| Фильтры
|--------------------------------------------------------------------------
*/

const filters = ref({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    module: props.filters.module || '',
    event_type: props.filters.event_type || '',
    user_id: props.filters.user_id || '',
    visitor_uuid: props.filters.visitor_uuid || '',
    url: props.filters.url || '',
    per_page: props.filters.per_page || '50',
    sort_direction: props.filters.sort_direction || 'desc',
})

const applyFilters = () => {
    router.get(
        route('admin.analyticsVisitorLogs.index'),
        {
            ...filters.value,
            page: 1,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    )
}

const resetFilters = () => {
    router.get(
        route('admin.analyticsVisitorLogs.index'),
        {},
        {
            preserveScroll: true,
            preserveState: false,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Пагинация
|--------------------------------------------------------------------------
*/

const currentPage = computed(() => {
    return Number(props.visitorLogs.meta?.current_page || 1)
})

const lastPage = computed(() => {
    return Number(props.visitorLogs.meta?.last_page || 1)
})

const pageInput = ref(currentPage.value)

watch(currentPage, (value) => {
    pageInput.value = value
})

const goToPage = (value = pageInput.value) => {
    let page = Number.parseInt(value, 10)

    if (!Number.isInteger(page)) {
        page = currentPage.value
    }

    page = Math.max(1, page)
    page = Math.min(page, lastPage.value)

    pageInput.value = page

    if (page === currentPage.value) {
        return
    }

    router.get(
        route('admin.analyticsVisitorLogs.index'),
        {
            ...filters.value,
            page,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    )
}

const previousPage = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(currentPage.value - 1)
}

const nextPage = () => {
    if (currentPage.value >= lastPage.value) {
        return
    }

    goToPage(currentPage.value + 1)
}

/*
|--------------------------------------------------------------------------
| Форматирование
|--------------------------------------------------------------------------
*/

const formatDate = (value) => {
    if (!value) {
        return '—'
    }

    return new Date(value).toLocaleString()
}

/*
|--------------------------------------------------------------------------
| Импорт аналитики
|--------------------------------------------------------------------------
*/

const importAnalytics = () => {
    router.post(
        route('admin.analytics.import'),
        {},
        {
            preserveScroll: true,
            preserveState: false,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Экспорт
|--------------------------------------------------------------------------
*/

const { download } = useTableExport()

const exportColumns = [
    'id',
    'visited_at',
    'user_id',
    'visitor_uuid',
    'module',
    'entity_type',
    'entity_id',
    'event_type',
    'page_title',
    'url',
    'referer',
    'time_on_page',
    'scroll_depth',
    'clicks_count',
    'locale',
]

const exportItems = computed(() => {
    return props.visitorLogs.data || []
})

const downloadAnalytics = (format) => {
    download(
        format,
        exportItems.value,
        exportColumns,
        'analytics_visitor_logs',
        'Analytics Visitor Logs'
    )
}

/*
|--------------------------------------------------------------------------
| Очистка аналитики
|--------------------------------------------------------------------------
*/

const cleanupAnalytics = () => {
    if (!filters.value.date_from || !filters.value.date_to) {
        alert('Выберите период очистки.')
        return
    }

    if (!confirm('Удалить аналитику за выбранный период?')) {
        return
    }

    router.delete(
        route('admin.analytics.cleanup.destroy'),
        {
            data: {
                date_from: filters.value.date_from,
                date_to: filters.value.date_to,
            },
            preserveScroll: true,
            preserveState: false,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Двойной горизонтальный скролл
|--------------------------------------------------------------------------
*/

const topScroll = ref(null)
const tableScroll = ref(null)
const tableContent = ref(null)
const tableScrollWidth = ref(0)

let resizeObserver = null

const updateTableScrollWidth = async () => {
    await nextTick()

    tableScrollWidth.value =
        tableContent.value?.scrollWidth || 0
}

const syncTopScroll = () => {
    if (!topScroll.value || !tableScroll.value) {
        return
    }

    topScroll.value.scrollLeft =
        tableScroll.value.scrollLeft
}

const syncTableScroll = () => {
    if (!topScroll.value || !tableScroll.value) {
        return
    }

    tableScroll.value.scrollLeft =
        topScroll.value.scrollLeft
}

onMounted(async () => {
    await updateTableScrollWidth()

    if (tableContent.value) {
        resizeObserver = new ResizeObserver(() => {
            updateTableScrollWidth()
        })

        resizeObserver.observe(tableContent.value)
    }
})

onBeforeUnmount(() => {
    resizeObserver?.disconnect()
})

watch(
    () => props.visitorLogs.data,
    () => {
        updateTableScrollWidth()
    }
)

/*
|--------------------------------------------------------------------------
| Общие классы
|--------------------------------------------------------------------------
*/

const inputClass =
    'w-full min-w-0 px-3 py-1 border border-slate-500 font-semibold text-sm ' +
    'focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm ' +
    'dark:bg-cyan-800 dark:text-slate-100'

const selectClass =
    'w-16 px-2 py-1 border border-slate-500 font-semibold text-sm ' +
    'focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm ' +
    'dark:bg-cyan-800 dark:text-slate-100'
</script>

<template>
    <AdminLayout title="Analytics Visitor Logs">
        <template #header>
            <TitlePage>{{ t('analyticsLogs') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400
                       dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Управление -->
                <div class="flex flex-col lg:flex-row items-center justify-between gap-3 pb-4">
                    <div class="flex items-center justify-start flex-wrap gap-3">
                        <button
                            type="button"
                            @click="importAnalytics"
                            class="px-3 py-1 rounded flex items-center justify-center gap-2
                                   bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700
                                   dark:hover:bg-cyan-800"
                        >
                            <svg class="shrink-0 h-3 w-3" viewBox="0 0 24 24">
                                <path
                                    class="fill-current text-white"
                                    d="M14,0H3A1,1,0,0,0,2,1V23a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V8H15a1,1,0,0,1-1-1ZM5.5,17h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,17Zm0-5h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,12Zm5-3h-5A.5.5,0,0,1,5,8.5v-1A.5.5,0,0,1,5.5,7h5a.5.5,0,0,1,.5.5v1A.5.5,0,0,1,10.5,9Z"
                                />
                                <polygon
                                    class="fill-current text-white"
                                    points="21.414 6 16 6 16 0.586 21.414 6"
                                />
                            </svg>

                            <span class="text-sm font-semibold text-white">
                                {{ t('import') }}
                            </span>
                        </button>

                        <button
                            type="button"
                            @click="cleanupAnalytics"
                            class="px-3 py-1 rounded flex items-center justify-center gap-2
                                   bg-rose-500 hover:bg-rose-800"
                        >
                            <svg class="shrink-0 h-3 w-3" viewBox="0 0 512 512">
                                <path
                                    class="fill-current text-white"
                                    d="M504 255.531c.253 136.64-111.18 248.372-247.82 248.468-59.015.042-113.223-20.53-155.822-54.911-11.077-8.94-11.905-25.541-1.839-35.607l11.267-11.267c8.609-8.609 22.353-9.551 31.891-1.984C173.062 425.135 212.781 440 256 440c101.705 0 184-82.311 184-184 0-101.705-82.311-184-184-184-48.814 0-93.149 18.969-126.068 49.932l50.754 50.754c10.08 10.08 2.941 27.314-11.313 27.314H24c-8.837 0-16-7.163-16-16V38.627c0-14.254 17.234-21.393 27.314-11.314l49.372 49.372C129.209 34.136 189.552 8 256 8c136.81 0 247.747 110.78 248 247.531zm-180.912 78.784l9.823-12.63c8.138-10.463 6.253-25.542-4.21-33.679L288 256.349V152c0-13.255-10.745-24-24-24h-16c-13.255 0-24 10.745-24 24v135.651l65.409 50.874c10.463 8.137 25.541 6.253 33.679-4.21z"
                                />
                            </svg>

                            <span class="text-sm font-semibold text-white">
                                {{ t('clear') }} {{ t('period') }}
                            </span>
                        </button>
                    </div>

                    <!-- Экспорт -->
                    <div class="flex items-center flex-wrap gap-2">
                        <button
                            type="button"
                            @click="downloadAnalytics('csv')"
                            class="px-3 py-1 rounded text-sm font-semibold text-white
                                   bg-purple-600 hover:bg-purple-700"
                        >
                            CSV
                        </button>

                        <button
                            type="button"
                            @click="downloadAnalytics('xls')"
                            class="px-3 py-1 rounded text-sm font-semibold text-white
                                   bg-green-600 hover:bg-green-700"
                        >
                            Excel
                        </button>

                        <button
                            type="button"
                            @click="downloadAnalytics('docx')"
                            class="px-3 py-1 rounded text-sm font-semibold text-white
                                   bg-blue-600 hover:bg-blue-700"
                        >
                            Word
                        </button>

                        <button
                            type="button"
                            @click="downloadAnalytics('pdf')"
                            class="px-3 py-1 rounded text-sm font-semibold text-white
                                   bg-red-600 hover:bg-red-700"
                        >
                            PDF
                        </button>

                        <button
                            type="button"
                            @click="downloadAnalytics('zip')"
                            class="px-3 py-1 rounded text-sm font-semibold text-white
                                   bg-slate-600 hover:bg-slate-700"
                        >
                            ZIP
                        </button>
                    </div>
                </div>

                <!-- Фильтры -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-10 ]
                            gap-2 mb-3">
                    <input
                        v-model="filters.date_from"
                        type="date"
                        :class="[inputClass, 'xl:col-span-2']"
                    />

                    <input
                        v-model="filters.date_to"
                        type="date"
                        :class="[inputClass, 'xl:col-span-2']"
                    />

                    <input
                        v-model="filters.module"
                        type="text"
                        placeholder="module"
                        :class="inputClass"
                    />

                    <input
                        v-model="filters.event_type"
                        type="text"
                        placeholder="event_type"
                        :class="inputClass"
                    />

                    <input
                        v-model="filters.user_id"
                        type="number"
                        min="1"
                        placeholder="user_id"
                        :class="inputClass"
                    />

                    <input
                        v-model="filters.visitor_uuid"
                        type="text"
                        placeholder="visitor_uuid"
                        :class="[inputClass, 'xl:col-span-2']"
                    />

                    <input
                        v-model="filters.url"
                        type="text"
                        placeholder="url"
                        :class="inputClass"
                    />
                </div>

                <!-- Настройки списка -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-2 mb-3">
                    <select
                        v-model="filters.per_page"
                        @change="applyFilters"
                        :class="selectClass"
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
                            class="px-3 py-1 rounded flex items-center justify-center gap-2
                                   bg-blue-600 hover:bg-blue-700"
                        >
                            <svg class="shrink-0 h-3 w-3" viewBox="0 0 512 512">
                                <path
                                    class="fill-current text-white"
                                    d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z"
                                />
                            </svg>

                            <span class="text-sm font-semibold text-white">
                                {{ t('filter') }}
                            </span>
                        </button>

                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-3 py-1 rounded flex items-center justify-center gap-2
                                   border border-gray-400 dark:border-gray-500
                                   bg-slate-200 hover:bg-slate-300 dark:bg-slate-800
                                   dark:hover:bg-slate-900"
                        >
                            <svg class="shrink-0 h-4 w-4" viewBox="0 0 352 512">
                                <path
                                    class="fill-current text-slate-700 dark:text-slate-300"
                                    d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"
                                />
                            </svg>

                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ t('resetFilter') }}
                            </span>
                        </button>
                    </div>

                    <select
                        v-model="filters.sort_direction"
                        @change="applyFilters"
                        :class="selectClass"
                    >
                        <option value="desc">ID ↓</option>
                        <option value="asc">ID ↑</option>
                    </select>
                </div>

                <!-- Верхний горизонтальный скролл -->
                <div
                    ref="topScroll"
                    class="overflow-x-auto mb-1"
                    @scroll="syncTableScroll"
                >
                    <div
                        :style="{
                            width: `${tableScrollWidth}px`,
                            height: '1px',
                        }"
                    ></div>
                </div>

                <!-- Таблица -->
                <div
                    ref="tableScroll"
                    class="overflow-x-auto bg-white dark:bg-slate-800 border rounded"
                    @scroll="syncTopScroll"
                >
                    <table
                        ref="tableContent"
                        class="min-w-full text-xs border border-gray-400 whitespace-nowrap"
                    >
                        <thead
                            class="bg-gray-100 dark:bg-slate-900
                                   text-gray-700 dark:text-gray-200"
                        >
                        <tr>
                            <th class="px-2 py-2 text-left">ID</th>
                            <th class="px-2 py-2 text-left">Дата</th>
                            <th class="px-2 py-2 text-left">User</th>
                            <th class="px-2 py-2 text-left">Visitor UUID</th>
                            <th class="px-2 py-2 text-left">Module</th>
                            <th class="px-2 py-2 text-left">Event</th>
                            <th class="px-2 py-2 text-left">Title</th>
                            <th class="px-2 py-2 text-left">URL</th>
                            <th class="px-2 py-2 text-left">Time</th>
                            <th class="px-2 py-2 text-left">Scroll</th>
                            <th class="px-2 py-2 text-left">Clicks</th>
                            <th class="px-2 py-2 text-center">Show</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr
                            v-for="log in props.visitorLogs.data"
                            :key="log.id"
                            class="border-t dark:border-slate-700
                                       text-gray-700 dark:text-gray-200"
                        >
                            <td class="px-2 py-1.5">{{ log.id }}</td>

                            <td class="px-2 py-1.5">
                                {{ formatDate(log.visited_at) }}
                            </td>

                            <td class="px-2 py-1.5">
                                {{ log.user_id || 'guest' }}
                            </td>

                            <td
                                class="px-2 py-1.5 max-w-40 truncate"
                                :title="log.visitor_uuid || ''"
                            >
                                {{ log.visitor_uuid || '—' }}
                            </td>

                            <td class="px-2 py-1.5">
                                {{ log.module || '—' }}
                            </td>

                            <td class="px-2 py-1.5">
                                {{ log.event_type || '—' }}
                            </td>

                            <td
                                class="px-2 py-1.5 max-w-xs truncate"
                                :title="log.page_title || ''"
                            >
                                {{ log.page_title || '—' }}
                            </td>

                            <td
                                class="px-2 py-1.5 max-w-md truncate"
                                :title="log.url || ''"
                            >
                                {{ log.url || '—' }}
                            </td>

                            <td class="px-2 py-1.5">
                                {{ log.time_on_page ?? '—' }}
                            </td>

                            <td class="px-2 py-1.5">
                                {{ log.scroll_depth ?? '—' }}
                            </td>

                            <td class="px-2 py-1.5">
                                {{ log.clicks_count ?? 0 }}
                            </td>

                            <td class="px-2 py-1.5">
                                <Link
                                    :href="route(
                                            'admin.analyticsVisitorLogs.show',
                                            log.id
                                        )"
                                    :title="t('openLink')"
                                    class="flex items-center justify-center"
                                >
                                    <svg class="shrink-0 h-4 w-4" viewBox="0 0 576 512">
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-400"
                                            d="M576 24v127.984c0 21.461-25.96 31.98-40.971 16.971l-35.707-35.709-243.523 243.523c-9.373 9.373-24.568 9.373-33.941 0l-22.627-22.627c-9.373-9.373-9.373-24.569 0-33.941L442.756 76.676l-35.703-35.705C391.982 25.9 402.656 0 424.024 0H552c13.255 0 24 10.745 24 24zM407.029 270.794l-16 16A23.999 23.999 0 0 0 384 303.765V448H64V128h264a24.003 24.003 0 0 0 16.97-7.029l16-16C376.089 89.851 365.381 64 344 64H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V287.764c0-21.382-25.852-32.09-40.971-16.97z"
                                        />
                                    </svg>
                                </Link>
                            </td>
                        </tr>

                        <tr
                            v-if="
                                    !props.visitorLogs.data ||
                                    !props.visitorLogs.data.length
                                "
                        >
                            <td
                                colspan="12"
                                class="px-2 py-6 text-center text-sm
                                           text-gray-600 dark:text-gray-400"
                            >
                                {{ t('noData') }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div
                    v-if="props.visitorLogs.meta"
                    class="flex flex-col xl:flex-row items-center
                           justify-between gap-2 mt-3"
                >
                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                        {{ props.visitorLogs.meta.from || 0 }}
                        –
                        {{ props.visitorLogs.meta.to || 0 }}
                        из
                        {{ props.visitorLogs.meta.total || 0 }}
                    </div>

                    <div class="flex items-center justify-center flex-wrap gap-2">
                        <button
                            type="button"
                            :disabled="currentPage <= 1"
                            @click="previousPage"
                            class="px-3 py-1 text-sm font-semibold border rounded"
                            :class="
                                currentPage <= 1
                                    ? 'text-slate-400 dark:text-slate-500 cursor-not-allowed'
                                    : 'bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 ' +
                                     'dark:hover:bg-cyan-900 text-white'"
                        >
                            ← {{ t('previous') }}
                        </button>

                        <div class="flex items-center gap-2">
                            <input
                                v-model="pageInput"
                                type="number"
                                min="1"
                                :max="lastPage"
                                @change="goToPage()"
                                @keyup.enter="goToPage()"
                                class="w-20 px-2 py-1 text-center border border-slate-500
                                       font-semibold text-sm rounded-sm focus:border-indigo-500
                                       focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"
                            />

                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                {{ t('of') }} {{ lastPage }}
                            </span>
                        </div>

                        <button
                            type="button"
                            :disabled="currentPage >= lastPage"
                            @click="nextPage"
                            class="px-3 py-1 text-sm font-semibold border rounded"
                            :class="
                                currentPage >= lastPage
                                    ? 'text-slate-400 dark:text-slate-500 cursor-not-allowed'
                                    : 'bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 ' +
                                     'dark:hover:bg-cyan-900 text-white'"
                        >
                            {{ t('next') }} →
                        </button>
                    </div>

                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                        {{ currentPage }} / {{ lastPage }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
