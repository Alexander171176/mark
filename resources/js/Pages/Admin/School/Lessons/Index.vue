<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список уроков школы
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

import BulkActionSelect from '@/Components/Admin/School/Lesson/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/Lesson/Sort/SortSelect.vue'
import LessonTable from '@/Components/Admin/School/Lesson/Table/LessonTable.vue'
import LessonCardGrid from '@/Components/Admin/School/Lesson/View/LessonCardGrid.vue'

// Локализация и Toast уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    lessons: { type: Array, default: () => [] },
    lessonsCount: { type: Number, default: 0 },

    adminSchoolLessonsPerPage: { type: Number, default: 10 },
    adminSchoolLessonsDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем режим отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия уроков
const localLessons = ref([])

// Обновление локального списка уроков
watch(
    () => props.lessons,
    (newVal) => {
        localLessons.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolLessonsPerPage || 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountLessons'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolLessonsDefaultSort || 'idDesc')

// Сохранение параметра сортировки
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortLessons'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)
const lessonToDeleteId = ref(null)
const lessonToDeleteTitle = ref('')

// Открытие модального окна удаления
const confirmDelete = (lessonOrId, title = null) => {
    if (typeof lessonOrId === 'object') {
        lessonToDeleteId.value = lessonOrId.id
        lessonToDeleteTitle.value = title || lessonOrId.title || `ID: ${lessonOrId.id}`
    } else {
        lessonToDeleteId.value = lessonOrId
        lessonToDeleteTitle.value = title || `ID: ${lessonOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    lessonToDeleteId.value = null
    lessonToDeleteTitle.value = ''
}

// Удаление урока
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

// Текущая страница
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация строки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Сортировка уроков
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
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

const sortLessons = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') {
        return list.filter(item => !!item.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter(item => !item.activity)
    }

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        moduleAsc: byNumberAsc('school_module_id'),
        moduleDesc: byNumberDesc('school_module_id'),

        titleAsc: byStringAsc('title'),
        titleDesc: byStringDesc('title'),

        slugAsc: byStringAsc('slug'),
        slugDesc: byStringDesc('slug'),

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

// Отфильтрованные уроки
const filteredLessons = computed(() => {
    let filtered = localLessons.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortLessons(filtered)
    }

    filtered = filtered.filter((lesson) => {
        const title = normalize(lesson?.title)
        const subtitle = normalize(lesson?.subtitle)
        const slug = normalize(lesson?.slug)
        const short = normalize(lesson?.short)
        const description = normalize(lesson?.description)
        const moduleTitle = normalize(lesson?.module?.title)
        const moduleSlug = normalize(lesson?.module?.slug)
        const courseTitle = normalize(lesson?.course?.title)
        const courseSlug = normalize(lesson?.course?.slug)

        return (
            title.includes(q) ||
            subtitle.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            moduleTitle.includes(q) ||
            moduleSlug.includes(q) ||
            courseTitle.includes(q) ||
            courseSlug.includes(q)
        )
    })

    return sortLessons(filtered)
})

// Уроки текущей страницы
const paginatedLessons = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredLessons.value.slice(start, start + per)
})

// Сброс страницы при поиске и изменении лимита
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление урока
const patchLesson = (lessonId, payload) => {
    const index = localLessons.value.findIndex(lesson => lesson.id === lessonId)

    if (index !== -1) {
        localLessons.value[index] = {
            ...localLessons.value[index],
            ...payload,
        }
    }
}

// Выбранные уроки
const selectedLessons = ref([])

// Выбрать / снять выбор со всех уроков
const toggleAll = ({ ids, checked }) => {
    selectedLessons.value = checked ? [...ids] : []
}

// Выбор одного урока
const toggleSelectLesson = (id) => {
    const index = selectedLessons.value.indexOf(id)

    if (index > -1) {
        selectedLessons.value.splice(index, 1)
    } else {
        selectedLessons.value.push(id)
    }
}

// Обновление порядка сортировки
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

// Массовое обновление активности
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

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedLessons.value = paginatedLessons.value.map(lesson => lesson.id)
    } else if (action === 'deselectAll') {
        selectedLessons.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

// Переключение активности урока
const toggleActivity = (lesson) => {
    const newActivity = !lesson.activity
    const lessonTitle = lesson.title || `ID: ${lesson.id}`
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

// Клонирование урока
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolLessons.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addLesson') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="lessonsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="lessonsCount"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <LessonTable
                    v-if="viewMode === 'table'"
                    :lessons="paginatedLessons"
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
                    :lessons="paginatedLessons"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
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
            :onConfirm="deleteLesson"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
