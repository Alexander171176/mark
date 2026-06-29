<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список инструкторов школы
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

import SortSelect from '@/Components/Admin/School/SchoolInstructorProfile/Sort/SortSelect.vue'
import BulkActionSelect from '@/Components/Admin/School/SchoolInstructorProfile/Select/BulkActionSelect.vue'
import InstructorProfileTable from '@/Components/Admin/School/SchoolInstructorProfile/Table/InstructorProfileTable.vue'
import InstuctorCardGrid from '@/Components/Admin/School/SchoolInstructorProfile/View/InstuctorCardGrid.vue'

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

    adminSchoolInstructorsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    instructorProfiles: { type: [Array, Object], default: () => [] },
    instructorProfilesCount: { type: Number, default: 0 },

    adminSchoolInstructorsPerPage: { type: Number, default: 6 },
    adminSchoolInstructorsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_instructors') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_instructors', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список инструкторов:
 * frontend → обычный массив
 * server → instructorProfiles.data
 */
const instructorProfilesList = computed(() => {
    if (Array.isArray(props.instructorProfiles)) {
        return props.instructorProfiles
    }

    if (Array.isArray(props.instructorProfiles?.data)) {
        return props.instructorProfiles.data
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
const localInstructorProfiles = ref([])

watch(
    instructorProfilesList,
    (newVal) => {
        localInstructorProfiles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolInstructorsPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolInstructors'),
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
const sortParam = ref(props.sortParam || props.adminSchoolInstructorsDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolInstructors'),
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

/** Получение имени/заголовка инструктора */
const getInstructorTitle = (instructorProfile) => {
    return instructorProfile?.title
        || instructorProfile?.public_name
        || instructorProfile?.translation?.title
        || instructorProfile?.translations?.[0]?.title
        || instructorProfile?.user?.name
        || `ID: ${instructorProfile?.id}`
}

/** Получение краткого описания инструктора */
const getInstructorShort = (instructorProfile) => {
    return instructorProfile?.short
        || instructorProfile?.translation?.short
        || instructorProfile?.translations?.[0]?.short
        || ''
}

/** Получение биографии инструктора */
const getInstructorBio = (instructorProfile) => {
    return instructorProfile?.bio
        || instructorProfile?.translation?.bio
        || instructorProfile?.translations?.[0]?.bio
        || ''
}

/** Получение slug инструктора */
const getInstructorSlug = (instructorProfile) => {
    return instructorProfile?.slug
        || instructorProfile?.translation?.slug
        || instructorProfile?.translations?.[0]?.slug
        || ''
}

/** Получение имени пользователя */
const getUserName = (instructorProfile) => {
    return instructorProfile?.user?.name || ''
}

/** Получение email пользователя */
const getUserEmail = (instructorProfile) => {
    return instructorProfile?.user?.email || ''
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

/** Получение текста курсов инструктора */
const getCoursesText = (instructorProfile) => {
    const courses = Array.isArray(instructorProfile?.courses)
        ? instructorProfile.courses
        : []

    return courses.map(getNestedTitle).filter(Boolean).join(' ')
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

/**
 * Главный обработчик сортировки.
 * Должен совпадать со scopeSortByParam() модели и SortSelect.vue.
 */
const sortInstructorProfiles = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getInstructorTitle(a)).localeCompare(normalize(getInstructorTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getInstructorTitle(b)).localeCompare(normalize(getInstructorTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getInstructorSlug(a)).localeCompare(normalize(getInstructorSlug(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getInstructorSlug(b)).localeCompare(normalize(getInstructorSlug(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        ratingAvgAsc: byNumberAsc('rating_avg'),
        ratingAvgDesc: byNumberDesc('rating_avg'),

        ratingCountAsc: byNumberAsc('rating_count'),
        ratingCountDesc: byNumberDesc('rating_count'),

        hourlyRateAsc: byNumberAsc('hourly_rate'),
        hourlyRateDesc: byNumberDesc('hourly_rate'),

        experienceAsc: byNumberAsc('experience_years'),
        experienceDesc: byNumberDesc('experience_years'),

        coursesAsc: byNumberAsc('courses_count'),
        coursesDesc: byNumberDesc('courses_count'),

        payoutsAsc: byNumberAsc('payouts_count'),
        payoutsDesc: byNumberDesc('payouts_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        dateAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
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
const filteredInstructorProfiles = computed(() => {
    let filtered = localInstructorProfiles.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortInstructorProfiles(filtered)
    }

    filtered = filtered.filter((instructorProfile) => {
        const title = normalize(getInstructorTitle(instructorProfile))
        const short = normalize(getInstructorShort(instructorProfile))
        const bio = normalize(getInstructorBio(instructorProfile))
        const slug = normalize(getInstructorSlug(instructorProfile))
        const userName = normalize(getUserName(instructorProfile))
        const userEmail = normalize(getUserEmail(instructorProfile))
        const courses = normalize(getCoursesText(instructorProfile))

        return title.includes(query)
            || short.includes(query)
            || bio.includes(query)
            || slug.includes(query)
            || userName.includes(query)
            || userEmail.includes(query)
            || courses.includes(query)
    })

    return sortInstructorProfiles(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedInstructorProfiles = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredInstructorProfiles.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedInstructorProfiles = computed(() => {
    return props.useServerProcessing
        ? instructorProfilesList.value
        : paginatedInstructorProfiles.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const instructorProfileToDeleteId = ref(null)
const instructorProfileToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (instructorProfileOrId, title = null) => {
    if (typeof instructorProfileOrId === 'object') {
        instructorProfileToDeleteId.value = instructorProfileOrId.id
        instructorProfileToDeleteTitle.value = title || getInstructorTitle(instructorProfileOrId)
    } else {
        instructorProfileToDeleteId.value = instructorProfileOrId
        instructorProfileToDeleteTitle.value = title || `ID: ${instructorProfileOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    instructorProfileToDeleteId.value = null
    instructorProfileToDeleteTitle.value = ''
}

/** Выполнить удаление */
const deleteInstructorProfile = () => {
    if (instructorProfileToDeleteId.value === null) return

    const idToDelete = instructorProfileToDeleteId.value
    const titleToDelete = instructorProfileToDeleteTitle.value

    router.delete(route('admin.schoolInstructorProfiles.destroy', {
        schoolInstructorProfile: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Инструктор "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Инструктор: ${titleToDelete || 'ID: ' + idToDelete})`)
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
const patchInstructorProfile = (instructorProfileId, payload) => {
    const index = localInstructorProfiles.value.findIndex(item => item.id === instructorProfileId)

    if (index !== -1) {
        localInstructorProfiles.value[index] = {
            ...localInstructorProfiles.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedInstructorProfiles = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedInstructorProfiles.value.map((item) => item.id)

    if (checked) {
        selectedInstructorProfiles.value = [...new Set([...selectedInstructorProfiles.value, ...ids])]
    } else {
        selectedInstructorProfiles.value = selectedInstructorProfiles.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectInstructorProfile = (id) => {
    const index = selectedInstructorProfiles.value.indexOf(id)

    if (index > -1) {
        selectedInstructorProfiles.value.splice(index, 1)
    } else {
        selectedInstructorProfiles.value.push(id)
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

    router.put(route('admin.actions.schoolInstructorProfiles.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок инструкторов успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки инструкторов:', errors)
            toast.error(errors?.general || errors?.instructorProfiles || 'Не удалось обновить порядок инструкторов.')

            router.reload({
                only: ['instructorProfiles'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedInstructorProfiles.value.length) {
        toast.warning('Выберите инструкторов для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedInstructorProfiles.value]

    router.put(route('admin.actions.schoolInstructorProfiles.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchInstructorProfile(id, { activity: newActivity }))
            selectedInstructorProfiles.value = []
            toast.success('Активность выбранных инструкторов обновлена.')
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
const toggleActivity = (instructorProfile) => {
    const newActivity = !instructorProfile.activity
    const instructorTitle = getInstructorTitle(instructorProfile)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolInstructorProfiles.updateActivity', {
        instructorProfile: instructorProfile.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchInstructorProfile(instructorProfile.id, { activity: newActivity })
            instructorProfile.activity = newActivity
            toast.success(`Инструктор "${instructorTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для инструктора "${instructorTitle}".`)
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('instructors')">
        <template #header>
            <TitlePage>{{ t('instructors') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolInstructorProfiles.create')">
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
                        {{ t('addInstructor') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolInstructorsProcessingMode"
                        :mode="adminSchoolInstructorsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="instructorProfilesCount"
                    />
                </div>

                <SearchInput
                    v-if="instructorProfilesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="instructorProfilesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="instructorProfilesCount"
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
                        update-route="admin.settings.updateAdminCountSchoolInstructors"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="instructorProfilesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ instructorProfilesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="instructorProfilesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredInstructorProfiles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="instructorProfiles"
                    />
                </div>

                <InstructorProfileTable
                    v-if="viewMode === 'table'"
                    :instructor-profiles="displayedInstructorProfiles"
                    :selected-instructor-profiles="selectedInstructorProfiles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectInstructorProfile"
                    @toggle-all="toggleAll"
                />

                <InstuctorCardGrid
                    v-else
                    :instructor-profiles="displayedInstructorProfiles"
                    :selected-instructor-profiles="selectedInstructorProfiles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectInstructorProfile"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <div
                    v-if="instructorProfilesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredInstructorProfiles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="instructorProfiles"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteInstructorProfile"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
