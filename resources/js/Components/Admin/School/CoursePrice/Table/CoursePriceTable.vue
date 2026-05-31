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
    if (value === null || value === undefined || value === '') {
        return '—'
    }

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

const courseLabel = (price) => {
    return price?.course?.title
        || price?.course?.translation?.title
        || `ID: ${price?.school_course_id || '—'}`
}

const courseSlug = (price) => {
    return price?.course?.slug || ''
}

const currencyLabel = (price) => {
    return price?.currency?.code || '—'
}

const currencySuffix = (price) => {
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
        <div class="overflow-x-auto">
            <table
                v-if="localPrices.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
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
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('course') }}
                        </div>
                    </th>
                    <th
                        class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap"
                        :title="t('currency')"
                    >
                        <div class="flex justify-center">
                            <svg class="shrink-0 h-4 w-4" viewBox="0 0 640 512">
                                <path class="fill-current text-teal-600 dark:text-teal-400"
                                      d="M352 288h-16v-88c0-4.42-3.58-8-8-8h-13.58c-4.74 0-9.37 1.4-13.31 4.03l-15.33 10.22a7.994 7.994 0 0 0-2.22 11.09l8.88 13.31a7.994 7.994 0 0 0 11.09 2.22l.47-.31V288h-16c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h64c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8zM608 64H32C14.33 64 0 78.33 0 96v320c0 17.67 14.33 32 32 32h576c17.67 0 32-14.33 32-32V96c0-17.67-14.33-32-32-32zM48 400v-64c35.35 0 64 28.65 64 64H48zm0-224v-64h64c0 35.35-28.65 64-64 64zm272 192c-53.02 0-96-50.15-96-112 0-61.86 42.98-112 96-112s96 50.14 96 112c0 61.87-43 112-96 112zm272 32h-64c0-35.35 28.65-64 64-64v64zm0-224c-35.35 0-64-28.65-64-64h64v64z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('price') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('salePrice') }}</div>
                    </th>
                    <th
                        class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap"
                        :title="t('periodValidityPrice')"
                    >
                        <div class="flex justify-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    class="fill-current text-sky-700 dark:text-sky-300"
                                    d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localPrices"
                    tag="tbody"
                    item-key="id"
                    handle=".drag-handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: price }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-2 py-1 text-center cursor-move">
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
                            </td>
                            <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                                <div
                                    class="text-center text-xs text-slate-800 dark:text-blue-200"
                                    :title="`[${price.sort ?? 0}]`"
                                >
                                    {{ price.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-8 last:pr-8">
                                <div class="flex flex-col">
                                    <span class="text-xs text-sky-700 dark:text-sky-200">
                                        {{ courseLabel(price) }}
                                    </span>
                                    <span class="text-[10px] text-slate-700 dark:text-slate-300">
                                        {{ courseSlug(price) || '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center
                                            text-teal-700 dark:text-teal-300">
                                    {{ currencyLabel(price) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-baseline justify-center gap-1">
                                    <div class="text-slate-700 dark:text-slate-300">
                                        {{ money(price.price) }}
                                    </div>
                                    <div
                                        v-if="money(price.price) !== '—'"
                                        class="text-[13px] opacity-70
                                               text-gray-700 dark:text-gray-300"
                                    >
                                        {{ currencySuffix(price) }}
                                    </div>
                                </div>
                                <div
                                    v-if="Number(price.compare_at_price) > 0"
                                    class="flex items-baseline justify-center gap-1"
                                >
                                    <div
                                        class="text-gray-500 dark:text-gray-400
                                        line-through opacity-75 text-xs"
                                    >
                                        {{ money(price.compare_at_price) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <div class="text-orange-700 dark:text-orange-300">
                                        {{ money(price.effective_price) }}
                                        <span
                                            v-if="money(price.effective_price) !== '—'"
                                            class="text-[13px] opacity-70
                                                   text-gray-700 dark:text-gray-300"
                                        >
                                            {{ currencySuffix(price) }}
                                        </span>
                                    </div>
                                    <div
                                        v-if="price.has_discount && price.discount_percent"
                                        class="text-[11px] text-rose-600 dark:text-rose-300"
                                    >
                                        -{{ price.discount_percent }}%
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-xs text-center">
                                    <div class="text-blue-700 dark:text-blue-300">
                                        {{ dateShort(price.starts_at) }}
                                    </div>
                                    <div class="text-slate-400">—</div>
                                    <div class="text-blue-700 dark:text-blue-300">
                                        {{ dateShort(price.ends_at) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1">
                                    <ActivityToggle
                                        :isActive="price.activity"
                                        :title="price.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="$emit('toggle-activity', price)"
                                    />
                                    <IconEdit
                                        :href="route('admin.schoolCoursePrices.edit', {
                                            schoolCoursePrice: price.id,
                                        })"
                                    />
                                    <DeleteIconButton @delete="$emit('delete', price)" />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedPrices.includes(price.id)"
                                        @change="$emit('toggle-select', price.id)"
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
