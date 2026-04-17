<script setup>
const props = defineProps({
    subtitle: { type: String, required: true },
    title: { type: String, required: true },
    center: { type: Boolean, default: true },
})
</script>

<template>
    <div :class="['has-text-centered', center && '']">
        <h3 class="subtitle is-5 is-bold">
            <span class="text-gradient">{{ subtitle }}</span>
        </h3>
        <h2 class="title is-3 is-bold dark:text-white">
            <span>{{ title }}</span>
        </h2>
    </div>
</template>

<style scoped></style>
