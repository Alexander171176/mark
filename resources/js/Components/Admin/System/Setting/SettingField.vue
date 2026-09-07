<script setup>
import {
    computed,
    defineEmits,
    defineProps,
    watch,
} from 'vue'

import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import IconSaveButton from '@/Components/Admin/UI/Buttons/IconSaveButton.vue'

import InfoIconButton from '@/Components/Admin/System/Setting/Button/InfoIconButton.vue'
import LabelInput from '@/Components/Admin/System/Setting/Input/LabelInput.vue'

import SettingText from '@/Components/Admin/System/Setting/Fields/SettingText.vue'
import SettingNumber from '@/Components/Admin/System/Setting/Fields/SettingNumber.vue'
import SettingCheckbox from '@/Components/Admin/System/Setting/Fields/SettingCheckbox.vue'
import SettingSelect from '@/Components/Admin/System/Setting/Fields/SettingSelect.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    setting: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'show-description',
])

/**
 * Нормализованный тип настройки.
 */
const settingType = computed(() => {
    return String(
        props.setting?.type || 'string'
    )
        .trim()
        .toLowerCase()
})

/**
 * Уникальный ID поля.
 */
const fieldId = computed(() => {
    return `setting-${props.setting.id}`
})

/**
 * Нормализация значения настройки.
 */
const normalizeValue = (value) => {
    if (settingType.value === 'checkbox') {
        return value === 'true'
            ? 'true'
            : 'false'
    }

    return value ?? ''
}

/**
 * Форма настройки.
 */
const form = useForm({
    value: normalizeValue(
        props.setting?.value
    ),
})

/**
 * Синхронизация значения,
 * если настройка пришла заново с сервера.
 */
watch(
    () => props.setting?.value,
    (newValue) => {
        form.value = normalizeValue(
            newValue
        )
    }
)

/**
 * Определяем, является ли настройка
 * режимом обработки данных.
 *
 * Только ProcessingMode используем
 * как select.
 */
const isProcessingMode = computed(() => {
    const option = String(
        props.setting?.option || ''
    )

    return option.endsWith(
        'ProcessingMode'
    )
})

/**
 * Варианты ProcessingMode.
 */
const processingModeOptions = [
    {
        value: 'frontend',
        label: 'Frontend',
    },
    {
        value: 'server',
        label: 'Server',
    },
    {
        value: 'auto',
        label: 'Auto',
    },
]

/**
 * Компонент поля по типу настройки.
 */
const fieldComponent = computed(() => {
    switch (settingType.value) {
        case 'checkbox':
            return SettingCheckbox

        case 'number':
            return SettingNumber

        case 'string':
        default:
            return SettingText
    }
})

/**
 * Заголовок настройки.
 */
const settingLabel = computed(() => {
    return props.setting?.option || ''
})

/**
 * Сохранение значения настройки.
 */
const submitForm = () => {
    if (!props.setting?.id) {
        return
    }

    /**
     * Checkbox всегда сохраняем
     * строкой "true" / "false".
     */
    if (
        settingType.value === 'checkbox'
    ) {
        form.value =
            form.value === 'true'
                ? 'true'
                : 'false'
    }

    form.put(
        route(
            'admin.actions.settings.updateValue',
            {
                setting: props.setting.id,
            }
        ),
        {
            preserveScroll: true,

            onSuccess: (page) => {
                if (
                    page.props.flash?.success
                ) {
                    toast.success(
                        page.props.flash.success
                    )

                    return
                }

                toast.success(
                    `Настройка "${props.setting.option}" успешно обновлена.`
                )
            },

            onError: (errors) => {
                const firstError =
                    errors[
                        Object.keys(errors)[0]
                        ]

                toast.error(
                    firstError
                    || `Ошибка обновления настройки "${props.setting.option}".`
                )
            },
        }
    )
}

/**
 * Открытие описания настройки.
 */
const showDescription = () => {
    emit(
        'show-description',
        props.setting?.description || ''
    )
}
</script>

<template>
    <form
        @submit.prevent="submitForm"
        class="border-dashed border-b border-slate-500 dark:border-slate-400 last:border-b-0"
    >
        <div
            class="py-2 flex flex-col gap-4 xl:flex-row xl:items-center"
        >
            <!-- Название настройки -->
            <div
                class="w-full xl:w-1/3 xl:min-w-[320px]"
            >
                <LabelInput
                    :for="fieldId"
                    :value="settingLabel"
                />

                <div
                    class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1
                           font-semibold text-xs text-slate-600 dark:text-slate-400"
                >
                    <span>
                        {{ setting.category }}
                    </span>

                    <span>
                        ·
                    </span>

                    <span>
                        {{ setting.type }}
                    </span>
                </div>
            </div>

            <div class="w-full flex items-center justify-between">
                <!-- Значение -->
                <div
                    class="w-full min-w-0 xl:flex-1"
                >
                    <SettingSelect
                        v-if="isProcessingMode"
                        :id="fieldId"
                        v-model="form.value"
                        :options="processingModeOptions"
                    />

                    <component
                        v-else
                        :is="fieldComponent"
                        :id="fieldId"
                        v-model="form.value"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.value"
                    />
                </div>

                <!-- Действия -->
                <div
                    class="w-full flex items-center justify-end gap-2
                       xl:w-auto xl:flex-shrink-0"
                >
                    <IconSaveButton
                        :class="{
                        'opacity-25':
                            form.processing
                    }"
                        :disabled="form.processing"
                    >
                        {{ t('save') }}
                    </IconSaveButton>

                    <InfoIconButton
                        v-if="setting.description"
                        @click="showDescription"
                    />
                </div>
            </div>
        </div>
    </form>
</template>
