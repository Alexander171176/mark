<script setup>
/**
 * Локации — Index
 * - frontend/server/auto режимы обработки
 * - локальный и серверный поиск/фильтрация/сортировка/пагинация
 * - таблица / карточки
 * - одиночная и массовая активность
 * - изменение sort
 * - одиночное и массовое удаление
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/System/Location/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/System/Location/Sort/SortSelect.vue'
import LocationTable from '@/Components/Admin/System/Location/Table/LocationTable.vue'
import LocationCardGrid from '@/Components/Admin/System/Location/View/LocationCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    locations: { type: [Array, Object], default: () => [] },
    locationsCount: { type: Number, default: 0 },

    useServerProcessing: { type: Boolean, default: false },

    adminSystemLocationsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    adminSystemLocationsPerPage: {
        type: Number,
        default: 6,
    },

    adminSystemLocationsDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    adminSystemLocationsDefaultView: {
        type: String,
        default: 'table',
    },

    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    locationTypes: { type: Array, default: () => [] },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
    typeFilter: { type: String, default: '' },

    activityFilter: {
        type: [String, Boolean, Number],
        default: null,
    },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem('admin_view_mode_locations')
    || props.adminSystemLocationsDefaultView
    || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_locations', value)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

const locationsList = computed(() => {
    if (Array.isArray(props.locations)) return props.locations
    if (Array.isArray(props.locations?.data)) return props.locations.data

    return []
})

const localLocations = ref([])

watch(
    locationsList,
    (newVal) => {
        localLocations.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * ПАГИНАЦИЯ
 * ========================================================== */

const currentPage = ref(1)
const itemsPerPage = ref(
    props.adminSystemLocationsPerPage || 6
)

watch(itemsPerPage, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminCountLocations'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(`Показ ${newVal} элементов на странице.`)
            },

            onError: (errors) => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления количества элементов.'
                )
            },
        }
    )
})

/* ==========================================================
 * СОРТИРОВКА СПИСКА
 * ========================================================== */

const sortParam = ref(
    props.sortParam
    || props.adminSystemLocationsDefaultSort
    || 'idDesc'
)

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortLocations'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    reloadServerData({
                        sort: newVal,
                        page: undefined,
                    })
                }

                toast.info('Сортировка успешно изменена.')
            },

            onError: (errors) => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
})

/* ==========================================================
 * DRAG'N'DROP SORT
 * ========================================================== */

