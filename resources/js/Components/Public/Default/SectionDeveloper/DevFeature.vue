<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: { type: String, required: true },
    text:  { type: String, required: true },
    lightSrc: { type: String, required: true },
    darkSrc:  { type: String, default: '' },     // если не передан — возьмём lightSrc
    alt: { type: String, default: '' },
    columnClass: { type: String, default: 'column is-3' }, // можно переопределить размер колонки
    small: { type: Boolean, default: true },     // включает .is-small у контейнера картинки
})

const dark = computed(() => props.darkSrc || props.lightSrc)
const altText = computed(() => props.alt || props.title)
</script>

<template>
    <div :class="columnClass">
        <div class="py-4">
            <div :class="['feature-block-image', { 'is-small': small }]">
                <img class="is-image is-image-block is-image is-image-light"
                     :src="lightSrc" :alt="altText" loading="lazy" />
                <img class="is-image is-image-block is-image is-image-dark"
                     :src="dark" :alt="altText" loading="lazy" />
            </div>

            <h3 class="title is-6 is-semi is-leading dark:text-white">
                <span>{{ title }}</span>
            </h3>

            <p class="paragraph rem-90 max-w-3">
                <slot>{{ text }}</slot>
            </p>
        </div>
    </div>
</template>

<style scoped></style>
