<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    subscriptionPlans: { type: Array, default: () => [] },
    selectedPlans: { type: Array, default: () => [] },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/** Локальная копия для dnd */
const localPlans = ref([])

watch(
    () => props.subscriptionPlans,
    (newVal) => {
        localPlans.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localPlans.value.map(p => p.id)
    emits('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localPlans.value.map(p => p.id)
    emits('toggle-all', { ids, checked })
}

const getPrimaryImage = (plan) => {
    if (plan.images && plan.images.length) {
        return [...plan.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }
    return null
}

const formatDate = (dateStr) => {
    if (!dateStr) return '—'
    const d = new Date(dateStr)
    if (isNaN(d)) return '—'
    return d.toLocaleDateString('ru-RU', { year: 'numeric', month: 'short', day: 'numeric' })
}

// Маппинг billing_period -> i18n ключ
const billingPeriodLabel = (bp) => {
    const map = {
        day: 'days',
        week: 'weeks',
        month: 'months',
        year: 'years',
    }

    const key = map[String(bp || '').toLowerCase()]
    return key ? t(key) : (bp || '—')
}

// Формат: "<период>: <количество>"  →  "месяцев: 12"
const periodLabel = (plan) => {
    const period = billingPeriodLabel(plan.billing_period)
    const interval = plan.interval ?? '—'

    return `${period}: ${interval}`
}

const priceLabel = (plan) => {
    const price = plan.price ?? '0.00'
    const code = plan.currency?.code || ''
    return `${price} ${code}`.trim()
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Верхняя панель -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedPlans.length }}
            </div>

            <label
                v-if="localPlans.length"
                class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localPlans.length" class="p-3">
            <draggable
                tag="div"
                v-model="localPlans"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: plan }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="cursor-move drag-handle text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${plan.locale}] : [${plan.sort}]`"
                                >
                                    ID: {{ plan.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           font-semibold border border-gray-400
                                           bg-teal-100 dark:bg-teal-900/50
                                           text-teal-700 dark:text-teal-300"
                                    :title="t('price')"
                                >
                                    {{ priceLabel(plan) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedPlans.includes(plan.id)"
                                    @change="$emit('toggle-select', plan.id)"
                                />
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <template v-if="plan.images?.length">
                            <img
                                :src="getPrimaryImage(plan).webp_url || getPrimaryImage(plan).url"
                                :alt="getPrimaryImage(plan).alt || t('defaultImageAlt')"
                                :title="getPrimaryImage(plan).caption || t('image')"
                                class="w-full h-full object-cover"
                            />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/subscription_plan_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col flex-1 px-3 py-2">
                            <div
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       text-center line-clamp-2"
                                :title="plan.subtitle || plan.title"
                            >
                                {{ plan.title }}
                            </div>

                            <div class="text-[9px] text-slate-500 dark:text-slate-300 text-center">
                                {{ plan.slug }}
                            </div>

                            <div class="font-semibold text-[11px]
                                        text-gray-700 dark:text-gray-300 text-center">
                                {{ plan.subtitle }}
                            </div>

                            <div class="my-1 text-[11px] text-center
                                        text-gray-500 dark:text-gray-400
                                        border border-dashed border-gray-400">
                                {{ plan.short }}
                            </div>

                            <div class="flex flex-wrap justify-center gap-1 mt-1
                                        text-[10px] font-semibold">
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-teal-100 dark:bg-teal-900
                                           border border-gray-400
                                           text-teal-700 dark:text-teal-300"
                                    :title="t('period')"
                                >
                                    {{ t('period') }} - {{ periodLabel(plan) }}
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-fuchsia-100 dark:bg-fuchsia-900
                                           border border-gray-400
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                    :title="t('trial')"
                                >
                                    {{ t('trial') }}: {{ plan.trial_days ?? 0 }} {{ t('days') }}
                                </span>
                            </div>

                            <div class="mt-2 text-[11px] text-center">
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
                                    <span class="text-teal-700 dark:text-teal-300">
                                        {{ formatDate(plan.available_from) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ t('shortExpires') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                        {{ formatDate(plan.available_until) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="plan.activity"
                                    @toggle-activity="$emit('toggle-activity', plan)"
                                    :title="plan.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.subscriptionPlans.edit', plan.id)" />
                                <DeleteIconButton
                                    @delete="$emit('delete', plan.id, plan.title)" />
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
