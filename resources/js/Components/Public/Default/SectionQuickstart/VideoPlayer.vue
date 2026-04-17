<script setup>
const props = defineProps({
    src: { type: String, required: true },
    poster: { type: String, default: '' },
})
</script>

<template>
    <div class="video-player-container-wrapper">
        <div class="video-player-container is-16by9">
            <video class="w-full h-full" :poster="poster" controls playsinline>
                <source :src="src" type="video/mp4" />
            </video>
        </div>
    </div>
</template>

<style scoped></style>
