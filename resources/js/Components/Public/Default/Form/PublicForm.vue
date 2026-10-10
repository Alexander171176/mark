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

// Диагностика доступна только при разработке.
const showDiagnostics = import.meta.env.DEV
const requestDiagnostics = ref(null)
const technicalFieldLabels = {
    _form_website: 'Проверка Honeypot',
    _form_token: 'Токен защиты формы',
    _form: 'Антиспам-проверка',
    _captcha_answer: 'Код CAPTCHA',
    _captcha_token: 'Токен CAPTCHA',
}

const validationDetails = computed(() => Object.entries(errors).map(([name, message]) => ({
    name,
    label: technicalFieldLabels[name] || fields.value.find(field => field.name === name)?.translation?.label || name,
    message,
})))

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
            errorMessage.value = 'Не удалось подготовить защиту формы. Повторите попытку.'
        }
        captureRequestError(error, 'Получение токена защиты')
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
            errorMessage.value = 'Сервер вернул некорректную CAPTCHA.'
        }
    } catch (error) {
        if (id === initializationId) {
            errorMessage.value = 'Не удалось загрузить CAPTCHA. Обновите изображение.'
        }
        captureRequestError(error, 'Загрузка CAPTCHA')
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
    requestDiagnostics.value = null
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
        errorMessage.value = 'Защита формы ещё не готова. Обновите CAPTCHA или откройте форму заново.'
        return
    }
    if (props.form?.captcha_enabled && !captchaAnswer.value.trim()) {
        errors._captcha_answer = 'Введите код с изображения.'
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
        captureRequestError(error)
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
                errors._captcha_answer = 'Код неверен или устарел. Введите новый код.'
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

        // Технические детали доступны в блоке диагностики ниже.
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

        <!-- Ошибки Laravel: видны непосредственно в форме. -->
        <div v-if="validationDetails.length" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm dark:border-red-800 dark:bg-red-950/40">
            <p class="mb-2 font-semibold text-red-700 dark:text-red-300">Ошибки проверки:</p>
            <ul class="list-disc space-y-1 pl-5 text-red-700 dark:text-red-300">
                <li v-for="item in validationDetails" :key="item.name">
                    <strong>{{ item.label }}:</strong> {{ item.message }}
                </li>
            </ul>
        </div>

        <!-- Технические детали показываем только в режиме разработки. -->
        <details v-if="showDiagnostics && requestDiagnostics" class="rounded-lg border border-gray-300 p-3 text-xs dark:border-gray-700">
            <summary class="cursor-pointer font-semibold">Диагностика запроса (DEV)</summary>
            <div class="mt-3 space-y-1 break-all">
                <p>Операция: {{ requestDiagnostics.action }}</p>
                <p>HTTP: {{ requestDiagnostics.status ?? 'Нет ответа' }}</p>
                <p>{{ requestDiagnostics.method }} {{ requestDiagnostics.url }}</p>
                <p>{{ requestDiagnostics.message }}</p>
                <pre v-if="requestDiagnostics.errors" class="mt-2 overflow-x-auto whitespace-pre-wrap">{{ JSON.stringify(requestDiagnostics.errors, null, 2) }}</pre>
            </div>
        </details>

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
        <div v-if="form.honeypot_enabled" class="absolute -left-[10000px] h-px w-px overflow-hidden" aria-hidden="true">
            <label for="form-website-hp">Не заполняйте это поле</label>
            <input id="form-website-hp" v-model="honeypotValue" type="text" name="_form_website" tabindex="-1" autocomplete="off" />
        </div>

        <!-- Отправка -->
        <div
            v-if="fields.length"
            class="flex items-center justify-center py-1"
        >
            <button
                type="submit"
                :disabled="processing || protectionLoading || captchaLoading || (form.spam_protection && !protectionReady) || (form.captcha_enabled && !captchaReady)"
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
