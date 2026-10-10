<script setup>
/**
 * @version PulsarCMS 1.0
 * Журнал отправки Email Notifications.
 *
 * - структурные фильтры применяет Laravel в обоих режимах;
 * - поиск, сортировка и пагинация: Vue (frontend) / Laravel (server).
 */
import { computed, ref, watch, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import StatusStats from '@/Components/Admin/UI/Stats/StatusStats.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import SortSelect from '@/Components/Admin/NotificationEmailLog/Sort/SortSelect.vue'
import NotificationEmailLogFilters from '@/Components/Admin/NotificationEmailLog/Filter/NotificationEmailLogFilters.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'
import NotificationEmailLogTable from '@/Components/Admin/NotificationEmailLog/Table/NotificationEmailLogTable.vue'
import NotificationEmailLogCards from '@/Components/Admin/NotificationEmailLog/View/NotificationEmailLogCards.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    logs: { type: [Array, Object], default: () => [] },
    filters: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) },
    events: { type: Array, default: () => [] },
    statuses: { type: [Array, Object], default: () => ({}) },
    totalRecords: { type: Number, default: 0 },
    useServerProcessing: { type: Boolean, default: false },
    effectiveMode: { type: String, default: 'frontend' },
    adminEmailLogsProcessingMode: { type: String, default: 'auto' },
    adminEmailLogsPerPage: { type: Number, default: 20 },
    adminEmailLogsDefaultSort: { type: String, default: 'idDesc' },
    adminEmailLogsDefaultView: { type: String, default: 'table' },
    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
    error: { type: String, default: '' },
})

const statusTranslations = {
    pending: 'inLine',
    processing: 'statusProcessing',
    retrying: 'retryAttempt',
    sent: 'sent',
    failed: 'error',
    skipped: 'omitted',
}

// Переведённые подписи для фильтра статусов
const statusOptions = computed(() =>
    Object.fromEntries(
        Object.entries(statusTranslations).map(([key, translationKey]) => [
            key,
            t(translationKey),
        ])
    )
)

/*
|--------------------------------------------------------------------------
| Режим отображения
|--------------------------------------------------------------------------
*/
const viewKey = 'admin_view_mode_notification_email_logs'
const defaultView = props.adminEmailLogsDefaultView === 'grid' ? 'cards' : 'table'
const storedView = typeof window !== 'undefined' ? localStorage.getItem(viewKey) : null
const viewMode = ref(['table', 'cards'].includes(storedView) ? storedView : defaultView)

watch(viewMode, value => {
    if (typeof window !== 'undefined') localStorage.setItem(viewKey, value)
})

/*
|--------------------------------------------------------------------------
| Данные
|--------------------------------------------------------------------------
*/
const logsList = computed(() => {
    if (Array.isArray(props.logs)) return props.logs
    if (Array.isArray(props.logs?.data)) return props.logs.data
    if (Array.isArray(props.logs?.data?.data)) return props.logs.data.data
    return []
})

const currentPage = ref(1)
const itemsPerPage = ref(Number(props.adminEmailLogsPerPage || 20))
const sortParam = ref(props.sortParam || props.adminEmailLogsDefaultSort || 'idDesc')
const searchQuery = ref(props.search || '')

/*
|--------------------------------------------------------------------------
| Структурные фильтры
|--------------------------------------------------------------------------
| Их применяет Laravel независимо от режима обработки.
*/
const emptyLogFilters = () => ({
    status: '',
    event: '',
    recipient: '',
    uuid: '',
    attempts: '',
    date_from: '',
    date_to: '',
})

const filterKeys = Object.keys(emptyLogFilters())
const extractFilters = (value) => Object.fromEntries(
    filterKeys.map(key => [key, value?.[key] ?? ''])
)

const logFilters = ref({
    ...emptyLogFilters(),
    ...extractFilters(props.filters),
})

const invalidDates = computed(() => Boolean(
    logFilters.value.date_from
    && logFilters.value.date_to
    && logFilters.value.date_to < logFilters.value.date_from
))

/*
|--------------------------------------------------------------------------
| Локальные поиск и сортировка
|--------------------------------------------------------------------------
| В server mode данные уже обработаны Laravel.
*/
const normalize = value => String(value ?? '').trim().toLowerCase()
const safeNumber = value => Number.isFinite(Number(value)) ? Number(value) : 0
const safeDate = value => {
    const time = value ? new Date(value).getTime() : 0
    return Number.isFinite(time) ? time : 0
}
const idAsc = (a, b) => safeNumber(a.id) - safeNumber(b.id)
const idDesc = (a, b) => safeNumber(b.id) - safeNumber(a.id)
const compareField = (a, b, field, direction, type = 'text') => {
    let result = 0
    if (type === 'number') {
        result = safeNumber(a[field]) - safeNumber(b[field])
    } else if (type === 'date') {
        result = safeDate(a[field]) - safeDate(b[field])
    } else {
        result = normalize(a[field]).localeCompare(normalize(b[field]))
    }
    return result * direction || idDesc(a, b)
}

