<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Табличное представление списка полей динамических форм.
 */

import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    fields: {
        type: Array,
        default: () => [],
    },

    fieldTypes: {
        type: Object,
        default: () => ({}),
    },

    selectedFields: {
        type: Array,
        default: () => [],
    },

    /**
     * Показывать родительскую форму.
     *
     * false используется, когда Index уже открыт
     * в контексте конкретной формы.
     */
    showForm: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits([
    'show',
    'edit',
    'delete',
    'toggle-activity',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/* ===================== Local list ===================== */

const localFields = ref([])

watch(
    () => props.fields,
    (newVal) => {
        localFields.value = JSON.parse(
            JSON.stringify(
                Array.isArray(newVal)
                    ? newVal
                    : []
            )
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ===================== Drag & Drop ===================== */

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localFields.value.map(
            (field) => field.id
        )
    )
}

/* ===================== Selection ===================== */

const toggleAll = (event) => {
    emit(
        'toggle-all',
        {
            ids: localFields.value.map(
                (field) => field.id
            ),

            checked: Boolean(
                event?.target?.checked
            ),
        }
    )
}

const allSelected = () => {
    if (!localFields.value.length) {
        return false
    }

    return localFields.value.every(
        (field) =>
            props.selectedFields.includes(
                field.id
            )
    )
}

/* ===================== Translation ===================== */

/**
 * Перевод поля.
 */
const fieldTranslation = (field) => {
    return field?.translation || {}
}

/**
 * Название поля.
 */
const fieldLabel = (field) => {
    return fieldTranslation(field)?.label
        || field?.name
        || `ID: ${field?.id}`
}

/**
 * Placeholder поля.
 */
const fieldPlaceholder = (field) => {
    return fieldTranslation(field)?.placeholder
        || ''
}

/**
 * Перевод родительской формы.
 */
const formTranslation = (form) => {
    return form?.translation || {}
}

/**
 * Название родительской формы.
 */
const formTitle = (form) => {
    return formTranslation(form)?.title
        || form?.code
        || `ID: ${form?.id}`
}

/* ===================== Field type ===================== */

/**
 * Конфигурация типа поля.
 */
const getFieldTypeConfig = (field) => {
    if (!field?.type) {
        return null
    }

    return props.fieldTypes?.[field.type]
        || null
}

/**
 * Человекочитаемое название типа поля.
 *
 * Поддерживает:
 * fieldTypes[type] = 'Text'
 *
 * и:
 * fieldTypes[type] = {
 *     label: 'Text',
 *     ...
 * }
 */
const getFieldTypeLabel = (field) => {
    const config =
        getFieldTypeConfig(field)

    if (
        typeof config === 'string'
    ) {
        return config
    }

    if (
        config
        && typeof config === 'object'
    ) {
        return config.label
            || config.title
            || config.name
            || field.type
    }

    return field?.type
        || t('noData')
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
    if (!dateStr) {
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
    if (!text) {
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
        <!-- Selection -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div
                class="text-xs text-slate-600 dark:text-slate-200"
            >
                {{ t('selected') }}:
                {{ selectedFields.length }}
            </div>

            <label
                v-if="localFields.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>
                    {{ t('selectAll') }}
                </span>

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
                v-if="localFields.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- Drag -->
                    <th class="px-1 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current
                                       text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />

                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>

                    <!-- ID -->
                    <th
                        class="px-1 py-3 whitespace-nowrap w-px"
                    >
                        <div
                            class="font-semibold text-center"
                        >
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Field -->
                    <th
                        class="px-2 py-3 whitespace-nowrap"
                    >
                        <div
                            class="font-semibold text-left"
                        >
                            {{ t('field') }}
                        </div>
                    </th>

                    <!-- Type -->
                    <th
                        class="px-2 py-3 whitespace-nowrap w-px"
                    >
                        <div
                            class="font-semibold text-center"
                        >
                            {{ t('type') }}
                        </div>
                    </th>

                    <!-- Form -->
                    <th
                        v-if="showForm"
                        class="px-2 py-3 whitespace-nowrap"
                    >
                        <div
                            class="font-semibold text-left"
                        >
                            {{ t('form') }}
                        </div>
                    </th>

                    <!-- Width -->
                    <th
                        class="px-1 py-3 whitespace-nowrap w-px"
                    >
                        <div
                            class="font-semibold text-center"
                        >
                            {{ t('width') }}
                        </div>
                    </th>

                    <!-- Required -->
                    <th
                        class="px-1 py-3 whitespace-nowrap w-px"
                    >
                        <div
                            class="flex items-center justify-center"
                        >
                            <svg
                                class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 640 512">
                                <path
                                    d="M384 320H256c-17.67 0-32 14.33-32 32v128c0 17.67 14.33 32 32 32h128c17.67 0 32-14.33 32-32V352c0-17.67-14.33-32-32-32zM192 32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v128c0 17.67 14.33 32 32 32h95.72l73.16 128.04C211.98 300.98 232.4 288 256 288h.28L192 175.51V128h224V64H192V32zM608 0H480c-17.67 0-32 14.33-32 32v128c0 17.67 14.33 32 32 32h128c17.67 0 32-14.33 32-32V32c0-17.67-14.33-32-32-32z" />
                            </svg>
                        </div>
                    </th>

                    <!-- Options -->
                    <th
                        class="px-1 py-3 whitespace-nowrap w-px"
                    >
                        <div
                            class="font-semibold text-center"
                        >
                            {{ t('variants') }}
                        </div>
                    </th>

                    <!-- Actions -->
                    <th
                        class="px-1 py-3 whitespace-nowrap"
                    >
                        <div
                            class="font-semibold text-end"
                        >
                            {{ t('actions') }}
                        </div>
                    </th>

                    <!-- Select -->
                    <th
                        class="px-1 py-1 whitespace-nowrap
                                   text-center w-px"
                    >
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
                    v-model="localFields"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: field }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <!-- Drag -->
                            <td
                                class="px-1 py-1 text-center
                                       cursor-move handle w-px"
                            >
                                <svg
                                    class="w-4 h-4
                                           text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td
                                class="px-1 py-1 whitespace-nowrap w-px"
                            >
                                <div
                                    class="text-center
                                           text-blue-600 dark:text-blue-200"
                                    :title="`[${field.sort ?? 0}] / ${formatDate(field.updated_at)}`"
                                >
                                    {{ field.id }}
                                </div>
                            </td>

                            <!-- Field -->
                            <td class="px-2 py-1">
                                <div
                                    class="flex flex-col items-start space-y-1"
                                >
                                    <button
                                        type="button"
                                        class="text-xs font-semibold text-left
                                               text-blue-600 dark:text-blue-300
                                               hover:underline"
                                        :title="fieldLabel(field)"
                                        @click="emit('show', field)"
                                    >
                                        {{
                                            truncateText(
                                                fieldLabel(field),
                                                70
                                            )
                                        }}
                                    </button>

                                    <div
                                        class="text-[11px] font-mono
                                               text-slate-500 dark:text-slate-300"
                                    >
                                        {{
                                            truncateText(
                                                field.name,
                                                70
                                            )
                                        }}
                                    </div>

                                    <div
                                        v-if="fieldPlaceholder(field)"
                                        class="text-[10px]
                                               text-cyan-700 dark:text-cyan-300"
                                        :title="fieldPlaceholder(field)"
                                    >
                                        {{
                                            truncateText(
                                                fieldPlaceholder(field),
                                                90
                                            )
                                        }}
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td
                                class="px-2 py-1 whitespace-nowrap"
                            >
                                <div
                                    class="flex flex-col items-center"
                                >
                                    <span
                                        class="px-2 py-0.5 text-[12px]
                                               font-semibold font-mono
                                               bg-indigo-50 dark:bg-slate-600
                                               text-indigo-700 dark:text-indigo-200
                                               border border-indigo-200
                                               dark:border-slate-500 rounded-sm"
                                        :title="field.type"
                                    >
                                        {{ getFieldTypeLabel(field) }}
                                    </span>

                                    <span
                                        v-if="getFieldTypeLabel(field) !== field.type"
                                        class="mt-1 text-[9px] font-mono
                                               text-slate-500 dark:text-slate-300"
                                    >
                                        {{ field.type }}
                                    </span>
                                </div>
                            </td>

                            <!-- Form -->
                            <td
                                v-if="showForm"
                                class="px-2 py-1"
                            >
                                <div
                                    v-if="field.form"
                                    class="flex flex-col items-start space-y-1"
                                >
                                    <div
                                        class="text-xs font-semibold
                                               text-fuchsia-700 dark:text-fuchsia-300"
                                        :title="formTitle(field.form)"
                                    >
                                        {{
                                            truncateText(
                                                formTitle(field.form),
                                                55
                                            )
                                        }}
                                    </div>

                                    <div
                                        v-if="field.form.code"
                                        class="text-[10px] font-mono
                                               text-slate-500 dark:text-slate-300"
                                    >
                                        {{
                                            truncateText(
                                                field.form.code,
                                                55
                                            )
                                        }}
                                    </div>

                                    <div
                                        class="text-[9px]
                                               text-slate-400 dark:text-slate-400"
                                    >
                                        ID: {{ field.form_id }}
                                    </div>
                                </div>

                                <div
                                    v-else
                                    class="text-xs
                                           text-slate-400 dark:text-slate-300"
                                >
                                    {{ t('noData') }}
                                </div>
                            </td>

                            <!-- Width -->
                            <td
                                class="px-1 py-1 whitespace-nowrap"
                            >
                                <div
                                    class="text-center text-xs font-mono
                                           text-slate-600 dark:text-slate-200"
                                >
                                    {{ field.width || '—' }}
                                </div>
                            </td>

                            <!-- Required -->
                            <td
                                class="px-1 py-1 whitespace-nowrap"
                            >
                                <div
                                    class="flex justify-center gap-1 uppercase"
                                >
                                    <span
                                        class="text-[9px] px-2 py-1
                                               rounded-sm border font-semibold"
                                        :class="booleanClasses(field.required)"
                                        :title="t('required')"
                                    >
                                        {{ booleanLabel(field.required) }}
                                    </span>
                                    <span
                                        class="text-[9px] px-2 py-1
                                               rounded-sm border font-semibold"
                                        :class="booleanClasses(field.readonly)"
                                        :title="t('readOnly')"
                                    >
                                        {{ booleanLabel(field.readonly) }}
                                    </span>
                                    <span
                                        class="text-[9px] px-2 py-1
                                               rounded-sm border font-semibold"
                                        :class="booleanClasses(field.disabled)"
                                        :title="t('disabled')"
                                    >
                                        {{ booleanLabel(field.disabled) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Options -->
                            <td
                                class="px-1 py-1 whitespace-nowrap"
                            >
                                <div
                                    class="flex justify-center"
                                >
                                    <span
                                        class="min-w-6 px-2 py-0.5 text-xs
                                               text-center font-semibold
                                               text-blue-600 dark:text-blue-300
                                               bg-blue-50 dark:bg-slate-600
                                               border border-blue-200
                                               dark:border-slate-500
                                               rounded-sm"
                                        title="Количество вариантов поля"
                                    >
                                        {{ field.options_count ?? 0 }}
                                    </span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td
                                class="px-1 py-1 whitespace-nowrap"
                            >
                                <div
                                    class="flex justify-end
                                           items-center space-x-1"
                                >
                                    <ActivityToggle
                                        :isActive="field.activity"
                                        :title="
                                            field.activity
                                                ? t('enabled')
                                                : t('disabled')
                                        "
                                        @toggle-activity="
                                            emit(
                                                'toggle-activity',
                                                field
                                            )
                                        "
                                    />

                                    <IconEdit
                                        :href="
                                            route(
                                                'admin.formFields.edit',
                                                {
                                                    formField:
                                                        field.id,
                                                }
                                            )
                                        "
                                    />

                                    <DeleteIconButton
                                        @delete="
                                            emit(
                                                'delete',
                                                field
                                            )
                                        "
                                    />
                                </div>
                            </td>

                            <!-- Select -->
                            <td
                                class="px-1 py-1 whitespace-nowrap"
                            >
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedFields.includes(
                                                field.id
                                            )
                                        "
                                        @change="
                                            emit(
                                                'toggle-select',
                                                field.id
                                            )
                                        "
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center
                       text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
