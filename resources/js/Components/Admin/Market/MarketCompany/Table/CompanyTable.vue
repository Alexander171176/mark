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
    companies: {
        type: Array,
        default: () => [],
    },

    selectedCompanies: {
        type: Array,
        default: () => [],
    },

    isAdmin: {
        type: Boolean,
        default: false,
    },
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

/** Локальный список для drag&drop */
const localCompanies = ref([])

/** Синхронизация входного списка */
watch(
    () => props.companies,
    (newVal) => {
        localCompanies.value = JSON.parse(
            JSON.stringify(newVal || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/** Завершение drag&drop */
const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localCompanies.value.map(
            (company) => company.id
        )
    )
}

/** Выбрать / снять все компании текущего списка */
const toggleAll = (event) => {
    emits(
        'toggle-all',
        {
            ids: localCompanies.value.map(
                (company) => company.id
            ),
            checked: event.target.checked,
        }
    )
}

/** Все компании текущего списка выбраны */
const allSelected = () => {
    return !!localCompanies.value.length
        && localCompanies.value.every(
            (company) =>
                props.selectedCompanies.includes(
                    company.id
                )
        )
}

/** Название компании */
const companyTitle = (company) => {
    return company?.translation?.title
        || company?.legal_name
        || `ID: ${company?.id}`
}

/** Информация о владельце */
const ownerTitle = (company) => {
    const owner = company?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

/** Аватар владельца */
const ownerAvatar = (company) => {
    return company?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/** URL логотипа компании */
const logoUrl = (company) => {
    if (!company?.logo) {
        return '/storage/market/companies/logos/default-image-light.png'
    }

    return company.logo.startsWith('/storage/')
        ? company.logo
        : `/storage/${company.logo}`
}

/** Бейдж модерации */
const moderationBadge = (status) => {
    const moderationStatus =
        Number(status ?? 0)

    if (moderationStatus === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 '
                + 'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (moderationStatus === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 '
                + 'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 '
            + 'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/** Локализованное название статуса публикации */
const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const getStatusLabel = (status) => {
    return t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )
}

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString(
        'ru-RU',
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    )
}

/** Обрезка текста */
const truncateText = (
    text,
    maxLength = 40
) => {
    if (!text) {
        return ''
    }

    return text.length > maxLength
        ? text
        .slice(0, maxLength)
        .trimEnd() + '…'
        : text
}

/** Локализованное название типа компании */
const companyTypeLabelKeyMap = {
    company: 'marketCompanyTypeCompany',
    entrepreneur: 'marketCompanyTypeEntrepreneur',
    individual: 'marketCompanyTypeIndividual',
}

const companyTypeLabel = (type) => {
    return t(
        companyTypeLabelKeyMap[type]
        || 'marketCompanyTypeCompany'
    )
}

/** Локализованная информация по НДС */
const vatLabel = (company) => {
    if (!company?.vat_enabled) {
        return t('marketCompanyWithoutVat')
    }

    if (company?.vat_rate) {
        return `${t('marketCompanyVat')} ${company.vat_rate}%`
    }

    return t('marketCompanyWithVat')
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

        <div class="overflow-x-auto">
            <table
                v-if="localCompanies.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-1 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24"
                        >
                            <path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div
                            class="flex justify-center"
                            :title="t('owner')"
                        >
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z" />
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z" />
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z" />
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z" />
                                <path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z" />
                            </svg>
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('image')"
                        >
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                            </svg>
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('title') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            БИН/ИИН
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('contacts') }} / {{ t('city') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('views')"
                        >
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('show') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('status') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <th class="px-1 py-1 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            :checked="allSelected()"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localCompanies"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: company }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-1 py-1 text-center cursor-move handle w-px">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                                    :title="`[${company.sort}] / ${formatDate(company.published_at)}`"
                                >
                                    {{ company.id }}
                                </div>
                            </td>

                            <td class="px-1 py-3">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(company)"
                                        :title="ownerTitle(company)"
                                        :alt="t('owner')"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-3">
                                <div class="flex justify-center">
                                    <img
                                        :src="logoUrl(company)"
                                        :alt="companyTitle(company)"
                                        :title="companyTitle(company)"
                                        class="h-8 w-12 object-cover rounded-sm
                                               border border-slate-300 dark:border-slate-600"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-3">
                                <div class="text-left">
                                    <a
                                        :href="`/market/companies/${encodeURIComponent(company.url)}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sky-700 dark:text-sky-200
                                               text-xs hover:underline
                                               hover:text-amber-700 dark:hover:text-amber-200"
                                        :title="company.show_from_at
                                            ? `${t('show')}: ${company.show_from_at} / ${company.show_to_at}`
                                            : `${formatDate(company.published_at)}`"
                                    >
                                        {{ truncateText(companyTitle(company), 70) }}
                                    </a>

                                    <div class="text-[11px] text-slate-500 dark:text-slate-300">
                                        {{ truncateText(company.legal_name, 70) }}
                                    </div>

                                    <div class="text-[10px] text-slate-400 dark:text-slate-400">
                                        {{ companyTypeLabel(company.company_type) }} / {{ vatLabel(company) }}
                                    </div>

                                    <div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ getStatusLabel(company.status) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-3 whitespace-nowrap">
                                <div class="text-left text-xs text-indigo-700 dark:text-indigo-300">
                                    {{ company.bin_iin || '—' }}
                                </div>
                            </td>

                            <td
                                class="px-1 py-3 whitespace-nowrap
                                       flex flex-col items-center justify-center gap-3"
                            >
                                <div class="text-left text-xs">
                                    <div class="text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ company.phone || '—' }}
                                    </div>

                                    <div class="text-[10px] text-slate-500 dark:text-slate-300">
                                        {{ company.email || '' }}
                                    </div>
                                </div>

                                <div class="text-left text-xs">
                                    <div>
                                        {{ company.city || '—' }}
                                    </div>

                                    <div class="text-[10px] text-slate-500 dark:text-slate-300">
                                        {{ company.region || '' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-3 whitespace-nowrap">
                                <div class="text-center text-blue-600 dark:text-blue-300">
                                    {{ company.views }}
                                </div>
                            </td>

                            <td class="px-1 py-3 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <LeftToggle
                                        :isActive="company.left"
                                        :title="company.left ? t('enabled') : t('disabled')"
                                        @toggle-left="$emit('toggle-left', company)"
                                    />

                                    <MainToggle
                                        :isActive="company.main"
                                        :title="company.main ? t('enabled') : t('disabled')"
                                        @toggle-main="$emit('toggle-main', company)"
                                    />

                                    <RightToggle
                                        :isActive="company.right"
                                        :title="company.right ? t('enabled') : t('disabled')"
                                        @toggle-right="$emit('toggle-right', company)"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <span
                                        class="text-[10px] px-2 py-1 rounded-sm
                                               border font-semibold"
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
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-1">
                                    <ActivityToggle
                                        :isActive="company.activity"
                                        :title="company.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="$emit('toggle-activity', company)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketCompanies.edit', { marketCompany: company.id })"
                                    />

                                    <DeleteIconButton
                                        @delete="$emit('delete', company)"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
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
