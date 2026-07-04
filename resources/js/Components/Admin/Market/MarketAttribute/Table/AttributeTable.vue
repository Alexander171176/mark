<script setup>
import { defineProps, defineEmits, watch, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t } = useI18n()

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

watch(
    () => props.attributes,
    (newVal) => {
        localAttributes.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localAttributes.value.map(attribute => attribute.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localAttributes.value.map(attribute => attribute.id),
        checked: event.target.checked,
    })
}

const allSelected = () => {
    return localAttributes.value.length
        && localAttributes.value.every(attribute => props.selectedAttributes.includes(attribute.id))
}

const attributeTranslation = (attribute) => attribute?.translation || {}
const attributeTitle = (attribute) => attributeTranslation(attribute)?.title || `ID: ${attribute?.id}`
const attributeShort = (attribute) => attributeTranslation(attribute)?.short || ''
const groupTitle = (attribute) => attribute?.group?.title || attribute?.group?.translation?.title || '—'

const ownerTitle = (attribute) => {
    const owner = attribute?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (attribute) => {
    return attribute?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

const getSafeIcon = (icon) => {
    if (!icon) return null

    const trimmed = icon.trim()

    return trimmed.startsWith('<svg') && trimmed.endsWith('</svg>')
        ? trimmed
        : null
}

const getStatusLabel = (status) => {
    const map = {
        draft: 'statusDraft',
        published: 'statusPublished',
        archived: 'statusArchived',
    }

    return t(map[status] || status || 'no')
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

    return t(map[type] || type || 'noData')
}

const booleanBadge = (value, label) => {
    return {
        text: value ? label : '—',
        class: value
            ? 'bg-cyan-100 text-cyan-700 ' +
            'border-cyan-300 dark:bg-cyan-900/40 dark:text-cyan-300'
            : 'bg-slate-100 text-slate-500 ' +
            'border-slate-300 dark:bg-slate-700 dark:text-slate-400',
    }
}

const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 ' +
                'border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 ' +
                'border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 ' +
            'border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
    }
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

const truncateText = (text, maxLength = 70) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
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

        <div class="overflow-x-auto">
            <table
                v-if="localAttributes.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-1 py-3 w-px">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('owner') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('icon') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('title') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center" :title="t('values')">
                            <svg class="w-4 h-4 fill-current shrink-0"
                                 viewBox="0 0 512 512">
                                <path
                                    d="M395.198 256c3.461-10.526 18.796-21.28 36.265-32.425 16.625-10.605 35.467-22.626 50.341-38.862 17.458-19.054 25.944-40.175 25.944-64.567 0-60.562-50.702-88.146-97.81-88.146-42.491 0-76.378 22.016-94.432 50.447-4.654 7.329-2.592 17.036 4.623 21.865l30.328 20.296c7.032 4.706 16.46 3.084 21.63-3.614 8.022-10.394 18.818-18.225 31.667-18.225 19.387 0 26.266 12.901 26.266 23.948 0 36.159-119.437 57.023-119.437 160.024 0 6.654.561 13.014 1.415 19.331 1.076 7.964 7.834 13.928 15.87 13.928H496c8.837 0 16-7.163 16-16v-32c0-8.837-7.163-16-16-16H395.198zM272 416c8.837 0 16 7.163 16 16v32c0 8.837-7.163 16-16 16h-62.399a16 16 0 0 1-13.541-7.478l-45.701-72.615c-2.297-3.352-4.422-6.969-6.195-10.209-1.65 3.244-3.647 6.937-5.874 10.582l-44.712 72.147a15.999 15.999 0 0 1-13.6 7.572H16c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h26.325l56.552-82.709L46.111 256H16c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h68.806a16 16 0 0 1 13.645 7.644l39.882 65.126c2.072 3.523 4.053 7.171 5.727 10.37 1.777-3.244 3.92-6.954 6.237-10.537l40.332-65.035a16 16 0 0 1 13.598-7.567H272c8.837 0 16 7.163 16 16v32c0 8.837-7.163 16-16 16h-27.979l-52.69 75.671L249.974 416H272z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('type') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('settings') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('status') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
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
                    v-model="localAttributes"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: attribute }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-1 py-1 text-center cursor-move handle w-px">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                            :title="`[${attribute.sort}] / ${formatDate(attribute.published_at)}`"
                                >
                                    {{ attribute.id }}
                                </div>
                            </td>
                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(attribute)"
                                        :title="ownerTitle(attribute)"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                        :alt="t('owner')"
                                    />
                                </div>
                                <div class="text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300">
                                    {{ ownerTitle(attribute) }}
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <div
                                        v-if="getSafeIcon(attribute.icon)"
                                        v-html="getSafeIcon(attribute.icon)"
                                        class="w-6 h-6 text-slate-700 dark:text-slate-100
                                               flex items-center justify-center"
                                    />
                                    <span v-else class="text-slate-400 dark:text-slate-300">—</span>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center space-y-1">
                                    <div
                                        class="text-xs font-semibold text-center
                                               bg-white dark:bg-slate-600
                                               w-fit border-2 px-2 py-0.5 rounded-md"
                                        :style="{ borderColor: attribute.color || 'transparent' }"
                                    >
                                        <span :style="{ color: attribute.color || '#666666' }">
                                            {{ truncateText(attributeTitle(attribute)) }}
                                        </span>
                                    </div>
                                    <div
                                        class="italic text-center text-xs
                                               text-slate-500 dark:text-slate-400"
                                    :title="truncateText(attributeShort(attribute), 80)"
                                    >
                                        {{ attribute.code }}
                                    </div>
                                    <div class="text-center text-xs
                                                text-gray-700 dark:text-gray-300"
                                         :title="t('group')">
                                        {{ truncateText(groupTitle(attribute), 45) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center text-blue-600 dark:text-blue-300">
                                    {{ attribute.values_count ?? 0 }}
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center gap-1">
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500">
                                        {{ getTypeLabel(attribute.type) }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-300">
                                        {{ attribute.unit || '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center gap-1">
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
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <div class="flex items-center justify-center gap-1">
                    <span
                        class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
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

                                    <div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ getStatusLabel(attribute.status) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-end space-x-1">
                                    <ActivityToggle
                                        :isActive="attribute.activity"
                                        @toggle-activity="emits('toggle-activity', attribute)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketAttributes.edit', { marketAttribute: attribute.id })"
                                    />

                                    <DeleteIconButton
                                        @delete="emits('delete', attribute)"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 text-center">
                                <input
                                    type="checkbox"
                                    :checked="selectedAttributes.includes(attribute.id)"
                                    @change="emits('toggle-select', attribute.id)"
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