const sortMap = {
    idAsc,
    idDesc,
    createdAtAsc: (a, b) => compareField(a, b, 'created_at', 1, 'date'),
    createdAtDesc: (a, b) => compareField(a, b, 'created_at', -1, 'date'),
    recipientAsc: (a, b) => compareField(a, b, 'recipient', 1),
    recipientDesc: (a, b) => compareField(a, b, 'recipient', -1),
    statusAsc: (a, b) => compareField(a, b, 'status', 1),
    statusDesc: (a, b) => compareField(a, b, 'status', -1),
    attemptsAsc: (a, b) => compareField(a, b, 'attempts', 1, 'number'),
    attemptsDesc: (a, b) => compareField(a, b, 'attempts', -1, 'number'),
}

const filteredLogs = computed(() => {
    const term = normalize(searchQuery.value)
    return logsList.value
        .filter(log => !term || [log.recipient, log.subject, log.event, log.uuid]
            .some(value => normalize(value).includes(term)))
        .slice()
        .sort(sortMap[sortParam.value] || idDesc)
})

const paginatedLogs = computed(() => {
    const size = Math.max(1, Number(itemsPerPage.value) || 20)
    const start = (currentPage.value - 1) * size
    return filteredLogs.value.slice(start, start + size)
})

const displayedLogs = computed(() =>
    props.useServerProcessing ? logsList.value : paginatedLogs.value
)

const foundCount = computed(() => props.useServerProcessing
    ? Number(props.logs?.meta?.total ?? props.logs?.total ?? logsList.value.length)
    : filteredLogs.value.length
)

const hasRecords = computed(() => Number(props.totalRecords) > 0)

/*
|--------------------------------------------------------------------------
| Серверные запросы
|--------------------------------------------------------------------------
*/
const cleanParams = params => Object.fromEntries(
    Object.entries(params).filter(([, value]) =>
        value !== '' && value !== null && value !== undefined
    )
)

const reloadServerList = (patch = {}, options = {}) => {
    const params = cleanParams({
        ...extractFilters(props.filters),
        ...patch,
    })

    router.get(route('admin.notificationEmailLogs.index'), params, {
        preserveScroll: true,
        preserveState: options.preserveState ?? true,
        replace: true,
    })
}

let searchTimer = null

watch(searchQuery, value => {
    currentPage.value = 1
    if (!props.useServerProcessing) return

    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => {
        reloadServerList({
            ...extractFilters(props.filters),
            search: value || undefined,
            sort: sortParam.value || undefined,
            page: undefined,
        })
    }, 400)
})

watch(sortParam, value => {
    currentPage.value = 1
    if (!value || value === props.adminEmailLogsDefaultSort && value === props.sortParam) return

    router.put(
        route('admin.settings.updateAdminSortEmailLogs'),
        { value },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (props.useServerProcessing) {
                    reloadServerList({
                        ...extractFilters(props.filters),
                        search: searchQuery.value || undefined,
                        sort: value,
                        page: undefined,
                    })
                }
                toast.info('Сортировка журнала обновлена.')
            },
            onError: errors => toast.error(errors?.value || 'Ошибка сохранения сортировки.'),
        }
    )
})

