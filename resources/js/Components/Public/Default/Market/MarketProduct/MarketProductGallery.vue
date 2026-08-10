<script setup>
/**
 * Полноценная галерея изображений товара.
 *
 * - компактное главное изображение
 * - миниатюры снизу
 * - квадратные стрелки возле миниатюр
 * - fullscreen и счётчик поверх изображения
 * - полноэкранный режим
 * - пошаговое увеличение и уменьшение
 * - zoom колесом мыши
 * - сохранение zoom при переключении изображений
 * - клавиатура: ← → Esc
 * - свайп на мобильных устройствах
 */

import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue'

import { useI18n } from 'vue-i18n'
const { t } = useI18n()

/* ===================== PROPS ===================== */

const props = defineProps({
    images: {
        type: [Array, Object],
        default: () => [],
    },

    title: {
        type: String,
        default: '',
    },
})

/* ===================== DATA ===================== */

/** Нормализация коллекции изображений */
const imagesList = computed(() => {
    if (Array.isArray(props.images)) {
        return props.images
    }

    if (Array.isArray(props.images?.data)) {
        return props.images.data
    }

    return []
})

/** Количество изображений */
const totalImages = computed(() => imagesList.value.length)

/** Текущий индекс */
const currentIndex = ref(0)

/** Текущее изображение */
const currentImage = computed(() => {
    return imagesList.value[currentIndex.value] ?? null
})

/* ===================== IMAGE HELPERS ===================== */

/** Полноразмерное изображение */
const getImageUrl = (image) => {
    return image?.url
        || image?.original_url
        || image?.full_url
        || image?.image_url
        || image?.src
        || ''
}

/** Миниатюра */
const getThumbnailUrl = (image) => {
    return image?.thumb_url
        || image?.thumbnail_url
        || image?.small_url
        || getImageUrl(image)
}

/** Alt изображения */
const getImageAlt = (image, index) => {
    return image?.alt
        || image?.title
        || props.title
        || `Image ${index + 1}`
}

/* ===================== THUMBNAILS ===================== */

/** Контейнер обычных миниатюр */
const thumbnailsRef = ref(null)

/** Контейнер fullscreen-миниатюр */
const fullscreenThumbnailsRef = ref(null)

/** Прокрутить активную миниатюру в видимую область */
const scrollThumbnailContainer = async (containerRef) => {
    await nextTick()

    const container = containerRef.value

    if (!container) {
        return
    }

    const activeThumbnail = container.querySelector(
        `[data-thumbnail-index="${currentIndex.value}"]`
    )

    activeThumbnail?.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'center',
    })
}

/** Синхронизировать ленты миниатюр */
const scrollActiveThumbnail = () => {
    scrollThumbnailContainer(thumbnailsRef)
    scrollThumbnailContainer(fullscreenThumbnailsRef)
}

/* ===================== NAVIGATION ===================== */

/**
 * Установить изображение.
 *
 * Zoom намеренно не сбрасывается.
 */
const setIndex = (index) => {
    if (!totalImages.value) {
        return
    }

    currentIndex.value = Math.max(
        0,
        Math.min(index, totalImages.value - 1)
    )

    scrollActiveThumbnail()
}

/**
 * Предыдущее изображение.
 *
 * Zoom сохраняется.
 */
const prev = () => {
    if (totalImages.value <= 1) {
        return
    }

    currentIndex.value = currentIndex.value <= 0
        ? totalImages.value - 1
        : currentIndex.value - 1

    scrollActiveThumbnail()
}

/**
 * Следующее изображение.
 *
 * Zoom сохраняется.
 */
const next = () => {
    if (totalImages.value <= 1) {
        return
    }

    currentIndex.value = currentIndex.value >= totalImages.value - 1
        ? 0
        : currentIndex.value + 1

    scrollActiveThumbnail()
}

/* ===================== FULLSCREEN ===================== */

/** Полноэкранный режим */
const isFullscreen = ref(false)

/** Контейнер fullscreen */
const fullscreenRef = ref(null)

