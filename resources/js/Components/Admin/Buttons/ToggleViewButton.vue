<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
    /**
     * Режим отображения:
     *  - 'table' — таблица
     *  - 'cards' — карточки (grid)
     */
    viewMode: {
        type: String,
        default: 'table'
    }
})

const emits = defineEmits(['update:viewMode'])

const setViewMode = (mode) => {
    if (mode !== props.viewMode) {
        emits('update:viewMode', mode)
    }
}
</script>

<template>
    <!-- Переключатель вида -->
    <div class="flex justify-end items-center space-x-2">
        <!-- Карточки (grid) -->
        <button
            type="button"
            @click="setViewMode('cards')"
            :class="[
                'p-1 border transition-colors duration-200 rounded',
                viewMode === 'cards'
                    ? 'border-slate-400 dark:border-slate-200 text-red-400 dark:text-red-200'
                    : 'border-slate-300 dark:border-slate-400 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 hover:border-slate-500 dark:hover:border-slate-500'
            ]"
        >
            <!-- иконка grid -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </button>

        <!-- Таблица (list / horizontal) -->
        <button
            type="button"
            @click="setViewMode('table')"
            :class="[
                'p-1 border transition-colors duration-200 rounded',
                viewMode === 'table'
                    ? 'border-slate-400 dark:border-slate-200 text-red-400 dark:text-red-200'
                    : 'border-slate-300 dark:border-slate-400 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 hover:border-slate-500 dark:hover:border-slate-500'
            ]"
        >
            <!-- иконка горизонтального списка -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</template>
