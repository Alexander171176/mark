<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    topTitle: { type: String, required: true },
    title: { type: String, required: true },
    // 'bottom' | 'top' | 'left' | 'right' — по умолчанию снизу
    revealFrom: { type: String, default: 'bottom' },
    delay: { type: Number, default: 0 },
    threshold: { type: Number, default: 0.15 },
    distance: { type: Number, default: 28 }, // px, насколько сдвигать изначально
})

const featureEl = ref(null)
const isInView = ref(false)
let io

onMounted(() => {
    if (!('IntersectionObserver' in window)) { isInView.value = true; return }
    io = new IntersectionObserver(([entry]) => {
        // работает и вперёд, и назад
        isInView.value = entry.isIntersecting
    }, { threshold: props.threshold })
    if (featureEl.value) io.observe(featureEl.value)
})

onBeforeUnmount(() => { if (io && featureEl.value) io.unobserve(featureEl.value) })

const startTransform = computed(() => {
    const d = props.distance
    switch (props.revealFrom) {
        case 'top': return `translateY(-${d}px)`
        case 'left': return `translateX(-${d}px)`
        case 'right': return `translateX(${d}px)`
        default: return `translateY(${d}px)` // bottom
    }
})
</script>

<template>
    <div class="demo-feature-wrapper">
        <div>
            <div
                ref="featureEl"
                class="demo-feature"
                :style="{
                opacity: isInView ? 1 : 0,
                transform: isInView ? 'translate3d(0,0,0)' : startTransform,
                transition: 'opacity 0.6s ease, transform 0.6s ease',
                transitionDelay: `${delay}ms`,
                willChange: 'opacity, transform',
              }"
            >

            <div class="svg-icon">
                    <slot name="icon" />
                </div>
                <div class="feature-meta">
                    <h4 class="top-title">{{ topTitle }}</h4>
                    <h3 class="title is-4 is-bold is-leading dark:text-white">
                        <span>{{ title }}</span>
                    </h3>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* даёт нам приоритет поверх .demo-features .demo-feature */
.demo-feature.reveal-guard { opacity: 0; }
@media (prefers-reduced-motion: reduce) {
    .demo-feature { transition: none !important; opacity: 1 !important; transform: none !important; }
}
</style>

