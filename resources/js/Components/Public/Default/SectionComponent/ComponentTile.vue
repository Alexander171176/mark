<script setup>
defineProps({
    href: { type: String, required: true },
    light: { type: String, required: true },
    dark: { type: String, required: true },
    title: { type: String, required: true },
})
</script>

<template>
    <a :href="href" class="component-box">
        <div class="component">
            <img class="is-image is-image-light" :src="light" alt="component icon" loading="lazy">
            <img class="is-image is-image-dark"  :src="dark"  alt="component icon" loading="lazy">
        </div>
        <div class="component-title text-center">{{ title }}</div>
    </a>
</template>
