<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    video: { type: Object, required: true },
})

const { t } = useI18n()

/** Текущий слайд превью */
const currentSlide = ref(0)

/** ID интервала автослайдера */
const intervalId = ref(null)

/** Активное видео после нажатия Play */
const activeVideoId = ref(null)

/** Заголовок видео с учётом новой мультиязычной структуры */
const videoTitle = computed(() => {
    return props.video?.translation?.title ||
        props.video?.title ||
        ''
})

/** Изображения превью */
const videoImages = computed(() => {
    return Array.isArray(props.video?.images) ? props.video.images : []
})

/** Есть ли превью изображения */
const hasImages = computed(() => videoImages.value.length > 0)

/** Получить URL изображения из разных форматов ресурса */
const getImageUrl = (image) => {
    return image?.webp_url ||
        image?.thumb_url ||
        image?.url ||
        image?.image_url ||
        ''
}

/** Получить YouTube ID из ID, watch-url, shorts-url или embed-url */
const extractYouTubeId = (value) => {
    if (!value) {
        return null
    }

    const source = String(value).trim()

    if (/^[a-zA-Z0-9_-]{11}$/.test(source)) {
        return source
    }

    const match = source.match(
        /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/|youtube-nocookie\.com\/embed\/)([a-zA-Z0-9_-]{11})/
    )

    if (match?.[1]) {
        return match[1]
    }

    try {
        const url = new URL(source)
        return url.searchParams.get('v')
    } catch {
        return null
    }
}

/** Получить Vimeo ID из ID или URL */
const extractVimeoId = (value) => {
    if (!value) {
        return null
    }

    const source = String(value).trim()

    if (/^\d+$/.test(source)) {
        return source
    }

    const match = source.match(/vimeo\.com\/(?:video\/)?(\d+)/)

    return match?.[1] || null
}

/** Нормализация YouTube в embed URL */
const normalizeYouTubeEmbedUrl = (value) => {
    if (!value) {
        return null
    }

    const source = String(value).trim()

    if (
        source.startsWith('https://') &&
        source.includes('/embed/')
    ) {
        return source
    }

    const id = extractYouTubeId(source)

    return id
        ? `https://www.youtube-nocookie.com/embed/${id}?rel=0`
        : null
}

/** Нормализация Vimeo в embed URL */
const normalizeVimeoEmbedUrl = (value) => {
    if (!value) {
        return null
    }

    const source = String(value).trim()

    if (source.includes('player.vimeo.com/video/')) {
        return source
    }

    const id = extractVimeoId(source)

    return id
        ? `https://player.vimeo.com/video/${id}`
        : null
}

/** Итоговый URL/код видео */
const videoUrl = computed(() => {
    const video = props.video || {}
    const sourceType = video.source_type

    if (sourceType === 'youtube') {
        return normalizeYouTubeEmbedUrl(
            video.embed_url ||
            video.display_source ||
            video.external_video_id ||
            video.video_url
        )
    }

    if (sourceType === 'vimeo') {
        return normalizeVimeoEmbedUrl(
            video.embed_url ||
            video.display_source ||
            video.external_video_id ||
            video.video_url
        )
    }

    if (sourceType === 'local') {
        return video.video_url ||
            video.display_source ||
            video.external_video_id ||
            null
    }

    if (sourceType === 'code') {
        return video.embed_code ||
            video.video_code ||
            video.display_source ||
            null
    }

    return null
})

/** Запуск автослайдера превью */
const startSlideshow = () => {
    stopSlideshow()

    if (videoImages.value.length > 1) {
        intervalId.value = setInterval(() => {
            currentSlide.value = (currentSlide.value + 1) % videoImages.value.length
        }, 4000)
    }
}

/** Остановка автослайдера */
const stopSlideshow = () => {
    if (intervalId.value) {
        clearInterval(intervalId.value)
        intervalId.value = null
    }
}

/** Запуск видео */
const playVideo = () => {
    activeVideoId.value = props.video.id
    stopSlideshow()
}

/** Сброс состояния при смене видео */
watch(
    () => props.video?.id,
    () => {
        currentSlide.value = 0
        activeVideoId.value = null
        startSlideshow()
    }
)

onMounted(() => {
    startSlideshow()
})

onUnmounted(() => {
    stopSlideshow()
})
</script>

<template>
    <div class="relative w-full aspect-video bg-black mb-4 overflow-hidden">
        <template v-if="hasImages && activeVideoId !== video.id">
            <div class="relative w-full h-full">
                <template
                    v-for="(img, index) in videoImages"
                    :key="img.id || index"
                >
                    <img
                        v-if="getImageUrl(img)"
                        :src="getImageUrl(img)"
                        :alt="img.alt || videoTitle"
                        loading="lazy"
                        class="slide-fade w-full h-full object-cover"
                        :class="{ 'slide-fade-active': index === currentSlide }"
                    />
                </template>

                <div class="absolute inset-0 flex items-center justify-center z-20">
                    <button
                        type="button"
                        @click="playVideo"
                        class="bg-white/30 hover:bg-white/40 backdrop-blur-md rounded-full
                               p-2 border-8 border-white/30"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 text-red-600"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <template v-else>
            <div
                v-if="video.source_type === 'code' && videoUrl"
                class="w-full h-full"
                v-html="videoUrl"
            />

            <iframe
                v-else-if="videoUrl && ['youtube', 'vimeo'].includes(video.source_type)"
                :key="videoUrl"
                :src="videoUrl"
                class="w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                loading="lazy"
            />

            <video
                v-else-if="video.source_type === 'local' && videoUrl"
                class="w-full h-full object-contain"
                controls
            >
                <source :src="videoUrl" type="video/mp4" />
                {{ t('videoNotSupported') }}
            </video>

            <div
                v-else
                class="flex h-full w-full items-center justify-center text-sm text-slate-200"
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
