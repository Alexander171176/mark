<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    subscriptionPlans: {
        type: Array,
        default: () => [],
    },
    selectedPlans: {
        type: Array,
        default: () => [],
    },
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
    (plans) => {
        localPlans.value = Array.isArray(plans)
            ? plans.map((plan) => ({ ...plan }))
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
        || '/storage/subscription_plan_images/default-image.png'
}

const imageAlt = (plan) => {
    const image = getPrimaryImage(plan)

    return image?.alt
        || plan.title
        || t('defaultImageTitle')
}

const formatDate = (date) => {
    if (!date) return '—'

    const value = new Date(date)

    if (Number.isNaN(value.getTime())) {
        return '—'
    }

    return value.toLocaleDateString('ru-RU', {
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
    const code = plan.currency?.code || plan.currency_code || ''

    return `${price} ${code}`.trim()
}
</script>

<template>
    <div
        class="relative overflow-hidden rounded-sm border border-slate-300 bg-white shadow-lg
               dark:border-slate-600 dark:bg-slate-700"
    >
        <div
            class="flex flex-col gap-2 border-b border-slate-300 px-3 py-2
                   dark:border-slate-600 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="text-xs font-semibold text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedPlans.length }}
            </div>

            <label
                v-if="localPlans.length"
                class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold
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

        <div v-if="localPlans.length"
             class="overflow-x-auto">
            <table class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
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
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-left">
                            {{ t('subscriptionPlan') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('period') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('price') }}
                        </div>
                    </th>
                    <th class="flex items-center justify-center"
                        :title="t('availability')">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24">
                            <path
                                class="fill-current text-violet-700 dark:text-violet-300"
                                d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                        </svg>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <input
                            type="checkbox"
                            class="rounded border-slate-400"
                            :checked="allSelected"
                            @change="toggleAll"
                        >
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localPlans"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: plan }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg class="h-4 w-4 text-slate-500" fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div :title="`Sort: ${plan.sort ?? '—'}`">
                                    {{ plan.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageSrc(plan)"
                                        :alt="imageAlt(plan)"
                                        class="h-9 w-12 rounded-sm object-cover"
                                    >
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="w-fit flex flex-col space-y-1">
                                    <div
                                        class="text-sm text-blue-700 dark:text-blue-200"
                                        :title="plan.title"
                                    >
                                        {{ plan.title || '—' }}
                                    </div>
                                    <div
                                        class="text-[11px] text-slate-500 dark:text-slate-300"
                                        :title="plan.slug"
                                    >
                                        {{ plan.slug || '—' }}
                                    </div>
                                    <div
                                        v-if="plan.subtitle"
                                        class="text-[11px] text-slate-600 dark:text-slate-300"
                                        :title="plan.subtitle"
                                    >
                                        {{ plan.subtitle }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-xs flex flex-col
                                            justify-center items-center">
                                    <div>
                                        {{ periodLabel(plan) }}
                                    </div>
                                    <div class="text-fuchsia-700 dark:text-fuchsia-300"
                                         :title="t('trial')">
                                        {{ plan.trial_days ?? 0 }} {{ t('days') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-xs flex flex-col items-center justify-center">
                                    <div class="text-emerald-700 dark:text-emerald-300">
                                        {{ priceLabel(plan) }}
                                    </div>
                                    <div class="text-yellow-700 dark:text-yellow-300"
                                         :title="t('publishedAt')">
                                        {{ formatDate(plan.published_at) }}
                                    </div>

                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-[10px] space-y-1">
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
                                <div class="flex justify-end gap-2">
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
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-400"
                                    :checked="selectedPlans.includes(plan.id)"
                                    @change="emit('toggle-select', plan.id)"
                                >
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>
        </div>
        <div v-else
             class="p-5 text-center text-sm font-semibold
                    text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
