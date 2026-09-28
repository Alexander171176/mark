<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список динамических форм.
 *
 * Возможности:
 * - режимы обработки: frontend | server | auto;
 * - локальный / серверный поиск;
 * - локальная / серверная сортировка;
 * - локальная / серверная пагинация;
 * - изменение количества элементов;
 * - изменение активности;
 * - массовые действия;
 * - табличное / карточное отображение;
 * - просмотр;
 * - редактирование;
 * - переход к полям формы;
 * - переход к заявкам;
 * - удаление.
 */

import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'

import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'

import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'

import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'

import SortSelect from '@/Components/Admin/Form/Form/Sort/SortSelect.vue'
import BulkActionSelect from '@/Components/Admin/Form/Form/Select/BulkActionSelect.vue'

import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'

import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'

import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'

import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import FormTable from '@/Components/Admin/Form/Form/Table/FormTable.vue'
import FormCardGrid from '@/Components/Admin/Form/Form/View/FormCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()

/* ===================== Props ===================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    adminFormsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    adminFormsPerPage: {
        type: Number,
        default: 12,
    },

    adminFormsDefaultSort: {
        type: String,
        default: 'sortAsc',
    },

    forms: {
        type: [Array, Object],
        default: () => [],
    },

    formsCount: {
        type: Number,
        default: 0,
    },

    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

    statuses: {
        type: Object,
        default: () => ({}),
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ===================== Helpers ===================== */

/**
 * Получение перевода формы.
 */
const getFormTranslation = (form) => {
    return form?.translation || {}
}

/**
 * Название формы без fallback.
 */
const getFormTitleValue = (form) => {
    return getFormTranslation(form)?.title || ''
}

/**
 * Название формы.
 */
const getFormTitle = (form) => {
    return getFormTitleValue(form)
        || form?.code
        || `ID: ${form?.id}`
}

/**
 * Подзаголовок формы.
 */
const getFormSubtitle = (form) => {
    return getFormTranslation(form)?.subtitle || ''
}

/**
 * Описание формы.
 */
const getFormDescription = (form) => {
    return getFormTranslation(form)?.description || ''
}

/**
 * Имя владельца.
 */
const getOwnerName = (form) => {
    return form?.owner?.name || ''
}

/**
 * Email владельца.
 */
const getOwnerEmail = (form) => {
    return form?.owner?.email || ''
}

/**
 * Нормализация строки.
 */
const normalize = (value) => {
    return (value ?? '')
        .toString()
        .trim()
        .toLowerCase()
}

/**
 * Безопасное преобразование в число.
 */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/**
 * Безопасное преобразование даты.
 */
