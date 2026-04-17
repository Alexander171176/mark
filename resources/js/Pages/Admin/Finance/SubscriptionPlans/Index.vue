<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список тарифов подписок (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

import BulkActionSelect from '@/Components/Admin/SubscriptionPlan/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/SubscriptionPlan/Sort/SortSelect.vue'

import SubscriptionPlanTable from '@/Components/Admin/SubscriptionPlan/Table/SubscriptionPlanTable.vue'
import SubscriptionPlanCardGrid from '@/Components/Admin/SubscriptionPlan/View/SubscriptionPlanCardGrid.vue'

// --- Инициализация i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/** Пропсы из контроллера  */
const props = defineProps({
    subscriptionPlans: Array,
    plansCount: Number,
    adminCountSubscriptionPlans: Number,
    adminSortSubscriptionPlans: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountSubscriptionPlans || 10)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountSubscriptionPlans'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortSubscriptionPlans || 'idDesc')
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortSubscriptionPlans'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const planToDeleteId = ref(null)
const planToDeleteTitle = ref('')

const confirmDelete = (id, title) => {
    planToDeleteId.value = id
    planToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    planToDeleteId.value = null
    planToDeleteTitle.value = ''
}

const deletePlan = () => {
    if (planToDeleteId.value === null) return

    const idToDelete = planToDeleteId.value
    const titleToDelete = planToDeleteTitle.value

    router.delete(route('admin.subscriptionPlans.destroy', { subscriptionPlan: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Тариф "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Тариф: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            planToDeleteId.value = null
            planToDeleteTitle.value = ''
        },
    })
}

/** Пагинация + поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива планов */
const sortPlans = (plans) => {
    const value = String(sortParam.value || 'idDesc')
    const list = plans.slice()

    /**
     * Хелпер: безопасно получить число (для price, interval, trial_days, sort и т.д.)
     */
    const num = (v) => {
        const n = Number(v)
        return Number.isFinite(n) ? n : 0
    }

    /**
     * Хелпер: безопасно получить timestamp.
     * null/undefined -> null (чтобы можно было отправлять в конец)
     */
    const time = (v) => {
        if (!v) return null
        const t = new Date(v).getTime()
        return Number.isFinite(t) ? t : null
    }

    /**
     * Универсальная сортировка для дат:
     * - null всегда уходит в конец
     */
    const sortByDate = (field, dir = 'desc') => {
        return list.sort((a, b) => {
            const at = time(a[field])
            const bt = time(b[field])

            if (at === null && bt === null) return 0
            if (at === null) return 1
            if (bt === null) return -1

            return dir === 'asc' ? at - bt : bt - at
        })
    }

    /**
     * Универсальная сортировка для чисел:
     */
    const sortByNumber = (field, dir = 'desc') => {
        return list.sort((a, b) => {
            const av = num(a[field])
            const bv = num(b[field])
            return dir === 'asc' ? av - bv : bv - av
        })
    }

    /**
     * Универсальная сортировка для строк:
     */
    const sortByString = (field, dir = 'asc') => {
        return list.sort((a, b) => {
            const av = String(a[field] ?? '').toLowerCase()
            const bv = String(b[field] ?? '').toLowerCase()
            if (av < bv) return dir === 'asc' ? -1 : 1
            if (av > bv) return dir === 'asc' ? 1 : -1
            return 0
        })
    }

    // --- Фильтры активности (это именно фильтр, не сортировка) ---
    if (value === 'activity') return list.filter(p => !!p.activity)
    if (value === 'inactive') return list.filter(p => !p.activity)

    // --- Базовые id ---
    if (value === 'idAsc') return sortByNumber('id', 'asc')
    if (value === 'idDesc') return sortByNumber('id', 'desc')

    // --- sort (порядок) ---
    if (value === 'sortAsc' || value === 'sort') return sortByNumber('sort', 'asc')   // sort оставил для совместимости
    if (value === 'sortDesc') return sortByNumber('sort', 'desc')

    // --- Даты публикации/создания/обновления/окна доступности ---
    if (value === 'publishedAtDesc' || value === 'published_at') return sortByDate('published_at', 'desc') // published_at оставил для совместимости
    if (value === 'publishedAtAsc') return sortByDate('published_at', 'asc')

    if (value === 'createdAtDesc') return sortByDate('created_at', 'desc')
    if (value === 'createdAtAsc') return sortByDate('created_at', 'asc')

    if (value === 'updatedAtDesc') return sortByDate('updated_at', 'desc')
    if (value === 'updatedAtAsc') return sortByDate('updated_at', 'asc')

    if (value === 'availableFromDesc') return sortByDate('available_from', 'desc')
    if (value === 'availableFromAsc') return sortByDate('available_from', 'asc')

    if (value === 'availableUntilDesc') return sortByDate('available_until', 'desc')
    if (value === 'availableUntilAsc') return sortByDate('available_until', 'asc')

    /**
     * availability:
     * 1) "доступен сейчас" -> вверх
     * 2) затем по available_from (раньше -> выше)
     * null-значения окна -> в конец
     */
    if (value === 'availabilityNowFirst' || value === 'availability') {
        const now = Date.now()

        const isNowAvailable = (p) => {
            // published_at обязателен для витрины? (ты в модели так используешь scopePublicAvailable)
            if (!p.published_at) return false

            const from = time(p.available_from)
            const until = time(p.available_until)

            const okFrom = from === null ? true : from <= now
            const okUntil = until === null ? true : until >= now

            return okFrom && okUntil
        }

        return list.sort((a, b) => {
            const aNow = isNowAvailable(a) ? 1 : 0
            const bNow = isNowAvailable(b) ? 1 : 0
            if (aNow !== bNow) return bNow - aNow

            // если оба одинаково "доступны сейчас" (или нет) — сортируем по available_from asc (null в конец)
            const af = time(a.available_from)
            const bf = time(b.available_from)

            if (af === null && bf === null) return 0
            if (af === null) return 1
            if (bf === null) return -1
            return af - bf
        })
    }

    // --- Числовые поля (цена/интервал/триал/валюта) ---
    if (value === 'priceDesc') return sortByNumber('price', 'desc')
    if (value === 'priceAsc') return sortByNumber('price', 'asc')

    if (value === 'trialDaysDesc' || value === 'trial_days') return sortByNumber('trial_days', 'desc') // trial_days оставил для совместимости
    if (value === 'trialDaysAsc') return sortByNumber('trial_days', 'asc')

    // --- Строковые поля ---
    if (value === 'titleAsc' || value === 'title') return sortByString('title', 'asc') // title оставил для совместимости
    if (value === 'titleDesc') return sortByString('title', 'desc')

    if (value === 'billingPeriodAsc' || value === 'billing_period') return sortByString('billing_period', 'asc')
    if (value === 'billingPeriodDesc') return sortByString('billing_period', 'desc')

    // fallback: если прилетело неизвестное значение — пробуем как строку asc
    return sortByString(value, 'asc')
}

/** Фильтрация + сортировка (поиск по title/slug) */
const filteredPlans = computed(() => {
    let filtered = props.subscriptionPlans || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(p =>
            (p.title || '').toLowerCase().includes(q) ||
            (p.slug || '').toLowerCase().includes(q)
        )
    }

    return sortPlans(filtered)
})

/** Пагинация */
const paginatedPlans = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredPlans.value.slice(start, start + itemsPerPage.value)
})

