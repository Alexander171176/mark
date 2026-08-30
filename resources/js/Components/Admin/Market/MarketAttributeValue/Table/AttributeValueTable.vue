<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
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

/* ===================== Attribute helpers ===================== */

/** Перевод характеристики */
const attributeTranslation = (value) => {
    return value?.attribute?.translation
        || {}
}

/**
 * Название характеристики.
 *
 * Новый контракт:
 * attribute.translation.title
 *
 * code используется только
 * как display fallback.
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
            attribute.type,
            attribute.unit,
        ]
            .filter(Boolean)
            .join(' / ')
        || '—'
}

/* ===================== Icon ===================== */

/**
 * Проверка содержимого icon.
 *
 * Это базовая проверка формата,
 * а не полноценная SVG-санация.
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
    maxLength = 70
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

        <div class="overflow-x-auto">
            <table
                v-if="localValues.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-1 py-3 w-px"></th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('attribute') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('icon') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('value') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('code') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
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
                    v-model="localValues"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: value }">
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
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                                    :title="`[${value.sort}] / ${formatDate(value.published_at)}`"
                                >
                                    {{ value.id }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="text-xs text-center text-gray-700 dark:text-gray-200">
                                        {{ truncateText(attributeTitle(value), 55) }}
                                    </div>

                                    <div
                                        class="text-[10px] text-slate-500 dark:text-slate-300"
                                        :title="attributeInfo(value)"
                                    >
                                        {{ attributeInfo(value) }}
                                    </div>

                                    <div
                                        v-if="value?.attribute?.type"
                                        class="text-[10px] px-2 py-0.5 rounded-sm
                                               border border-slate-300 dark:border-slate-500"
                                    >
                                        {{ getTypeLabel(value.attribute.type) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <div
                                        v-if="getSafeIcon(value.icon)"
                                        v-html="getSafeIcon(value.icon)"
                                        class="w-6 h-6 text-slate-700 dark:text-slate-100
                                               flex items-center justify-center"
                                    />

                                    <span
                                        v-else
                                        class="text-slate-400 dark:text-slate-300"
                                    >
                                        —
                                    </span>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center space-y-1">
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

                                    <div
                                        v-if="valueShort(value)"
                                        class="italic text-center text-xs
                                               text-slate-500 dark:text-slate-400"
                                        :title="valueShort(value)"
                                    >
                                        {{ truncateText(valueShort(value), 80) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center text-xs text-slate-700 dark:text-slate-200">
                                    {{ value.code || '—' }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <div class="flex items-center justify-center gap-1">
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

                                    <div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ getStatusLabel(value.status) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-end space-x-1">
                                    <ActivityToggle
                                        :isActive="value.activity"
                                        @toggle-activity="emits('toggle-activity', value)"
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
                            </td>

                            <td class="px-1 py-1 text-center">
                                <input
                                    type="checkbox"
                                    :checked="selectedValues.includes(value.id)"
                                    @change="emits('toggle-select', value.id)"
                                />
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-500 dark:text-slate-300"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
