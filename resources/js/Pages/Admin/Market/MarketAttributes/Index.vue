<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список характеристик MarketAttribute
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { defineProps, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'

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

import BulkActionSelect from '@/Components/Admin/Market/MarketAttribute/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketAttribute/Sort/SortSelect.vue'
import AttributeTable from '@/Components/Admin/Market/MarketAttribute/Table/AttributeTable.vue'
import AttributeCardGrid from '@/Components/Admin/Market/MarketAttribute/View/AttributeCardGrid.vue'

/** Локализация */
const { t, locale } = useI18n()

/** Toast уведомления */
const toast = useToast()

/** Данные текущей страницы Inertia */
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminMarketAttributesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketAttributesPerPage: { type: Number, default: 10 },
    adminMarketAttributesDefaultSort: { type: String, default: 'idDesc' },

    attributes: { type: [Array, Object], default: () => [] },
    attributesCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка прав администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/* ===================== Data helpers ===================== */

/**
 * Перевод характеристики.
 *
 * Index получает только translation текущей локали.
 * translations[] в SharedResource отсутствует.
 */
const getAttributeTranslation = (attribute) => attribute?.translation || {}

/** Raw title для поиска и сортировки */
const getAttributeTranslationTitle = (attribute) =>
    getAttributeTranslation(attribute)?.title || ''

/** Название характеристики для отображения */
const getAttributeTitle = (attribute) =>
    getAttributeTranslationTitle(attribute) || `ID: ${attribute?.id}`

const getAttributeSubtitle = (attribute) =>
    getAttributeTranslation(attribute)?.subtitle || ''

const getAttributeShort = (attribute) =>
    getAttributeTranslation(attribute)?.short || ''

const getAttributeDescription = (attribute) =>
    getAttributeTranslation(attribute)?.description || ''

/** Перевод группы */
const getGroupTranslation = (attribute) =>
    attribute?.group?.translation || {}

const getGroupTitle = (attribute) =>
    getGroupTranslation(attribute)?.title || ''

const getGroupSubtitle = (attribute) =>
    getGroupTranslation(attribute)?.subtitle || ''

const getGroupShort = (attribute) =>
    getGroupTranslation(attribute)?.short || ''

/** Владелец */
const getOwnerName = (attribute) => attribute?.owner?.name || ''
const getOwnerEmail = (attribute) => attribute?.owner?.email || ''

/** Нормализация строки */
const normalize = (value) =>
    (value ?? '').toString().trim().toLowerCase()

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

/** Безопасное получение статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/* ===================== View ===================== */

/** Режим отображения списка */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_attributes') || 'cards'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_attributes', value)
})

/* ===================== Pagination settings ===================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketAttributesPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketAttributes'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} характеристик на странице.`),
            onError: (errors) => toast.error(
                errors.value || 'Ошибка обновления кол-ва характеристик.'
            ),
        }
    )
})

/* ===================== Sort setting ===================== */

const sortParam = ref(
    props.sortParam || props.adminMarketAttributesDefaultSort || 'idDesc'
)

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketAttributes'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(
                                new URLSearchParams(window.location.search)
                            ),
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

                toast.info('Сортировка характеристик успешно изменена.')
            },

            onError: (errors) => {
                toast.error(
                    errors.value || 'Ошибка обновления сортировки характеристик.'
                )
            },
        }
    )
})

/* ===================== Attributes ===================== */

/** Локальная копия списка характеристик */
const localAttributes = ref([])

/** Исходный список характеристик */
const attributesList = computed(() => {
    if (Array.isArray(props.attributes)) return props.attributes
    if (Array.isArray(props.attributes?.data)) return props.attributes.data
    if (Array.isArray(props.attributes?.data?.data)) return props.attributes.data.data
    if (Array.isArray(props.attributes?.resource)) return props.attributes.resource

    return []
})

watch(
    attributesList,
    (newVal) => {
        localAttributes.value = JSON.parse(
            JSON.stringify(newVal || [])
        )
    },
    { immediate: true, deep: true }
)

/* ===================== Delete ===================== */

/** Окно подтверждения удаления */
const showConfirmDeleteModal = ref(false)

/** ID удаляемой характеристики */
const attributeToDeleteId = ref(null)

/** Название удаляемой характеристики */
const attributeToDeleteTitle = ref('')