/** Открыть полноэкранную галерею */
const openFullscreen = async () => {
    if (!currentImage.value) {
        return
    }

    /**
     * При новом открытии fullscreen
     * начинаем со стандартного масштаба.
     */
    resetZoom()

    isFullscreen.value = true

    await nextTick()

    fullscreenRef.value?.focus()
    scrollActiveThumbnail()
}

/** Закрыть полноэкранную галерею */
const closeFullscreen = () => {
    isFullscreen.value = false
}

/* ===================== ZOOM ===================== */

/**
 * Уровни масштаба.
 *
 * Минимум: 50%.
 * Стандарт: 100%.
 * Максимум: 300%.
 */
const zoomLevels = [
    0.5,
    0.75,
    1,
    1.25,
    1.5,
    1.75,
    2,
    2.25,
    2.5,
    2.75,
    3,
]

/** Индекс масштаба 100% */
const DEFAULT_ZOOM_INDEX = 2

/** Текущий индекс масштаба */
const zoomIndex = ref(DEFAULT_ZOOM_INDEX)

/** Текущий коэффициент */
const zoomScale = computed(() => {
    return zoomLevels[zoomIndex.value]
})

/** Масштаб в процентах */
const zoomPercent = computed(() => {
    return Math.round(
        zoomScale.value * 100
    )
})

/** Можно увеличить */
const canZoomIn = computed(() => {
    return zoomIndex.value < zoomLevels.length - 1
})

/** Можно уменьшить */
const canZoomOut = computed(() => {
    return zoomIndex.value > 0
})

/** Увеличить на один шаг */
const zoomIn = () => {
    if (!canZoomIn.value) {
        return
    }

    zoomIndex.value++
}

/** Уменьшить на один шаг */
const zoomOut = () => {
    if (!canZoomOut.value) {
        return
    }

    zoomIndex.value--
}

/** Сбросить к 100% */
const resetZoom = () => {
    zoomIndex.value = DEFAULT_ZOOM_INDEX
}

/**
 * Zoom колесом мыши.
 *
 * Колесо вверх  → увеличить.
 * Колесо вниз   → уменьшить.
 */
const handleWheel = (event) => {
    if (!isFullscreen.value) {
        return
    }

    if (event.deltaY < 0) {
        zoomIn()
        return
    }

    if (event.deltaY > 0) {
        zoomOut()
    }
}

/* ===================== KEYBOARD ===================== */

/** Управление клавиатурой */
const handleKeydown = (event) => {
    if (!isFullscreen.value) {
        return
    }

    if (event.key === 'Escape') {
        closeFullscreen()
        return
    }

    if (event.key === 'ArrowLeft') {
        prev()
        return
    }

    if (event.key === 'ArrowRight') {
        next()
    }
}

/* ===================== SWIPE ===================== */

const touchStartX = ref(0)
const touchStartY = ref(0)

/** Начало свайпа */
const handleTouchStart = (event) => {
    const touch = event.touches?.[0]

    if (!touch) {
        return
    }

    touchStartX.value = touch.clientX
    touchStartY.value = touch.clientY
}

/** Завершение свайпа */
const handleTouchEnd = (event) => {
    const touch = event.changedTouches?.[0]

    if (!touch) {
        return
    }

    const deltaX = touch.clientX - touchStartX.value
    const deltaY = touch.clientY - touchStartY.value

    /** Вертикальное движение не считаем свайпом */
    if (Math.abs(deltaY) > Math.abs(deltaX)) {
        return
    }

    /** Слишком короткое движение */
    if (Math.abs(deltaX) < 50) {
        return
    }

    deltaX > 0
        ? prev()
        : next()
}

/* ===================== WATCH ===================== */

/** Контроль индекса при изменении коллекции */
watch(imagesList, () => {
    if (currentIndex.value >= totalImages.value) {
        currentIndex.value = 0
    }

    /**
     * Сброс здесь допустим:
     * изменилась сама коллекция изображений.
     */
    resetZoom()
})

