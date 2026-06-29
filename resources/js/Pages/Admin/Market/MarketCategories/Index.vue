<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список категорий товаров MarketCategories
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 * - дерево категорий с drag&drop
 * - плоский список карточек
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
import BulkActionSelect from '@/Components/Admin/Market/MarketCategory/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketCategory/Sort/SortSelect.vue'
import CategoryCardGrid from '@/Components/Admin/Market/MarketCategory/View/CategoryCardGrid.vue'
import CategoryTreeDraggable from '@/Components/Admin/Market/MarketCategory/Tree/CategoryTreeDraggable.vue'

/** Сервисы страницы */
const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminMarketCategoriesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    categoriesTree: { type: Array, default: () => [] },
    categories: { type: [Array, Object], default: () => [] },
    categoriesCount: { type: Number, default: 0 },

    adminMarketCategoriesPerPage: { type: Number, default: 6 },
    adminMarketCategoriesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка роли администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_categories') || 'table'
)

/** Режим отображения: дерево или карточки */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_categories', value)
})

/** Сохранение режима отображения */
const categoriesList = computed(() => {
    if (Array.isArray(props.categories)) {
        return props.categories
    }

    if (Array.isArray(props.categories?.data)) {
        return props.categories.data
    }

    if (Array.isArray(props.categories?.data?.data)) {
        return props.categories.data.data
    }

    return []
})

/** Нормализация списка категорий из props */
const localCategoriesTree = ref([])

/** Локальные копии дерева и плоского списка */
const localCategoriesFlat = ref([])

/** Обновление локального дерева */
watch(
    () => props.categoriesTree,
    (value) => {
        localCategoriesTree.value = JSON.parse(JSON.stringify(value || []))
    },
    { immediate: true, deep: true }
)

/** Обновление локального плоского списка */
watch(
    categoriesList,
    (value) => {
        localCategoriesFlat.value = JSON.parse(JSON.stringify(value || []))
    },
    { immediate: true, deep: true }
)

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketCategoriesPerPage || 6)

/** Сохранение количества элементов на странице */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketCategories'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} категорий на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления количества категорий.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam || props.adminMarketCategoriesDefaultSort || 'idDesc'
)

/** Сохранение выбранной сортировки */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortMarketCategories'),
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

                toast.info('Сортировка категорий успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки категорий.')
            },
        }
    )
})

/** Поисковая строка */
const searchQuery = ref(props.search || '')

/** Текущая страница локальной пагинации */
const currentPage = ref(1)

/** Нормализация значения для поиска/сортировки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное преобразование в число */
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/** Числовое значение статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Получение текущего перевода категории */
const getCategoryTranslation = (category) => {
    return category?.translation || category?.translations?.[0] || {}
}

/** Получение названия категории */
const getCategoryTitle = (category) => {
    return category?.title
        || getCategoryTranslation(category)?.title
        || `ID: ${category?.id}`
}

/** Получение краткого описания категории */
const getCategoryShort = (category) => {
    return category?.short
        || getCategoryTranslation(category)?.short
        || ''
}

/** Получение полного описания категории */
const getCategoryDescription = (category) => {
    return category?.description
        || getCategoryTranslation(category)?.description
        || ''
}

/** Получение названия родительской категории */
const getParentTitle = (category) => {
    return category?.parent?.title
        || category?.parent?.translation?.title
        || category?.parent?.translations?.[0]?.title
        || ''
}

/** Получение имени владельца */
const getOwnerName = (category) => category?.owner?.name || ''

/** Получение email владельца */
const getOwnerEmail = (category) => category?.owner?.email || ''

