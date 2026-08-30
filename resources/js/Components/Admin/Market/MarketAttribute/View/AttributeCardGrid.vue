<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    attributes: { type: Array, default: () => [] },
    selectedAttributes: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

const localAttributes = ref([])
const openedOwnerBlocks = ref([])

watch(
    () => props.attributes,
    (newVal) => {
        localAttributes.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localAttributes.value.map((attribute) => attribute.id)
    )
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localAttributes.value.map((attribute) => attribute.id),
        checked: Boolean(event?.target?.checked),
    })
}

const allSelected = () => {
    return Boolean(
        localAttributes.value.length
        && localAttributes.value.every(
            (attribute) =>
                props.selectedAttributes.includes(attribute.id)
        )
    )
}

/* ===================== Translations ===================== */

const attributeTranslation = (attribute) =>
    attribute?.translation || {}

const attributeTitle = (attribute) =>
    attributeTranslation(attribute)?.title
    || `ID: ${attribute?.id}`

const attributeShort = (attribute) =>
    attributeTranslation(attribute)?.short || ''

const groupTranslation = (attribute) =>
    attribute?.group?.translation || {}

const groupTitle = (attribute) =>
    groupTranslation(attribute)?.title
    || attribute?.group?.code
    || '—'

/* ===================== Owner ===================== */

const ownerName = (attribute) =>
    attribute?.owner?.name
    || t('noData')

const ownerEmail = (attribute) =>
    attribute?.owner?.email || ''

const ownerTitle = (attribute) => {
    const owner = attribute?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (attribute) =>
    attribute?.owner?.profile_photo_url
    || '/storage/profile-photos/default-image.png'

/* ===================== Helpers ===================== */

const getSafeIcon = (icon) => {
    if (typeof icon !== 'string') {
        return null
    }

    const trimmed = icon.trim()

    if (!trimmed) {
        return null
    }

    return trimmed.startsWith('<svg')
    && trimmed.endsWith('</svg>')
        ? trimmed
        : null
}

const getStatusLabel = (status) => {
    const map = {
        draft: 'statusDraft',
        published: 'statusPublished',
        archived: 'statusArchived',
    }

    return t(
        map[status]
        || status
        || 'no'
    )
}

const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString(
        locale.value || undefined,
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    )
}

const truncateText = (text, maxLength = 80) => {
    if (text === null || text === undefined || text === '') {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? value.slice(0, maxLength).trimEnd() + '…'
        : value
}

const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
    }
}

const getTypeLabel = (type) => {
    const map = {
        string: 'string',
        text: 'text',
        integer: 'integer',
        decimal: 'float',
        boolean: 'boolean',
        date: 'date',
        datetime: 'datetime',
        select: 'typeSelect',
        multiselect: 'multiselect',
    }

    return t(
        map[type]
        || type
        || 'noData'
    )
}

const booleanBadge = (value, label) => {
    return {
        text: value ? label : '—',
        class: value
            ? 'bg-cyan-100 text-cyan-700 border-cyan-300 dark:bg-cyan-900/40 dark:text-cyan-300'
            : 'bg-slate-100 text-slate-500 border-slate-300 dark:bg-slate-700 dark:text-slate-400',
    }
}

/* ===================== Owner block ===================== */

const isOwnerBlockOpen = (attributeId) =>
    openedOwnerBlocks.value.includes(attributeId)

