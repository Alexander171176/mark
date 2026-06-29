<script setup>
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

import BulkActionSelect
    from '@/Components/Admin/School/SchoolSubscriptionPlan/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolSubscriptionPlan/Sort/SortSelect.vue'
import SubscriptionPlanTable
    from '@/Components/Admin/School/SchoolSubscriptionPlan/Table/SubscriptionPlanTable.vue'
import SubscriptionPlanCardGrid
    from '@/Components/Admin/School/SchoolSubscriptionPlan/View/SubscriptionPlanCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolSubscriptionPlansProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    subscriptionPlans: { type: [Array, Object], default: () => [] },
    plansCount: { type: Number, default: 0 },

    adminSchoolSubscriptionPlansPerPage: { type: Number, default: 10 },
    adminSchoolSubscriptionPlansDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    currencies: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_subscription_plans') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_subscription_plans', val)
})

const plansList = computed(() => {
    if (Array.isArray(props.subscriptionPlans)) return props.subscriptionPlans
    if (Array.isArray(props.subscriptionPlans?.data)) return props.subscriptionPlans.data
    return []
})

const localPlans = ref([])

watch(
    plansList,
    (newVal) => {
        localPlans.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSchoolSubscriptionPlansPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountSchoolSubscriptionPlans'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSchoolSubscriptionPlansDefaultSort ||
    'idDesc'
)

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortSchoolSubscriptionPlans'), { value: newVal }, {
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
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getPlanTitle = (plan) => {
    return plan?.title
        || plan?.translation?.title
        || plan?.translations?.[0]?.title
        || `ID: ${plan?.id}`
}

const getPlanSubtitle = (plan) => {
    return plan?.subtitle
        || plan?.translation?.subtitle
        || plan?.translations?.[0]?.subtitle
        || ''
}

const getPlanShort = (plan) => {
    return plan?.short
        || plan?.translation?.short
        || plan?.translations?.[0]?.short
        || ''
}

const getPlanDescription = (plan) => {
    return plan?.description
        || plan?.translation?.description
        || plan?.translations?.[0]?.description
        || ''
}

const isAvailableNow = (plan) => {
    const now = Date.now()
    const publishedAt = safeDate(plan?.published_at)
    const availableFrom = safeDate(plan?.available_from)
    const availableUntil = safeDate(plan?.available_until)

    return publishedAt > 0
        && (!availableFrom || availableFrom <= now)
        && (!availableUntil || availableUntil >= now)
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

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortPlans = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getPlanTitle(a)).localeCompare(normalize(getPlanTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getPlanTitle(b)).localeCompare(normalize(getPlanTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        availabilityNowFirst: (a, b) =>
            Number(isAvailableNow(b)) - Number(isAvailableNow(a))
            || safeNumber(b?.id) - safeNumber(a?.id),

        availableFromAsc: byDateAsc('available_from'),
        availableFromDesc: byDateDesc('available_from'),

        availableUntilAsc: byDateAsc('available_until'),
        availableUntilDesc: byDateDesc('available_until'),

        priceAsc: byNumberAsc('price'),
        priceDesc: byNumberDesc('price'),

        trialDaysAsc: byNumberAsc('trial_days'),
        trialDaysDesc: byNumberDesc('trial_days'),

        billingPeriodAsc: byStringAsc('billing_period'),
        billingPeriodDesc: byStringDesc('billing_period'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        autoRenewAsc: byNumberAsc('auto_renew'),
        autoRenewDesc: byNumberDesc('auto_renew'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredPlans = computed(() => {
    let filtered = localPlans.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortPlans(filtered)
    }

    filtered = filtered.filter((plan) => {
        const values = [
            plan?.id,
            getPlanTitle(plan),
            getPlanSubtitle(plan),
            plan?.slug,
            getPlanShort(plan),
            getPlanDescription(plan),
            plan?.billing_period,
            plan?.provider,
            plan?.provider_ref,
            plan?.price,
            plan?.trial_days,
            plan?.currency?.code,
            plan?.currency?.name,
            plan?.currency?.symbol,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortPlans(filtered)
})

const paginatedPlans = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredPlans.value.slice(start, start + per)
})

const displayedPlans = computed(() => {
    return props.useServerProcessing
        ? plansList.value
        : paginatedPlans.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const planToDeleteId = ref(null)
const planToDeleteTitle = ref('')

const confirmDelete = (planOrId, title = null) => {
    if (typeof planOrId === 'object') {
        planToDeleteId.value = planOrId.id
        planToDeleteTitle.value = title || getPlanTitle(planOrId)
    } else {
        planToDeleteId.value = planOrId
        planToDeleteTitle.value = title || `ID: ${planOrId}`
    }

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

    router.delete(route('admin.schoolSubscriptionPlans.destroy', {
        schoolSubscriptionPlan: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Тариф "${titleToDelete || 'ID: ' + idToDelete}" удалён.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Тариф: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const patchPlan = (planId, payload) => {
    const index = localPlans.value.findIndex(plan => plan.id === planId)

    if (index !== -1) {
        localPlans.value[index] = {
            ...localPlans.value[index],
            ...payload,
        }
    }
}

const selectedPlans = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedPlans.value.map(plan => plan.id)

    if (checked) {
        selectedPlans.value = [...new Set([...selectedPlans.value, ...ids])]
    } else {
        selectedPlans.value = selectedPlans.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectPlan = (id) => {
    const index = selectedPlans.value.indexOf(id)

    if (index > -1) {
        selectedPlans.value.splice(index, 1)
    } else {
        selectedPlans.value.push(id)
    }
}

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
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок тарифов.')

            router.reload({
                only: ['subscriptionPlans'],
                preserveScroll: true,
            })
        },
    })
}

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
    }

    event.target.value = ''
}

const toggleActivity = (plan) => {
    const newActivity = !plan.activity
    const planTitle = getPlanTitle(plan)
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolSubscriptionPlans.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addSubscriptionPlan') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolSubscriptionPlansProcessingMode"
                        :mode="adminSchoolSubscriptionPlansProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="plansCount"
                    />
                </div>

                <SearchInput
                    v-if="plansCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="plansCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div v-if="plansCount" class="flex justify-between items-center flex-col md:flex-row my-3 gap-3">
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolSubscriptionPlans"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div v-if="plansCount" class="flex flex-col lg:flex-row items-center justify-between gap-3">
                    <CountTable>{{ plansCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div v-if="plansCount" class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPlans.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="subscriptionPlans"
                    />
                </div>

                <SubscriptionPlanTable
                    v-if="viewMode === 'table'"
                    :subscription-plans="displayedPlans"
                    :selected-plans="selectedPlans"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPlan"
                    @toggle-all="toggleAll"
                />

                <SubscriptionPlanCardGrid
                    v-else
                    :subscription-plans="displayedPlans"
                    :selected-plans="selectedPlans"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectPlan"
                    @toggle-all="toggleAll"
                />

                <div v-if="plansCount" class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPlans.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="subscriptionPlans"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deletePlan"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
