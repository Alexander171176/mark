<script setup>
const props = defineProps({
    modelValue: { type: String, default: '' },
    id:         { type: String, default: undefined },
    placeholder:{ type: String, default: '' },
    rows:       { type: Number, default: 12 },
    /** 'y' | 'x' | 'both' | 'none' */
    resize:     { type: String, default: 'y' },
    /** 'off' | 'soft' | 'hard' — HTML-атрибут wrap */
    wrap:       { type: String, default: 'off' },
})

const emit = defineEmits(['update:modelValue'])

const resizeClassMap = {
    y: 'resize-y',
    x: 'resize-x',
    both: 'resize',
    none: 'resize-none',
}
</script>

<template>
  <textarea
      :id="id"
      :rows="rows"
      :wrap="wrap"
      :placeholder="placeholder"
      :value="modelValue"
      @input="emit('update:modelValue', $event.target.value)"

      spellcheck="false" autocapitalize="off" autocomplete="off"

      class="block w-full p-2 rounded border border-slate-500 dark:text-slate-100
             bg-white/90 dark:bg-cyan-800/60 font-semibold text-xs font-mono leading-5
             whitespace-pre overflow-auto focus:border-indigo-500 focus:ring-indigo-300"
      :class="resizeClassMap[resize]"
  />
</template>
