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
import { router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
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

/** Режим отображения */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_categories') || 'table'
)

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_categories', value)
})

/** Нормализация плоского списка из Inertia Resource */
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

/** Локальные данные */
const localCategoriesTree = ref([])
const localCategoriesFlat = ref([])

/** Синхронизация дерева */
watch(
    () => props.categoriesTree,
    (categories) => {
        localCategoriesTree.value = JSON.parse(
            JSON.stringify(categories || [])
        )
    },
    { immediate: true, deep: true }
)

/** Синхронизация плоского списка */
watch(
    categoriesList,
    (categories) => {
        localCategoriesFlat.value = JSON.parse(
            JSON.stringify(categories || [])
        )
    },
    { immediate: true, deep: true }
)

/** Количество элементов на странице */
const itemsPerPage = ref(
    props.adminMarketCategoriesPerPage || 6
)

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam
    || props.adminMarketCategoriesDefaultSort
    || 'idDesc'
)

/** Поиск */
const searchQuery = ref(props.search || '')

/** Страница локальной пагинации */
const currentPage = ref(1)

/** Выбранные категории */
const selectedCategories = ref([])

/** Сохранение количества элементов на странице */
watch(itemsPerPage, (value) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminCountMarketCategories'),
        { value },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(`Показ ${value} категорий на странице.`)
            },

            onError: (errors) => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления количества категорий.'
                )
            },
        }
    )
})

/** Сохранение сортировки */
watch(sortParam, (value) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortMarketCategories'),
        { value },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (
                    props.useServerProcessing
                    && viewMode.value !== 'table'
                ) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(
                                new URLSearchParams(window.location.search)
                            ),
                            sort: value || undefined,
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
                toast.error(
                    errors.value
                    || 'Ошибка обновления сортировки категорий.'
                )
            },
        }
    )
})

/** Сброс локальной пагинации */
watch([itemsPerPage, searchQuery, sortParam], () => {
    currentPage.value = 1
})

/** Нормализация значения */
const normalize = (value) => {
    return String(value ?? '').trim().toLowerCase()
}

/** Безопасное преобразование в число */
const numberValue = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const dateValue = (value) => {
    if (!value) return 0

    const timestamp = new Date(value).getTime()
    return Number.isNaN(timestamp) ? 0 : timestamp
}

/** Сортировка ID по убыванию */
const compareIdDesc = (a, b) => {
    return numberValue(b?.id) - numberValue(a?.id)
}

/** Сравнение числовых значений */
const compareNumber = (aValue, bValue, direction, a, b) => {
    const result = (
        numberValue(aValue) - numberValue(bValue)
    ) * direction

    return result || compareIdDesc(a, b)
}

/** Сравнение строк */
const compareText = (aValue, bValue, direction, a, b) => {
    const result = normalize(aValue).localeCompare(
        normalize(bValue),
        locale.value || undefined
    ) * direction

    return result || compareIdDesc(a, b)
}

/** Сравнение дат */
const compareDate = (aValue, bValue, direction, a, b) => {
    const result = (
        dateValue(aValue) - dateValue(bValue)
    ) * direction

    return result || compareIdDesc(a, b)
}

/** Фильтрация с backend-compatible сортировкой ID DESC */
const filterBy = (items, callback) => {
    return items.filter(callback).sort(compareIdDesc)
}

/** Текущий перевод категории */
const getTranslation = (category) => {
    return category?.translation || {}
}

/** Название категории для интерфейса */
const getTitle = (category) => {
    return getTranslation(category).title || `ID: ${category?.id}`
}

/** Название категории для сортировки */
const getTranslationTitle = (category) => {
    return getTranslation(category).title || ''
}

/** Перевод родительской категории */
const getParentTranslation = (category) => {
    return category?.parent?.translation || {}
}

/** Имя владельца */
const getOwnerName = (category) => {
    return category?.owner?.name || ''
}

/** Email владельца */
const getOwnerEmail = (category) => {
    return category?.owner?.email || ''
}