/** Сортировка чисел по возрастанию */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел по убыванию */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка дат по возрастанию */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка дат по убыванию */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Локальная сортировка категорий */
const sortCategories = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'inMenu') return list.filter(item => !!item.in_menu)
    if (sortParam.value === 'notInMenu') return list.filter(item => !item.in_menu)

    if (sortParam.value === 'statusDraft') return list.filter(item => item?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter(item => item?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter(item => item?.status === 'archived')

    if (sortParam.value === 'moderationPending') return list.filter(item => moderationNum(item?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter(item => moderationNum(item?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter(item => moderationNum(item?.moderation_status) === 2)

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
            normalize(a?.url).localeCompare(normalize(b?.url), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        urlDesc: (a, b) =>
            normalize(b?.url).localeCompare(normalize(a?.url), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        titleAsc: (a, b) =>
            normalize(getCategoryTitle(a)).localeCompare(normalize(getCategoryTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getCategoryTitle(b)).localeCompare(normalize(getCategoryTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        childrenAsc: byNumberAsc('children_count'),
        childrenDesc: byNumberDesc('children_count'),

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
            normalize(getOwnerName(a)).localeCompare(normalize(getOwnerName(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerNameDesc: (a, b) =>
            normalize(getOwnerName(b)).localeCompare(normalize(getOwnerName(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerEmailAsc: (a, b) =>
            normalize(getOwnerEmail(a)).localeCompare(normalize(getOwnerEmail(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerEmailDesc: (a, b) =>
            normalize(getOwnerEmail(b)).localeCompare(normalize(getOwnerEmail(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: (a, b) =>
            normalize(a?.status).localeCompare(normalize(b?.status), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        statusDesc: (a, b) =>
            normalize(b?.status).localeCompare(normalize(a?.status), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),
        dateAsc: byDateAsc('created_at'),
        dateDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/** Локальный поиск категорий */
const filteredCategories = computed(() => {
    let filtered = localCategoriesFlat.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortCategories(filtered)
    }

    filtered = filtered.filter((category) => {
        const values = [
            category?.id,
            category?.url,
            category?.icon,
            category?.views,
            category?.status,
            category?.moderation_note,
            getCategoryTitle(category),
            getCategoryShort(category),
            getCategoryDescription(category),
            getParentTitle(category),
            getOwnerName(category),
            getOwnerEmail(category),
            category?.moderator?.name,
            category?.moderator?.email,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortCategories(filtered)
})

/** Локальная пагинация категорий */
const paginatedCategories = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredCategories.value.slice(start, start + perPage)
})

/** Итоговый список для карточек */
const displayedCategories = computed(() => {
    return props.useServerProcessing
        ? categoriesList.value
        : paginatedCategories.value
})

/** Сброс страницы при изменении пагинации или поиска */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const categoryToDeleteId = ref(null)
const categoryToDeleteTitle = ref('')

/** Подготовка подтверждения удаления */
const confirmDelete = (categoryOrId, title = null) => {
    if (typeof categoryOrId === 'object') {
        categoryToDeleteId.value = categoryOrId.id
        categoryToDeleteTitle.value = title || getCategoryTitle(categoryOrId)
    } else {
        categoryToDeleteId.value = categoryOrId
        categoryToDeleteTitle.value = title || `ID: ${categoryOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    categoryToDeleteId.value = null
    categoryToDeleteTitle.value = ''
}

/** Удаление одной категории */
const deleteCategory = () => {
    if (categoryToDeleteId.value === null) return

    const idToDelete = categoryToDeleteId.value
    const titleToDelete = categoryToDeleteTitle.value

    router.delete(route('admin.marketCategories.destroy', { marketCategory: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Категория "${titleToDelete || 'ID: ' + idToDelete}" удалена.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Категория: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Обновление категории в дереве */
const patchCategoryInTree = (nodes, categoryId, callback) => {
    for (const node of nodes) {
        if (node.id === categoryId) {
            callback(node)
            return true
        }

        if (node.children?.length && patchCategoryInTree(node.children, categoryId, callback)) {
            return true
        }
    }

    return false
}

/** Обновление категории в плоском списке */
const patchCategoryInFlat = (categoryId, callback) => {
    const index = localCategoriesFlat.value.findIndex((category) => category.id === categoryId)

    if (index !== -1) {
        callback(localCategoriesFlat.value[index])
    }
}

/** Переключение активности категории */
const toggleActivity = (category) => {
    const newActivity = !category.activity
    const title = getCategoryTitle(category)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketCategories.updateActivity', { marketCategory: category.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchCategoryInTree(localCategoriesTree.value, category.id, node => {
                    node.activity = newActivity
                })

                patchCategoryInFlat(category.id, node => {
                    node.activity = newActivity
                })

                category.activity = newActivity
                toast.success(`Категория "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Переключение показа категории в меню */
const toggleInMenu = (category) => {
    const newInMenu = !category.in_menu
    const title = getCategoryTitle(category)

    router.put(
        route('admin.actions.marketCategories.updateInMenu', {
            marketCategory: category.id,
        }),
        { in_menu: newInMenu },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchCategoryInTree(localCategoriesTree.value, category.id, node => {
                    node.in_menu = newInMenu
                })

                patchCategoryInFlat(category.id, node => {
                    node.in_menu = newInMenu
                })

                category.in_menu = newInMenu

                toast.success(
                    newInMenu
                        ? `Категория "${title}" добавлена в меню.`
                        : `Категория "${title}" скрыта из меню.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors.in_menu
                    || errors.general
                    || `Ошибка изменения показа в меню для "${title}".`
                )
            },
        }
    )
}

/** Модерация категории */
const approveCategory = (category, status = 1, note = '') => {
    if (!category?.id) return

    router.put(
        route('admin.actions.marketCategories.approve', { marketCategory: category.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchCategoryInTree(localCategoriesTree.value, category.id, node => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                patchCategoryInFlat(category.id, node => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Категория одобрена.' : 'Категория отклонена.')
            },
            onError: () => toast.error('Ошибка модерации категории.'),
        }
    )
}

/** Обработка изменения дерева через drag&drop */
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

    updateSortAndCollectChanges(localCategoriesTree.value, null)

    if (!changes.length) return

    router.put(
        route('admin.actions.marketCategories.updateSortBulk'),
        { items: changes },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия категорий успешно обновлена.'),
            onError: (errors) => {
                toast.error(errors.message || 'Ошибка обновления иерархии категорий.')

                router.reload({
                    only: ['categoriesTree', 'categories'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/** Выбранные категории */
const selectedCategories = ref([])

/** Получение всех ID из дерева */
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

/** Выбор/снятие выбора всех категорий */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false

    const ids = viewMode.value === 'table'
        ? getAllIds(localCategoriesTree.value)
        : displayedCategories.value.map(category => category.id)

    selectedCategories.value = checked ? ids : []
}

/** Выбор/снятие выбора всех карточек */
const toggleAllCards = ({ ids, checked }) => {
    selectedCategories.value = checked ? [...ids] : []
}

/** Выбор одной категории */
const toggleSelectCategory = (categoryId) => {
    const index = selectedCategories.value.indexOf(categoryId)

    if (index > -1) {
        selectedCategories.value.splice(index, 1)
    } else {
        selectedCategories.value.push(categoryId)
    }
}

/** Обновление активности выбранных категорий в дереве */
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

/** Массовое переключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedCategories.value.length) {
        toast.warning('Выберите категории для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedCategories.value]

    router.put(
        route('admin.actions.marketCategories.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                updateActivityByIds(localCategoriesTree.value, idsToUpdate, newActivity)

                localCategoriesFlat.value = localCategoriesFlat.value.map(item => {
                    return idsToUpdate.includes(item.id)
                        ? { ...item, activity: newActivity }
                        : item
                })

                selectedCategories.value = []
                toast.success('Активность выбранных категорий обновлена.')
            },
            onError: (errors) => {
                toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
            },
        }
    )
}

/** Массовое удаление категорий */
const bulkDelete = () => {
    if (!selectedCategories.value.length) {
        toast.warning('Выберите хотя бы одну категорию для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные категории?')) return

    router.delete(route('admin.actions.marketCategories.bulkDestroy'), {
        data: { ids: selectedCategories.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedCategories.value = []
            toast.success('Выбранные категории успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка при массовом удалении категорий.')
        },
    })
}

/** Обработка выбранного массового действия */
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
    <AdminLayout :title="t('marketCategories')">
        <template #header>
            <TitlePage>{{ t('marketCategories') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketCategories.create')">
                        {{ t('addMarketCategory') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketCategoriesProcessingMode"
                        :mode="adminMarketCategoriesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="categoriesCount"
                    />
                </div>

                <SearchInput
                    v-if="categoriesCount && !useServerProcessing && viewMode !== 'table'"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="categoriesCount && useServerProcessing && viewMode !== 'table'"
                    v-model="searchQuery"
                />

                <div
                    v-if="categoriesCount && viewMode !== 'table'"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing && viewMode !== 'table'"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-if="useServerProcessing && viewMode !== 'table'"
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountMarketCategories"
                    />

                    <SortSelect
                        v-if="viewMode !== 'table'"
                        :sortParam="sortParam"
                        @update:sortParam="(value) => (sortParam = value)"
                    />
                </div>

                <div
                    v-if="categoriesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                >
                    <CountTable>{{ categoriesCount }}</CountTable>

                    <BulkActionSelect
                        :disabled="!selectedCategories.length"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="categoriesCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCategories.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="categories"
                    />
                </div>

                <div v-if="viewMode === 'table'"
                     class="mt-2 border border-gray-400 bg-white dark:bg-slate-800">
                    <div
                        v-if="categoriesCount"
                        class="flex justify-between items-center px-3 py-2
                               border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    >
                        <div class="text-xs text-slate-600 dark:text-slate-200">
                            {{ t('selected') }}: {{ selectedCategories.length }}
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
                        v-model="localCategoriesTree"
                        item-key="id"
                        group="market-categories"
                        handle=".drag-handle"
                        @end="handleDragEnd"
                        class="category-tree-root p-1"
                    >
                        <template #item="{ element }">
                            <CategoryTreeDraggable
                                :category="element"
                                :selected-categories="selectedCategories"
                                :is-admin="isAdmin"
                                @toggle-activity="toggleActivity"
                                @delete="confirmDelete"
                                @toggle-select="toggleSelectCategory"
                                @approve="approveCategory"
                                @request-drag-end="handleDragEnd"
                                @toggle-menu="toggleInMenu"
                            />
                        </template>

                        <template #footer>
                            <div
                                v-if="!localCategoriesTree.length"
                                class="p-4 text-center text-slate-900 dark:text-slate-100"
                            >
                                {{ t('noData') }}
                            </div>
                        </template>
                    </draggable>
                </div>

                <CategoryCardGrid
                    v-else
                    :categories="displayedCategories"
                    :selected-categories="selectedCategories"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectCategory"
                    @toggle-all="toggleAllCards"
                    @approve="approveCategory"
                    @toggle-menu="toggleInMenu"
                />

                <div
                    v-if="categoriesCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCategories.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="categories"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteCategory"
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
