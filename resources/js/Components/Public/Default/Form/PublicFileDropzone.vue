<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: [Array, File],
        default: null,
    },

    field: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'update:modelValue',
])

const input = ref(null)
const isDragging = ref(false)

/**
 * Настройки загрузки.
 */
const multiple = computed(() => {
    return Boolean(props.field.settings?.multiple)
})

const maxFiles = computed(() => {
    const value = Number(props.field.settings?.max_files)

    return Number.isFinite(value) && value > 0
        ? value
        : multiple.value
            ? null
            : 1
})

const maxSize = computed(() => {
    const value = Number(props.field.settings?.max_size)

    return Number.isFinite(value) && value > 0
        ? value
        : null
})

const extensions = computed(() => {
    if (!Array.isArray(props.field.settings?.extensions)) {
        return []
    }

    return props.field.settings.extensions
        .map((extension) => String(extension)
            .trim()
            .toLowerCase()
            .replace(/^\./, '')
        )
        .filter(Boolean)
})

/**
 * Файлы всегда приводим к массиву
 * для удобной работы внутри Drop Zone.
 */
const files = computed(() => {
    if (Array.isArray(props.modelValue)) {
        return props.modelValue
    }

    if (props.modelValue instanceof File) {
        return [props.modelValue]
    }

    return []
})

/**
 * accept для системного окна выбора файлов.
 */
const accept = computed(() => {
    if (!extensions.value.length) {
        return undefined
    }

    return extensions.value
        .map((extension) => `.${extension}`)
        .join(',')
})

/**
 * Можно ли добавлять новые файлы.
 */
const canAddFiles = computed(() => {
    if (!multiple.value) {
        return files.value.length === 0
    }

    if (!maxFiles.value) {
        return true
    }

    return files.value.length < maxFiles.value
})

/**
 * Текст допустимых расширений.
 */
const extensionsText = computed(() => {
    if (!extensions.value.length) {
        return null
    }

    return extensions.value
        .map((extension) => extension.toUpperCase())
        .join(', ')
})

/**
 * Человекочитаемый размер.
 */
const formatBytes = (bytes) => {
    if (!Number.isFinite(bytes) || bytes <= 0) {
        return '0 Б'
    }

    const units = [
        'Б',
        'КБ',
        'МБ',
        'ГБ',
    ]

    const index = Math.min(
        Math.floor(
            Math.log(bytes) / Math.log(1024)
        ),
        units.length - 1
    )

    const value = bytes / (1024 ** index)

    return `${value.toFixed(index === 0 ? 0 : 1)} ${units[index]}`
}

/**
 * Уникальный ключ файла.
 *
 * Не даём случайно добавить один и тот же
 * файл несколько раз.
 */
const fileKey = (file) => {
    return [
        file.name,
        file.size,
        file.lastModified,
    ].join(':')
}

/**
 * Добавление файлов.
 */
const addFiles = (incomingFiles) => {
    let incoming = Array.from(incomingFiles || [])

    if (!incoming.length) {
        return
    }

    /**
     * Для одиночного поля оставляем
     * только первый выбранный файл.
     */
    if (!multiple.value) {
        emit(
            'update:modelValue',
            incoming[0] ?? null
        )

        resetInput()

        return
    }

    const current = [...files.value]

    const existingKeys = new Set(
        current.map(fileKey)
    )

    /**
     * Добавляем только новые файлы.
     */
    incoming = incoming.filter((file) => {
        const key = fileKey(file)

        if (existingKeys.has(key)) {
            return false
        }

        existingKeys.add(key)

        return true
    })

    let result = [
        ...current,
        ...incoming,
    ]

    /**
     * Ограничиваем количество файлов.
     *
     * Серверная валидация всё равно
     * остаётся обязательной.
     */
    if (maxFiles.value) {
        result = result.slice(
            0,
            maxFiles.value
        )
    }

    emit(
        'update:modelValue',
        result
    )

    resetInput()
}

/**
 * Выбор через системное окно.
 */
const handleInput = (event) => {
    addFiles(event.target.files)
}

/**
 * Drag & Drop.
 */
const handleDrop = (event) => {
    isDragging.value = false

    if (!canAddFiles.value) {
        return
    }

    addFiles(
        event.dataTransfer?.files
    )
}

/**
 * Удаление одного файла.
 */
const removeFile = (index) => {
    const result = [...files.value]

    result.splice(index, 1)

    emit(
        'update:modelValue',
        multiple.value
            ? result
            : null
    )

    resetInput()
}

/**
 * Очистка всех файлов.
 */
const clearFiles = () => {
    emit(
        'update:modelValue',
        multiple.value
            ? []
            : null
    )

    resetInput()
}

/**
 * Открытие системного окна.
 */
const openFileDialog = () => {
    if (!canAddFiles.value) {
        return
    }

    input.value?.click()
}

/**
 * Сбрасываем native input,
 * чтобы один и тот же файл можно было
 * выбрать повторно после удаления.
 */
