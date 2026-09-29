<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Карточное представление списка полей динамических форм.
 */

import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    fields: { type: Array, default: () => [] },
    fieldTypes: { type: Object, default: () => ({}) },
    selectedFields: { type: Array, default: () => [] },
    showForm: { type: Boolean, default: true },
})

const emit = defineEmits([
    'show',
    'delete',
    'toggle-activity',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/* ===================== Local list ===================== */

const localFields = ref([])
const openedSettingsBlocks = ref([])

watch(
    () => props.fields,
    (value) => {
        localFields.value = JSON.parse(JSON.stringify(Array.isArray(value) ? value : []))
    },
    { immediate: true, deep: true }
)

/* ===================== Drag & Drop ===================== */

const handleDragEnd = () => {
    emit('update-sort-order', localFields.value.map((field) => field.id))
}

/* ===================== Selection ===================== */

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localFields.value.map((field) => field.id),
        checked: Boolean(event?.target?.checked),
    })
}

const allSelected = () => {
    if (!localFields.value.length) return false
    return localFields.value.every((field) => props.selectedFields.includes(field.id))
}

/* ===================== Translation ===================== */

const fieldTranslation = (field) => field?.translation || {}
const fieldLabel = (field) => fieldTranslation(field)?.label || field?.name || `ID: ${field?.id}`
const fieldPlaceholder = (field) => fieldTranslation(field)?.placeholder || ''
const fieldDescription = (field) => fieldTranslation(field)?.description || ''
const fieldLocale = (field) => fieldTranslation(field)?.locale || ''

/* ===================== Parent form ===================== */

const formTranslation = (form) => form?.translation || {}
const formTitle = (form) => formTranslation(form)?.title || form?.code || `ID: ${form?.id}`

/* ===================== Field type ===================== */

const getFieldTypeConfig = (field) => {
    if (!field?.type) return null
    return props.fieldTypes?.[field.type] || null
}

const getFieldTypeLabel = (field) => {
    const config = getFieldTypeConfig(field)

    if (typeof config === 'string') return config

    if (config && typeof config === 'object') {
        return config.label || config.title || config.name || field.type
    }

    return field?.type || t('noData')
}

/* ===================== Settings block ===================== */

const isSettingsBlockOpen = (fieldId) => openedSettingsBlocks.value.includes(fieldId)

const toggleSettingsBlock = (fieldId) => {
    if (isSettingsBlockOpen(fieldId)) {
        openedSettingsBlocks.value = openedSettingsBlocks.value.filter((id) => id !== fieldId)
        return
    }

    openedSettingsBlocks.value.push(fieldId)
}

/* ===================== Boolean ===================== */

const booleanLabel = (value) => value ? t('yes') : t('no')

const booleanClasses = (value) => {
    if (value) {
        return [
            'bg-emerald-100',
            'text-emerald-700',
            'border-emerald-300',
            'dark:bg-emerald-900/40',
            'dark:text-emerald-300',
            'dark:border-emerald-700',
        ]
    }

    return [
        'bg-slate-100',
        'text-slate-600',
        'border-slate-300',
        'dark:bg-slate-600',
        'dark:text-slate-200',
        'dark:border-slate-500',
    ]
}

/* ===================== Helpers ===================== */

