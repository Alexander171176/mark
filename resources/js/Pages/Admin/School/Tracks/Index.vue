<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 *  Страница треков школы (админка)
 *  Режимы: дерево (drag&drop) и карточки (поиск/сортировка/пагинация)
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'

import BulkActionSelect from '@/Components/Admin/School/Track/Select/BulkActionSelect.vue'
import TrackTreeItem from '@/Components/Admin/School/Track/Tree/TrackTreeItem.vue'
import TrackCardGrid from '@/Components/Admin/School/Track/View/TrackCardGrid.vue'
import SortSelect from '@/Components/Admin/School/Track/Sort/SortSelect.vue'

/** i18n и уведомления */
const { t } = useI18n()
const toast = useToast()

/** Props из контроллера */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    tracksTree: { type: Array, default: () => [] },       // дерево треков
    tracks: { type: Array, default: () => [] },           // плоский список
    tracksCount: { type: Number, default: 0 },            // общее количество

    adminSchoolTracksPerPage: { type: Number, default: 10 },      // кол-во на страницу
    adminSchoolTracksDefaultSort: { type: String, default: 'idDesc' }, // текущая сортировка

    errors: { type: Object, default: () => ({}) },
})

/** Режим отображения: table = дерево, cards = карточки */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolTracksPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountTracks'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSchoolTracksDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortTracks'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

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
    () => props.tracks,
    (newVal) => {
        localTracksFlat.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const trackToDeleteId = ref(null)
const trackToDeleteName = ref('')

/** Открыть модалку удаления */
const confirmDelete = (trackOrId, name = null) => {
    if (typeof trackOrId === 'object') {
        trackToDeleteId.value = trackOrId.id
        trackToDeleteName.value = name || trackOrId.name || `ID: ${trackOrId.id}`
    } else {
        trackToDeleteId.value = trackOrId
        trackToDeleteName.value = name || `ID: ${trackOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть модалку удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    trackToDeleteId.value = null
    trackToDeleteName.value = ''
}

/** Удалить один трек */
const deleteTrack = () => {
    if (trackToDeleteId.value === null) return

    const idToDelete = trackToDeleteId.value
    const nameToDelete = trackToDeleteName.value

    router.delete(route('admin.schoolTracks.destroy', { schoolTrack: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Трек "${nameToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Трек: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Обновить трек внутри дерева */
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

/** Обновить трек в плоском списке */
const patchTrackInFlat = (trackId, callback) => {
    const index = localTracksFlat.value.findIndex((track) => track.id === trackId)

    if (index !== -1) {
        callback(localTracksFlat.value[index])
    }
}

/** Переключить активность одного трека */
const toggleActivity = (track) => {
    const newActivity = !track.activity
    const trackName = track.name || `ID: ${track.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.schoolTracks.updateActivity', { schoolTrack: track.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchTrackInTree(localTracksTree.value, track.id, (node) => {
                    node.activity = newActivity
                })

                patchTrackInFlat(track.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Трек "${trackName}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${trackName}".`)
            },
        }
    )
}

/** Поиск и пагинация */
const searchQuery = ref('')
const currentPage = ref(1)

/** Нормализация строки */
const normalize = (value) => String(value ?? '').trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getParentSortValue = (track) => {
    return track?.parent?.name || track?.parent_id || ''
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortTracks = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') {
        return list.filter((item) => item.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((item) => !item.activity)
    }

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        nameAsc: byStringAsc('name'),
        nameDesc: byStringDesc('name'),

        slugAsc: byStringAsc('slug'),
        slugDesc: byStringDesc('slug'),

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

        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at)
            || safeNumber(a.id) - safeNumber(b.id),

        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at)
            || safeNumber(b.id) - safeNumber(a.id),

        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at)
            || safeNumber(a.id) - safeNumber(b.id),

        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at)
            || safeNumber(b.id) - safeNumber(a.id),

        parentAsc: (a, b) =>
            normalize(getParentSortValue(a)).localeCompare(normalize(getParentSortValue(b)), props.currentLocale)
            || safeNumber(a.id) - safeNumber(b.id),

        parentDesc: (a, b) =>
            normalize(getParentSortValue(b)).localeCompare(normalize(getParentSortValue(a)), props.currentLocale)
            || safeNumber(b.id) - safeNumber(a.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/** Фильтрация треков */
const filteredTracks = computed(() => {
    let filtered = localTracksFlat.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortTracks(filtered)
    }

    filtered = filtered.filter((track) => {
        const name = normalize(track?.name)
        const slug = normalize(track?.slug)
        const short = normalize(track?.short)
        const description = normalize(track?.description)
        const parentName = normalize(track?.parent?.name)

        const hasCourse = (track?.courses || []).some(course =>
            normalize(course?.title).includes(q)
        )

        return (
            name.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            parentName.includes(q) ||
            hasCourse
        )
    })

    return sortTracks(filtered)
})

/** Пагинированные карточки */
const paginatedTracks = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredTracks.value.slice(start, start + per)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Drag-and-drop дерева */
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

    const uniqueChanges = changes.reduce((acc, current) => {
        const existing = acc.find(item => item.id === current.id)

        if (!existing) {
            return acc.concat([current])
        }

        Object.assign(existing, current)
        return acc
    }, [])

    if (!uniqueChanges.length) return

    router.put(
        route('admin.actions.schoolTracks.updateSortBulk'),
        { items: uniqueChanges },
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

/** Выбранные треки */
const selectedTracks = ref([])

/** Получить ID всех треков из дерева */
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

/** Выбрать/снять все */
const toggleAll = (event) => {
    const checked = event?.target?.checked

    const allIds = viewMode.value === 'table'
        ? getAllIds(localTracksTree.value)
        : paginatedTracks.value.map(track => track.id)

    selectedTracks.value = checked ? allIds : []
}

/** Выбрать/снять все в карточках */
const toggleAllCards = ({ ids, checked }) => {
    selectedTracks.value = checked ? [...ids] : []
}

/** Выбрать один трек */
const toggleSelectTrack = (trackId) => {
    const index = selectedTracks.value.indexOf(trackId)

    if (index > -1) {
        selectedTracks.value.splice(index, 1)
    } else {
        selectedTracks.value.push(trackId)
    }
}

/** Локальное обновление activity в дереве */
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

/** Массовое изменение активности */
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
                if (idsToUpdate.includes(item.id)) {
                    return { ...item, activity: newActivity }
                }

                return item
            })

            selectedTracks.value = []
            toast.success('Активность выбранных треков обновлена.')
        },
        onError: (errors) => {
            const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.'
            toast.error(msg)
        },
    })
}

/** Массовое удаление */
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
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка при массовом удалении треков.')
        },
    })
}

/** Обработка массовых действий */
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

</script>

<template>
    <AdminLayout :title="t('tracks')">
        <template #header>
            <TitlePage>{{ t('tracks') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolTracks.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addLearningCategory') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="tracksCount && viewMode !== 'table'"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div v-if="tracksCount && viewMode !== 'table'"
                     class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div v-if="tracksCount"
                     class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                >
                    <CountTable>{{ tracksCount }}</CountTable>
                    <BulkActionSelect @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Пагинация -->
                <div
                    v-if="tracksCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTracks.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <!-- Дерево -->
                <div
                    v-if="viewMode === 'table'"
                    class="border border-gray-400 bg-white dark:bg-slate-800"
                >
                    <div
                        v-if="tracksCount"
                        class="flex justify-between items-center px-3 py-2
                               border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    >
                        <div class="text-xs text-slate-600 dark:text-slate-200">
                            {{ t('selected') }}: {{ selectedTracks.length }}
                        </div>

                        <label
                            class="flex items-center text-xs
                                   text-slate-600 dark:text-slate-200 cursor-pointer"
                        >
                            <span>{{ t('selectAll') }}</span>
                            <input
                                type="checkbox"
                                @change="toggleAll"
                                class="form-checkbox rounded-sm text-indigo-500 ml-2"
                                :title="t('selectAll')"
                            />
                        </label>
                    </div>

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

                        <template #header v-if="localTracksTree.length === 0 && tracksCount > 0">
                            <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                                {{ t('loading') }}
                            </div>
                        </template>

                        <template #footer v-if="localTracksTree.length === 0 && tracksCount === 0">
                            <div class="p-4 text-center text-slate-900 dark:text-slate-100">
                                {{ t('noData') }}
                            </div>
                        </template>
                    </draggable>
                </div>

                <!-- Карточки -->
                <TrackCardGrid
                    v-else
                    :tracks="paginatedTracks"
                    :selected-tracks="selectedTracks"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectTrack"
                    @toggle-all="toggleAllCards"
                />

                <!-- Пагинация -->
                <div
                    v-if="tracksCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTracks.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
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
