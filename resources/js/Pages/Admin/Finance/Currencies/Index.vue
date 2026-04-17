<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список валют (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import CurrencyTable from '@/Components/Admin/Currency/Table/CurrencyTable.vue'
import CurrencyCardGrid from '@/Components/Admin/Currency/View/CurrencyCardGrid.vue'
import BulkActionSelect from '@/Components/Admin/Currency/Select/BulkActionSelect.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SortSelect from '@/Components/Admin/Currency/Sort/SortSelect.vue'
import RefreshRatesButton from '@/Components/Admin/Currency/Buttons/RefreshRatesButton.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currencies: { type: Array, default: () => [] },
    currenciesCount: { type: Number, default: 0 },
    adminCountCurrencies: { type: Number, default: 10 },
    adminSortCurrencies: { type: String, default: 'idDesc' },
})

/** Вид: таблица или карточки (общий ключ) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountCurrencies || 10)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountCurrencies'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortCurrencies || 'idDesc')
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortCurrencies'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const currencyToDeleteId = ref(null)
const currencyToDeleteName = ref('')

const confirmDelete = (id, name) => {
    currencyToDeleteId.value = id
    currencyToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    currencyToDeleteId.value = null
    currencyToDeleteName.value = ''
}

const deleteCurrency = () => {
    if (currencyToDeleteId.value === null) return

    const idToDelete = currencyToDeleteId.value
    const nameToDelete = currencyToDeleteName.value

    router.delete(route('admin.currencies.destroy', { currency: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Валюта "${nameToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors?.general ||
                errors?.[Object.keys(errors || {})[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Валюта: ${nameToDelete || 'ID: ' + idToDelete})`)
            console.error('Ошибка удаления:', errors)
        },
        onFinish: () => {
            currencyToDeleteId.value = null
            currencyToDeleteName.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива валют (как у курсов) */
const sortCurrencies = (currencies) => {
    const value = sortParam.value
    const list = currencies.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)

    if (value === 'activity') return currencies.filter(c => c.activity)
    if (value === 'inactive') return currencies.filter(c => !c.activity)

    // по умолчанию: сортировка по ключу
    return list.sort((a, b) => {
        const av = a?.[value] ?? ''
        const bv = b?.[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка (поиск по name/code) */
const filteredCurrencies = computed(() => {
    let filtered = props.currencies || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(c =>
            (c.name || '').toLowerCase().includes(q) ||
            (c.code || '').toLowerCase().includes(q)
        )
    }

    return sortCurrencies(filtered)
})

/** Пагинация */
const totalPages = computed(() =>
    Math.ceil((filteredCurrencies.value.length || 0) / (itemsPerPage.value || 1))
)

const paginatedCurrencies = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredCurrencies.value.slice(start, start + itemsPerPage.value)
})

watch(searchQuery, () => { currentPage.value = 1 })

