<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    storefronts: {
        type: Array,
        default: () => [],
    },
    selectedStorefronts: {
        type: Array,
        default: () => [],
    },
})

const emits = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/** Локальная копия для vuedraggable */
const localStorefronts = ref([])

watch(
    () => props.storefronts,
    (newVal) => {
        localStorefronts.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localStorefronts.value.map((storefront) => storefront.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localStorefronts.value.map((storefront) => storefront.id)
    emits('toggle-all', { ids, checked })
}

const getCompanyLabel = (storefront) => {
    if (!storefront.company) return '—'
    return storefront.company.name || storefront.company.brand_name || `ID: ${storefront.company.id}`
}

const getCompanyBrandLabel = (storefront) => {
    return storefront.company?.brand_name || '—'
}

const getCompanySlugLabel = (storefront) => {
    return storefront.company?.slug || '—'
}

const getCurrencyLabel = (storefront) => {
    if (!storefront.default_currency) return '—'

    const title = storefront.default_currency.title || '—'
    const code = storefront.default_currency.code || ''
    const symbol = storefront.default_currency.symbol || ''

    return [title, code, symbol].filter(Boolean).join(' / ')
}

const getDomainLabel = (storefront) => {
    return storefront.domain || '—'
}

const getSubdomainLabel = (storefront) => {
    return storefront.subdomain || '—'
}

const getPrimaryHostLabel = (storefront) => {
    return storefront.primary_host || '—'
}

const getLocaleLabel = (storefront) => {
    return storefront.default_locale || '—'
}

const getNoteLabel = (storefront) => {
    return storefront.note || '—'
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedStorefronts.length }}
            </div>

            <div class="flex items-center space-x-2">
                <label
                    class="flex items-center text-xs
                           text-slate-600 dark:text-slate-200 cursor-pointer"
                >
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <div v-if="localStorefronts.length" class="p-3">
            <draggable
                tag="div"
                v-model="localStorefronts"
                @end="handleDragEnd"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: storefront }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель -->
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle text-slate-400
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
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`sort: ${storefront.sort}`"
                                >
                                    ID: {{ storefront.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400"
                                    :class="storefront.is_main
                                        ? 'bg-emerald-500 text-white'
                                        : 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100'"
                                >
                                    {{ storefront.is_main ? t('main') : t('regular') }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedStorefronts.includes(storefront.id)"
                                    @change="$emit('toggle-select', storefront.id)"
                                />
                            </div>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div
                                class="text-sm font-semibold text-center
                                       text-sky-700 dark:text-sky-200 line-clamp-2 break-all"
                                :title="storefront.slug"
                            >
                                {{ storefront.slug }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-2"
                                :title="getCompanyLabel(storefront)"
                            >
                                <span class="font-medium">{{ t('marketCompany') }}:</span>
                                {{ getCompanyLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getCompanyBrandLabel(storefront)"
                            >
                                <span class="font-medium">{{ t('brand') }}:</span>
                                {{ getCompanyBrandLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-indigo-700 dark:text-indigo-300 line-clamp-1 break-all"
                                :title="getCompanySlugLabel(storefront)"
                            >
                                <span class="font-medium">{{ t('slug') }}:</span>
                                {{ getCompanySlugLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1 break-all"
                                :title="getDomainLabel(storefront)"
                            >
                                <span class="font-medium">Domain:</span>
                                {{ getDomainLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1 break-all"
                                :title="getSubdomainLabel(storefront)"
                            >
                                <span class="font-medium">Subdomain:</span>
                                {{ getSubdomainLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-teal-700 dark:text-teal-300 line-clamp-1 break-all"
                                :title="getPrimaryHostLabel(storefront)"
                            >
                                <span class="font-medium">Host:</span>
                                {{ getPrimaryHostLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getLocaleLabel(storefront)"
                            >
                                <span class="font-medium">{{ t('locale') }}:</span>
                                {{ getLocaleLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getCurrencyLabel(storefront)"
                            >
                                <span class="font-medium">{{ t('currency') }}:</span>
                                {{ getCurrencyLabel(storefront) }}
                            </div>

                            <div
                                class="text-[11px] text-center font-semibold
                                       text-amber-600 dark:text-amber-200 line-clamp-2 break-words"
                                :title="getNoteLabel(storefront)"
                            >
                                <span class="font-medium">{{ t('note') }}:</span>
                                {{ getNoteLabel(storefront) }}
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="storefront.activity"
                                    @toggle-activity="$emit('toggle-activity', storefront)"
                                    :title="storefront.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.marketStorefronts.edit', storefront.id)" />
                                <DeleteIconButton
                                    @delete="$emit('delete', storefront.id, storefront.slug)"
                                />
                            </div>
                        </div>
                    </div>
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
