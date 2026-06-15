<script setup>
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import SortSelect from '@/Components/Admin/System/Parameters/Sort/SortSelect.vue'
import BulkActionSelect from '@/Components/Admin/System/Parameters/Select/BulkActionSelect.vue'
import ParameterTable from '@/Components/Admin/System/Parameters/Table/ParameterTable.vue'
import ParameterCardGrid from '@/Components/Admin/System/Parameters/View/ParameterCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },

    adminSystemSettingsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    settings: { type: [Array, Object], default: () => [] },
    settingsCount: { type: Number, default: 0 },

    adminSystemSettingsPerPage: { type: Number, default: 10 },
    adminSystemSettingsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_parameters') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_parameters', val)
})

const settingsList = computed(() => {
    if (Array.isArray(props.settings)) return props.settings
    if (Array.isArray(props.settings?.data)) return props.settings.data
    return []
})

const localSettings = ref([])

watch(
    settingsList,
    (newVal) => {
        localSettings.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSystemSettingsPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountSettings'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSystemSettingsDefaultSort ||
    'idDesc'
)

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortSettings'), { value: newVal }, {
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

            toast.info('Сортировка успешно изменена')
        },
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortSettings = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sort: byNumberAsc('sort'),
        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        category: byStringAsc('category'),
        categoryAsc: byStringAsc('category'),
        categoryDesc: byStringDesc('category'),

        type: byStringAsc('type'),
        typeAsc: byStringAsc('type'),
        typeDesc: byStringDesc('type'),

        option: byStringAsc('option'),
        optionAsc: byStringAsc('option'),
        optionDesc: byStringDesc('option'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredSettings = computed(() => {
    let filtered = localSettings.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortSettings(filtered)
    }

    filtered = filtered.filter((setting) => {
        const values = [
            setting?.id,
            setting?.sort,
            setting?.type,
            setting?.option,
            setting?.value,
            setting?.constant,
            setting?.category,
            setting?.description,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortSettings(filtered)
})

const paginatedSettings = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredSettings.value.slice(start, start + per)
})

const displayedSettings = computed(() => {
    return props.useServerProcessing
        ? settingsList.value
        : paginatedSettings.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const settingToDeleteId = ref(null)

const confirmDelete = (id) => {
    settingToDeleteId.value = id
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    settingToDeleteId.value = null
}

const deleteSetting = () => {
    if (settingToDeleteId.value === null) return

    const idToDelete = settingToDeleteId.value

    router.delete(route('admin.settings.destroy', { setting: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Параметр ID ${idToDelete} успешно удалён.`)
        },
        onError: (errors) => {
            const errorMsg = errors.general || 'Ошибка удаления параметра.'
            toast.error(`${errorMsg} (ID: ${idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const patchSetting = (settingId, payload) => {
    const index = localSettings.value.findIndex(setting => setting.id === settingId)

    if (index !== -1) {
        localSettings.value[index] = {
            ...localSettings.value[index],
            ...payload,
        }
    }
}

const toggleActivity = (setting) => {
    const newActivity = !setting.activity

    router.put(route('admin.actions.settings.updateActivity', { setting: setting.id }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            patchSetting(setting.id, { activity: newActivity })
            setting.activity = newActivity

            if (page.props.flash.success) {
                toast.success(page.props.flash.success)
            } else if (page.props.flash.warning) {
                toast.warning(page.props.flash.warning)
            } else if (page.props.flash.error || page.props.flash.general) {
                toast.error(page.props.flash.error || page.props.flash.general)
            } else {
                toast.info(`Изменение активности параметра "${setting.option}" выполнено.`)
            }
        },
        onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности параметра "${setting.option}".`)
        },
    })
}

const selectedSettings = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedSettings.value.map(setting => setting.id)

    if (checked) {
        selectedSettings.value = [...new Set([...selectedSettings.value, ...ids])]
    } else {
        selectedSettings.value = selectedSettings.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectSetting = (settingId) => {
    const index = selectedSettings.value.indexOf(settingId)

    if (index > -1) {
        selectedSettings.value.splice(index, 1)
    } else {
        selectedSettings.value.push(settingId)
    }
}

const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(route('admin.actions.settings.updateSortBulk'), {
        settings: sortData,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок параметров успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки:', errors)
            toast.error(errors.general || errors.settings || 'Не удалось обновить порядок параметров.')

            router.reload({
                only: ['settings'],
                preserveScroll: true,
            })
        },
    })
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedSettings.value.length) {
        toast.warning('Выберите параметры для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedSettings.value]

    router.put(route('admin.actions.settings.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchSetting(id, { activity: newActivity }))
            selectedSettings.value = []
            toast.success('Активность параметров массово обновлена')
        },
        onError: (errors) => {
            const msg = errors?.ids || errors?.activity || errors?.general ||
                'Не удалось массово обновить активность параметров'

            toast.error(msg)
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
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('parametersHeader')">
        <template #header>
            <TitlePage>{{ t('parametersHeader') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.parameters.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addParameter') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSystemSettingsProcessingMode"
                        :mode="adminSystemSettingsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="settingsCount"
                    />
                </div>

                <SearchInput
                    v-if="settingsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByParameter')"
                />

                <ServerSearchInput
                    v-if="settingsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="settingsCount"
                    class="flex items-center justify-between flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSettings"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="settingsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ settingsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="settingsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSettings.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="settings"
                    />
                </div>

                <ParameterTable
                    v-if="viewMode === 'table'"
                    :settings="displayedSettings"
                    :selected-settings="selectedSettings"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectSetting"
                    @toggle-all="toggleAll"
                />

                <ParameterCardGrid
                    v-else
                    :settings="displayedSettings"
                    :selected-settings="selectedSettings"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectSetting"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="settingsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSettings.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="settings"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteSetting"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
