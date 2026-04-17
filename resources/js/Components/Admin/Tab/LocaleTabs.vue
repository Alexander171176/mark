<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: { type: String, default: 'ru' },
    locales: { type: Array, default: () => ['ru', 'en', 'kk'] },
    /** Необязательная карта отображаемых подписей: { ru: 'RU', en: 'EN', ... } */
    displayMap: { type: Object, default: () => ({}) },
    disabled:   { type: Boolean, default: false },
    /** Когда идёт навигация/загрузка — табы блочатся и показывается спиннер на активном */
    busy:       { type: Boolean, default: false },
    size:       { type: String,  default: 'sm' },   // 'sm' | 'md'
    rounded:    { type: String,  default: 'lg' },   // 'sm' | 'md' | 'lg' | 'full'
    class:      { type: String,  default: '' },     // доп. классы-обёртки
})

const emit = defineEmits(['update:modelValue','change'])

const labelOf = (code) => props.displayMap[code] ?? code.toUpperCase()
const isActive = (code) => code === props.modelValue
const onClick  = (code) => {
    if (props.disabled || props.busy || code === props.modelValue) return
    emit('update:modelValue', code)
    emit('change', code)
}

const sizeCls = computed(() => props.size === 'md' ? 'text-sm px-3 py-1.5' : 'text-xs px-2 py-1')
const radius  = { sm:'rounded', md:'rounded-md', lg:'rounded-lg', full:'rounded-full' }
const roundedCls = computed(() => radius[props.rounded] ?? 'rounded-lg')
</script>

<template>
    <div
        class="inline-flex items-center gap-1 bg-slate-200/60
               dark:bg-slate-800/60 p-0.5 rounded-xl shadow-inner"
        role="tablist"
        :class="props.class"
    >
        <button
            v-for="code in locales"
            :key="code"
            type="button"
            role="tab"
            :aria-selected="isActive(code)"
            :disabled="disabled || busy"
            @click="onClick(code)"
            class="select-none transition outline-none
                   focus-visible:ring-2 focus-visible:ring-indigo-400/70"
            :class="[
        sizeCls, roundedCls,
        isActive(code)
          ? 'bg-white text-slate-900 dark:bg-slate-900 dark:text-white shadow'
          : 'text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-900/40'
      ]"
        >
      <span
          v-if="busy && isActive(code)"
          class="inline-block mr-1 h-3 w-3 align-[-2px]
                 animate-spin rounded-full border-2 border-current border-t-transparent"
      />
            {{ labelOf(code) }}
        </button>
    </div>
</template>
