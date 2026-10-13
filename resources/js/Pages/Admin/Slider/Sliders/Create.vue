<script setup>
/** Страница создания слайдера. */
import SliderForm from './SliderForm.vue'
defineProps({
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})
</script>
<template>
    <SliderForm
        :current-locale="currentLocale"
        :available-locales="availableLocales"
        :is-admin="isAdmin" />
</template>
