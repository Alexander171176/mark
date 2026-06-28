<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import LeftToggle from '@/Components/Admin/UI/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/UI/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/UI/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t } = useI18n()

const props = defineProps({
    companies: { type: Array, default: () => [] },
    selectedCompanies: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

const emits = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

const localCompanies = ref([])

watch(
    () => props.companies,
    (newVal) => {
        localCompanies.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localCompanies.value.map(company => company.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localCompanies.value.map(company => company.id),
        checked: event.target.checked,
    })
}

const allSelected = () => {
    return localCompanies.value.length
        && localCompanies.value.every(company => props.selectedCompanies.includes(company.id))
}

const companyTranslation = (company) => company?.translation || {}

const companyTitle = (company) => {
    return companyTranslation(company)?.title
        || company?.legal_name
        || `ID: ${company?.id}`
}

const companyShort = (company) => companyTranslation(company)?.short || ''

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}
const getStatusLabel = (status) => t(statusLabelKeyMap[status] || status || 'no')

const ownerName = (company) => company?.owner?.name || t('noData')
const ownerEmail = (company) => company?.owner?.email || ''

const ownerTitle = (company) => {
    const owner = company?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (company) => {
    return company?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

const logoUrl = (company) => {
    if (!company?.logo) {
        return '/storage/market/companies/logos/default-image-light.png'
    }

    return company.logo.startsWith('/storage/')
        ? company.logo
        : `/storage/${company.logo}`
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (isNaN(date)) return ''

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const truncateText = (text, maxLength = 80) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

const companyTypeLabel = (type) => {
    if (type === 'entrepreneur') return 'ИП'
    if (type === 'individual') return 'Физ. лицо'

    return 'Компания'
}

const vatLabel = (company) => {
    if (!company?.vat_enabled) return 'Без НДС'

    return company?.vat_rate
        ? `НДС ${company.vat_rate}%`
        : 'С НДС'
}

const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

const openedOwnerBlocks = ref([])

const isOwnerBlockOpen = (companyId) => {
    return openedOwnerBlocks.value.includes(companyId)
}

const toggleOwnerBlock = (companyId) => {
    if (isOwnerBlockOpen(companyId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(id => id !== companyId)
        return
    }

    openedOwnerBlocks.value.push(companyId)
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

            <label
                v-if="localCompanies.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="allSelected()"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div v-if="localCompanies.length" class="p-3">
            <draggable
                tag="div"
                v-model="localCompanies"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: company }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
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
                                :title="`[${company.sort}] / ${formatDate(company.published_at)}`"
                                >
                                    ID: {{ company.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                            :title="isOwnerBlockOpen(company.id) ? t('hideOwner') : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(company.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOwnerBlockOpen(company.id) }"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm border font-semibold"
                                    :class="moderationBadge(company.moderation_status).class"
                            :title="company.moderation_note && company.moderated_at
                                ? `${company.moderation_note} [${formatDate(company.moderated_at)}]`
                                : null"
                                >
                                    {{ moderationBadge(company.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedCompanies.includes(company.id)"
                                    @change="$emit('toggle-select', company.id)"
                                />
                            </div>
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <div
                                v-show="isOwnerBlockOpen(company.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(company)"
                                    :title="ownerTitle(company)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('owner')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(company)"
                                >
                                    {{ ownerName(company) }}
                                </div>

                                <div
                                    v-if="ownerEmail(company)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(company)"
                                >
                                    {{ ownerEmail(company) }}
                                </div>

                                <div
                                    v-if="company.show_from_at"
                                    class="flex flex-col items-center justify-center
                                       text-center text-[10px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('show') }}: {{ company.show_from_at }} / {{ company.show_to_at }}
                                </div>

                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center
                                       text-center text-[10px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDate(company.published_at) }}
                                </div>
                            </div>

                            <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                                <img
                                    :src="logoUrl(company)"
                                    :alt="companyTitle(company)"
                                    :title="companyTitle(company)"
                                    class="h-32 w-full object-cover"
                                />
                            </div>

                            <a
                                :href="`/market/companies/${encodeURIComponent(company.url)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-xs font-semibold
                                       text-sky-700 dark:text-sky-200 hover:underline
                                       hover:text-amber-700 dark:hover:text-amber-200
                                       line-clamp-2 text-center"
                            >
                                {{ truncateText(companyTitle(company), 90) }}
                            </a>

                            <div class="text-center text-[11px] text-slate-500 dark:text-slate-300">
                                {{ truncateText(company.legal_name, 90) }}
                            </div>

                            <div class="flex justify-center flex-wrap gap-1 text-[10px]">
                                <span
                                    class="px-2 py-0.5 rounded-sm border
                                           bg-indigo-100 text-indigo-700 border-indigo-300
                                           dark:bg-indigo-900/40 dark:text-indigo-300"
                                >
                                    {{ companyTypeLabel(company.company_type) }}
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm border
                                           bg-cyan-100 text-cyan-700 border-cyan-300
                                           dark:bg-cyan-900/40 dark:text-cyan-300"
                                >
                                    {{ vatLabel(company) }}
                                </span>
                            </div>

                            <div
                                class="font-semibold text-[12px] text-center
                                       text-teal-700 dark:text-teal-300"
                            >
                                {{ truncateText(companyShort(company), 120) }}
                            </div>

                            <div
                                class="grid grid-cols-1 gap-0.5 text-[11px]
                                       text-slate-600 dark:text-slate-300"
                            >
                                <div class="text-center">
                                    <span class="font-semibold">БИН/ИИН:</span>
                                    {{ company.bin_iin || '—' }}
                                </div>

                                <div class="text-center">
                                    <span class="font-semibold">{{ t('city') }}:</span>
                                    {{ company.city || '—' }}
                                    <span v-if="company.region">/ {{ company.region }}</span>
                                </div>

                                <div class="text-center">
                                    <span class="font-semibold">{{ t('contacts') }}:</span>
                                    {{ company.phone || '—' }}
                                </div>

                                <div
                                    v-if="company.email"
                                    class="text-center line-clamp-1"
                                    :title="company.email"
                                >
                                    {{ company.email }}
                                </div>

                                <div class="font-semibold text-center my-1
                                            text-fuchsia-700 dark:text-fuchsia-300">
                                    <span>{{ t('status') }}: </span>
                                    {{ getStatusLabel(company.status) }}
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-center gap-3 text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <div
                                    class="flex items-center justify-center space-x-1"
                                    :title="t('views')"
                                >
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-300"
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                        />
                                    </svg>

                                    <span class="text-[12px] text-slate-700 dark:text-slate-200">
                                        {{ company.views ?? 0 }}
                                    </span>
                                </div>

                                <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(company.moderation_status).class"
                                    :title="company.moderation_note && company.moderated_at
                                        ? `${company.moderation_note} [${formatDate(company.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(company.moderation_status).text }}
                                </span>

                                    <ModerationButton
                                        :isAdmin="isAdmin"
                                        :status="company?.moderation_status ?? 0"
                                        :initialNote="company?.moderation_note || ''"
                                        mode="toggle"
                                        @submit="({ status, note }) => $emit('approve', company, status, note)"
                                    />
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="company.left"
                                    @toggle-left="$emit('toggle-left', company)"
                                    :title="company.left ? t('enabled') : t('disabled')"
                                />

                                <MainToggle
                                    :isActive="company.main"
                                    @toggle-main="$emit('toggle-main', company)"
                                    :title="company.main ? t('enabled') : t('disabled')"
                                />

                                <RightToggle
                                    :isActive="company.right"
                                    @toggle-right="$emit('toggle-right', company)"
                                    :title="company.right ? t('enabled') : t('disabled')"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="company.activity"
                                    @toggle-activity="$emit('toggle-activity', company)"
                                    :title="company.activity ? t('enabled') : t('disabled')"
                                />

                                <IconEdit
                                    :href="route('admin.marketCompanies.edit', { marketCompany: company.id })"
                                />

                                <DeleteIconButton @delete="$emit('delete', company)" />
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
