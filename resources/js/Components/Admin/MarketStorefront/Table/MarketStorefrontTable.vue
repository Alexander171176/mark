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

/**
 * Локальная копия для drag&drop
 */
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

/**
 * Массовый выбор
 */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localStorefronts.value.map((storefront) => storefront.id)
    emits('toggle-all', { ids, checked })
}

/**
 * Формат компании
 */
const getCompanyLabel = (storefront) => {
    if (!storefront.company) return '—'
    return storefront.company.name || storefront.company.brand_name || `ID: ${storefront.company.id}`
}

/**
 * Формат бренда компании
 */
const getCompanyBrandLabel = (storefront) => {
    return storefront.company?.brand_name || '—'
}

/**
 * Формат slug компании
 */
const getCompanySlugLabel = (storefront) => {
    return storefront.company?.slug || '—'
}

/**
 * Формат валюты
 */
const getCurrencyLabel = (storefront) => {
    if (!storefront.default_currency) return '—'

    const title = storefront.default_currency.title || '—'
    const code = storefront.default_currency.code || ''
    const symbol = storefront.default_currency.symbol || ''

    return [title, code, symbol].filter(Boolean).join(' / ')
}

/**
 * Формат доменных данных
 */
const getDomainLabel = (storefront) => {
    return storefront.domain || '—'
}

const getSubdomainLabel = (storefront) => {
    return storefront.subdomain || '—'
}

const getPrimaryHostLabel = (storefront) => {
    return storefront.primary_host || '—'
}

/**
 * Формат локали
 */
const getLocaleLabel = (storefront) => {
    return storefront.default_locale || '—'
}

/**
 * Формат заметки
 */
const getNoteLabel = (storefront) => {
    return storefront.note || '—'
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
                {{ t('selected') }}: {{ selectedStorefronts.length }}
            </div>

            <div class="flex items-center space-x-2">
                <label
                    class="flex items-center text-xs text-slate-600
                           dark:text-slate-200 cursor-pointer"
                >
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="storefronts.length > 0"
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
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[220px]">
                        <div class="font-semibold text-left">
                            {{ t('marketStorefront') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[220px]">
                        <div class="font-semibold text-left">
                            {{ t('marketCompany') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[220px]">
                        <div class="font-semibold text-left">
                            {{ t('hosts') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[180px]">
                        <div class="font-semibold text-left">
                            {{ t('locale') }} / {{ t('currency') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[180px]">
                        <div class="font-semibold text-left">
                            {{ t('note') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
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
                    v-model="localStorefronts"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: storefront }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800 align-top"
                        >
                            <!-- drag -->
                            <td class="px-2 py-2 text-center cursor-move handle">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- id -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs text-slate-800 dark:text-blue-200"
                                    :title="`sort: ${storefront.sort}`"
                                >
                                    <div>{{ storefront.id }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        #{{ storefront.sort }}
                                    </div>
                                </div>
                            </td>

                            <!-- storefront -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="font-semibold text-sky-700 dark:text-sky-200 break-all"
                                        :title="storefront.slug"
                                    >
                                        {{ storefront.slug }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="storefront.is_main ? 'Главная витрина' : 'Обычная витрина'"
                                    >
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-medium"
                                            :class="storefront.is_main
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                                : 'bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200'"
                                        >
                                            {{ storefront.is_main ? t('main') : t('regular') }}
                                        </span>
                                    </div>

                                    <div
                                        class="text-slate-600 dark:text-slate-300 break-words"
                                        :title="storefront.activity ? t('enabled') : t('disabled')"
                                    >
                                        {{ storefront.activity ? t('enabled') : t('disabled') }}
                                    </div>
                                </div>
                            </td>

                            <!-- company -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="font-semibold text-indigo-700 dark:text-indigo-300 break-words"
                                        :title="getCompanyLabel(storefront)"
                                    >
                                        {{ getCompanyLabel(storefront) }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="getCompanyBrandLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{ t('brand') }}:
                                        </span>
                                        {{ getCompanyBrandLabel(storefront) }}
                                    </div>

                                    <div
                                        class="text-amber-700 dark:text-amber-300 break-all"
                                        :title="getCompanySlugLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{ t('slug') }}:
                                        </span>
                                        {{ getCompanySlugLabel(storefront) }}
                                    </div>
                                </div>
                            </td>

                            <!-- hosts -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-all"
                                        :title="getDomainLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            Domain:
                                        </span>
                                        {{ getDomainLabel(storefront) }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-all"
                                        :title="getSubdomainLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            Subdomain:
                                        </span>
                                        {{ getSubdomainLabel(storefront) }}
                                    </div>

                                    <div
                                        class="text-teal-700 dark:text-teal-300 break-all"
                                        :title="getPrimaryHostLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            Host:
                                        </span>
                                        {{ getPrimaryHostLabel(storefront) }}
                                    </div>
                                </div>
                            </td>

                            <!-- locale / currency -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="getLocaleLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{ t('locale') }}:
                                        </span>
                                        {{ getLocaleLabel(storefront) }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="getCurrencyLabel(storefront)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{ t('currency') }}:
                                        </span>
                                        {{ getCurrencyLabel(storefront) }}
                                    </div>
                                </div>
                            </td>

                            <!-- note -->
                            <td class="px-2 py-3">
                                <div
                                    class="text-xs text-slate-700 dark:text-slate-200 break-words"
                                    :title="getNoteLabel(storefront)"
                                >
                                    {{ getNoteLabel(storefront) }}
                                </div>
                            </td>

                            <!-- actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="storefront.activity"
                                        @toggle-activity="$emit('toggle-activity', storefront)"
                                        :title="storefront.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit
                                        :href="route('admin.marketStorefronts.edit', storefront.id)"
                                    />
                                    <DeleteIconButton
                                        @delete="$emit('delete', storefront.id, storefront.slug)"
                                    />
                                </div>
                            </td>

                            <!-- checkbox -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedStorefronts.includes(storefront.id)"
                                        @change="$emit('toggle-select', storefront.id)"
                                    />
                                </div>
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
