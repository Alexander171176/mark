<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    subscriptionPlans: { type: Array, default: () => [] },
    selectedPlans: { type: Array, default: () => [] },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

const localPlans = ref([])

watch(
    () => props.subscriptionPlans,
    (newVal) => {
        localPlans.value = Array.isArray(newVal)
            ? newVal.map((plan) => ({ ...plan }))
            : []
    },
    { immediate: true, deep: true }
)

const allSelected = computed(() => {
    return localPlans.value.length > 0
        && localPlans.value.every((plan) => props.selectedPlans.includes(plan.id))
})

const handleDragEnd = () => {
    emit('update-sort-order', localPlans.value.map((plan) => plan.id))
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localPlans.value.map((plan) => plan.id),
        checked: event.target.checked,
    })
}

const getPrimaryImage = (plan) => {
    if (!Array.isArray(plan.images) || !plan.images.length) {
        return null
    }

    return [...plan.images].sort((a, b) => {
        return Number(a.order ?? 0) - Number(b.order ?? 0)
    })[0]
}

const imageSrc = (plan) => {
    const image = getPrimaryImage(plan)

    return image?.webp_url
        || image?.url
        || '/storage/school/school_subscription_plan_images/default-image.png'
}

const imageAlt = (plan) => {
    const image = getPrimaryImage(plan)

    return image?.alt
        || plan.title
        || t('defaultImageTitle')
}

const formatDate = (dateStr) => {
    if (!dateStr) return '—'

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const billingPeriodLabel = (period) => {
    const map = {
        day: 'days',
        week: 'weeks',
        month: 'months',
        year: 'years',
    }

    const key = map[String(period || '').toLowerCase()]

    return key ? t(key) : '—'
}

const periodLabel = (plan) => {
    return `${billingPeriodLabel(plan.billing_period)}: ${plan.interval ?? '—'}`
}

const priceLabel = (plan) => {
    const price = plan.price ?? '0.00'
    const code = plan.currency?.code || ''

    return `${price} ${code}`.trim()
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-400 bg-white shadow-lg
               dark:border-slate-500 dark:bg-slate-700"
    >
        <div
            class="flex flex-col gap-2 border-b border-slate-400 px-3 py-2
                   dark:border-slate-500 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedPlans.length }}
            </div>

            <label
                v-if="localPlans.length"
                class="inline-flex cursor-pointer items-center gap-2 text-xs
                       text-slate-600 dark:text-slate-200"
            >
                <span>{{ t('selectAll') }}</span>
                <input
                    type="checkbox"
                    class="rounded border-slate-400"
                    :checked="allSelected"
                    @change="toggleAll"
                >
            </label>
        </div>

        <div v-if="localPlans.length" class="p-3">
            <draggable
                v-model="localPlans"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: plan }">
                    <div
                        class="relative flex h-full flex-col overflow-hidden rounded-md
                               border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow
                               duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"
                    >
                        <div
                            class="flex items-center justify-between border-b border-dashed
                                   border-slate-400 px-2 py-1 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>
                                <div
                                    class="rounded-sm border border-gray-400
                                           bg-slate-200 px-1.5 py-0.5
                                           text-[10px] font-semibold
                                           text-slate-800 dark:bg-slate-700
                                           dark:text-blue-100"
                                    :title="`Sort: ${plan.sort ?? '—'}`"
                                >
                                    ID: {{ plan.id }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-teal-100 px-1.5 py-0.5
                                           text-[10px] font-bold text-teal-700
                                           dark:bg-teal-900/50 dark:text-teal-300"
                                    :title="t('price')"
                                >
                                    {{ priceLabel(plan) }}
                                </span>
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-400"
                                    :checked="selectedPlans.includes(plan.id)"
                                    @change="emit('toggle-select', plan.id)"
                                >
                            </div>
                        </div>
                        <div class="relative h-32 w-full bg-slate-200 dark:bg-slate-900">
                            <img
                                :src="imageSrc(plan)"
                                :alt="imageAlt(plan)"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div class="flex flex-1 flex-col px-3 py-2">
                            <div
                                class="line-clamp-2 text-center text-sm font-semibold
                                       text-blue-700 dark:text-blue-200"
                                :title="plan.subtitle || plan.title"
                            >
                                {{ plan.title || '—' }}
                            </div>
                            <div class="text-center text-[9px] text-slate-500 dark:text-slate-300">
                                {{ plan.slug || '—' }}
                            </div>
                            <div
                                v-if="plan.subtitle"
                                class="text-center text-[11px] font-semibold
                                       text-gray-700 dark:text-gray-300"
                            >
                                {{ plan.subtitle }}
                            </div>
                            <div
                                v-if="plan.short"
                                class="my-1 border border-dashed border-gray-400 text-center
                                       text-[11px] text-gray-500 dark:text-gray-400"
                            >
                                {{ plan.short }}
                            </div>
                            <div class="mt-1 flex flex-wrap justify-center gap-1
                                        text-[10px] font-semibold">
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-teal-100 dark:bg-teal-900 px-2 py-0.5
                                           text-teal-700 dark:text-teal-300"
                                >
                                    {{ t('period') }} - {{ periodLabel(plan) }}
                                </span>
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900 px-2 py-0.5
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ t('trial') }}: {{ plan.trial_days ?? 0 }} {{ t('days') }}
                                </span>
                            </div>
                            <div class="mt-2 text-center text-[11px]">
                                <div class="font-semibold">
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ t('publishedAt') }}:
                                    </span>
                                    <span class="text-yellow-700 dark:text-yellow-300">
                                        {{ formatDate(plan.published_at) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ t('shortStarted') }}:
                                    </span>
                                    <span class="font-semibold text-teal-700 dark:text-teal-300">
                                        {{ formatDate(plan.available_from) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ t('shortExpires') }}:
                                    </span>
                                    <span class="font-semibold text-sky-700 dark:text-sky-300">
                                        {{ formatDate(plan.available_until) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center border-t border-dashed
                                   border-slate-400 px-3 py-2 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :is-active="plan.activity"
                                    :title="plan.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emit('toggle-activity', plan)"
                                />

                                <IconEdit
                                    :href="route('admin.schoolSubscriptionPlans.edit', plan.id)"
                                />

                                <DeleteIconButton
                                    @delete="emit('delete', plan.id, plan.title)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
