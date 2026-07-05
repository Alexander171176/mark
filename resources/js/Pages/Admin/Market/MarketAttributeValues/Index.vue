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

/** Получение текущего перевода */
const getValueTranslation = (value) => value?.translation || value?.translations?.[0] || {}

/** Получение названия значения */
const getValueTitle = (value) => getValueTranslation(value)?.title || `ID: ${value?.id}`

/** Получение краткого описания */
const getValueShort = (value) => getValueTranslation(value)?.short || ''

/** Получение названия характеристики */
const getAttributeTitle = (value) => {
    return value?.attribute?.title
        || value?.attribute?.translation?.title
        || value?.attribute?.code
        || ''
}

/** Нормализация строки */
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

/** Безопасное получение статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения списка */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_attribute_values') || 'cards'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_attribute_values', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketAttributeValuesPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketAttributeValues'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} значений характеристик на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва значений.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam || props.adminMarketAttributeValuesDefaultSort || 'idDesc'
)

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketAttributeValues'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
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

                toast.info('Сортировка значений характеристик успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки значений.')
            },
        }
    )
})

/** Локальная копия списка */
const localValues = ref([])

/** Исходный список */
const valuesList = computed(() => {
    if (Array.isArray(props.values)) return props.values
    if (Array.isArray(props.values?.data)) return props.values.data
    if (Array.isArray(props.values?.data?.data)) return props.values.data.data
    if (Array.isArray(props.values?.resource)) return props.values.resource

    return []
})

