<script setup>
import {
    computed,
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
} from 'vue'

import {
    unwrap,
    unwrapList,
} from '@/composables/useUnwrap.js'

const props = defineProps({
    banners: {
        type: [Array, Object],
        default: () => [],
    },

    intervalMs: {
        type: Number,
        default: 4500,
    },

    pauseOnHover: {
        type: Boolean,
        default: true,
    },

    pauseOnHidden: {
        type: Boolean,
        default: true,
    },
})

/**
 * Нормализованный список баннеров.
 */
const list = computed(() =>
    unwrapList(props.banners)
)

/**
 * Перевод баннера
 * из Public BlogBannerSharedResource.
 */
const getBannerTranslation = (banner) => {
    const item = unwrap(banner)

    return item?.translation || {}
}

/**
 * Заголовок баннера.
 */
const getBannerTitle = (banner) => {
    return getBannerTranslation(banner)?.title || ''
}

/**
 * Внешняя ссылка баннера.
 */
const getBannerLink = (banner) => {
    return getBannerTranslation(banner)?.link || ''
}

/**
 * Изображения баннера.
 *
 * Public Resource отдаёт готовые
 * публичные URL изображений.
 */
const getBannerImages = (banner) => {
    const item = unwrap(banner)

    const imagesRaw = Array.isArray(item?.images)
        ? item.images
        : (item?.images?.data ?? [])

    return (Array.isArray(imagesRaw) ? imagesRaw : [])
        .slice()
        .sort(
            (a, b) =>
                Number(a?.order ?? 0)
                - Number(b?.order ?? 0)
        )
        .map((image, index) => {
            const src =
                image?.image_url
                || image?.url
                || image?.src
                || image?.path
                || image?.image
                || ''

            return {
                id:
                    image?.id
                    ?? `${src || 'image'}-${image?.order ?? index}`,

                src,

                alt:
                    image?.alt
                    || getBannerTitle(banner),

                title:
                    image?.title
                    || image?.alt
                    || getBannerTitle(banner),

                order: Number(
                    image?.order ?? 0
                ),
            }
        })
        .filter(image => Boolean(image.src))
}

/**
 * Состояние слайдера
 * отдельно для каждого баннера.
 */
const currentByBanner = ref({})
const hoveredByBanner = ref({})

let timer = null

/**
 * Текущий индекс изображения.
 */
const getCurrent = (bannerId) =>
    Number(
        currentByBanner.value?.[bannerId] ?? 0
    )

/**
 * Установить текущее изображение.
 */
const setCurrent = (
    bannerId,
    index,
    total
) => {
    const count = Number(total) || 0

    if (count <= 1) {
        currentByBanner.value = {
            ...currentByBanner.value,
            [bannerId]: 0,
        }

        return
    }

    const value = Number(index)

    const safeIndex = Number.isFinite(value)
        ? Math.min(
            Math.max(0, value),
            count - 1
        )
        : 0

    currentByBanner.value = {
        ...currentByBanner.value,
        [bannerId]: safeIndex,
    }
}

/**
 * Можно ли выполнять автопрокрутку.
 */
const canRun = () => {
    return !(
        props.pauseOnHidden
        && typeof document !== 'undefined'
        && document.hidden
    )
}

/**
 * Переключить изображения баннеров.
 */
const tick = () => {
    if (!canRun()) {
        return
    }

    const nextState = {
        ...currentByBanner.value,
    }

    for (const bannerItem of list.value) {
        const banner = unwrap(bannerItem)
        const id = banner?.id

        if (!id) {
            continue
        }

        const images =
            getBannerImages(bannerItem)

        if (images.length <= 1) {
            continue
        }

        if (
            props.pauseOnHover
            && hoveredByBanner.value?.[id]
        ) {
            continue
        }

        const current =
            Number(nextState[id] ?? 0)

        nextState[id] =
            (current + 1) % images.length
    }

    currentByBanner.value =
        nextState
}

/**
 * Остановить таймер.
 */
const stop = () => {
    if (!timer) {
        return
    }

    clearInterval(timer)
    timer = null
}

/**
 * Запустить таймер.
 */
const start = () => {
    stop()

    const hasAnySlider =
        list.value.some(
            banner =>
                getBannerImages(banner).length > 1
        )

    if (!hasAnySlider) {
        return
    }

    timer = setInterval(
        tick,
        Math.max(
            1500,
            Number(props.intervalMs) || 4500
        )
    )
}

/**
 * Сбросить состояние слайдера
 * для текущего списка баннеров.
 */
const resetState = () => {
    const initialState = {}

    for (const banner of list.value) {
        const id = unwrap(banner)?.id

        if (id) {
            initialState[id] = 0
        }
    }

    currentByBanner.value =
        initialState
}

/**
 * Изменение видимости вкладки.
 */
const onVisibilityChange = () => {
    start()
}

onMounted(() => {
    resetState()
    start()

    if (
        props.pauseOnHidden
        && typeof document !== 'undefined'
    ) {
        document.addEventListener(
            'visibilitychange',
            onVisibilityChange
        )
    }
})

onBeforeUnmount(() => {
    stop()

    if (
        props.pauseOnHidden
        && typeof document !== 'undefined'
    ) {
        document.removeEventListener(
            'visibilitychange',
            onVisibilityChange
        )
    }
})

