<script setup>
defineProps({
    disabledPrev: { type: Boolean, default: false },
    disabledNext: { type: Boolean, default: false },
    labelPrev: { type: String, default: 'Предыдущий слайд' },
    labelNext: { type: String, default: 'Следующий слайд' },
})

defineEmits(['prev', 'next'])
</script>

<template>
    <div class="flex items-center gap-3">
        <button
            type="button"
            :aria-label="labelPrev"
            :disabled="disabledPrev"
            class="group flex h-11 w-11 items-center justify-center rounded-full
                   border border-white/40 bg-slate-700/50 text-white backdrop-blur-sm
                   transition hover:border-white/70 hover:bg-sky-600/70
                   disabled:pointer-events-none disabled:opacity-30"
            @click="$emit('prev')"
        >
            <svg
                class="h-5 w-5 transition-transform group-hover:-translate-x-0.5"
                viewBox="0 0 24 24"
                fill="none"
                aria-hidden="true">
                <path
                    d="M15 18l-6-6 6-6"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

        <button
            type="button"
            :aria-label="labelNext"
            :disabled="disabledNext"
            class="group flex h-11 w-11 items-center justify-center rounded-full
                   border border-white/40 bg-slate-700/50 text-white backdrop-blur-sm
                   transition hover:border-white/70 hover:bg-sky-600/70
                   disabled:pointer-events-none disabled:opacity-30"
            @click="$emit('next')"
        >
            <svg
                class="h-5 w-5 transition-transform group-hover:translate-x-0.5"
                viewBox="0 0 24 24"
                fill="none"
                aria-hidden="true">
                <path
                    d="m9 18 6-6-6-6"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</template>
