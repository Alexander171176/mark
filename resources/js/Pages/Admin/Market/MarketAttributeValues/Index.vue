<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список значений характеристик MarketAttributeValue
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
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

import BulkActionSelect from '@/Components/Admin/Market/MarketAttributeValue/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketAttributeValue/Sort/SortSelect.vue'
import AttributeValueTable from '@/Components/Admin/Market/MarketAttributeValue/Table/AttributeValueTable.vue'
import AttributeValueCardGrid from '@/Components/Admin/Market/MarketAttributeValue/View/AttributeValueCardGrid.vue'

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

    adminMarketAttributeValuesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketAttributeValuesPerPage: { type: Number, default: 10 },
    adminMarketAttributeValuesDefaultSort: { type: String, default: 'idDesc' },

    values: { type: [Array, Object], default: () => [] },
    valuesCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка прав администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []

    return roles.some((role) => role?.name === 'admin')
})

/* ===================== Translation helpers ===================== */

/** Перевод значения характеристики */
const getValueTranslation = (value) => value?.translation || {}

/**
 * Реальное название перевода.
 *
 * Используется для поиска и сортировки,
 * чтобы fallback ID не становился
 * частью бизнес-данных.
 */
const getValueTranslationTitle = (value) => {
    return getValueTranslation(value)?.title || ''
}

/** Название для отображения */
const getValueTitle = (value) => {
    return getValueTranslationTitle(value)
        || `ID: ${value?.id}`
}

/** Подзаголовок значения */
const getValueSubtitle = (value) => {
    return getValueTranslation(value)?.subtitle || ''
}

/** Краткое описание значения */
const getValueShort = (value) => {
    return getValueTranslation(value)?.short || ''
}

/** Полное описание значения */
const getValueDescription = (value) => {
    return getValueTranslation(value)?.description || ''
}

/** Перевод родительской характеристики */
const getAttributeTranslation = (value) => {
    return value?.attribute?.translation || {}
}

/**
 * Реальное название характеристики.
 *
 * Используется для поиска и сортировки.
 */
const getAttributeTranslationTitle = (value) => {
    return getAttributeTranslation(value)?.title || ''
}

/** Подзаголовок характеристики */
const getAttributeSubtitle = (value) => {
    return getAttributeTranslation(value)?.subtitle || ''
}

/** Краткое описание характеристики */
const getAttributeShort = (value) => {
    return getAttributeTranslation(value)?.short || ''
}

/** Полное описание характеристики */
const getAttributeDescription = (value) => {
    return getAttributeTranslation(value)?.description || ''
}

/** Имя модератора */
const getModeratorName = (value) => {
    return value?.moderator?.name || ''
}

/** Email модератора */
const getModeratorEmail = (value) => {
    return value?.moderator?.email || ''
}

/* ===================== Normalization helpers ===================== */

/** Нормализация строки */
const normalize = (value) => {
    return (value ?? '')
        .toString()
        .trim()
        .toLowerCase()
}

/** Безопасное преобразование в число */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/** Безопасное преобразование даты */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/** Безопасное получение статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/* ===================== View mode ===================== */

/** Режим отображения списка */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_attribute_values')
    || 'cards'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_market_attribute_values',
        value
    )
})

/* ===================== Per page ===================== */

/** Количество элементов на странице */
const itemsPerPage = ref(
    props.adminMarketAttributeValuesPerPage || 10
)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketAttributeValues'),
        {
            value: newVal,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newVal} значений характеристик на странице.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления кол-ва значений.'
                )
            },
        }
    )
})

/* ===================== Sorting setting ===================== */

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam
    || props.adminMarketAttributeValuesDefaultSort
    || 'idDesc'
)

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketAttributeValues'),
        {
            value: newVal,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(
                                new URLSearchParams(
                                    window.location.search
                                )
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

                toast.info(
                    'Сортировка значений характеристик успешно изменена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors.value
                    || 'Ошибка обновления сортировки значений.'
                )
            },
        }
    )
})

/* ===================== Source data ===================== */

/** Локальная копия списка */
const localValues = ref([])

/** Исходный список */
const valuesList = computed(() => {
    if (Array.isArray(props.values)) {
        return props.values
    }

    if (Array.isArray(props.values?.data)) {
        return props.values.data
    }

    if (Array.isArray(props.values?.data?.data)) {
        return props.values.data.data
    }

    if (Array.isArray(props.values?.resource)) {
        return props.values.resource
    }

    return []
})