/** Подготовка удаления характеристики */
const confirmDelete = (attributeOrId, title = null) => {
    if (typeof attributeOrId === 'object') {
        attributeToDeleteId.value = attributeOrId.id
        attributeToDeleteTitle.value =
            title || getAttributeTitle(attributeOrId)
    } else {
        attributeToDeleteId.value = attributeOrId
        attributeToDeleteTitle.value =
            title || `ID: ${attributeOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    attributeToDeleteId.value = null
    attributeToDeleteTitle.value = ''
}

/** Удаление характеристики */
const deleteAttribute = () => {
    if (attributeToDeleteId.value === null) return

    const idToDelete = attributeToDeleteId.value
    const titleToDelete = attributeToDeleteTitle.value

    router.delete(
        route('admin.marketAttributes.destroy', {
            marketAttribute: idToDelete,
        }),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Характеристика "${titleToDelete || 'ID: ' + idToDelete}" удалена.`
                )
            },

            onError: (errors) => {
                const errorKey = Object.keys(errors || {})[0]
                const errorMsg =
                    errors.general
                    || errors[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${errorMsg} (Характеристика: ${titleToDelete || 'ID: ' + idToDelete})`
                )
            },

            onFinish: () => closeModal(),
        }
    )
}

/* ===================== Local patch ===================== */

/** Локальное обновление записи */
const patchLocalAttribute = (attributeId, callback) => {
    const index = localAttributes.value.findIndex(
        (attribute) => attribute.id === attributeId
    )

    if (index !== -1) {
        callback(localAttributes.value[index])
    }
}

/* ===================== Activity ===================== */

/** Переключение активности */
const toggleActivity = (attribute) => {
    const newActivity = !attribute.activity
    const title = getAttributeTitle(attribute)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketAttributes.updateActivity', {
            marketAttribute: attribute.id,
        }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalAttribute(attribute.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(
                    `Характеристика "${title}" ${actionText}.`
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

/* ===================== Search ===================== */

/** Поисковая строка */
const searchQuery = ref(props.search || '')

/** Текущая страница пагинации */
const currentPage = ref(1)

/* ===================== Local sorting helpers ===================== */

/**
 * Во всех сортировках вторичный порядок
 * соответствует backend: id DESC.
 */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (getter) => (a, b) =>
    normalize(getter(a)).localeCompare(
        normalize(getter(b)),
        locale.value
    )
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringDesc = (getter) => (a, b) =>
    normalize(getter(b)).localeCompare(
        normalize(getter(a)),
        locale.value
    )
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Локальная сортировка.
 *
 * Семантика должна совпадать
 * с MarketAttribute::scopeSortByParam().
 */
const sortAttributes = (attributes) => {
    const list = (attributes || []).slice()

    /** Activity filters */
    if (sortParam.value === 'activity') {
        return list
            .filter((attribute) => !!attribute.activity)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'inactive') {
        return list
            .filter((attribute) => !attribute.activity)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Required filters */
    if (sortParam.value === 'required') {
        return list
            .filter((attribute) => !!attribute.required)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'notRequired') {
        return list
            .filter((attribute) => !attribute.required)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Filterable filters */
    if (sortParam.value === 'filterable') {
        return list
            .filter((attribute) => !!attribute.filterable)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'notFilterable') {
        return list
            .filter((attribute) => !attribute.filterable)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Use for variants filters */
    if (sortParam.value === 'useForVariants') {
        return list
            .filter((attribute) => !!attribute.use_for_variants)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'notForVariants') {
        return list
            .filter((attribute) => !attribute.use_for_variants)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Visible filters */
    if (sortParam.value === 'visible') {
        return list
            .filter((attribute) => !!attribute.visible)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'hidden') {
        return list
            .filter((attribute) => !attribute.visible)
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Status filters */
    if (sortParam.value === 'statusDraft') {
        return list
            .filter((attribute) => attribute?.status === 'draft')
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'statusPublished') {
        return list
            .filter((attribute) => attribute?.status === 'published')
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'statusArchived') {
        return list
            .filter((attribute) => attribute?.status === 'archived')
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    /** Moderation filters */
    if (sortParam.value === 'moderationPending') {
        return list
            .filter(
                (attribute) =>
                    moderationNum(attribute?.moderation_status) === 0
            )
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'moderationApproved') {
        return list
            .filter(
                (attribute) =>
                    moderationNum(attribute?.moderation_status) === 1
            )
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    if (sortParam.value === 'moderationRejected') {
        return list
            .filter(
                (attribute) =>
                    moderationNum(attribute?.moderation_status) === 2
            )
            .sort((a, b) => safeNumber(b?.id) - safeNumber(a?.id))
    }

    const sortMap = {
        idAsc: (a, b) =>
            safeNumber(a?.id) - safeNumber(b?.id),

        idDesc: (a, b) =>
            safeNumber(b?.id) - safeNumber(a?.id),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        /**
         * Для сортировки используем raw translation title,
         * а не display fallback "ID: ...".
         */
        titleAsc: byStringAsc(
            getAttributeTranslationTitle
        ),

        titleDesc: byStringDesc(
            getAttributeTranslationTitle
        ),

        groupTitleAsc: byStringAsc(
            getGroupTitle
        ),

        groupTitleDesc: byStringDesc(
            getGroupTitle
        ),

        codeAsc: byStringAsc(
            (attribute) => attribute?.code
        ),

        codeDesc: byStringDesc(
            (attribute) => attribute?.code
        ),

        colorAsc: byStringAsc(
            (attribute) => attribute?.color
        ),

        colorDesc: byStringDesc(
            (attribute) => attribute?.color
        ),

        typeAsc: byStringAsc(
            (attribute) => attribute?.type
        ),

        typeDesc: byStringDesc(
            (attribute) => attribute?.type
        ),

        unitAsc: byStringAsc(
            (attribute) => attribute?.unit
        ),

        unitDesc: byStringDesc(
            (attribute) => attribute?.unit
        ),

        valuesCountAsc: byNumberAsc('values_count'),
        valuesCountDesc: byNumberDesc('values_count'),

        requiredAsc: byNumberAsc('required'),
        requiredDesc: byNumberDesc('required'),

        filterableAsc: byNumberAsc('filterable'),
        filterableDesc: byNumberDesc('filterable'),

        useForVariantsAsc: byNumberAsc('use_for_variants'),
        useForVariantsDesc: byNumberDesc('use_for_variants'),

        visibleAsc: byNumberAsc('visible'),
        visibleDesc: byNumberDesc('visible'),

        statusAsc: byStringAsc(
            (attribute) => attribute?.status
        ),

        statusDesc: byStringDesc(
            (attribute) => attribute?.status
        ),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        moderationStatusAsc: (a, b) =>
            moderationNum(a?.moderation_status)
            - moderationNum(b?.moderation_status)
            || safeNumber(b?.id) - safeNumber(a?.id),

        moderationStatusDesc: (a, b) =>
            moderationNum(b?.moderation_status)
            - moderationNum(a?.moderation_status)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerNameAsc: byStringAsc(
            getOwnerName
        ),

        ownerNameDesc: byStringDesc(
            getOwnerName
        ),

        ownerEmailAsc: byStringAsc(
            getOwnerEmail
        ),

        ownerEmailDesc: byStringDesc(
            getOwnerEmail
        ),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        showFromAtAsc: byDateAsc('show_from_at'),
        showFromAtDesc: byDateDesc('show_from_at'),

        showToAtAsc: byDateAsc('show_to_at'),
        showToAtDesc: byDateDesc('show_to_at'),

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

/**
 * Локальный поиск.
 *
 * Полностью повторяет MarketAttribute::scopeSearch():
 *
 * characteristic:
 * - code
 * - icon
 * - color
 * - type
 * - unit
 * - status
 * - moderation_note
 *
 * translation:
 * - title
 * - subtitle
 * - short
 * - description
 *
 * group.translation:
 * - title
 * - subtitle
 * - short
 *
 * owner:
 * - name
 * - email
 */
const filteredAttributes = computed(() => {
    let filtered = localAttributes.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortAttributes(filtered)
    }

    filtered = filtered.filter((attribute) => {
        const values = [
            attribute?.code,
            attribute?.icon,
            attribute?.color,
            attribute?.type,
            attribute?.unit,
            attribute?.status,
            attribute?.moderation_note,

            getAttributeTranslationTitle(attribute),
            getAttributeSubtitle(attribute),
            getAttributeShort(attribute),
            getAttributeDescription(attribute),

            getGroupTitle(attribute),
            getGroupSubtitle(attribute),
            getGroupShort(attribute),

            getOwnerName(attribute),
            getOwnerEmail(attribute),
        ]

        return values.some(
            (value) => normalize(value).includes(query)
        )
    })

    return sortAttributes(filtered)
})

/* ===================== Local pagination ===================== */

/** Локальная пагинация */
const paginatedAttributes = computed(() => {
    const perPage = Number(
        itemsPerPage.value || 10
    )

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredAttributes.value.slice(
        start,
        start + perPage
    )
})

/** Отображаемый список */
const displayedAttributes = computed(() => {
    return props.useServerProcessing
        ? attributesList.value
        : paginatedAttributes.value
})

watch(
    [itemsPerPage, searchQuery],
    () => {
        currentPage.value = 1
    }
)

/* ===================== Selection ===================== */

/** Выбранные характеристики */
const selectedAttributes = ref([])

/** Выделение всех элементов */
const toggleAll = (payload) => {
    const checked = Boolean(
        payload?.checked
        ?? payload?.target?.checked
        ?? false
    )

    const ids =
        payload?.ids
        ?? displayedAttributes.value.map(
            (attribute) => attribute.id
        )

    if (checked) {
        selectedAttributes.value = [
            ...new Set([
                ...selectedAttributes.value,
                ...ids,
            ]),
        ]
    } else {
        selectedAttributes.value =
            selectedAttributes.value.filter(
                (id) => !ids.includes(id)
            )
    }
}

/** Переключение выбора строки */
const toggleSelectAttribute = (attributeId) => {
    const index =
        selectedAttributes.value.indexOf(
            attributeId
        )

    if (index > -1) {
        selectedAttributes.value.splice(
            index,
            1
        )
    } else {
        selectedAttributes.value.push(
            attributeId
        )
    }
}

/* ===================== Bulk activity ===================== */

/** Массовое изменение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedAttributes.value.length) {
        toast.warning(
            'Выберите характеристики для активации/деактивации.'
        )
        return
    }

    const idsToUpdate = [
        ...selectedAttributes.value,
    ]

    router.put(
        route(
            'admin.actions.marketAttributes.bulkUpdateActivity'
        ),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localAttributes.value =
                    localAttributes.value.map(
                        (attribute) => {
                            return idsToUpdate.includes(
                                attribute.id
                            )
                                ? {
                                    ...attribute,
                                    activity: newActivity,
                                }
                                : attribute
                        }
                    )

                selectedAttributes.value = []

                toast.success(
                    'Активность характеристик массово обновлена.'
                )
            },

            onError: (errors) => {
                const msg =
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности.'

                toast.error(msg)
            },
        }
    )
}

/* ===================== Bulk delete ===================== */

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedAttributes.value.length) {
        toast.warning(
            'Выберите хотя бы одну характеристику для удаления.'
        )
        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные характеристики?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.marketAttributes.bulkDestroy'
        ),
        {
            data: {
                ids: selectedAttributes.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedAttributes.value = []

                toast.success(
                    'Массовое удаление характеристик успешно завершено.'
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(errors || {})[0]

                toast.error(
                    errors[errorKey]
                    || 'Произошла ошибка при удалении характеристик.'
                )
            },
        }
    )
}

/* ===================== Bulk actions ===================== */

/** Обработка массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({
            target: {
                checked: true,
            },
        })
    } else if (action === 'deselectAll') {
        toggleAll({
            target: {
                checked: false,
            },
        })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/* ===================== Moderation ===================== */

/** Модерация характеристики */
const approveAttribute = (
    attribute,
    status = 1,
    note = ''
) => {
    if (!attribute?.id) return

    router.put(
        route(
            'admin.actions.marketAttributes.approve',
            {
                marketAttribute: attribute.id,
            }
        ),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalAttribute(
                    attribute.id,
                    (node) => {
                        node.moderation_status =
                            status

                        node.is_approved =
                            status === 1

                        node.moderation_note =
                            note
                    }
                )

                toast.success(
                    status === 1
                        ? 'Характеристика одобрена.'
                        : 'Характеристика отклонена.'
                )
            },

            onError: () =>
                toast.error(
                    'Ошибка модерации характеристики.'
                ),
        }
    )
}

/* ===================== Drag & drop ===================== */

/** Массовое обновление сортировки */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map(
        (id, index) => ({
            id,
            sort: index,
        })
    )

    if (!items.length) return

    router.put(
        route(
            'admin.actions.marketAttributes.updateSortBulk'
        ),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () =>
                toast.success(
                    'Сортировка характеристик обновлена.'
                ),

            onError: (errors) => {
                console.error(
                    'Ошибка сортировки характеристик:',
                    errors
                )

                toast.error(
                    errors.message
                    || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketAttributes')">
        <template #header>
            <TitlePage>{{ t('marketAttributes') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketAttributes.create')">
                        {{ t('addMarketAttribute') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketAttributesProcessingMode"
                        :mode="adminMarketAttributesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="attributesCount"
                    />
                </div>

                <SearchInput
                    v-if="attributesCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="attributesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="attributesCount"
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
                        update-route="admin.settings.updateAdminCountMarketAttributes"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="attributesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ attributesCount }}</CountTable>

                    <BulkActionSelect
                        v-if="attributesCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="attributesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttributes.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="attributes"
                    />
                </div>

                <AttributeTable
                    v-if="viewMode === 'table'"
                    :attributes="displayedAttributes"
                    :selected-attributes="selectedAttributes"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAttribute"
                    @toggle-all="toggleAll"
                    @approve="approveAttribute"
                />

                <AttributeCardGrid
                    v-else
                    :attributes="displayedAttributes"
                    :selected-attributes="selectedAttributes"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAttribute"
                    @toggle-all="toggleAll"
                    @approve="approveAttribute"
                />

                <div
                    v-if="attributesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttributes.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="attributes"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteAttribute"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
