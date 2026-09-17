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

    /**
     * Schema.org itemprop.
     *
     * Компонент универсальный, поэтому по умолчанию
     * никакую Schema.org-семантику не навязываем.
     */
    itemprop: {
        type: String,
        default: '',
    },

    /**
     * Основное изображение страницы можно загружать eagerly,
     * остальные сценарии оставляем lazy по умолчанию.
     */
    loading: {
        type: String,
        default: 'lazy',
    },

    fetchpriority: {
        type: String,
        default: 'auto',
    },

    decoding: {
        type: String,
        default: 'async',
    },

    /**
     * Доступность элементов управления.
     *
     * Пока не создаём новые i18n-ключи внутри
     * универсального компонента.
     */
    previousLabel: {
        type: String,
        default: 'Previous image',
    },

    nextLabel: {
        type: String,
        default: 'Next image',
    },

    slideLabel: {
        type: String,
        default: 'Image',
    },
})

const currentIndex = ref(0)

/**
 * Нормализованный список изображений.
 *
 * Public Image Resource уже отдаёт готовые URL.
 * Дополнительно сохраняем поддержку старых форматов.
 */
const normalizedImages = computed(() => {
    const raw = Array.isArray(props.images)
        ? props.images
        : []

    return [...raw]
        .sort((a, b) => {
            const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
            const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)

            return aOrder - bOrder
        })
        .map((img, index) => {
            const src =
                img?.webp_url
                || img?.image_url
                || img?.url
                || img?.thumb_url
                || null

            return {
                id: img?.id ?? `img-${index}`,
                alt: img?.alt || props.alt || '',
                src,
            }
        })
        .filter((img) => !!img.src)
})

const totalImages = computed(() =>
    normalizedImages.value.length
)

/**
 * При изменении набора изображений
 * возвращаем галерею к первому изображению.
 */
watch(
    () => normalizedImages.value,
    () => {
        currentIndex.value = 0
    },
    { deep: true }
)

const safeIndex = computed(() => {
    if (!totalImages.value) {
        return 0
    }

    return Math.min(
        currentIndex.value,
        totalImages.value - 1
    )
})

const currentImage = computed(() => {
    if (!totalImages.value) {
        return null
    }

    return normalizedImages.value[safeIndex.value]
        ?? normalizedImages.value[0]
        ?? null
})

const currentImageSrc = computed(() =>
    currentImage.value?.src || props.fallbackSrc
)

const currentImageAlt = computed(() =>
    currentImage.value?.alt
    || props.alt
    || ''
)

const prev = () => {
    if (totalImages.value <= 1) {
        return
    }

    currentIndex.value =
        (
            safeIndex.value
            - 1
            + totalImages.value
        ) % totalImages.value
}

const next = () => {
    if (totalImages.value <= 1) {
        return
    }

    currentIndex.value =
        (
            safeIndex.value
            + 1
        ) % totalImages.value
}

const setIndex = (index) => {
    if (!totalImages.value) {
        return
    }

    const normalizedIndex = Number(index)

    if (!Number.isFinite(normalizedIndex)) {
        return
    }

    currentIndex.value = Math.max(
        0,
        Math.min(
            normalizedIndex,
            totalImages.value - 1
        )
    )
}

const slideAriaLabel = (index) =>
    `${props.slideLabel} ${index + 1}`
</script>

<template>
    <div
        class="relative w-full h-full overflow-hidden"
        role="region"
        :aria-label="alt || undefined"
    >
        <transition
            name="gallery-fade"
            mode="out-in"
        >
            <img
                :key="
                    currentImage?.id
                    ?? `fallback-${safeIndex}`
                "
                :src="currentImageSrc"
                :alt="currentImageAlt"
                :class="[
                    imgClass,
                    roundedClass,
                    shadowClass,
                ]"
                :loading="loading"
                :fetchpriority="fetchpriority"
                :decoding="decoding"
                :itemprop="itemprop || undefined"
            />
        </transition>

        <!-- Назад -->
        <button
            v-if="
                showArrows
                && totalImages > 1
            "
            type="button"
            @click="prev"
            class="absolute top-1/2 left-0
                   -translate-y-1/2
                   bg-gray-700/75 text-white
                   px-3 py-1 rounded-r
                   focus:outline-none
                   focus-visible:ring-2
                   focus-visible:ring-white
                   transition
                   hover:bg-gray-800/85"
            :aria-label="previousLabel"
        >
            &#10094;
        </button>

        <!-- Вперёд -->
        <button
            v-if="
                showArrows
                && totalImages > 1
            "
            type="button"
            @click="next"
            class="absolute top-1/2 right-0
                   -translate-y-1/2
                   bg-gray-700/75 text-white
                   px-3 py-1 rounded-l
                   focus:outline-none
                   focus-visible:ring-2
                   focus-visible:ring-white
                   transition
                   hover:bg-gray-800/85"
            :aria-label="nextLabel"
        >
            &#10095;
        </button>

        <!-- Точки -->
        <div
            v-if="
                showDots
                && totalImages > 1
            "
            class="absolute bottom-2 left-1/2
                   -translate-x-1/2
                   flex items-center gap-2"
        >
            <button
                v-for="(_, index) in totalImages"
                :key="index"
                type="button"
                @click="setIndex(index)"
                class="w-2.5 h-2.5
                       rounded-full
                       border border-gray-400
                       transition
                       focus:outline-none
                       focus-visible:ring-2
                       focus-visible:ring-white"
                :class="
                    safeIndex === index
                        ? 'bg-red-500'
                        : 'bg-white/80 hover:bg-white'
                "
                :aria-label="slideAriaLabel(index)"
                :aria-current="
                    safeIndex === index
                        ? 'true'
                        : undefined
                "
            />
        </div>
    </div>
</template>

<style scoped>
.gallery-fade-enter-active,
.gallery-fade-leave-active {
    transition:
        opacity 0.55s ease,
        transform 0.55s ease;
}

.gallery-fade-enter-from,
.gallery-fade-leave-to {
    opacity: 0;
    transform: scale(1.015);
}
</style>
