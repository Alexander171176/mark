<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({
    placement: { type: String, default: 'bottom' },
    offset: { type: Array, default: () => [0, 10] },
    strategy: { type: String, default: 'fixed' },
    initialOpen: { type: Boolean, default: false },
    closeOnEsc: { type: Boolean, default: true },
    closeOnClickOutside: { type: Boolean, default: true },
})

const isOpen = ref(false)
const btnRef = ref(null)
const popperRef = ref(null)

let popperInstance = null
let offDoc = null
let offEsc = null
let createPopperFn = null

async function initPopper() {
    if (!btnRef.value || !popperRef.value || popperInstance) return
    if (!createPopperFn) {
        const mod = await import('@popperjs/core')
        createPopperFn = mod.createPopper
    }
    popperInstance = createPopperFn(btnRef.value, popperRef.value, {
        placement: props.placement,
        strategy: props.strategy,
        modifiers: [
            { name: 'offset', options: { offset: props.offset } },
            { name: 'flip', options: { fallbackPlacements: ['top','right','left'] } },
            { name: 'preventOverflow', options: { padding: 8 } },
            { name: 'arrow', options: { element: '[data-popper-arrow]' } },
            { name: 'computeStyles', options: { gpuAcceleration: true } },
        ],
    })
}

function open() {
    isOpen.value = true
    popperInstance?.update?.()
    bindOutside()
}
function close() {
    isOpen.value = false
    unbindOutside()
}
function toggle() { isOpen.value ? close() : open() }

function onDocClick(e) {
    if (!props.closeOnClickOutside) return
    const t = e.target
    if (btnRef.value?.contains(t) || popperRef.value?.contains(t)) return
    close()
}
function onKey(e) { if (props.closeOnEsc && e.key === 'Escape') close() }

function bindOutside() {
    if (!offDoc) { document.addEventListener('mousedown', onDocClick); offDoc = () => document.removeEventListener('mousedown', onDocClick) }
    if (!offEsc) { document.addEventListener('keydown', onKey); offEsc = () => document.removeEventListener('keydown', onKey) }
}
function unbindOutside() { offDoc?.(); offDoc=null; offEsc?.(); offEsc=null }

onMounted(async () => {
    await nextTick()
    // поповер уже в DOM, но скрыт -> создаём Popper ЗАРАНЕЕ
    await initPopper()
    isOpen.value = props.initialOpen
    popperInstance?.update?.()
})
onBeforeUnmount(() => {
    unbindOutside()
    popperInstance?.destroy?.()
    popperInstance = null
})

defineExpose({ open, close, toggle, isOpen, btnRef, popperRef })
</script>

<template>
    <div class="relative inline-block">
        <!-- Триггер -->
        <span ref="btnRef" @click="toggle">
      <slot name="trigger" />
    </span>

        <!-- Поповер в DOM всегда; показываем через data-атрибут -->
        <div
            ref="popperRef"
            class="popover"
            :data-open="isOpen ? 'true' : 'false'"
            role="dialog"
            aria-hidden="!isOpen"
        >
            <slot />
            <div data-popper-arrow></div>
        </div>
    </div>
</template>

<style scoped>
.popover{
    /* Popper навесит transform; мы исключаем display:none, чтобы не было дёрганья */
    position: fixed;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .12s ease, visibility .12s ease;
    z-index: 1000;

    background: var(--popover-bg, #fff);
    color: var(--popover-fg, inherit);
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,.1);
    min-width: 260px; max-width: 320px; padding: 12px;
}
.popover[data-open="true"]{
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* стрелка */
[data-popper-arrow]{ position:absolute; width:10px; height:10px; }
[data-popper-arrow]::before{
    content:""; position:absolute; width:10px; height:10px;
    background: var(--popover-bg, #fff);
    transform: rotate(45deg);
    box-shadow: -1px -1px 2px rgba(0,0,0,.05);
}
.popover[data-popper-placement^="bottom"]>[data-popper-arrow]{ top:-5px; }
.popover[data-popper-placement^="top"]>[data-popper-arrow]{ bottom:-5px; }
.popover[data-popper-placement^="left"]>[data-popper-arrow]{ right:-5px; }
.popover[data-popper-placement^="right"]>[data-popper-arrow]{ left:-5px; }

/* центрируем ромб в соответствующей оси */
.popover[data-popper-placement^="bottom"]>[data-popper-arrow]::before,
.popover[data-popper-placement^="top"]>[data-popper-arrow]::before { left:0; right:0; margin:auto; }
.popover[data-popper-placement^="left"]>[data-popper-arrow]::before,
.popover[data-popper-placement^="right"]>[data-popper-arrow]::before { top:0; bottom:0; margin:auto; }

.dark .popover{
    --popover-bg: hsl(240 33% 12%);
    --popover-fg: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,.4);
}
</style>