watch(itemsPerPage, value => {
    currentPage.value = 1
    if (props.useServerProcessing) return

    router.put(
        route('admin.settings.updateAdminCountEmailLogs'),
        { value: Number(value) },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${value} записей на странице.`),
            onError: errors => toast.error(errors?.value || 'Ошибка сохранения количества записей.'),
        }
    )
})

watch(() => props.search, value => {
    if (value !== searchQuery.value) searchQuery.value = value || ''
})
watch(() => props.sortParam, value => {
    if (value && value !== sortParam.value) sortParam.value = value
})
watch(() => props.adminEmailLogsPerPage, value => {
    if (value && Number(value) !== Number(itemsPerPage.value)) itemsPerPage.value = Number(value)
})
watch(() => props.filters, value => {
    logFilters.value = { ...emptyLogFilters(), ...extractFilters(value) }
}, { deep: true })

onBeforeUnmount(() => clearTimeout(searchTimer))

/*
|--------------------------------------------------------------------------
| Применение / сброс структурных фильтров
|--------------------------------------------------------------------------
*/
const applyLogFilters = () => {
    if (invalidDates.value) {
        toast.error('Дата окончания не может быть раньше даты начала.')
        return
    }
    clearTimeout(searchTimer)
    currentPage.value = 1
    reloadServerList({
        ...extractFilters(logFilters.value),
        search: props.useServerProcessing ? searchQuery.value || undefined : undefined,
        sort: props.useServerProcessing ? sortParam.value || undefined : undefined,
        page: undefined,
    }, { preserveState: false })
}

const resetLogFilters = () => {
    clearTimeout(searchTimer)
    logFilters.value = emptyLogFilters()
    currentPage.value = 1
    router.get(
        route('admin.notificationEmailLogs.index'),
        cleanParams({
            search: props.useServerProcessing ? searchQuery.value || undefined : undefined,
            sort: props.useServerProcessing ? sortParam.value || undefined : undefined,
        }),
        { preserveScroll: true, preserveState: false, replace: true }
    )
}

const refresh = () => {
    clearTimeout(searchTimer)
    reloadServerList({
        ...extractFilters(props.filters),
        search: props.useServerProcessing ? searchQuery.value || undefined : undefined,
        sort: props.useServerProcessing ? sortParam.value || undefined : undefined,
        page: props.useServerProcessing
            ? Number(props.logs?.meta?.current_page ?? props.logs?.current_page ?? 1)
            : undefined,
    })
}

watch(() => props.error, value => {
    if (value) toast.error(value)
}, { immediate: true })
</script>

<template>
    <AdminLayout :title="`${t('notificationEmailLogs')} Email Notifications`">
        <template #header>
            <TitlePage>{{ t('notificationEmailLogs') }} Email Notifications</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95">

                <!-- Управление и режим обработки -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <button
                        type="button"
                        class="flex flex-row items-center justify-center gap-1
                               px-2 py-0.5 bg-blue-600 hover:bg-blue-700 rounded-sm"
                        @click="refresh"
                    >
                        <svg
                            class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                            viewBox="0 0 16 16">
                            <path
                                d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" ></path>
                        </svg>
                        <span class="text-white text-sm">
                            {{ t('refreshLog') }}
                        </span>
                    </button>

                    <ProcessingModeSwitcher
                        setting-key="adminEmailLogsProcessingMode"
                        :mode="adminEmailLogsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="totalRecords"
                    />
                </div>

                <!-- Универсальная статистика статусов -->
                <StatusStats
                    :stats="stats"
                    :statuses="statusTranslations"
                />

                <!-- Поиск -->
                <SearchInput
                    v-if="hasRecords && !useServerProcessing"
                    v-model="searchQuery"
                />
                <ServerSearchInput
                    v-else-if="hasRecords"
                    v-model="searchQuery"
                />

                <!-- Количество и сортировка -->
                <div
                    v-if="hasRecords"
                    class="flex justify-between items-center flex-col md:flex-row gap-3 my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountEmailLogs"
                    />
                    <SortSelect v-model:sortParam="sortParam" />
                </div>

                <!-- Универсальные фильтры -->
                <NotificationEmailLogFilters
                    v-if="hasRecords"
                    v-model="logFilters"
                    :statuses="statusOptions"
                    :events="events"
                    @apply="applyLogFilters"
                    @reset="resetLogFilters"
                />

                <!-- Счётчик и переключение вида -->
                <div
                    v-if="hasRecords"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3 my-3"
                >
                    <CountTable>{{ foundCount }}</CountTable>
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Верхняя пагинация -->
                <div v-if="hasRecords" class="flex justify-center items-center mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLogs.length"
                        @update:currentPage="currentPage = $event"
                    />
                    <AdminServerPagination v-else :pagination="logs" />
                </div>

                <!-- Список -->
                <div v-if="!hasRecords" class="py-12 text-center text-gray-500 dark:text-gray-300">
                    {{ t('noData') }}
                </div>
                <div v-else-if="!displayedLogs.length" class="py-12 text-center text-gray-500 dark:text-gray-300">
                    {{ t('nothingFound') }}
                </div>
                <NotificationEmailLogTable
                    v-else-if="viewMode === 'table'"
                    :logs="displayedLogs"
                />
                <NotificationEmailLogCards
                    v-else
                    :logs="displayedLogs"
                />

                <!-- Нижняя пагинация -->
                <div v-if="hasRecords" class="flex justify-center items-center mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLogs.length"
                        @update:currentPage="currentPage = $event"
                    />
                    <AdminServerPagination v-else :pagination="logs" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
