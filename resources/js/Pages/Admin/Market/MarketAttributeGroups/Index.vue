<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список групп характеристик MarketAttributeGroup
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

import BulkActionSelect from '@/Components/Admin/Market/MarketAttributeGroup/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketAttributeGroup/Sort/SortSelect.vue'
import AttributeGroupTable from '@/Components/Admin/Market/MarketAttributeGroup/Table/AttributeGroupTable.vue'
import AttributeGroupCardGrid from '@/Components/Admin/Market/MarketAttributeGroup/View/AttributeGroupCardGrid.vue'

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

    adminMarketAttributeGroupsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketAttributeGroupsPerPage: { type: Number, default: 10 },
    adminMarketAttributeGroupsDefaultSort: { type: String, default: 'idDesc' },

    groups: { type: [Array, Object], default: () => [] },
    groupsCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка прав администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Получение текущего перевода группы */
const getGroupTranslation = (group) => group?.translation || group?.translations?.[0] || {}

/** Получение названия группы */
const getGroupTitle = (group) => getGroupTranslation(group)?.title || `ID: ${group?.id}`

/** Получение краткого описания */
const getGroupShort = (group) => getGroupTranslation(group)?.short || ''

/** Получение имени владельца */
const getOwnerName = (group) => group?.owner?.name || ''

/** Получение Email владельца */
const getOwnerEmail = (group) => group?.owner?.email || ''

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
    localStorage.getItem('admin_view_mode_market_attribute_groups') || 'cards'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_attribute_groups', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketAttributeGroupsPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketAttributeGroups'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} групп характеристик на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва групп характеристик.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam || props.adminMarketAttributeGroupsDefaultSort || 'idDesc'
)

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketAttributeGroups'),
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

                toast.info('Сортировка групп характеристик успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки групп характеристик.')
            },
        }
    )
})

/** Локальная копия списка групп */
const localGroups = ref([])

/** Исходный список групп */
const groupsList = computed(() => {
    if (Array.isArray(props.groups)) return props.groups
    if (Array.isArray(props.groups?.data)) return props.groups.data
    if (Array.isArray(props.groups?.data?.data)) return props.groups.data.data
    if (Array.isArray(props.groups?.resource)) return props.groups.resource

    return []
})