const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)
    if (Number.isNaN(date.getTime())) return ''

    return date.toLocaleDateString(locale.value || undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const truncateText = (text, maxLength = 80) => {
    if (!text) return ''

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
                {{ t('selected') }}: {{ selectedFields.length }}
            </div>

            <label
                v-if="localFields.length"
                class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
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
        <div v-if="localFields.length" class="p-3">
            <draggable
                v-model="localFields"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: field }">
                    <div
                        class="relative flex flex-col h-full rounded-md border
                               border-slate-400 dark:border-slate-500 bg-slate-50/70
                               dark:bg-slate-800/80 shadow-sm hover:shadow-md
                               transition-shadow duration-150"
                    >
                        <!-- Header -->
                        <header class="flex items-center justify-between px-2 py-1
                                       border-b border-dashed border-slate-400
                                       dark:border-slate-500">
                            <div class="flex items-center space-x-2">

                                <!-- Drag -->
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5
                                           rounded-sm border border-gray-400 bg-slate-200
                                           dark:bg-slate-700 text-slate-800 dark:text-blue-100"
                                    :title="`[${fieldLocale(field)}] : [${field.sort ?? 0}]`"
                                >
                                    ID: {{ field.id }}
                                </div>

                                <!-- Settings -->
                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isSettingsBlockOpen(field.id)
                                    ? t('hide')
                                    : t('showDetails')"
                                    @click.prevent="toggleSettingsBlock(field.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isSettingsBlockOpen(field.id) }"
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

                            <!-- Select -->
                            <input
                                type="checkbox"
                                :checked="selectedFields.includes(field.id)"
                                @change="emit('toggle-select', field.id)"
                            />
                        </header>

                        <!-- Content -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">

                            <!-- Settings -->
                            <div
                                v-show="isSettingsBlockOpen(field.id)"
                                class="p-2 rounded-sm bg-white/80 dark:bg-slate-700/70
                                       border border-slate-300 dark:border-slate-600"
                            >
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="min-h-7 text-[9px] font-semibold
                                                     text-slate-700 dark:text-slate-300">
                                            {{ t('required') }}
                                        </span>
                                        <span
                                            class="text-[9px] px-2 py-0.5 rounded-sm
                                                   border font-semibold"
                                            :class="booleanClasses(field.required)"
                                        >
                                            <span class="uppercase">
                                                {{ booleanLabel(field.required) }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-center gap-1">
                                        <span class="min-h-7 text-[9px] font-semibold
                                                     text-slate-700 dark:text-slate-300">
                                            {{ t('readOnly') }}
                                        </span>
                                        <span
                                            class="text-[9px] px-2 py-0.5 rounded-sm
                                                   border font-semibold"
                                            :class="booleanClasses(field.readonly)"
                                        >
                                            <span class="uppercase">
                                                {{ booleanLabel(field.readonly) }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-center gap-1">
                                        <span class="min-h-7 text-[9px] font-semibold
                                                     text-slate-700 dark:text-slate-300">
                                            {{ t('disabled') }}
                                        </span>
                                        <span
                                            class="text-[9px] px-2 py-0.5 rounded-sm
                                                   border font-semibold"
                                            :class="booleanClasses(field.disabled)"
                                        >
                                            <span class="uppercase">
                                                {{ booleanLabel(field.disabled) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-2 pt-2 border-t border-dashed
                                            border-slate-300 dark:border-slate-500
                                            grid grid-cols-2 gap-2 text-[10px]">
                                    <div class="text-slate-500 dark:text-slate-300">
                                        {{ t('width') }}:
                                        <span class="font-semibold text-slate-700
                                                     dark:text-slate-100">
                                            {{ field.width || '—' }}
                                        </span>
                                    </div>

                                    <div class="text-slate-500 dark:text-slate-300 text-right">
                                        {{ t('sortNumber') }}:
                                        <span class="font-semibold text-slate-700
                                                     dark:text-slate-100">
                                            {{ field.sort ?? 0 }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-if="field.updated_at"
                                    class="mt-2 text-center text-[9px]
                                           text-gray-800 dark:text-gray-200"
                                >
                                    {{ formatDate(field.updated_at) }}
                                </div>
                            </div>

                            <!-- Label -->
                            <div class="flex justify-center items-center">
                                <button
                                    type="button"
                                    class="text-xs font-semibold text-center
                                           text-blue-600 dark:text-blue-300
                                           bg-white dark:bg-slate-600 w-fit
                                           border-2 border-blue-300 dark:border-blue-700
                                           px-2 py-0.5 rounded-md hover:bg-blue-50
                                           dark:hover:bg-slate-500"
                                    :title="fieldLabel(field)"
                                    @click="emit('show', field)"
                                >
                                    {{ truncateText(fieldLabel(field), 80) }}
                                </button>
                            </div>

                            <!-- Name -->
                            <div
                                class="text-center text-[11px] font-mono
                                       text-slate-600 dark:text-slate-400 break-all"
                                :title="field.name"
                            >
                                {{ truncateText(field.name, 90) }}
                            </div>

                            <!-- Type -->
                            <div class="flex justify-center">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="px-2 py-0.5 text-[12px] font-semibold font-mono
                                               bg-indigo-50 dark:bg-slate-600 text-indigo-700
                                               dark:text-indigo-300 border border-indigo-300
                                               dark:border-slate-500 rounded-sm"
                                        :title="field.type"
                                    >
                                        {{ getFieldTypeLabel(field) }}
                                    </span>

                                    <span
                                        v-if="getFieldTypeLabel(field) !== field.type"
                                        class="mt-1 text-[9px] font-mono
                                               text-slate-600 dark:text-slate-400"
                                    >
                                        {{ field.type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Parent form -->
                            <div
                                v-if="showForm && field.form"
                                class="p-2 text-center rounded-sm bg-fuchsia-50/70
                                       dark:bg-slate-700/60 border border-fuchsia-200
                                       dark:border-slate-600"
                            >
                                <div class="text-[9px] uppercase tracking-wide
                                            text-slate-800 dark:text-slate-200">
                                    {{ t('form') }}
                                </div>

                                <div
                                    class="text-[11px] font-semibold
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                    :title="formTitle(field.form)"
                                >
                                    {{ truncateText(formTitle(field.form), 70) }}
                                </div>

                                <div
                                    v-if="field.form.code"
                                    class="mt-0.5 text-[9px] font-mono
                                           text-slate-600 dark:text-slate-400"
                                >
                                    {{ truncateText(field.form.code, 70) }}
                                </div>
                            </div>

                            <!-- Placeholder -->
                            <div
                                v-if="fieldPlaceholder(field)"
                                class="text-[11px] text-center text-cyan-700 dark:text-cyan-300"
                                :title="fieldPlaceholder(field)"
                            >
                                {{ truncateText(fieldPlaceholder(field), 100) }}
                            </div>

                            <!-- Description -->
                            <div
                                v-if="fieldDescription(field)"
                                class="text-[10px] text-center text-slate-700 dark:text-slate-300"
                                :title="fieldDescription(field)"
                            >
                                {{ truncateText(fieldDescription(field), 120) }}
                            </div>

                            <!-- Counters -->
                            <div class="flex items-center justify-center gap-4 text-[11px]
                                        font-semibold text-slate-600 dark:text-slate-200">
                                <div
                                    class="flex items-center gap-1 text-blue-600 dark:text-blue-300"
                                    title="Количество вариантов поля"
                                >
                                    <span>{{ t('variants') }}:</span>
                                    <span>{{ field.options_count ?? 0 }}</span>
                                </div>
                            </div>

                            <!-- Main flags -->
                            <div class="flex flex-wrap justify-center gap-1">
                                <span
                                    v-if="field.required"
                                    class="text-[9px] px-2 py-0.5 rounded-sm border
                                           font-semibold bg-emerald-100 text-emerald-700
                                           border-emerald-300 dark:bg-emerald-900/40
                                           dark:text-emerald-300"
                                >
                                    {{ t('required') }}
                                </span>

                                <span
                                    v-if="field.readonly"
                                    class="text-[9px] px-2 py-0.5 rounded-sm
                                           border font-semibold bg-amber-100 text-amber-700
                                           border-amber-300 dark:bg-amber-900/40
                                           dark:text-amber-300"
                                >
                                    {{ t('readOnly') }}
                                </span>

                                <span
                                    v-if="field.disabled"
                                    class="text-[9px] px-2 py-0.5 rounded-sm border
                                           font-semibold bg-rose-100 text-rose-700
                                           border-rose-300 dark:bg-rose-900/40 dark:text-rose-300"
                                >
                                    {{ t('disabled') }}
                                </span>
                            </div>

                            <!-- Width / sort -->
                            <div class="flex items-center justify-center gap-3
                                        text-[10px] text-slate-700 dark:text-slate-300">
                                <span>
                                    <span class="font-semibold">{{ t('width') }}: </span>
                                    {{ field.width || '—' }}
                                </span>
                                <span>
                                    <span class="font-semibold">{{ t('sortNumber') }}: </span>
                                    {{ field.sort ?? 0 }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-center px-3 py-2
                                    border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="field.activity"
                                    :title="field.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emit('toggle-activity', field)"
                                />

                                <IconEdit
                                    :href="route('admin.formFields.edit', { formField: field.id })"
                                />

                                <DeleteIconButton
                                    @delete="emit('delete', field)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <!-- Empty -->
        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
