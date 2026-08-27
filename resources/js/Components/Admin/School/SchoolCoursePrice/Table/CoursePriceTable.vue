<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    prices: {
        type: Array,
        default: () => [],
    },

    selectedPrices: {
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

/* ==========================================================
 * LOCAL DATA
 * ========================================================== */

const localPrices = ref([])

watch(
    () => props.prices,
    (newValue) => {
        localPrices.value = JSON.parse(
            JSON.stringify(newValue || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ==========================================================
 * SELECT / DRAG
 * ========================================================== */

const allSelected = computed(() => {
    return localPrices.value.length > 0
        && localPrices.value.every(
            price =>
                props.selectedPrices.includes(
                    price.id
                )
        )
})

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localPrices.value.map(
            price => price.id
        )
    )
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localPrices.value.map(
            price => price.id
        ),

        checked:
        event.target.checked,
    })
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

const getCourseTitle = (price) => {
    return price?.course?.translation?.title
        || `ID: ${price?.school_course_id || '—'}`
}

const getCourseSubtitle = (price) => {
    return price?.course?.translation?.subtitle
        || ''
}

const getCourseSlug = (price) => {
    return price?.course?.slug
        || ''
}

const getCurrencyCode = (price) => {
    return price?.currency?.code
        || '—'
}

const getCurrencyName = (price) => {
    return price?.currency?.name
        || ''
}

const getCurrencySymbol = (price) => {
    return price?.currency?.symbol
        || price?.currency?.code
        || ''
}

/* ==========================================================
 * FORMATTERS
 * ========================================================== */

const money = (value) => {
    if (
        value === null
        || value === undefined
        || value === ''
    ) {
        return '—'
    }

    const number =
        Number(value)

    if (
        !Number.isFinite(number)
    ) {
        return '—'
    }

    return number.toLocaleString(
        'ru-RU',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }
    )
}

const dateShort = (iso) => {
    if (!iso) {
        return '—'
    }

    const date =
        new Date(iso)

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return '—'
    }

    return date.toLocaleDateString(
        'ru-RU',
        {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }
    )
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <!-- Selection -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}:
                {{ selectedPrices.length }}
            </div>

            <label
                v-if="localPrices.length"
                class="flex items-center gap-2 text-xs
                       text-slate-600 dark:text-slate-200 cursor-pointer"
            >
                <span>
                    {{ t('selectAll') }}
                </span>

                <input
                    type="checkbox"
                    class="rounded border-slate-400"
                    :checked="allSelected"
                    @change="toggleAll"
                />
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
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
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

                    <th class="px-2 py-3 w-px text-center font-medium">
                        {{ t('id') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-left font-semibold">
                        {{ t('course') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center font-semibold">
                            <span :title="t('currency')">
                                {{ t('currency') }}
                            </span>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center font-semibold">
                        {{ t('price') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center font-semibold">
                        {{ t('salePrice') }}
                    </th>

                    <th
                        class="px-2 py-3 whitespace-nowrap text-center font-semibold"
                        :title="t('periodValidityPrice')"
                    >
                        {{ t('period') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-end font-semibold">
                        {{ t('actions') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            class="rounded border-slate-400"
                            :checked="allSelected"
                            @change="toggleAll"
                        />
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
                            <!-- Drag -->
                            <td class="px-2 py-1 text-center">
                                <button
                                    type="button"
                                    class="drag-handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>
                            </td>

                            <!-- ID -->
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <div
                                    class="text-xs text-slate-800 dark:text-blue-200"
                                    :title="`${t('sort')}: ${price.sort ?? 0}`"
                                >
                                    {{ price.id }}
                                </div>
                            </td>

                            <!-- Course -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs text-sky-700 dark:text-sky-200"
                                        :title="getCourseTitle(price)"
                                    >
                                        {{ getCourseTitle(price) }}
                                    </span>

                                    <span
                                        v-if="getCourseSubtitle(price)"
                                        class="text-[10px] text-slate-600 dark:text-slate-300"
                                    >
                                        {{ getCourseSubtitle(price) }}
                                    </span>

                                    <span
                                        class="text-[10px] text-slate-500 dark:text-slate-400"
                                    >
                                        {{ getCourseSlug(price) || '—' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Currency -->
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <div
                                    class="font-semibold text-teal-700 dark:text-teal-300"
                                    :title="getCurrencyName(price)"
                                >
                                    {{ getCurrencyCode(price) }}
                                </div>
                            </td>

                            <!-- Regular price -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-baseline justify-center gap-1">
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ money(price.price) }}
                                        </span>

                                        <span
                                            v-if="money(price.price) !== '—'"
                                            class="text-[12px] opacity-70 text-gray-700 dark:text-gray-300"
                                        >
                                            {{ getCurrencySymbol(price) }}
                                        </span>
                                    </div>

                                    <div
                                        v-if="Number(price.compare_at_price) > 0"
                                        class="flex items-baseline justify-center gap-1"
                                    >
                                        <span
                                            class="text-xs line-through opacity-75
                                                   text-gray-500 dark:text-gray-400"
                                        >
                                            {{ money(price.compare_at_price) }}
                                        </span>

                                        <span
                                            class="text-[10px] opacity-70
                                                   text-gray-500 dark:text-gray-400"
                                        >
                                            {{ getCurrencySymbol(price) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Effective / sale price -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <div class="text-orange-700 dark:text-orange-300">
                                        {{ money(price.effective_price) }}

                                        <span
                                            v-if="money(price.effective_price) !== '—'"
                                            class="text-[12px] opacity-70 text-gray-700 dark:text-gray-300"
                                        >
                                            {{ getCurrencySymbol(price) }}
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

                            <!-- Period -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-xs text-center">
                                    <div class="text-blue-700 dark:text-blue-300">
                                        {{ dateShort(price.starts_at) }}
                                    </div>

                                    <div class="text-slate-400">
                                        —
                                    </div>

                                    <div class="text-blue-700 dark:text-blue-300">
                                        {{ dateShort(price.ends_at) }}
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1">
                                    <ActivityToggle
                                        :is-active="price.activity"
                                        :title="price.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', price)"
                                    />

                                    <IconEdit
                                        :href="route('admin.schoolCoursePrices.edit', {
                                            schoolCoursePrice: price.id,
                                        })"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', price)"
                                    />
                                </div>
                            </td>

                            <!-- Select -->
                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-400"
                                    :checked="selectedPrices.includes(price.id)"
                                    @change="emit('toggle-select', price.id)"
                                />
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
