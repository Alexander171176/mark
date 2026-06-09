<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список курсов школы
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

import BulkActionSelect from '@/Components/Admin/School/SchoolCourse/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCourse/Sort/SortSelect.vue'
import CourseTable from '@/Components/Admin/School/SchoolCourse/Table/CourseTable.vue'
import CourseCardGrid from '@/Components/Admin/School/SchoolCourse/View/CourseCardGrid.vue'

// Локализация и Toast уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    courses: { type: Array, default: () => [] },
    coursesCount: { type: Number, default: 0 },

    adminSchoolCoursesPerPage: { type: Number, default: 10 },
    adminSchoolCoursesDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения (таблица / карточки)
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем режим отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия курсов
const localCourses = ref([])

// Обновление локального списка курсов
watch(
    () => props.courses,
    (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolCoursesPerPage || 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountCourses'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.')
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolCoursesDefaultSort || 'idDesc')

// Сохранение параметра сортировки
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortCourses'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.')
    })
})

// Модальное окно удаления, ID удаляемого курса, Название удаляемого курса
const showConfirmDeleteModal = ref(false)
const courseToDeleteId = ref(null)
const courseToDeleteTitle = ref('')

// Открытие модального окна удаления
const confirmDelete = (courseOrId, title = null) => {
    if (typeof courseOrId === 'object') {
        courseToDeleteId.value = courseOrId.id
        courseToDeleteTitle.value = title || courseOrId.title || `ID: ${courseOrId.id}`
    } else {
        courseToDeleteId.value = courseOrId
        courseToDeleteTitle.value = title || `ID: ${courseOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    courseToDeleteId.value = null
    courseToDeleteTitle.value = ''
}

// Удаление курса
const deleteCourse = () => {
    if (courseToDeleteId.value === null) return

    const idToDelete = courseToDeleteId.value
    const titleToDelete = courseToDeleteTitle.value

    router.delete(route('admin.schoolCourses.destroy', { schoolCourse: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Курс "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Курс: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal()
    })
}

// Текущая страница
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация строки поиска
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Сортировка курсов
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field]) || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortCourses = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'left') return list.filter(item => !!item.left)
    if (sortParam.value === 'noLeft') return list.filter(item => !item.left)
    if (sortParam.value === 'main') return list.filter(item => !!item.main)
    if (sortParam.value === 'noMain') return list.filter(item => !item.main)
    if (sortParam.value === 'right') return list.filter(item => !!item.right)
    if (sortParam.value === 'noRight') return list.filter(item => !item.right)

    if (sortParam.value === 'isNew') return list.filter(item => !!item.is_new)
    if (sortParam.value === 'isHit') return list.filter(item => !!item.is_hit)
    if (sortParam.value === 'isSale') return list.filter(item => !!item.is_sale)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: byStringAsc('title'),
        titleDesc: byStringDesc('title'),

        slugAsc: byStringAsc('slug'),
        slugDesc: byStringDesc('slug'),

        levelAsc: byStringAsc('level'),
        levelDesc: byStringDesc('level'),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        availabilityAsc: byStringAsc('availability'),
        availabilityDesc: byStringDesc('availability'),

        studentsCountAsc: byNumberAsc('students_count'),
        studentsCountDesc: byNumberDesc('students_count'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes'),
        likesDesc: byNumberDesc('likes'),

        popularityAsc: byNumberAsc('popularity'),
        popularityDesc: byNumberDesc('popularity'),

        ratingCountAsc: byNumberAsc('rating_count'),
        ratingCountDesc: byNumberDesc('rating_count'),

        ratingAvgAsc: byNumberAsc('rating_avg'),
        ratingAvgDesc: byNumberDesc('rating_avg'),

        difficultyAsc: byNumberAsc('difficulty'),
        difficultyDesc: byNumberDesc('difficulty'),

        durationAsc: byNumberAsc('duration'),
        durationDesc: byNumberDesc('duration'),

        modulesAsc: byNumberAsc('modules_count'),
        modulesDesc: byNumberDesc('modules_count'),

        lessonsAsc: byNumberAsc('lessons_count'),
        lessonsDesc: byNumberDesc('lessons_count'),

        tracksAsc: byNumberAsc('tracks_count'),
        tracksDesc: byNumberDesc('tracks_count'),

        hashtagsAsc: byNumberAsc('hashtags_count'),
        hashtagsDesc: byNumberDesc('hashtags_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        pricesAsc: byNumberAsc('prices_count'),
        pricesDesc: byNumberDesc('prices_count'),

        reviewsAsc: byNumberAsc('reviews_count'),
        reviewsDesc: byNumberDesc('reviews_count'),

        enrollmentsAsc: byNumberAsc('enrollments_count'),
        enrollmentsDesc: byNumberDesc('enrollments_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        isNewAsc: byNumberAsc('is_new'),
        isNewDesc: byNumberDesc('is_new'),

        isHitAsc: byNumberAsc('is_hit'),
        isHitDesc: byNumberDesc('is_hit'),

        isSaleAsc: byNumberAsc('is_sale'),
        isSaleDesc: byNumberDesc('is_sale'),

        publishedAtAsc: (a, b) => safeDate(a.published_at) - safeDate(b.published_at)
            || safeNumber(a.id) - safeNumber(b.id),

        publishedAtDesc: (a, b) => safeDate(b.published_at) - safeDate(a.published_at)
            || safeNumber(b.id) - safeNumber(a.id),

        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at)
            || safeNumber(a.id) - safeNumber(b.id),

        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at)
            || safeNumber(b.id) - safeNumber(a.id),

        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at)
            || safeNumber(a.id) - safeNumber(b.id),

        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at)
            || safeNumber(b.id) - safeNumber(a.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

// Отфильтрованные курсы
const filteredCourses = computed(() => {
    let filtered = localCourses.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortCourses(filtered)
    }

    filtered = filtered.filter((course) => {
        const title = normalize(course?.title)
        const subtitle = normalize(course?.subtitle)
        const slug = normalize(course?.slug)
        const short = normalize(course?.short)
        const description = normalize(course?.description)
        const instructor = normalize(course?.instructorProfile?.name || course?.instructorProfile?.title)

        const hasTrack = (course?.tracks || []).some(track =>
            normalize(track?.name).includes(q)
        )

        const hasHashtag = (course?.hashtags || []).some(hashtag =>
            normalize(hashtag?.name).includes(q)
        )

        return (
            title.includes(q) ||
            subtitle.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            instructor.includes(q) ||
            hasTrack ||
            hasHashtag
        )
    })

    return sortCourses(filtered)
})

// Курсы текущей страницы
const paginatedCourses = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredCourses.value.slice(start, start + per)
})

// Сброс страницы при поиске и изменении лимита
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление курса
const patchCourse = (courseId, payload) => {
    const index = localCourses.value.findIndex(course => course.id === courseId)

    if (index !== -1) {
        localCourses.value[index] = {
            ...localCourses.value[index],
            ...payload
        }
    }
}

// Выбранные курсы
const selectedCourses = ref([])

// Выбрать / снять выбор со всех курсов
const toggleAll = ({ ids, checked }) => {
    selectedCourses.value = checked ? [...ids] : []
}

// Выбор одного курса
const toggleSelectCourse = (id) => {
    const index = selectedCourses.value.indexOf(id)

    if (index > -1) {
        selectedCourses.value.splice(index, 1)
    } else {
        selectedCourses.value.push(id)
    }
}

// Обновление порядка сортировки
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolCourses.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок курсов успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки курсов:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок курсов.')

            router.reload({
                only: ['courses'],
                preserveScroll: true
            })
        }
    })
}

// Массовое обновление активности
const bulkToggleActivity = (newActivity) => {
    if (!selectedCourses.value.length) {
        toast.warning('Выберите курсы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedCourses.value]

    router.put(route('admin.actions.schoolCourses.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchCourse(id, { activity: newActivity }))
            selectedCourses.value = []
            toast.success('Активность выбранных курсов обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        }
    })
}

// Массовое обновление boolean-полей
const bulkToggleFlag = (field, routeName, value) => {
    if (!selectedCourses.value.length) {
        toast.warning('Выберите курсы.')
        return
    }

    const idsToUpdate = [...selectedCourses.value]

    router.put(route(routeName), {
        ids: idsToUpdate,
        [field]: value
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchCourse(id, { [field]: value }))
            selectedCourses.value = []
            toast.success('Выбранные курсы обновлены.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления.')
        }
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedCourses.value = paginatedCourses.value.map(course => course.id)
    } else if (action === 'deselectAll') {
        selectedCourses.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'isNewOn') {
        bulkToggleFlag('is_new', 'admin.actions.schoolCourses.bulkUpdateIsNew', true)
    } else if (action === 'isNewOff') {
        bulkToggleFlag('is_new', 'admin.actions.schoolCourses.bulkUpdateIsNew', false)
    } else if (action === 'isHitOn') {
        bulkToggleFlag('is_hit', 'admin.actions.schoolCourses.bulkUpdateIsHit', true)
    } else if (action === 'isHitOff') {
        bulkToggleFlag('is_hit', 'admin.actions.schoolCourses.bulkUpdateIsHit', false)
    } else if (action === 'isSaleOn') {
        bulkToggleFlag('is_sale', 'admin.actions.schoolCourses.bulkUpdateIsSale', true)
    } else if (action === 'isSaleOff') {
        bulkToggleFlag('is_sale', 'admin.actions.schoolCourses.bulkUpdateIsSale', false)
    } else if (action === 'leftOn') {
        bulkToggleFlag('left', 'admin.actions.schoolCourses.bulkUpdateLeft', true)
    } else if (action === 'leftOff') {
        bulkToggleFlag('left', 'admin.actions.schoolCourses.bulkUpdateLeft', false)
    } else if (action === 'mainOn') {
        bulkToggleFlag('main', 'admin.actions.schoolCourses.bulkUpdateMain', true)
    } else if (action === 'mainOff') {
        bulkToggleFlag('main', 'admin.actions.schoolCourses.bulkUpdateMain', false)
    } else if (action === 'rightOn') {
        bulkToggleFlag('right', 'admin.actions.schoolCourses.bulkUpdateRight', true)
    } else if (action === 'rightOff') {
        bulkToggleFlag('right', 'admin.actions.schoolCourses.bulkUpdateRight', false)
    }

    event.target.value = ''
}

// Переключение активности курса
const toggleActivity = (course) => {
    const newActivity = !course.activity
    const courseTitle = course.title || `ID: ${course.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolCourses.updateActivity', {
        schoolCourse: course.id
    }), {
        activity: newActivity
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchCourse(course.id, { activity: newActivity })
            course.activity = newActivity
            toast.success(`Курс "${courseTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для курса "${courseTitle}".`)
        }
    })
}

