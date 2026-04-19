<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
    selectedCompanies: {
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
const localCompanies = ref([])

watch(
    () => props.companies,
    (newVal) => {
        localCompanies.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localCompanies.value.map((company) => company.id)
    emits('update-sort-order', newOrderIds)
}

/**
 * Массовый выбор
 */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localCompanies.value.map((company) => company.id)
    emits('toggle-all', { ids, checked })
}

/**
 * Формат владельца
 */
const getOwnerLabel = (company) => {
    if (!company.owner) return '—'

    return company.owner.name || company.owner.email || `ID: ${company.owner.id}`
}

/**
 * Формат местоположения
 */
const getLocationLabel = (company) => {
    const parts = []

    if (company.country) parts.push(company.country)
    if (company.city) parts.push(company.city)

    return parts.length ? parts.join(' / ') : '—'
}

/**
 * Телефон или заглушка
 */
const getPhoneLabel = (company) => {
    return company.phone || '—'
}

/**
 * Email или заглушка
 */
const getEmailLabel = (company) => {
    return company.email || '—'
}

/**
 * Бренд или заглушка
 */
const getBrandLabel = (company) => {
    return company.brand_name || '—'
}

/**
 * Юр. название или заглушка
 */
const getLegalLabel = (company) => {
    return company.legal_name || '—'
}

/**
 * BIN/IIN или заглушка
 */
const getBinIinLabel = (company) => {
    return company.bin_iin || '—'
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <!-- верхняя панель -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedCompanies.length }}
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
                v-if="companies.length > 0"
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

                    <th class="px-2 py-3 min-w-[100px]">
                        <div class="font-semibold text-left">
                            {{ t('owner') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[100px]">
                        <div class="font-semibold text-left">
                            {{t('country')}} / {{t('city')}}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[100px]">
                        <div class="font-semibold text-left">
                            {{ t('marketCompany') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 min-w-[220px]">
                        <div class="font-semibold text-left">
                            {{ t('contacts') }}
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
                    v-model="localCompanies"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: company }">
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
                                    :title="`sort: ${company.sort}`"
                                >
                                    <div>{{ company.id }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        #{{ company.sort }}
                                    </div>
                                </div>
                            </td>

                            <!-- owner -->
                            <td class="px-2 py-3">
                                <div
                                    class="text-xs text-amber-600 dark:text-amber-200 break-words"
                                    :title="getOwnerLabel(company)"
                                >
                                    {{ getOwnerLabel(company) }}
                                </div>
                            </td>

                            <!-- location / bin -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="text-teal-700 dark:text-teal-300 break-words"
                                        :title="getLocationLabel(company)"
                                    >
                                        {{ getLocationLabel(company) }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="getBinIinLabel(company)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            БИН:
                                        </span>
                                        {{ getBinIinLabel(company) }}
                                    </div>
                                </div>
                            </td>

                            <!-- company -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="font-semibold text-sky-700 dark:text-sky-200 break-words"
                                        :title="company.name"
                                    >
                                        {{ company.name }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="getBrandLabel(company)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{ t('brand') }}:
                                        </span>
                                        {{ getBrandLabel(company) }}
                                    </div>

                                    <div
                                        class="text-slate-600 dark:text-slate-300 break-words"
                                        :title="getLegalLabel(company)"
                                    >
                                        {{ getLegalLabel(company) }}
                                    </div>

                                    <div
                                        class="text-indigo-700 dark:text-indigo-300 break-all"
                                        :title="company.slug || '—'"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{t('slug')}}:
                                        </span>
                                        {{ company.slug || '—' }}
                                    </div>
                                </div>
                            </td>

                            <!-- contacts -->
                            <td class="px-2 py-3">
                                <div class="flex flex-col space-y-1 text-xs">
                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-all"
                                        :title="getEmailLabel(company)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            Email:
                                        </span>
                                        {{ getEmailLabel(company) }}
                                    </div>

                                    <div
                                        class="text-slate-700 dark:text-slate-200 break-words"
                                        :title="getPhoneLabel(company)"
                                    >
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            {{ t('phone') }}:
                                        </span>
                                        {{ getPhoneLabel(company) }}
                                    </div>

                                    <div
                                        class="text-amber-600 dark:text-amber-200 break-words"
                                        :title="company.messenger_type || '—'"
                                    >
                                        {{ company.messenger_type || '—' }}:
                                        <span v-if="company.messenger_contact">
                                            {{ company.messenger_contact }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="company.activity"
                                        @toggle-activity="$emit('toggle-activity', company)"
                                        :title="company.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit
                                        :href="route('admin.marketCompanies.edit', company.id)"
                                    />
                                    <DeleteIconButton
                                        @delete="$emit('delete', company.id, company.name)"
                                    />
                                </div>
                            </td>

                            <!-- checkbox -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedCompanies.includes(company.id)"
                                        @change="$emit('toggle-select', company.id)"
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
