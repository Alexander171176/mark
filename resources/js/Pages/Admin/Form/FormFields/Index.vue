<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список полей динамических форм.
 *
 * Возможности:
 * - режимы обработки frontend | server | auto;
 * - локальный / серверный поиск;
 * - локальная / серверная сортировка;
 * - локальная / серверная пагинация;
 * - изменение количества элементов;
 * - изменение активности;
 * - массовые действия;
 * - Drag & Drop сортировка;
 * - табличное / карточное отображение;
 * - фильтрация по родительской форме;
 * - просмотр, редактирование и удаление.
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
import SortSelect from '@/Components/Admin/Form/FormField/Sort/SortSelect.vue'
import BulkActionSelect from '@/Components/Admin/Form/FormField/Select/BulkActionSelect.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'
import FormFieldTable from '@/Components/Admin/Form/FormField/Table/FormFieldTable.vue'
import FormFieldCardGrid from '@/Components/Admin/Form/FormField/View/FormFieldCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    adminFormFieldsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },
    adminFormFieldsPerPage: { type: Number, default: 12 },
    adminFormFieldsDefaultSort: { type: String, default: 'sortAsc' },
    fields: { type: [Array, Object], default: () => [] },
    fieldsCount: { type: Number, default: 0 },
    form: { type: Object, default: null },
    formId: { type: [Number, String], default: null },
    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
    fieldTypes: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
})

/* ===================== Helpers ===================== */

const getFieldTranslation = (field) => field?.translation || {}
const getFieldLabelValue = (field) => getFieldTranslation(field)?.label || ''
const getFieldLabel = (field) => getFieldLabelValue(field) || field?.name || `ID: ${field?.id}`
const getFieldPlaceholder = (field) => getFieldTranslation(field)?.placeholder || ''
const getFieldDescription = (field) => getFieldTranslation(field)?.description || ''

const getFormTranslation = (form) => form?.translation || {}
const getFormTitle = (form) => getFormTranslation(form)?.title || form?.code || `ID: ${form?.id}`
const getFieldFormTitle = (field) => getFormTitle(field?.form)

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/* ===================== Form context ===================== */

const hasFormContext = computed(() => Boolean(props.formId))
const formContextParams = computed(() => props.formId ? { form_id: props.formId } : {})
const createFieldUrl = computed(() => route('admin.formFields.create', formContextParams.value))

/* ===================== View mode ===================== */

const viewMode = ref(localStorage.getItem('admin_view_mode_form_fields') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_form_fields', value)
})

/* ===================== Per page ===================== */

const itemsPerPage = ref(props.adminFormFieldsPerPage || 12)

watch(itemsPerPage, (value) => {
    if (props.useServerProcessing) return

    router.put(route('admin.settings.updateAdminCountFormFields'), { value }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${value} полей формы на странице.`),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления количества полей формы.'),
    })
})

/* ===================== Sorting setting ===================== */

const currentSort = ref(props.sortParam || props.adminFormFieldsDefaultSort || 'sortAsc')

watch(currentSort, (value) => {
    router.put(route('admin.settings.updateAdminSortFormFields'), { value }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            if (props.useServerProcessing) {
                const query = Object.fromEntries(new URLSearchParams(window.location.search))
                delete query.page

                router.get(window.location.pathname, {
                    ...query,
                    sort: value || undefined,
                }, {
                    preserveScroll: true,
                    preserveState: false,
                    replace: true,
                })
            }

            toast.info('Сортировка полей формы успешно изменена.')
        },

        onError: (errors) => {
            toast.error(errors?.value || 'Ошибка обновления сортировки полей формы.')
        },
    })
})

/* ===================== Data ===================== */

const localFields = ref([])

const fieldsList = computed(() => {
    if (Array.isArray(props.fields)) return props.fields
    if (Array.isArray(props.fields?.data)) return props.fields.data
    if (Array.isArray(props.fields?.data?.data)) return props.fields.data.data
    if (Array.isArray(props.fields?.resource)) return props.fields.resource
    return []
})

watch(fieldsList, (value) => {
    localFields.value = JSON.parse(JSON.stringify(value || []))
}, {
    immediate: true,
    deep: true,
})

/* ===================== Delete ===================== */

const showConfirmDeleteModal = ref(false)
const fieldToDeleteId = ref(null)
const fieldToDeleteLabel = ref('')

const confirmDelete = (fieldOrId, label = null) => {
    if (fieldOrId && typeof fieldOrId === 'object') {
        fieldToDeleteId.value = fieldOrId.id
        fieldToDeleteLabel.value = label || getFieldLabel(fieldOrId)
    } else {
        fieldToDeleteId.value = fieldOrId
        fieldToDeleteLabel.value = label || `ID: ${fieldOrId}`
    }

    if (fieldToDeleteId.value === null || fieldToDeleteId.value === undefined) return
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    fieldToDeleteId.value = null
    fieldToDeleteLabel.value = ''
}

const deleteField = () => {
    if (fieldToDeleteId.value === null) return

    const id = fieldToDeleteId.value
    const label = fieldToDeleteLabel.value

    router.delete(route('admin.formFields.destroy', { formField: id }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: (page) => {
            const flashError = page?.props?.flash?.error

            if (flashError) {
                toast.error(flashError)
                return
            }

            toast.success(`Поле "${label || 'ID: ' + id}" удалено.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(
                errors?.general
                || errors?.[errorKey]
                || 'Произошла ошибка при удалении поля формы.'
            )
        },

        onFinish: closeModal,
    })
}