// Переключение позиции курса
const togglePlacement = (course, field, routeName) => {
    const newValue = !course[field]
    const courseTitle = course.title || `ID: ${course.id}`

    router.put(route(routeName, {
        schoolCourse: course.id
    }), {
        [field]: newValue
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchCourse(course.id, { [field]: newValue })
            course[field] = newValue
            toast.success(`Поле "${field}" для курса "${courseTitle}" обновлено.`)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || `Ошибка обновления поля "${field}".`)
        }
    })
}

// Переключение boolean-флага курса
const toggleFlag = (course, field, routeName) => {
    const newValue = !course[field]
    const courseTitle = course.title || `ID: ${course.id}`

    router.put(route(routeName, {
        schoolCourse: course.id
    }), {
        [field]: newValue
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchCourse(course.id, { [field]: newValue })
            course[field] = newValue
            toast.success(`Флаг "${field}" для курса "${courseTitle}" обновлён.`)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || `Ошибка обновления флага "${field}".`)
        }
    })
}

// Переключение позиции left
const toggleLeft = (course) =>
    togglePlacement(course, 'left', 'admin.actions.schoolCourses.updateLeft')

// Переключение позиции main
const toggleMain = (course) =>
    togglePlacement(course, 'main', 'admin.actions.schoolCourses.updateMain')

