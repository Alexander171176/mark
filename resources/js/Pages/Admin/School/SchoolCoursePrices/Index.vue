<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список цен курсов школы
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolCoursePrice/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCoursePrice/Sort/SortSelect.vue'
import CoursePriceTable from '@/Components/Admin/School/SchoolCoursePrice/Table/CoursePriceTable.vue'
import CoursePriceCardGrid from '@/Components/Admin/School/SchoolCoursePrice/View/CoursePriceCardGrid.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    prices: { type: Array, default: () => [] },
    pricesCount: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    adminSchoolCoursePricesPerPage: { type: Number, default: 10 },
    adminSchoolCoursePricesDefaultSort: { type: String, default: 'idDesc' },

    courses: { type: Array, default: () => [] },
    currencies: { type: Array, default: () => [] },
})

// Режим отображения (таблица / карточки)
const viewMode = ref(localStorage.getItem('admin_view_mode_course_prices') || 'table')

// Сохранение режима отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_course_prices', val)
})

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolCoursePricesPerPage ?? 10)

// Сохранение настройки количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountCoursePrices'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Текущий параметр сортировки
const sortParam = ref(props.adminSchoolCoursePricesDefaultSort ?? 'idDesc')

// Сохранение настройки сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortCoursePrices'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Состояние модального окна удаления
const showConfirmDeleteModal = ref(false)

// Удаляемая цена курса
const priceToDelete = ref(null)

// Открытие окна подтверждения удаления
const confirmDelete = (priceOrId) => {
    if (typeof priceOrId === 'object') {
        priceToDelete.value = priceOrId
    } else {
        priceToDelete.value = paginatedPrices.value.find((price) => price.id === priceOrId) || {
            id: priceOrId,
        }
    }

    showConfirmDeleteModal.value = true
}

// Закрытие окна удаления
const closeModal = () => {
    showConfirmDeleteModal.value = false
    priceToDelete.value = null
}

// Удаление цены курса
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

// Нормализация строки для поиска и сортировки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Преобразование значения в число
const toNum = (value) => {
    if (value === null || value === undefined || value === '') return 0

    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

// Преобразование даты во временную метку
const toTime = (value) => {
    if (!value) return 0

    const time = new Date(value).getTime()

    return Number.isNaN(time) ? 0 : time
}

// Получение названия курса
const getCourseTitle = (price) => {
    return price?.course?.title || price?.course?.translation?.title || `ID: ${price?.school_course_id || ''}`
}

// Получение slug курса
const getCourseSlug = (price) => {
    return price?.course?.slug || ''
}

// Получение кода валюты
const getCurrencyCode = (price) => {
    return price?.currency?.code || ''
}

// Получение названия валюты
const getCurrencyName = (price) => {
    return price?.currency?.name || ''
}

// Сортировка списка цен
const sortPrices = (items) => {
    const list = items.slice()

    switch (sortParam.value) {
        case 'idAsc':
            return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))

        case 'idDesc':
            return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

        case 'sortAsc':
            return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))

        case 'sortDesc':
            return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))

        case 'priceAsc':
            return list.sort((a, b) => toNum(a.price) - toNum(b.price))

        case 'priceDesc':
            return list.sort((a, b) => toNum(b.price) - toNum(a.price))

        case 'salePriceAsc':
            return list.sort((a, b) => toNum(a.sale_price) - toNum(b.sale_price))

        case 'salePriceDesc':
            return list.sort((a, b) => toNum(b.sale_price) - toNum(a.sale_price))

        case 'compareAtPriceAsc':
            return list.sort((a, b) => toNum(a.compare_at_price) - toNum(b.compare_at_price))

        case 'compareAtPriceDesc':
            return list.sort((a, b) => toNum(b.compare_at_price) - toNum(a.compare_at_price))

        case 'effectivePriceAsc':
            return list.sort((a, b) => toNum(a.effective_price) - toNum(b.effective_price))

        case 'effectivePriceDesc':
            return list.sort((a, b) => toNum(b.effective_price) - toNum(a.effective_price))

        case 'discountPercentAsc':
            return list.sort((a, b) => toNum(a.discount_percent) - toNum(b.discount_percent))

        case 'discountPercentDesc':
            return list.sort((a, b) => toNum(b.discount_percent) - toNum(a.discount_percent))

        case 'startsAtAsc':
            return list.sort((a, b) => toTime(a.starts_at) - toTime(b.starts_at))

        case 'startsAtDesc':
            return list.sort((a, b) => toTime(b.starts_at) - toTime(a.starts_at))

        case 'endsAtAsc':
            return list.sort((a, b) => toTime(a.ends_at) - toTime(b.ends_at))

        case 'endsAtDesc':
            return list.sort((a, b) => toTime(b.ends_at) - toTime(a.ends_at))

        case 'courseTitleAsc':
            return list.sort((a, b) => normalize(getCourseTitle(a)).localeCompare(normalize(getCourseTitle(b))))

        case 'courseTitleDesc':
            return list.sort((a, b) => normalize(getCourseTitle(b)).localeCompare(normalize(getCourseTitle(a))))

        case 'currencyCodeAsc':
            return list.sort((a, b) => normalize(getCurrencyCode(a)).localeCompare(normalize(getCurrencyCode(b))))

        case 'currencyCodeDesc':
            return list.sort((a, b) => normalize(getCurrencyCode(b)).localeCompare(normalize(getCurrencyCode(a))))

        case 'activity':
            return list.filter((price) => !!price.activity)

        case 'inactive':
            return list.filter((price) => !price.activity)

        default:
            return list
    }
}

