<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список курсов школы
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolCourse/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCourse/Sort/SortSelect.vue'
import CourseTable from '@/Components/Admin/School/SchoolCourse/Table/CourseTable.vue'
import CourseCardGrid from '@/Components/Admin/School/SchoolCourse/View/CourseCardGrid.vue'

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные страницы из Inertia */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolCoursesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    courses: { type: [Array, Object], default: () => [] },
    coursesCount: { type: Number, default: 0 },

    adminSchoolCoursesPerPage: { type: Number, default: 6 },
    adminSchoolCoursesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_courses') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_courses', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список курсов:
 * frontend → обычный массив
 * server → courses.data
 */
const coursesList = computed(() => {
    if (Array.isArray(props.courses)) {
        return props.courses
    }

    if (Array.isArray(props.courses?.data)) {
        return props.courses.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/**
 * Локальная копия списка.
 * Используется для:
 * - локального поиска
 * - локальной сортировки
 * - моментального обновления UI
 */
const localCourses = ref([])

watch(
    coursesList,
    (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolCoursesPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountCourses'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSchoolCoursesDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortCourses'),
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

                toast.info('Сортировка успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

/* ==========================================================
 * ПОИСК И ПАГИНАЦИЯ
 * ========================================================== */

/** Поисковый запрос */
const searchQuery = ref(props.search || '')

/** Текущая страница frontend-пагинации */
const currentPage = ref(1)

/* ==========================================================
 * ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
 * ========================================================== */

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

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ РЕСУРСОВ
 * ========================================================== */

/** Получение заголовка курса */
const getCourseTitle = (course) => {
    return course?.title
        || course?.translation?.title
        || course?.translations?.[0]?.title
        || `ID: ${course?.id}`
}

/** Получение подзаголовка курса */
const getCourseSubtitle = (course) => {
    return course?.subtitle
        || course?.translation?.subtitle
        || course?.translations?.[0]?.subtitle
        || ''
}

/** Получение краткого описания курса */
const getCourseShort = (course) => {
    return course?.short
        || course?.translation?.short
        || course?.translations?.[0]?.short
        || ''
}

/** Получение описания курса */
const getCourseDescription = (course) => {
    return course?.description
        || course?.translation?.description
        || course?.translations?.[0]?.description
        || ''
}

/** Получение заголовка связанной сущности */
const getNestedTitle = (item) => {
    return item?.title
        || item?.name
        || item?.translation?.title
        || item?.translation?.name
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || item?.user?.name
        || ''
}

/** Получение имени преподавателя */
const getInstructorTitle = (course) => {
    return getNestedTitle(course?.instructorProfile || course?.instructor_profile)
}

/** Получение текста треков */
const getTracksText = (course) => {
    const tracks = Array.isArray(course?.tracks) ? course.tracks : []

    return tracks.map(getNestedTitle).filter(Boolean).join(' ')
}

/** Получение текста хештегов */
const getHashtagsText = (course) => {
    const hashtags = Array.isArray(course?.hashtags) ? course.hashtags : []

    return hashtags.map(getNestedTitle).filter(Boolean).join(' ')
}

/* ==========================================================
 * СОРТИРОВКА FRONTEND
 * ========================================================== */

/** Сортировка чисел ↑ */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел ↓ */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк ↑ */
const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка строк ↓ */
const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Главный обработчик сортировки.
 * Должен совпадать со scopeSortByParam() модели и SortSelect.vue.
 */
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

        titleAsc: (a, b) =>
            normalize(getCourseTitle(a)).localeCompare(normalize(getCourseTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getCourseTitle(b)).localeCompare(normalize(getCourseTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

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

        publishedAtAsc: (a, b) =>
            safeDate(a?.published_at) - safeDate(b?.published_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        publishedAtDesc: (a, b) =>
            safeDate(b?.published_at) - safeDate(a?.published_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        dateAsc: (a, b) =>
            safeDate(a?.published_at) - safeDate(b?.published_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.published_at) - safeDate(a?.published_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at) - safeDate(b?.updated_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at) - safeDate(a?.updated_at)
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * ПОИСК FRONTEND
 * ========================================================== */

/**
 * Фильтрация списка.
 *
 * frontend:
 * поиск выполняется здесь
 *
 * server:
 * поиск выполняется контроллером
 */
const filteredCourses = computed(() => {
    let filtered = localCourses.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortCourses(filtered)
    }

    filtered = filtered.filter((course) => {
        const title = normalize(getCourseTitle(course))
        const subtitle = normalize(getCourseSubtitle(course))
        const slug = normalize(course?.slug || course?.translation?.slug)
        const short = normalize(getCourseShort(course))
        const description = normalize(getCourseDescription(course))
        const instructor = normalize(getInstructorTitle(course))
        const tracks = normalize(getTracksText(course))
        const hashtags = normalize(getHashtagsText(course))

        return title.includes(query)
            || subtitle.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || instructor.includes(query)
            || tracks.includes(query)
            || hashtags.includes(query)
    })

    return sortCourses(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedCourses = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredCourses.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedCourses = computed(() => {
    return props.useServerProcessing
        ? coursesList.value
        : paginatedCourses.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const courseToDeleteId = ref(null)
const courseToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (courseOrId, title = null) => {
    if (typeof courseOrId === 'object') {
        courseToDeleteId.value = courseOrId.id
        courseToDeleteTitle.value = title || getCourseTitle(courseOrId)
    } else {
        courseToDeleteId.value = courseOrId
        courseToDeleteTitle.value = title || `ID: ${courseOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    courseToDeleteId.value = null
    courseToDeleteTitle.value = ''
}

/** Выполнить удаление */
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
        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

/**
 * Обновление записи локально
 * без полной перезагрузки страницы
 */
const patchCourse = (courseId, payload) => {
    const index = localCourses.value.findIndex(course => course.id === courseId)

    if (index !== -1) {
        localCourses.value[index] = {
            ...localCourses.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedCourses = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedCourses.value.map((course) => course.id)

    if (checked) {
        selectedCourses.value = [...new Set([...selectedCourses.value, ...ids])]
    } else {
        selectedCourses.value = selectedCourses.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectCourse = (id) => {
    const index = selectedCourses.value.indexOf(id)

    if (index > -1) {
        selectedCourses.value.splice(index, 1)
    } else {
        selectedCourses.value.push(id)
    }
}

/** Изменить порядок */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
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
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedCourses.value.length) {
        toast.warning('Выберите курсы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedCourses.value]

    router.put(route('admin.actions.schoolCourses.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
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
        },
    })
}

/** Массовые флаги */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedCourses.value.length) {
        toast.warning('Выберите курсы для массового действия.')
        return
    }

    const idsToUpdate = [...selectedCourses.value]

    router.put(route(routeName), {
        ids: idsToUpdate,
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchCourse(id, { [field]: newValue }))
            selectedCourses.value = []
            toast.success(successMessage)
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления.')
        },
    })
}

/** Обработчик массовых действий */
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
    } else if (action === 'isNewOn') {
        bulkToggleFlag('is_new', true, 'admin.actions.schoolCourses.bulkUpdateIsNew', 'Курсы отмечены как новые.')
    } else if (action === 'isNewOff') {
        bulkToggleFlag('is_new', false, 'admin.actions.schoolCourses.bulkUpdateIsNew', 'Флаг новых курсов снят.')
    } else if (action === 'isHitOn') {
        bulkToggleFlag('is_hit', true, 'admin.actions.schoolCourses.bulkUpdateIsHit', 'Курсы отмечены как популярные.')
    } else if (action === 'isHitOff') {
        bulkToggleFlag('is_hit', false, 'admin.actions.schoolCourses.bulkUpdateIsHit', 'Флаг популярных курсов снят.')
    } else if (action === 'isSaleOn') {
        bulkToggleFlag('is_sale', true, 'admin.actions.schoolCourses.bulkUpdateIsSale', 'Курсы отмечены как скидочные.')
    } else if (action === 'isSaleOff') {
        bulkToggleFlag('is_sale', false, 'admin.actions.schoolCourses.bulkUpdateIsSale', 'Флаг скидки снят.')
    } else if (action === 'leftOn') {
        bulkToggleFlag('left', true, 'admin.actions.schoolCourses.bulkUpdateLeft', 'Курсы добавлены в левую колонку.')
    } else if (action === 'leftOff') {
        bulkToggleFlag('left', false, 'admin.actions.schoolCourses.bulkUpdateLeft', 'Курсы убраны из левой колонки.')
    } else if (action === 'mainOn') {
        bulkToggleFlag('main', true, 'admin.actions.schoolCourses.bulkUpdateMain', 'Курсы добавлены в главный блок.')
    } else if (action === 'mainOff') {
        bulkToggleFlag('main', false, 'admin.actions.schoolCourses.bulkUpdateMain', 'Курсы убраны из главного блока.')
    } else if (action === 'rightOn') {
        bulkToggleFlag('right', true, 'admin.actions.schoolCourses.bulkUpdateRight', 'Курсы добавлены в правую колонку.')
    } else if (action === 'rightOff') {
        bulkToggleFlag('right', false, 'admin.actions.schoolCourses.bulkUpdateRight', 'Курсы убраны из правой колонки.')
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности */
const toggleActivity = (course) => {
    const newActivity = !course.activity
    const courseTitle = getCourseTitle(course)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolCourses.updateActivity', {
        schoolCourse: course.id,
    }), {
        activity: newActivity,
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
        },
    })
}

/** Универсальный переключатель флагов */
const toggleFlag = (course, field, routeName, successMessage, errorMessage) => {
    const newValue = !course[field]

    router.put(route(routeName, {
        schoolCourse: course.id,
    }), {
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchCourse(course.id, { [field]: newValue })
            course[field] = newValue
            toast.success(successMessage)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || errorMessage)
        },
    })
}

/** Левая колонка */
const toggleLeft = (course) => {
    toggleFlag(
        course,
        'left',
        'admin.actions.schoolCourses.updateLeft',
        'Левая колонка обновлена.',
        'Ошибка обновления левой колонки.'
    )
}

/** Главный блок */
const toggleMain = (course) => {
    toggleFlag(
        course,
        'main',
        'admin.actions.schoolCourses.updateMain',
        'Главный блок обновлён.',
        'Ошибка обновления главного блока.'
    )
}

/** Правая колонка */
const toggleRight = (course) => {
    toggleFlag(
        course,
        'right',
        'admin.actions.schoolCourses.updateRight',
        'Правая колонка обновлена.',
        'Ошибка обновления правой колонки.'
    )
}

/** Флаг "новый" */
const toggleIsNew = (course) => {
    toggleFlag(
        course,
        'is_new',
        'admin.actions.schoolCourses.updateIsNew',
        'Флаг "новый" обновлён.',
        'Ошибка обновления флага "новый".'
    )
}

/** Флаг "популярный" */
const toggleIsHit = (course) => {
    toggleFlag(
        course,
        'is_hit',
        'admin.actions.schoolCourses.updateIsHit',
        'Флаг "популярный" обновлён.',
        'Ошибка обновления флага "популярный".'
    )
}

/** Флаг "скидка" */
const toggleIsSale = (course) => {
    toggleFlag(
        course,
        'is_sale',
        'admin.actions.schoolCourses.updateIsSale',
        'Флаг "скидка" обновлён.',
        'Ошибка обновления флага "скидка".'
    )
}
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolCourses.create')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current opacity-50 shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addCourse') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolCoursesProcessingMode"
                        :mode="adminSchoolCoursesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="coursesCount"
                    />
                </div>

                <SearchInput
                    v-if="coursesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="coursesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="coursesCount"
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
                        update-route="admin.settings.updateAdminCountCourses"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="coursesCount"
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
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCourses.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="courses"
                    />
                </div>

                <CourseTable
                    v-if="viewMode === 'table'"
                    :courses="displayedCourses"
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
                    :courses="displayedCourses"
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
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCourses.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="courses"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteCourse"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