// Переключение позиции right
const toggleRight = (course) =>
    togglePlacement(course, 'right', 'admin.actions.schoolCourses.updateRight')

// Переключение флага "новый"
const toggleIsNew = (course) =>
    toggleFlag(course, 'is_new', 'admin.actions.schoolCourses.updateIsNew')

// Переключение флага "популярный"
const toggleIsHit = (course) =>
    toggleFlag(course, 'is_hit', 'admin.actions.schoolCourses.updateIsHit')

// Переключение флага "скидка"
const toggleIsSale = (course) =>
    toggleFlag(course, 'is_sale', 'admin.actions.schoolCourses.updateIsSale')

</script>

<template>
    <AdminLayout :title="t('courses')">
        <template #header>
            <TitlePage>{{ t('courses') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolCourses.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addCourse') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="coursesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div v-if="coursesCount"
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

                <div v-if="coursesCount"
                     class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ coursesCount }}</CountTable>
                    <BulkActionSelect @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="coursesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCourses.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <CourseTable
                    v-if="viewMode === 'table'"
                    :courses="paginatedCourses"
                    :selected-courses="selectedCourses"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-is-new="toggleIsNew"
                    @toggle-is-hit="toggleIsHit"
                    @toggle-is-sale="toggleIsSale"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCourse"
                    @toggle-all="toggleAll"
                />

                <CourseCardGrid
                    v-else
                    :courses="paginatedCourses"
                    :selected-courses="selectedCourses"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-is-new="toggleIsNew"
                    @toggle-is-hit="toggleIsHit"
                    @toggle-is-sale="toggleIsSale"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCourse"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="coursesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCourses.length"
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
            :onConfirm="deleteCourse"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
