<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список уроков школы
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

import BulkActionSelect from '@/Components/Admin/School/SchoolLesson/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolLesson/Sort/SortSelect.vue'
import LessonTable from '@/Components/Admin/School/SchoolLesson/Table/LessonTable.vue'
import LessonCardGrid from '@/Components/Admin/School/SchoolLesson/View/LessonCardGrid.vue'

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

    adminSchoolLessonsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    lessons: { type: [Array, Object], default: () => [] },
    lessonsCount: { type: Number, default: 0 },

    adminSchoolLessonsPerPage: { type: Number, default: 6 },
    adminSchoolLessonsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_lessons') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_lessons', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список уроков:
 * frontend → обычный массив
 * server → lessons.data
 */
const lessonsList = computed(() => {
    if (Array.isArray(props.lessons)) {
        return props.lessons
    }

    if (Array.isArray(props.lessons?.data)) {
        return props.lessons.data
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
const localLessons = ref([])

watch(
    lessonsList,
    (newVal) => {
        localLessons.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolLessonsPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolLessons'),
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
const sortParam = ref(props.sortParam || props.adminSchoolLessonsDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolLessons'),
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

/** Получение заголовка урока */
const getLessonTitle = (lesson) => {
    return lesson?.title
        || lesson?.translation?.title
        || lesson?.translations?.[0]?.title
        || `ID: ${lesson?.id}`
}

/** Получение подзаголовка урока */
const getLessonSubtitle = (lesson) => {
    return lesson?.subtitle
        || lesson?.translation?.subtitle
        || lesson?.translations?.[0]?.subtitle
        || ''
}

/** Получение краткого описания урока */
const getLessonShort = (lesson) => {
    return lesson?.short
        || lesson?.translation?.short
        || lesson?.translations?.[0]?.short
        || ''
}

/** Получение описания урока */
const getLessonDescription = (lesson) => {
    return lesson?.description
        || lesson?.translation?.description
        || lesson?.translations?.[0]?.description
        || ''
}

/** Получение slug урока */
const getLessonSlug = (lesson) => {
    return lesson?.slug
        || lesson?.translation?.slug
        || lesson?.translations?.[0]?.slug
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
        || ''
}

/** Получение заголовка модуля */
const getModuleTitle = (lesson) => {
    return getNestedTitle(lesson?.module)
}

/** Получение slug модуля */
const getModuleSlug = (lesson) => {
    return lesson?.module?.slug
        || lesson?.module?.translation?.slug
        || lesson?.module?.translations?.[0]?.slug
        || ''
}

/** Получение заголовка курса */
const getCourseTitle = (lesson) => {
    return getNestedTitle(lesson?.module?.course || lesson?.course)
}

/** Получение slug курса */
const getCourseSlug = (lesson) => {
    const course = lesson?.module?.course || lesson?.course

    return course?.slug
        || course?.translation?.slug
        || course?.translations?.[0]?.slug
        || ''
}

/** Получение текста хештегов */
const getHashtagsText = (lesson) => {
    const hashtags = Array.isArray(lesson?.hashtags) ? lesson.hashtags : []

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
const sortLessons = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        moduleAsc: byNumberAsc('school_module_id'),
        moduleDesc: byNumberDesc('school_module_id'),

        titleAsc: (a, b) =>
            normalize(getLessonTitle(a)).localeCompare(normalize(getLessonTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getLessonTitle(b)).localeCompare(normalize(getLessonTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getLessonSlug(a)).localeCompare(normalize(getLessonSlug(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getLessonSlug(b)).localeCompare(normalize(getLessonSlug(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        availabilityAsc: byStringAsc('availability'),
        availabilityDesc: byStringDesc('availability'),

        accessTypeAsc: byStringAsc('access_type'),
        accessTypeDesc: byStringDesc('access_type'),

        contentTypeAsc: byStringAsc('content_type'),
        contentTypeDesc: byStringDesc('content_type'),

        contentIdAsc: byNumberAsc('content_id'),
        contentIdDesc: byNumberDesc('content_id'),

        difficultyAsc: byNumberAsc('difficulty'),
        difficultyDesc: byNumberDesc('difficulty'),

        durationAsc: byNumberAsc('duration'),
        durationDesc: byNumberDesc('duration'),

        previewValueAsc: byNumberAsc('preview_value'),
        previewValueDesc: byNumberDesc('preview_value'),

        popularityAsc: byNumberAsc('popularity'),
        popularityDesc: byNumberDesc('popularity'),

        ratingCountAsc: byNumberAsc('rating_count'),
        ratingCountDesc: byNumberDesc('rating_count'),

        ratingAvgAsc: byNumberAsc('rating_avg'),
        ratingAvgDesc: byNumberDesc('rating_avg'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes'),
        likesDesc: byNumberDesc('likes'),

        likesCountAsc: byNumberAsc('likes_count'),
        likesCountDesc: byNumberDesc('likes_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        hashtagsAsc: byNumberAsc('hashtags_count'),
        hashtagsDesc: byNumberDesc('hashtags_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

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
const filteredLessons = computed(() => {
    let filtered = localLessons.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortLessons(filtered)
    }

    filtered = filtered.filter((lesson) => {
        const title = normalize(getLessonTitle(lesson))
        const subtitle = normalize(getLessonSubtitle(lesson))
        const slug = normalize(getLessonSlug(lesson))
        const short = normalize(getLessonShort(lesson))
        const description = normalize(getLessonDescription(lesson))

        const moduleTitle = normalize(getModuleTitle(lesson))
        const moduleSlug = normalize(getModuleSlug(lesson))

        const courseTitle = normalize(getCourseTitle(lesson))
        const courseSlug = normalize(getCourseSlug(lesson))

        const hashtags = normalize(getHashtagsText(lesson))

        const status = normalize(lesson?.status)
        const availability = normalize(lesson?.availability)
        const accessType = normalize(lesson?.access_type)
        const contentType = normalize(lesson?.content_type)

        return title.includes(query)
            || subtitle.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || moduleTitle.includes(query)
            || moduleSlug.includes(query)
            || courseTitle.includes(query)
            || courseSlug.includes(query)
            || hashtags.includes(query)
            || status.includes(query)
            || availability.includes(query)
            || accessType.includes(query)
            || contentType.includes(query)
    })

    return sortLessons(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedLessons = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredLessons.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedLessons = computed(() => {
    return props.useServerProcessing
        ? lessonsList.value
        : paginatedLessons.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const lessonToDeleteId = ref(null)
const lessonToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (lessonOrId, title = null) => {
    if (typeof lessonOrId === 'object') {
        lessonToDeleteId.value = lessonOrId.id
        lessonToDeleteTitle.value = title || getLessonTitle(lessonOrId)
    } else {
        lessonToDeleteId.value = lessonOrId
        lessonToDeleteTitle.value = title || `ID: ${lessonOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    lessonToDeleteId.value = null
    lessonToDeleteTitle.value = ''
}

/** Выполнить удаление */
const deleteLesson = () => {
    if (lessonToDeleteId.value === null) return

    const idToDelete = lessonToDeleteId.value
    const titleToDelete = lessonToDeleteTitle.value

    router.delete(route('admin.schoolLessons.destroy', { schoolLesson: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Урок "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Урок: ${titleToDelete || 'ID: ' + idToDelete})`)
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
const patchLesson = (lessonId, payload) => {
    const index = localLessons.value.findIndex(lesson => lesson.id === lessonId)

    if (index !== -1) {
        localLessons.value[index] = {
            ...localLessons.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedLessons = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedLessons.value.map((lesson) => lesson.id)

    if (checked) {
        selectedLessons.value = [...new Set([...selectedLessons.value, ...ids])]
    } else {
        selectedLessons.value = selectedLessons.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectLesson = (id) => {
    const index = selectedLessons.value.indexOf(id)

    if (index > -1) {
        selectedLessons.value.splice(index, 1)
    } else {
        selectedLessons.value.push(id)
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

    router.put(route('admin.actions.schoolLessons.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок уроков успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки уроков:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок уроков.')

            router.reload({
                only: ['lessons'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedLessons.value.length) {
        toast.warning('Выберите уроки для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedLessons.value]

    router.put(route('admin.actions.schoolLessons.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchLesson(id, { activity: newActivity }))
            selectedLessons.value = []
            toast.success('Активность выбранных уроков обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
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
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности */
const toggleActivity = (lesson) => {
    const newActivity = !lesson.activity
    const lessonTitle = getLessonTitle(lesson)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolLessons.updateActivity', {
        schoolLesson: lesson.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLesson(lesson.id, { activity: newActivity })
            lesson.activity = newActivity
            toast.success(`Урок "${lessonTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для урока "${lessonTitle}".`)
        },
    })
}

/** Клонирование урока */
const cloneLesson = (lesson) => {
    router.post(route('admin.actions.schoolLessons.clone', {
        schoolLesson: lesson.id,
    }), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Урок успешно клонирован.'),
        onError: () => toast.error('Ошибка при клонировании урока.'),
    })
}
</script>

<template>
    <AdminLayout :title="t('lessons')">
        <template #header>
            <TitlePage>{{ t('lessons') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolLessons.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addLesson') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolLessonsProcessingMode"
                        :mode="adminSchoolLessonsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="lessonsCount"
                    />
                </div>

                <SearchInput
                    v-if="lessonsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="lessonsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="lessonsCount"
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
                        update-route="admin.settings.updateAdminCountSchoolLessons"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="lessonsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ lessonsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="lessonsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="lessons"
                    />
                </div>

                <LessonTable
                    v-if="viewMode === 'table'"
                    :lessons="displayedLessons"
                    :selected-lessons="selectedLessons"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLesson"
                    @toggle-all="toggleAll"
                    @clone="cloneLesson"
                />

                <LessonCardGrid
                    v-else
                    :lessons="displayedLessons"
                    :selected-lessons="selectedLessons"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLesson"
                    @toggle-all="toggleAll"
                    @clone="cloneLesson"
                />

                <div
                    v-if="lessonsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="lessons"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteLesson"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
