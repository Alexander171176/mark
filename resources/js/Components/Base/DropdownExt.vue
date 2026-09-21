<script setup>
import {
    computed,
    onMounted,
    onUnmounted,
    ref,
} from 'vue'

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },

    width: {
        type: String,
        default: '48',
    },

    contentClasses: {
        type: Array,
        default: () => [
            'py-0',
            'bg-white',
        ],
    },

    /**
     * Обычные Dropdown закрываются после клика
     * по содержимому.
     *
     * Для интерактивных Dropdown с формами
     * можно отключить это поведение.
     */
    closeOnContentClick: {
        type: Boolean,
        default: true,
    },
})

const open = ref(false)

const close = () => {
    open.value = false
}

const toggle = () => {
    open.value = !open.value
}

const handleContentClick = () => {
    if (props.closeOnContentClick) {
        close()
    }
}

const closeOnEscape = (event) => {
    if (
        open.value
        && event.key === 'Escape'
    ) {
        close()
    }
}

onMounted(() => {
    document.addEventListener(
        'keydown',
        closeOnEscape
    )
})

onUnmounted(() => {
    document.removeEventListener(
        'keydown',
        closeOnEscape
    )
})

const widthClass = computed(() => {
    return {
        48: 'w-48',
        56: 'w-56',
        60: 'w-60',
        64: 'w-64',
        72: 'w-72',
        80: 'w-80',
        96: 'w-96',
    }[props.width.toString()] || 'w-48'
})

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0'
    }

    if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0'
    }

    return 'origin-top'
})

defineExpose({
    open,
    close,
    toggle,
})
</script>

<template>
    <div class="relative">
        <!-- Trigger -->
        <div @click="toggle">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="fixed inset-0 z-10"
            @click="close"
        />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-20 mt-2 rounded-md shadow-lg"
                :class="[
                    widthClass,
                    alignmentClasses,
                ]"
                style="display: none"
                @click="handleContentClick"
            >
                <div
                    class="w-full rounded-md
                           ring-1 ring-black ring-opacity-5
                           dark:bg-slate-900
                           dark:border dark:border-gray-100"
                    :class="contentClasses"
                >
                    <slot
                        name="content"
                        :close="close"
                    />
                </div>
            </div>
        </transition>
    </div>
</template>
