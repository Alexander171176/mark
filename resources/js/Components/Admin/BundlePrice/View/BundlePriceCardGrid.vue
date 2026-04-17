<script setup>
/**
 * Карточный вид прайсов бандлов (аналог CoursePriceCardGrid).
 * Тут та же логика:
 * - localPrices для draggable
 * - события наружу такие же
 */
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

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

/** Локальная копия под draggable */
const localPrices = ref([])

watch(
    () => props.prices,
    (newVal) => {
        localPrices.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Drag&drop завершён — отправляем новый порядок */
const handleDragEnd = () => {
    const newOrderIds = localPrices.value.map(p => p.id)
    emits('update-sort-order', newOrderIds)
}

/** Выбор всех на текущей странице */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localPrices.value.map(p => p.id)
    emits('toggle-all', { ids, checked })
}

/** Формат денег */
const money = (v) => {
    if (v === null || v === undefined || v === '') return '—'
    return String(v)
}

/** Короткая дата */
const dateShort = (iso) => {
    if (!iso) return '—'
    const d = new Date(iso)
    if (isNaN(d)) return '—'
    return d.toLocaleDateString('ru-RU', { year: 'numeric', month: 'short', day: 'numeric' })
}

/** Подписи для бандла/валюты */
const bundleTitle = (p) => p?.bundle?.title || '—'
const bundleSlug = (p) => p?.bundle?.slug || ''
const currencyCode = (p) => p?.currency?.code || '—'
const currencyName = (p) => p?.currency?.name || ''
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedPrices.length }}
            </div>

            <label
                v-if="localPrices.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localPrices.length" class="p-3">
            <draggable
                tag="div"
                v-model="localPrices"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: p }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верх карточки -->
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
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`${t('sort')}: ${p.sort ?? 0}`"
                                >
                                    ID: {{ p.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-emerald-100 dark:bg-emerald-900/50
                                           text-emerald-700 dark:text-emerald-300"
                                    :title="t('currency')"
                                >
                                    {{ currencyCode(p) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedPrices.includes(p.id)"
                                    @change="$emit('toggle-select', p.id)"
                                />
                            </div>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <!-- бандл -->
                            <a
                                v-if="bundleSlug(p)"
                                :href="`/bundles/${encodeURIComponent(bundleSlug(p))}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-xs font-semibold
                                       text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="`${bundleSlug(p)}`"
                            >
                                {{ bundleTitle(p) }}
                                <span v-if="p.bundle?.locale"
                                      class="uppercase text-gray-500 dark:text-gray-400">
                                    · [{{ p.bundle.locale }}]
                                </span>
                            </a>

                            <div v-else class="text-sm font-semibold text-center
                                               text-sky-700 dark:text-sky-200">
                                {{ bundleTitle(p) }}
                                <span v-if="p.bundle?.locale"
                                      class="uppercase text-gray-500 dark:text-gray-400">
                                    · [{{ p.bundle.locale }}]
                                </span>
                            </div>

                            <!-- валюта -->
                            <div class="text-[12px] font-semibold
                                        text-amber-700 dark:text-amber-200 text-center">
                                {{ currencyName(p) }}
                                <span class="text-slate-400">—</span>
                                {{ currencyCode(p) }}
                            </div>

                            <!-- цены -->
                            <div class="text-center">
                                <div class="text-[11px] font-semibold">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('price') }}:
                                    </span>
                                    <span class="text-amber-700 dark:text-amber-300 mr-1">
                                        {{ money(p.price) }}
                                    </span>
                                    <span v-if="money(p.price) !== '—'"
                                          class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ p.currency?.symbol || p.currency?.code }}
                                    </span>
                                </div>

                                <div v-if="p.sale_price" class="text-[11px] font-semibold">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('salePrice') }}:
                                    </span>
                                    <span class="text-teal-700 dark:text-teal-300 mr-1">
                                        {{ money(p.sale_price) }}
                                    </span>
                                    <span v-if="money(p.sale_price) !== '—'"
                                          class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ p.currency?.symbol || p.currency?.code }}
                                    </span>
                                </div>

                                <div v-if="p.compare_at_price" class="text-[11px] font-semibold">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('compareAtPrice') }}:
                                    </span>
                                    <span class="text-indigo-700 dark:text-indigo-300 mr-1">
                                        {{ money(p.compare_at_price) }}
                                    </span>
                                    <span v-if="money(p.compare_at_price) !== '—'"
                                          class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ p.currency?.symbol || p.currency?.code }}
                                    </span>
                                </div>

                                <!-- скидка -->
                                <div class="text-[11px] font-semibold">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('effectivePrice') }}:
                                    </span>
                                    <span v-if="p.has_discount && p.discount_percent"
                                          class="ml-2 text-rose-600 dark:text-rose-300">
                                        -{{ p.discount_percent }} %
                                    </span>
                                </div>
                            </div>

                            <!-- период -->
                            <div class="text-[9px] text-center font-semibold">
                                <div>
                                    <span class="text-slate-700 dark:text-slate-200">
                                        {{ t('priceStartsAt') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                        {{ dateShort(p.starts_at) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-700 dark:text-slate-200">
                                        {{ t('priceEndsAt') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                        {{ dateShort(p.ends_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="p.activity"
                                    @toggle-activity="$emit('toggle-activity', p)"
                                    :title="p.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.bundlePrices.edit', p.id)" />
                                <DeleteIconButton @delete="$emit('delete', p.id)" />
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