watch(
    groupsList,
    (newVal) => {
        localGroups.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Окно подтверждения удаления */
const showConfirmDeleteModal = ref(false)

/** ID удаляемой группы */
const groupToDeleteId = ref(null)

/** Название удаляемой группы */
const groupToDeleteTitle = ref('')

/** Подготовка удаления группы */
const confirmDelete = (groupOrId, title = null) => {
    if (typeof groupOrId === 'object') {
        groupToDeleteId.value = groupOrId.id
        groupToDeleteTitle.value = title || getGroupTitle(groupOrId)
    } else {
        groupToDeleteId.value = groupOrId
        groupToDeleteTitle.value = title || `ID: ${groupOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    groupToDeleteId.value = null
    groupToDeleteTitle.value = ''
}

/** Удаление группы */
const deleteGroup = () => {
    if (groupToDeleteId.value === null) return

    const idToDelete = groupToDeleteId.value
    const titleToDelete = groupToDeleteTitle.value

    router.delete(route('admin.marketAttributeGroups.destroy', { marketAttributeGroup: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Группа характеристик "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Группа: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}

/** Локальное обновление записи */
const patchLocalGroup = (groupId, callback) => {
    const index = localGroups.value.findIndex((group) => group.id === groupId)

    if (index !== -1) {
        callback(localGroups.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (group) => {
    const newActivity = !group.activity
    const title = getGroupTitle(group)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketAttributeGroups.updateActivity', { marketAttributeGroup: group.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalGroup(group.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Группа характеристик "${title}" ${actionText}.`)
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

/** Сортировка списка групп */
const sortGroups = (groups) => {
    const list = (groups || []).slice()

    if (sortParam.value === 'activity') return list.filter((group) => !!group.activity)
    if (sortParam.value === 'inactive') return list.filter((group) => !group.activity)

    if (sortParam.value === 'statusDraft') return list.filter((group) => group?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter((group) => group?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter((group) => group?.status === 'archived')

    if (sortParam.value === 'moderationPending') return list.filter((group) => moderationNum(group?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((group) => moderationNum(group?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((group) => moderationNum(group?.moderation_status) === 2)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getGroupTitle(a)).localeCompare(normalize(getGroupTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
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

        attributesCountAsc: byNumberAsc('attributes_count'),
        attributesCountDesc: byNumberDesc('attributes_count'),

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
const filteredGroups = computed(() => {
    let filtered = localGroups.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortGroups(filtered)
    }

    filtered = filtered.filter((group) => {
        const values = [
            group?.id,
            group?.code,
            group?.icon,
            group?.color,
            group?.status,
            group?.moderation_note,
            group?.attributes_count,
            getGroupTitle(group),
            getGroupShort(group),
            getOwnerName(group),
            getOwnerEmail(group),
            group?.moderator?.name,
            group?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortGroups(filtered)
})

/** Локальная пагинация */
const paginatedGroups = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredGroups.value.slice(start, start + perPage)
})

/** Отображаемый список */
const displayedGroups = computed(() => {
    return props.useServerProcessing
        ? groupsList.value
        : paginatedGroups.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные группы */
const selectedGroups = ref([])

/** Выделение всех элементов */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedGroups.value.map((group) => group.id)

    if (checked) {
        selectedGroups.value = [...new Set([...selectedGroups.value, ...ids])]
    } else {
        selectedGroups.value = selectedGroups.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора строки */
const toggleSelectGroup = (groupId) => {
    const index = selectedGroups.value.indexOf(groupId)

    if (index > -1) {
        selectedGroups.value.splice(index, 1)
    } else {
        selectedGroups.value.push(groupId)
    }
}

/** Массовое изменение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedGroups.value.length) {
        toast.warning('Выберите группы характеристик для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedGroups.value]

    router.put(
        route('admin.actions.marketAttributeGroups.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localGroups.value = localGroups.value.map((group) => {
                    return idsToUpdate.includes(group.id)
                        ? { ...group, activity: newActivity }
                        : group
                })

                selectedGroups.value = []
                toast.success('Активность групп характеристик массово обновлена.')
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
    if (!selectedGroups.value.length) {
        toast.warning('Выберите хотя бы одну группу характеристик для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные группы характеристик?')) return

    router.delete(route('admin.actions.marketAttributeGroups.bulkDestroy'), {
        data: { ids: selectedGroups.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            selectedGroups.value = []
            toast.success('Массовое удаление групп характеристик успешно завершено.')
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении групп характеристик.')
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

/** Модерация группы */
const approveGroup = (group, status = 1, note = '') => {
    if (!group?.id) return

    router.put(
        route('admin.actions.marketAttributeGroups.approve', { marketAttributeGroup: group.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalGroup(group.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Группа характеристик одобрена.' : 'Группа характеристик отклонена.')
            },

            onError: () => toast.error('Ошибка модерации группы характеристик.'),
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
        route('admin.actions.marketAttributeGroups.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => toast.success('Сортировка групп характеристик обновлена.'),

            onError: (errors) => {
                console.error('Ошибка сортировки групп характеристик:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки.')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketAttributeGroups')">
        <template #header>
            <TitlePage>{{ t('marketAttributeGroups') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketAttributeGroups.create')">
                        {{ t('addMarketAttributeGroup') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketAttributeGroupsProcessingMode"
                        :mode="adminMarketAttributeGroupsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="groupsCount"
                    />
                </div>

                <SearchInput
                    v-if="groupsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="groupsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="groupsCount"
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
                        update-route="admin.settings.updateAdminCountMarketAttributeGroups"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="groupsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ groupsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="groupsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="groupsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredGroups.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="groups"
                    />
                </div>

                <AttributeGroupTable
                    v-if="viewMode === 'table'"
                    :groups="displayedGroups"
                    :selected-groups="selectedGroups"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectGroup"
                    @toggle-all="toggleAll"
                    @approve="approveGroup"
                />

                <AttributeGroupCardGrid
                    v-else
                    :groups="displayedGroups"
                    :selected-groups="selectedGroups"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectGroup"
                    @toggle-all="toggleAll"
                    @approve="approveGroup"
                />

                <div
                    v-if="groupsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredGroups.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="groups"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteGroup"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
