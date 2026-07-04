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

/** Получение текущего перевода характеристики */
const getAttributeTranslation = (attribute) => attribute?.translation || attribute?.translations?.[0] || {}

/** Получение названия характеристики */
const getAttributeTitle = (attribute) => getAttributeTranslation(attribute)?.title || `ID: ${attribute?.id}`

/** Получение краткого описания */
const getAttributeShort = (attribute) => getAttributeTranslation(attribute)?.short || ''

/** Получение названия группы */
const getGroupTitle = (attribute) => attribute?.group?.title || attribute?.group?.translation?.title || ''

/** Получение имени владельца */
const getOwnerName = (attribute) => attribute?.owner?.name || ''

/** Получение Email владельца */
const getOwnerEmail = (attribute) => attribute?.owner?.email || ''

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
    localStorage.getItem('admin_view_mode_market_attributes') || 'cards'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_attributes', value)
})

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
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва характеристик.'),
        }
    )
})

const sortParam = ref(
    props.sortParam || props.adminMarketAttributesDefaultSort || 'idDesc'
)

/** Текущий параметр сортировки */
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

                toast.info('Сортировка характеристик успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки характеристик.')
            },
        }
    )
})

/** Локальная копия списка характеристики */
const localAttributes = ref([])

/** Исходный список характеристики */
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
        localAttributes.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

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
        attributeToDeleteTitle.value = title || getAttributeTitle(attributeOrId)
    } else {
        attributeToDeleteId.value = attributeOrId
        attributeToDeleteTitle.value = title || `ID: ${attributeOrId}`
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

    router.delete(route('admin.marketAttributes.destroy', { marketAttribute: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Характеристика "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Характеристика: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}

/** Локальное обновление записи */
const patchLocalAttribute = (attributeId, callback) => {
    const index = localAttributes.value.findIndex((attribute) => attribute.id === attributeId)

    if (index !== -1) {
        callback(localAttributes.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (attribute) => {
    const newActivity = !attribute.activity
    const title = getAttributeTitle(attribute)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketAttributes.updateActivity', { marketAttribute: attribute.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalAttribute(attribute.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Характеристика "${title}" ${actionText}.`)
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

/** Сортировка списка характеристики */
const sortAttributes = (attributes) => {
    const list = (attributes || []).slice()

    if (sortParam.value === 'activity') return list.filter((attribute) => !!attribute.activity)
    if (sortParam.value === 'inactive') return list.filter((attribute) => !attribute.activity)

    if (sortParam.value === 'required') return list.filter((attribute) => !!attribute.required)
    if (sortParam.value === 'notRequired') return list.filter((attribute) => !attribute.required)

    if (sortParam.value === 'filterable') return list.filter((attribute) => !!attribute.filterable)
    if (sortParam.value === 'notFilterable') return list.filter((attribute) => !attribute.filterable)

    if (sortParam.value === 'visible') return list.filter((attribute) => !!attribute.visible)
    if (sortParam.value === 'hidden') return list.filter((attribute) => !attribute.visible)

    if (sortParam.value === 'statusDraft') return list.filter((attribute) => attribute?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter((attribute) => attribute?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter((attribute) => attribute?.status === 'archived')

    if (sortParam.value === 'moderationPending') return list.filter((attribute) => moderationNum(attribute?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((attribute) => moderationNum(attribute?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((attribute) => moderationNum(attribute?.moderation_status) === 2)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getAttributeTitle(a)).localeCompare(normalize(getAttributeTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getAttributeTitle(b)).localeCompare(normalize(getAttributeTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        groupTitleAsc: (a, b) =>
            normalize(getGroupTitle(a)).localeCompare(normalize(getGroupTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        groupTitleDesc: (a, b) =>
            normalize(getGroupTitle(b)).localeCompare(normalize(getGroupTitle(a)), locale.value)
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

        typeAsc: (a, b) =>
            normalize(a?.type).localeCompare(normalize(b?.type), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        typeDesc: (a, b) =>
            normalize(b?.type).localeCompare(normalize(a?.type), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        unitAsc: (a, b) =>
            normalize(a?.unit).localeCompare(normalize(b?.unit), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        unitDesc: (a, b) =>
            normalize(b?.unit).localeCompare(normalize(a?.unit), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        valuesCountAsc: byNumberAsc('values_count'),
        valuesCountDesc: byNumberDesc('values_count'),

        requiredAsc: byNumberAsc('required'),
        requiredDesc: byNumberDesc('required'),

        filterableAsc: byNumberAsc('filterable'),
        filterableDesc: byNumberDesc('filterable'),

        visibleAsc: byNumberAsc('visible'),
        visibleDesc: byNumberDesc('visible'),

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
const filteredAttributes = computed(() => {
    let filtered = localAttributes.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortAttributes(filtered)
    }

    filtered = filtered.filter((attribute) => {
        const values = [
            attribute?.id,
            attribute?.code,
            attribute?.icon,
            attribute?.color,
            attribute?.type,
            attribute?.unit,
            attribute?.status,
            attribute?.moderation_note,
            attribute?.values_count,
            getAttributeTitle(attribute),
            getAttributeShort(attribute),
            getGroupTitle(attribute),
            getOwnerName(attribute),
            getOwnerEmail(attribute),
            attribute?.moderator?.name,
            attribute?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortAttributes(filtered)
})

/** Локальная пагинация */
const paginatedAttributes = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredAttributes.value.slice(start, start + perPage)
})

/** Отображаемый список */
const displayedAttributes = computed(() => {
    return props.useServerProcessing
        ? attributesList.value
        : paginatedAttributes.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные характеристики */
const selectedAttributes = ref([])

/** Выделение всех элементов */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedAttributes.value.map((attribute) => attribute.id)

    if (checked) {
        selectedAttributes.value = [...new Set([...selectedAttributes.value, ...ids])]
    } else {
        selectedAttributes.value = selectedAttributes.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора строки */
const toggleSelectAttribute = (attributeId) => {
    const index = selectedAttributes.value.indexOf(attributeId)

    if (index > -1) {
        selectedAttributes.value.splice(index, 1)
    } else {
        selectedAttributes.value.push(attributeId)
    }
}

/** Массовое изменение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedAttributes.value.length) {
        toast.warning('Выберите характеристики для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedAttributes.value]

    router.put(
        route('admin.actions.marketAttributes.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localAttributes.value = localAttributes.value.map((attribute) => {
                    return idsToUpdate.includes(attribute.id)
                        ? { ...attribute, activity: newActivity }
                        : attribute
                })

                selectedAttributes.value = []
                toast.success('Активность характеристик массово обновлена.')
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
    if (!selectedAttributes.value.length) {
        toast.warning('Выберите хотя бы одну характеристику для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные характеристики?')) return

    router.delete(route('admin.actions.marketAttributes.bulkDestroy'), {
        data: { ids: selectedAttributes.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            selectedAttributes.value = []
            toast.success('Массовое удаление характеристик успешно завершено.')
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении характеристик.')
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

/** Модерация характеристики */
const approveAttribute = (attribute, status = 1, note = '') => {
    if (!attribute?.id) return

    router.put(
        route('admin.actions.marketAttributes.approve', { marketAttribute: attribute.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalAttribute(attribute.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Характеристика одобрена.' : 'Характеристика отклонена.')
            },

            onError: () => toast.error('Ошибка модерации характеристики.'),
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
        route('admin.actions.marketAttributes.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => toast.success('Сортировка характеристик обновлена.'),

            onError: (errors) => {
                console.error('Ошибка сортировки характеристик:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки.')
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
