<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import InlineRateEditor from '@/Components/Admin/Currency/Buttons/InlineRateEditor.vue'
import SetDefaultButton from '@/Components/Admin/Currency/Buttons/SetDefaultButton.vue'
import { Link } from '@inertiajs/vue3'

const { t } = useI18n()

const props = defineProps({
    currencies: { type: Array, default: () => [] },
    selectedCurrencies: { type: Array, default: () => [] }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'set-default',
    'save-rate'
])

/** Локальная копия для vuedraggable (как у курсов) */
const localCurrencies = ref([])

watch(
    () => props.currencies,
    (newVal) => {
        localCurrencies.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Drag end: отдаем массив ID вверх */
const handleDragEnd = () => {
    const newOrderIds = localCurrencies.value.map(c => c.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовый выбор (как у курсов) */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localCurrencies.value.map(c => c.id)
    emits('toggle-all', { ids, checked })
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedCurrencies.length }}
            </div>

            <label
                v-if="localCurrencies.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localCurrencies.length" class="p-3">
            <draggable
                tag="div"
                v-model="localCurrencies"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: currency }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель карточки -->
                        <div class="flex items-center justify-between px-2 py-1
                                    border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-slate-200"
                                    :title="`sort: ${currency.sort ?? '—'}`"
                                >
                                    ID: {{ currency.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- code badge -->
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400"
                                    :class="currency.is_default
                                        ? 'bg-teal-100 dark:bg-teal-900/60 ' +
                                         'text-teal-600 dark:text-teal-400'
                                        : 'bg-indigo-100 dark:bg-indigo-900/60 ' +
                                         'text-indigo-700 dark:text-indigo-300'"
                                    :title="t('code')"
                                >
                                    {{ currency.code || '—' }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedCurrencies.includes(currency.id)"
                                    @change="$emit('toggle-select', currency.id)"
                                />
                            </div>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">

                            <!-- Название -->
                            <div
                                class="text-sm font-semibold text-center line-clamp-2"
                                :class="currency.is_default
                                        ? 'text-amber-600 dark:text-amber-400'
                                        : 'text-blue-800 dark:text-blue-200'"
                                :title="currency.name"
                            >
                                {{ currency.name || '—' }}
                            </div>

                            <div class="flex flex-row items-center justify-center gap-1">
                                <!-- Code + symbol -->
                                <div class="font-semibold text-[14px] text-center"
                                     :class="currency.is_default
                                        ? 'text-teal-600 dark:text-teal-400'
                                        : 'text-indigo-700 dark:text-indigo-300'">
                                    <span v-if="currency.symbol">{{ currency.symbol }} • </span>
                                    <span>{{ currency.code || '—' }}</span>
                                </div>
                                <Link
                                    :href="route('admin.currencies.rates.index', currency.id)"
                                    :title="t('currencyRates')"
                                    class="inline-flex items-center justify-center p-1 rounded
                                               text-blue-500 hover:text-blue-700
                                               dark:text-blue-300 dark:hover:text-blue-100"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                         viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z"></path>
                                        <path
                                            d="M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z"></path>
                                    </svg>
                                </Link>
                            </div>

                            <!-- Rate -->
                            <div
                                class="text-center text-[11px] mt-2
                                       text-slate-900 dark:text-slate-200"
                            >
                                {{ t('rate') }}:
                                <span class="font-semibold text-amber-600 dark:text-amber-400">
                                    {{ currency.rate_vs_default ?? '—' }}
                                </span>
                            </div>

                            <!-- Inline editor -->
                            <div class="mt-2">
                                <InlineRateEditor
                                    :currency="currency"
                                    @save="$emit('save-rate', $event)"
                                />
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <SetDefaultButton
                                    :disabled="currency.is_default"
                                    :title="currency.is_default ? t('mainCurrency') : t('makeMainCurrency')"
                                    @click="$emit('set-default', currency)"
                                />
                                <ActivityToggle
                                    :isActive="currency.activity"
                                    @toggle-activity="$emit('toggle-activity', currency)"
                                    :title="currency.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.currencies.edit', currency.id)" />
                                <DeleteIconButton @click="$emit('delete', currency.id, currency.name)" />
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