watch(
    valuesList,
    (newVal) => {
        localValues.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Окно подтверждения удаления */
const showConfirmDeleteModal = ref(false)

/** ID удаляемого значения */
const valueToDeleteId = ref(null)

/** Название удаляемого значения */
const valueToDeleteTitle = ref('')

/** Подготовка удаления */
const confirmDelete = (valueOrId, title = null) => {
    if (typeof valueOrId === 'object') {
        valueToDeleteId.value = valueOrId.id
        valueToDeleteTitle.value = title || getValueTitle(valueOrId)
    } else {
        valueToDeleteId.value = valueOrId
        valueToDeleteTitle.value = title || `ID: ${valueOrId}`
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
    if (valueToDeleteId.value === null) return

    const idToDelete = valueToDeleteId.value
    const titleToDelete = valueToDeleteTitle.value

    router.delete(route('admin.marketAttributeValues.destroy', { marketAttributeValue: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Значение характеристики "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Значение: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}

/** Локальное обновление записи */
const patchLocalValue = (valueId, callback) => {
    const index = localValues.value.findIndex((value) => value.id === valueId)

    if (index !== -1) {
        callback(localValues.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (value) => {
    const newActivity = !value.activity
    const title = getValueTitle(value)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketAttributeValues.updateActivity', { marketAttributeValue: value.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalValue(value.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Значение характеристики "${title}" ${actionText}.`)
            },

            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Поисковая строка */
const searchQuery = ref(props.search || '')

/** Текущая страница пагинации */
const currentPage = ref(1)

/** Сортировка чисел по возрастанию */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка чисел по убыванию */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка дат по возрастанию */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка дат по убыванию */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка списка */
const sortValues = (values) => {
    const list = (values || []).slice()

    if (sortParam.value === 'activity') return list.filter((value) => !!value.activity)
    if (sortParam.value === 'inactive') return list.filter((value) => !value.activity)

    if (sortParam.value === 'statusDraft') return list.filter((value) => value?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter((value) => value?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter((value) => value?.status === 'archived')

    if (sortParam.value === 'moderationPending') return list.filter((value) => moderationNum(value?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((value) => moderationNum(value?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((value) => moderationNum(value?.moderation_status) === 2)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getValueTitle(a)).localeCompare(normalize(getValueTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getValueTitle(b)).localeCompare(normalize(getValueTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        attributeTitleAsc: (a, b) =>
            normalize(getAttributeTitle(a)).localeCompare(normalize(getAttributeTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        attributeTitleDesc: (a, b) =>
            normalize(getAttributeTitle(b)).localeCompare(normalize(getAttributeTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        codeAsc: (a, b) =>
            normalize(a?.code).localeCompare(normalize(b?.code), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        codeDesc: (a, b) =>
            normalize(b?.code).localeCompare(normalize(a?.code), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        colorAsc: (a, b) =>
            normalize(a?.color).localeCompare(normalize(b?.color), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        colorDesc: (a, b) =>
            normalize(b?.color).localeCompare(normalize(a?.color), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: (a, b) =>
            normalize(a?.status).localeCompare(normalize(b?.status), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        statusDesc: (a, b) =>
            normalize(b?.status).localeCompare(normalize(a?.status), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        moderationStatusAsc: (a, b) =>
            moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status)
            || safeNumber(a?.id) - safeNumber(b?.id),

        moderationStatusDesc: (a, b) =>
            moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status)
            || safeNumber(b?.id) - safeNumber(a?.id),

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

/** Отфильтрованный список */
const filteredValues = computed(() => {
    let filtered = localValues.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortValues(filtered)
    }

    filtered = filtered.filter((value) => {
        const values = [
            value?.id,
            value?.code,
            value?.icon,
            value?.color,
            value?.status,
            value?.moderation_note,
            getValueTitle(value),
            getValueShort(value),
            getAttributeTitle(value),
            value?.attribute?.code,
            value?.attribute?.type,
            value?.attribute?.unit,
            value?.moderator?.name,
            value?.moderator?.email,
        ]

        return values.some((item) => normalize(item).includes(query))
    })

    return sortValues(filtered)
})

/** Локальная пагинация */
const paginatedValues = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredValues.value.slice(start, start + perPage)
})

/** Отображаемый список */
const displayedValues = computed(() => {
    return props.useServerProcessing
        ? valuesList.value
        : paginatedValues.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные значения характеристик */
const selectedValues = ref([])

/** Выделение всех элементов */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedValues.value.map((value) => value.id)

    if (checked) {
        selectedValues.value = [...new Set([...selectedValues.value, ...ids])]
    } else {
        selectedValues.value = selectedValues.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора строки */
const toggleSelectValue = (valueId) => {
    const index = selectedValues.value.indexOf(valueId)

    if (index > -1) {
        selectedValues.value.splice(index, 1)
    } else {
        selectedValues.value.push(valueId)
    }
}

/** Массовое изменение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedValues.value.length) {
        toast.warning('Выберите значения характеристик для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedValues.value]

    router.put(
        route('admin.actions.marketAttributeValues.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localValues.value = localValues.value.map((value) => {
                    return idsToUpdate.includes(value.id)
                        ? { ...value, activity: newActivity }
                        : value
                })

                selectedValues.value = []
                toast.success('Активность значений характеристик массово обновлена.')
            },

            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedValues.value.length) {
        toast.warning('Выберите хотя бы одно значение характеристики для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные значения характеристик?')) return

    router.delete(route('admin.actions.marketAttributeValues.bulkDestroy'), {
        data: { ids: selectedValues.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            selectedValues.value = []
            toast.success('Массовое удаление значений характеристик успешно завершено.')
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении значений характеристик.')
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

/** Модерация */
const approveValue = (value, status = 1, note = '') => {
    if (!value?.id) return

    router.put(
        route('admin.actions.marketAttributeValues.approve', { marketAttributeValue: value.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalValue(value.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Значение характеристики одобрено.' : 'Значение характеристики отклонено.')
            },

            onError: () => toast.error('Ошибка модерации значения характеристики.'),
        }
    )
}

/** Массовое обновление сортировки */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.marketAttributeValues.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => toast.success('Сортировка значений характеристик обновлена.'),

            onError: (errors) => {
                console.error('Ошибка сортировки значений характеристик:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки.')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketAttributeValues')">
        <template #header>
            <TitlePage>{{ t('marketAttributeValues') }}</TitlePage>
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
                    <CountTable>{{ valuesCount }}</CountTable>

                    <BulkActionSelect
                        v-if="valuesCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
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
