<script setup>
/**
 * Таблица прайсов бандлов (аналог CoursePriceTable).
 * Отдаём наружу события:
 * - toggle-activity
 * - delete
 * - update-sort-order (drag&drop)
 * - toggle-select
 * - toggle-all
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

/**
 * Локальная копия нужна для vuedraggable (иначе мы будем мутировать props напрямую).
 */
const localPrices = ref([])

watch(
    () => props.prices,
    (newVal) => {
        localPrices.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Drag&drop завершён — отправляем новый порядок ID наверх */
const handleDragEnd = () => {
    const newOrderIds = localPrices.value.map(p => p.id)
    emits('update-sort-order', newOrderIds)
}

/** Выбор/снятие выбора всех на текущей странице */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localPrices.value.map(p => p.id)
    emits('toggle-all', { ids, checked })
}

/** Формат цены (как у курсов): если пусто — рисуем "—" */
const money = (v) => {
    if (v === null || v === undefined || v === '') return '—'
    return String(v)
}

/** Короткий формат даты */
const dateShort = (iso) => {
    if (!iso) return ' '
    const d = new Date(iso)
    if (isNaN(d)) return ' '
    return d.toLocaleDateString('ru-RU', { year: 'numeric', month: 'short', day: 'numeric' })
}

/** Подписи */
const bundleLabel = (p) => p?.bundle?.title || '—'
const currencyLabel = (p) => p?.currency?.code || '—'
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
                        <!-- drag handle header -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
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
                            {{ t('bundle') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap"
                        :title="t('currency')">
                        <div class="flex justify-center">
                            <!-- иконка валюты (как у курсов) -->
                            <svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24">
                                <path class="fill-current text-slate-700 dark:text-slate-100"
                                      d="M0,5V19H24V5ZM7,9.749a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366A.517.517,0,0,1,3.507,16c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,4.431,10H1.5A.5.5,0,0,1,1,9.5v-1A.5.5,0,0,1,1.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,12.431,10H9.5A.5.5,0,0,1,9,9.5v-1A.5.5,0,0,1,9.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,20.431,10H17.5a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5h5a.5.5,0,0,1,.5.5Z"></path>
                                <path class="fill-current text-slate-700 dark:text-slate-100"
                                      d="M1,1H23a1,1,0,0,1,1,1V3a0,0,0,0,1,0,0H0A0,0,0,0,1,0,3V2A1,1,0,0,1,1,1Z"></path>
                                <path class="fill-current text-slate-700 dark:text-slate-100"
                                      d="M0,21H24a0,0,0,0,1,0,0v1a1,1,0,0,1-1,1H1a1,1,0,0,1-1-1V21A0,0,0,0,1,0,21Z"></path>
                            </svg>
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('price') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('salePrice') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('compareAtPrice') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('effectivePrice') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-center"
                             :title="t('periodValidityPrice')">
                            <div class="flex justify-center">
                                <!-- иконка периода (как у курсов) -->
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-4 h-4" height="24" width="24" viewBox="0 0 24 24">
                                    <path class="fill-current text-sky-700 dark:text-sky-300"
                                          d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                                </svg>
                            </div>
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localPrices"
                    item-key="id"
                    handle=".drag-handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: p }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">

                            <!-- drag -->
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <button
                                    type="button"
                                    class="drag-handle
                                           text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>
                            </td>

                            <!-- id -->
                            <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[${p.sort ?? 0}]`"
                                >
                                    <span class="text-[11px] opacity-90">
                                        {{ p.id }}
                                    </span>
                                </div>
                            </td>

                            <!-- bundle -->
                            <td class="px-2 py-3 first:pl-8 last:pr-8">
                                <div class="flex flex-col">
                                    <span class="text-xs text-sky-700 dark:text-sky-200">
                                        {{ bundleLabel(p) }}
                                        <span v-if="p.bundle?.locale"
                                              class="uppercase text-amber-500 dark:text-amber-400">
                                            · [{{ p.bundle.locale }}]
                                        </span>
                                    </span>
                                    <span class="text-[10px] text-slate-700 dark:text-slate-300">
                                        {{ p.bundle?.slug || '—' }}
                                    </span>
                                </div>
                            </td>

                            <!-- currency -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center
                                             text-amber-700 dark:text-amber-300">
                                    {{ currencyLabel(p) }}
                                </div>
                            </td>

                            <!-- price -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-baseline justify-center gap-1">
                                    <div class="text-amber-700 dark:text-amber-300">
                                        {{ money(p.price) }}
                                    </div>
                                    <div v-if="money(p.price) !== '—'"
                                         class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ p.currency?.symbol || p.currency?.code }}
                                    </div>
                                </div>
                            </td>

                            <!-- sale -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-baseline justify-center gap-1">
                                    <div class="text-teal-700 dark:text-teal-300">
                                        {{ money(p.sale_price) }}
                                    </div>
                                    <div v-if="money(p.sale_price) !== '—'"
                                         class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ p.currency?.symbol || p.currency?.code }}
                                    </div>
                                </div>
                            </td>

                            <!-- compare -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-baseline justify-center gap-1">
                                    <div class="text-indigo-700 dark:text-indigo-300">
                                        {{ money(p.compare_at_price) }}
                                    </div>
                                    <div v-if="money(p.compare_at_price) !== '—'"
                                         class="text-[11px] opacity-70 text-gray-700 dark:text-gray-300">
                                        {{ p.currency?.symbol || p.currency?.code }}
                                    </div>
                                </div>
                            </td>

                            <!-- effective (в текущем UI показываем % скидки как у курсов) -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div v-if="p.has_discount && p.discount_percent"
                                     class="ml-2 text-center text-[11px]
                                            text-rose-600 dark:text-rose-300">
                                    -{{ p.discount_percent }}%
                                </div>
                            </td>

                            <!-- period -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-[12px] text-center">
                                    <div class="text-sky-700 dark:text-sky-300">
                                        {{ dateShort(p.starts_at) }}
                                    </div>
                                    <div class="text-slate-400">—</div>
                                    <div class="text-sky-700 dark:text-sky-300">
                                        {{ dateShort(p.ends_at) }}
                                    </div>
                                </div>
                            </td>

                            <!-- actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1">
                                    <ActivityToggle
                                        :isActive="p.activity"
                                        @toggle-activity="$emit('toggle-activity', p)"
                                        :title="p.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit :href="route('admin.bundlePrices.edit', p.id)" />
                                    <DeleteIconButton @delete="$emit('delete', p.id)" />
                                </div>
                            </td>

                            <!-- checkbox -->
                            <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedPrices.includes(p.id)"
                                        @change="$emit('toggle-select', p.id)"
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
