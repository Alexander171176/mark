<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    entity: {
        type: Object,
        default: () => ({})
    },

    heightClass: {
        type: String,
        default: 'h-48'
    },

    roundedClass: {
        type: String,
        default: ''
    },

    wrapperClass: {
        type: String,
        default: ''
    },

    imgClass: {
        type: String,
        default: 'w-full h-full object-cover transition duration-300 group-hover:scale-105'
    },

    showDots: {
        type: Boolean,
        default: true
    },

    autoplay: {
        type: Boolean,
        default: true
    },

    interval: {
        type: Number,
        default: 4000
    }
})

const normalizeImages = (entity) => {
    const raw = Array.isArray(entity?.images)
        ? entity.images
        : (Array.isArray(entity?.images?.data) ? entity.images.data : [])

    return [...raw]
        .sort((a, b) => {
            const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
            const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
            return aOrder - bOrder
        })
        .map((img, index) => ({
            id: img?.id ?? `img-${index}`,
            alt: img?.alt ?? entity?.title ?? '',
            src: img?.webp_url || img?.url || img?.image_url || img?.thumb_url || null,
        }))
        .filter(img => !!img.src)
}

const images = computed(() => normalizeImages(props.entity))
const hasImages = computed(() => images.value.length > 0)
const hasManyImages = computed(() => images.value.length > 1)

const currentIndex = ref(0)
const isHovered = ref(false)

const currentImage = computed(() => {
    return images.value[currentIndex.value] ?? null
})

const setSlide = (index) => {
    if (!hasManyImages.value) return
    currentIndex.value = index
}

const nextSlide = () => {
    if (!hasManyImages.value) return
    currentIndex.value = (currentIndex.value + 1) % images.value.length
}

let timer = null

const stopAutoplay = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

const startAutoplay = () => {
    stopAutoplay()

    if (!props.autoplay || !hasManyImages.value) return

    timer = setInterval(() => {
        if (!isHovered.value) {
            nextSlide()
        }
    }, Math.max(1500, Number(props.interval) || 4000))
}

watch(images, () => {
    currentIndex.value = 0
    startAutoplay()
})

watch(
    () => props.interval,
    () => {
        startAutoplay()
    }
)

watch(
    () => props.autoplay,
    () => {
        startAutoplay()
    }
)

onMounted(() => {
    startAutoplay()
})

onBeforeUnmount(() => {
    stopAutoplay()
})
</script>

<template>
    <div
        v-if="hasImages"
        class="relative overflow-hidden group"
        :class="[heightClass, roundedClass, wrapperClass]"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Одно изображение -->
        <img
            v-if="!hasManyImages"
            :src="currentImage?.src"
            :alt="currentImage?.alt"
            :class="imgClass"
            loading="lazy"
        />

        <!-- Несколько изображений -->
        <template v-else>
            <transition name="fade" mode="out-in">
                <img
                    :key="currentImage?.id || currentImage?.src"
                    :src="currentImage?.src"
                    :alt="currentImage?.alt"
                    :class="imgClass"
                    loading="lazy"
                />
            </transition>

            <div
                v-if="showDots"
                class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5"
            >
                <button
                    v-for="(img, index) in images"
                    :key="img.id"
                    type="button"
                    class="h-2 w-2 rounded-full transition"
                    :class="index === currentIndex
                        ? 'bg-red-600 dark:bg-red-400 shadow border border-rose-600'
                        : 'bg-white/50 hover:bg-white/80 border border-gray-600'"
                    @click.stop.prevent="setSlide(index)"
                />
            </div>
        </template>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
