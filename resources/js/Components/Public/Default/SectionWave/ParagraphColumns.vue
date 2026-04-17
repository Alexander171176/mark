<script setup>
const props = defineProps({
    left:  { type: String, required: true },
    right: { type: String, required: true },
})
</script>

<template>
    <div class="columns m-0">
        <div class="column is-6">
            <p class="paragraph mobile:text-centered">{{ left }}</p>
        </div>
        <div class="column is-6">
            <p class="paragraph mobile:text-centered">{{ right }}</p>
        </div>
    </div>
</template>
