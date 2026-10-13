<script setup>
/** Страница редактирования универсального слайдера. */
import SliderForm from './SliderForm.vue'

// Данные передаются из SliderController через Inertia.
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
        :is-admin="isAdmin"
    />
</template>
