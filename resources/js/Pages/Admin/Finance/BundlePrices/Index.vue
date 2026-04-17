<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Список прайсов бандлов (паттерн как у прайсов курсов)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'

// 👉 Компоненты прайсов бандлов (аналогично coursePrices)
import BulkActionSelect from '@/Components/Admin/BundlePrice/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/BundlePrice/Sort/SortSelect.vue'
import BundlePriceTable from '@/Components/Admin/BundlePrice/Table/BundlePriceTable.vue'
import BundlePriceCardGrid from '@/Components/Admin/BundlePrice/View/BundlePriceCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    prices: Array,
    pricesCount: Number,
    adminCountBundlePrices: Number,
    adminSortBundlePrices: String,
})

/** Вид: таблица или карточки (общий ключ как у курсов) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Кол-во элементов на странице (берём из настроек админки) */
const itemsPerPage = ref(props.adminCountBundlePrices || 10)
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountBundlePrices'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Параметр сортировки (как у курсов) */
const sortParam = ref(props.adminSortBundlePrices || 'idDesc')
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortBundlePrices'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors?.value || 'Ошибка обновления сортировки.'),
        }
    )
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const priceToDeleteId = ref(null)
const priceToDeleteTitle = ref('')

const confirmDelete = (id, title = '') => {
    priceToDeleteId.value = id
    priceToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    priceToDeleteId.value = null
    priceToDeleteTitle.value = ''
}

