<script setup>
const props = defineProps({
    title: { type: String, required: true },
    text:  { type: String, required: true },
    bordered: { type: Boolean, default: false },      // добавляет .is-bordered
    noBorderEdge: { type: Boolean, default: false },  // добавляет .no-border-edge
})
</script>

<template>
    <div :class="['flex-cell', { 'is-bordered': bordered, 'no-border-edge': noBorderEdge }]">
        <slot name="icon" />
        <h3>{{ title }}</h3>
        <p>{{ text }}</p>
    </div>
</template>
