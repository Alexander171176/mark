<script setup>
import {
    computed,
    onMounted,
    onUnmounted,
    ref,
    watch,
} from 'vue'

import { useI18n } from 'vue-i18n'

const props = defineProps({
    video: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()

/* ======================== State ======================== */

/**
 * Текущий слайд превью.
 */
const currentSlide =
    ref(0)

/**
 * ID интервала автослайдера.
 */
const intervalId =
    ref(null)

/**
 * Активное видео
 * после нажатия Play.
 */
const activeVideoId =
    ref(null)

/* ======================== Video data ======================== */

/**
 * Заголовок видео
 * из Public-контракта.
 */
const videoTitle = computed(() =>
    props.video?.translation?.title
    || ''
)

/**
 * Изображения превью.
 */
const videoImages = computed(() =>
    Array.isArray(props.video?.images)
        ? props.video.images
        : []
)

/**
 * Есть ли изображения превью.
 */
const hasImages = computed(() =>
    videoImages.value.length > 0
)

/**
 * URL изображения
 * из общего Image Resource.
 */
const getImageUrl = (image) =>
    image?.webp_url
    || image?.thumb_url
    || image?.image_url
    || image?.url
    || ''

/* ======================== YouTube ======================== */

/**
 * Получить YouTube ID:
 *
 * - чистый ID;
 * - watch URL;
 * - shorts URL;
 * - youtu.be;
 * - embed URL;
 * - youtube-nocookie.
 */
const extractYouTubeId = (value) => {
    if (!value) {
        return null
    }

    const source =
        String(value)
            .trim()

    if (
        /^[a-zA-Z0-9_-]{11}$/
            .test(source)
    ) {
        return source
    }

    const match =
        source.match(
            /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/|youtube-nocookie\.com\/embed\/)([a-zA-Z0-9_-]{11})/
        )

    if (match?.[1]) {
        return match[1]
    }

    try {
        const url =
            new URL(source)

        return url.searchParams
            .get('v')
    } catch {
        return null
    }
}

/**
 * Нормализация YouTube
 * в privacy-enhanced embed URL.
 */
const normalizeYouTubeEmbedUrl = (value) => {
    const id =
        extractYouTubeId(
            value
        )

    return id
        ? `https://www.youtube-nocookie.com/embed/${id}?rel=0`
        : null
}

/* ======================== Vimeo ======================== */

/**
 * Получить Vimeo ID:
 *
 * - чистый ID;
 * - vimeo.com;
 * - player.vimeo.com/video.
 */
const extractVimeoId = (value) => {
    if (!value) {
        return null
    }

    const source =
        String(value)
            .trim()

    if (
        /^\d+$/.test(source)
    ) {
        return source
    }

    const match =
        source.match(
            /vimeo\.com\/(?:video\/)?(\d+)/
        )

    return match?.[1]
        || null
}

/**
 * Нормализация Vimeo
 * в embed URL.
 */
const normalizeVimeoEmbedUrl = (value) => {
    if (!value) {
        return null
    }

    const source =
        String(value)
            .trim()

    if (
        source.includes(
            'player.vimeo.com/video/'
        )
    ) {
        return source
    }

    const id =
        extractVimeoId(
            source
        )

    return id
        ? `https://player.vimeo.com/video/${id}`
        : null
}

/* ======================== Source ======================== */

/**
 * Итоговый источник видео.
 *
 * Используем только поля
 * нового Public Resource:
 *
 * source_type
 * video_url
 * embed_url
 * embed_code
 * external_video_id
 */
const videoUrl = computed(() => {
    const video =
        props.video
        || {}

    switch (
        video.source_type
        ) {
        case 'youtube':
            return normalizeYouTubeEmbedUrl(
                video.embed_url
                || video.external_video_id
                || video.video_url
            )

        case 'vimeo':
            return normalizeVimeoEmbedUrl(
                video.embed_url
                || video.external_video_id
                || video.video_url
            )

        case 'local':
            return video.video_url
                || null

        case 'code':
            return video.embed_code
                || null

        default:
            return null
    }
})

/* ======================== Slideshow ======================== */

/**
 * Остановка автослайдера.
 */
const stopSlideshow = () => {
    if (
        intervalId.value
    ) {
        clearInterval(
            intervalId.value
        )

        intervalId.value =
            null
    }
}

/**
 * Запуск автослайдера.
 */
const startSlideshow = () => {
    stopSlideshow()

    if (
        videoImages.value.length > 1
    ) {
        intervalId.value =
            setInterval(
                () => {
                    currentSlide.value =
                        (
                            currentSlide.value
                            + 1
                        )
                        %
                        videoImages.value.length
                },
                4000
            )
    }
}

/* ======================== Playback ======================== */

/**
 * Запуск видео.
 */
const playVideo = () => {
    activeVideoId.value =
        props.video?.id
        ?? null

    stopSlideshow()
}

/**
 * При смене видео
 * полностью сбрасываем
 * состояние проигрывателя.
 */
watch(
    () =>
        props.video?.id,

    () => {
        currentSlide.value =
            0

        activeVideoId.value =
            null

        startSlideshow()
    }
)

/* ======================== Lifecycle ======================== */

onMounted(() => {
    startSlideshow()
})

onUnmounted(() => {
    stopSlideshow()
})
</script>

<template>
    <div
        class="relative w-full aspect-video
               bg-black mb-4 overflow-hidden"
    >
        <!-- ======================== Preview ======================== -->

        <template
            v-if="
                hasImages
                && activeVideoId !== video.id
            "
        >
            <div class="relative w-full h-full">
                <template
                    v-for="(img, index) in videoImages"
                    :key="img.id || index"
                >
                    <img
                        v-if="getImageUrl(img)"
                        :src="getImageUrl(img)"
                        :alt="
                            img.alt
                            || videoTitle
                        "
                        loading="lazy"
                        class="slide-fade
                               w-full h-full object-cover"
                        :class="{
                            'slide-fade-active':
                                index
                                === currentSlide,
                        }"
                    >
                </template>

                <div
                    class="absolute inset-0
                           flex items-center
                           justify-center z-20"
                >
                    <button
                        type="button"
                        :aria-label="videoTitle || t('videos')"
                        @click="playVideo"
                        class="bg-white/30 hover:bg-white/40
                               backdrop-blur-md rounded-full
                               p-2 border-8 border-white/30"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 text-red-600"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M8 5v14l11-7z"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <!-- ======================== Player ======================== -->

        <template v-else>
            <!-- Embed code -->
            <div
                v-if="
                    video.source_type === 'code'
                    && videoUrl
                "
                class="w-full h-full"
                v-html="videoUrl"
            />

            <!-- YouTube / Vimeo -->
            <iframe
                v-else-if="
                    videoUrl
                    && [
                        'youtube',
                        'vimeo',
                    ].includes(
                        video.source_type
                    )
                "
                :key="videoUrl"
                :src="videoUrl"
                :title="videoTitle"
                class="w-full h-full"
                frameborder="0"
                allow="accelerometer;
                       autoplay;
                       clipboard-write;
                       encrypted-media;
                       gyroscope;
                       picture-in-picture;
                       fullscreen"
                loading="lazy"
                allowfullscreen
            />

            <!-- Local -->
            <video
                v-else-if="
                    video.source_type === 'local'
                    && videoUrl
                "
                class="w-full h-full object-contain"
                controls
                preload="metadata"
            >
                <source
                    :src="videoUrl"
                >

                {{ t('videoNotSupported') }}
            </video>

            <!-- No source -->
            <div
                v-else
                class="flex h-full w-full
                       items-center justify-center
                       text-sm text-slate-200"
            >
                {{ t('videoNotSupported') }}
            </div>
        </template>
    </div>
</template>

<style scoped>
.slide-fade {
    transition: opacity 1s ease-in-out;
    opacity: 0;
    position: absolute;
    inset: 0;
}

.slide-fade-active {
    opacity: 1;
    z-index: 10;
}
</style>
