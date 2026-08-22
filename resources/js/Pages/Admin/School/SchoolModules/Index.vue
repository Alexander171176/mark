<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список модулей школы.
 *
 * - frontend | server | auto;
 * - frontend search / sort / pagination;
 * - server search / sort / pagination;
 * - единый Admin SharedResource contract;
 * - translation содержит только выбранную locale.
 */

import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

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

import BulkActionSelect from '@/Components/Admin/School/SchoolModule/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolModule/Sort/SortSelect.vue'
import ModuleTable from '@/Components/Admin/School/SchoolModule/Table/ModuleTable.vue'
import ModuleCardGrid from '@/Components/Admin/School/SchoolModule/View/ModuleCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolModulesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    modules: { type: [Array, Object], default: () => [] },
    modulesCount: { type: Number, default: 0 },

    adminSchoolModulesPerPage: { type: Number, default: 6 },
    adminSchoolModulesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem('admin_view_mode_modules') || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_modules',
        value
    )
})

/* ==========================================================
 * SOURCE DATA
 * ========================================================== */

/**
 * frontend:
 * modules = array
 *
 * server:
 * modules.data = array
 */
const modulesList = computed(() => {
    if (Array.isArray(props.modules)) {
        return props.modules
    }

    if (Array.isArray(props.modules?.data)) {
        return props.modules.data
    }

    return []
})

/**
 * Локальная копия нужна для:
 *
 * - frontend search;
 * - frontend sort;
 * - frontend pagination;
 * - drag&drop;
 * - моментального обновления UI.
 */
const localModules = ref([])

watch(
    modulesList,
    (modules) => {
        localModules.value = JSON.parse(
            JSON.stringify(modules || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ==========================================================
 * PAGINATION / SORT
 * ========================================================== */

const itemsPerPage = ref(
    props.adminSchoolModulesPerPage || 6
)

const sortParam = ref(
    props.sortParam
    || props.adminSchoolModulesDefaultSort
    || 'idDesc'
)

const searchQuery = ref(
    props.search || ''
)

const currentPage = ref(1)

/**
 * Текущая server-page.
 *
 * Нужна также корректному вычислению
 * sort при drag&drop на server pagination.
 */
const serverCurrentPage = computed(() => {
    return Number(
        props.modules?.meta?.current_page
        ?? props.modules?.current_page
        ?? 1
    ) || 1
})

const activeCurrentPage = computed(() => {
    return props.useServerProcessing
        ? serverCurrentPage.value
        : currentPage.value
})

/**
 * Изменение количества элементов.
 *
 * Для frontend сохраняем настройку здесь.
 *
 * ServerItemsPerPageSelect самостоятельно
 * работает через update-route.
 */
watch(itemsPerPage, (newValue) => {
    if (props.useServerProcessing) {
        return
    }

    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminCountSchoolModules'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newValue} элементов на странице.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления количества элементов.'
                )
            },
        }
    )
})

/**
 * Сохраняем сортировку.
 *
 * frontend:
 * новый порядок вычисляется локально.
 *
 * server:
 * после сохранения выполняем GET
 * с новым sort.
 */
watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolModules'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    const query = Object.fromEntries(
                        new URLSearchParams(
                            window.location.search
                        )
                    )

                    router.get(
                        window.location.pathname,
                        {
                            ...query,
                            sort: newValue || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info(
                    'Сортировка успешно изменена'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
})

/* ==========================================================
 * RESOURCE CONTRACT
 * ========================================================== */

/**
 * Новый Admin Index contract:
 *
 * module.translation
 *
 * translations на Index больше нет.
 */
const getModuleTitle = (module) => {
    return module?.translation?.title
        || `ID: ${module?.id}`
}

const getModuleSubtitle = (module) => {
    return module?.translation?.subtitle || ''
}

const getModuleShort = (module) => {
    return module?.translation?.short || ''
}

const getModuleDescription = (module) => {
    return module?.translation?.description || ''
}

/**
 * slug — поле основной таблицы,
 * а не translation.
 */
const getModuleSlug = (module) => {
    return module?.slug || ''
}

/**
 * Course SharedResource использует
 * тот же новый contract:
 *
 * course.translation.title
 */
const getCourseTitle = (module) => {
    return module?.course?.translation?.title
        || `ID: ${module?.school_course_id || '-'}`
}

const getCourseSlug = (module) => {
    return module?.course?.slug || ''
}

/* ==========================================================
 * HELPERS
 * ========================================================== */

const normalize = (value) => {
    return String(value ?? '')
        .trim()
        .toLocaleLowerCase()
}

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const safeDate = (value) => {
    const time = new Date(
        value || 0
    ).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

const compareText = (a, b) => {
    return normalize(a).localeCompare(
        normalize(b),
        props.currentLocale || undefined,
        {
            sensitivity: 'base',
        }
    )
}

/* ==========================================================
 * FRONTEND SORT
 * ========================================================== */

const byNumberAsc = (field) => {
    return (a, b) =>
        safeNumber(a?.[field])
        - safeNumber(b?.[field])
        || safeNumber(a?.id)
        - safeNumber(b?.id)
}

const byNumberDesc = (field) => {
    return (a, b) =>
        safeNumber(b?.[field])
        - safeNumber(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

const byStringAsc = (field) => {
    return (a, b) =>
        compareText(
            a?.[field],
            b?.[field]
        )
        || safeNumber(a?.id)
        - safeNumber(b?.id)
}

const byStringDesc = (field) => {
    return (a, b) =>
        compareText(
            b?.[field],
            a?.[field]
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/**
 * Должен совпадать:
 *
 * SchoolModule::scopeSortByParam()
 * SortSelect.vue
 * frontend sort.
 */
const sortModules = (items) => {
    const list = [
        ...(items || []),
    ]

    if (sortParam.value === 'activity') {
        return list.filter(
            module => !!module.activity
        )
    }

    if (sortParam.value === 'inactive') {
        return list.filter(
            module => !module.activity
        )
    }

    const sortMap = {
        idAsc:
            byNumberAsc('id'),

        idDesc:
            byNumberDesc('id'),

        sortAsc:
            byNumberAsc('sort'),

        sortDesc:
            byNumberDesc('sort'),

        courseAsc:
            byNumberAsc('school_course_id'),

        courseDesc:
            byNumberDesc('school_course_id'),

        titleAsc: (a, b) =>
            compareText(
                getModuleTitle(a),
                getModuleTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        titleDesc: (a, b) =>
            compareText(
                getModuleTitle(b),
                getModuleTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        slugAsc: (a, b) =>
            compareText(
                getModuleSlug(a),
                getModuleSlug(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        slugDesc: (a, b) =>
            compareText(
                getModuleSlug(b),
                getModuleSlug(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        statusAsc:
            byStringAsc('status'),

        statusDesc:
            byStringDesc('status'),

        availabilityAsc:
            byStringAsc('availability'),

        availabilityDesc:
            byStringDesc('availability'),

        difficultyAsc:
            byNumberAsc('difficulty'),

        difficultyDesc:
            byNumberDesc('difficulty'),

        durationAsc:
            byNumberAsc('duration'),

        durationDesc:
            byNumberDesc('duration'),

        lessonsAsc:
            byNumberAsc('lessons_count'),

        lessonsDesc:
            byNumberDesc('lessons_count'),

        imagesAsc:
            byNumberAsc('images_count'),

        imagesDesc:
            byNumberDesc('images_count'),

        likesAsc:
            byNumberAsc('likes'),

        likesDesc:
            byNumberDesc('likes'),

        likesCountAsc:
            byNumberAsc('likes_count'),

        likesCountDesc:
            byNumberDesc('likes_count'),

        viewsAsc:
            byNumberAsc('views'),

        viewsDesc:
            byNumberDesc('views'),

        popularityAsc:
            byNumberAsc('popularity'),

        popularityDesc:
            byNumberDesc('popularity'),

        ratingCountAsc:
            byNumberAsc('rating_count'),

        ratingCountDesc:
            byNumberDesc('rating_count'),

        ratingAvgAsc:
            byNumberAsc('rating_avg'),

        ratingAvgDesc:
            byNumberDesc('rating_avg'),

        activityAsc:
            byNumberAsc('activity'),

        activityDesc:
            byNumberDesc('activity'),

        publishedAtAsc: (a, b) =>
            safeDate(a?.published_at)
            - safeDate(b?.published_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        publishedAtDesc: (a, b) =>
            safeDate(b?.published_at)
            - safeDate(a?.published_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        dateAsc: (a, b) =>
            safeDate(a?.published_at)
            - safeDate(b?.published_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.published_at)
            - safeDate(a?.published_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at)
            - safeDate(b?.created_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at)
            - safeDate(a?.created_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at)
            - safeDate(b?.updated_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at)
            - safeDate(a?.updated_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),
    }

    const sorter =
        sortMap[sortParam.value]

    return sorter
        ? list.sort(sorter)
        : list
}

/* ==========================================================
 * FRONTEND SEARCH
 * ========================================================== */

const filteredModules = computed(() => {
    let modules =
        localModules.value || []

    /**
     * Server уже выполнил search/sort.
     *
     * Не повторяем frontend-фильтрацию
     * над текущей страницей paginator.
     */
    if (props.useServerProcessing) {
        return modules
    }

    const query =
        normalize(searchQuery.value)

    if (query) {
        modules = modules.filter((module) => {
            const title =
                normalize(
                    getModuleTitle(module)
                )

            const subtitle =
                normalize(
                    getModuleSubtitle(module)
                )

            const slug =
                normalize(
                    getModuleSlug(module)
                )

            const short =
                normalize(
                    getModuleShort(module)
                )

            const description =
                normalize(
                    getModuleDescription(module)
                )

            const courseTitle =
                normalize(
                    getCourseTitle(module)
                )

            const courseSlug =
                normalize(
                    getCourseSlug(module)
                )

            return title.includes(query)
                || subtitle.includes(query)
                || slug.includes(query)
                || short.includes(query)
                || description.includes(query)
                || courseTitle.includes(query)
                || courseSlug.includes(query)
        })
    }

    return sortModules(
        modules
    )
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedModules = computed(() => {
    const perPage =
        Number(itemsPerPage.value) || 6

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredModules.value.slice(
        start,
        start + perPage
    )
})

const displayedModules = computed(() => {
    return props.useServerProcessing
        ? modulesList.value
        : paginatedModules.value
})

watch(
    [
        itemsPerPage,
        searchQuery,
    ],
    () => {
        if (!props.useServerProcessing) {
            currentPage.value = 1
        }
    }
)

/* ==========================================================
 * DELETE
 * ========================================================== */

const showConfirmDeleteModal =
    ref(false)

const moduleToDeleteId =
    ref(null)

const moduleToDeleteTitle =
    ref('')

const confirmDelete = (
    moduleOrId,
    title = null
) => {
    if (
        typeof moduleOrId === 'object'
        && moduleOrId !== null
    ) {
        moduleToDeleteId.value =
            moduleOrId.id

        moduleToDeleteTitle.value =
            title
            || getModuleTitle(
                moduleOrId
            )
    } else {
        moduleToDeleteId.value =
            moduleOrId

        moduleToDeleteTitle.value =
            title
            || `ID: ${moduleOrId}`
    }

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    moduleToDeleteId.value =
        null

    moduleToDeleteTitle.value =
        ''
}

const deleteModule = () => {
    if (
        moduleToDeleteId.value === null
    ) {
        return
    }

    const id =
        moduleToDeleteId.value

    const title =
        moduleToDeleteTitle.value

    router.delete(
        route(
            'admin.schoolModules.destroy',
            {
                schoolModule: id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Модуль "${title || `ID: ${id}`}" удалён.`
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const message =
                    errors?.general
                    || errors?.[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${message} (Модуль: ${title || `ID: ${id}`})`
                )
            },

            onFinish: () => {
                closeModal()
            },
        }
    )
}

/* ==========================================================
 * LOCAL UI PATCH
 * ========================================================== */

const patchModule = (
    moduleId,
    payload
) => {
    const index =
        localModules.value.findIndex(
            module => module.id === moduleId
        )

    if (index === -1) {
        return
    }

    localModules.value[index] = {
        ...localModules.value[index],
        ...payload,
    }
}

/* ==========================================================
 * SELECTION / BULK ACTIONS
 * ========================================================== */

const selectedModules =
    ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedModules.value.map(
            module => module.id
        )

    if (checked) {
        selectedModules.value = [
            ...new Set([
                ...selectedModules.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedModules.value =
        selectedModules.value.filter(
            id => !ids.includes(id)
        )
}

const toggleSelectModule = (id) => {
    const index =
        selectedModules.value.indexOf(id)

    if (index > -1) {
        selectedModules.value.splice(
            index,
            1
        )

        return
    }

    selectedModules.value.push(id)
}

/* ==========================================================
 * DRAG & DROP SORT
 * ========================================================== */

const handleSortOrderUpdate = (
    orderedIds
) => {
    /**
     * В server mode учитываем
     * фактическую страницу paginator,
     * а не frontend currentPage.
     */
    const startSort =
        (
            activeCurrentPage.value - 1
        )
        * Number(
            itemsPerPage.value || 6
        )

    const items =
        orderedIds.map(
            (id, index) => ({
                id,
                sort:
                    startSort
                    + index
                    + 1,
            })
        )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.schoolModules.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Порядок модулей успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки модулей:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.general
                    || 'Не удалось обновить порядок модулей.'
                )

                router.reload({
                    only: ['modules'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * BULK ACTIVITY
 * ========================================================== */

const bulkToggleActivity = (
    newActivity
) => {
    if (
        !selectedModules.value.length
    ) {
        toast.warning(
            'Выберите модули для активации/деактивации.'
        )

        return
    }

    const ids = [
        ...selectedModules.value,
    ]

    router.put(
        route(
            'admin.actions.schoolModules.bulkUpdateActivity'
        ),
        {
            ids,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                ids.forEach((id) => {
                    patchModule(
                        id,
                        {
                            activity: newActivity,
                        }
                    )
                })

                selectedModules.value = []

                toast.success(
                    'Активность выбранных модулей обновлена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности.'
                )
            },
        }
    )
}

const handleBulkAction = (event) => {
    const action =
        event.target.value

    if (action === 'selectAll') {
        toggleAll({
            target: {
                checked: true,
            },
        })
    }

    if (action === 'deselectAll') {
        toggleAll({
            target: {
                checked: false,
            },
        })
    }

    if (action === 'activate') {
        bulkToggleActivity(true)
    }

    if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/* ==========================================================
 * SINGLE ACTIVITY
 * ========================================================== */

const toggleActivity = (module) => {
    const newActivity =
        !module.activity

    const moduleTitle =
        getModuleTitle(module)

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.schoolModules.updateActivity',
            {
                schoolModule:
                module.id,
            }
        ),
        {
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchModule(
                    module.id,
                    {
                        activity: newActivity,
                    }
                )

                module.activity =
                    newActivity

                toast.success(
                    `Модуль "${moduleTitle}" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности для модуля "${moduleTitle}".`
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('modules')">
        <template #header>
            <TitlePage>
                {{ t('modules') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolModules.create')">
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

                        {{ t('addModule') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolModulesProcessingMode"
                        :mode="adminSchoolModulesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="modulesCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="modulesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="modulesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Per page + sort -->
                <div
                    v-if="modulesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolModules"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <!-- Count + bulk + view -->
                <div
                    v-if="modulesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ modulesCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Top pagination -->
                <div
                    v-if="modulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="modules"
                    />
                </div>

                <!-- Table -->
                <ModuleTable
                    v-if="viewMode === 'table'"
                    :modules="displayedModules"
                    :selected-modules="selectedModules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectModule"
                    @toggle-all="toggleAll"
                />

                <!-- Cards -->
                <ModuleCardGrid
                    v-else
                    :modules="displayedModules"
                    :selected-modules="selectedModules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectModule"
                    @toggle-all="toggleAll"
                />

                <!-- Bottom pagination -->
                <div
                    v-if="modulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="modules"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteModule"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