/* ===================== Local patch ===================== */

const patchLocalField = (fieldId, callback) => {
    const field = localFields.value.find((item) => item.id === fieldId)
    if (field) callback(field)
}

/* ===================== Activity ===================== */

const toggleActivity = (field) => {
    if (!field?.id) return

    const activity = !field.activity
    const label = getFieldLabel(field)
    const actionText = activity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.formFields.updateActivity', { formField: field.id }),
        { activity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalField(field.id, (item) => {
                    item.activity = activity
                })

                toast.success(`Поле "${label}" ${actionText}.`)
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности поля "${label}".`
                )
            },
        }
    )
}

/* ===================== Search / pagination ===================== */

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/* ID */
const byIdAsc = (a, b) => safeNumber(a?.id) - safeNumber(b?.id)
const byIdDesc = (a, b) => safeNumber(b?.id) - safeNumber(a?.id)

/* FormField::ordered(): sort ASC, id DESC */
const byOrdered = (a, b) => safeNumber(a?.sort) - safeNumber(b?.sort) || byIdDesc(a, b)
const bySortAsc = (a, b) => safeNumber(a?.sort) - safeNumber(b?.sort) || byIdDesc(a, b)
const bySortDesc = (a, b) => safeNumber(b?.sort) - safeNumber(a?.sort) || byIdDesc(a, b)

/* Числа */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field]) || byIdAsc(a, b)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field]) || byIdDesc(a, b)

/* Даты */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field]) || byIdAsc(a, b)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field]) || byIdDesc(a, b)

/* Строки */
const byStringAsc = (resolver) => (a, b) =>
    normalize(resolver(a)).localeCompare(normalize(resolver(b)), locale.value) || byIdAsc(a, b)

const byStringDesc = (resolver) => (a, b) =>
    normalize(resolver(b)).localeCompare(normalize(resolver(a)), locale.value) || byIdDesc(a, b)

/* Быстрые фильтры используют FormField::ordered() */
const filterWithOrdered = (list, callback) => list.filter(callback).sort(byOrdered)

const sortFields = (fields) => {
    const list = Array.isArray(fields) ? fields.slice() : []

    const filters = {
        activity: (field) => !!field.activity,
        inactive: (field) => !field.activity,
        required: (field) => !!field.required,
        optional: (field) => !field.required,
        readonly: (field) => !!field.readonly,
        editable: (field) => !field.readonly,
        disabled: (field) => !!field.disabled,
        enabled: (field) => !field.disabled,
    }

    if (filters[currentSort.value]) {
        return filterWithOrdered(list, filters[currentSort.value])
    }

    const sortMap = {
        idAsc: byIdAsc,
        idDesc: byIdDesc,

        sortAsc: bySortAsc,
        sortDesc: bySortDesc,

        labelAsc: byStringAsc(getFieldLabelValue),
        labelDesc: byStringDesc(getFieldLabelValue),

        nameAsc: byStringAsc((field) => field?.name),
        nameDesc: byStringDesc((field) => field?.name),

        typeAsc: byStringAsc((field) => field?.type),
        typeDesc: byStringDesc((field) => field?.type),

        widthAsc: byStringAsc((field) => field?.width),
        widthDesc: byStringDesc((field) => field?.width),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        requiredAsc: byNumberAsc('required'),
        requiredDesc: byNumberDesc('required'),

        readonlyAsc: byNumberAsc('readonly'),
        readonlyDesc: byNumberDesc('readonly'),

        disabledAsc: byNumberAsc('disabled'),
        disabledDesc: byNumberDesc('disabled'),

        optionsAsc: byNumberAsc('options_count'),
        optionsDesc: byNumberDesc('options_count'),

        formIdAsc: byNumberAsc('form_id'),
        formIdDesc: byNumberDesc('form_id'),

        formTitleAsc: byStringAsc(getFieldFormTitle),
        formTitleDesc: byStringDesc(getFieldFormTitle),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        dateAsc: byDateAsc('created_at'),
        dateDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return list.sort(sortMap[currentSort.value] || byOrdered)
}

const filteredFields = computed(() => {
    const query = normalize(searchQuery.value)
    const fields = localFields.value || []

    if (!query) return sortFields(fields)

    return sortFields(
        fields.filter((field) => [
            field?.name,
            field?.type,
            getFieldLabelValue(field),
            getFieldPlaceholder(field),
            getFieldDescription(field),
            getFieldFormTitle(field),
            field?.form?.code,
        ].some((value) => normalize(value).includes(query)))
    )
})

const paginatedFields = computed(() => {
    const perPage = Number(itemsPerPage.value || 12)
    const start = (currentPage.value - 1) * perPage

    return filteredFields.value.slice(start, start + perPage)
})

const displayedFields = computed(() => {
    return props.useServerProcessing
        ? fieldsList.value
        : paginatedFields.value
})

watch([itemsPerPage, searchQuery, currentSort], () => {
    currentPage.value = 1
})

/* ===================== Selection ===================== */

const selectedFields = ref([])

const toggleAll = (payload) => {
    const checked = Boolean(payload?.checked ?? payload?.target?.checked ?? false)
    const ids = payload?.ids ?? displayedFields.value.map((field) => field.id)

    if (checked) {
        selectedFields.value = [...new Set([...selectedFields.value, ...ids])]
        return
    }

    selectedFields.value = selectedFields.value.filter((id) => !ids.includes(id))
}

const toggleSelectField = (fieldId) => {
    const index = selectedFields.value.indexOf(fieldId)

    if (index > -1) {
        selectedFields.value.splice(index, 1)
        return
    }

    selectedFields.value.push(fieldId)
}

/* ===================== Bulk activity ===================== */

const bulkToggleActivity = (activity) => {
    if (!selectedFields.value.length) {
        toast.warning('Выберите поля для активации/деактивации.')
        return
    }

    const ids = [...selectedFields.value]

    router.put(
        route('admin.actions.formFields.bulkUpdateActivity'),
        { ids, activity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localFields.value = localFields.value.map((field) => {
                    return ids.includes(field.id)
                        ? { ...field, activity }
                        : field
                })

                selectedFields.value = []
                toast.success('Активность полей формы массово обновлена.')
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности полей формы.'
                )
            },
        }
    )
}

/* ===================== Bulk delete ===================== */

const bulkDelete = () => {
    if (!selectedFields.value.length) {
        toast.warning('Выберите хотя бы одно поле для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные поля формы?')) return

    router.delete(route('admin.actions.formFields.bulkDestroy'), {
        data: { ids: selectedFields.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: (page) => {
            const flashError = page?.props?.flash?.error

            if (flashError) {
                toast.error(flashError)
                return
            }

            selectedFields.value = []
            toast.success('Массовое удаление полей формы успешно завершено.')
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]

            toast.error(
                errors?.general
                || errors?.[errorKey]
                || 'Произошла ошибка при удалении полей формы.'
            )
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

/* ===================== Drag & Drop ===================== */

const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) return

    router.put(route('admin.actions.formFields.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            items.forEach((item) => {
                patchLocalField(item.id, (field) => {
                    field.sort = item.sort
                })
            })

            toast.success('Сортировка полей формы обновлена.')
        },

        onError: (errors) => {
            toast.error(
                errors?.message
                || errors?.general
                || 'Ошибка обновления сортировки полей формы.'
            )
        },
    })
}

/* ===================== Navigation ===================== */

const showField = (field) => {
    if (!field?.id) return
    router.visit(route('admin.formFields.show', { formField: field.id }))
}

const editField = (field) => {
    if (!field?.id) return
    router.visit(route('admin.formFields.edit', { formField: field.id }))
}
</script>

<template>
    <AdminLayout :title="t('formFields')">
        <template #header>
            <TitlePage>{{ t('formFields') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400
                        dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500
                        dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">

                <!-- Управление -->
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="createFieldUrl">
                        {{ t('addFormField') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminFormFieldsProcessingMode"
                        :mode="adminFormFieldsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="fieldsCount"
                    />
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="fieldsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="fieldsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Количество / сортировка -->
                <div
                    v-if="fieldsCount"
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
                        update-route="admin.settings.updateAdminCountFormFields"
                    />

                    <SortSelect v-model:sortParam="currentSort" />
                </div>

                <!-- Счётчик / массовые действия / вид -->
                <div
                    v-if="fieldsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ fieldsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Пагинация сверху -->
                <div
                    v-if="fieldsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredFields.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="fields"
                    />
                </div>

                <!-- Пустой список -->
                <div
                    v-if="!fieldsCount"
                    class="py-12 text-center text-gray-500 dark:text-gray-300"
                >
                    <template v-if="form">
                        В форме «{{ currentFormTitle }}» поля не найдены.
                    </template>

                    <template v-else>
                        Поля форм не найдены.
                    </template>
                </div>

                <!-- Таблица -->
                <FormFieldTable
                    v-else-if="viewMode === 'table'"
                    :fields="displayedFields"
                    :field-types="fieldTypes"
                    :selected-fields="selectedFields"
                    :show-form="!hasFormContext"
                    @show="showField"
                    @edit="editField"
                    @delete="confirmDelete"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectField"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <FormFieldCardGrid
                    v-else
                    :fields="displayedFields"
                    :field-types="fieldTypes"
                    :selected-fields="selectedFields"
                    :show-form="!hasFormContext"
                    @show="showField"
                    @edit="editField"
                    @delete="confirmDelete"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectField"
                    @toggle-all="toggleAll"
                />

                <!-- Пагинация снизу -->
                <div
                    v-if="fieldsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredFields.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="fields"
                    />
                </div>
            </div>
        </div>

        <!-- Подтверждение удаления -->
        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteField"
            @close="closeModal"
        />
    </AdminLayout>
</template>
