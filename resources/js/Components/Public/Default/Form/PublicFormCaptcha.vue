
<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    image: {
        type: String,
        default: '',
    },
    modelValue: {
        type: String,
        default: '',
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: null,
    },
})

const emit = defineEmits([
    'update:modelValue',
    'refresh',
])

/**
 * SVG формируется Laravel-сервисом.
 * Не используем v-html, отображаем через img.
 */
const imageUrl = computed(() => props.image
    ? `data:image/svg+xml;charset=utf-8,${encodeURIComponent(props.image)}`
    : ''
)
</script>

<template>
    <div
        class="rounded-xl border border-gray-300 p-3
               dark:border-gray-700"
    >
        <div
            class="flex flex-col gap-3
                   sm:flex-row sm:flex-wrap sm:items-center
                   lg:flex-nowrap"
        >
            <!-- Название -->
            <label
                for="public-form-captcha"
                class="shrink-0 text-sm font-medium
                       text-gray-700 dark:text-gray-200"
            >
                {{ t('captchaVerificationCode') }}
                <span class="text-red-500">*</span>
            </label>

            <!-- Изображение CAPTCHA -->
            <div
                class="flex h-11 w-full shrink-0 items-center
                       justify-center overflow-hidden rounded-md
                       border border-gray-300 bg-white
                       sm:w-[150px] dark:border-gray-600"
            >
                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    :alt="t('captchaImageAlt')"
                    class="block h-full w-full object-fill"
                />

                <span
                    v-else
                    class="px-2 text-center text-xs text-gray-500"
                >
                    {{ loading ? t('loading') : t('captchaUnavailable') }}
                </span>
            </div>

            <!-- Ввод кода -->
            <input
                id="public-form-captcha"
                :value="modelValue"
                type="text"
                inputmode="text"
                maxlength="5"
                autocomplete="off"
                autocapitalize="characters"
                spellcheck="false"
                :disabled="loading || !imageUrl"
                :placeholder="t('captchaEnterCharacters')"
                :aria-invalid="Boolean(error)"
                :aria-describedby="error ? 'public-form-captcha-error' : undefined"
                class="h-11 w-full min-w-0 rounded-md
                       border border-gray-400 bg-white
                       px-3 text-sm uppercase text-gray-900
                       focus:border-sky-500 focus:outline-none
                       focus:ring-2 focus:ring-sky-500
                       disabled:cursor-not-allowed disabled:opacity-50
                       sm:min-w-[160px] sm:flex-1
                       dark:border-gray-600 dark:bg-gray-900
                       dark:text-white font-semibold"
                @input="emit('update:modelValue', $event.target.value.toUpperCase())"
            />

            <!-- Обновление CAPTCHA -->
            <button
                type="button"
                :disabled="loading"
                class="inline-flex h-11 w-full shrink-0
                       items-center justify-center rounded-md
                       border border-gray-300 px-4 text-sm
                       font-medium text-white transition
                       bg-cyan-600 hover:bg-cyan-500 dark:bg-cyan-700 dark:hover:bg-cyan-800
                       disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto
                       dark:border-gray-600 dark:text-gray-200"
                @click="emit('refresh')"
            >
                <svg
                    v-if="loading"
                    class="mr-2 h-4 w-4 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
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
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    />
                </svg>

                <svg v-else
                     class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                     viewBox="0 0 16 16">
                    <path
                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" ></path>
                </svg>

                {{ t('captchaRefreshCode') }}
            </button>
        </div>

        <!-- Ошибка -->
        <p
            v-if="error"
            id="public-form-captcha-error"
            class="mt-2 text-xs font-medium
                   text-red-600 dark:text-red-400"
            role="alert"
        >
            {{ error }}
        </p>
    </div>
</template>