/** Локальная сортировка */
const sortCategories = (categories) => {
    const items = Array.isArray(categories)
        ? [...categories]
        : []

    if (sortParam.value === 'activity') {
        return filterBy(items, (category) => category.activity)
    }

    if (sortParam.value === 'inactive') {
        return filterBy(items, (category) => !category.activity)
    }

    if (sortParam.value === 'inMenu') {
        return filterBy(items, (category) => category.in_menu)
    }

    if (sortParam.value === 'notInMenu') {
        return filterBy(items, (category) => !category.in_menu)
    }

    if (sortParam.value === 'statusDraft') {
        return filterBy(
            items,
            (category) => category.status === 'draft'
        )
    }

    if (sortParam.value === 'statusPublished') {
        return filterBy(
            items,
            (category) => category.status === 'published'
        )
    }

    if (sortParam.value === 'statusArchived') {
        return filterBy(
            items,
            (category) => category.status === 'archived'
        )
    }

    if (sortParam.value === 'moderationPending') {
        return filterBy(
            items,
            (category) => numberValue(category.moderation_status) === 0
        )
    }

    if (sortParam.value === 'moderationApproved') {
        return filterBy(
            items,
            (category) => numberValue(category.moderation_status) === 1
        )
    }

    if (sortParam.value === 'moderationRejected') {
        return filterBy(
            items,
            (category) => numberValue(category.moderation_status) === 2
        )
    }

    const sortMap = {
        idAsc: (a, b) =>
            numberValue(a.id) - numberValue(b.id),

        idDesc: compareIdDesc,

        sortAsc: (a, b) =>
            compareNumber(a.sort, b.sort, 1, a, b),

        sortDesc: (a, b) =>
            compareNumber(a.sort, b.sort, -1, a, b),

        levelAsc: (a, b) =>
            compareNumber(a.level, b.level, 1, a, b),

        levelDesc: (a, b) =>
            compareNumber(a.level, b.level, -1, a, b),

        parentAsc: (a, b) =>
            compareNumber(a.parent_id, b.parent_id, 1, a, b),

        parentDesc: (a, b) =>
            compareNumber(a.parent_id, b.parent_id, -1, a, b),

        urlAsc: (a, b) =>
            compareText(a.url, b.url, 1, a, b),

        urlDesc: (a, b) =>
            compareText(a.url, b.url, -1, a, b),

        titleAsc: (a, b) =>
            compareText(
                getTranslationTitle(a),
                getTranslationTitle(b),
                1,
                a,
                b
            ),

        titleDesc: (a, b) =>
            compareText(
                getTranslationTitle(a),
                getTranslationTitle(b),
                -1,
                a,
                b
            ),

        viewsAsc: (a, b) =>
            compareNumber(a.views, b.views, 1, a, b),

        viewsDesc: (a, b) =>
            compareNumber(a.views, b.views, -1, a, b),

        imagesAsc: (a, b) =>
            compareNumber(
                a.images_count,
                b.images_count,
                1,
                a,
                b
            ),

        imagesDesc: (a, b) =>
            compareNumber(
                a.images_count,
                b.images_count,
                -1,
                a,
                b
            ),

        childrenAsc: (a, b) =>
            compareNumber(
                a.children_count,
                b.children_count,
                1,
                a,
                b
            ),

        childrenDesc: (a, b) =>
            compareNumber(
                a.children_count,
                b.children_count,
                -1,
                a,
                b
            ),

        activityAsc: (a, b) =>
            compareNumber(a.activity, b.activity, 1, a, b),

        activityDesc: (a, b) =>
            compareNumber(a.activity, b.activity, -1, a, b),

        inMenuAsc: (a, b) =>
            compareNumber(a.in_menu, b.in_menu, 1, a, b),

        inMenuDesc: (a, b) =>
            compareNumber(a.in_menu, b.in_menu, -1, a, b),

        moderationStatusAsc: (a, b) =>
            compareNumber(
                a.moderation_status,
                b.moderation_status,
                1,
                a,
                b
            ),

        moderationStatusDesc: (a, b) =>
            compareNumber(
                a.moderation_status,
                b.moderation_status,
                -1,
                a,
                b
            ),

        ownerNameAsc: (a, b) =>
            compareText(
                getOwnerName(a),
                getOwnerName(b),
                1,
                a,
                b
            ),

        ownerNameDesc: (a, b) =>
            compareText(
                getOwnerName(a),
                getOwnerName(b),
                -1,
                a,
                b
            ),

        ownerEmailAsc: (a, b) =>
            compareText(
                getOwnerEmail(a),
                getOwnerEmail(b),
                1,
                a,
                b
            ),

        ownerEmailDesc: (a, b) =>
            compareText(
                getOwnerEmail(a),
                getOwnerEmail(b),
                -1,
                a,
                b
            ),

        statusAsc: (a, b) =>
            compareText(a.status, b.status, 1, a, b),

        statusDesc: (a, b) =>
            compareText(a.status, b.status, -1, a, b),

        publishedAtAsc: (a, b) =>
            compareDate(
                a.published_at,
                b.published_at,
                1,
                a,
                b
            ),

        publishedAtDesc: (a, b) =>
            compareDate(
                a.published_at,
                b.published_at,
                -1,
                a,
                b
            ),

        createdAtAsc: (a, b) =>
            compareDate(
                a.created_at,
                b.created_at,
                1,
                a,
                b
            ),

        createdAtDesc: (a, b) =>
            compareDate(
                a.created_at,
                b.created_at,
                -1,
                a,
                b
            ),

        dateAsc: (a, b) =>
            compareDate(
                a.created_at,
                b.created_at,
                1,
                a,
                b
            ),

        dateDesc: (a, b) =>
            compareDate(
                a.created_at,
                b.created_at,
                -1,
                a,
                b
            ),

        updatedAtAsc: (a, b) =>
            compareDate(
                a.updated_at,
                b.updated_at,
                1,
                a,
                b
            ),

        updatedAtDesc: (a, b) =>
            compareDate(
                a.updated_at,
                b.updated_at,
                -1,
                a,
                b
            ),
    }

    const comparator = sortMap[sortParam.value]

    return comparator
        ? items.sort(comparator)
        : items
}