const deleteBundlePrice = () => {
    if (priceToDeleteId.value === null) return

    const idToDelete = priceToDeleteId.value
    const titleToDelete = priceToDeleteTitle.value

    router.delete(route('admin.bundlePrices.destroy', { bundlePrice: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Прайс "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors?.general ||
                errors?.server ||
                errors?.[Object.keys(errors || {})[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Прайс: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            priceToDeleteId.value = null
            priceToDeleteTitle.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Хелперы */
const toNum = (v) => {
    if (v === null || v === undefined || v === '') return 0
    const n = Number(v)
    return Number.isFinite(n) ? n : 0
}

/** Показ бандлов */
const getBundleTitle = (p) => p?.bundle?.title || ''
const getBundleSlug = (p) => p?.bundle?.slug || ''

/** Показ валюты */
const getCurrencyCode = (p) => p?.currency?.code || ''
const getCurrencyName = (p) => p?.currency?.name || ''

/**
 * Сортировка массива прайсов (по аналогии с курсами)
 * Важно: не трогаем бэк — всё на фронте.
 */
const sortPrices = (prices) => {
    const value = sortParam.value
    const list = prices.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)

    if (value === 'activity') return prices.filter(p => !!p.activity)
    if (value === 'inactive') return prices.filter(p => !p.activity)

    if (value === 'bundle') {
        return list.sort((a, b) => getBundleTitle(a).localeCompare(getBundleTitle(b)))
    }

    if (value === 'currency') {
        return list.sort((a, b) => getCurrencyCode(a).localeCompare(getCurrencyCode(b)))
    }

    if (value === 'price') return list.sort((a, b) => toNum(b.price) - toNum(a.price))
    if (value === 'effective_price') return list.sort((a, b) => toNum(b.effective_price) - toNum(a.effective_price))
    if (value === 'sale_price') return list.sort((a, b) => toNum(b.sale_price) - toNum(a.sale_price))
    if (value === 'compare_at_price') return list.sort((a, b) => toNum(b.compare_at_price) - toNum(a.compare_at_price))
    if (value === 'discount_percent') return list.sort((a, b) => toNum(b.discount_percent) - toNum(a.discount_percent))

    if (value === 'starts_at') {
        return list.sort((a, b) => {
            const at = a.starts_at ? new Date(a.starts_at).getTime() : 0
            const bt = b.starts_at ? new Date(b.starts_at).getTime() : 0
            return bt - at
        })
    }

    if (value === 'ends_at') {
        return list.sort((a, b) => {
            const at = a.ends_at ? new Date(a.ends_at).getTime() : 0
            const bt = b.ends_at ? new Date(b.ends_at).getTime() : 0
            return bt - at
        })
    }

    if (value === 'sort') return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))

    // fallback: строковые поля (если вдруг добавишь новые)
    return list.sort((a, b) => {
        const av = a[value] ?? ''
        const bv = b[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/**
 * Фильтрация + сортировка
 * Поиск: bundle title/slug + currency + id (как у курсов)
 */
const filteredPrices = computed(() => {
    let filtered = props.prices || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter(p => {
            const idStr = String(p.id ?? '')
            const bundleTitle = getBundleTitle(p).toLowerCase()
            const bundleSlug = getBundleSlug(p).toLowerCase()
            const curCode = getCurrencyCode(p).toLowerCase()
            const curName = getCurrencyName(p).toLowerCase()

            return (
                idStr.includes(q) ||
                bundleTitle.includes(q) ||
                bundleSlug.includes(q) ||
                curCode.includes(q) ||
                curName.includes(q)
            )
        })
    }

    return sortPrices(filtered)
})

/** Пагинация */
const paginatedPrices = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredPrices.value.slice(start, start + itemsPerPage.value)
})

/** Общее количество страниц */
const totalPages = computed(() =>
    Math.ceil((filteredPrices.value.length || 0) / itemsPerPage.value)
)

/**
 * Обновление сортировки (drag&drop)
 * Важно: считаем sort с учётом текущей страницы (как у курсов).
 */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.bundlePrices.updateSortBulk'),
        { bundlePrices: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок прайсов успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки прайсов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.bundlePrices ||
                    'Не удалось обновить порядок прайсов.'
                )
                router.reload({ only: ['prices'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedPrices = ref([])

/** Выбор всех (массовые действия) */
const toggleAll = ({ ids, checked }) => {
    if (checked) selectedPrices.value = [...ids]
    else selectedPrices.value = []
}

/** Выбор одной строки */
const toggleSelectPrice = (id) => {
    const idx = selectedPrices.value.indexOf(id)
    if (idx > -1) selectedPrices.value.splice(idx, 1)
    else selectedPrices.value.push(id)
}

/** Массовое включение/выключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedPrices.value.length) {
        toast.warning('Выберите прайсы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.bundlePrices.bulkUpdateActivity'),
        { ids: selectedPrices.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность прайсов массово обновлена')

                const updatedIds = [...selectedPrices.value]
                selectedPrices.value = []

                paginatedPrices.value.forEach(p => {
                    if (updatedIds.includes(p.id)) p.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность прайсов'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDestroy = () => {
    if (!selectedPrices.value.length) {
        toast.warning('Выберите прайсы для удаления')
        return
    }

    router.delete(
        route('admin.actions.bundlePrices.bulkDestroy'),
        {
            data: { ids: selectedPrices.value },
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success('Прайсы удалены')
                selectedPrices.value = []
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.general ||
                    'Не удалось массово удалить прайсы'
                toast.error(msg)
            },
        }
    )
}

/** Выбор действия в селекте массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedPrices.value = paginatedPrices.value.map(p => p.id)
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

/** Переключение активности одного прайса */
const toggleActivity = (price) => {
    const newActivity = !price.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.bundlePrices.updateActivity', { bundlePrice: price.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                price.activity = newActivity
                const label = `${getBundleTitle(price)} / ${getCurrencyCode(price)}`
                toast.success(`Прайс "${label || 'ID: ' + price.id}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности (ID: ${price.id}).`
                )
            },
        }
    )
}

/** Заголовок для удаления (UX) */
const getDeleteTitle = (price) => {
    const bundle = getBundleTitle(price)
    const cur = getCurrencyCode(price)
    const eff = price?.effective_price ? String(price.effective_price) : ''
    return [bundle, cur, eff].filter(Boolean).join(' • ')
}
</script>

<template>
    <AdminLayout :title="t('bundlePrices')">
        <template #header>
            <TitlePage>{{ t('bundlePrices') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить прайс -->
                    <DefaultButton :href="route('admin.bundlePrices.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addBundlePrice') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="pricesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <SearchInput
                    v-if="pricesCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <!-- Счётчик + переключатель вида -->
                <div class="flex items-center justify-end my-2">
                    <div class="flex items-center space-x-3">
                        <CountTable v-if="pricesCount">{{ pricesCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <!-- Таблица -->
                <BundlePriceTable
                    v-if="viewMode === 'table'"
                    :prices="paginatedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="(id) => {
                        const p = paginatedPrices.find(x => x.id === id)
                        confirmDelete(id, p ? getDeleteTitle(p) : '')
                    }"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <BundlePriceCardGrid
                    v-else
                    :prices="paginatedPrices"
                    :selected-prices="selectedPrices"
                    @toggle-activity="toggleActivity"
                    @delete="(id) => {
                        const p = paginatedPrices.find(x => x.id === id)
                        confirmDelete(id, p ? getDeleteTitle(p) : '')
                    }"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPrice"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="pricesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPrices.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => (sortParam = val)"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteBundlePrice"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
