<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import PublicFormField from '@/Components/Public/Default/Form/PublicFormField.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'success',
])

/**
 * Значения полей формы.
 */
const values = reactive({})

/**
 * Ошибки серверной валидации.
 */
const errors = reactive({})

/**
 * Состояние отправки.
 */
const processing = ref(false)

/**
 * Общая ошибка отправки.
 */
const errorMessage = ref('')

/**
 * Активные поля формы.
 */
const fields = computed(() => {
    return Array.isArray(props.form?.fields)
        ? props.form.fields
        : []
})

/**
 * Начальное значение поля.
 */
const getInitialValue = (field) => {
    if (field.type === 'file') {
        return field.settings?.multiple
            ? []
            : null
    }

    if (field.type === 'checkbox_group') {
        if (Array.isArray(field.default_value)) {
            return field.default_value
        }

        const defaults = (field.options || [])
            .filter((option) => option.is_default)
            .map((option) => option.value)

        return defaults.length
            ? defaults
            : []
    }

    if (field.type === 'checkbox') {
        if (
            field.default_value !== null &&
            field.default_value !== undefined
        ) {
            return [
                true,
                1,
                '1',
                'true',
                'on',
                'yes',
            ].includes(field.default_value)
        }

        return false
    }

    if (
        field.type === 'select' ||
        field.type === 'radio'
    ) {
        if (
            field.default_value !== null &&
            field.default_value !== undefined &&
            field.default_value !== ''
        ) {
            return field.default_value
        }

        const defaultOption = (field.options || [])
            .find((option) => option.is_default)

        if (defaultOption) {
            return defaultOption.value
        }
    }

    return field.default_value ?? ''
}

/**
 * Очистить ошибки формы.
 */
const clearErrors = () => {
    Object.keys(errors).forEach((name) => {
        delete errors[name]
    })

    errorMessage.value = ''
}

/**
 * Инициализация значений формы.
 */
const initializeValues = () => {
    const fieldNames = new Set(
        fields.value.map((field) => field.name)
    )

    /**
     * Удаляем значения полей,
     * которых больше нет в текущей форме.
     */
    Object.keys(values).forEach((name) => {
        if (!fieldNames.has(name)) {
            delete values[name]
        }
    })

    /**
     * Добавляем начальные значения.
     */
    fields.value.forEach((field) => {
        values[field.name] = getInitialValue(field)
    })

    clearErrors()
}

/**
 * Обновление значения поля.
 */
const updateField = (name, value) => {
    values[name] = value

    /**
     * После изменения поля удаляем
     * его предыдущую серверную ошибку.
     */
    if (errors[name]) {
        delete errors[name]
    }

    errorMessage.value = ''
}

/**
 * Добавить значение поля в FormData.
 */
const appendFieldToFormData = (
    formData,
    field,
    value
) => {
    /**
     * Disabled-поля сервер не принимает.
     */
    if (field.disabled) {
        return
    }

    /**
     * Множественные файлы.
     */
    if (
        field.type === 'file' &&
        field.settings?.multiple
    ) {
        if (!Array.isArray(value)) {
            return
        }

        value.forEach((file) => {
            if (file instanceof File) {
                formData.append(
                    `${field.name}[]`,
                    file
                )
            }
        })

        return
    }

    /**
     * Один файл.
     */
    if (field.type === 'file') {
        if (value instanceof File) {
            formData.append(
                field.name,
                value
            )
        }

        return
    }

    /**
     * Группа checkbox.
     */
    if (field.type === 'checkbox_group') {
        if (!Array.isArray(value)) {
            return
        }

        value.forEach((item) => {
            formData.append(
                `${field.name}[]`,
                item
            )
        })

        return
    }

    /**
     * Обычный checkbox.
     *
     * Передаём 1/0, которые Laravel
     * корректно принимает правилом boolean.
     */
    if (field.type === 'checkbox') {
        formData.append(
            field.name,
            value ? '1' : '0'
        )

        return
    }

    /**
     * Пустое nullable-значение можно
     * передать пустой строкой.
     */
    if (
        value === null ||
        value === undefined
    ) {
        formData.append(
            field.name,
            ''
        )

        return
    }

    formData.append(
        field.name,
        String(value)
    )
}

/**
 * Собрать универсальный multipart FormData.
 */