const handleSortOrderUpdate = (orderedIds) => {
    const startSort =
        (currentPage.value - 1)
        * itemsPerPage.value

    const sortData = orderedIds.map(
        (id, index) => ({
            id,
            sort: startSort + index + 1,
        })
    )

    router.put(
        route(
            'admin.actions.locations.updateSortBulk'
        ),
        {
            items: sortData,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                sortData.forEach(
                    ({ id, sort }) => {
                        patchLocalLocation(
                            id,
                            (location) => {
                                location.sort = sort
                            }
                        )
                    }
                )

                toast.success(
                    'Порядок локаций успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки локаций:',
                    errors
                )

                toast.error(
                    errors.general
                    || errors.items
                    || 'Не удалось обновить порядок локаций.'
                )

                router.reload({
                    only: ['locations'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * ПОИСК И ФИЛЬТРЫ
 * ========================================================== */

const searchQuery = ref(props.search || '')
const typeFilter = ref(props.typeFilter || '')

const normalizeActivityFilter = (value) => {
    if (
        value === true
        || value === 1
        || value === '1'
        || value === 'true'
    ) {
        return 'true'
    }

    if (
        value === false
        || value === 0
        || value === '0'
        || value === 'false'
    ) {
        return 'false'
    }

    return ''
}

const activityFilter = ref(
    normalizeActivityFilter(props.activityFilter)
)

/* ==========================================================
 * SERVER PROCESSING
 * ========================================================== */

const reloadServerData = (overrides = {}) => {
    const currentQuery = Object.fromEntries(
        new URLSearchParams(window.location.search)
    )

    router.get(
        window.location.pathname,
        {
            ...currentQuery,
            search: searchQuery.value || undefined,
            sort: sortParam.value || undefined,
            type: typeFilter.value || undefined,
            activity: activityFilter.value || undefined,
            page: undefined,
            ...overrides,
        },
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        }
    )
}

watch(typeFilter, () => {
    currentPage.value = 1

    if (props.useServerProcessing) {
        reloadServerData()
    }
})

watch(activityFilter, () => {
    currentPage.value = 1

    if (props.useServerProcessing) {
        reloadServerData()
    }
})

watch(
    [
        itemsPerPage,
        searchQuery,
        typeFilter,
        activityFilter,
    ],
    () => {
        currentPage.value = 1
    }
)

/* ==========================================================
 * DEFAULT LOCATION
 * ========================================================== */

const makeDefaultLocation = (location) => {
    if (!location?.id) return

    if (location.is_default) {
        toast.info(
            'Эта локация уже установлена по умолчанию.'
        )

        return
    }

    const title =
        getLocationTitle(location)

    router.put(
        route(
            'admin.actions.locations.makeDefault',
            {
                location: location.id,
            }
        ),
        {},
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localLocations.value =
                    localLocations.value.map(
                        (item) => ({
                            ...item,

                            is_default:
                                item.id === location.id,

                            activity:
                                item.id === location.id
                                    ? true
                                    : item.activity,
                        })
                    )

                toast.success(
                    `Локация "${title}" установлена по умолчанию.`
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors.general
                    || errors[errorKey]
                    || `Не удалось установить локацию "${title}" по умолчанию.`
                )
            },
        }
    )
}

/* ==========================================================
 * HELPERS
 * ========================================================== */

const locationTypeTitle = (type) => {
    const types = {
        country: 'country',
        region: 'region',
        city: 'city',
        district: 'district',
    }

    return types[type] ? t(types[type]) : type || '—'
}

const normalize = (value) => {
    return (value ?? '')
        .toString()
        .trim()
        .toLowerCase()
}

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/* ==========================================================
 * ДАННЫЕ RESOURCE
 * ========================================================== */

const getLocationTranslation = (location) => {
    return location?.translation || {}
}

const getLocationTitle = (location) => {
    return getLocationTranslation(location)?.title
        || `ID: ${location?.id}`
}

const getLocationTitleIn = (location) => {
    return getLocationTranslation(location)?.title_in || ''
}

const getLocationTitleFrom = (location) => {
    return getLocationTranslation(location)?.title_from || ''
}

const getLocationShort = (location) => {
    return getLocationTranslation(location)?.short || ''
}

const getParentTitle = (location) => {
    return location?.parent?.translation?.title
        || (
            location?.parent_id
                ? `ID: ${location.parent_id}`
                : ''
        )
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ
 * ========================================================== */

const patchLocalLocation = (locationId, callback) => {
    const location = localLocations.value.find(
        (item) => item.id === locationId
    )

    if (location) {
        callback(location)
    }
}

/* ==========================================================
 * FRONTEND СОРТИРОВКА
 * ========================================================== */

const byNumberAsc = (field) => (a, b) => {
    return safeNumber(a?.[field])
        - safeNumber(b?.[field])
        || safeNumber(a?.id)
        - safeNumber(b?.id)
}

const byNumberDesc = (field) => (a, b) => {
    return safeNumber(b?.[field])
        - safeNumber(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

const sortLocations = (locations) => {
    const list = (locations || []).slice()

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getLocationTitle(a))
                .localeCompare(
                    normalize(getLocationTitle(b)),
                    props.currentLocale
                )
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getLocationTitle(b))
                .localeCompare(
                    normalize(getLocationTitle(a)),
                    props.currentLocale
                )
            || safeNumber(b?.id) - safeNumber(a?.id),

        typeAsc: (a, b) =>
            normalize(a?.type)
                .localeCompare(normalize(b?.type))
            || safeNumber(a?.sort) - safeNumber(b?.sort),

        typeDesc: (a, b) =>
            normalize(b?.type)
                .localeCompare(normalize(a?.type))
            || safeNumber(a?.sort) - safeNumber(b?.sort),

        slugAsc: (a, b) =>
            normalize(a?.slug)
                .localeCompare(normalize(b?.slug))
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(b?.slug)
                .localeCompare(normalize(a?.slug))
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * FRONTEND ФИЛЬТРАЦИЯ И ПОИСК
 * ========================================================== */

const filteredLocations = computed(() => {
    let filtered = (localLocations.value || []).slice()

    if (typeFilter.value) {
        filtered = filtered.filter(
            (location) => location?.type === typeFilter.value
        )
    }

    if (activityFilter.value === 'true') {
        filtered = filtered.filter(
            (location) => !!location?.activity
        )
    }

    if (activityFilter.value === 'false') {
        filtered = filtered.filter(
            (location) => !location?.activity
        )
    }

    const query = normalize(searchQuery.value)

    if (query) {
        filtered = filtered.filter((location) => {
            const values = [
                location?.id,
                location?.parent_id,
                location?.type,
                location?.slug,
                location?.code,
                location?.latitude,
                location?.longitude,
                location?.timezone,
                location?.sort,
                getLocationTitle(location),
                getLocationTitleIn(location),
                getLocationTitleFrom(location),
                getLocationShort(location),
                getParentTitle(location),
            ]

            return values.some(
                (value) => normalize(value).includes(query)
            )
        })
    }

    return sortLocations(filtered)
})

/* ==========================================================
 * FRONTEND ПАГИНАЦИЯ
 * ========================================================== */

const paginatedLocations = computed(() => {
    const perPage = Number(itemsPerPage.value || 12)
    const start = (currentPage.value - 1) * perPage

    return filteredLocations.value.slice(
        start,
        start + perPage
    )
})

/* ==========================================================
 * ВЫВОД
 * ========================================================== */

const displayedLocations = computed(() => {
    return props.useServerProcessing
        ? locationsList.value
        : paginatedLocations.value
})

/* ==========================================================
 * ACTIVITY
 * ========================================================== */

const toggleActivity = (location) => {
    if (!location?.id) return

    const newActivity = !location.activity
    const title = getLocationTitle(location)

    if (
        location.is_default
        && !newActivity
    ) {
        toast.warning(
            'Локацию по умолчанию нельзя деактивировать.'
        )

        return
    }

    router.put(
        route(
            'admin.actions.locations.updateActivity',
            { location: location.id }
        ),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalLocation(
                    location.id,
                    (item) => {
                        item.activity = newActivity
                    }
                )

                toast.success(
                    newActivity
                        ? `Локация "${title}" активирована.`
                        : `Локация "${title}" деактивирована.`
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                toast.error(
                    errors.activity
                    || errors.general
                    || errors[errorKey]
                    || `Ошибка изменения активности локации "${title}".`
                )
            },
        }
    )
}

/* ==========================================================
 * SORT ОДНОЙ LOCATION
 * ========================================================== */

const updateLocationSort = (location, sort) => {
    if (!location?.id) return

    const normalizedSort = Number(sort)

    if (
        !Number.isInteger(normalizedSort)
        || normalizedSort < 0
    ) {
        toast.warning(
            'Значение сортировки должно быть целым числом от 0.'
        )

        return
    }

    if (
        Number(location.sort)
        === normalizedSort
    ) {
        return
    }

    const title = getLocationTitle(location)

    router.put(
        route(
            'admin.actions.locations.updateSort',
            { location: location.id }
        ),
        { sort: normalizedSort },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalLocation(
                    location.id,
                    (item) => {
                        item.sort = normalizedSort
                    }
                )

                toast.success(
                    `Сортировка локации "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                toast.error(
                    errors.sort
                    || errors.general
                    || errors[errorKey]
                    || `Ошибка изменения сортировки локации "${title}".`
                )
            },
        }
    )
}

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

const showConfirmDeleteModal = ref(false)
const locationToDeleteId = ref(null)
const locationToDeleteTitle = ref('')

const confirmDelete = (locationOrId, title = null) => {
    if (typeof locationOrId === 'object') {
        locationToDeleteId.value = locationOrId.id
        locationToDeleteTitle.value =
            title || getLocationTitle(locationOrId)
    } else {
        locationToDeleteId.value = locationOrId
        locationToDeleteTitle.value =
            title || `ID: ${locationOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    locationToDeleteId.value = null
    locationToDeleteTitle.value = ''
}

const deleteLocation = () => {
    if (locationToDeleteId.value === null) return

    const idToDelete = locationToDeleteId.value
    const titleToDelete = locationToDeleteTitle.value

    router.delete(
        route(
            'admin.locations.destroy',
            { location: idToDelete }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedLocations.value =
                    selectedLocations.value.filter(
                        (id) => id !== idToDelete
                    )

                toast.success(
                    `Локация "${titleToDelete || 'ID: ' + idToDelete}" удалена.`
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                const message =
                    errors.general
                    || errors[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${message} (Локация: ${titleToDelete || 'ID: ' + idToDelete})`
                )
            },

            onFinish: closeModal,
        }
    )
}

/* ==========================================================
 * ВЫБОР
 * ========================================================== */

const selectedLocations = ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedLocations.value.map(
            (location) => location.id
        )

    if (checked) {
        selectedLocations.value = [
            ...new Set([
                ...selectedLocations.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedLocations.value =
        selectedLocations.value.filter(
            (id) => !ids.includes(id)
        )
}

const toggleSelectLocation = (locationId) => {
    const index =
        selectedLocations.value.indexOf(locationId)

    if (index > -1) {
        selectedLocations.value.splice(index, 1)
        return
    }

    selectedLocations.value.push(locationId)
}

/* ==========================================================
 * МАССОВАЯ АКТИВНОСТЬ
 * ========================================================== */

const bulkToggleActivity = (newActivity) => {
    if (!selectedLocations.value.length) {
        toast.warning(
            'Выберите локации для изменения активности.'
        )

        return
    }

    const idsToUpdate = [...selectedLocations.value]

    if (!newActivity) {
        const containsDefault =
            localLocations.value.some(
                (location) =>
                    idsToUpdate.includes(location.id)
                    && location.is_default
            )

        if (containsDefault) {
            toast.warning(
                'Локацию по умолчанию нельзя деактивировать.'
            )

            return
        }
    }

    router.put(
        route(
            'admin.actions.locations.bulkUpdateActivity'
        ),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localLocations.value =
                    localLocations.value.map(
                        (location) => {
                            if (!idsToUpdate.includes(location.id)) {
                                return location
                            }

                            return {
                                ...location,
                                activity: newActivity,
                            }
                        }
                    )

                selectedLocations.value = []

                toast.success(
                    newActivity
                        ? 'Выбранные локации активированы.'
                        : 'Выбранные локации деактивированы.'
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                toast.error(
                    errors.ids
                    || errors.activity
                    || errors.general
                    || errors[errorKey]
                    || 'Ошибка массового изменения активности локаций.'
                )
            },
        }
    )
}

/* ==========================================================
 * МАССОВОЕ УДАЛЕНИЕ
 * ========================================================== */

const bulkDelete = () => {
    if (!selectedLocations.value.length) {
        toast.warning(
            'Выберите хотя бы одну локацию для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные локации?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.locations.bulkDestroy'
        ),
        {
            data: {
                ids: [...selectedLocations.value],
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedLocations.value = []

                toast.success(
                    'Выбранные локации успешно удалены.'
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                toast.error(
                    errors.general
                    || errors[errorKey]
                    || 'Произошла ошибка при удалении локаций.'
                )
            },
        }
    )
}

/* ==========================================================
 * МАССОВЫЕ ДЕЙСТВИЯ
 * ========================================================== */

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ checked: true })
    } else if (action === 'deselectAll') {
        toggleAll({ checked: false })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('locations')">
        <template #header>
            <TitlePage>{{ t('locations') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель -->
                <div
                    class="sm:flex sm:justify-between
                           sm:items-center mb-3 gap-3"
                >
                    <DefaultButton :href="route('admin.locations.create')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current
                                       opacity-50 shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addLocation') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSystemLocationsProcessingMode"
                        :mode="adminSystemLocationsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="locationsCount"
                    />
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="locationsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="locationsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Фильтры -->
                <div
                    v-if="locationsCount"
                    class="grid grid-cols-1 sm:grid-cols-2
                           gap-2 my-3"
                >
                    <select
                        v-model="typeFilter"
                        class="w-full py-1 px-2 text-sm
                               border border-slate-400 dark:border-slate-600
                               bg-white dark:bg-slate-800
                               text-gray-900 dark:text-gray-100
                               rounded-sm focus:ring-1 focus:ring-blue-500
                               focus:border-blue-500"
                    >
                        <option value="">
                            {{ t('allTypes') }}
                        </option>

                        <option
                            v-for="type in locationTypes"
                            :key="type"
                            :value="type"
                        >
                            {{ locationTypeTitle(type) }}
                        </option>
                    </select>

                    <select
                        v-model="activityFilter"
                        class="w-full py-1 px-2 text-sm
                               border border-slate-400 dark:border-slate-600
                               bg-white dark:bg-slate-800
                               text-gray-900 dark:text-gray-100
                               rounded-sm focus:ring-1 focus:ring-blue-500
                               focus:border-blue-500"
                    >
                        <option value="">
                            {{ t('activity') }}
                        </option>

                        <option value="true">
                            {{ t('active') }}
                        </option>

                        <option value="false">
                            {{ t('inactive') }}
                        </option>
                    </select>
                </div>

                <!-- Количество / сортировка -->
                <div
                    v-if="locationsCount"
                    class="flex justify-between items-center
                           flex-col md:flex-row my-3 gap-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountLocations"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <!-- Количество / bulk / вид -->
                <div
                    v-if="locationsCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ locationsCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Верхняя пагинация -->
                <div
                    v-if="locationsCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLocations.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="locations"
                    />
                </div>

                <!-- Таблица -->
                <LocationTable
                    v-if="viewMode === 'table'"
                    :locations="displayedLocations"
                    :selected-locations="selectedLocations"
                    @toggle-activity="toggleActivity"
                    @make-default="makeDefaultLocation"
                    @update-sort="updateLocationSort"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectLocation"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <LocationCardGrid
                    v-else
                    :locations="displayedLocations"
                    :selected-locations="selectedLocations"
                    @toggle-activity="toggleActivity"
                    @make-default="makeDefaultLocation"
                    @update-sort="updateLocationSort"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectLocation"
                    @toggle-all="toggleAll"
                />

                <!-- Нижняя пагинация -->
                <div
                    v-if="locationsCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLocations.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="locations"
                    />
                </div>

                <!-- Нет данных -->
                <div
                    v-if="!locationsCount"
                    class="py-8 text-center text-sm
                           text-slate-600 dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteLocation"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