watch(
    () => [
        list.value.length,
        props.intervalMs,
    ],
    () => {
        resetState()
        start()
    }
)
</script>

<template>
    <!-- Блок баннеров -->
    <div
        v-if="list.length"
        class="mb-1"
    >
        <div class="grid gap-4">
            <div
                v-for="banner in list"
                :key="unwrap(banner).id"
                class="overflow-hidden rounded-md
                       border border-slate-400 dark:border-slate-500
                       shadow-md shadow-gray-400 dark:shadow-gray-800
                       hover:shadow-lg transition-shadow"
            >
                <!-- IMAGE -->
                <div
                    v-if="getBannerImages(banner).length"
                    class="relative w-full overflow-hidden
                           bg-slate-100 dark:bg-slate-900
                           aspect-[16/9] sm:aspect-[4/3] lg:aspect-[16/10]"
                    @mouseenter="
                        hoveredByBanner = {
                            ...hoveredByBanner,
                            [unwrap(banner).id]: true,
                        }
                    "
                    @mouseleave="
                        hoveredByBanner = {
                            ...hoveredByBanner,
                            [unwrap(banner).id]: false,
                        }
                    "
                >
                    <!-- WITH LINK -->
                    <a
                        v-if="getBannerLink(banner)"
                        :href="getBannerLink(banner)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block w-full h-full"
                    >
                        <Transition
                            name="bnfx"
                            mode="out-in"
                        >
                            <img
                                :key="
                                    getBannerImages(banner)[
                                        getCurrent(
                                            unwrap(banner).id
                                        )
                                    ]?.id
                                "
                                class="w-full h-full object-cover"
                                :src="
                                    getBannerImages(banner)[
                                        getCurrent(
                                            unwrap(banner).id
                                        )
                                    ]?.src
                                "
                                :alt="
                                    getBannerImages(banner)[
                                        getCurrent(
                                            unwrap(banner).id
                                        )
                                    ]?.alt
                                "
                                :title="
                                    getBannerImages(banner)[
                                        getCurrent(
                                            unwrap(banner).id
                                        )
                                    ]?.title
                                "
                                loading="lazy"
                            />
                        </Transition>
                    </a>

                    <!-- WITHOUT LINK -->
                    <Transition
                        v-else
                        name="bnfx"
                        mode="out-in"
                    >
                        <img
                            :key="
                                getBannerImages(banner)[
                                    getCurrent(
                                        unwrap(banner).id
                                    )
                                ]?.id
                            "
                            class="w-full h-full object-cover"
                            :src="
                                getBannerImages(banner)[
                                    getCurrent(
                                        unwrap(banner).id
                                    )
                                ]?.src
                            "
                            :alt="
                                getBannerImages(banner)[
                                    getCurrent(
                                        unwrap(banner).id
                                    )
                                ]?.alt
                            "
                            :title="
                                getBannerImages(banner)[
                                    getCurrent(
                                        unwrap(banner).id
                                    )
                                ]?.title
                            "
                            loading="lazy"
                        />
                    </Transition>

                    <!-- DOTS -->
                    <div
                        v-if="getBannerImages(banner).length > 1"
                        class="absolute left-0 right-0 bottom-0 px-3 pb-2"
                    >
                        <div
                            class="flex items-center justify-center gap-1.5"
                        >
                            <button
                                v-for="(image, index) in getBannerImages(banner)"
                                :key="image.id"
                                type="button"
                                class="h-2 w-2 rounded-full transition-all"
                                :class="
                                    index === getCurrent(
                                        unwrap(banner).id
                                    )
                                        ? 'bg-orange-400 shadow ring-1 ring-black/40'
                                        : 'bg-white/60 hover:bg-orange-400/80'
                                "
                                :aria-label="`banner image ${index + 1}`"
                                :title="image.title"
                                @click.prevent.stop="
                                    setCurrent(
                                        unwrap(banner).id,
                                        index,
                                        getBannerImages(banner).length
                                    )
                                "
                            />
                        </div>
                    </div>
                </div>

                <!-- TITLE -->
                <div
                    v-if="getBannerTitle(banner)"
                    class="p-3"
                >
                    <div
                        class="text-center font-semibold
                               text-sm leading-snug"
                    >
                        <a
                            v-if="getBannerLink(banner)"
                            :href="getBannerLink(banner)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:underline transition
                                   text-slate-900/85 dark:text-slate-100/85
                                   hover:text-indigo-700
                                   dark:hover:text-indigo-300"
                        >
                            {{ getBannerTitle(banner) }}
                        </a>

                        <span
                            v-else
                            class="text-slate-900/85
                                   dark:text-slate-100/85"
                        >
                            {{ getBannerTitle(banner) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bnfx-enter-active {
    transition:
        opacity 520ms ease,
        filter 520ms ease;
    will-change: opacity, filter;
}

.bnfx-leave-active {
    transition:
        opacity 280ms ease,
        filter 280ms ease;
    will-change: opacity, filter;
}

.bnfx-enter-from {
    opacity: 0;
    filter: blur(6px);
}

.bnfx-enter-to {
    opacity: 1;
    filter: blur(0);
}

.bnfx-leave-from {
    opacity: 1;
    filter: blur(0);
}

.bnfx-leave-to {
    opacity: 0;
    filter: blur(5px);
}
</style>
