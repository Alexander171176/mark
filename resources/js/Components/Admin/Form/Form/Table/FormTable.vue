<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Табличное представление списка динамических форм.
 */

import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import IconShow from '@/Components/Admin/UI/Buttons/IconShow.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    forms: { type: Array, default: () => [] },
    statuses: { type: Object, default: () => ({}) },
    selectedForms: { type: Array, default: () => [] },
})

const emit = defineEmits([
    'show',
    'edit',
    'delete',
    'fields',
    'submissions',
    'toggle-activity',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/* ===================== Local list ===================== */

const localForms = ref([])

watch(
    () => props.forms,
    (newVal) => {
        localForms.value = JSON.parse(
            JSON.stringify(Array.isArray(newVal) ? newVal : [])
        )
    },
    { immediate: true, deep: true }
)

/* ===================== Drag & Drop ===================== */

const handleDragEnd = () => {
    emit('update-sort-order', localForms.value.map((form) => form.id))
}

/* ===================== Selection ===================== */

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localForms.value.map((form) => form.id),
        checked: Boolean(event?.target?.checked),
    })
}

const allSelected = () => {
    if (!localForms.value.length) {
        return false
    }

    return localForms.value.every(
        (form) => props.selectedForms.includes(form.id)
    )
}

/* ===================== Translation ===================== */

const formTranslation = (form) => form?.translation || {}

const formTitle = (form) => {
    return formTranslation(form)?.title
        || form?.code
        || `ID: ${form?.id}`
}

const formSubtitle = (form) => {
    return formTranslation(form)?.subtitle || ''
}

/* ===================== Owner ===================== */

const formOwner = (form) => form?.owner || form?.user || null

const ownerTitle = (form) => {
    const owner = formOwner(form)

    if (!owner) {
        return t('noData')
    }

    const values = [owner.name, owner.email].filter(Boolean)

    return values.length
        ? values.join(' — ')
        : t('noData')
}

const ownerAvatar = (form) => {
    return formOwner(form)?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/* ===================== Status ===================== */

const getStatusLabel = (status) => {
    if (status && props.statuses?.[status]) {
        return props.statuses[status]
    }

    const map = {
        draft: 'statusDraft',
        published: 'statusPublished',
        archived: 'statusArchived',
    }

    return t(map[status] || status || 'no')
}

const getStatusClasses = (status) => {
    if (status === 'published') {
        return [
            'bg-emerald-100',
            'text-emerald-700',
            'border-emerald-300',
            'dark:bg-emerald-900/40',
            'dark:text-emerald-300',
        ]
    }

    if (status === 'archived') {
        return [
            'bg-slate-200',
            'text-slate-700',
            'border-slate-300',
            'dark:bg-slate-600',
            'dark:text-slate-200',
        ]
    }

    return [
        'bg-amber-100',
        'text-amber-800',
        'border-amber-300',
        'dark:bg-amber-900/40',
        'dark:text-amber-300',
    ]
}

/* ===================== Helpers ===================== */

const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString(locale.value || undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const truncateText = (text, maxLength = 70) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}
</script>

<template>
    <div class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-200 dark:border-slate-600 relative">

        <!-- Selection -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedForms.length }}
            </div>

            <label
                v-if="localForms.length"
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
                v-if="localForms.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                              border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <!-- Drag -->
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
                        <div class="font-semibold text-center">{{ t('id') }}</div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('owner') }}</div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('title') }}</div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('fields') }}</div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('submissions') }}</div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('status') }}</div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>

                    <th class="px-1 py-1 whitespace-nowrap text-center w-px">
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
                    v-model="localForms"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: form }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">

                            <!-- Drag -->
                            <td class="px-1 py-1 text-center cursor-move handle w-px">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="px-1 py-1 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                                    :title="`[${form.sort ?? 0}] / ${formatDate(form.updated_at)}`"
                                >
                                    {{ form.id }}
                                </div>
                            </td>

                            <!-- Owner -->
                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(form)"
                                        :title="ownerTitle(form)"
                                        :alt="t('owner')"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                    />
                                </div>

                                <div class="text-[10px] font-semibold text-center
                                            text-slate-700 dark:text-slate-300">
                                    {{ truncateText(ownerTitle(form), 35) }}
                                </div>
                            </td>

                            <!-- Form -->
                            <td class="px-2 py-1">
                                <div class="flex flex-col items-start space-y-1">
                                    <div
                                        class="text-xs font-semibold text-left text-blue-600
                                               dark:text-blue-300"
                                        :title="formTitle(form)"
                                        @click="emit('show', form)"
                                    >
                                        {{ truncateText(formTitle(form), 70) }}
                                    </div>

                                    <div class="text-[11px] font-mono
                                                text-slate-500 dark:text-slate-300">
                                        {{ truncateText(form.code, 70) }}
                                    </div>

                                    <div
                                        v-if="formSubtitle(form)"
                                        class="text-[10px] text-cyan-700 dark:text-cyan-300"
                                    >
                                        {{ truncateText(formSubtitle(form), 90) }}
                                    </div>
                                </div>
                            </td>

                            <!-- Fields -->
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <button
                                        type="button"
                                        class="min-w-8 px-2 py-1 text-xs font-semibold
                                               text-blue-600 dark:text-blue-300
                                               bg-blue-50 dark:bg-slate-600
                                               border border-blue-200 dark:border-slate-500
                                               rounded-sm hover:bg-blue-100
                                               dark:hover:bg-slate-500"
                                        title="Поля формы"
                                        @click="emit('fields', form)"
                                    >
                                        {{ form.fields_count ?? 0 }}
                                    </button>
                                </div>
                            </td>

                            <!-- Submissions -->
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <button
                                        type="button"
                                        class="min-w-8 px-2 py-1 text-xs font-semibold
                                               text-fuchsia-700 dark:text-fuchsia-300
                                               bg-fuchsia-50 dark:bg-slate-600
                                               border border-fuchsia-200 dark:border-slate-500
                                               rounded-sm hover:bg-fuchsia-100
                                               dark:hover:bg-slate-500"
                                        title="Заявки формы"
                                        @click="emit('submissions', form)"
                                    >
                                        {{ form.submissions_count ?? 0 }}
                                    </button>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    <span
                                        class="text-[10px] px-2 py-1 rounded-sm
                                               border font-semibold"
                                        :class="getStatusClasses(form.status)"
                                    >
                                        {{ getStatusLabel(form.status) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-end items-center space-x-1">
                                    <ActivityToggle
                                        :isActive="form.activity"
                                        :title="form.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', form)"
                                    />

                                    <IconShow
                                        :href="route('admin.forms.show', { form: form.id })"
                                    />

                                    <IconEdit
                                        :href="route('admin.forms.edit', { form: form.id })"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', form)"
                                    />
                                </div>
                            </td>

                            <!-- Select -->
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedForms.includes(form.id)"
                                        @change="emit('toggle-select', form.id)"
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
