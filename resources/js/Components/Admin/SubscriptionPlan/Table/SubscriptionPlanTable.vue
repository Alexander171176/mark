<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    subscriptionPlans: { type: Array, default: () => [] },
    selectedPlans: { type: Array, default: () => [] }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

/** Локальная копия для vuedraggable */
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

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localPlans.value.map(p => p.id)
    emits('toggle-all', { ids, checked })
}

/** Главное изображение (первое по order) */
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
               border border-slate-200 dark:border-slate-600 relative"
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

        <div class="overflow-x-auto">
            <table
                v-if="subscriptionPlans.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <!-- drag handle header -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('subscriptionPlan') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center" :title="t('billingPeriod')">
                            {{ t('period') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center" :title="t('price')">
                            {{ t('price') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center" :title="t('trial')">
                            {{ t('trial') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center" :title="t('publishedAt')">
                            {{ t('publishedAt') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center" :title="t('availability')">
                            {{ t('availability') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localPlans"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: plan }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs text-slate-800 dark:text-blue-200"
                                    :title="`[${plan.locale}] : [${plan.sort}]`"
                                >
                                    {{ plan.id }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <template v-if="plan.images?.length">
                                        <img
                                            :src="getPrimaryImage(plan).webp_url || getPrimaryImage(plan).url"
                                            :alt="getPrimaryImage(plan).alt || t('defaultImageAlt')"
                                            :title="getPrimaryImage(plan).caption || t('image')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        />
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/subscription_plan_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        />
                                    </template>
                                </div>
                            </td>

                            <td class="px-2 py-3">
                                <div class="flex flex-col">
                                    <div class="text-xs text-sky-700 dark:text-sky-200"
                                         :title="plan.subtitle">
                                        {{ plan.title }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-300"
                                         :title="plan.short">
                                        {{ plan.slug }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs text-slate-700 dark:text-slate-300">
                                    {{ periodLabel(plan) }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-emerald-700 dark:text-emerald-300">
                                    {{ priceLabel(plan) }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-fuchsia-700 dark:text-fuchsia-300">
                                    {{ (plan.trial_days ?? 0) }} {{ t('days') }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-yellow-700 dark:text-yellow-300">
                                    {{ formatDate(plan.published_at) }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-left text-[10px]
                                            text-slate-600 dark:text-slate-200">
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
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="plan.activity"
                                        @toggle-activity="$emit('toggle-activity', plan)"
                                        :title="plan.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit
                                        :href="route('admin.subscriptionPlans.edit', plan.id)" />
                                    <DeleteIconButton
                                        @delete="$emit('delete', plan.id, plan.title)" />
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedPlans.includes(plan.id)"
                                        @change="$emit('toggle-select', plan.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
