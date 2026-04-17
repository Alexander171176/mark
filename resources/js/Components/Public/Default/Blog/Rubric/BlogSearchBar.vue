<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const props = defineProps({
    modelValue: { type: String, default: '' },
})
const emit = defineEmits(['update:modelValue', 'submit', 'reset'])
</script>

<template>
    <div class="mx-auto max-w-2xl pb-2">
        <form @submit.prevent="emit('submit')" class="blog-search">
            <div class="control has-icon is-invalid flex gap-5">
                <input
                    type="text"
                    :value="modelValue"
                    @input="emit('update:modelValue', $event.target.value)"
                    :placeholder="t('searchByName')"
                    aria-label="input"
                    class="input dark:bg-[hsl(240_33%_12%)] dark:text-gray-300"
                />

                <div class="form-icon">
                    <!-- icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         aria-hidden="true"
                         role="img"
                         width="1em"
                         height="1em"
                         viewBox="0 0 24 24"
                         class="iconify iconify--feather">
                        <g fill="none"
                           stroke="currentColor"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21l-4.35-4.35"></path>
                        </g>
                    </svg>
                </div>

                <button
                    v-if="modelValue"
                    type="button"
                    @click="emit('reset')"
                    class="control -ml-12 dark:text-gray-300"
                    :title="t('delete')"
                >
                    ✕
                </button>

                <button
                    type="submit"
                    class="px-4 py-1.5 rounded border border-slate-300
                           hover:opacity-75 dark:bg-[hsl(240_33%_12%)] dark:text-gray-300"
                    :title="t('search')"
                >
                    <!-- icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         aria-hidden="true"
                         role="img"
                         width="1em"
                         height="1em"
                         viewBox="0 0 24 24"
                         class="iconify iconify--feather">
                        <g fill="none"
                           stroke="currentColor"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21l-4.35-4.35"></path>
                        </g>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</template>
