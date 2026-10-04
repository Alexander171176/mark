<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    title: {
        type: String,
        default: '',
    },

    hasActiveFilters: {
        type: Boolean,
        default: false,
    },

    applyText: {
        type: String,
        default: '',
    },

    resetText: {
        type: String,
        default: '',
    },

    columns: {
        type: String,
        default: 'xl:grid-cols-5',
    },
})

const emits = defineEmits([
    'apply',
    'reset',
])

const resolvedTitle = computed(() => {
    return props.title || t('filters')
})

const resolvedApplyText = computed(() => {
    return props.applyText || t('applyFilters')
})

const resolvedResetText = computed(() => {
    return props.resetText || t('clearFilters')
})
</script>

<template>
    <div
        class="my-3 rounded-lg border border-gray-400 bg-gray-50 p-4
               dark:border-gray-500 dark:bg-gray-800"
    >
        <!-- Заголовок -->
        <div
            class="mb-3 flex flex-col gap-2
                   md:flex-row md:items-center md:justify-center"
        >
            <div class="font-semibold text-lg text-gray-700 dark:text-gray-200">
                {{ resolvedTitle }}
            </div>
        </div>

        <!-- Поля фильтра -->
        <div
            class="grid grid-cols-1 gap-3 md:grid-cols-2"
            :class="columns"
        >
            <slot />
        </div>

        <!-- Дополнительная область -->
        <slot name="extra" />

        <!-- Управление -->
        <div class="mt-3 flex items-center justify-center gap-2">
            <slot name="actions" />

            <!-- Сброс фильтров -->
            <button
                type="button"
                :disabled="!hasActiveFilters"
                :aria-disabled="!hasActiveFilters"
                class="flex flex-row items-center justify-center gap-2
                       rounded-sm bg-fuchsia-600 px-3 py-1
                       transition-all duration-200 ease-out
                       hover:bg-fuchsia-700 hover:shadow-md
                       active:scale-95
                       disabled:cursor-not-allowed
                       disabled:bg-gray-400 disabled:opacity-50
                       disabled:hover:shadow-none
                       disabled:active:scale-100
                       dark:disabled:bg-gray-600"
                @click="emits('reset')"
            >
                <svg
                    class="w-3 h-3 shrink-0 fill-current text-white"
                    viewBox="0 0 512 512"
                >
                    <path
                        d="M256.455 8c66.269.119 126.437 26.233 170.859 68.685l35.715-35.715C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.75c-30.864-28.899-70.801-44.907-113.23-45.273-92.398-.798-170.283 73.977-169.484 169.442C88.764 348.009 162.184 424 256 424c41.127 0 79.997-14.678 110.629-41.556 4.743-4.161 11.906-3.908 16.368.553l39.662 39.662c4.872 4.872 4.631 12.815-.482 17.433C378.202 479.813 319.926 504 256 504 119.034 504 8.001 392.967 8 256.002 7.999 119.193 119.646 7.755 256.455 8z"
                    />
                </svg>

                <span class="text-sm font-medium text-white">
                    {{ resolvedResetText }}
                </span>
            </button>

            <!-- Применить фильтры -->
            <button
                type="button"
                class="flex flex-row items-center justify-center gap-2
                       rounded-sm bg-indigo-600 px-3 py-1 hover:bg-indigo-700"
                @click="emits('apply')"
            >
                <svg
                    class="w-3 h-3 shrink-0 fill-current text-white"
                    viewBox="0 0 512 512">
                    <path
                        d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z" />
                </svg>

                <span class="text-sm font-medium text-white transition">
                    {{ resolvedApplyText }}
                </span>
            </button>

        </div>
    </div>
</template>
