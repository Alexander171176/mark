<script setup>
/** Страница редактирования слайдера и просмотра его слайдов. */
import SliderForm from './SliderForm.vue'
defineProps({
    slider: { type: Object, required: true },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})
</script>
<template>
    <SliderForm
        :slider="slider"
        :current-locale="currentLocale"
        :available-locales="availableLocales"
        :is-admin="isAdmin" />
</template>
