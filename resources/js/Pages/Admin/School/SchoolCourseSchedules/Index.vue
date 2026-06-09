<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список расписаний курсов школы
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolCourseSchedule/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCourseSchedule/Sort/SortSelect.vue'
import CourseScheduleTable from '@/Components/Admin/School/SchoolCourseSchedule/Table/CourseScheduleTable.vue'
import CourseScheduleCardGrid from '@/Components/Admin/School/SchoolCourseSchedule/View/CourseScheduleCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    schedules: { type: Array, default: () => [] },
    schedulesCount: { type: Number, default: 0 },

    adminSchoolCourseSchedulesPerPage: { type: Number, default: 10 },
    adminSchoolCourseSchedulesDefaultSort: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },
})

const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

const localSchedules = ref([])

watch(
    () => props.schedules,
    (newVal) => {
        localSchedules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSchoolCourseSchedulesPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountCourseSchedules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(props.adminSchoolCourseSchedulesDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortCourseSchedules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

const showConfirmDeleteModal = ref(false)
const scheduleToDeleteId = ref(null)
const scheduleToDeleteTitle = ref('')

const confirmDelete = (scheduleOrId, title = null) => {
    if (typeof scheduleOrId === 'object') {
        scheduleToDeleteId.value = scheduleOrId.id
        scheduleToDeleteTitle.value = title || scheduleOrId.title || `ID: ${scheduleOrId.id}`
    } else {
        scheduleToDeleteId.value = scheduleOrId
        scheduleToDeleteTitle.value = title || `ID: ${scheduleOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    scheduleToDeleteId.value = null
    scheduleToDeleteTitle.value = ''
}

const deleteSchedule = () => {
    if (scheduleToDeleteId.value === null) return

    const idToDelete = scheduleToDeleteId.value
    const titleToDelete = scheduleToDeleteTitle.value

    router.delete(route('admin.schoolCourseSchedules.destroy', {
        schoolCourseSchedule: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Расписание "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Расписание: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const currentPage = ref(1)
const searchQuery = ref('')

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const sortSchedules = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'idAsc') {
        return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    }

    if (sortParam.value === 'idDesc') {
        return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
    }

    if (sortParam.value === 'sortAsc') {
        return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    }

    if (sortParam.value === 'sortDesc') {
        return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    }

    if (sortParam.value === 'titleAsc') {
        return list.sort((a, b) => normalize(a.title).localeCompare(normalize(b.title)))
    }

    if (sortParam.value === 'titleDesc') {
        return list.sort((a, b) => normalize(b.title).localeCompare(normalize(a.title)))
    }

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'online') return list.filter(item => !!item.is_online)
    if (sortParam.value === 'offline') return list.filter(item => !item.is_online)

    if ([
        'starts_at',
        'ends_at',
        'enroll_starts_at',
        'enroll_ends_at',
    ].includes(sortParam.value)) {
        return list.sort((a, b) => {
            const aTime = a[sortParam.value] ? new Date(a[sortParam.value]).getTime() : 0
            const bTime = b[sortParam.value] ? new Date(b[sortParam.value]).getTime() : 0

            return bTime - aTime
        })
    }

    if ([
        'views',
        'capacity',
        'images_count',
        'cohort_enrollments_count',
    ].includes(sortParam.value)) {
        return list.sort((a, b) => (b[sortParam.value] ?? 0) - (a[sortParam.value] ?? 0))
    }

    return list
}

const filteredSchedules = computed(() => {
    let filtered = localSchedules.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortSchedules(filtered)
    }

    filtered = filtered.filter((schedule) => {
        const title = normalize(schedule?.title)
        const slug = normalize(schedule?.slug)
        const notes = normalize(schedule?.notes)
        const location = normalize(schedule?.location)
        const courseTitle = normalize(schedule?.course?.title)
        const instructorTitle = normalize(schedule?.instructor?.title)
        const instructorUser = normalize(schedule?.instructor?.user?.name || schedule?.instructor?.user?.email)

        return (
            title.includes(q) ||
            slug.includes(q) ||
            notes.includes(q) ||
            location.includes(q) ||
            courseTitle.includes(q) ||
            instructorTitle.includes(q) ||
            instructorUser.includes(q)
        )
    })

    return sortSchedules(filtered)
})

const paginatedSchedules = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredSchedules.value.slice(start, start + per)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const patchSchedule = (scheduleId, payload) => {
    const index = localSchedules.value.findIndex(schedule => schedule.id === scheduleId)

    if (index !== -1) {
        localSchedules.value[index] = {
            ...localSchedules.value[index],
            ...payload,
        }
    }
}

const selectedSchedules = ref([])

const toggleAll = ({ ids, checked }) => {
    selectedSchedules.value = checked ? [...ids] : []
}

const toggleSelectSchedule = (id) => {
    const index = selectedSchedules.value.indexOf(id)

    if (index > -1) {
        selectedSchedules.value.splice(index, 1)
    } else {
        selectedSchedules.value.push(id)
    }
}

const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolCourseSchedules.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок расписаний успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки расписаний:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок расписаний.')

            router.reload({
                only: ['schedules'],
                preserveScroll: true,
            })
        },
    })
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedSchedules.value.length) {
        toast.warning('Выберите расписания для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedSchedules.value]

    router.put(route('admin.actions.schoolCourseSchedules.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchSchedule(id, { activity: newActivity }))
            selectedSchedules.value = []
            toast.success('Активность выбранных расписаний обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedSchedules.value = paginatedSchedules.value.map(schedule => schedule.id)
    } else if (action === 'deselectAll') {
        selectedSchedules.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

const toggleActivity = (schedule) => {
    const newActivity = !schedule.activity
    const scheduleTitle = schedule.title || `ID: ${schedule.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolCourseSchedules.updateActivity', {
        schoolCourseSchedule: schedule.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchSchedule(schedule.id, { activity: newActivity })
            schedule.activity = newActivity
            toast.success(`Расписание "${scheduleTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для расписания "${scheduleTitle}".`)
        },
    })
}

const cloneSchedule = (schedule) => {
    router.post(route('admin.actions.schoolCourseSchedules.clone', {
        schoolCourseSchedule: schedule.id,
    }), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Расписание успешно клонировано.'),
        onError: () => toast.error('Ошибка при клонировании расписания.'),
    })
}
</script>

<template>
    <AdminLayout :title="t('schedules')">
        <template #header>
            <TitlePage>{{ t('schedules') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolCourseSchedules.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addSchedule') }}
                    </DefaultButton>
                </div>
                <SearchInput
                    v-if="schedulesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />
                <div v-if="schedulesCount"
                     class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div v-if="schedulesCount"
                     class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ schedulesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="schedulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSchedules.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <CourseScheduleTable
                    v-if="viewMode === 'table'"
                    :schedules="paginatedSchedules"
                    :selected-schedules="selectedSchedules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneSchedule"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectSchedule"
                    @toggle-all="toggleAll"
                />

                <CourseScheduleCardGrid
                    v-else
                    :schedules="paginatedSchedules"
                    :selected-schedules="selectedSchedules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneSchedule"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectSchedule"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="schedulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSchedules.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteSchedule"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