const resetInput = () => {
    if (input.value) {
        input.value.value = ''
    }
}
</script>

<template>
    <div class="space-y-3">
        <!-- Native input -->
        <input
            ref="input"
            type="file"
            class="hidden"
            :name="multiple ? `${field.name}[]` : field.name"
            :accept="accept"
            :multiple="multiple"
            :disabled="field.disabled"
            @change="handleInput"
        />

        <!-- Drop Zone -->
        <button
            type="button"
            class="group relative flex w-full flex-col items-center
                   justify-center rounded-2xl border-2 border-dashed
                   px-6 py-8 text-center transition"
            :class="[
                isDragging
                    ? 'border-sky-500 bg-sky-50 dark:bg-sky-950/20'
                    : 'border-gray-300 bg-gray-50/70 hover:border-sky-400 hover:bg-sky-50/50 ' +
                     'dark:border-gray-600 dark:bg-gray-900/50 dark:hover:border-sky-600 ' +
                      'dark:hover:bg-gray-900',

                !canAddFiles || field.disabled
                    ? 'cursor-not-allowed opacity-60'
                    : 'cursor-pointer',
            ]"
            :disabled="field.disabled || !canAddFiles"
            @click="openFileDialog"
            @dragenter.prevent="isDragging = true"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
        >
            <!-- Иконка -->
            <span
                class="mb-3 flex h-12 w-12 items-center justify-center
                       rounded-full bg-sky-100 text-sky-600
                       transition group-hover:bg-sky-200
                       dark:bg-sky-950 dark:text-sky-400"
            >
                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M12 16V4" />
                    <path d="m7 9 5-5 5 5" />
                    <path d="M20 15v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4" />
                </svg>
            </span>

            <span
                v-if="canAddFiles"
                class="text-sm font-semibold text-gray-800 dark:text-gray-200"
            >
                {{ t('dragOrClickToUpload') }}
            </span>

            <span
                v-else
                class="text-sm font-semibold text-gray-800 dark:text-gray-200"
            >
                {{ t('maxNumberFilesText') }}
            </span>

            <!-- Ограничения -->
            <span
                v-if="extensionsText"
                class="mt-3 text-xs text-gray-500 dark:text-gray-400"
            >
                {{ extensionsText }}
            </span>

            <span
                v-if="maxSize || maxFiles"
                class="mt-1 font-semibold text-xs text-amber-600 dark:text-amber-200"
            >
                <template v-if="maxSize">
                    {{ t('maxFileSizeKb') }} {{ formatBytes(maxSize) }}
                </template>

                <template v-if="maxSize && maxFiles">
                    ·
                </template>

                <template v-if="maxFiles && multiple">
                    {{ t('maxNumberFiles') }} {{ maxFiles }}
                </template>
            </span>
        </button>

        <!-- Выбранные файлы -->
        <div
            v-if="files.length"
            class="overflow-hidden rounded-xl border border-gray-200
                   bg-white dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Заголовок -->
            <div
                class="flex items-center justify-between gap-3
                       border-b border-gray-200 px-4 py-3
                       dark:border-gray-700"
            >
                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ t('filesSelected') }}:
                    {{ files.length }}
                    <template v-if="maxFiles && multiple">
                        / {{ maxFiles }}
                    </template>
                </div>

                <button
                    type="button"
                    class="text-xs font-medium text-gray-500 transition
                           hover:text-red-600 dark:text-gray-400
                           dark:hover:text-red-400"
                    @click="clearFiles"
                >
                    {{ t('clear') }}
                </button>
            </div>

            <!-- Список -->
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                <div
                    v-for="(file, index) in files"
                    :key="fileKey(file)"
                    class="flex items-center gap-3 px-4 py-3"
                >
                    <!-- Файл -->
                    <div
                        class="flex h-9 w-9 shrink-0 items-center
                               justify-center rounded-lg bg-gray-100
                               text-gray-500 dark:bg-gray-800
                               dark:text-gray-400"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                            <path d="M14 2v6h6" />
                        </svg>
                    </div>

                    <!-- Информация -->
                    <div class="min-w-0 flex-1">
                        <div
                            class="truncate text-sm font-medium
                                   text-gray-800 dark:text-gray-200"
                            :title="file.name"
                        >
                            {{ file.name }}
                        </div>

                        <div
                            class="mt-0.5 text-xs
                                   text-gray-500 dark:text-gray-400"
                        >
                            {{ formatBytes(file.size) }}
                        </div>
                    </div>

                    <!-- Удалить -->
                    <button
                        type="button"
                        class="flex h-8 w-8 shrink-0 items-center
                               justify-center rounded-lg text-gray-400
                               transition hover:bg-red-50 hover:text-red-600
                               dark:hover:bg-red-950/30 dark:hover:text-red-400"
                        :aria-label="t('delete')"
                        @click="removeFile(index)"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
