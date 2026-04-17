<script setup>
defineProps({
    boxClass: { type: String, default: 'is-primary' }, // модификатор цвета для .icon-box
    title: { type: String, required: true },
    text:  { type: String, required: true },
})
</script>

<template>
    <div class="is-flex items-start py-4">
        <div :class="['icon-box','is-small', boxClass]">
            <slot name="icon" />
        </div>
        <div class="ml-4">
            <h3 class="title is-6 is-semi is-narrow"><span>{{ title }}</span></h3>
            <p class="paragraph small:max-w-2 pt-2 max-w-3 rem-80">{{ text }}</p>
        </div>
    </div>
</template>
