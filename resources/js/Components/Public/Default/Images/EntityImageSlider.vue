<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    entity: {
        type: Object,
        required: true,
    },

    /** classes */
    heightClass: {
        type: String,
        default: 'h-48',
    },
    roundedClass: {
        type: String,
        default: 'rounded-md',
    },
    wrapperClass: {
        type: String,
        default: '',
    },
    emptyTextClass: {
        type: String,
        default: 'text-sm font-semibold text-gray-400 dark:text-gray-500',
    },
    imageClass: {
        type: String,
        default: 'h-full w-full object-cover transition duration-300 group-hover:scale-105',
    },

    /** slider behavior */
    intervalMs: {
        type: Number,
        default: 4200,
    },
    pauseOnHover: {
        type: Boolean,
        default: true,
    },
    pauseOnHidden: {
        type: Boolean,
        default: true,
    },
    showDots: {
        type: Boolean,
        default: true,
    },

    /** fallback */
    fallbackText: {
        type: String,
        default: 'Нет изображения',
    },
})

const images = computed(() => {
    const entityImages = Array.isArray(props.entity?.images)
        ? props.entity.images
        : (props.entity?.images?.data ?? [])

    return (Array.isArray(entityImages) ? entityImages : [])
        .slice()
        .sort((a, b) => Number(a?.order ?? 0) - Number(b?.order ?? 0))
        .map((img, idx) => ({
            id: img?.id ?? `${props.entity?.id ?? 'entity'}-${idx}`,
            src:
                img?.url ||
                img?.image_url ||
                img?.src ||
                img?.path ||
                img?.image ||
                null,
            alt: img?.alt || props.entity?.title || props.entity?.name || '',
            title: img?.title || img?.alt || props.entity?.title || props.entity?.name || '',
            order: Number(img?.order ?? 0),
        }))
        .filter(img => !!img.src)
})

const currentIndex = ref(0)
const hovered = ref(false)
let timer = null

const currentImage = computed(() => images.value[currentIndex.value] ?? null)

const setCurrent = (idx) => {
    const total = images.value.length

    if (total <= 1) {
        currentIndex.value = 0
        return
    }

    const safe = Number.isFinite(Number(idx))
        ? Math.min(Math.max(0, Number(idx)), total - 1)
        : 0

    currentIndex.value = safe
}

const canRun = () => {
    return !(props.pauseOnHidden && typeof document !== 'undefined' && document.hidden)
}

const tick = () => {
    if (!canRun()) return
    if (props.pauseOnHover && hovered.value) return
    if (images.value.length <= 1) return

    currentIndex.value = (currentIndex.value + 1) % images.value.length
}

const stop = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

const start = () => {
    stop()

    if (images.value.length <= 1) return

    timer = setInterval(tick, Math.max(1500, Number(props.intervalMs) || 4200))
}

const onVisibilityChange = () => {
    start()
}

onMounted(() => {
    currentIndex.value = 0
    start()

    if (props.pauseOnHidden && typeof document !== 'undefined') {
        document.addEventListener('visibilitychange', onVisibilityChange)
    }
})

onBeforeUnmount(() => {
    stop()

    if (props.pauseOnHidden && typeof document !== 'undefined') {
        document.removeEventListener('visibilitychange', onVisibilityChange)
    }
})

watch(
    () => [images.value.length, props.intervalMs, props.entity?.id],
    () => {
        currentIndex.value = 0
        start()
    }
)
</script>

<template>
    <div
        class="relative w-full overflow-hidden bg-gray-100 dark:bg-gray-800"
        :class="[heightClass, roundedClass, wrapperClass]"
        @mouseenter="hovered = true"
        @mouseleave="hovered = false"
    >
        <template v-if="images.length">
            <Transition name="imgfx" mode="out-in">
                <img
                    v-if="currentImage"
                    :key="currentImage.id"
                    :src="currentImage.src"
                    :alt="currentImage.alt"
                    :title="currentImage.title"
                    loading="lazy"
                    :class="imageClass"
                />
            </Transition>

            <div
                v-if="showDots && images.length > 1"
                class="absolute left-0 right-0 bottom-0 px-2 pb-2"
            >
                <div class="flex items-center justify-center gap-1">
                    <button
                        v-for="(img, idx) in images"
                        :key="img.id"
                        type="button"
                        class="h-1.5 w-1.5 rounded-full transition-all"
                        :class="idx === currentIndex
                            ? 'bg-orange-400 shadow ring-1 ring-black/40'
                            : 'bg-white/60 hover:bg-orange-400/80'"
                        @click.prevent.stop="setCurrent(idx)"
                        :aria-label="`image ${idx + 1}`"
                        :title="img.title"
                    />
                </div>
            </div>
        </template>

        <div
            v-else
            class="flex h-full w-full items-center justify-center text-center"
            :class="emptyTextClass"
        >
            {{ fallbackText }}
        </div>
    </div>
</template>

<style scoped>
.imgfx-enter-active {
    transition: opacity 520ms ease, filter 520ms ease;
    will-change: opacity, filter;
}

.imgfx-leave-active {
    transition: opacity 280ms ease, filter 280ms ease;
    will-change: opacity, filter;
}

.imgfx-enter-from {
    opacity: 0;
    filter: blur(6px);
}

.imgfx-enter-to {
    opacity: 1;
    filter: blur(0);
}

.imgfx-leave-from {
    opacity: 1;
    filter: blur(0);
}

.imgfx-leave-to {
    opacity: 0;
    filter: blur(5px);
}
</style>
