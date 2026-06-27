<script setup>
/**
 * Рубрики блога — Index
 * - дерево рубрик с drag&drop
 * - режимы обработки плоского списка: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

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

import BulkActionSelect from '@/Components/Admin/Blog/BlogRubric/Select/BulkActionSelect.vue'
import RubricTreeDraggable from '@/Components/Admin/Blog/BlogRubric/Tree/RubricTreeDraggable.vue'
import RubricCardGrid from '@/Components/Admin/Blog/BlogRubric/View/RubricCardGrid.vue'
import SortSelect from '@/Components/Admin/Blog/BlogRubric/Sort/SortSelect.vue'

const { t } = useI18n()
const toast = useToast()
const page = usePage()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminBlogRubricsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    rubricsTree: { type: Array, default: () => [] },
    rubrics: { type: [Array, Object], default: () => [] },
    rubricsCount: { type: Number, default: 0 },

    adminBlogRubricsPerPage: { type: Number, default: 6 },
    adminBlogRubricsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * ПРАВА
 * ========================================================== */

const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/**
 * table = дерево
 * cards = карточный плоский список
 */
const viewMode = ref(localStorage.getItem('admin_view_mode_blog_rubrics') || 'table')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_blog_rubrics', value)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

const rubricsList = computed(() => {
    if (Array.isArray(props.rubrics)) {
        return props.rubrics
    }

    if (Array.isArray(props.rubrics?.data)) {
        return props.rubrics.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНЫЕ КОПИИ
 * ========================================================== */

const localRubricsTree = ref([])
const localRubricsFlat = ref([])

watch(
    () => props.rubricsTree,
    (newVal) => {
        localRubricsTree.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

watch(
    rubricsList,
    (newVal) => {
        localRubricsFlat.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

const itemsPerPage = ref(props.adminBlogRubricsPerPage || 6)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountRubrics'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminBlogRubricsDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortRubrics'),
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

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/* ==========================================================
 * HELPERS
 * ========================================================== */

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ RESOURCE
 * ========================================================== */

const getRubricTranslation = (rubric) => {
    return rubric?.translation || rubric?.translations?.[0] || {}
}

const getRubricTitle = (rubric) => {
    return rubric?.title
        || getRubricTranslation(rubric)?.title
        || `ID: ${rubric?.id}`
}

const getRubricShort = (rubric) => {
    return rubric?.short
        || getRubricTranslation(rubric)?.short
        || ''
}

const getRubricDescription = (rubric) => {
    return rubric?.description
        || getRubricTranslation(rubric)?.description
        || ''
}

const getParentTitle = (rubric) => {
    return rubric?.parent?.title
        || rubric?.parent?.translation?.title
        || rubric?.parent?.translations?.[0]?.title
        || ''
}

const getOwnerName = (rubric) => rubric?.owner?.name || ''
const getOwnerEmail = (rubric) => rubric?.owner?.email || ''

/* ==========================================================
 * FRONTEND СОРТИРОВКА
 * ========================================================== */

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortRubrics = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'inMenu') return list.filter(item => !!item.in_menu)
    if (sortParam.value === 'notInMenu') return list.filter(item => !item.in_menu)

    if (sortParam.value === 'moderationPending') {
        return list.filter(item => moderationNum(item?.moderation_status) === 0)
    }

    if (sortParam.value === 'moderationApproved') {
        return list.filter(item => moderationNum(item?.moderation_status) === 1)
    }

    if (sortParam.value === 'moderationRejected') {
        return list.filter(item => moderationNum(item?.moderation_status) === 2)
    }

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        levelAsc: byNumberAsc('level'),
        levelDesc: byNumberDesc('level'),

        parentAsc: byNumberAsc('parent_id'),
        parentDesc: byNumberDesc('parent_id'),

        urlAsc: (a, b) =>
            normalize(a?.url).localeCompare(normalize(b?.url), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        urlDesc: (a, b) =>
            normalize(b?.url).localeCompare(normalize(a?.url), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        titleAsc: (a, b) =>
            normalize(getRubricTitle(a)).localeCompare(normalize(getRubricTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getRubricTitle(b)).localeCompare(normalize(getRubricTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        articlesAsc: byNumberAsc('articles_count'),
        articlesDesc: byNumberDesc('articles_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        inMenuAsc: byNumberAsc('in_menu'),
        inMenuDesc: byNumberDesc('in_menu'),

        moderationStatusAsc: (a, b) =>
            moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status)
            || safeNumber(a?.id) - safeNumber(b?.id),

        moderationStatusDesc: (a, b) =>
            moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerNameAsc: (a, b) =>
            normalize(getOwnerName(a)).localeCompare(normalize(getOwnerName(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerNameDesc: (a, b) =>
            normalize(getOwnerName(b)).localeCompare(normalize(getOwnerName(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerEmailAsc: (a, b) =>
            normalize(getOwnerEmail(a)).localeCompare(normalize(getOwnerEmail(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerEmailDesc: (a, b) =>
            normalize(getOwnerEmail(b)).localeCompare(normalize(getOwnerEmail(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * FRONTEND ПОИСК
 * ========================================================== */

const filteredRubrics = computed(() => {
    let filtered = localRubricsFlat.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortRubrics(filtered)
    }

    filtered = filtered.filter((rubric) => {
        const values = [
            rubric?.id,
            rubric?.url,
            rubric?.icon,
            rubric?.views,
            rubric?.moderation_note,
            getRubricTitle(rubric),
            getRubricShort(rubric),
            getRubricDescription(rubric),
            getParentTitle(rubric),
            getOwnerName(rubric),
            getOwnerEmail(rubric),
            rubric?.moderator?.name,
            rubric?.moderator?.email,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortRubrics(filtered)
})

const paginatedRubrics = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredRubrics.value.slice(start, start + perPage)
})

const displayedRubrics = computed(() => {
    return props.useServerProcessing
        ? rubricsList.value
        : paginatedRubrics.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

const showConfirmDeleteModal = ref(false)
const rubricToDeleteId = ref(null)
const rubricToDeleteTitle = ref('')

const confirmDelete = (rubricOrId, title = null) => {
    if (typeof rubricOrId === 'object') {
        rubricToDeleteId.value = rubricOrId.id
        rubricToDeleteTitle.value = title || getRubricTitle(rubricOrId)
    } else {
        rubricToDeleteId.value = rubricOrId
        rubricToDeleteTitle.value = title || `ID: ${rubricOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    rubricToDeleteId.value = null
    rubricToDeleteTitle.value = ''
}

const deleteRubric = () => {
    if (rubricToDeleteId.value === null) return

    const idToDelete = rubricToDeleteId.value
    const titleToDelete = rubricToDeleteTitle.value

    router.delete(route('admin.blogRubrics.destroy', { blogRubric: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Рубрика "${titleToDelete || 'ID: ' + idToDelete}" удалена.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Рубрика: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

const patchRubricInTree = (nodes, rubricId, callback) => {
    for (const node of nodes) {
        if (node.id === rubricId) {
            callback(node)
            return true
        }

        if (node.children?.length && patchRubricInTree(node.children, rubricId, callback)) {
            return true
        }
    }

    return false
}

const patchRubricInFlat = (rubricId, callback) => {
    const index = localRubricsFlat.value.findIndex((rubric) => rubric.id === rubricId)

    if (index !== -1) {
        callback(localRubricsFlat.value[index])
    }
}

/* ==========================================================
 * ОДИНОЧНЫЕ ОПЕРАЦИИ
 * ========================================================== */

const toggleActivity = (rubric) => {
    const newActivity = !rubric.activity
    const title = getRubricTitle(rubric)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogRubrics.updateActivity', { blogRubric: rubric.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchRubricInTree(localRubricsTree.value, rubric.id, node => {
                    node.activity = newActivity
                })

                patchRubricInFlat(rubric.id, node => {
                    node.activity = newActivity
                })

                rubric.activity = newActivity
                toast.success(`Рубрика "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

const cloneRubric = (rubric) => {
    const rubricId = rubric?.id
    const rubricTitle = getRubricTitle(rubric)

    if (!rubricId) {
        toast.error('Не удалось определить рубрику для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать рубрику "${rubricTitle}"?`)) return

    router.post(
        route('admin.actions.blogRubrics.clone', { blogRubric: rubricId }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => toast.success(`Рубрика "${rubricTitle}" успешно клонирована.`),
            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]
                toast.error(errors[errorKey] || `Ошибка клонирования рубрики "${rubricTitle}".`)
            },
        }
    )
}

const approveRubric = (rubric, status = 1, note = '') => {
    if (!rubric?.id) return

    router.put(
        route('admin.actions.blogRubrics.approve', { blogRubric: rubric.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchRubricInTree(localRubricsTree.value, rubric.id, node => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                patchRubricInFlat(rubric.id, node => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Рубрика одобрена' : 'Рубрика отклонена')
            },
            onError: () => toast.error('Ошибка модерации рубрики'),
        }
    )
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

    updateSortAndCollectChanges(localRubricsTree.value, null)

    if (!changes.length) return

    router.put(
        route('admin.actions.blogRubrics.updateSortBulk'),
        { items: changes },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия рубрик успешно обновлена.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || 'Ошибка обновления иерархии рубрик.')

                router.reload({
                    only: ['rubricsTree', 'rubrics'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * МАССОВЫЕ ДЕЙСТВИЯ
 * ========================================================== */

const selectedRubrics = ref([])

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
        ? getAllIds(localRubricsTree.value)
        : displayedRubrics.value.map(rubric => rubric.id)

    selectedRubrics.value = checked ? ids : []
}

const toggleAllCards = ({ ids, checked }) => {
    selectedRubrics.value = checked ? [...ids] : []
}

const toggleSelectRubric = (rubricId) => {
    const index = selectedRubrics.value.indexOf(rubricId)

    if (index > -1) {
        selectedRubrics.value.splice(index, 1)
    } else {
        selectedRubrics.value.push(rubricId)
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
    if (!selectedRubrics.value.length) {
        toast.warning('Выберите рубрики для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedRubrics.value]

    router.put(route('admin.actions.blogRubrics.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            updateActivityByIds(localRubricsTree.value, idsToUpdate, newActivity)

            localRubricsFlat.value = localRubricsFlat.value.map(item => {
                return idsToUpdate.includes(item.id)
                    ? { ...item, activity: newActivity }
                    : item
            })

            selectedRubrics.value = []
            toast.success('Активность выбранных рубрик обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

const bulkDelete = () => {
    if (!selectedRubrics.value.length) {
        toast.warning('Выберите хотя бы одну рубрику для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные рубрики?')) return

    router.delete(route('admin.actions.blogRubrics.bulkDestroy'), {
        data: { ids: selectedRubrics.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedRubrics.value = []
            toast.success('Выбранные рубрики успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка при массовом удалении рубрик.')
        },
    })
}

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
    <AdminLayout :title="t('rubrics')">
        <template #header>
            <TitlePage>{{ t('rubrics') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.blogRubrics.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addRubric') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminBlogRubricsProcessingMode"
                        :mode="adminBlogRubricsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="rubricsCount"
                    />
                </div>

                <SearchInput
                    v-if="rubricsCount && viewMode !== 'table' && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="rubricsCount && viewMode !== 'table' && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="rubricsCount && viewMode !== 'table'"
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
                        update-route="admin.settings.updateAdminCountRubrics"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="rubricsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                >
                    <CountTable>{{ rubricsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="rubricsCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredRubrics.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="rubrics"
                    />
                </div>

                <div
                    v-if="viewMode === 'table'"
                    class="mt-2 border border-gray-400 bg-white dark:bg-slate-800"
                >
                    <div
                        v-if="rubricsCount"
                        class="flex justify-between items-center px-3 py-2
                               border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    >
                        <div class="text-xs text-slate-600 dark:text-slate-200">
                            {{ t('selected') }}: {{ selectedRubrics.length }}
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
                        v-model="localRubricsTree"
                        tag="div"
                        item-key="id"
                        handle=".handle"
                        group="rubrics"
                        @end="handleDragEnd"
                        class="category-tree-root p-1"
                        :data-parent-id="null"
                    >
                        <template #item="{ element: rubric }">
                            <RubricTreeDraggable
                                :rubric="rubric"
                                :level="0"
                                :selected-rubrics="selectedRubrics"
                                :is-admin="isAdmin"
                                @toggle-activity="toggleActivity"
                                @delete="confirmDelete"
                                @clone="cloneRubric"
                                @toggle-select="toggleSelectRubric"
                                @request-drag-end="handleDragEnd"
                                @approve="approveRubric"
                            />
                        </template>

                        <template #header v-if="localRubricsTree.length === 0 && rubricsCount > 0">
                            <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                                {{ t('loading') }}
                            </div>
                        </template>

                        <template #footer v-if="localRubricsTree.length === 0 && rubricsCount === 0">
                            <div class="p-4 text-center text-slate-900 dark:text-slate-100">
                                {{ t('noData') }}
                            </div>
                        </template>
                    </draggable>
                </div>

                <RubricCardGrid
                    v-else
                    :rubrics="displayedRubrics"
                    :selectedRubrics="selectedRubrics"
                    :isAdmin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneRubric"
                    @toggle-select="toggleSelectRubric"
                    @toggle-all="toggleAllCards"
                    @approve="approveRubric"
                />

                <div
                    v-if="rubricsCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredRubrics.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="rubrics"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteRubric"
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