// Фильтрация и поиск по ценам
const filteredPrices = computed(() => {
    let filtered = Array.isArray(props.prices) ? props.prices : []

    if (searchQuery.value) {
        const query = normalize(searchQuery.value)

        filtered = filtered.filter((price) => {
            const values = [
                price.id,
                price.price,
                price.sale_price,
                price.compare_at_price,
                price.effective_price,
                price.discount_percent,

                getCourseTitle(price),
                getCourseSlug(price),

                getCurrencyCode(price),
                getCurrencyName(price),
            ]

            return values.some((value) => normalize(value).includes(query))
        })
    }

    return sortPrices(filtered)
})

// Пагинация цен
const paginatedPrices = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredPrices.value.slice(start, start + itemsPerPage.value)
})

// Общее количество страниц
const totalPages = computed(() => {
    if (!itemsPerPage.value) return 1

    return Math.ceil(filteredPrices.value.length / itemsPerPage.value) || 1
})

// Контроль корректности текущей страницы
watch([filteredPrices, itemsPerPage], () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})

// Выбранные элементы
const selectedPrices = ref([])

// Выбор или снятие выбора всех элементов
const toggleAll = ({ ids, checked }) => {
    selectedPrices.value = checked ? [...ids] : []
}

// Выбор одного элемента
const toggleSelectPrice = (id) => {
    const index = selectedPrices.value.indexOf(id)

    if (index > -1) {
        selectedPrices.value.splice(index, 1)
    } else {
        selectedPrices.value.push(id)
    }
}

// Массовое изменение активности
const bulkToggleActivity = (newActivity) => {
    if (!selectedPrices.value.length) {
        toast.warning('Выберите цены курсов для активации/деактивации')
        return
    }

    router.put(route('admin.actions.schoolCoursePrices.bulkUpdateActivity'), {
        ids: selectedPrices.value,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.success('Активность цен курсов массово обновлена')
            selectedPrices.value = []
        },
        onError: (errors) => {
            const msg = errors?.ids || errors?.activity || errors?.general || 'Не удалось массово обновить активность'
            toast.error(msg)
        },
    })
}

// Массовое удаление
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

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedPrices.value = paginatedPrices.value.map((price) => price.id)
    } else if (action === 'deselectAll') {
        selectedPrices.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDestroy()
    }

    event.target.value = ''
}

// Переключение активности одной цены
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
            price.activity = newActivity
            toast.success(`Цена курса "${getDeleteTitle(price) || 'ID: ' + price.id}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности ID: ${price.id}`)
        },
    })
}

// Обновление порядка сортировки drag&drop
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
            router.reload({ only: ['prices'], preserveScroll: true })
        },
    })
}

// Заголовок для удаления цены курса
const getDeleteTitle = (price) => {
    const course = getCourseTitle(price)
    const currency = getCurrencyCode(price)
    const effectivePrice = price?.effective_price ? String(price.effective_price) : ''

    return [course, currency, effectivePrice].filter(Boolean).join(' • ')
}
</script>

<template>
    <AdminLayout :title="t('coursePrices')">
        <template #header>
            <TitlePage>{{ t('coursePrices') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolCoursePrices.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addCoursePrice') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="pricesCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div
                    v-if="pricesCount"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <CoursePriceTable
                    v-if="viewMode === 'table'"
                    :prices="paginatedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <CoursePriceCardGrid
                    v-else
                    :prices="paginatedPrices"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
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
            :onConfirm="deleteCoursePrice"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
