<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import SortSelect from '@/Components/Admin/System/Permission/Sort/SortSelect.vue'
import PermissionTable from '@/Components/Admin/System/Permission/Table/PermissionTable.vue'
import PermissionCardGrid from '@/Components/Admin/System/Permission/View/PermissionCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    adminSystemPermissionsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    permissions: { type: [Array, Object], default: () => [] },
    permissionsCount: { type: Number, default: 0 },

    adminSystemPermissionsPerPage: { type: Number, default: 20 },
    adminSystemPermissionsDefaultSort: { type: String, default: 'nameAsc' },

    adminCountPermissions: { type: Number, default: 20 },
    adminSortPermissions: { type: String, default: 'nameAsc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_permissions') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_permissions', val)
})

const permissionsList = computed(() => {
    if (Array.isArray(props.permissions)) return props.permissions
    if (Array.isArray(props.permissions?.data)) return props.permissions.data
    return []
})

const localPermissions = ref([])

watch(
    permissionsList,
    (newVal) => {
        localPermissions.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSystemPermissionsPerPage || props.adminCountPermissions || 20)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountPermissions'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSystemPermissionsDefaultSort ||
    props.adminSortPermissions ||
    'nameAsc'
)

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortPermissions'), { value: newVal }, {
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

        onError: (errors) => {
            toast.error(errors.value || 'Ошибка обновления сортировки.')
        },
    })
})

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getRolesText = (permission) => {
    const roles = Array.isArray(permission?.roles) ? permission.roles : []
    return roles.map(role => role?.name || '').filter(Boolean).join(' ')
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]))
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]))
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortPermissions = (items) => {
    const list = (items || []).slice()

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        name: byStringAsc('name'),
        nameAsc: byStringAsc('name'),
        nameDesc: byStringDesc('name'),

        guardNameAsc: byStringAsc('guard_name'),
        guardNameDesc: byStringDesc('guard_name'),

        rolesAsc: byNumberAsc('roles_count'),
        rolesDesc: byNumberDesc('roles_count'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredPermissions = computed(() => {
    let filtered = localPermissions.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortPermissions(filtered)
    }

    filtered = filtered.filter((permission) => {
        const values = [
            permission?.id,
            permission?.name,
            permission?.guard_name,
            getRolesText(permission),
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortPermissions(filtered)
})

const paginatedPermissions = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredPermissions.value.slice(start, start + per)
})

const displayedPermissions = computed(() => {
    return props.useServerProcessing
        ? permissionsList.value
        : paginatedPermissions.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const permissionToDeleteId = ref(null)
const permissionToDeleteName = ref('')

const confirmDelete = (id, name = '') => {
    permissionToDeleteId.value = id
    permissionToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    permissionToDeleteId.value = null
    permissionToDeleteName.value = ''
}

const deletePermission = () => {
    if (permissionToDeleteId.value === null) return

    const idToDelete = permissionToDeleteId.value
    const nameToDelete = permissionToDeleteName.value

    router.delete(route('admin.permissions.destroy', { permission: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Разрешение "${nameToDelete || 'ID: ' + idToDelete}" удалено.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Разрешение: ${nameToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}
</script>

<template>
    <AdminLayout :title="t('permissions')">
        <template #header>
            <TitlePage>{{ t('permissions') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.permissions.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addPermission') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSystemPermissionsProcessingMode"
                        :mode="adminSystemPermissionsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="permissionsCount"
                    />
                </div>

                <SearchInput
                    v-if="permissionsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="permissionsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="permissionsCount"
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
                        update-route="admin.settings.updateAdminCountPermissions"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="permissionsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>{{ permissionsCount }}</CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="permissionsCount"
                    class="flex justify-center items-center flex-col md:flex-row"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPermissions.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="permissions"
                    />
                </div>

                <PermissionTable
                    v-if="viewMode === 'table'"
                    :permissions="displayedPermissions"
                    @delete="confirmDelete"
                />

                <PermissionCardGrid
                    v-else
                    :permissions="displayedPermissions"
                    @delete="confirmDelete"
                />

                <div
                    v-if="permissionsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPermissions.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="permissions"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deletePermission"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
