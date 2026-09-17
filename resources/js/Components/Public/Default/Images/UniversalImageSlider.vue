<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount, useAttrs } from 'vue'

defineOptions({
    inheritAttrs: false,
})

const attrs = useAttrs()

const props = defineProps({
    entity: {
        type: Object,
        default: () => ({}),
    },

    alt: {
        type: String,
        default: '',
    },

    heightClass: {
        type: String,
        default: 'h-48',
    },

    roundedClass: {
        type: String,
        default: '',
    },

    wrapperClass: {
        type: String,
        default: '',
    },

    imgClass: {
        type: String,
        default: 'w-full h-full object-cover transition duration-300 group-hover:scale-105',
    },

    showDots: {
        type: Boolean,
        default: true,
    },

    autoplay: {
        type: Boolean,
        default: true,
    },

    interval: {
        type: Number,
        default: 4000,
    },

    width: {
        type: Number,
        default: 800,
    },

    height: {
        type: Number,
        default: 450,
    },
})

/**
 * Название сущности для alt.
 *
 * Новый Public-контракт:
 * entity.translation.title.
 *
 * entity.title оставляем только как универсальную
 * совместимость компонента с сущностями, которые
 * ещё не переведены на новый Public Resource.
 */
const entityTitle = computed(() => {
    return props.alt
        || props.entity?.translation?.title
        || props.entity?.title
        || ''
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
            alt: img?.alt || entityTitle.value,
            src: img?.webp_url || img?.url || img?.image_url || img?.thumb_url || null,
        }))
        .filter(img => !!img.src)
}

const images = computed(() => normalizeImages(props.entity))
const hasImages = computed(() => images.value.length > 0)
const hasManyImages = computed(() => images.value.length > 1)

const currentIndex = ref(0)
const isHovered = ref(false)

const currentImage = computed(() => images.value[currentIndex.value] ?? null)

const imageAttrs = computed(() => {
    const {
        class: _class,
        style: _style,
        ...rest
    } = attrs

    return rest
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
    if (!timer) return

    clearInterval(timer)
    timer = null
}

const startAutoplay = () => {
    stopAutoplay()

    if (!props.autoplay || !hasManyImages.value) return

    timer = setInterval(() => {
        if (!isHovered.value) nextSlide()
    }, Math.max(1500, Number(props.interval) || 4000))
}

watch(images, () => {
    currentIndex.value = 0
    startAutoplay()
})

watch(() => props.interval, startAutoplay)
watch(() => props.autoplay, startAutoplay)

onMounted(startAutoplay)
onBeforeUnmount(stopAutoplay)
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
            v-bind="imageAttrs"
            :src="currentImage?.src"
            :alt="currentImage?.alt || entityTitle"
            :class="imgClass"
            :width="width"
            :height="height"
            loading="lazy"
            decoding="async"
        >

        <!-- Несколько изображений -->
        <template v-else>
            <transition name="fade" mode="out-in">
                <img
                    :key="currentImage?.id || currentImage?.src"
                    v-bind="imageAttrs"
                    :src="currentImage?.src"
                    :alt="currentImage?.alt || entityTitle"
                    :class="imgClass"
                    :width="width"
                    :height="height"
                    loading="lazy"
                    decoding="async"
                >
            </transition>

            <div
                v-if="showDots"
                class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5"
                role="group"
                :aria-label="entityTitle"
            >
                <button
                    v-for="(img, index) in images"
                    :key="img.id"
                    type="button"
                    class="h-2 w-2 rounded-full transition"
                    :class="index === currentIndex
                        ? 'bg-red-600 dark:bg-red-400 shadow border border-rose-600'
                        : 'bg-white/50 hover:bg-white/80 border border-gray-600'"
                    :aria-label="`${index + 1} / ${images.length}: ${img.alt || entityTitle}`"
                    :aria-current="index === currentIndex ? 'true' : undefined"
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
