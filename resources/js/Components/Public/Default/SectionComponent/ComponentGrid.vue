<script setup>
import ComponentTile from './ComponentTile.vue'

defineProps({
    items: {
        type: Array,
        required: true, // [{ href, light, dark, title }]
    },
})
</script>

<template>
    <TransitionGroup name="grid" tag="div" class="columns is-multiline">
        <div
            v-for="(item, idx) in items"
            :key="item.title"
            class="column is-4"
            :style="{'transition-delay': (idx * 35) + 'ms'}"
        >
            <ComponentTile
                :href="item.href"
                :light="item.light"
                :dark="item.dark"
                :title="item.title"
            />
        </div>
    </TransitionGroup>
</template>

<style scoped>
/* Плавное появление каждой колонки (с шагом задержки через inline style) */
.grid-enter-active,
.grid-leave-active {
    transition: opacity .24s ease, transform .24s ease;
}
.grid-enter-from,
.grid-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
/* Анимация перестановок (если понадобится) */
.grid-move {
    transition: transform .24s ease;
}

/* ComponentGrid.vue */
.component-box{
    display:block;
    text-decoration:none;
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, rgba(0,0,0,.08));
    border-radius: 14px;
    padding: 16px;
    cursor: pointer;
    transform: translateY(0) translateZ(0);
    transition:
        transform .44s cubic-bezier(.22,.95,.35,1),
        box-shadow .44s ease,
        border-color .44s ease,
        background-color .44s ease;
    will-change: transform;
}

.component-box .component{
    transition: transform .44s ease;
}

.component-box:hover,
.component-box:focus-visible{
    transform: translateY(-6px);
    border-color: var(--card-accent, #4f46e5); /* цветной бордер при ховере */
    box-shadow: 0 14px 28px rgba(0,0,0,.10);
    outline: none;
}

.component-box:hover .component,
.component-box:focus-visible .component{
    transform: translateY(-2px);
}

/* чуть подчёркиваем заголовок при ховере (по желанию) */
.component-box .component-title{
    transition: color .2s ease, opacity .2s ease;
}
.component-box:hover .component-title,
.component-box:focus-visible .component-title{
    color: var(--card-accent, #4f46e5);
}

/* тёмная тема */
.dark .component-box{
    --card-bg: hsl(240 33% 12%);
    --card-border: hsl(240 33% 18%);
    box-shadow: 0 1px 0 rgba(255,255,255,.02) inset;
}
.dark .component-box:hover,
.dark .component-box:focus-visible{
    box-shadow: 0 16px 32px rgba(0,0,0,.45);
}

/* уважение к reduced-motion */
@media (prefers-reduced-motion: reduce){
    .component-box,
    .component-box .component{
        transition: none;
    }
}
</style>