const buildFormData = () => {
    const formData = new FormData()

    fields.value.forEach((field) => {
        appendFieldToFormData(
            formData,
            field,
            values[field.name]
        )
    })

    return formData
}

/**
 * Получить первую ошибку Laravel
 * для конкретного поля.
 *
 * Для файлов Laravel может вернуть:
 * files.0, files.1 и т.д.
 */
const getFieldError = (fieldName) => {
    if (errors[fieldName]) {
        return errors[fieldName]
    }

    const nestedKey = Object.keys(errors)
        .find((key) => {
            return key.startsWith(
                `${fieldName}.`
            )
        })

    return nestedKey
        ? errors[nestedKey]
        : null
}

/**
 * Записать Laravel validation errors.
 */
const setValidationErrors = (validationErrors) => {
    clearErrors()

    Object.entries(
        validationErrors || {}
    ).forEach(([name, messages]) => {
        errors[name] = Array.isArray(messages)
            ? messages[0]
            : String(messages)
    })
}

/**
 * Отправка публичной формы.
 */
const submit = async () => {
    if (
        processing.value ||
        !props.form?.code
    ) {
        return
    }

    clearErrors()
    processing.value = true

    try {
        const formData = buildFormData()

        const response = await axios.post(
            route(
                'forms.submit',
                {
                    formCode: props.form.code,
                }
            ),
            formData
        )

        const message =
            response.data?.message ||
            props.form.translation?.success_message ||
            t('success')

        toast.success(message)

        emit(
            'success',
            response.data
        )
    } catch (error) {
        if (
            error.response?.status === 422 &&
            error.response?.data?.errors
        ) {
            setValidationErrors(
                error.response.data.errors
            )

            errorMessage.value =
                props.form.translation?.error_message ||
                error.response.data?.message ||
                t('error')

            return
        }

        if (error.response?.status === 401) {
            errorMessage.value =
                error.response.data?.message ||
                t('error')

            return
        }

        errorMessage.value =
            props.form.translation?.error_message ||
            error.response?.data?.message ||
            t('error')

        console.error(
            'Public form submission error:',
            error
        )
    } finally {
        processing.value = false
    }
}

/**
 * При смене формы полностью
 * пересобираем её начальное состояние.
 */
watch(
    () => props.form,
    initializeValues,
    {
        immediate: true,
    }
)
</script>

<template>
    <form
        class="space-y-4 px-1"
        novalidate
        @submit.prevent="submit"
    >
        <!-- Общая ошибка -->
        <div
            v-if="errorMessage"
            class="rounded-lg border border-red-200 bg-red-50
                   px-4 py-3 text-sm text-red-700
                   dark:border-red-800 dark:bg-red-950/40
                   dark:text-red-300"
        >
            {{ errorMessage }}
        </div>

        <!-- Поля -->
        <div
            class="grid grid-cols-1 gap-x-5 gap-y-5
                   md:grid-cols-2"
        >
            <PublicFormField
                v-for="field in fields"
                :key="field.id"
                :field="field"
                :model-value="values[field.name]"
                :error="getFieldError(field.name)"
                @update:model-value="
                    updateField(
                        field.name,
                        $event
                    )
                "
            />
        </div>

        <!-- Отправка -->
        <div
            v-if="fields.length"
            class="flex items-center justify-center py-1"
        >
            <button
                type="submit"
                :disabled="processing"
                class="inline-flex items-center justify-center rounded-md
                       bg-sky-600 px-3 py-1.5 text-sm font-semibold text-white
                       shadow-sm transition hover:bg-sky-500
                       focus:outline-none focus:ring-2 focus:ring-sky-500
                       focus:ring-offset-2
                       disabled:cursor-not-allowed disabled:opacity-50
                       dark:focus:ring-offset-gray-800"
            >
                <svg
                    v-if="processing"
                    class="-ml-0.5 mr-2 h-4 w-4 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0
                           00-4 4H4z"
                    />
                </svg>

                {{
                    processing
                        ? t('sending')
                        : (
                            form.translation?.submit_text ||
                            t('send')
                        )
                }}
            </button>
        </div>

        <!-- Нет полей -->
        <div
            v-else
            class="rounded-xl border border-dashed border-gray-400
                   p-4 text-center text-sm text-gray-500
                   dark:border-gray-500 dark:text-gray-400"
        >
            {{ t('noData') }}
        </div>
    </form>
</template>
