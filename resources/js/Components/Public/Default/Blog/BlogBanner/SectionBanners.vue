<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'
import { unwrap } from '@/composables/useUnwrap.js'

const { t } = useI18n()

const props = defineProps({
    banners: { type: [Array, Object], default: () => [] },

    // автосмена (мс)
    intervalMs: { type: Number, default: 4500 },

    // пауза при наведении
    pauseOnHover: { type: Boolean, default: true },

    // пауза если вкладка скрыта
    pauseOnHidden: { type: Boolean, default: true }
})

const { appUrl } = usePage().props

const unwrapList = (v) => v?.data ?? v ?? []

/** список баннеров без секций */
const list = computed(() => {
    const banners = unwrapList(props.banners)
    return Array.isArray(banners) ? banners : []
})

/** link может быть пустой => ссылки не должно быть */
const hasLink = (banner) => {
    const link = unwrap(banner)?.link
    return typeof link === 'string' && link.trim().length > 0
}

/** helper: storage url */
const getImgSrc = (imgPath) => {
    if (!imgPath) return ''
    const base = appUrl?.endsWith('/') ? appUrl.slice(0, -1) : (appUrl || '')
    const path = imgPath.startsWith('/') ? imgPath.slice(1) : imgPath
    return `${base}/storage/${path}`
}

