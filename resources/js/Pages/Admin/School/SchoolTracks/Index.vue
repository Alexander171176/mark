<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список треков школы
 * - дерево треков с drag&drop
 * - режимы обработки плоского списка: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolTrack/Select/BulkActionSelect.vue'
import TrackTreeItem from '@/Components/Admin/School/SchoolTrack/Tree/TrackTreeItem.vue'
import TrackCardGrid from '@/Components/Admin/School/SchoolTrack/View/TrackCardGrid.vue'
import SortSelect from '@/Components/Admin/School/SchoolTrack/Sort/SortSelect.vue'

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные страницы из Inertia */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolTracksProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    tracksTree: { type: Array, default: () => [] },
    tracks: { type: [Array, Object], default: () => [] },
    tracksCount: { type: Number, default: 0 },

    adminSchoolTracksPerPage: { type: Number, default: 6 },
    adminSchoolTracksDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий вид: table = дерево, cards = плоский список */
const viewMode = ref(localStorage.getItem('admin_view_mode_tracks') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_tracks', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/** Унифицированный плоский список треков */
const tracksList = computed(() => {
    if (Array.isArray(props.tracks)) {
        return props.tracks
    }

    if (Array.isArray(props.tracks?.data)) {
        return props.tracks.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/** Локальная копия дерева */
const localTracksTree = ref([])

watch(
    () => props.tracksTree,
    (newVal) => {
        localTracksTree.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Локальная копия плоского списка */
const localTracksFlat = ref([])

watch(
    tracksList,
    (newVal) => {
        localTracksFlat.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolTracksPerPage || 6)

/** Сохраняем количество элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountTracks'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSchoolTracksDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortTracks'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing && viewMode.value !== 'table') {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(new URLSearchParams(window.location.search)),
                            sort: newVal || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

/* ==========================================================
 * ПОИСК И ПАГИНАЦИЯ
 * ========================================================== */

/** Поисковый запрос */
const searchQuery = ref(props.search || '')

/** Текущая страница frontend-пагинации */
const currentPage = ref(1)

/* ==========================================================
 * ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
 * ========================================================== */

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное число */
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасная дата */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ РЕСУРСОВ
 * ========================================================== */

/** Получение названия трека */
const getTrackName = (track) => {
    return track?.name
        || track?.translation?.name
        || track?.translations?.[0]?.name
        || `ID: ${track?.id}`
}

/** Получение краткого описания */
const getTrackShort = (track) => {
    return track?.short
        || track?.translation?.short
        || track?.translations?.[0]?.short
        || ''
}

/** Получение описания */
const getTrackDescription = (track) => {
    return track?.description
        || track?.translation?.description
        || track?.translations?.[0]?.description
        || ''
}

/** Получение slug */
const getTrackSlug = (track) => {
    return track?.slug
        || track?.translation?.slug
        || track?.translations?.[0]?.slug
        || ''
}

/** Получение названия родителя */
const getParentName = (track) => {
    return track?.parent?.name
        || track?.parent?.translation?.name
        || track?.parent?.translations?.[0]?.name
        || ''
}

/** Значение родителя для сортировки */
const getParentSortValue = (track) => {
    return getParentName(track) || track?.parent_id || ''
}

/** Название связанной сущности */
const getNestedTitle = (item) => {
    return item?.title
        || item?.name
        || item?.translation?.title
        || item?.translation?.name
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || ''
}

/** Текст курсов трека */
const getCoursesText = (track) => {
    const courses = Array.isArray(track?.courses) ? track.courses : []

    return courses.map(getNestedTitle).filter(Boolean).join(' ')
}

/* ==========================================================
 * СОРТИРОВКА FRONTEND
 * ========================================================== */

/** Сортировка чисел ↑ */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел ↓ */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк ↑ */
const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка строк ↓ */
const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка треков */
const sortTracks = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        parentAsc: byNumberAsc('parent_id'),
        parentDesc: byNumberDesc('parent_id'),

        nameAsc: (a, b) =>
            normalize(getTrackName(a)).localeCompare(normalize(getTrackName(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        nameDesc: (a, b) =>
            normalize(getTrackName(b)).localeCompare(normalize(getTrackName(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getTrackSlug(a)).localeCompare(normalize(getTrackSlug(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getTrackSlug(b)).localeCompare(normalize(getTrackSlug(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes_count'),
        likesDesc: byNumberDesc('likes_count'),

        childrenAsc: byNumberAsc('children_count'),
        childrenDesc: byNumberDesc('children_count'),

        coursesAsc: byNumberAsc('courses_count'),
        coursesDesc: byNumberDesc('courses_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        dateAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at) - safeDate(b?.updated_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at) - safeDate(a?.updated_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        parentNameAsc: (a, b) =>
            normalize(getParentSortValue(a)).localeCompare(normalize(getParentSortValue(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        parentNameDesc: (a, b) =>
            normalize(getParentSortValue(b)).localeCompare(normalize(getParentSortValue(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * ПОИСК FRONTEND
 * ========================================================== */

/** Фильтрация плоского списка */
const filteredTracks = computed(() => {
    let filtered = localTracksFlat.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortTracks(filtered)
    }

    filtered = filtered.filter((track) => {
        const name = normalize(getTrackName(track))
        const slug = normalize(getTrackSlug(track))
        const short = normalize(getTrackShort(track))
        const description = normalize(getTrackDescription(track))
        const parentName = normalize(getParentName(track))
        const courses = normalize(getCoursesText(track))

        return name.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || parentName.includes(query)
            || courses.includes(query)
    })

    return sortTracks(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Пагинация frontend-режима */
const paginatedTracks = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredTracks.value.slice(start, start + per)
})

/** Итоговый плоский список */
const displayedTracks = computed(() => {
    return props.useServerProcessing
        ? tracksList.value
        : paginatedTracks.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

const showConfirmDeleteModal = ref(false)
const trackToDeleteId = ref(null)
const trackToDeleteName = ref('')

const confirmDelete = (trackOrId, name = null) => {
    if (typeof trackOrId === 'object') {
        trackToDeleteId.value = trackOrId.id
        trackToDeleteName.value = name || getTrackName(trackOrId)
    } else {
        trackToDeleteId.value = trackOrId
        trackToDeleteName.value = name || `ID: ${trackOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    trackToDeleteId.value = null
    trackToDeleteName.value = ''
}

const deleteTrack = () => {
    if (trackToDeleteId.value === null) return

    const idToDelete = trackToDeleteId.value
    const nameToDelete = trackToDeleteName.value

    router.delete(route('admin.schoolTracks.destroy', { schoolTrack: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Трек "${nameToDelete || 'ID: ' + idToDelete}" удалён.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Трек: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

const patchTrackInTree = (nodes, trackId, callback) => {
    for (const node of nodes) {
        if (node.id === trackId) {
            callback(node)
            return true
        }

        if (node.children?.length && patchTrackInTree(node.children, trackId, callback)) {
            return true
        }
    }

    return false
}

const patchTrackInFlat = (trackId, callback) => {
    const index = localTracksFlat.value.findIndex(track => track.id === trackId)

    if (index !== -1) {
        callback(localTracksFlat.value[index])
    }
}

/* ==========================================================
 * DRAG & DROP ДЕРЕВА
 * ========================================================== */

const handleDragEnd = () => {
    const changes = []

    const updateSortAndCollectChanges = (nodes, parentId) => {
        nodes.forEach((node, index) => {
            let changed = false

            if (node.sort !== index) {
                node.sort = index
                changed = true
            }

            if (node.parent_id !== parentId) {
                node.parent_id = parentId
                changed = true
            }

            if (changed) {
                changes.push({
                    id: node.id,
                    sort: node.sort,
                    parent_id: parentId,
                })
            }

            if (node.children?.length) {
                updateSortAndCollectChanges(node.children, node.id)
            }
        })
    }

    updateSortAndCollectChanges(localTracksTree.value, null)

    if (!changes.length) return

    router.put(
        route('admin.actions.schoolTracks.updateSortBulk'),
        { items: changes },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия треков успешно обновлена.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || 'Ошибка обновления иерархии треков.')

                router.reload({
                    only: ['tracksTree', 'tracks'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

const selectedTracks = ref([])

const getAllIds = (nodes) => {
    let ids = []

    nodes.forEach((node) => {
        ids.push(node.id)

        if (node.children?.length) {
            ids = ids.concat(getAllIds(node.children))
        }
    })

    return ids
}

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false

    const ids = viewMode.value === 'table'
        ? getAllIds(localTracksTree.value)
        : displayedTracks.value.map(track => track.id)

    selectedTracks.value = checked ? ids : []
}

const toggleAllCards = ({ ids, checked }) => {
    selectedTracks.value = checked ? [...ids] : []
}

const toggleSelectTrack = (trackId) => {
    const index = selectedTracks.value.indexOf(trackId)

    if (index > -1) {
        selectedTracks.value.splice(index, 1)
    } else {
        selectedTracks.value.push(trackId)
    }
}

const updateActivityByIds = (nodes, ids, activity) => {
    nodes.forEach((node) => {
        if (ids.includes(node.id)) {
            node.activity = activity
        }

        if (node.children?.length) {
            updateActivityByIds(node.children, ids, activity)
        }
    })
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedTracks.value.length) {
        toast.warning('Выберите треки для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedTracks.value]

    router.put(route('admin.actions.schoolTracks.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            updateActivityByIds(localTracksTree.value, idsToUpdate, newActivity)

            localTracksFlat.value = localTracksFlat.value.map(item => {
                return idsToUpdate.includes(item.id)
                    ? { ...item, activity: newActivity }
                    : item
            })

            selectedTracks.value = []
            toast.success('Активность выбранных треков обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

const bulkDelete = () => {
    if (!selectedTracks.value.length) {
        toast.warning('Выберите хотя бы один трек для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные треки?')) return

    router.delete(route('admin.actions.schoolTracks.bulkDestroy'), {
        data: { ids: selectedTracks.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedTracks.value = []
            toast.success('Выбранные треки успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка при массовом удалении треков.')
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ target: { checked: true } })
    } else if (action === 'deselectAll') {
        toggleAll({ target: { checked: false } })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/* ==========================================================
 * ОДИНОЧНЫЕ ОПЕРАЦИИ
 * ========================================================== */

const toggleActivity = (track) => {
    const newActivity = !track.activity
    const trackName = getTrackName(track)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.schoolTracks.updateActivity', { schoolTrack: track.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchTrackInTree(localTracksTree.value, track.id, node => {
                    node.activity = newActivity
                })

                patchTrackInFlat(track.id, node => {
                    node.activity = newActivity
                })

                track.activity = newActivity
                toast.success(`Трек "${trackName}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${trackName}".`)
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('tracks')">
        <template #header>
            <TitlePage>{{ t('tracks') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolTracks.create')">
                        {{ t('addLearningCategory') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        v-if="viewMode !== 'table'"
                        setting-key="adminSchoolTracksProcessingMode"
                        :mode="adminSchoolTracksProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="tracksCount"
                    />
                </div>

                <SearchInput
                    v-if="tracksCount && viewMode !== 'table' && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="tracksCount && viewMode !== 'table' && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="tracksCount && viewMode !== 'table'"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountTracks"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="tracksCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                >
                    <CountTable>{{ tracksCount }}</CountTable>
                    <BulkActionSelect @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="tracksCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTracks.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="tracks"
                    />
                </div>

                <div
                    v-if="viewMode === 'table'"
                    class="border border-gray-400 bg-white dark:bg-slate-800"
                >
                    <draggable
                        v-model="localTracksTree"
                        tag="div"
                        item-key="id"
                        handle=".handle"
                        group="tracks"
                        @end="handleDragEnd"
                        class="category-tree-root p-1"
                        :data-parent-id="null"
                    >
                        <template #item="{ element: track }">
                            <TrackTreeItem
                                :track="track"
                                :level="0"
                                :selected-tracks="selectedTracks"
                                @toggle-activity="toggleActivity"
                                @delete="confirmDelete"
                                @toggle-select="toggleSelectTrack"
                                @request-drag-end="handleDragEnd"
                            />
                        </template>
                    </draggable>
                </div>

                <TrackCardGrid
                    v-else
                    :tracks="displayedTracks"
                    :selected-tracks="selectedTracks"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectTrack"
                    @toggle-all="toggleAllCards"
                />

                <div
                    v-if="tracksCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTracks.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="tracks"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteTrack"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>

<style scoped>
.category-tree-root {
    padding: 5px;
}
</style>
