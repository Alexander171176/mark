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
    values: { type: Array, default: () => [] },
    selectedValues: { type: Array, default: () => [] },
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

/* ===================== Local data ===================== */

const localValues = ref([])

/** Открытые информационные блоки характеристик */
const openedAttributeBlocks = ref([])

watch(
    () => props.values,
    (newVal) => {
        localValues.value = JSON.parse(
            JSON.stringify(newVal || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ===================== Drag & Drop ===================== */

const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localValues.value.map(
            (value) => value.id
        )
    )
}

/* ===================== Selection ===================== */

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localValues.value.map(
            (value) => value.id
        ),

        checked: Boolean(
            event?.target?.checked
        ),
    })
}

const allSelected = () => {
    return localValues.value.length > 0
        && localValues.value.every(
            (value) =>
                props.selectedValues.includes(
                    value.id
                )
        )
}

/* ===================== Value helpers ===================== */

/** Перевод значения */
const valueTranslation = (value) => {
    return value?.translation || {}
}

/** Название значения */
const valueTitle = (value) => {
    return valueTranslation(value)?.title
        || `ID: ${value?.id}`
}

/** Краткое описание значения */
const valueShort = (value) => {
    return valueTranslation(value)?.short
        || ''
}

/** Полное описание значения */
const valueDescription = (value) => {
    return valueTranslation(value)?.description
        || ''
}

/* ===================== Attribute helpers ===================== */

/** Перевод характеристики */
const attributeTranslation = (value) => {
    return value?.attribute?.translation
        || {}
}

/**
 * Название характеристики.
 *
 * Используется новый контракт
 * attribute.translation.title.
 */
const attributeTitle = (value) => {
    return attributeTranslation(value)?.title
        || value?.attribute?.code
        || '—'
}

/** Дополнительная информация характеристики */
const attributeInfo = (value) => {
    const attribute =
        value?.attribute

    if (! attribute) {
        return '—'
    }

    return [
            attribute.code,
            attribute.type
                ? getTypeLabel(
                    attribute.type
                )
                : null,
            attribute.unit,
        ]
            .filter(Boolean)
            .join(' / ')
        || '—'
}

/* ===================== Icon ===================== */

/**
 * Базовая проверка SVG.
 *
 * Проверка структуры строки не является
 * полноценной SVG-санацией.
 */
const getSafeIcon = (icon) => {
    if (
        typeof icon !== 'string'
    ) {
        return null
    }

    const trimmed =
        icon.trim()

    if (! trimmed) {
        return null
    }

    return trimmed.startsWith('<svg')
    && trimmed.endsWith('</svg>')
        ? trimmed
        : null
}

/* ===================== Status helpers ===================== */

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

