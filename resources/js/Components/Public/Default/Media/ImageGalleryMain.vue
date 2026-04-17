<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
    alt: {
        type: String,
        default: '',
    },
    fallbackSrc: {
        type: String,
        default: '/article_images/default-image.png',
    },
    roundedClass: {
        type: String,
        default: 'rounded-sm',
    },
    shadowClass: {
        type: String,
        default: 'shadow-md shadow-gray-600 dark:shadow-gray-900',
    },
    imgClass: {
        type: String,
        default: 'w-full h-full object-cover',
    },
    showDots: {
        type: Boolean,
        default: true,
    },
    showArrows: {
        type: Boolean,
        default: true,
    },
})

const currentIndex = ref(0)

const normalizedImages = computed(() => {
    const raw = Array.isArray(props.images) ? props.images : []

    return [...raw]
        .sort((a, b) => {
            const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
            const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
            return aOrder - bOrder
        })
        .map((img, index) => ({
            id: img?.id ?? `img-${index}`,
            alt: img?.alt ?? props.alt ?? 'Image',
            src: img?.webp_url || img?.url || img?.image_url || img?.thumb_url || props.fallbackSrc,
        }))
        .filter(img => !!img.src)
})

const totalImages = computed(() => normalizedImages.value.length)

watch(
    () => normalizedImages.value,
    () => {
        currentIndex.value = 0
    },
    { deep: true }
)

const safeIndex = computed(() => {
    if (!totalImages.value) return 0
    return Math.min(currentIndex.value, totalImages.value - 1)
})

const currentImage = computed(() => {
    if (!totalImages.value) return null
    return normalizedImages.value[safeIndex.value] ?? normalizedImages.value[0] ?? null
})

const currentImageSrc = computed(() => {
    return currentImage.value?.src || props.fallbackSrc
})

const currentImageAlt = computed(() => {
    return currentImage.value?.alt || props.alt || 'Image'
})

const prev = () => {
    if (totalImages.value <= 1) return
    currentIndex.value = (safeIndex.value - 1 + totalImages.value) % totalImages.value
}

const next = () => {
    if (totalImages.value <= 1) return
    currentIndex.value = (safeIndex.value + 1) % totalImages.value
}

const setIndex = (index) => {
    if (!totalImages.value) return

    const i = Number(index)
    if (!Number.isFinite(i)) return

    currentIndex.value = Math.max(0, Math.min(i, totalImages.value - 1))
}
</script>

<template>
    <div class="relative w-full h-full overflow-hidden">
        <transition name="gallery-fade" mode="out-in">
            <img
                :key="currentImage?.id ?? `fallback-${safeIndex}`"
                :src="currentImageSrc"
                :alt="currentImageAlt"
                :class="[imgClass, roundedClass, shadowClass]"
                loading="lazy"
            />
        </transition>

        <!-- Назад -->
        <button
            v-if="showArrows && totalImages > 1"
            type="button"
            @click="prev"
            class="absolute top-1/2 left-0 -translate-y-1/2
                   bg-gray-700/75 text-white px-3 py-1 rounded-r
                   focus:outline-none transition hover:bg-gray-800/85"
            aria-label="Previous"
        >
            &#10094;
        </button>

        <!-- Вперёд -->
        <button
            v-if="showArrows && totalImages > 1"
            type="button"
            @click="next"
            class="absolute top-1/2 right-0 -translate-y-1/2
                   bg-gray-700/75 text-white px-3 py-1 rounded-l
                   focus:outline-none transition hover:bg-gray-800/85"
            aria-label="Next"
        >
            &#10095;
        </button>

        <!-- Точки -->
        <div
            v-if="showDots && totalImages > 1"
            class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-2"
        >
            <button
                v-for="(_, index) in totalImages"
                :key="index"
                type="button"
                @click="setIndex(index)"
                class="w-2.5 h-2.5 rounded-full border border-gray-400 transition"
                :class="safeIndex === index ? 'bg-red-500' : 'bg-white/80 hover:bg-white'"
                :aria-label="`Go to slide ${index + 1}`"
            />
        </div>
    </div>
</template>

<style scoped>
.gallery-fade-enter-active,
.gallery-fade-leave-active {
    transition: opacity 0.55s ease, transform 0.55s ease;
}

.gallery-fade-enter-from,
.gallery-fade-leave-to {
    opacity: 0;
    transform: scale(1.015);
}
</style>