/** Обновление сортировки (drag&drop) — как у курсов */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.currencies.updateSortBulk'),
        { currencies: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок валют успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки валют:', errors)
                toast.error(
                    errors?.general ||
                    errors?.currencies ||
                    'Не удалось обновить порядок валют.'
                )
                router.reload({ only: ['currencies'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия — как у курсов (без “умных” merge) */
const selectedCurrencies = ref([])

const toggleAll = ({ ids, checked }) => {
    if (checked) selectedCurrencies.value = [...ids]
    else selectedCurrencies.value = []
}

const toggleSelectCurrency = (id) => {
    const idx = selectedCurrencies.value.indexOf(id)
    if (idx > -1) selectedCurrencies.value.splice(idx, 1)
    else selectedCurrencies.value.push(id)
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedCurrencies.value.length) {
        toast.warning('Выберите валюты для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.currencies.bulkUpdateActivity'),
        { ids: selectedCurrencies.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность валют массово обновлена')
                const updatedIds = [...selectedCurrencies.value]
                selectedCurrencies.value = []

                // локально обновим текущую страницу (как у курсов)
                paginatedCurrencies.value.forEach(c => {
                    if (updatedIds.includes(c.id)) c.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность валют'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = () => {
    if (!selectedCurrencies.value.length) {
        toast.warning('Выберите хотя бы одну валюту для удаления.')
        return
    }
    if (!confirm('Вы уверены, что хотите удалить выбранные валюты?')) return

    router.delete(route('admin.actions.currencies.bulkDestroy'), {
        data: { ids: selectedCurrencies.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedCurrencies.value = []
            toast.success('Массовое удаление валют успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const key = Object.keys(errors || {})[0]
            toast.error(errors?.[key] || 'Произошла ошибка при удалении валют.')
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') selectedCurrencies.value = paginatedCurrencies.value.map(c => c.id)
    else if (action === 'deselectAll') selectedCurrencies.value = []
    else if (action === 'activate') bulkToggleActivity(true)
    else if (action === 'deactivate') bulkToggleActivity(false)
    else if (action === 'delete') bulkDelete()

    event.target.value = ''
}

/** Переключение активности одной валюты — как у курсов */
const toggleActivity = (currency) => {
    const newActivity = !currency.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.currencies.updateActivity', { currency: currency.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                currency.activity = newActivity
                toast.success(`Валюта "${currency.name}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для "${currency.name}".`
                )
            },
        }
    )
}

/** Базовая валюта */
const currentBase = computed(() => (props.currencies || []).find(c => c.is_default) || null)

/** refreshRates */
const refreshFromProvider = () => {
    if (!currentBase.value) {
        toast.error('Не выбрана основная валюта.')
        return
    }
    if (!confirm(`Обновить курсы из провайдера для базы ${currentBase.value.code}?`)) return

    router.post(
        route('admin.actions.currencies.refreshRates', { currency: currentBase.value.id }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => toast.success('Курсы обновлены из провайдера.'),
            onError: (errors) => toast.error(errors?.general || 'Ошибка обновления курсов из провайдера.'),
        }
    )
}

/** set default */
const setDefault = (currency) => {
    if (currency.is_default) return

    router.put(
        route('admin.actions.currencies.setDefault', { currency: currency.id }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => toast.success(`Назначена основной: ${currency.name}`),
            onError: (errors) => toast.error(errors?.general || 'Не удалось назначить основную валюту.'),
        }
    )
}

/** save rate */
const saveRate = ({ id, value }) => {
    router.post(
        route('admin.actions.currencies.updateRate', { currency: id }),
        { rate: value, provider: 'manual' },
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => toast.success('Курс обновлён'),
            onError: (errors) => toast.error(errors?.general || errors?.rate || 'Ошибка обновления курса'),
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('currencies')">
        <template #header>
            <TitlePage>{{ t('currencies') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.currencies.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addCurrency') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="currenciesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <div class="flex justify-center items-center mb-2">
                    <RefreshRatesButton
                        :title="`${t('currencyRefreshRates')} (${currentBase?.code || '—'})`"
                        :disabled="!currentBase"
                        @click="refreshFromProvider"
                    />
                </div>

                <SearchInput
                    v-if="currenciesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div class="flex items-center justify-between my-2" v-if="currenciesCount">
                    <CountTable>{{ currenciesCount }}</CountTable>
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Таблица -->
                <CurrencyTable
                    v-if="viewMode === 'table'"
                    :currencies="paginatedCurrencies"
                    :selected-currencies="selectedCurrencies"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCurrency"
                    @toggle-all="toggleAll"
                    @set-default="setDefault"
                    @save-rate="saveRate"
                />

                <!-- Карточки -->
                <CurrencyCardGrid
                    v-else
                    :currencies="paginatedCurrencies"
                    :selected-currencies="selectedCurrencies"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCurrency"
                    @toggle-all="toggleAll"
                    @set-default="setDefault"
                    @save-rate="saveRate"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="currenciesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCurrencies.length"
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
            :onConfirm="deleteCurrency"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
