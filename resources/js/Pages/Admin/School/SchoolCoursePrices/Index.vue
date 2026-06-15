<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список цен курсов школы
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
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolCoursePrice/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCoursePrice/Sort/SortSelect.vue'
import CoursePriceTable from '@/Components/Admin/School/SchoolCoursePrice/Table/CoursePriceTable.vue'
import CoursePriceCardGrid from '@/Components/Admin/School/SchoolCoursePrice/View/CoursePriceCardGrid.vue'

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

    adminSchoolCoursePricesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    prices: { type: [Array, Object], default: () => [] },
    pricesCount: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    adminSchoolCoursePricesPerPage: { type: Number, default: 10 },
    adminSchoolCoursePricesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    courses: { type: Array, default: () => [] },
    currencies: { type: Array, default: () => [] },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения: таблица / карточки */
const viewMode = ref(localStorage.getItem('admin_view_mode_course_prices') || 'table')

/** Сохраняем выбранный режим отображения */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_course_prices', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список цен:
 * frontend → обычный массив
 * server → prices.data
 */
const pricesList = computed(() => {
    if (Array.isArray(props.prices)) {
        return props.prices
    }

    if (Array.isArray(props.prices?.data)) {
        return props.prices.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/**
 * Локальная копия списка.
 * Используется для:
 * - frontend-поиска
 * - frontend-сортировки
 * - frontend-пагинации
 * - моментального обновления UI
 */
const localPrices = ref([])

watch(
    pricesList,
    (newVal) => {
        localPrices.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolCoursePricesPerPage || 10)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountCoursePrices'),
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
const sortParam = ref(
    props.sortParam ||
    props.adminSchoolCoursePricesDefaultSort ||
    'idDesc'
)

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortCoursePrices'),
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
const searchQuery = ref(props.search || props.filters?.search || '')

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

/** Получение названия курса */
const getCourseTitle = (price) => {
    return price?.course?.title
        || price?.course?.translation?.title
        || price?.course?.translations?.[0]?.title
        || `ID: ${price?.school_course_id || ''}`
}

/** Получение slug курса */
const getCourseSlug = (price) => {
    return price?.course?.slug || ''
}

/** Получение кода валюты */
const getCurrencyCode = (price) => {
    return price?.currency?.code || ''
}

/** Получение названия валюты */
const getCurrencyName = (price) => {
    return price?.currency?.name || ''
}

/** Получение символа валюты */
const getCurrencySymbol = (price) => {
    return price?.currency?.symbol || ''
}

/** Получение заголовка для уведомлений и удаления */
const getDeleteTitle = (price) => {
    const course = getCourseTitle(price)
    const currency = getCurrencyCode(price)
    const effectivePrice = price?.effective_price ? String(price.effective_price) : ''

    return [course, currency, effectivePrice].filter(Boolean).join(' • ')
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

/** Сортировка дат ↑ */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка дат ↓ */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Главный обработчик сортировки.
 * Должен совпадать со SchoolCoursePrice::scopeSortByParam() и SortSelect.vue.
 */
const sortPrices = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(price => !!price.activity)
    if (sortParam.value === 'inactive') return list.filter(price => !price.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        courseTitleAsc: (a, b) =>
            normalize(getCourseTitle(a)).localeCompare(normalize(getCourseTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        courseTitleDesc: (a, b) =>
            normalize(getCourseTitle(b)).localeCompare(normalize(getCourseTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        currencyCodeAsc: (a, b) =>
            normalize(getCurrencyCode(a)).localeCompare(normalize(getCurrencyCode(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        currencyCodeDesc: (a, b) =>
            normalize(getCurrencyCode(b)).localeCompare(normalize(getCurrencyCode(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        effectivePriceAsc: byNumberAsc('effective_price'),
        effectivePriceDesc: byNumberDesc('effective_price'),

        priceAsc: byNumberAsc('price'),
        priceDesc: byNumberDesc('price'),

        salePriceAsc: byNumberAsc('sale_price'),
        salePriceDesc: byNumberDesc('sale_price'),

        compareAtPriceAsc: byNumberAsc('compare_at_price'),
        compareAtPriceDesc: byNumberDesc('compare_at_price'),

        discountPercentAsc: byNumberAsc('discount_percent'),
        discountPercentDesc: byNumberDesc('discount_percent'),

        startsAtAsc: byDateAsc('starts_at'),
        startsAtDesc: byDateDesc('starts_at'),

        endsAtAsc: byDateAsc('ends_at'),
        endsAtDesc: byDateDesc('ends_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
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
const filteredPrices = computed(() => {
    let filtered = localPrices.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortPrices(filtered)
    }

    filtered = filtered.filter((price) => {
        const values = [
            price?.id,
            price?.price,
            price?.sale_price,
            price?.compare_at_price,
            price?.effective_price,
            price?.discount_amount,
            price?.discount_percent,

            getCourseTitle(price),
            getCourseSlug(price),

            getCurrencyCode(price),
            getCurrencyName(price),
            getCurrencySymbol(price),
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortPrices(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedPrices = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredPrices.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedPrices = computed(() => {
    return props.useServerProcessing
        ? pricesList.value
        : paginatedPrices.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)

/** Удаляемая цена курса */
const priceToDelete = ref(null)

/** Открытие окна подтверждения удаления */
const confirmDelete = (priceOrId) => {
    if (typeof priceOrId === 'object') {
        priceToDelete.value = priceOrId
    } else {
        priceToDelete.value = displayedPrices.value.find(price => price.id === priceOrId) || {
            id: priceOrId,
        }
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    priceToDelete.value = null
}

/** Удаление цены курса */
const deleteCoursePrice = () => {
    if (!priceToDelete.value?.id) return

    const idToDelete = priceToDelete.value.id
    const titleToDelete = getDeleteTitle(priceToDelete.value)

    router.delete(route('admin.schoolCoursePrices.destroy', {
        schoolCoursePrice: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Цена курса "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            const errorMsg = errors?.general || errors?.[firstKey] || 'Ошибка при удалении цены курса.'

            toast.error(`${errorMsg} ID: ${idToDelete}`)
        },

        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

/** Обновление записи локально без полной перезагрузки страницы */
const patchPrice = (priceId, payload) => {
    const index = localPrices.value.findIndex(price => price.id === priceId)

    if (index !== -1) {
        localPrices.value[index] = {
            ...localPrices.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedPrices = ref([])

/** Выбор или снятие выбора всех элементов */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedPrices.value.map(price => price.id)

    if (checked) {
        selectedPrices.value = [...new Set([...selectedPrices.value, ...ids])]
    } else {
        selectedPrices.value = selectedPrices.value.filter(id => !ids.includes(id))
    }
}

/** Выбор одного элемента */
const toggleSelectPrice = (id) => {
    const index = selectedPrices.value.indexOf(id)

    if (index > -1) {
        selectedPrices.value.splice(index, 1)
    } else {
        selectedPrices.value.push(id)
    }
}

/** Массовое изменение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedPrices.value.length) {
        toast.warning('Выберите цены курсов для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedPrices.value]

    router.put(route('admin.actions.schoolCoursePrices.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            idsToUpdate.forEach(id => patchPrice(id, { activity: newActivity }))
            selectedPrices.value = []
            toast.success('Активность цен курсов массово обновлена')
        },

        onError: (errors) => {
            const msg = errors?.ids || errors?.activity || errors?.general || 'Не удалось массово обновить активность'
            toast.error(msg)
        },
    })
}

/** Массовое удаление */
const bulkDestroy = () => {
    if (!selectedPrices.value.length) {
        toast.warning('Выберите цены курсов для удаления')
        return
    }

    router.delete(route('admin.actions.schoolCoursePrices.bulkDestroy'), {
        data: { ids: selectedPrices.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success('Выбранные цены курсов удалены')
            selectedPrices.value = []
        },

        onError: (errors) => {
            const msg = errors?.ids || errors?.general || 'Не удалось массово удалить цены курсов'
            toast.error(msg)
        },
    })
}

/** Обработка массовых действий */
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
    } else if (action === 'delete') {
        bulkDestroy()
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности одной цены */
const toggleActivity = (price) => {
    const newActivity = !price.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolCoursePrices.updateActivity', {
        schoolCoursePrice: price.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            patchPrice(price.id, { activity: newActivity })
            price.activity = newActivity
            toast.success(`Цена курса "${getDeleteTitle(price) || 'ID: ' + price.id}" ${actionText}.`)
        },

        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности ID: ${price.id}`)
        },
    })
}

/** Обновление порядка сортировки drag&drop */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolCoursePrices.updateSortBulk'), {
        items,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => toast.success('Порядок цен курсов успешно обновлён.'),

        onError: (errors) => {
            console.error('Ошибка обновления сортировки цен курсов:', errors)
            toast.error(errors?.items || errors?.general || 'Не удалось обновить порядок цен курсов.')

            router.reload({
                only: ['prices'],
                preserveScroll: true,
            })
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('coursePrices')">
        <template #header>
            <TitlePage>{{ t('coursePrices') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolCoursePrices.create')">
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

                        {{ t('addCoursePrice') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolCoursePricesProcessingMode"
                        :mode="adminSchoolCoursePricesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="pricesCount"
                    />
                </div>

                <SearchInput
                    v-if="pricesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="pricesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="pricesCount"
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
                        update-route="admin.settings.updateAdminCountCoursePrices"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="pricesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>
                        {{ pricesCount }}
                    </CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="pricesCount"
                    class="flex justify-center items-center flex-col md:flex-row mb-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="prices"
                    />
                </div>

                <CoursePriceTable
                    v-if="viewMode === 'table'"
                    :prices="displayedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <CoursePriceCardGrid
                    v-else
                    :prices="displayedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="pricesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="prices"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteCoursePrice"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
