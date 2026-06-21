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

import SortSelect from '@/Components/Admin/System/ImageProcessor/Profile/Sort/SortSelect.vue'
import BulkActionSelect from '@/Components/Admin/System/ImageProcessor/Profile/Select/BulkActionSelect.vue'
import ProfileTable from '@/Components/Admin/System/ImageProcessor/Profile/Table/ProfileTable.vue'
import ProfileCardGrid from '@/Components/Admin/System/ImageProcessor/Profile/View/ProfileCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

/** =============================================
 * Входные параметры страницы
 * ============================================= */
const props = defineProps({
    currentLocale: { type: String, default: '' },

    useServerProcessing: { type: Boolean, default: false },

    adminImageProcessorProfilesPerPage: { type: Number, default: 20 },
    adminImageProcessorProfilesDefaultSort: { type: String, default: 'sortAsc' },
    adminImageProcessorProfilesProcessingMode: { type: String, default: 'frontend' },

    profiles: { type: [Array, Object], default: () => [] },
    profilesCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },
})

/** =============================================
 * Режим отображения (таблица / карточки)
 * сохраняется в localStorage
 * ============================================= */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_image_processor_profiles') || 'table'
)

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_image_processor_profiles', val)
})

/** =============================================
 * Приведение коллекции Inertia к массиву
 * ============================================= */
const profilesList = computed(() => {
    if (Array.isArray(props.profiles)) return props.profiles
    if (Array.isArray(props.profiles?.data)) return props.profiles.data
    return []
})

/** =============================================
 * Локальная копия профилей
 * используется для DnD и локального обновления
 * ============================================= */
const localProfiles = ref([])

watch(
    profilesList,
    (newVal) => {
        localProfiles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** =============================================
 * Настройка количества элементов на странице
 * ============================================= */
const itemsPerPage = ref(props.adminImageProcessorProfilesPerPage || 20)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountImageProcessorProfiles'), {
        key: 'adminImageProcessorProfilesPerPage',
        value: newVal,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления количества элементов.'),
    })
})

/** =============================================
 * Текущая сортировка списка
 * ============================================= */
const sortParam = ref(
    props.sortParam ||
    props.adminImageProcessorProfilesDefaultSort ||
    'sortAsc'
)

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortImageProcessorProfiles'), {
        key: 'adminImageProcessorProfilesDefaultSort',
        value: newVal,
    }, {
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

            toast.info('Сортировка успешно изменена.')
        },
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** =============================================
 * Утилиты нормализации данных
 * ============================================= */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** =============================================
 * Набор функций сортировки
 * ============================================= */
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

/** =============================================
 * Универсальная сортировка профилей
 * ============================================= */