/**
 * Локальный поиск.
 *
 * Полностью соответствует MarketCategory::scopeSearch():
 * - url
 * - icon
 * - status
 * - moderation_note
 * - translation.title
 * - translation.subtitle
 * - translation.short
 * - translation.description
 * - parent.translation.title
 * - owner.name
 * - owner.email
 */
const filteredCategories = computed(() => {
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortCategories(localCategoriesFlat.value)
    }

    const categories = localCategoriesFlat.value.filter((category) => {
        const translation = getTranslation(category)
        const parentTranslation = getParentTranslation(category)

        const values = [
            category.url,
            category.icon,
            category.status,
            category.moderation_note,

            translation.title,
            translation.subtitle,
            translation.short,
            translation.description,

            parentTranslation.title,

            category.owner?.name,
            category.owner?.email,
        ]

        return values.some((value) => {
            return normalize(value).includes(query)
        })
    })

    return sortCategories(categories)
})

/** Локальная пагинация */
const paginatedCategories = computed(() => {
    const perPage = numberValue(itemsPerPage.value) || 10
    const start = (currentPage.value - 1) * perPage

    return filteredCategories.value.slice(
        start,
        start + perPage
    )
})

/** Итоговый список карточек */
const displayedCategories = computed(() => {
    return props.useServerProcessing
        ? categoriesList.value
        : paginatedCategories.value
})

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const categoryToDeleteId = ref(null)
const categoryToDeleteTitle = ref('')

/** Открытие модального окна удаления */
const confirmDelete = (categoryOrId, title = null) => {
    if (
        categoryOrId
        && typeof categoryOrId === 'object'
    ) {
        categoryToDeleteId.value = categoryOrId.id
        categoryToDeleteTitle.value =
            title || getTitle(categoryOrId)
    } else {
        categoryToDeleteId.value = categoryOrId
        categoryToDeleteTitle.value =
            title || `ID: ${categoryOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    categoryToDeleteId.value = null
    categoryToDeleteTitle.value = ''
}

/** Удаление категории */
const deleteCategory = () => {
    if (categoryToDeleteId.value === null) {
        return
    }

    const categoryId = categoryToDeleteId.value
    const categoryTitle = categoryToDeleteTitle.value

    router.delete(
        route(
            'admin.marketCategories.destroy',
            { marketCategory: categoryId }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Категория "${categoryTitle || `ID: ${categoryId}`}" удалена.`
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                const message = errors.general
                    || errors[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${message} (Категория: ${categoryTitle || `ID: ${categoryId}`})`
                )
            },

            onFinish: closeModal,
        }
    )
}

/** Обновление категории в дереве */
const patchCategoryInTree = (nodes, categoryId, callback) => {
    const items = Array.isArray(nodes) ? nodes : []

    for (const node of items) {
        if (node.id === categoryId) {
            callback(node)
            return true
        }

        if (
            node.children?.length
            && patchCategoryInTree(
                node.children,
                categoryId,
                callback
            )
        ) {
            return true
        }
    }

    return false
}

/** Обновление категории в плоском списке */
const patchCategoryInFlat = (categoryId, callback) => {
    const category = localCategoriesFlat.value.find(
        (item) => item.id === categoryId
    )

    if (category) {
        callback(category)
    }
}

/** Переключение активности */
const toggleActivity = (category) => {
    const activity = !category.activity
    const title = getTitle(category)

    router.put(
        route(
            'admin.actions.marketCategories.updateActivity',
            { marketCategory: category.id }
        ),
        { activity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchCategoryInTree(
                    localCategoriesTree.value,
                    category.id,
                    (node) => {
                        node.activity = activity
                    }
                )

                patchCategoryInFlat(
                    category.id,
                    (node) => {
                        node.activity = activity
                    }
                )

                category.activity = activity

                toast.success(
                    activity
                        ? `Категория "${title}" активирована.`
                        : `Категория "${title}" деактивирована.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors.activity
                    || errors.general
                    || `Ошибка изменения активности для "${title}".`
                )
            },
        }
    )
}