const moderationBadge = (status) => {
    const moderationStatus =
        Number(status ?? 0)

    if (
        moderationStatus === 1
    ) {
        return {
            text:
                t('statusSelectApproved'),

            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 '
                + 'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (
        moderationStatus === 2
    ) {
        return {
            text:
                t('statusSelectRejected'),

            class:
                'bg-rose-100 text-rose-700 border-rose-300 '
                + 'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text:
            t('underModeration'),

        class:
            'bg-amber-100 text-amber-800 border-amber-300 '
            + 'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/* ===================== Formatting ===================== */

const formatDate = (dateStr) => {
    if (! dateStr) {
        return ''
    }

    const date =
        new Date(dateStr)

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
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

const truncateText = (
    text,
    maxLength = 80
) => {
    if (
        text === null
        || text === undefined
        || text === ''
    ) {
        return ''
    }

    const value =
        String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

/* ===================== Attribute block ===================== */

const isAttributeBlockOpen = (valueId) => {
    return openedAttributeBlocks.value.includes(
        valueId
    )
}

const toggleAttributeBlock = (valueId) => {
    if (
        isAttributeBlockOpen(
            valueId
        )
    ) {
        openedAttributeBlocks.value =
            openedAttributeBlocks.value.filter(
                (id) => id !== valueId
            )

        return
    }

    openedAttributeBlocks.value.push(
        valueId
    )
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
                {{ t('selected') }}: {{ selectedValues.length }}
            </div>

            <label
                v-if="localValues.length"
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
            v-if="localValues.length"
            class="p-3"
        >
            <draggable
                tag="div"
                v-model="localValues"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: value }">
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
                                    :title="`[${value.sort}] / ${formatDate(value.published_at)}`"
                                >
                                    ID: {{ value.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isAttributeBlockOpen(value.id) ? t('hide') : t('show')"
                                    @click.prevent="toggleAttributeBlock(value.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isAttributeBlockOpen(value.id) }"
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
                                    :class="moderationBadge(value.moderation_status).class"
                                >
                                    {{ moderationBadge(value.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedValues.includes(value.id)"
                                    @change="emits('toggle-select', value.id)"
                                />
                            </div>
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div
                                v-show="isAttributeBlockOpen(value.id)"
                                class="p-2 rounded-sm border border-slate-300 dark:border-slate-600
                                       bg-white dark:bg-slate-700 text-center"
                            >
                                <div class="text-[11px] font-semibold text-slate-700 dark:text-slate-100">
                                    {{ t('attribute') }}
                                </div>

                                <div
                                    class="text-[12px] font-semibold text-blue-700 dark:text-blue-300"
                                    :title="attributeTitle(value)"
                                >
                                    {{ truncateText(attributeTitle(value), 90) }}
                                </div>

                                <div
                                    class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"
                                    :title="attributeInfo(value)"
                                >
                                    {{ attributeInfo(value) }}
                                </div>
                            </div>

                            <div class="flex justify-center items-center">
                                <div
                                    class="text-xs font-semibold text-center
                                           bg-white dark:bg-slate-600
                                           w-fit border-2 px-2 py-0.5 rounded-md"
                                    :style="{ borderColor: value.color || 'transparent' }"
                                >
                                    <span :style="{ color: value.color || '#666666' }">
                                        {{ truncateText(valueTitle(value)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <div
                                    v-if="getSafeIcon(value.icon)"
                                    v-html="getSafeIcon(value.icon)"
                                    class="w-8 h-8 text-slate-700 dark:text-slate-100
                                           flex items-center justify-center"
                                />
                            </div>

                            <div
                                v-if="value.color"
                                class="flex items-center justify-center gap-1
                                       font-semibold text-xs text-slate-700 dark:text-slate-300"
                            >
                                <span
                                    class="inline-block w-4 h-4 rounded-sm border border-slate-400"
                                    :style="{ backgroundColor: value.color }"
                                ></span>

                                <span>
                                    {{ value.color }}
                                </span>
                            </div>

                            <div class="text-center text-xs text-slate-500 dark:text-slate-400">
                                {{ t('code') }}:
                                {{ truncateText(value.code, 90) || '—' }}
                            </div>

                            <div
                                class="text-center text-[11px] font-semibold
                                       text-gray-700 dark:text-gray-300"
                            >
                                {{ t('attribute') }}:
                                {{ truncateText(attributeTitle(value), 80) }}
                            </div>

                            <div
                                v-if="valueShort(value)"
                                class="font-semibold text-[12px] text-center
                                       text-cyan-700 dark:text-cyan-300"
                            >
                                {{ truncateText(valueShort(value), 120) }}
                            </div>

                            <div
                                v-if="valueDescription(value)"
                                class="text-[11px] text-center text-slate-500 dark:text-slate-300"
                            >
                                {{ truncateText(valueDescription(value), 140) }}
                            </div>

                            <div
                                v-if="value.show_from_at"
                                class="text-center text-[10px] text-gray-700 dark:text-gray-300"
                            >
                                {{ t('show') }}:
                                {{ formatDate(value.show_from_at) }}
                                /
                                {{ formatDate(value.show_to_at) || '—' }}
                            </div>

                            <div
                                v-else
                                class="text-center text-[10px] text-slate-500 dark:text-slate-300"
                            >
                                {{ formatDate(value.published_at) }}
                            </div>

                            <div
                                class="font-semibold text-center text-[11px]
                                       text-fuchsia-700 dark:text-fuchsia-300"
                            >
                                <span>
                                    {{ t('status') }}:
                                </span>

                                {{ getStatusLabel(value.status) }}
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(value.moderation_status).class"
                                    :title="value.moderation_note && value.moderated_at
                                        ? `${value.moderation_note} [${formatDate(value.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(value.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="value?.moderation_status ?? 0"
                                    :initialNote="value?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) => emits('approve', value, status, note)"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="value.activity"
                                    @toggle-activity="emits('toggle-activity', value)"
                                    :title="value.activity ? t('enabled') : t('disabled')"
                                />

                                <IconEdit
                                    :href="route('admin.marketAttributeValues.edit', {
                                        marketAttributeValue: value.id
                                    })"
                                />

                                <DeleteIconButton
                                    @delete="emits('delete', value)"
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