const sortProfiles = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        keyAsc: byStringAsc('key'),
        keyDesc: byStringDesc('key'),

        nameAsc: byStringAsc('name'),
        nameDesc: byStringDesc('name'),

        variantsAsc: byNumberAsc('variants_count'),
        variantsDesc: byNumberDesc('variants_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        createdAtAsc: byStringAsc('created_at'),
        createdAtDesc: byStringDesc('created_at'),

        updatedAtAsc: byStringAsc('updated_at'),
        updatedAtDesc: byStringDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/** =============================================
 * Фильтрация и поиск
 * ============================================= */
const filteredProfiles = computed(() => {
    let filtered = localProfiles.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortProfiles(filtered)
    }

    filtered = filtered.filter((profile) => {
        const values = [
            profile?.id,
            profile?.sort,
            profile?.key,
            profile?.name,
            profile?.description,
            profile?.variants_count,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortProfiles(filtered)
})

/** =============================================
 * Локальная пагинация
 * ============================================= */
const paginatedProfiles = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredProfiles.value.slice(start, start + per)
})

/** =============================================
 * Итоговый набор профилей
 * зависит от режима обработки
 * ============================================= */
const displayedProfiles = computed(() => {
    return props.useServerProcessing
        ? profilesList.value
        : paginatedProfiles.value
})

/** =============================================
 * Сброс страницы при изменении фильтров
 * ============================================= */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** =============================================
 * Работа с выделением профилей
 * ============================================= */
const selectedProfiles = ref([])

/** =============================================
 * Локальное обновление профиля
 * ============================================= */
const patchProfile = (profileId, payload) => {
    const index = localProfiles.value.findIndex(profile => profile.id === profileId)

    if (index !== -1) {
        localProfiles.value[index] = {
            ...localProfiles.value[index],
            ...payload,
        }
    }
}

/** =============================================
 * Управление активностью
 * ============================================= */
const toggleActivity = (profile) => {
    const newActivity = !profile.activity

    router.put(route('admin.actions.imageProcessorProfiles.updateActivity', {
        imageProcessorProfile: profile.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchProfile(profile.id, { activity: newActivity })
            profile.activity = newActivity
            toast.success('Активность профиля обновлена.')
        },
        onError: (errors) => {
            toast.error(errors.activity || 'Ошибка изменения активности профиля.')
        },
    })
}

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedProfiles.value.map(profile => profile.id)

    if (checked) {
        selectedProfiles.value = [...new Set([...selectedProfiles.value, ...ids])]
    } else {
        selectedProfiles.value = selectedProfiles.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectProfile = (profileId) => {
    const index = selectedProfiles.value.indexOf(profileId)

    if (index > -1) {
        selectedProfiles.value.splice(index, 1)
    } else {
        selectedProfiles.value.push(profileId)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedProfiles.value.length) {
        toast.warning('Выберите профили для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedProfiles.value]

    router.put(route('admin.actions.imageProcessorProfiles.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchProfile(id, { activity: newActivity }))
            selectedProfiles.value = []
            toast.success('Активность профилей массово обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || 'Ошибка массового обновления активности.')
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

/** =============================================
 * Обновление сортировки (Drag & Drop)
 * ============================================= */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(route('admin.actions.imageProcessorProfiles.updateSortBulk'), {
        profiles: sortData,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок профилей успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки:', errors)
            toast.error(errors.general || errors.profiles || 'Не удалось обновить порядок профилей.')

            router.reload({
                only: ['profiles'],
                preserveScroll: true,
            })
        },
    })
}

/** =============================================
 * Удаление профиля
 * ============================================= */
const showConfirmDeleteModal = ref(false)
const profileToDeleteId = ref(null)

const confirmDelete = (id) => {
    profileToDeleteId.value = id
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    profileToDeleteId.value = null
}

const deleteProfile = () => {
    if (profileToDeleteId.value === null) return

    const idToDelete = profileToDeleteId.value

    router.delete(route('admin.imageProcessorProfiles.destroy', {
        imageProcessorProfile: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Профиль ID ${idToDelete} успешно удалён.`)
        },
        onError: (errors) => {
            toast.error(errors.general || 'Ошибка удаления профиля.')
        },
        onFinish: () => closeModal(),
    })
}
</script>

<template>
    <AdminLayout :title="t('imageProcessingProfiles')">
        <template #header>
            <TitlePage>{{ t('imageProcessingProfiles') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.imageProcessorProfiles.create')">
                        Добавить профиль
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminImageProcessorProfilesProcessingMode"
                        :mode="adminImageProcessorProfilesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="profilesCount"
                    />
                </div>

                <SearchInput
                    v-if="profilesCount && !useServerProcessing"
                    v-model="searchQuery"
                    placeholder="Поиск по профилям"
                />

                <ServerSearchInput
                    v-if="profilesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="profilesCount"
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
                    v-if="profilesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ profilesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="profilesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredProfiles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="profiles"
                    />
                </div>

                <ProfileTable
                    v-if="viewMode === 'table'"
                    :profiles="displayedProfiles"
                    :selected-profiles="selectedProfiles"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectProfile"
                    @toggle-all="toggleAll"
                />

                <ProfileCardGrid
                    v-else
                    :profiles="displayedProfiles"
                    :selected-profiles="selectedProfiles"
                    @toggle-activity="toggleActivity"
                    @update-sort-order="handleSortOrderUpdate"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectProfile"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="profilesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredProfiles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="profiles"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteProfile"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
