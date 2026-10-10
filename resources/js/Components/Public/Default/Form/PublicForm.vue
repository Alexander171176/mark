<script setup>
import { computed, reactive, ref, watch, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import PublicFormField from '@/Components/Public/Default/Form/PublicFormField.vue'
import PublicFormCaptcha from '@/Components/Public/Default/Form/PublicFormCaptcha.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    description: {
        type: String,
        default: '',
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
 * Техническая диагностика HTTP-запросов.
 * Отключена для продакшена. При необходимости
 * можно временно вернуть для локальной отладки.
 */
/*
const showDiagnostics = import.meta.env.DEV
const requestDiagnostics = ref(null)

const captureRequestError = (error, action = 'Отправка формы') => {
    const response = error?.response
    requestDiagnostics.value = {
        action,
        status: response?.status ?? null,
        method: error?.config?.method?.toUpperCase() || null,
        url: error?.config?.url || null,
        message: response?.data?.message || error?.message || 'Неизвестная ошибка',
        errors: response?.data?.errors || null,
    }
}
*/

/** Перевод названий технических полей для блока ошибок валидации. */
const technicalFieldLabels = computed(() => ({
    _form_website: t('honeypotCheck'),
    _form_token: t('protectionToken'),
    _form: t('spamCheck'),
    _captcha_answer: t('captchaCode'),
    _captcha_token: t('captchaToken'),
}))

const validationDetails = computed(() => Object.entries(errors).map(([name, message]) => ({
    name,
    label: technicalFieldLabels.value[name] || fields.value.find(field => field.name === name)?.translation?.label || name,
    message,
})))


const honeypotValue = ref('')
const protectionToken = ref('')
const protectionLoading = ref(false)
const protectionReady = ref(false)
const captchaToken = ref('')
const captchaImage = ref('')
const captchaAnswer = ref('')
const captchaLoading = ref(false)
const captchaReady = ref(false)
let initializationId = 0

/** Получить токен серверного времени заполнения. */
const loadProtection = async (id) => {
    if (!props.form?.spam_protection) {
        protectionReady.value = true
        return
    }

    protectionLoading.value = true
    try {
        const response = await axios.get(route('forms.protection', {
            formCode: props.form.code,
        }))
        if (id !== initializationId) return
        protectionToken.value = response.data?.token || ''
        protectionReady.value = Boolean(protectionToken.value)
    } catch (error) {
        if (id === initializationId) {
            protectionReady.value = false
            errorMessage.value = t('protectionLoadError')
        }
        // captureRequestError(error, 'Получение токена защиты')
    } finally {
        if (id === initializationId) protectionLoading.value = false
    }
}

/** Получить или обновить изображение CAPTCHA. */
const loadCaptcha = async (id = initializationId) => {
    if (!props.form?.captcha_enabled) {
        captchaReady.value = true
        return
    }

    captchaLoading.value = true
    captchaReady.value = false
    captchaAnswer.value = ''
    captchaToken.value = ''
    captchaImage.value = ''
    delete errors._captcha_answer

    try {
        const response = await axios.get(route('forms.captcha', {
            formCode: props.form.code,
        }))
        if (id !== initializationId) return
        captchaToken.value = response.data?.token || ''
        captchaImage.value = response.data?.image || ''
        captchaReady.value = Boolean(captchaToken.value && captchaImage.value)
        if (!captchaReady.value) {
            errorMessage.value = t('captchaInvalidResponse')
        }
    } catch (error) {
        if (id === initializationId) {
            errorMessage.value = t('captchaLoadError')
        }
        // captureRequestError(error, 'Загрузка CAPTCHA')
    } finally {
        if (id === initializationId) captchaLoading.value = false
    }
}

/** Запуск защит при каждом открытии или переключении формы. */
const initializeProtection = () => {
    const id = ++initializationId
    honeypotValue.value = ''
    protectionToken.value = ''
    protectionReady.value = false
    captchaToken.value = ''
    captchaImage.value = ''
    captchaAnswer.value = ''
    captchaReady.value = false
    protectionLoading.value = false
    captchaLoading.value = false
    if (!props.form?.code) return
    void loadProtection(id)
    void loadCaptcha(id)
}

onBeforeUnmount(() => { initializationId++ })


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
    // requestDiagnostics.value = null
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

    // Технические поля защиты не относятся к динамическим полям Form Builder.
    if (props.form?.honeypot_enabled) {
        formData.append('_form_website', honeypotValue.value)
    }
    if (props.form?.spam_protection) {
        formData.append('_form_token', protectionToken.value)
    }
    if (props.form?.captcha_enabled) {
        formData.append('_captcha_token', captchaToken.value)
        formData.append('_captcha_answer', captchaAnswer.value.trim())
    }

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

    if ((props.form?.spam_protection && !protectionReady.value) ||
        (props.form?.captcha_enabled && !captchaReady.value)) {
        errorMessage.value = t('protectionNotReady')
        return
    }
    if (props.form?.captcha_enabled && !captchaAnswer.value.trim()) {
        errors._captcha_answer = t('captchaRequired')
        return
    }

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
        // captureRequestError(error)
        if (
            error.response?.status === 422 &&
            error.response?.data?.errors
        ) {
            setValidationErrors(
                error.response.data.errors
            )

            // Не сбрасываем обычные поля и файлы при ошибке.
            // При ошибке CAPTCHA выдаём новый одноразовый код.
            if (errors._captcha_answer && props.form?.captcha_enabled) {
                await loadCaptcha()
                // Сохраняем понятное сообщение об ошибке после обновления.
                errors._captcha_answer = t('captchaExpired')
            }
            // Токен времени мог истечь или быть уже использован.
            if (errors._form && props.form?.spam_protection) {
                await loadProtection(initializationId)
            }

            errorMessage.value =
                error.response.data?.message ||
                props.form.translation?.error_message ||
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

        // Техническая диагностика отключена для продакшена.
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
    () => {
        initializeValues()
        initializeProtection()
    },
    {
        immediate: true,
    }
)
</script>

<template>
    <form
        class="flex min-h-0 flex-1 flex-col"
        novalidate
        @submit.prevent="submit"
    >
        <!-- Прокручивается только содержимое, а не кнопка отправки -->
        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
            <p
                v-if="description"
                class="text-sm leading-6 text-gray-600 dark:text-gray-300"
            >
                {{ description }}
            </p>
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

            <!-- Ошибки Laravel: видны непосредственно в форме. -->
            <div v-if="validationDetails.length" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm dark:border-red-800 dark:bg-red-950/40">
                <p class="mb-2 font-semibold text-red-700 dark:text-red-300">{{ t('validationErrors') }}</p>
                <ul class="list-disc space-y-1 pl-5 text-red-700 dark:text-red-300">
                    <li v-for="item in validationDetails" :key="item.name">
                        <strong>{{ item.label }}:</strong> {{ item.message }}
                    </li>
                </ul>
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

            <!-- CAPTCHA -->
            <PublicFormCaptcha
                v-if="form.captcha_enabled"
                :image="captchaImage"
                :model-value="captchaAnswer"
                :loading="captchaLoading"
                :error="errors._captcha_answer || null"
                @update:model-value="(value) => {
                    captchaAnswer = value
                    delete errors._captcha_answer
                }"
                @refresh="loadCaptcha()"
            />

            <!-- Ошибка серверной антиспам-проверки -->
            <p v-if="errors._form" class="text-sm text-red-600 dark:text-red-400">
                {{ errors._form }}
            </p>

            <!-- Honeypot: не скрытый type=hidden, а невидимое для людей текстовое поле. -->
            <div
                v-if="form.honeypot_enabled"
                class="absolute -left-[10000px] h-px w-px overflow-hidden" aria-hidden="true">
                <label for="form-website-hp">{{ t('honeypotLabel') }}</label>
                <input
                    id="form-website-hp"
                    v-model="honeypotValue"
                    type="text"
                    name="_form_website"
                    tabindex="-1"
                    autocomplete="off" />
            </div>

            <!-- Нет полей -->
            <div
                v-if="!fields.length"
                class="rounded-xl border border-dashed border-gray-400
                       p-4 text-center text-sm text-gray-500
                       dark:border-gray-500 dark:text-gray-400"
            >
                {{ t('noData') }}
            </div>
        </div>

        <!-- Отправка -->
        <div
            v-if="fields.length"
            class="z-20 flex shrink-0 items-center justify-center border-t
                   border-gray-200 bg-white px-5 py-3
                   shadow-[0_-4px_12px_rgba(0,0,0,0.04)]
                   dark:border-gray-700 dark:bg-gray-800"
        >
            <button
                type="submit"
                :disabled="processing || protectionLoading || captchaLoading || (form.spam_protection && !protectionReady) || (form.captcha_enabled && !captchaReady)"
                class="inline-flex items-center justify-center rounded-md
                       bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white
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

                <svg v-else
                     class="-ml-0.5 mr-2 h-4 w-4 fill-current text-slate-100"
                     viewBox="0 0 512 512">
                    <path
                        d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z" />
                </svg>


                {{ processing ? t('sending') : (form.translation?.submit_text || t('send')) }}
            </button>
        </div>

    </form>
</template>
