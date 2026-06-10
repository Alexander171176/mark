<script setup>
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

import { useI18n } from 'vue-i18n'
const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
})

const placeholderText = computed(() => {
    return props.placeholder || `${t('search')} ...`
})

const searchQuery = ref(props.modelValue || '')

watch(
    () => props.modelValue,
    (value) => {
        searchQuery.value = value || ''
    }
)

let timeout = null

const runSearch = () => {
    clearTimeout(timeout)

    timeout = setTimeout(() => {
        router.get(
            window.location.pathname,
            {
                ...Object.fromEntries(new URLSearchParams(window.location.search)),
                search: searchQuery.value || undefined,
                page: undefined,
            },
            {
                preserveScroll: true,
                preserveState: false,
                replace: true,
            }
        )
    }, 200)
}

const clearSearch = () => {
    searchQuery.value = ''

    router.get(
        window.location.pathname,
        {
            ...Object.fromEntries(new URLSearchParams(window.location.search)),
            search: undefined,
            page: undefined,
        },
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        }
    )
}
</script>

<template>
    <div class="px-3 py-3 mb-2 border border-gray-400 dark:border-gray-500">
        <div class="relative w-full">
            <input
                v-model="searchQuery"
                type="text"
                :placeholder="placeholderText"
                @input="runSearch"
                class="w-full px-2 py-1 pr-16
                       border border-slate-300
                       rounded-xs
                       bg-white dark:bg-gray-800
                       text-sm font-semibold
                       text-gray-700 dark:text-gray-300"
            />

            <button
                v-if="searchQuery"
                @click="clearSearch"
                type="button"
                class="absolute right-8 top-1/2 -translate-y-1/2
                       rounded-sm px-1.5 py-1 hover:bg-blue-50
                       dark:hover:bg-blue-950
                       border border-blue-700 dark:border-blue-300"
                :title="t('clear')">

                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-blue-700 dark:text-blue-300"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor"
                         viewBox="0 0 512 512">
                        <path d="M370.72 133.28C339.458 104.008 298.888 87.962 255.848 88c-77.458.068-144.328 53.178-162.791 126.85-1.344 5.363-6.122 9.15-11.651 9.15H24.103c-7.498 0-13.194-6.807-11.807-14.176C33.933 94.924 134.813 8 256 8c66.448 0 126.791 26.136 171.315 68.685L463.03 40.97C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.749zM32 296h134.059c21.382 0 32.09 25.851 16.971 40.971l-41.75 41.75c31.262 29.273 71.835 45.319 114.876 45.28 77.418-.07 144.315-53.144 162.787-126.849 1.344-5.363 6.122-9.15 11.651-9.15h57.304c7.498 0 13.194 6.807 11.807 14.176C478.067 417.076 377.187 504 256 504c-66.448 0-126.791-26.136-171.315-68.685L48.97 471.03C33.851 486.149 8 475.441 8 454.059V320c0-13.255 10.745-24 24-24z"></path>
                    </svg>
                </span>

            </button>

            <svg
                class="absolute right-2 top-2 w-4 h-4 text-gray-400 dark:text-gray-500"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path
                    fill-rule="evenodd"
                    d="M12.9 14.32a8 8 0 111.42-1.42l4.58 4.58a1 1 0 01-1.42 1.42l-4.58-4.58zm-4.9 0a6 6 0 100-12 6 6 0 000 12z"
                    clip-rule="evenodd"
                />
            </svg>
        </div>
    </div>
</template>
