<script setup>
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import SortSelect from '@/Components/Admin/System/User/Sort/SortSelect.vue'
import UserTable from '@/Components/Admin/System/User/Table/UserTable.vue'
import UserCardGrid from '@/Components/Admin/System/User/View/UserCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    adminSystemUsersProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    users: { type: [Array, Object], default: () => [] },
    usersCount: { type: Number, default: 0 },

    adminSystemUsersPerPage: { type: Number, default: 20 },
    adminSystemUsersDefaultSort: { type: String, default: 'idDesc' },

    adminCountUsers: { type: Number, default: 20 },
    adminSortUsers: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_users') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_users', val)
})

const usersList = computed(() => {
    if (Array.isArray(props.users)) return props.users
    if (Array.isArray(props.users?.data)) return props.users.data
    return []
})

const localUsers = ref([])

watch(
    usersList,
    (newVal) => {
        localUsers.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSystemUsersPerPage || props.adminCountUsers || 20)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountUsers'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSystemUsersDefaultSort ||
    props.adminSortUsers ||
    'idDesc'
)

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortUsers'), { value: newVal }, {
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

const getRolesText = (user) => {
    const roles = Array.isArray(user?.roles) ? user.roles : []
    return roles.map(role => role?.name || '').filter(Boolean).join(' ')
}

const getPermissionsText = (user) => {
    const permissions = Array.isArray(user?.permissions) ? user.permissions : []
    return permissions.map(permission => permission?.name || '').filter(Boolean).join(' ')
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]))
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]))
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortUsers = (items) => {
    const list = (items || []).slice()

    const sortMap = {
        idAsc: (a, b) => safeNumber(a?.id) - safeNumber(b?.id),
        idDesc: (a, b) => safeNumber(b?.id) - safeNumber(a?.id),

        name: byStringAsc('name'),
        nameAsc: byStringAsc('name'),
        nameDesc: byStringDesc('name'),

        emailAsc: byStringAsc('email'),
        emailDesc: byStringDesc('email'),

        rolesAsc: byNumberAsc('roles_count'),
        rolesDesc: byNumberDesc('roles_count'),

        permissionsAsc: byNumberAsc('permissions_count'),
        permissionsDesc: byNumberDesc('permissions_count'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const searchWords = computed(() =>
    normalize(searchQuery.value)
        .split(/[\s:#№,"'«»(){}\[\].!?/\\|;+=*&^%$@<>`~_-]+/u)
        .map(word => word.trim())
        .filter(Boolean)
)

const filteredUsers = computed(() => {
    let filtered = localUsers.value || []

    if (!searchWords.value.length) {
        return sortUsers(filtered)
    }

    filtered = filtered.filter((user) => {
        const searchableValues = [
            normalize(user?.name),
            normalize(user?.email),
            normalize(getRolesText(user)),
            normalize(getPermissionsText(user)),
        ]

        return searchWords.value.every((word) => {
            if (/^\d+$/.test(word) && safeNumber(user?.id) === Number(word)) {
                return true
            }

            return searchableValues.some(value => value.includes(word))
        })
    })

    return sortUsers(filtered)
})

const paginatedUsers = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredUsers.value.slice(start, start + per)
})

const displayedUsers = computed(() => {
    return props.useServerProcessing
        ? usersList.value
        : paginatedUsers.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const userToDeleteId = ref(null)
const userToDeleteName = ref('')

const confirmDelete = (id, name = '') => {
    userToDeleteId.value = id
    userToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    userToDeleteId.value = null
    userToDeleteName.value = ''
}

const deleteUser = () => {
    if (userToDeleteId.value === null) return

    const idToDelete = userToDeleteId.value
    const nameToDelete = userToDeleteName.value

    router.delete(route('admin.users.destroy', { user: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Пользователь "${nameToDelete || 'ID: ' + idToDelete}" удалён.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Пользователь: ${nameToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}
</script>

<template>
    <AdminLayout :title="t('users')">
        <template #header>
            <TitlePage>{{ t('users') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.users.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addUser') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSystemUsersProcessingMode"
                        :mode="adminSystemUsersProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="usersCount"
                    />
                </div>

                <SearchInput
                    v-if="usersCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByNameOrEmail')"
                />

                <ServerSearchInput
                    v-if="usersCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="usersCount"
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
                        update-route="admin.settings.updateAdminCountUsers"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="usersCount"
                    class="flex justify-between items-center flex-col md:flex-row"
                >
                    <CountTable>{{ usersCount }}</CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="usersCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredUsers.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="users"
                    />
                </div>

                <UserTable
                    v-if="viewMode === 'table'"
                    :users="displayedUsers"
                    @delete="confirmDelete"
                />

                <UserCardGrid
                    v-else
                    :users="displayedUsers"
                    @delete="confirmDelete"
                />

                <div
                    v-if="usersCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredUsers.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="users"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteUser"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