const normalizeSrc = (raw) => {
    if (!raw) return ''
    if (/^https?:\/\//i.test(raw)) return raw
    if (raw.startsWith('/')) return raw
    return getImgSrc(raw)
}

/** нормализуем images баннера (с сортировкой по order) */
const normalizeBannerImages = (banner) => {
    const b = unwrap(banner)
    const imgsRaw = Array.isArray(b?.images) ? b.images : (b?.images?.data ?? [])

    return (Array.isArray(imgsRaw) ? imgsRaw : [])
        .slice()
        .sort((a, c) => Number(a?.order ?? 0) - Number(c?.order ?? 0))
        .map((img, idx) => {
            const raw =
                img?.image_url ||
                img?.url ||
                img?.src ||
                img?.path ||
                img?.image ||
                null

            const src = normalizeSrc(raw)

            return {
                id: img?.id ?? `${raw}-${img?.order ?? idx}`,
                src,
                alt: img?.alt ?? '',
                title: img?.title ?? img?.alt ?? '',
                order: Number(img?.order ?? 0)
            }
        })
        .filter(img => !!img.src)
}

/** карта изображений */
const imagesMap = computed(() => {
    const map = {}
    for (const b of list.value) {
        const id = unwrap(b)?.id
        if (!id) continue
        map[id] = normalizeBannerImages(b)
    }
    return map
})

const getImages = (banner) => {
    const id = unwrap(banner)?.id
    return (id && imagesMap.value[id]) ? imagesMap.value[id] : []
}

/** ===== Автослайдер: индекс для каждого баннера ===== */
const currentByBanner = ref({})
const hoveredByBanner = ref({})
let timer = null

const getCurrent = (bannerId) => Number(currentByBanner.value?.[bannerId] ?? 0)

const setCurrent = (bannerId, idx, total) => {
    const n = Number(total) || 0
    if (n <= 1) {
        currentByBanner.value = { ...currentByBanner.value, [bannerId]: 0 }
        return
    }
    const i = Number(idx)
    const safe = Number.isFinite(i) ? Math.min(Math.max(0, i), n - 1) : 0
    currentByBanner.value = { ...currentByBanner.value, [bannerId]: safe }
}

const canRun = () => {
    return !(props.pauseOnHidden && typeof document !== 'undefined' && document.hidden)
}

const tick = () => {
    if (!canRun()) return

    const nextState = { ...currentByBanner.value }

    for (const b of list.value) {
        const id = unwrap(b)?.id
        if (!id) continue

        const imgs = getImages(b)
        if (imgs.length <= 1) continue

        if (props.pauseOnHover && hoveredByBanner.value?.[id]) continue

        const cur = Number(nextState[id] ?? 0)
        nextState[id] = (cur + 1) % imgs.length
    }

    currentByBanner.value = nextState
}

const stop = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

const start = () => {
    stop()

    const hasAnySlider = list.value.some((b) => getImages(b).length > 1)
    if (!hasAnySlider) return

    timer = setInterval(tick, Math.max(1500, Number(props.intervalMs) || 4500))
}

const onVisibilityChange = () => start()

onMounted(() => {
    const init = {}
    for (const b of list.value) {
        const id = unwrap(b)?.id
        if (id) init[id] = 0
    }
    currentByBanner.value = init

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
    () => [list.value.length, props.intervalMs],
    () => start()
)
</script>

<template>
    <div v-if="list.length" class="mt-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div
                v-for="b in list"
                :key="unwrap(b).id"
                class="overflow-hidden rounded-md
                       border border-gray-300 dark:border-gray-700
                       bg-slate-100 dark:bg-slate-900
                       shadow-sm hover:shadow-md transition-shadow"
            >
                <!-- Изображение -->
                <div
                    v-if="getImages(b).length"
                    class="relative w-full overflow-hidden
                           bg-slate-100 dark:bg-slate-900
                           aspect-[16/9] sm:aspect-[4/3] lg:aspect-[16/10]"
                    @mouseenter="hoveredByBanner = { ...hoveredByBanner, [unwrap(b).id]: true }"
                    @mouseleave="hoveredByBanner = { ...hoveredByBanner, [unwrap(b).id]: false }"
                >
                    <a
                        v-if="hasLink(b)"
                        :href="unwrap(b).link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block w-full h-full"
                        @click.stop
                    >
                        <Transition name="bnfx" mode="out-in">
                            <img
                                :key="getImages(b)[getCurrent(unwrap(b).id)]?.id"
                                class="w-full h-full object-cover"
                                :src="getImages(b)[getCurrent(unwrap(b).id)]?.src"
                                :alt="getImages(b)[getCurrent(unwrap(b).id)]?.alt"
                                :title="getImages(b)[getCurrent(unwrap(b).id)]?.title"
                                loading="lazy"
                            />
                        </Transition>
                    </a>

                    <Transition v-else name="bnfx" mode="out-in">
                        <img
                            :key="getImages(b)[getCurrent(unwrap(b).id)]?.id"
                            class="w-full h-full object-cover"
                            :src="getImages(b)[getCurrent(unwrap(b).id)]?.src"
                            :alt="getImages(b)[getCurrent(unwrap(b).id)]?.alt"
                            :title="getImages(b)[getCurrent(unwrap(b).id)]?.title"
                            loading="lazy"
                        />
                    </Transition>

                    <!-- точки -->
                    <div
                        v-if="getImages(b).length > 1"
                        class="absolute left-0 right-0 bottom-0 px-3 pb-2"
                    >
                        <div class="flex items-center justify-center gap-1.5">
                            <button
                                v-for="(img, idx) in getImages(b)"
                                :key="img.id"
                                type="button"
                                class="h-2 w-2 rounded-full transition-all"
                                :class="idx === getCurrent(unwrap(b).id)
                                  ? 'bg-orange-400 shadow ring-1 ring-black/40'
                                  : 'bg-white/60 hover:bg-orange-400/80'"
                                @click.prevent.stop="setCurrent(unwrap(b).id, idx, getImages(b).length)"
                                :aria-label="`banner image ${idx + 1}`"
                            />
                        </div>
                    </div>
                </div>

                <!-- Контент -->
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 w-full">
                            <div
                                v-if="unwrap(b).title"
                                class="text-md font-semibold text-center leading-snug"
                            >
                                <a
                                    v-if="hasLink(b)"
                                    :href="unwrap(b).link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:underline transition
                                           text-indigo-700 dark:text-indigo-300
                                           hover:text-indigo-500 dark:hover:text-indigo-500"
                                >
                                    {{ unwrap(b).title }}
                                </a>
                                <span v-else class="text-slate-700 dark:text-slate-300">
                                    {{ unwrap(b).title }}
                                </span>
                            </div>

                            <div
                                v-if="unwrap(b).short"
                                class="mt-2 text-xs font-semibold
                                       text-slate-700/85 dark:text-slate-300/85 leading-relaxed"
                            >
                                {{ unwrap(b).short }}
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="hasLink(b)"
                        class="gap-3 flex items-center justify-between
                               mt-2 text-center text-xs break-all"
                    >
                        <div class="font-semibold text-[10px] text-sky-700 dark:text-sky-300">
                            {{ unwrap(b).link }}
                        </div>

                        <a
                            :href="unwrap(b).link"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="shrink-0 p-1 rounded-sm
                                   border border-slate-400 dark:border-slate-300
                                   hover:bg-slate-50 dark:hover:bg-slate-700 transition"
                            :title="t('openLink')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-3 h-3"
                                 height="24" width="24"
                                 viewBox="0 0 24 24">
                                <path class="fill-current text-blue-500"
                                      d="M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z"></path>
                                <path class="fill-current text-blue-500"
                                      d="M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bnfx-enter-active {
    transition: opacity 520ms ease, filter 520ms ease;
    will-change: opacity, filter;
}

.bnfx-leave-active {
    transition: opacity 280ms ease, filter 280ms ease;
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