/** Блокировка прокрутки страницы */
watch(isFullscreen, (value) => {
    if (typeof document === 'undefined') {
        return
    }

    document.body.style.overflow = value
        ? 'hidden'
        : ''
})

/* ===================== LIFECYCLE ===================== */

onMounted(() => {
    window.addEventListener(
        'keydown',
        handleKeydown
    )
})

onBeforeUnmount(() => {
    window.removeEventListener(
        'keydown',
        handleKeydown
    )

    if (typeof document !== 'undefined') {
        document.body.style.overflow = ''
    }
})
</script>

<template>
    <div class="w-full min-w-0">
        <!-- ================= ОСНОВНАЯ ГАЛЕРЕЯ ================= -->

        <div
            v-if="totalImages"
            class="flex flex-col gap-3"
        >
            <!-- Главное изображение -->
            <div
                class="relative min-w-0 overflow-hidden
                       bg-slate-200 dark:bg-slate-800"
                @touchstart.passive="handleTouchStart"
                @touchend.passive="handleTouchEnd"
            >
                <!-- Изображение -->
                <div
                    class="flex h-[180px] w-full items-center justify-center
                           sm:h-[410px] lg:h-[360px]"
                >
                    <img
                        :src="getImageUrl(currentImage)"
                        :alt="getImageAlt(currentImage, currentIndex)"
                        class="max-h-full max-w-full object-contain"
                    />
                </div>

                <!-- Управление поверх изображения -->
                <div
                    class="absolute right-3 top-3 z-10
                           flex items-center gap-2"
                >
                    <!-- Счётчик -->
                    <div
                        v-if="totalImages > 1"
                        class="flex h-8 items-center justify-center
                               rounded-sm bg-black/55 px-3
                               text-xs font-semibold text-white
                               shadow-sm backdrop-blur-sm"
                    >
                        {{ currentIndex + 1 }} / {{ totalImages }}
                    </div>

                    <!-- Fullscreen -->
                    <button
                        type="button"
                        @click="openFullscreen"
                        class="flex h-8 w-8 items-center justify-center
                               rounded-sm bg-black/55 text-white
                               shadow-sm backdrop-blur-sm transition
                               hover:bg-black/75 focus:outline-none"
                        aria-label="Open image fullscreen"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M8 3H5a2 2 0 0 0-2 2v3"/>
                            <path d="M16 3h3a2 2 0 0 1 2 2v3"/>
                            <path d="M8 21H5a2 2 0 0 1-2-2v-3"/>
                            <path d="M16 21h3a2 2 0 0 0 2-2v-3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Миниатюры -->
            <div
                v-if="totalImages > 1"
                class="flex w-full items-center gap-2"
            >
                <!-- Назад -->
                <button
                    type="button"
                    @click="prev"
                    class="flex h-16 w-10 shrink-0
                           items-center justify-center
                           rounded-sm border border-gray-400
                           bg-white text-slate-600 shadow-sm
                           transition hover:border-indigo-400
                           hover:bg-slate-100 hover:text-indigo-500
                           focus:outline-none
                           dark:border-gray-600
                           dark:bg-slate-900
                           dark:text-slate-300
                           dark:hover:border-indigo-500
                           dark:hover:bg-slate-800"
                    aria-label="Previous image"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                </button>

                <!-- Лента миниатюр -->
                <div
                    ref="thumbnailsRef"
                    class="flex min-w-0 flex-1 justify-center gap-2
                           bg-slate-100 dark:bg-slate-800
                           overflow-x-auto scroll-smooth
                           [scrollbar-width:none]
                           [&::-webkit-scrollbar]:hidden"
                >
                    <button
                        v-for="(image, index) in imagesList"
                        :key="image.id ?? index"
                        :data-thumbnail-index="index"
                        type="button"
                        @click="setIndex(index)"
                        class="relative h-16 w-16 shrink-0
                               overflow-hidden rounded-sm border-2
                               bg-white transition
                               hover:border-indigo-400
                               dark:bg-slate-950"
                        :class="currentIndex === index
                            ? 'border-indigo-500 shadow-sm'
                            : 'border-gray-300 dark:border-gray-700'"
                        :aria-label="`Image ${index + 1}`"
                    >
                        <img
                            :src="getThumbnailUrl(image)"
                            :alt="getImageAlt(image, index)"
                            loading="lazy"
                            class="h-full w-full object-contain p-1"
                        />

                        <span
                            v-if="currentIndex === index"
                            class="pointer-events-none absolute inset-0
                                   ring-1 ring-inset ring-indigo-500"
                        />
                    </button>
                </div>

                <!-- Вперёд -->
                <button
                    type="button"
                    @click="next"
                    class="flex h-16 w-10 shrink-0
                           items-center justify-center
                           rounded-sm border border-gray-400
                           bg-white text-slate-600 shadow-sm
                           transition hover:border-indigo-400
                           hover:bg-slate-100 hover:text-indigo-500
                           focus:outline-none
                           dark:border-gray-600
                           dark:bg-slate-900
                           dark:text-slate-300
                           dark:hover:border-indigo-500
                           dark:hover:bg-slate-800"
                    aria-label="Next image"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Нет изображений -->
        <div
            v-else
            class="flex min-h-72 items-center justify-center
                   rounded-sm border border-gray-300
                   bg-slate-100 text-sm text-slate-400
                   dark:border-gray-700 dark:bg-slate-800"
        >
            {{ t('noImage') }}
        </div>

        <!-- ================= FULLSCREEN ================= -->

        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isFullscreen"
                    ref="fullscreenRef"
                    tabindex="-1"
                    role="dialog"
                    aria-modal="true"
                    class="fixed inset-0 z-[100]
                           flex flex-col bg-gray-800/95 outline-none"
                    @touchstart.passive="handleTouchStart"
                    @touchend.passive="handleTouchEnd"
                >
                    <!-- Верхняя панель -->
                    <div
                        class="relative z-30 flex h-16 shrink-0
                               items-center justify-between
                               border-b border-white/10
                               bg-black/20 px-4 text-white"
                    >
                        <!-- Счётчик -->
                        <div
                            class="text-sm font-semibold text-white/80"
                        >
                            {{ currentIndex + 1 }} / {{ totalImages }}
                        </div>

                        <!-- Управление -->
                        <div class="flex items-center gap-2">
                            <!-- Масштаб -->
                            <div
                                class="hidden min-w-14 text-center
                                       text-xs font-semibold
                                       text-white/70 sm:block"
                            >
                                {{ zoomPercent }}%
                            </div>

                            <!-- Уменьшить -->
                            <button
                                type="button"
                                @click="zoomOut"
                                :disabled="!canZoomOut"
                                class="flex h-10 w-10 items-center
                                       justify-center rounded-sm
                                       bg-white/10 transition
                                       hover:bg-white/20
                                       disabled:cursor-not-allowed
                                       disabled:opacity-30"
                                aria-label="Zoom out"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.5-3.5"/>
                                    <path d="M8 11h6"/>
                                </svg>
                            </button>

                            <!-- Увеличить -->
                            <button
                                type="button"
                                @click="zoomIn"
                                :disabled="!canZoomIn"
                                class="flex h-10 w-10 items-center
                                       justify-center rounded-sm
                                       bg-white/10 transition
                                       hover:bg-white/20
                                       disabled:cursor-not-allowed
                                       disabled:opacity-30"
                                aria-label="Zoom in"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.5-3.5"/>
                                    <path d="M11 8v6"/>
                                    <path d="M8 11h6"/>
                                </svg>
                            </button>

                            <!-- Сбросить zoom -->
                            <button
                                type="button"
                                @click="resetZoom"
                                class="hidden h-10 items-center justify-center
                                       rounded-sm bg-white/10 px-3
                                       text-xs font-semibold transition
                                       hover:bg-white/20 sm:flex"
                                aria-label="Reset zoom"
                            >
                                100%
                            </button>

                            <!-- Закрыть -->
                            <button
                                type="button"
                                @click="closeFullscreen"
                                class="flex h-10 w-10 items-center
                                       justify-center rounded-sm
                                       bg-white/10 transition
                                       hover:bg-white/20"
                                aria-label="Close"
                            >
                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M18 6 6 18"/>
                                    <path d="m6 6 12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Полноэкранное изображение -->
                    <div
                        class="relative flex min-h-0 flex-1
                               items-center justify-center
                               overflow-auto px-16 py-5"
                        @wheel.prevent="handleWheel"
                    >
                        <!-- Изображение -->
                        <div
                            class="flex min-h-full min-w-full
                                   items-center justify-center"
                        >
                            <img
                                :src="getImageUrl(currentImage)"
                                :alt="getImageAlt(
                                    currentImage,
                                    currentIndex
                                )"
                                class="max-h-[calc(100vh-11rem)]
                                       max-w-[calc(100vw-10rem)]
                                       object-contain
                                       transition-transform
                                       duration-200 ease-out
                                       select-none"
                                :style="{
                                    transform: `scale(${zoomScale})`,
                                }"
                                draggable="false"
                            />
                        </div>

                        <!-- Назад -->
                        <button
                            v-if="totalImages > 1"
                            type="button"
                            @click.stop="prev"
                            class="fixed left-4 top-1/2 z-20
                                   flex h-12 w-12
                                   -translate-y-1/2
                                   items-center justify-center
                                   rounded-sm bg-white/10
                                   text-white backdrop-blur-sm
                                   transition hover:bg-white/20"
                            aria-label="Previous image"
                        >
                            <svg
                                class="h-7 w-7"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>

                        <!-- Вперёд -->
                        <button
                            v-if="totalImages > 1"
                            type="button"
                            @click.stop="next"
                            class="fixed right-4 top-1/2 z-20
                                   flex h-12 w-12
                                   -translate-y-1/2
                                   items-center justify-center
                                   rounded-sm bg-white/10
                                   text-white backdrop-blur-sm
                                   transition hover:bg-white/20"
                            aria-label="Next image"
                        >
                            <svg
                                class="h-7 w-7"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Нижняя лента fullscreen -->
                    <div
                        v-if="totalImages > 1"
                        class="relative z-30 shrink-0
                               border-t border-white/10
                               bg-black/30 px-4 py-3"
                    >
                        <div
                            class="mx-auto flex max-w-5xl
                                   items-center gap-2"
                        >
                            <!-- Назад -->
                            <button
                                type="button"
                                @click="prev"
                                class="flex h-16 w-10 shrink-0
                                       items-center justify-center
                                       rounded-sm bg-white/10
                                       text-white transition
                                       hover:bg-white/20"
                                aria-label="Previous image"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                            </button>

                            <!-- Миниатюры -->
                            <div
                                ref="fullscreenThumbnailsRef"
                                class="flex min-w-0 flex-1
                                       items-center justify-center gap-2
                                       overflow-x-auto scroll-smooth
                                       [scrollbar-width:none]
                                       [&::-webkit-scrollbar]:hidden"
                            >
                                <button
                                    v-for="(image, index) in imagesList"
                                    :key="image.id ?? index"
                                    :data-thumbnail-index="index"
                                    type="button"
                                    @click="setIndex(index)"
                                    class="h-16 w-16 shrink-0
                                           overflow-hidden rounded-sm
                                           border-2 bg-white/5
                                           transition"
                                    :class="currentIndex === index
                                        ? 'border-white opacity-100'
                                        : 'border-transparent opacity-60 hover:opacity-100'"
                                    :aria-label="`Image ${index + 1}`"
                                >
                                    <img
                                        :src="getThumbnailUrl(image)"
                                        :alt="getImageAlt(image, index)"
                                        loading="lazy"
                                        class="h-full w-full
                                               object-contain p-1"
                                    />
                                </button>
                            </div>

                            <!-- Вперёд -->
                            <button
                                type="button"
                                @click="next"
                                class="flex h-16 w-10 shrink-0
                                       items-center justify-center
                                       rounded-sm bg-white/10
                                       text-white transition
                                       hover:bg-white/20"
                                aria-label="Next image"
                            >
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
