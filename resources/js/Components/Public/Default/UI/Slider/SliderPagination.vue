<script setup>
defineProps({
    activeIndex: { type: Number, default: 0 },
    count: { type: Number, default: 0 },
    label: { type: String, default: 'Перейти к слайду' },
})

defineEmits(['select'])
</script>

<template>
    <div v-if="count > 1"
         class="flex items-center gap-2"
         role="tablist"
         aria-label="Навигация слайдера">
        <button
            v-for="index in count"
            :key="index"
            type="button"
            :aria-label="`${label} ${index}`"
            :aria-selected="activeIndex === index - 1"
            role="tab"
            class="group flex h-5 items-center"
            @click="$emit('select', index - 1)"
        >
            <span
                class="block h-1 rounded-full transition-all duration-300"
                :class="activeIndex === index - 1
                    ? 'w-10 bg-sky-500'
                    : 'w-5 bg-gray-400/40 group-hover:bg-gray-400/70 ' +
                     'dark:bg-white/40 dark:group-hover:bg-white/70'"
            ></span>
        </button>
    </div>
</template>
