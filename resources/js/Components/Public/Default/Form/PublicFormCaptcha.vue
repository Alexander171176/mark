<script setup>
import { computed } from 'vue'

const props = defineProps({
    image: { type: String, default: '' },
    modelValue: { type: String, default: '' },
    loading: { type: Boolean, default: false },
    error: { type: String, default: null },
})

const emit = defineEmits(['update:modelValue', 'refresh'])

// SVG формируется нашим Laravel-сервисом. Никогда не вставляем его через v-html.
// Отображаем как data URL в обычном img.
const imageUrl = computed(() => props.image
    ? `data:image/svg+xml;charset=utf-8,${encodeURIComponent(props.image)}`
    : '')
</script>

<template>
    <div class="space-y-2 rounded-xl border border-gray-300 p-4 dark:border-gray-700">
        <label for="public-form-captcha" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
            Код проверки <span class="text-red-500">*</span>
        </label>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex h-[70px] w-[220px] items-center justify-center overflow-hidden rounded-lg bg-white">
                <img v-if="imageUrl" :src="imageUrl" alt="Проверочный код из пяти символов" width="220" height="70" />
                <span v-else class="text-xs text-gray-500">{{ loading ? 'Загрузка...' : 'Код недоступен' }}</span>
            </div>
            <button type="button" :disabled="loading" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800" @click="emit('refresh')">
                Обновить код
            </button>
        </div>
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
            placeholder="Введите 5 символов"
            :aria-invalid="Boolean(error)"
            class="block w-full max-w-xs rounded-md border border-gray-400 bg-white px-3 py-2 text-sm uppercase text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            @input="emit('update:modelValue', $event.target.value.toUpperCase())"
        />
        <p v-if="error" class="text-xs font-medium text-red-600 dark:text-red-400">{{ error }}</p>
    </div>
</template>
