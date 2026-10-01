<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Карточное представление списка динамических форм.
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
const openedOwnerBlocks = ref([])

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

const formLocale = (form) => {
    return formTranslation(form)?.locale || ''
}

/* ===================== Owner ===================== */

const formOwner = (form) => form?.owner || form?.user || null

const ownerName = (form) => {
    return formOwner(form)?.name || t('noData')
}

const ownerEmail = (form) => {
    return formOwner(form)?.email || ''
}

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

/* ===================== Owner block ===================== */

const isOwnerBlockOpen = (formId) => {
    return openedOwnerBlocks.value.includes(formId)
}

const toggleOwnerBlock = (formId) => {
    if (isOwnerBlockOpen(formId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
            (id) => id !== formId
        )

        return
    }

    openedOwnerBlocks.value.push(formId)
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
        return 'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
            'dark:bg-emerald-900/40 dark:text-emerald-300'
    }

    if (status === 'archived') {
        return 'bg-slate-200 text-slate-700 border-slate-300 ' +
            'dark:bg-slate-600 dark:text-slate-200'
    }

    return 'bg-amber-100 text-amber-800 border-amber-300 ' +
        'dark:bg-amber-900/40 dark:text-amber-300'
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

const truncateText = (text, maxLength = 80) => {
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
                border border-slate-400 dark:border-slate-500 relative">

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

        <!-- Cards -->
        <div v-if="localForms.length" class="p-3">
            <draggable
                tag="div"
                v-model="localForms"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: form }">
                    <div class="relative flex flex-col h-full rounded-md
                                border border-slate-400 dark:border-slate-500
                                bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                                hover:shadow-md transition-shadow duration-150">

                        <!-- Header -->
                        <header class="flex items-center justify-between px-2 py-1
                                       border-b border-dashed border-slate-400
                                       dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${formLocale(form)}] : [${form.sort ?? 0}]`"
                                >
                                    ID: {{ form.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(form.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(form.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOwnerBlockOpen(form.id) }"
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

                            <input
                                type="checkbox"
                                :checked="selectedForms.includes(form.id)"
                                @change="emit('toggle-select', form.id)"
                            />
                        </header>

                        <!-- Content -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">

                            <!-- Owner -->
                            <div
                                v-show="isOwnerBlockOpen(form.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(form)"
                                    :title="ownerTitle(form)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('owner')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(form)"
                                >
                                    {{ ownerName(form) }}
                                </div>

                                <div
                                    v-if="ownerEmail(form)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(form)"
                                >
                                    {{ ownerEmail(form) }}
                                </div>

                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ formatDate(form.updated_at) }}
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="flex justify-center items-center">
                                <div
                                    class="text-xs font-semibold text-center
                                           text-blue-600 dark:text-blue-300
                                           bg-white dark:bg-slate-600
                                           w-fit border-2 border-blue-300
                                           dark:border-blue-700 px-2 py-0.5 rounded-md"
                                    :title="formTitle(form)"
                                    @click="emit('show', form)"
                                >
                                    {{ truncateText(formTitle(form), 80) }}
                                </div>
                            </div>

                            <!-- Code -->
                            <div
                                class="text-center text-[11px] font-mono
                                       text-slate-500 dark:text-slate-400 break-all"
                                :title="form.code"
                            >
                                {{ truncateText(form.code, 90) }}
                            </div>

                            <!-- Subtitle -->
                            <div
                                v-if="formSubtitle(form)"
                                class="font-semibold text-[12px] text-center
                                       text-cyan-700 dark:text-cyan-300"
                            >
                                {{ truncateText(formSubtitle(form), 120) }}
                            </div>

                            <!-- Counters -->
                            <div class="flex items-center justify-center gap-4 text-[11px]
                                        font-semibold text-slate-600 dark:text-slate-200">

                                <button
                                    type="button"
                                    class="flex items-center gap-1 text-blue-600
                                           dark:text-blue-300 hover:underline"
                                    title="Поля формы"
                                    @click="emit('fields', form)"
                                >
                                    <span>Поля:</span>
                                    <span>{{ form.fields_count ?? 0 }}</span>
                                </button>

                                <button
                                    type="button"
                                    class="flex items-center gap-1 text-fuchsia-700
                                           dark:text-fuchsia-300 hover:underline"
                                    :title="t('submissions')"
                                    @click="emit('submissions', form)"
                                >
                                    <span>Заявки:</span>
                                    <span>{{ form.submissions_count ?? 0 }}</span>
                                </button>
                            </div>

                            <!-- Status -->
                            <div class="flex justify-center">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="getStatusClasses(form.status)"
                                >
                                    {{ getStatusLabel(form.status) }}
                                </span>
                            </div>

                            <!-- Sort -->
                            <div class="text-center text-[10px]
                                        text-slate-500 dark:text-slate-400">
                                {{ t('sortNumber') }}: {{ form.sort ?? 0 }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-center px-3 py-2
                                    border-t border-dashed border-slate-400
                                    dark:border-slate-500">
                            <div class="flex items-center space-x-1">
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
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <!-- Empty -->
        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
