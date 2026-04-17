<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const CIRCUMFERENCE = 2 * Math.PI * 49
const dashArray = `${CIRCUMFERENCE.toFixed(3)}, ${CIRCUMFERENCE.toFixed(3)}`
const dashOffset = ref(CIRCUMFERENCE)
const isActive = ref(false)
let ticking = false

function updateProgress() {
    const doc = document.documentElement
    const scrollTop = window.pageYOffset || doc.scrollTop || 0
    const max = Math.max(1, doc.scrollHeight - window.innerHeight)
    const progress = Math.min(1, Math.max(0, scrollTop / max))
    dashOffset.value = CIRCUMFERENCE * (1 - progress)
    isActive.value = scrollTop > 120
    ticking = false
}
function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(updateProgress) } }
const onResize = onScroll
function scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }) }
function onKeydown(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollToTop() } }

onMounted(() => {
    updateProgress()
    window.addEventListener('scroll', onScroll, { passive: true })
    window.addEventListener('resize', onResize, { passive: true })
})
onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll)
    window.removeEventListener('resize', onResize)
})
</script>

<template>
    <div
        class="progress-wrap"
        :class="{ 'active-progress': isActive }"
        role="button" tabindex="0" aria-label="Прокрутить вверх"
        @click="scrollToTop" @keydown="onKeydown"
    >
        <svg class="progress-circle" width="100%" height="100%" viewBox="-1 -1 102 102" aria-hidden="true">
            <!-- трек (по желанию можно удалить) -->
            <path class="track" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
            <!-- прогресс по контуру -->
            <path class="bar"
                  d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                  :style="{ strokeDasharray: dashArray, strokeDashoffset: dashOffset }"></path>
        </svg>

        <!-- твоя стрелка (в центре). fill перевёл на currentColor, чтобы можно было темизировать через CSS -->
        <svg class="arrow-icon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
            <path
                d="M22,18a1,1,0,0,1-.707-.293L12,8.414,2.707,17.707a1,1,0,0,1-1.414-1.414l10-10a1,1,0,0,1,1.414,0l10,10A1,1,0,0,1,22,18Z">
            </path>
        </svg>
    </div>
</template>

<style scoped>
/* БАЗА */
.progress-wrap {
    position: fixed;
    right: 10px;
    bottom: 10px;
    height: 40px;
    width: 40px;
    cursor: pointer;
    display: block;
    border-radius: 50%;
    box-shadow: inset 0 0 0 2px rgba(0,0,0,0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(15px);
    transition: all 200ms linear;
    z-index: 9;
    background: transparent;
}

/* Состояние появления */
.active-progress {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* На случай наследованных псевдо-элементов со стрелкой */
.progress-wrap::after { content: none !important; }

/* СЛОИ: круг снизу, стрелка сверху */
.progress-circle { position: absolute; inset: 0; z-index: 1; }
.arrow-icon {
    position: absolute; inset: 0; margin: auto;
    width: 20px; height: 20px;
    color: hsl(268 98% 50%);
    pointer-events: none;
    z-index: 2;
    transform: none;
    transform-origin: 50% 50%;
}
/* если где-то глобально стоит fill:none для path */
.arrow-icon path { fill: currentColor !important; }

/* SVG: общие свойства */
.progress-circle .track,
.progress-circle .bar {
    stroke-width: 3;
    fill: none;
}
/* Трек и прогресс */
.progress-circle .track {
    stroke: rgba(0,0,0,0.12);
}
.progress-circle .bar {
    stroke: hsl(268 98% 50%);
    stroke-linecap: round;
    transition: stroke-dashoffset 60ms linear;
}

/* Hover/Focus */
.progress-wrap:hover,
.progress-wrap:focus-visible {
    box-shadow: inset 0 0 0 2px rgba(0,0,0,0.2);
    outline: none;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .progress-circle .bar { transition: none; }
    .progress-wrap { transition: none; }
}
</style>
