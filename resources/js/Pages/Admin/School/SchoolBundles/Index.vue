<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список наборов курсов школы
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

import BulkActionSelect from '@/Components/Admin/School/SchoolBundle/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolBundle/Sort/SortSelect.vue'
import BundleTable from '@/Components/Admin/School/SchoolBundle/Table/BundleTable.vue'
import BundleCardGrid from '@/Components/Admin/School/SchoolBundle/View/BundleCardGrid.vue'

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

    adminSchoolBundlesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    bundles: { type: [Array, Object], default: () => [] },
    bundlesCount: { type: Number, default: 0 },

    adminSchoolBundlesPerPage: { type: Number, default: 6 },
    adminSchoolBundlesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_bundles') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_bundles', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список наборов:
 * frontend → обычный массив
 * server → bundles.data
 */
const bundlesList = computed(() => {
    if (Array.isArray(props.bundles)) {
        return props.bundles
    }

    if (Array.isArray(props.bundles?.data)) {
        return props.bundles.data
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
const localBundles = ref([])

watch(
    bundlesList,
    (newVal) => {
        localBundles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolBundlesPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolBundles'),
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
const sortParam = ref(props.sortParam || props.adminSchoolBundlesDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolBundles'),
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

/** Получение заголовка набора */
const getBundleTitle = (bundle) => {
    return bundle?.title
        || bundle?.translation?.title
        || bundle?.translations?.[0]?.title
        || `ID: ${bundle?.id}`
}

/** Получение подзаголовка набора */
const getBundleSubtitle = (bundle) => {
    return bundle?.subtitle
        || bundle?.translation?.subtitle
        || bundle?.translations?.[0]?.subtitle
        || ''
}

/** Получение краткого описания набора */
const getBundleShort = (bundle) => {
    return bundle?.short
        || bundle?.translation?.short
        || bundle?.translations?.[0]?.short
        || ''
}

/** Получение описания набора */
const getBundleDescription = (bundle) => {
    return bundle?.description
        || bundle?.translation?.description
        || bundle?.translations?.[0]?.description
        || ''
}

/** Получение slug набора */
const getBundleSlug = (bundle) => {
    return bundle?.slug
        || bundle?.translation?.slug
        || bundle?.translations?.[0]?.slug
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

/** Получение текста курсов внутри набора */
const getCoursesText = (bundle) => {
    const courses = Array.isArray(bundle?.courses) ? bundle.courses : []

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
const sortBundles = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getBundleTitle(a)).localeCompare(normalize(getBundleTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getBundleTitle(b)).localeCompare(normalize(getBundleTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getBundleSlug(a)).localeCompare(normalize(getBundleSlug(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getBundleSlug(b)).localeCompare(normalize(getBundleSlug(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes'),
        likesDesc: byNumberDesc('likes'),

        coursesAsc: byNumberAsc('courses_count'),
        coursesDesc: byNumberDesc('courses_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        pricesAsc: byNumberAsc('prices_count'),
        pricesDesc: byNumberDesc('prices_count'),

        orderItemsAsc: byNumberAsc('order_items_count'),
        orderItemsDesc: byNumberDesc('order_items_count'),

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
const filteredBundles = computed(() => {
    let filtered = localBundles.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortBundles(filtered)
    }

    filtered = filtered.filter((bundle) => {
        const title = normalize(getBundleTitle(bundle))
        const subtitle = normalize(getBundleSubtitle(bundle))
        const slug = normalize(getBundleSlug(bundle))
        const short = normalize(getBundleShort(bundle))
        const description = normalize(getBundleDescription(bundle))
        const courses = normalize(getCoursesText(bundle))

        return title.includes(query)
            || subtitle.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || courses.includes(query)
    })

    return sortBundles(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedBundles = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredBundles.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedBundles = computed(() => {
    return props.useServerProcessing
        ? bundlesList.value
        : paginatedBundles.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const bundleToDeleteId = ref(null)
const bundleToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (bundleOrId, title = null) => {
    if (typeof bundleOrId === 'object') {
        bundleToDeleteId.value = bundleOrId.id
        bundleToDeleteTitle.value = title || getBundleTitle(bundleOrId)
    } else {
        bundleToDeleteId.value = bundleOrId
        bundleToDeleteTitle.value = title || `ID: ${bundleOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    bundleToDeleteId.value = null
    bundleToDeleteTitle.value = ''
}

/** Выполнить удаление */
const deleteBundle = () => {
    if (bundleToDeleteId.value === null) return

    const idToDelete = bundleToDeleteId.value
    const titleToDelete = bundleToDeleteTitle.value

    router.delete(route('admin.schoolBundles.destroy', {
        schoolBundle: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Набор курсов "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Набор: ${titleToDelete || 'ID: ' + idToDelete})`)
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
const patchBundle = (bundleId, payload) => {
    const index = localBundles.value.findIndex(bundle => bundle.id === bundleId)

    if (index !== -1) {
        localBundles.value[index] = {
            ...localBundles.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedBundles = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedBundles.value.map((bundle) => bundle.id)

    if (checked) {
        selectedBundles.value = [...new Set([...selectedBundles.value, ...ids])]
    } else {
        selectedBundles.value = selectedBundles.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectBundle = (id) => {
    const index = selectedBundles.value.indexOf(id)

    if (index > -1) {
        selectedBundles.value.splice(index, 1)
    } else {
        selectedBundles.value.push(id)
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

    router.put(route('admin.actions.schoolBundles.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            toast.success('Порядок наборов курсов успешно обновлён.')
        },

        onError: (errors) => {
            console.error('Ошибка обновления сортировки наборов курсов:', errors)

            toast.error(
                errors?.message ||
                errors?.general ||
                'Не удалось обновить порядок наборов курсов.'
            )

            router.reload({
                only: ['bundles'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedBundles.value.length) {
        toast.warning('Выберите наборы курсов для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedBundles.value]

    router.put(route('admin.actions.schoolBundles.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            idsToUpdate.forEach(id => patchBundle(id, { activity: newActivity }))
            selectedBundles.value = []
            toast.success('Активность выбранных наборов курсов обновлена.')
        },

        onError: (errors) => {
            toast.error(
                errors?.ids ||
                errors?.activity ||
                errors?.general ||
                'Ошибка массового обновления активности.'
            )
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
const toggleActivity = (bundle) => {
    const newActivity = !bundle.activity
    const bundleTitle = getBundleTitle(bundle)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolBundles.updateActivity', {
        schoolBundle: bundle.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            patchBundle(bundle.id, { activity: newActivity })
            bundle.activity = newActivity

            toast.success(`Набор курсов "${bundleTitle}" ${actionText}.`)
        },

        onError: (errors) => {
            toast.error(
                errors?.activity ||
                errors?.general ||
                `Ошибка изменения активности для набора "${bundleTitle}".`
            )
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('bundles')">
        <template #header>
            <TitlePage>{{ t('bundles') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolBundles.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addBundle') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolBundlesProcessingMode"
                        :mode="adminSchoolBundlesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="bundlesCount"
                    />
                </div>

                <SearchInput
                    v-if="bundlesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="bundlesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="bundlesCount"
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
                        update-route="admin.settings.updateAdminCountSchoolBundles"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="bundlesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ bundlesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="bundles"
                    />
                </div>

                <BundleTable
                    v-if="viewMode === 'table'"
                    :bundles="displayedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <BundleCardGrid
                    v-else
                    :bundles="displayedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="bundles"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteBundle"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
