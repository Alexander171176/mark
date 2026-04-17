<script setup>
import TechBadge from './TechBadge.vue'

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <div
        class="mx-auto max-w-6 is-flex is-justify-content-center pt-8 tech-badges-row"
        data-section="tech-badges-row"
    >
        <TechBadge
            v-for="(it, i) in items"
            :key="i"
            :img="it.img"
            :alt="it.alt"
            :title="it.title"
            :subtitle="it.subtitle"
            :description="it.description"
        />
    </div>
</template>

<style scoped>
.tech-badges-row {
    /* чтобы и в 1 ряд, и перенос с отступами */
    flex-wrap: wrap;
    /* горизонтальный и вертикальный зазоры между значками */
    column-gap: 32px;
    row-gap: 32px;
}
</style>
