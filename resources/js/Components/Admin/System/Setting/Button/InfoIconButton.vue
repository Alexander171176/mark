<template>
    <svg @click="$emit('click')"
        class="w-6 h-6 shrink-0 fill-current text-blue-500 mx-1"
        viewBox="0 0 16 16">
        <path
            d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path>
    </svg>
</template>

<script setup>
defineEmits(['click']);
</script>