/** Переключение показа в меню */
const toggleInMenu = (category) => {
    const inMenu = !category.in_menu
    const title = getTitle(category)

    router.put(
        route(
            'admin.actions.marketCategories.updateInMenu',
            { marketCategory: category.id }
        ),
        { in_menu: inMenu },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchCategoryInTree(
                    localCategoriesTree.value,
                    category.id,
                    (node) => {
                        node.in_menu = inMenu
                    }
                )

                patchCategoryInFlat(
                    category.id,
                    (node) => {
                        node.in_menu = inMenu
                    }
                )

                category.in_menu = inMenu

                toast.success(
                    inMenu
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

/** Обновление состояния модерации */
const setModerationState = (category, status, note) => {
    const moderationStatus = numberValue(status)

    category.moderation_status = moderationStatus
    category.is_pending = moderationStatus === 0
    category.is_approved = moderationStatus === 1
    category.is_rejected = moderationStatus === 2
    category.moderation_note = note
}

/** Модерация категории */
const approveCategory = (category, status = 1, note = '') => {
    if (!category?.id) {
        return
    }

    const moderationStatus = numberValue(status)

    router.put(
        route(
            'admin.actions.marketCategories.approve',
            { marketCategory: category.id }
        ),
        {
            moderation_status: moderationStatus,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchCategoryInTree(
                    localCategoriesTree.value,
                    category.id,
                    (node) => {
                        setModerationState(
                            node,
                            moderationStatus,
                            note
                        )
                    }
                )

                patchCategoryInFlat(
                    category.id,
                    (node) => {
                        setModerationState(
                            node,
                            moderationStatus,
                            note
                        )
                    }
                )

                setModerationState(
                    category,
                    moderationStatus,
                    note
                )

                if (moderationStatus === 1) {
                    toast.success('Категория одобрена.')
                } else if (moderationStatus === 2) {
                    toast.success('Категория отклонена.')
                } else {
                    toast.success('Категория отправлена на модерацию.')
                }
            },

            onError: () => {
                toast.error('Ошибка модерации категории.')
            },
        }
    )
}

/** Сбор полного состояния дерева */
const collectTreeItems = (
    nodes,
    parentId = null,
    items = []
) => {
    const categories = Array.isArray(nodes)
        ? nodes
        : []

    categories.forEach((node, index) => {
        const normalizedParentId = parentId ?? null

        node.sort = index
        node.parent_id = normalizedParentId

        items.push({
            id: node.id,
            sort: index,
            parent_id: normalizedParentId,
        })

        patchCategoryInFlat(
            node.id,
            (category) => {
                category.sort = index
                category.parent_id = normalizedParentId
            }
        )

        if (node.children?.length) {
            collectTreeItems(
                node.children,
                node.id,
                items
            )
        }
    })

    return items
}

/** Сохранение drag&drop дерева */
const handleDragEnd = () => {
    const items = collectTreeItems(
        localCategoriesTree.value
    )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.marketCategories.updateSortBulk'
        ),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Иерархия категорий успешно обновлена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors.message
                    || errors.general
                    || 'Ошибка обновления иерархии категорий.'
                )

                router.reload({
                    only: [
                        'categoriesTree',
                        'categories',
                    ],
                    preserveScroll: true,
                })
            },
        }
    )
}

