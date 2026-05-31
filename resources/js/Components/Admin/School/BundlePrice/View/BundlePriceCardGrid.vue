<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    prices: { type: Array, default: () => [] },
    selectedPrices: { type: Array, default: () => [] },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

const localPrices = ref([])

watch(
    () => props.prices,
    (newVal) => {
        localPrices.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localPrices.value.map((price) => price.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localPrices.value.map((price) => price.id),
        checked: event.target.checked,
    })
}

const money = (value) => {
    if (value === null || value === undefined || value === '') return '—'

    return Number(value).toLocaleString('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
}

const dateShort = (iso) => {
    if (!iso) return '—'

    const date = new Date(iso)

    if (Number.isNaN(date.getTime())) return '—'

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const bundleTitle = (price) => {
    return price?.bundle?.title
        || price?.bundle?.translation?.title
        || `ID: ${price?.school_bundle_id || '—'}`
}

const bundleSlug = (price) => {
    return price?.bundle?.slug || ''
}

const currencyCode = (price) => {
    return price?.currency?.code || '—'
}

const currencyName = (price) => {
    return price?.currency?.name || ''
}

const currencySymbol = (price) => {
    return price?.currency?.symbol || price?.currency?.code || ''
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedPrices.length }}
            </div>

            <label
                v-if="localPrices.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localPrices.length" class="p-3">
            <draggable
                v-model="localPrices"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: price }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`${t('sort')}: ${price.sort ?? 0}`"
                                >
                                    ID: {{ price.id }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="font-semibold text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-teal-100 dark:bg-teal-900/50
                                           text-teal-700 dark:text-teal-300"
                                    :title="t('currency')"
                                >
                                    {{ currencyCode(price) }}
                                </span>
                                <input
                                    type="checkbox"
                                    :checked="selectedPrices.includes(price.id)"
                                    @change="$emit('toggle-select', price.id)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div
                                class="text-xs font-semibold text-center
                                       text-sky-700 dark:text-sky-200"
                            >
                                {{ bundleTitle(price) }}
                                <span
                                    v-if="bundleSlug(price)"
                                    class="block text-[10px] font-normal
                                           text-gray-500 dark:text-gray-400"
                                >
                                    {{ bundleSlug(price) }}
                                </span>
                            </div>
                            <div
                                class="text-[12px] font-semibold
                                       text-teal-700 dark:text-teal-300 text-center"
                            >
                                {{ currencyName(price) }}
                                <span class="text-slate-400">—</span>
                                {{ currencyCode(price) }}
                            </div>
                            <div
                                v-if="Number(price.compare_at_price) > 0"
                                class="text-[11px] font-semibold">
                                <span class="text-slate-500 dark:text-slate-300">
                                    {{ t('compareAtPrice') }}:
                                </span>
                                <span class="text-slate-700 dark:text-slate-300 mr-1
                                             line-through opacity-75">
                                    {{ money(price.compare_at_price) }}
                                </span>
                                <span class="text-[11px] opacity-70
                                             text-gray-700 dark:text-gray-300">
                                    {{ currencySymbol(price) }}
                                </span>
                                <div class="text-[11px] font-semibold">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('price') }}:
                                    </span>
                                    <span class="text-slate-700 dark:text-slate-300 mr-1">
                                        {{ money(price.price) }}
                                    </span>
                                    <span class="text-[11px] opacity-70
                                                 text-gray-700 dark:text-gray-300">
                                        {{ currencySymbol(price) }}
                                    </span>
                                </div>
                                <div class="text-[11px] font-semibold">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('salePrice') }}:
                                    </span>
                                    <span class="text-orange-700 dark:text-orange-300 mr-1">
                                        {{ money(price.effective_price) }}
                                    </span>
                                    <span class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ currencySymbol(price) }}
                                    </span>
                                    <br>
                                    <span
                                        v-if="price.has_discount && price.discount_percent"
                                        class="ml-2 text-rose-600 dark:text-rose-300"
                                    >
                                        -{{ price.discount_percent }}%
                                    </span>
                                </div>
                            </div>
                            <div class="text-[9px] text-center font-semibold">
                                <div>
                                    <span class="text-slate-700 dark:text-slate-200">
                                        {{ t('priceStartsAt') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                        {{ dateShort(price.starts_at) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-700 dark:text-slate-200">
                                        {{ t('priceEndsAt') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                        {{ dateShort(price.ends_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="price.activity"
                                    :title="price.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="$emit('toggle-activity', price)"
                                />
                                <IconEdit
                                    :href="route('admin.schoolBundlePrices.edit', {
                                        schoolBundlePrice: price.id,
                                    })"
                                />
                                <DeleteIconButton @delete="$emit('delete', price)" />
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
