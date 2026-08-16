<script setup>
import {
    computed,
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
} from 'vue'

import { unwrap } from '@/composables/useUnwrap.js'

const props = defineProps({
    leftBanners: {
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

const unwrapList = (value) => {
    return value?.data ?? value ?? []
}

const list = computed(() => {
    return unwrapList(
        props.leftBanners
    )
})

/**
 * Новый Public-контракт перевода.
 */
const bannerTranslation = (banner) => {
    return unwrap(banner)?.translation || {}
}

const bannerTitle = (banner) => {
    return bannerTranslation(banner).title || ''
}

const bannerLink = (banner) => {
    return bannerTranslation(banner).link || ''
}

/**
 * Изображения Public Resource.
 */
const bannerImages = (banner) => {
    const entity = unwrap(banner)

    const images = Array.isArray(entity?.images)
        ? entity.images
        : (entity?.images?.data ?? [])

    return (Array.isArray(images) ? images : [])
        .slice()
        .sort(
            (a, b) =>
                Number(a?.order ?? 0)
                - Number(b?.order ?? 0)
        )
        .map((image, index) => {
            const src =
                image?.image_url
                || image?.webp_url
                || image?.thumb_url
                || image?.url
                || ''

            return {
                id:
                    image?.id
                    ?? `${src}-${image?.order ?? index}`,

                src,

                alt:
                    image?.alt ?? '',

                title:
                    image?.caption
                    || image?.alt
                    || '',

                order:
                    Number(
                        image?.order ?? 0
                    ),
            }
        })
        .filter(image => !!image.src)
}

/**
 * Состояние слайдера
 * отдельно для каждого баннера.
 */
const currentByBanner = ref({})
const hoveredByBanner = ref({})

let timer = null

const getCurrent = (bannerId) => {
    return Number(
        currentByBanner.value?.[bannerId]
        ?? 0
    )
}

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

    const requested = Number(index)

    const safeIndex = Number.isFinite(requested)
        ? Math.min(
            Math.max(0, requested),
            count - 1
        )
        : 0

    currentByBanner.value = {
        ...currentByBanner.value,
        [bannerId]: safeIndex,
    }
}

const canRun = () => {
    return !(
        props.pauseOnHidden
        && typeof document !== 'undefined'
        && document.hidden
    )
}

const tick = () => {
    if (!canRun()) return

    const nextState = {
        ...currentByBanner.value,
    }

    for (const banner of list.value) {
        const entity = unwrap(banner)
        const id = entity?.id

        if (!id) continue

        const images = bannerImages(banner)

        if (images.length <= 1) continue

        if (
            props.pauseOnHover
            && hoveredByBanner.value?.[id]
        ) {
            continue
        }

        const current = Number(
            nextState[id] ?? 0
        )

        nextState[id] =
            (current + 1) % images.length
    }

    currentByBanner.value = nextState
}

const stop = () => {
    if (!timer) return

    clearInterval(timer)
    timer = null
}

const start = () => {
    stop()

    const hasSlider = list.value.some(
        banner =>
            bannerImages(banner).length > 1
    )

    if (!hasSlider) return

    timer = setInterval(
        tick,
        Math.max(
            1500,
            Number(props.intervalMs) || 4500
        )
    )
}

const onVisibilityChange = () => {
    start()
}

onMounted(() => {
    const initialState = {}

    for (const banner of list.value) {
        const id = unwrap(banner)?.id

        if (id) {
            initialState[id] = 0
        }
    }

    currentByBanner.value =
        initialState

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
        start()
    }
)
</script>

<template>
    <!-- Блок Баннеров-->
    <div v-if="list.length" class="mb-1">
        <div class="grid gap-4">
            <div
                v-for="b in list"
                :key="unwrap(b).id"
                class="overflow-hidden rounded-md
                       border border-slate-400 dark:border-slate-500
                       shadow-md shadow-gray-400 dark:shadow-gray-800
                       hover:shadow-lg transition-shadow"
            >
                <!-- Image slider -->
                <div
                    class="relative w-full overflow-hidden
                           bg-slate-100 dark:bg-slate-900
                           aspect-[16/9] sm:aspect-[4/3] lg:aspect-[16/10]"
                    @mouseenter="hoveredByBanner = { ...hoveredByBanner, [unwrap(b).id]: true }"
                    @mouseleave="hoveredByBanner = { ...hoveredByBanner, [unwrap(b).id]: false }"
                >
                    <a
                        v-if="bannerLink(b)"
                        :href="bannerLink(b)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block w-full h-full"
                    >
                        <Transition name="bnfx" mode="out-in">
                            <img
                                :key="bannerImages(b)[getCurrent(unwrap(b).id)]?.id"
                                class="w-full h-full object-cover"
                                :src="bannerImages(b)[getCurrent(unwrap(b).id)]?.src"
                                :alt="bannerImages(b)[getCurrent(unwrap(b).id)]?.alt"
                                :title="bannerImages(b)[getCurrent(unwrap(b).id)]?.title"
                                loading="lazy"
                            />
                        </Transition>
                    </a>

                    <Transition v-else name="bnfx" mode="out-in">
                        <img
                            :key="bannerImages(b)[getCurrent(unwrap(b).id)]?.id"
                            class="w-full h-full object-cover"
                            :src="bannerImages(b)[getCurrent(unwrap(b).id)]?.src"
                            :alt="bannerImages(b)[getCurrent(unwrap(b).id)]?.alt"
                            :title="bannerImages(b)[getCurrent(unwrap(b).id)]?.title"
                            loading="lazy"
                        />
                    </Transition>

                    <!-- dots -->
                    <div v-if="bannerImages(b).length > 1"
                         class="absolute left-0 right-0 bottom-0 px-3 pb-2">
                        <div class="flex items-center justify-center gap-1.5">
                            <button
                                v-for="(img, idx) in bannerImages(b)"
                                :key="img.id"
                                type="button"
                                class="h-2 w-2 rounded-full transition-all"
                                :class="idx === getCurrent(unwrap(b).id)
                                  ? 'bg-orange-400 shadow ring-1 ring-black/40'
                                  : 'bg-white/60 hover:bg-orange-400/80'"
                                @click.prevent.stop="setCurrent(unwrap(b).id, idx, bannerImages(b).length)"
                                :aria-label="`banner image ${idx + 1}`"
                            />
                        </div>
                    </div>
                </div>

                <!-- Title + optional link -->
                <div class="p-3">
                    <div
                        v-if="bannerTitle(b)"
                        class="text-center font-semibold
                                text-sm leading-snug"
                    >
                        <a
                            v-if="bannerLink(b)"
                            :href="bannerLink(b)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:underline transition
                                   text-slate-900/85
                                   dark:text-slate-100/85
                                   hover:text-indigo-700
                                   dark:hover:text-indigo-300"
                        >
                            {{ bannerTitle(b) }}
                        </a>

                        <span
                            v-else
                            class="text-slate-900/85
                                    dark:text-slate-100/85"
                        >
                            {{ bannerTitle(b) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Спокойный fade: без движения */
.bnfx-enter-active {
    transition: opacity 520ms ease, filter 520ms ease;
    will-change: opacity, filter;
}
.bnfx-leave-active {
    transition: opacity 280ms ease, filter 280ms ease;
    will-change: opacity, filter;
}

.bnfx-enter-from { opacity: 0; filter: blur(6px); }
.bnfx-enter-to   { opacity: 1; filter: blur(0); }

.bnfx-leave-from { opacity: 1; filter: blur(0); }
.bnfx-leave-to   { opacity: 0; filter: blur(5px); }
</style>
