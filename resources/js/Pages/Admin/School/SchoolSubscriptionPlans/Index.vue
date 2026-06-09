<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список тарифов подписок школы
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

import BulkActionSelect from '@/Components/Admin/School/SchoolSubscriptionPlan/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolSubscriptionPlan/Sort/SortSelect.vue'
import SubscriptionPlanTable from '@/Components/Admin/School/SchoolSubscriptionPlan/Table/SubscriptionPlanTable.vue'
import SubscriptionPlanCardGrid from '@/Components/Admin/School/SchoolSubscriptionPlan/View/SubscriptionPlanCardGrid.vue'

// Настройки локализации и уведомлений
const { t } = useI18n()
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    subscriptionPlans: { type: Array, default: () => [] },
    plansCount: { type: Number, default: 0 },

    adminSchoolSubscriptionPlansPerPage: { type: Number, default: 10 },
    adminSchoolSubscriptionPlansDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения: таблица или карточки
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем выбранный режим отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия тарифов для работы без перезагрузки страницы
const localPlans = ref([])

// Синхронизация тарифов из props
watch(
    () => props.subscriptionPlans,
    (newVal) => {
        localPlans.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolSubscriptionPlansPerPage || 10)

// Сохраняем выбранное количество элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountSubscriptionPlans'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Текущая сортировка списка
const sortParam = ref(props.adminSchoolSubscriptionPlansDefaultSort || 'idDesc')

// Сохраняем выбранную сортировку
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortSubscriptionPlans'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления тарифа
const showConfirmDeleteModal = ref(false)
const planToDeleteId = ref(null)
const planToDeleteTitle = ref('')

// Открытие окна подтверждения удаления
const confirmDelete = (planOrId, title = null) => {
    if (typeof planOrId === 'object') {
        planToDeleteId.value = planOrId.id
        planToDeleteTitle.value = title || planOrId.title || `ID: ${planOrId.id}`
    } else {
        planToDeleteId.value = planOrId
        planToDeleteTitle.value = title || `ID: ${planOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие окна удаления
const closeModal = () => {
    showConfirmDeleteModal.value = false
    planToDeleteId.value = null
    planToDeleteTitle.value = ''
}

// Удаление тарифного плана
const deletePlan = () => {
    if (planToDeleteId.value === null) return

    const idToDelete = planToDeleteId.value
    const titleToDelete = planToDeleteTitle.value

    router.delete(route('admin.schoolSubscriptionPlans.destroy', {
        schoolSubscriptionPlan: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Тариф "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Тариф: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковая строка
const searchQuery = ref('')

// Нормализация строк для поиска и сортировки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Сортировка тарифных планов
const sortPlans = (items) => {
    const list = (items || []).slice()

    const dateValue = (value) => {
        if (!value) return null

        const time = new Date(value).getTime()

        return Number.isFinite(time) ? time : null
    }

    const sortByNumber = (field, direction = 'desc') => {
        return list.sort((a, b) => {
            const av = Number(a[field] ?? 0)
            const bv = Number(b[field] ?? 0)

            return direction === 'asc' ? av - bv : bv - av
        })
    }

    const sortByString = (field, direction = 'asc') => {
        return list.sort((a, b) => {
            const av = normalize(a[field])
            const bv = normalize(b[field])

            return direction === 'asc'
                ? av.localeCompare(bv)
                : bv.localeCompare(av)
        })
    }

    const sortByDate = (field, direction = 'desc') => {
        return list.sort((a, b) => {
            const av = dateValue(a[field])
            const bv = dateValue(b[field])

            if (av === null && bv === null) return 0
            if (av === null) return 1
            if (bv === null) return -1

            return direction === 'asc' ? av - bv : bv - av
        })
    }

    if (sortParam.value === 'idAsc') return sortByNumber('id', 'asc')
    if (sortParam.value === 'idDesc') return sortByNumber('id', 'desc')

    if (sortParam.value === 'sortAsc') return sortByNumber('sort', 'asc')
    if (sortParam.value === 'sortDesc') return sortByNumber('sort', 'desc')

    if (sortParam.value === 'titleAsc') return sortByString('title', 'asc')
    if (sortParam.value === 'titleDesc') return sortByString('title', 'desc')

    if (sortParam.value === 'publishedAtAsc') return sortByDate('published_at', 'asc')
    if (sortParam.value === 'publishedAtDesc') return sortByDate('published_at', 'desc')

    if (sortParam.value === 'availableFromAsc') return sortByDate('available_from', 'asc')
    if (sortParam.value === 'availableFromDesc') return sortByDate('available_from', 'desc')

    if (sortParam.value === 'availableUntilAsc') return sortByDate('available_until', 'asc')
    if (sortParam.value === 'availableUntilDesc') return sortByDate('available_until', 'desc')

    if (sortParam.value === 'priceAsc') return sortByNumber('price', 'asc')
    if (sortParam.value === 'priceDesc') return sortByNumber('price', 'desc')

    if (sortParam.value === 'trialDaysAsc') return sortByNumber('trial_days', 'asc')
    if (sortParam.value === 'trialDaysDesc') return sortByNumber('trial_days', 'desc')

    if (sortParam.value === 'billingPeriodAsc') return sortByString('billing_period', 'asc')
    if (sortParam.value === 'billingPeriodDesc') return sortByString('billing_period', 'desc')

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'availabilityNowFirst') {
        const now = Date.now()

        return list.sort((a, b) => {
            const aFrom = dateValue(a.available_from)
            const aUntil = dateValue(a.available_until)
            const bFrom = dateValue(b.available_from)
            const bUntil = dateValue(b.available_until)

            const aAvailable =
                (!aFrom || aFrom <= now) &&
                (!aUntil || aUntil >= now)

            const bAvailable =
                (!bFrom || bFrom <= now) &&
                (!bUntil || bUntil >= now)

            if (aAvailable === bAvailable) return 0

            return aAvailable ? -1 : 1
        })
    }

    return list
}

// Поиск и фильтрация тарифов
const filteredPlans = computed(() => {
    let filtered = localPlans.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortPlans(filtered)
    }

    filtered = filtered.filter((plan) => {
        const title = normalize(plan?.title)
        const subtitle = normalize(plan?.subtitle)
        const slug = normalize(plan?.slug)
        const short = normalize(plan?.short)
        const description = normalize(plan?.description)
        const price = normalize(plan?.price)
        const currencyCode = normalize(plan?.currency?.code)

        return (
            title.includes(q) ||
            subtitle.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            price.includes(q) ||
            currencyCode.includes(q)
        )
    })

    return sortPlans(filtered)
})

// Пагинация тарифов
const paginatedPlans = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredPlans.value.slice(start, start + per)
})

// Сбрасываем страницу при изменении поиска или количества элементов
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление тарифа без перезагрузки
const patchPlan = (planId, payload) => {
    const index = localPlans.value.findIndex(plan => plan.id === planId)

    if (index !== -1) {
        localPlans.value[index] = {
            ...localPlans.value[index],
            ...payload,
        }
    }
}

// Выбранные тарифы для массовых действий
const selectedPlans = ref([])

// Выбрать или снять выбор со всех тарифов
const toggleAll = ({ ids, checked }) => {
    selectedPlans.value = checked ? [...ids] : []
}

// Выбор отдельного тарифа
const toggleSelectPlan = (id) => {
    const index = selectedPlans.value.indexOf(id)

    if (index > -1) {
        selectedPlans.value.splice(index, 1)
    } else {
        selectedPlans.value.push(id)
    }
}

// Массовое обновление порядка сортировки
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolSubscriptionPlans.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок тарифов успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки тарифов:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок тарифов.')

            router.reload({
                only: ['subscriptionPlans'],
                preserveScroll: true,
            })
        },
    })
}

// Массовое изменение активности тарифов
const bulkToggleActivity = (newActivity) => {
    if (!selectedPlans.value.length) {
        toast.warning('Выберите тарифы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedPlans.value]

    router.put(route('admin.actions.schoolSubscriptionPlans.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchPlan(id, { activity: newActivity }))
            selectedPlans.value = []
            toast.success('Активность выбранных тарифов обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general ||
                'Ошибка массового обновления активности.')
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedPlans.value = paginatedPlans.value.map(plan => plan.id)
    } else if (action === 'deselectAll') {
        selectedPlans.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

// Переключение активности одного тарифа
const toggleActivity = (plan) => {
    const newActivity = !plan.activity
    const planTitle = plan.title || `ID: ${plan.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolSubscriptionPlans.updateActivity', {
        schoolSubscriptionPlan: plan.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchPlan(plan.id, { activity: newActivity })
            plan.activity = newActivity
            toast.success(`Тариф "${planTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для тарифа "${planTitle}".`)
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('subscriptionPlans')">
        <template #header>
            <TitlePage>{{ t('subscriptionPlans') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolSubscriptionPlans.create')">
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
                        {{ t('addSubscriptionPlan') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="plansCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="plansCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"
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
                    v-if="plansCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ plansCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="plansCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPlans.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

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
                    v-if="plansCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPlans.length"
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
            :onConfirm="deletePlan"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