const totalPages = computed(() =>
    Math.ceil((filteredPlans.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.subscriptionPlans.updateSortBulk'),
        { subscriptionPlans: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок тарифов успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки тарифов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.subscriptionPlans ||
                    'Не удалось обновить порядок тарифов.'
                )
                router.reload({ only: ['subscriptionPlans'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedPlans = ref([])

const toggleAll = ({ ids, checked }) => {
    selectedPlans.value = checked ? [...ids] : []
}

const toggleSelectPlan = (id) => {
    const idx = selectedPlans.value.indexOf(id)
    if (idx > -1) selectedPlans.value.splice(idx, 1)
    else selectedPlans.value.push(id)
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedPlans.value.length) {
        toast.warning('Выберите тарифы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.subscriptionPlans.bulkUpdateActivity'),
        { ids: selectedPlans.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность тарифов массово обновлена')
                const updatedIds = [...selectedPlans.value]
                selectedPlans.value = []

                paginatedPlans.value.forEach(p => {
                    if (updatedIds.includes(p.id)) p.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность тарифов'
                toast.error(msg)
            },
        }
    )
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedPlans.value = paginatedPlans.value.map(p => p.id)
    } else if (action === 'deselectAll') {
        selectedPlans.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/** Переключение активности одного тарифа */
const toggleActivity = (plan) => {
    const newActivity = !plan.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.subscriptionPlans.updateActivity', { subscriptionPlan: plan.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                plan.activity = newActivity
                toast.success(`Тариф "${plan.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для тарифа "${plan.title}".`
                )
            },
        }
    )
}

/** Локали */
const localeLink = (locale) => route('admin.subscriptionPlans.index', { locale })
</script>

<template>
    <AdminLayout :title="t('subscriptionPlans')">
        <template #header>
            <TitlePage>{{ t('subscriptionPlans') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить тариф -->
                    <DefaultButton :href="route('admin.subscriptionPlans.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addSubscriptionPlan') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="plansCount"
                        @change="handleBulkAction"
                    />
                </div>

                <!-- Локали + счётчик -->
                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            {{ t('localization') }}:
                        </span>

                        <template v-for="locale in availableLocales" :key="locale">
                            <Link
                                :href="localeLink(locale)"
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-sm',
                                    currentLocale === locale
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ locale.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3">
                        <CountTable v-if="plansCount">
                            {{ plansCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="plansCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица -->
                <SubscriptionPlanTable
                    v-if="viewMode === 'table'"
                    :subscription-plans="paginatedPlans"
                    :selected-plans="selectedPlans"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPlan"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <SubscriptionPlanCardGrid
                    v-else
                    :subscription-plans="paginatedPlans"
                    :selected-plans="selectedPlans"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPlan"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="plansCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPlans.length"
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
            :onConfirm="deletePlan"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
