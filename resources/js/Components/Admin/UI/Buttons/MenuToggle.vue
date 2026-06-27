<script setup>
import { computed } from 'vue'

defineProps({
    isEnabled: Boolean,
    title: String,
})

const emit = defineEmits(['toggle-menu'])

const buttonClass = computed(() => [
    'flex items-center',
])
</script>

<template>
    <button
        :class="buttonClass"
        :title="title"
        type="button"
        @click="emit('toggle-menu')"
    >
        <svg
            v-if="isEnabled"
            class="w-5 h-5 fill-current text-emerald-400
                   hover:text-gray-500 dark:hover:text-white"
            viewBox="0 0 448 512"
        >
            <path d="M16 132h416c8.8 0 16-7.2 16-16V76c0-8.8-7.2-16-16-16H16C7.2 60 0 67.2 0 76v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16z" />
        </svg>

        <svg
            v-else
            class="w-5 h-5 fill-current text-gray-300
                   hover:text-emerald-400 dark:hover:text-emerald-300"
            viewBox="0 0 448 512"
        >
            <path d="M16 132h416c8.8 0 16-7.2 16-16V76c0-8.8-7.2-16-16-16H16C7.2 60 0 67.2 0 76v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16z" />
        </svg>
    </button>
</template>
