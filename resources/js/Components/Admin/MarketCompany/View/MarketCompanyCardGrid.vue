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

/** Локальная копия для vuedraggable */
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

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localCompanies.value.map((company) => company.id)
    emits('toggle-all', { ids, checked })
}

const getOwnerLabel = (company) => {
    if (!company.owner) return '—'
    return company.owner.name || company.owner.email || `ID: ${company.owner.id}`
}

const getLocationLabel = (company) => {
    const parts = []

    if (company.country) parts.push(company.country)
    if (company.city) parts.push(company.city)

    return parts.length ? parts.join(' / ') : '—'
}

const getBrandLabel = (company) => {
    return company.brand_name || '—'
}

const getLegalLabel = (company) => {
    return company.legal_name || '—'
}

const getEmailLabel = (company) => {
    return company.email || '—'
}

const getPhoneLabel = (company) => {
    return company.phone || '—'
}

const getBinIinLabel = (company) => {
    return company.bin_iin || '—'
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
                {{ t('selected') }}: {{ selectedCompanies.length }}
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

        <div v-if="localCompanies.length" class="p-3">
            <draggable
                tag="div"
                v-model="localCompanies"
                @end="handleDragEnd"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: company }">
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
                                    :title="`sort: ${company.sort}`"
                                >
                                    ID: {{ company.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase bg-blue-500 text-white"
                                >
                                    {{ getBrandLabel(company) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedCompanies.includes(company.id)"
                                    @change="$emit('toggle-select', company.id)"
                                />
                            </div>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div
                                class="text-sm font-semibold text-center
                                       text-sky-700 dark:text-sky-200 line-clamp-2"
                                :title="company.name"
                            >
                                {{ company.name }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getBrandLabel(company)"
                            >
                                <span class="font-medium">{{t('brand')}}:</span>
                                {{ getBrandLabel(company) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getLegalLabel(company)"
                            >
                                {{ getLegalLabel(company) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getBinIinLabel(company)"
                            >
                                <span class="font-medium">БИН / ИИН:</span>
                                {{ getBinIinLabel(company) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getEmailLabel(company)"
                            >
                                <span class="font-medium">Email:</span>
                                {{ getEmailLabel(company) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="getPhoneLabel(company)"
                            >
                                <span class="font-medium">{{ t('phone') }}:</span>
                                {{ getPhoneLabel(company) }}
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="company.messenger_type || '—'"
                            >
                                <span class="font-medium">
                                    {{ company.messenger_type || '—' }}:
                                </span>
                                <span v-if="company.messenger_contact">
                                    {{ company.messenger_contact }}
                                </span>
                            </div>

                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                :title="company.slug || '—'"
                            >
                                <span class="font-medium">
                                    {{t('slug')}}:
                                </span>
                                {{ company.slug || '—' }}
                            </div>

                            <div
                                class="text-[11px] text-center font-semibold
                                       text-teal-700 dark:text-teal-300 line-clamp-1"
                                :title="getLocationLabel(company)"
                            >
                                {{ getLocationLabel(company) }}
                            </div>

                            <div
                                class="text-[11px] text-center font-semibold
                                       text-amber-600 dark:text-amber-200 line-clamp-1"
                                :title="getOwnerLabel(company)"
                            >
                                <span class="font-medium">{{ t('owner') }}:</span>
                                {{ getOwnerLabel(company) }}
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="company.activity"
                                    @toggle-activity="$emit('toggle-activity', company)"
                                    :title="company.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.marketCompanies.edit', company.id)" />
                                <DeleteIconButton
                                    @delete="$emit('delete', company.id, company.name)"
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