/** Получение всех ID дерева */
const getAllTreeIds = (nodes) => {
    const items = Array.isArray(nodes) ? nodes : []
    const ids = []

    items.forEach((node) => {
        ids.push(node.id)

        if (node.children?.length) {
            ids.push(...getAllTreeIds(node.children))
        }
    })

    return ids
}

/** Выбор всех элементов текущего представления */
const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids = viewMode.value === 'table'
        ? getAllTreeIds(localCategoriesTree.value)
        : displayedCategories.value.map(
            (category) => category.id
        )

    selectedCategories.value = checked
        ? [...ids]
        : []
}

/** Выбор всех карточек */
const toggleAllCards = ({ ids, checked }) => {
    selectedCategories.value = checked
        ? [...ids]
        : []
}

/** Выбор одной категории */
const toggleSelectCategory = (categoryId) => {
    const index = selectedCategories.value.indexOf(categoryId)

    if (index !== -1) {
        selectedCategories.value.splice(index, 1)
        return
    }

    selectedCategories.value.push(categoryId)
}

/** Изменение активности категорий дерева */
const updateTreeActivity = (nodes, ids, activity) => {
    const items = Array.isArray(nodes) ? nodes : []

    items.forEach((node) => {
        if (ids.includes(node.id)) {
            node.activity = activity
        }

        if (node.children?.length) {
            updateTreeActivity(
                node.children,
                ids,
                activity
            )
        }
    })
}

/** Массовое изменение активности */
const bulkToggleActivity = (activity) => {
    if (!selectedCategories.value.length) {
        toast.warning(
            'Выберите категории для активации/деактивации.'
        )
        return
    }

    const ids = [...selectedCategories.value]

    router.put(
        route(
            'admin.actions.marketCategories.bulkUpdateActivity'
        ),
        {
            ids,
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                updateTreeActivity(
                    localCategoriesTree.value,
                    ids,
                    activity
                )

                localCategoriesFlat.value =
                    localCategoriesFlat.value.map((category) => {
                        if (!ids.includes(category.id)) {
                            return category
                        }

                        return {
                            ...category,
                            activity,
                        }
                    })

                selectedCategories.value = []

                toast.success(
                    'Активность выбранных категорий обновлена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors.ids
                    || errors.activity
                    || errors.general
                    || 'Ошибка массового обновления активности.'
                )
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedCategories.value.length) {
        toast.warning(
            'Выберите хотя бы одну категорию для удаления.'
        )
        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные категории?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.marketCategories.bulkDestroy'
        ),
        {
            data: {
                ids: [...selectedCategories.value],
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedCategories.value = []

                toast.success(
                    'Выбранные категории успешно удалены.'
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]

                toast.error(
                    errors[errorKey]
                    || 'Ошибка при массовом удалении категорий.'
                )
            },
        }
    )
}

/** Массовое действие */
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
            <TitlePage>
                {{ t('marketCategories') }}
            </TitlePage>
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
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountMarketCategories"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <div
                    v-if="categoriesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                >
                    <CountTable>
                        {{ categoriesCount }}
                    </CountTable>

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

                <div
                    v-if="viewMode === 'table'"
                    class="mt-2 border border-gray-400
                           bg-white dark:bg-slate-800"
                >
                    <div
                        v-if="categoriesCount"
                        class="flex justify-between items-center px-3 py-2
                               border-b border-gray-400
                               bg-gray-100 dark:bg-slate-900"
                    >
                        <div class="text-xs text-slate-600 dark:text-slate-200">
                            {{ t('selected') }}:
                            {{ selectedCategories.length }}
                        </div>

                        <label
                            class="flex items-center text-xs
                                   text-slate-600 dark:text-slate-200
                                   cursor-pointer"
                        >
                            <span>
                                {{ t('selectAll') }}
                            </span>

                            <input
                                type="checkbox"
                                class="form-checkbox rounded-sm
                                       text-indigo-500 ml-2"
                                :title="t('selectAll')"
                                @change="toggleAll"
                            >
                        </label>
                    </div>

                    <draggable
                        v-model="localCategoriesTree"
                        item-key="id"
                        group="market-categories"
                        handle=".drag-handle"
                        class="category-tree-root p-1"
                        @end="handleDragEnd"
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
                                class="p-4 text-center
                                       text-slate-900 dark:text-slate-100"
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
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
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
            :onCancel="closeModal"
            :onConfirm="deleteCategory"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>

<style scoped>
.category-tree-root {
    padding: 5px;
}
</style>