const safeDate = (value) => {
    const time = new Date(
        value || 0
    ).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/* ===================== View mode ===================== */

/**
 * Режим отображения списка.
 */
const viewMode = ref(
    localStorage.getItem(
        'admin_view_mode_forms'
    )
    || 'cards'
)

/**
 * Сохранение режима отображения.
 */
watch(
    viewMode,
    (value) => {
        localStorage.setItem(
            'admin_view_mode_forms',
            value
        )
    }
)

/* ===================== Per page ===================== */

/**
 * Количество элементов на странице.
 */
const itemsPerPage = ref(
    props.adminFormsPerPage || 12
)

/**
 * Обновление количества элементов.
 *
 * Для frontend режима настройка сохраняется
 * непосредственно со страницы.
 *
 * Для server режима этим занимается
 * ServerItemsPerPageSelect.
 */
watch(
    itemsPerPage,
    (newVal) => {
        if (props.useServerProcessing) {
            return
        }

        router.put(
            route(
                'admin.settings.updateAdminCountForms'
            ),
            {
                value: newVal,
            },
            {
                preserveScroll: true,
                preserveState: true,

                onSuccess: () => {
                    toast.info(
                        `Показ ${newVal} форм на странице.`
                    )
                },

                onError: (errors) => {
                    toast.error(
                        errors?.value
                        || 'Ошибка обновления количества форм.'
                    )
                },
            }
        )
    }
)

/* ===================== Sorting ===================== */

/**
 * Текущая сортировка.
 */
const currentSort = ref(
    props.sortParam
    || props.adminFormsDefaultSort
    || 'sortAsc'
)

/**
 * Обновление сортировки.
 */
watch(
    currentSort,
    (newVal) => {
        router.put(
            route(
                'admin.settings.updateAdminSortForms'
            ),
            {
                value: newVal,
            },
            {
                preserveScroll: true,
                preserveState: true,

                onSuccess: () => {
                    /*
                     * В server режиме после сохранения
                     * настройки запрашиваем новый список.
                     */
                    if (
                        props.useServerProcessing
                    ) {
                        router.get(
                            window.location.pathname,
                            {
                                ...Object.fromEntries(
                                    new URLSearchParams(
                                        window.location.search
                                    )
                                ),

                                sort:
                                    newVal || undefined,

                                page:
                                undefined,
                            },
                            {
                                preserveScroll:
                                    true,

                                preserveState:
                                    false,

                                replace:
                                    true,
                            }
                        )
                    }

                    toast.info(
                        'Сортировка форм успешно изменена.'
                    )
                },

                onError: (errors) => {
                    toast.error(
                        errors?.value
                        || 'Ошибка обновления сортировки форм.'
                    )
                },
            }
        )
    }
)

/* ===================== Data ===================== */

/**
 * Локальный список форм.
 */
const localForms = ref([])

/**
 * Нормализация списка форм.
 *
 * Поддерживает:
 * - обычную ResourceCollection;
 * - paginator;
 * - прямой массив.
 */
const formsList = computed(() => {
    if (
        Array.isArray(
            props.forms
        )
    ) {
        return props.forms
    }

    if (
        Array.isArray(
            props.forms?.data
        )
    ) {
        return props.forms.data
    }

    if (
        Array.isArray(
            props.forms?.data?.data
        )
    ) {
        return props.forms.data.data
    }

    if (
        Array.isArray(
            props.forms?.resource
        )
    ) {
        return props.forms.resource
    }

    return []
})

/**
 * Синхронизация локального списка.
 */
watch(
    formsList,
    (newVal) => {
        localForms.value = JSON.parse(
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

const showConfirmDeleteModal = ref(false)

const formToDeleteId = ref(null)
const formToDeleteTitle = ref('')

/**
 * Подготовка удаления формы.
 */
const confirmDelete = (
    formOrId,
    title = null
) => {
    if (
        formOrId
        && typeof formOrId === 'object'
    ) {
        formToDeleteId.value =
            formOrId.id

        formToDeleteTitle.value =
            title
            || getFormTitle(
                formOrId
            )
    } else {
        formToDeleteId.value =
            formOrId

        formToDeleteTitle.value =
            title
            || `ID: ${formOrId}`
    }

    if (
        formToDeleteId.value === null
        || formToDeleteId.value === undefined
    ) {
        return
    }

    showConfirmDeleteModal.value =
        true
}

/**
 * Закрытие модального окна.
 */
const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    formToDeleteId.value =
        null

    formToDeleteTitle.value =
        ''
}

/**
 * Удаление формы.
 */
const deleteForm = () => {
    if (
        formToDeleteId.value === null
    ) {
        return
    }

    const idToDelete =
        formToDeleteId.value

    const titleToDelete =
        formToDeleteTitle.value

    router.delete(
        route(
            'admin.forms.destroy',
            {
                form: idToDelete,
            }
        ),
        {
            preserveScroll:
                true,

            preserveState:
                false,

            onSuccess: (page) => {
                /*
                 * FormController может вернуть
                 * redirect back() с flash error,
                 * если у формы существуют заявки.
                 */
                const flashError =
                    page?.props?.flash?.error

                if (flashError) {
                    toast.error(
                        flashError
                    )

                    return
                }

                toast.success(
                    `Форма "${titleToDelete || 'ID: ' + idToDelete}" удалена.`
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const message =
                    errors?.general
                    || errors?.[errorKey]
                    || 'Произошла ошибка при удалении формы.'

                toast.error(
                    message
                )
            },

            onFinish: () => {
                closeModal()
            },
        }
    )
}

/* ===================== Local patch ===================== */

/**
 * Локальное обновление формы.
 */
const patchLocalForm = (
    formId,
    callback
) => {
    const index =
        localForms.value.findIndex(
            (form) =>
                form.id === formId
        )

    if (index === -1) {
        return
    }

    callback(
        localForms.value[index]
    )
}

/* ===================== Activity ===================== */

/**
 * Переключение активности формы.
 */
const toggleActivity = (form) => {
    if (!form?.id) {
        return
    }

    const newActivity =
        !form.activity

    const title =
        getFormTitle(form)

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.forms.updateActivity',
            {
                form: form.id,
            }
        ),
        {
            activity:
            newActivity,
        },
        {
            preserveScroll:
                true,

            preserveState:
                true,

            onSuccess: () => {
                patchLocalForm(
                    form.id,
                    (node) => {
                        node.activity =
                            newActivity
                    }
                )

                toast.success(
                    `Форма "${title}" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности формы "${title}".`
                )
            },
        }
    )
}

/* ===================== Search / pagination ===================== */

/**
 * Строка поиска.
 */
const searchQuery = ref(
    props.search || ''
)

/**
 * Текущая frontend-страница.
 */
const currentPage = ref(1)

/**
 * ID по убыванию.
 */
const byIdDesc = (
    a,
    b
) => {
    return safeNumber(
            b?.id
        )
        - safeNumber(
            a?.id
        )
}

/**
 * Числовая сортировка ASC.
 */
const byNumberAsc = (
    field
) => (
    a,
    b
) => {
    return safeNumber(
            a?.[field]
        )
        - safeNumber(
            b?.[field]
        )
        || byIdDesc(
            a,
            b
        )
}

/**
 * Числовая сортировка DESC.
 */
const byNumberDesc = (
    field
) => (
    a,
    b
) => {
    return safeNumber(
            b?.[field]
        )
        - safeNumber(
            a?.[field]
        )
        || byIdDesc(
            a,
            b
        )
}

/**
 * Дата ASC.
 */
const byDateAsc = (
    field
) => (
    a,
    b
) => {
    return safeDate(
            a?.[field]
        )
        - safeDate(
            b?.[field]
        )
        || byIdDesc(
            a,
            b
        )
}

/**
 * Дата DESC.
 */
const byDateDesc = (
    field
) => (
    a,
    b
) => {
    return safeDate(
            b?.[field]
        )
        - safeDate(
            a?.[field]
        )
        || byIdDesc(
            a,
            b
        )
}

/**
 * Строковая сортировка ASC.
 */
const byStringAsc = (
    valueResolver
) => (
    a,
    b
) => {
    return normalize(
            valueResolver(a)
        )
            .localeCompare(
                normalize(
                    valueResolver(b)
                ),
                locale.value
            )
        || byIdDesc(
            a,
            b
        )
}

/**
 * Строковая сортировка DESC.
 */
const byStringDesc = (
    valueResolver
) => (
    a,
    b
) => {
    return normalize(
            valueResolver(b)
        )
            .localeCompare(
                normalize(
                    valueResolver(a)
                ),
                locale.value
            )
        || byIdDesc(
            a,
            b
        )
}

/**
 * Фильтр с порядком ID DESC.
 *
 * Используется специальными параметрами
 * сортировки, которые фактически являются
 * быстрыми фильтрами.
 */
const filterWithIdDesc = (
    list,
    callback
) => {
    return list
        .filter(
            callback
        )
        .sort(
            byIdDesc
        )
}

/**
 * Сортировка / специальные фильтры форм.
 *
 * Семантика должна совпадать
 * с Form::scopeSortByParam().
 */
const sortForms = (forms) => {
    const list =
        Array.isArray(forms)
            ? forms.slice()
            : []

    /*
    |--------------------------------------------------------------------------
    | Активность
    |--------------------------------------------------------------------------
    */

    if (
        currentSort.value === 'activity'
    ) {
        return filterWithIdDesc(
            list,
            (form) =>
                !!form.activity
        )
    }

    if (
        currentSort.value === 'inactive'
    ) {
        return filterWithIdDesc(
            list,
            (form) =>
                !form.activity
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Статусы
    |--------------------------------------------------------------------------
    */

    if (
        currentSort.value === 'statusDraft'
    ) {
        return filterWithIdDesc(
            list,
            (form) =>
                form?.status === 'draft'
        )
    }

    if (
        currentSort.value === 'statusPublished'
    ) {
        return filterWithIdDesc(
            list,
            (form) =>
                form?.status === 'published'
        )
    }

    if (
        currentSort.value === 'statusArchived'
    ) {
        return filterWithIdDesc(
            list,
            (form) =>
                form?.status === 'archived'
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Обычные сортировки
    |--------------------------------------------------------------------------
    */

    const sortMap = {
        idAsc: (
            a,
            b
        ) => {
            return safeNumber(
                    a?.id
                )
                - safeNumber(
                    b?.id
                )
        },

        idDesc:
        byIdDesc,

        sortAsc:
            byNumberAsc(
                'sort'
            ),

        sortDesc:
            byNumberDesc(
                'sort'
            ),

        titleAsc:
            byStringAsc(
                getFormTitleValue
            ),

        titleDesc:
            byStringDesc(
                getFormTitleValue
            ),

        codeAsc:
            byStringAsc(
                (form) =>
                    form?.code
            ),

        codeDesc:
            byStringDesc(
                (form) =>
                    form?.code
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
                (form) =>
                    form?.status
            ),

        statusDesc:
            byStringDesc(
                (form) =>
                    form?.status
            ),

        fieldsCountAsc:
            byNumberAsc(
                'fields_count'
            ),

        fieldsCountDesc:
            byNumberDesc(
                'fields_count'
            ),

        submissionsCountAsc:
            byNumberAsc(
                'submissions_count'
            ),

        submissionsCountDesc:
            byNumberDesc(
                'submissions_count'
            ),

        ownerNameAsc:
            byStringAsc(
                getOwnerName
            ),

        ownerNameDesc:
            byStringDesc(
                getOwnerName
            ),

        ownerEmailAsc:
            byStringAsc(
                getOwnerEmail
            ),

        ownerEmailDesc:
            byStringDesc(
                getOwnerEmail
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

    const sorter =
        sortMap[
            currentSort.value
            ]

    return sorter
        ? list.sort(
            sorter
        )
        : list
}

/**
 * Фильтрация форм.
 *
 * Семантика соответствует
 * Form::scopeSearch():
 *
 * - code;
 * - status;
 * - title;
 * - subtitle;
 * - description;
 * - owner name;
 * - owner email.
 */
const filteredForms = computed(() => {
    let filtered =
        localForms.value || []

    const query =
        normalize(
            searchQuery.value
        )

    if (!query) {
        return sortForms(
            filtered
        )
    }

    filtered =
        filtered.filter(
            (form) => {
                const values = [
                    form?.code,
                    form?.status,

                    getFormTitleValue(
                        form
                    ),

                    getFormSubtitle(
                        form
                    ),

                    getFormDescription(
                        form
                    ),

                    getOwnerName(
                        form
                    ),

                    getOwnerEmail(
                        form
                    ),
                ]

                return values.some(
                    (value) =>
                        normalize(
                            value
                        ).includes(
                            query
                        )
                )
            }
        )

    return sortForms(
        filtered
    )
})

/**
 * Frontend-пагинация.
 */
const paginatedForms = computed(() => {
    const perPage =
        Number(
            itemsPerPage.value || 12
        )

    const start =
        (
            currentPage.value - 1
        )
        * perPage

    return filteredForms.value.slice(
        start,
        start + perPage
    )
})

/**
 * Итоговый список для отображения.
 */
const displayedForms = computed(() => {
    return props.useServerProcessing
        ? formsList.value
        : paginatedForms.value
})

/**
 * При изменении frontend-параметров
 * возвращаемся на первую страницу.
 */
watch(
    [
        itemsPerPage,
        searchQuery,
        currentSort,
    ],
    () => {
        currentPage.value = 1
    }
)

/* ===================== Selection ===================== */

/**
 * Выбранные формы.
 */
const selectedForms = ref([])

/**
 * Массовое выделение.
 */
const toggleAll = (payload) => {
    const checked =
        Boolean(
            payload?.checked
            ?? payload?.target?.checked
            ?? false
        )

    const ids =
        payload?.ids
        ?? displayedForms.value.map(
            (form) =>
                form.id
        )

    if (checked) {
        selectedForms.value = [
            ...new Set([
                ...selectedForms.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedForms.value =
        selectedForms.value.filter(
            (id) =>
                !ids.includes(
                    id
                )
        )
}

/**
 * Переключение выбора формы.
 */
const toggleSelectForm = (
    formId
) => {
    const index =
        selectedForms.value.indexOf(
            formId
        )

    if (index > -1) {
        selectedForms.value.splice(
            index,
            1
        )

        return
    }

    selectedForms.value.push(
        formId
    )
}

/* ===================== Bulk actions ===================== */

/**
 * Массовое обновление активности.
 */
const bulkToggleActivity = (
    newActivity
) => {
    if (
        !selectedForms.value.length
    ) {
        toast.warning(
            'Выберите формы для активации/деактивации.'
        )

        return
    }

    const idsToUpdate = [
        ...selectedForms.value,
    ]

    router.put(
        route(
            'admin.actions.forms.bulkUpdateActivity'
        ),
        {
            ids:
            idsToUpdate,

            activity:
            newActivity,
        },
        {
            preserveScroll:
                true,

            preserveState:
                true,

            onSuccess: () => {
                localForms.value =
                    localForms.value.map(
                        (form) => {
                            return idsToUpdate.includes(
                                form.id
                            )
                                ? {
                                    ...form,
                                    activity:
                                    newActivity,
                                }
                                : form
                        }
                    )

                selectedForms.value =
                    []

                toast.success(
                    'Активность форм массово обновлена.'
                )
            },

            onError: (errors) => {
                const message =
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности форм.'

                toast.error(
                    message
                )
            },
        }
    )
}

/**
 * Массовое удаление форм.
 */
const bulkDelete = () => {
    if (
        !selectedForms.value.length
    ) {
        toast.warning(
            'Выберите хотя бы одну форму для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные формы? Формы с существующими заявками удалить нельзя.'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.forms.bulkDestroy'
        ),
        {
            data: {
                ids:
                selectedForms.value,
            },

            preserveScroll:
                true,

            preserveState:
                false,

            onSuccess: (page) => {
                const flashError =
                    page?.props?.flash?.error

                if (flashError) {
                    toast.error(
                        flashError
                    )

                    return
                }

                selectedForms.value =
                    []

                toast.success(
                    'Массовое удаление форм успешно завершено.'
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors?.general
                    || errors?.[errorKey]
                    || 'Произошла ошибка при удалении форм.'
                )
            },
        }
    )
}

/**
 * Обработка массовых действий.
 */
const handleBulkAction = (
    event
) => {
    const action =
        event.target.value

    if (
        action === 'selectAll'
    ) {
        toggleAll({
            target: {
                checked: true,
            },
        })
    } else if (
        action === 'deselectAll'
    ) {
        toggleAll({
            target: {
                checked: false,
            },
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

/* ===================== Sort order ===================== */

/**
 * Массовое обновление порядка sort.
 */
const handleSortOrderUpdate = (
    newOrderIds
) => {
    const items =
        newOrderIds.map(
            (
                id,
                index
            ) => ({
                id,
                sort:
                index,
            })
        )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.forms.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll:
                true,

            preserveState:
                true,

            onSuccess: () => {
                toast.success(
                    'Сортировка форм обновлена.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка сортировки форм:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.general
                    || 'Ошибка обновления сортировки форм.'
                )
            },
        }
    )
}

/* ===================== Navigation ===================== */

/**
 * Просмотр формы.
 */
const showForm = (form) => {
    if (!form?.id) {
        return
    }

    router.visit(
        route(
            'admin.forms.show',
            {
                form:
                form.id,
            }
        )
    )
}

/**
 * Редактирование формы.
 */
const editForm = (form) => {
    if (!form?.id) {
        return
    }

    router.visit(
        route(
            'admin.forms.edit',
            {
                form:
                form.id,
            }
        )
    )
}

/**
 * Поля формы.
 */
const openFields = (form) => {
    if (!form?.id) {
        return
    }

    router.visit(
        route(
            'admin.formFields.index',
            {
                form_id:
                form.id,
            }
        )
    )
}

/**
 * Заявки формы.
 */
const openSubmissions = (form) => {
    if (!form?.id) {
        return
    }

    router.visit(
        route(
            'admin.formSubmissions.index',
            {
                form_id:
                form.id,
            }
        )
    )
}
</script>

<template>
    <AdminLayout :title="t('dynamicContactForms')">
        <template #header>
            <TitlePage>
                {{ t('dynamicContactForms') }}
            </TitlePage>
        </template>

        <div
            class="px-2 py-2 w-full max-w-12xl mx-auto"
        >
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- ===================== Header ===================== -->

                <div
                    class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"
                >
                    <DefaultButton
                        :href="route('admin.forms.create')"
                    >
                        {{ t('addForm') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminFormsProcessingMode"
                        :mode="adminFormsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="formsCount"
                    />
                </div>

                <!-- ===================== Search ===================== -->

                <SearchInput
                    v-if="formsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="formsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- ===================== Per page / sort ===================== -->

                <div
                    v-if="formsCount"
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
                        update-route="admin.settings.updateAdminCountForms"
                    />

                    <SortSelect
                        v-model:sortParam="currentSort"
                    />
                </div>

                <!-- ===================== Count / bulk / view ===================== -->

                <div
                    v-if="formsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ formsCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- ===================== Top pagination ===================== -->

                <div
                    v-if="formsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredForms.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="forms"
                    />
                </div>

                <!-- ===================== Empty ===================== -->

                <div
                    v-if="!formsCount"
                    class="py-12 text-center text-gray-500 dark:text-gray-300"
                >
                    Формы не найдены.
                </div>

                <!-- ===================== Table ===================== -->

                <FormTable
                    v-else-if="viewMode === 'table'"
                    :forms="displayedForms"
                    :statuses="statuses"
                    :selected-forms="selectedForms"
                    @show="showForm"
                    @edit="editForm"
                    @delete="confirmDelete"
                    @fields="openFields"
                    @submissions="openSubmissions"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectForm"
                    @toggle-all="toggleAll"
                />

                <!-- ===================== Cards ===================== -->

                <FormCardGrid
                    v-else
                    :forms="displayedForms"
                    :statuses="statuses"
                    :selected-forms="selectedForms"
                    @show="showForm"
                    @edit="editForm"
                    @delete="confirmDelete"
                    @fields="openFields"
                    @submissions="openSubmissions"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectForm"
                    @toggle-all="toggleAll"
                />

                <!-- ===================== Bottom pagination ===================== -->

                <div
                    v-if="formsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredForms.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="forms"
                    />
                </div>
            </div>
        </div>

        <!-- ===================== Delete modal ===================== -->

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteForm"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
