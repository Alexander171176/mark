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

const getCourseShort = (price) => {
    return price?.course?.translation?.short
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

        <div
            v-if="localPrices.length"
            class="p-3"
        >
            <draggable
                v-model="localPrices"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: price }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Header -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
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
                                           border border-gray-400 bg-teal-100 dark:bg-teal-900/50
                                           text-teal-700 dark:text-teal-300"
                                    :title="getCurrencyName(price) || t('currency')"
                                >
                                    {{ getCurrencyCode(price) }}
                                </span>

                                <input
                                    type="checkbox"
                                    class="rounded border-slate-400"
                                    :checked="selectedPrices.includes(price.id)"
                                    @change="emit('toggle-select', price.id)"
                                />
                            </div>
                        </header>

                        <!-- Body -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <!-- Course -->
                            <div
                                class="text-xs font-semibold text-center
                                       text-sky-700 dark:text-sky-200"
                                :title="getCourseTitle(price)"
                            >
                                {{ getCourseTitle(price) }}

                                <span
                                    v-if="getCourseSubtitle(price)"
                                    class="block text-[10px] font-normal
                                           text-slate-600 dark:text-slate-300"
                                >
                                    {{ getCourseSubtitle(price) }}
                                </span>

                                <span
                                    v-if="getCourseSlug(price)"
                                    class="block text-[10px] font-normal
                                           text-gray-500 dark:text-gray-400"
                                >
                                    {{ getCourseSlug(price) }}
                                </span>
                            </div>

                            <!-- Short -->
                            <div
                                v-if="getCourseShort(price)"
                                class="border border-dashed border-slate-300
                                       dark:border-slate-600 px-2 py-1
                                       text-[10px] text-center
                                       text-slate-600 dark:text-slate-300"
                            >
                                {{ getCourseShort(price) }}
                            </div>

                            <!-- Currency -->
                            <div
                                class="text-[12px] font-semibold
                                       text-teal-700 dark:text-teal-300 text-center"
                            >
                                {{ getCurrencyName(price) }}

                                <span class="text-slate-400">
                                    —
                                </span>

                                {{ getCurrencyCode(price) }}
                            </div>

                            <!-- Prices -->
                            <div class="space-y-1 text-[11px] font-semibold">
                                <div
                                    v-if="Number(price.compare_at_price) > 0"
                                    class="text-center"
                                >
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('compareAtPrice') }}:
                                    </span>

                                    <span
                                        class="ml-1 line-through opacity-75
                                               text-slate-700 dark:text-slate-300"
                                    >
                                        {{ money(price.compare_at_price) }}
                                    </span>

                                    <span
                                        class="ml-1 text-[10px] opacity-70
                                               text-gray-700 dark:text-gray-300"
                                    >
                                        {{ getCurrencySymbol(price) }}
                                    </span>
                                </div>

                                <div class="text-center">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('price') }}:
                                    </span>

                                    <span class="ml-1 text-slate-700 dark:text-slate-300">
                                        {{ money(price.price) }}
                                    </span>

                                    <span
                                        class="ml-1 text-[10px] opacity-70
                                               text-gray-700 dark:text-gray-300"
                                    >
                                        {{ getCurrencySymbol(price) }}
                                    </span>
                                </div>

                                <div class="text-center">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('salePrice') }}:
                                    </span>

                                    <span class="ml-1 text-orange-700 dark:text-orange-300">
                                        {{ money(price.effective_price) }}
                                    </span>

                                    <span
                                        class="ml-1 text-[10px] opacity-70
                                               text-gray-700 dark:text-gray-300"
                                    >
                                        {{ getCurrencySymbol(price) }}
                                    </span>

                                    <span
                                        v-if="price.has_discount && price.discount_percent"
                                        class="ml-2 text-rose-600 dark:text-rose-300"
                                    >
                                        -{{ price.discount_percent }}%
                                    </span>
                                </div>

                                <div
                                    v-if="price.discount_amount !== null
                                        && price.discount_amount !== undefined
                                        && Number(price.discount_amount) > 0"
                                    class="text-center text-[10px]
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ t('discount') }}:
                                    {{ money(price.discount_amount) }}
                                    {{ getCurrencySymbol(price) }}
                                </div>
                            </div>

                            <!-- Dates -->
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

                        <!-- Footer -->
                        <footer
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
                        </footer>
                    </article>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