watch(
    valuesList,
    (newVal) => {
        localValues.value = JSON.parse(
            JSON.stringify(
                newVal || []
            )
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ===================== Delete ===================== */

/** Окно подтверждения удаления */
const showConfirmDeleteModal = ref(false)

/** ID удаляемого значения */
const valueToDeleteId = ref(null)

/** Название удаляемого значения */
const valueToDeleteTitle = ref('')

/** Подготовка удаления */
const confirmDelete = (
    valueOrId,
    title = null
) => {
    if (
        valueOrId
        && typeof valueOrId === 'object'
    ) {
        valueToDeleteId.value =
            valueOrId.id

        valueToDeleteTitle.value =
            title
            || getValueTitle(valueOrId)
    } else {
        valueToDeleteId.value =
            valueOrId

        valueToDeleteTitle.value =
            title
            || `ID: ${valueOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    valueToDeleteId.value = null
    valueToDeleteTitle.value = ''
}

/** Удаление */
const deleteValue = () => {
    if (
        valueToDeleteId.value === null
    ) {
        return
    }

    const idToDelete =
        valueToDeleteId.value

    const titleToDelete =
        valueToDeleteTitle.value

    router.delete(
        route(
            'admin.marketAttributeValues.destroy',
            {
                marketAttributeValue: idToDelete,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Значение характеристики "${titleToDelete || 'ID: ' + idToDelete}" удалено.`
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const errorMsg =
                    errors.general
                    || errors[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${errorMsg} (Значение: ${titleToDelete || 'ID: ' + idToDelete})`
                )
            },

            onFinish: () => {
                closeModal()
            },
        }
    )
}

/* ===================== Local patch ===================== */

/** Локальное обновление записи */
const patchLocalValue = (
    valueId,
    callback
) => {
    const index = localValues.value.findIndex(
        (value) => value.id === valueId
    )

    if (index !== -1) {
        callback(
            localValues.value[index]
        )
    }
}

/* ===================== Activity ===================== */

/** Переключение активности */
const toggleActivity = (value) => {
    const newActivity =
        ! value.activity

    const title =
        getValueTitle(value)

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.marketAttributeValues.updateActivity',
            {
                marketAttributeValue: value.id,
            }
        ),
        {
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalValue(
                    value.id,
                    (node) => {
                        node.activity =
                            newActivity

                        node.is_active =
                            newActivity
                    }
                )

                toast.success(
                    `Значение характеристики "${title}" ${actionText}.`
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

/* ===================== Search / Pagination ===================== */

/** Поисковая строка */
const searchQuery = ref(
    props.search || ''
)

/** Текущая страница пагинации */
const currentPage = ref(1)

/* ===================== Sort helpers ===================== */

/**
 * Числовая сортировка по возрастанию.
 *
 * При равенстве сервер использует id DESC.
 */
const byNumberAsc = (field) => (a, b) => {
    return safeNumber(a?.[field])
        - safeNumber(b?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/**
 * Числовая сортировка по убыванию.
 *
 * При равенстве сервер использует id DESC.
 */
const byNumberDesc = (field) => (a, b) => {
    return safeNumber(b?.[field])
        - safeNumber(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/**
 * Сортировка даты по возрастанию.
 *
 * При равенстве сервер использует id DESC.
 */
const byDateAsc = (field) => (a, b) => {
    return safeDate(a?.[field])
        - safeDate(b?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/**
 * Сортировка даты по убыванию.
 *
 * При равенстве сервер использует id DESC.
 */
const byDateDesc = (field) => (a, b) => {
    return safeDate(b?.[field])
        - safeDate(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/**
 * Строковая сортировка по возрастанию.
 *
 * getter должен возвращать реальные данные,
 * а не display fallback.
 */
const byStringAsc = (getter) => (a, b) => {
    return normalize(
            getter(a)
        ).localeCompare(
            normalize(
                getter(b)
            ),
            locale.value
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/** Строковая сортировка по убыванию */
const byStringDesc = (getter) => (a, b) => {
    return normalize(
            getter(b)
        ).localeCompare(
            normalize(
                getter(a)
            ),
            locale.value
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/** Сортировка по ID DESC */
const byIdDesc = (a, b) => {
    return safeNumber(b?.id)
        - safeNumber(a?.id)
}

/* ===================== Local sorting ===================== */

/** Сортировка списка */
const sortValues = (values) => {
    const list =
        (values || []).slice()

    /**
     * Фильтрующие режимы сортировки
     * на backend после фильтра используют
     * market_attribute_values.id DESC.
     */
    if (
        sortParam.value === 'activity'
    ) {
        return list
            .filter(
                (value) =>
                    value?.activity
            )
            .sort(byIdDesc)
    }

    if (
        sortParam.value === 'inactive'
    ) {
        return list
            .filter(
                (value) =>
                    ! value?.activity
            )
            .sort(byIdDesc)
    }

    if (sortParam.value === 'statusDraft') {
        return list
            .filter(
                (value) =>
                    value?.status
                    === 'draft'
            )
            .sort(byIdDesc)
    }

    if (
        sortParam.value
        === 'statusPublished'
    ) {
        return list
            .filter(
                (value) =>
                    value?.status
                    === 'published'
            )
            .sort(byIdDesc)
    }

    if (
        sortParam.value
        === 'statusArchived'
    ) {
        return list
            .filter(
                (value) =>
                    value?.status
                    === 'archived'
            )
            .sort(byIdDesc)
    }

    if (
        sortParam.value
        === 'moderationPending'
    ) {
        return list
            .filter(
                (value) =>
                    moderationNum(
                        value?.moderation_status
                    ) === 0
            )
            .sort(byIdDesc)
    }

    if (
        sortParam.value
        === 'moderationApproved'
    ) {
        return list
            .filter(
                (value) =>
                    moderationNum(
                        value?.moderation_status
                    ) === 1
            )
            .sort(byIdDesc)
    }

    if (
        sortParam.value
        === 'moderationRejected'
    ) {
        return list
            .filter(
                (value) =>
                    moderationNum(
                        value?.moderation_status
                    ) === 2
            )
            .sort(byIdDesc)
    }

    const sortMap = {
        idAsc: (a, b) =>
            safeNumber(a?.id)
            - safeNumber(b?.id),

        idDesc: byIdDesc,

        sortAsc:
            byNumberAsc('sort'),

        sortDesc:
            byNumberDesc('sort'),

        /**
         * Используем именно translation.title.
         * Fallback `ID: ...` не должен влиять
         * на сортировку.
         */
        titleAsc:
            byStringAsc(
                getValueTranslationTitle
            ),

        titleDesc:
            byStringDesc(
                getValueTranslationTitle
            ),

        /**
         * Backend сортирует по
         * market_attribute_translations.title,
         * поэтому code fallback здесь не используем.
         */
        attributeTitleAsc:
            byStringAsc(
                getAttributeTranslationTitle
            ),

        attributeTitleDesc:
            byStringDesc(
                getAttributeTranslationTitle
            ),

        codeAsc:
            byStringAsc(
                (value) =>
                    value?.code
                    || ''
            ),

        codeDesc:
            byStringDesc(
                (value) =>
                    value?.code
                    || ''
            ),

        colorAsc:
            byStringAsc(
                (value) =>
                    value?.color
                    || ''
            ),

        colorDesc:
            byStringDesc(
                (value) =>
                    value?.color
                    || ''
            ),

        activityAsc:
            byNumberAsc(
                'activity'
            ),

        activityDesc:
            byNumberDesc(
                'activity'
            ),

        statusAsc:
            byStringAsc(
                (value) =>
                    value?.status
                    || ''
            ),

        statusDesc:
            byStringDesc(
                (value) =>
                    value?.status
                    || ''
            ),

        moderationStatusAsc:
            byNumberAsc(
                'moderation_status'
            ),

        moderationStatusDesc:
            byNumberDesc(
                'moderation_status'
            ),

        publishedAtAsc:
            byDateAsc(
                'published_at'
            ),

        publishedAtDesc:
            byDateDesc(
                'published_at'
            ),

        showFromAtAsc:
            byDateAsc(
                'show_from_at'
            ),

        showFromAtDesc:
            byDateDesc(
                'show_from_at'
            ),

        showToAtAsc:
            byDateAsc(
                'show_to_at'
            ),

        showToAtDesc:
            byDateDesc(
                'show_to_at'
            ),

        createdAtAsc:
            byDateAsc(
                'created_at'
            ),

        createdAtDesc:
            byDateDesc(
                'created_at'
            ),

        dateAsc:
            byDateAsc(
                'created_at'
            ),

        dateDesc:
            byDateDesc(
                'created_at'
            ),

        updatedAtAsc:
            byDateAsc(
                'updated_at'
            ),

        updatedAtDesc:
            byDateDesc(
                'updated_at'
            ),
    }

    return sortMap[
        sortParam.value
        ]
        ? list.sort(
            sortMap[
                sortParam.value
                ]
        )
        : list
}

/* ===================== Local search ===================== */

/**
 * Отфильтрованный список.
 *
 * Frontend search повторяет
 * MarketAttributeValue::scopeSearch():
 *
 * Value:
 * - code;
 * - color;
 * - status;
 * - moderation_note;
 * - translation title/subtitle/short/description.
 *
 * Attribute:
 * - code;
 * - type;
 * - unit;
 * - translation title/subtitle/short/description.
 *
 * Moderator:
 * - name;
 * - email.
 */
const filteredValues = computed(() => {
    let filtered =
        localValues.value || []

    const query =
        normalize(
            searchQuery.value
        )

    if (! query) {
        return sortValues(
            filtered
        )
    }

    filtered = filtered.filter(
        (value) => {
            const searchableValues = [
                /** MarketAttributeValue */
                value?.code,
                value?.color,
                value?.status,
                value?.moderation_note,

                /** MarketAttributeValue translation */
                getValueTranslationTitle(
                    value
                ),
                getValueSubtitle(
                    value
                ),
                getValueShort(
                    value
                ),
                getValueDescription(
                    value
                ),

                /** MarketAttribute */
                value?.attribute?.code,
                value?.attribute?.type,
                value?.attribute?.unit,

                /** MarketAttribute translation */
                getAttributeTranslationTitle(
                    value
                ),
                getAttributeSubtitle(
                    value
                ),
                getAttributeShort(
                    value
                ),
                getAttributeDescription(
                    value
                ),

                /** Moderator */
                getModeratorName(
                    value
                ),
                getModeratorEmail(
                    value
                ),
            ]

            return searchableValues.some(
                (item) =>
                    normalize(
                        item
                    ).includes(
                        query
                    )
            )
        }
    )

    return sortValues(
        filtered
    )
})

/** Локальная пагинация */
const paginatedValues = computed(() => {
    const perPage =
        Number(
            itemsPerPage.value
            || 10
        )

    const start =
        (
            currentPage.value
            - 1
        )
        * perPage

    return filteredValues.value.slice(
        start,
        start + perPage
    )
})

/** Отображаемый список */
const displayedValues = computed(() => {
    return props.useServerProcessing
        ? valuesList.value
        : paginatedValues.value
})

watch(
    [
        itemsPerPage,
        searchQuery,
    ],
    () => {
        currentPage.value = 1
    }
)

/* ===================== Selection ===================== */

/** Выбранные значения характеристик */
const selectedValues = ref([])

/** Выделение всех элементов */
const toggleAll = (payload) => {
    const checked =
        Boolean(
            payload?.checked
            ?? payload?.target?.checked
            ?? false
        )

    const ids =
        payload?.ids
        ?? displayedValues.value.map(
            (value) => value.id
        )

    if (checked) {
        selectedValues.value = [
            ...new Set([
                ...selectedValues.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedValues.value =
        selectedValues.value.filter(
            (id) =>
                ! ids.includes(id)
        )
}

/** Переключение выбора строки */
const toggleSelectValue = (valueId) => {
    const index =
        selectedValues.value.indexOf(
            valueId
        )

    if (index > -1) {
        selectedValues.value.splice(
            index,
            1
        )

        return
    }

    selectedValues.value.push(
        valueId
    )
}

/* ===================== Bulk activity ===================== */

/** Массовое изменение активности */
const bulkToggleActivity = (
    newActivity
) => {
    if (
        ! selectedValues.value.length
    ) {
        toast.warning(
            'Выберите значения характеристик для активации/деактивации.'
        )

        return
    }

    const idsToUpdate = [
        ...selectedValues.value,
    ]

    router.put(
        route(
            'admin.actions.marketAttributeValues.bulkUpdateActivity'
        ),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localValues.value =
                    localValues.value.map(
                        (value) => {
                            if (
                                ! idsToUpdate.includes(
                                    value.id
                                )
                            ) {
                                return value
                            }

                            return {
                                ...value,
                                activity:
                                newActivity,

                                is_active:
                                newActivity,
                            }
                        }
                    )

                selectedValues.value = []

                toast.success(
                    'Активность значений характеристик массово обновлена.'
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
    if (
        ! selectedValues.value.length
    ) {
        toast.warning(
            'Выберите хотя бы одно значение характеристики для удаления.'
        )

        return
    }

    if (
        ! confirm(
            'Вы уверены, что хотите удалить выбранные значения характеристик?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.marketAttributeValues.bulkDestroy'
        ),
        {
            data: {
                ids:
                selectedValues.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedValues.value = []

                toast.success(
                    'Массовое удаление значений характеристик успешно завершено.'
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors[errorKey]
                    || 'Произошла ошибка при удалении значений характеристик.'
                )
            },
        }
    )
}

/* ===================== Bulk actions ===================== */

/** Обработка массовых действий */
const handleBulkAction = (event) => {
    const action =
        event.target.value

    if (
        action === 'selectAll'
    ) {
        toggleAll({
            checked: true,
        })
    } else if (
        action === 'deselectAll'
    ) {
        toggleAll({
            checked: false,
        })
    } else if (
        action === 'activate'
    ) {
        bulkToggleActivity(
            true
        )
    } else if (
        action === 'deactivate'
    ) {
        bulkToggleActivity(
            false
        )
    } else if (
        action === 'delete'
    ) {
        bulkDelete()
    }

    event.target.value = ''
}

/* ===================== Moderation ===================== */

/** Модерация */
const approveValue = (
    value,
    status = 1,
    note = ''
) => {
    if (! value?.id) {
        return
    }

    router.put(
        route(
            'admin.actions.marketAttributeValues.approve',
            {
                marketAttributeValue:
                value.id,
            }
        ),
        {
            moderation_status:
            status,

            moderation_note:
            note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalValue(
                    value.id,
                    (node) => {
                        node.moderation_status =
                            status

                        node.is_pending =
                            status === 0

                        node.is_approved =
                            status === 1

                        node.is_rejected =
                            status === 2

                        node.moderation_note =
                            note
                    }
                )

                toast.success(
                    status === 1
                        ? 'Значение характеристики одобрено.'
                        : 'Значение характеристики отклонено.'
                )
            },

            onError: () => {
                toast.error(
                    'Ошибка модерации значения характеристики.'
                )
            },
        }
    )
}

/* ===================== Drag & Drop ===================== */

/** Массовое обновление сортировки */
const handleSortOrderUpdate = (
    newOrderIds
) => {
    const items =
        newOrderIds.map(
            (id, index) => ({
                id,
                sort: index,
            })
        )

    if (! items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.marketAttributeValues.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Сортировка значений характеристик обновлена.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка сортировки значений характеристик:',
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
    <AdminLayout :title="t('marketAttributeValues')">
        <template #header>
            <TitlePage>
                {{ t('marketAttributeValues') }}
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
                    <DefaultButton :href="route('admin.marketAttributeValues.create')">
                        {{ t('addMarketAttributeValue') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketAttributeValuesProcessingMode"
                        :mode="adminMarketAttributeValuesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="valuesCount"
                    />
                </div>

                <SearchInput
                    v-if="valuesCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="valuesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="valuesCount"
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
                        update-route="admin.settings.updateAdminCountMarketAttributeValues"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="valuesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ valuesCount }}
                    </CountTable>

                    <BulkActionSelect
                        v-if="valuesCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <div
                    v-if="valuesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredValues.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="values"
                    />
                </div>

                <AttributeValueTable
                    v-if="viewMode === 'table'"
                    :values="displayedValues"
                    :selected-values="selectedValues"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectValue"
                    @toggle-all="toggleAll"
                    @approve="approveValue"
                />

                <AttributeValueCardGrid
                    v-else
                    :values="displayedValues"
                    :selected-values="selectedValues"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectValue"
                    @toggle-all="toggleAll"
                    @approve="approveValue"
                />

                <div
                    v-if="valuesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredValues.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="values"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteValue"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