const toggleOwnerBlock = (attributeId) => {
    if (isOwnerBlockOpen(attributeId)) {
        openedOwnerBlocks.value =
            openedOwnerBlocks.value.filter(
                (id) => id !== attributeId
            )

        return
    }

    openedOwnerBlocks.value.push(attributeId)
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
                {{ t('selected') }}: {{ selectedAttributes.length }}
            </div>

            <label
                v-if="localAttributes.length"
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

        <div
            v-if="localAttributes.length"
            class="p-3"
        >
            <draggable
                tag="div"
                v-model="localAttributes"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: attribute }">
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
                                    :title="`[${attribute.sort}] / ${formatDate(attribute.published_at)}`"
                                >
                                    ID: {{ attribute.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(attribute.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(attribute.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOwnerBlockOpen(attribute.id) }"
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
                                    class="text-[10px] px-2 py-0.5 rounded-sm
                                           border font-semibold"
                                    :class="moderationBadge(attribute.moderation_status).class"
                                >
                                    {{ moderationBadge(attribute.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedAttributes.includes(attribute.id)"
                                    @change="emits('toggle-select', attribute.id)"
                                />
                            </div>
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div
                                v-show="isOwnerBlockOpen(attribute.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(attribute)"
                                    :title="ownerTitle(attribute)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('owner')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(attribute)"
                                >
                                    {{ ownerName(attribute) }}
                                </div>

                                <div
                                    v-if="ownerEmail(attribute)"
                                    class="text-[10px] text-slate-500 dark:text-slate-400
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(attribute)"
                                >
                                    {{ ownerEmail(attribute) }}
                                </div>

                                <div
                                    v-if="attribute.show_from_at"
                                    class="mt-1 text-center text-[10px]
                                           text-gray-700 dark:text-gray-300"
                                >
                                    {{ t('show') }}:
                                    {{ formatDate(attribute.show_from_at) }}
                                    /
                                    {{ formatDate(attribute.show_to_at) }}
                                </div>

                                <div
                                    v-else
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDate(attribute.published_at) }}
                                </div>
                            </div>

                            <div class="flex justify-center items-center">
                                <div
                                    class="text-xs font-semibold text-center
                                           bg-white dark:bg-slate-600
                                           w-fit border-2 px-2 py-0.5 rounded-md"
                                    :style="{ borderColor: attribute.color || 'transparent' }"
                                >
                                    <span
                                        :style="{ color: attribute.color || '#666666' }"
                                    >
                                        {{ truncateText(attributeTitle(attribute)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <div
                                    v-if="getSafeIcon(attribute.icon)"
                                    v-html="getSafeIcon(attribute.icon)"
                                    class="w-8 h-8 text-slate-700 dark:text-slate-100
                                           flex items-center justify-center"
                                />
                            </div>

                            <div
                                v-if="attribute.color"
                                class="flex items-center justify-center gap-1
                                       font-semibold text-xs
                                       text-slate-700 dark:text-slate-300"
                            >
                                <span
                                    class="inline-block w-4 h-4 rounded-sm
                                           border border-slate-400"
                                    :style="{ backgroundColor: attribute.color }"
                                />

                                <span>
                                    {{ attribute.color }}
                                </span>
                            </div>

                            <div class="text-center text-xs text-slate-500 dark:text-slate-400">
                                {{ t('code') }}:
                                {{ truncateText(attribute.code, 90) }}
                            </div>

                            <div
                                class="flex items-center justify-center gap-2 text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500"
                                >
                                    {{ getTypeLabel(attribute.type) }}
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500"
                                >
                                    {{ attribute.unit || '—' }}
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-center gap-3 text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <span>{{ t('values') }}:</span>

                                <span class="text-[12px] text-blue-600 dark:text-blue-300">
                                    {{ attribute.values_count ?? 0 }}
                                </span>
                            </div>

                            <div
                                class="text-center text-[11px] font-semibold
                                       text-gray-700 dark:text-gray-300"
                            >
                                {{ t('group') }}:
                                {{ truncateText(groupTitle(attribute), 80) }}
                            </div>

                            <div
                                v-if="attributeShort(attribute)"
                                class="font-semibold text-[12px] text-center
                                       text-cyan-700 dark:text-cyan-300"
                            >
                                {{ truncateText(attributeShort(attribute), 120) }}
                            </div>

                            <div class="flex flex-wrap justify-center gap-1 font-semibold">
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm border"
                                    :class="booleanBadge(attribute.visible, t('visibleCard')).class"
                                >
                                    {{ booleanBadge(attribute.visible, t('visibleCard')).text }}
                                </span>

                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm border"
                                    :class="booleanBadge(attribute.filterable, t('showFilter')).class"
                                >
                                    {{ booleanBadge(attribute.filterable, t('showFilter')).text }}
                                </span>

                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm border"
                                    :class="booleanBadge(attribute.required, t('required')).class"
                                >
                                    {{ booleanBadge(attribute.required, t('required')).text }}
                                </span>
                            </div>

                            <div
                                class="font-semibold text-center text-[11px]
                                       text-fuchsia-700 dark:text-fuchsia-300"
                            >
                                <span>{{ t('status') }}: </span>
                                {{ getStatusLabel(attribute.status) }}
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm
                                           border font-semibold"
                                    :class="moderationBadge(attribute.moderation_status).class"
                                    :title="attribute.moderation_note && attribute.moderated_at
                                        ? `${attribute.moderation_note} [${formatDate(attribute.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(attribute.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="attribute?.moderation_status ?? 0"
                                    :initialNote="attribute?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) =>
                                        emits('approve', attribute, status, note)"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="attribute.activity"
                                    @toggle-activity="emits('toggle-activity', attribute)"
                                    :title="attribute.activity
                                        ? t('enabled')
                                        : t('disabled')"
                                />

                                <IconEdit
                                    :href="route('admin.marketAttributes.edit', {
                                        marketAttribute: attribute.id
                                    })"
                                />

                                <DeleteIconButton
                                    @delete="emits('delete', attribute)"
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
