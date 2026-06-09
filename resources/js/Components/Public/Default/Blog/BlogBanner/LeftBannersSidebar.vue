<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { unwrap } from '@/composables/useUnwrap.js'

const props = defineProps({
    leftBanners: { type: [Array, Object], default: () => [] },
    limit: { type: Number, default: 2 },

    intervalMs: { type: Number, default: 4500 },
    pauseOnHover: { type: Boolean, default: true },
    pauseOnHidden: { type: Boolean, default: true },
})

const { appUrl } = usePage().props

const unwrapList = (v) => v?.data ?? v ?? []

const list = computed(() => unwrapList(props.leftBanners).slice(0, props.limit))

/** storage helper */
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

const bannerImages = (banner) => {
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

            return {
                id: img?.id ?? `${raw}-${img?.order ?? idx}`,
                src: normalizeSrc(raw),
                alt: img?.alt ?? '',
                title: img?.title ?? img?.alt ?? '',
                order: Number(img?.order ?? 0),
            }
        })
}

/** per-banner slider state */
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
    return !(props.pauseOnHidden && typeof document !== 'undefined' && document.hidden);

}

const tick = () => {
    if (!canRun()) return

    const nextState = { ...currentByBanner.value }

    for (const b of list.value) {
        const id = unwrap(b)?.id
        if (!id) continue

        const imgs = bannerImages(b)
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
    const hasAnySlider = list.value.some((b) => bannerImages(b).length > 1)
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
                        v-if="unwrap(b).link"
                        :href="unwrap(b).link"
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
                    <div v-if="unwrap(b).title"
                         class="text-center font-semibold text-sm leading-snug">
                        <a
                            v-if="unwrap(b).link"
                            :href="unwrap(b).link"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:underline transition
                                   text-slate-900/85 dark:text-slate-100/85
                                   hover:text-indigo-700 dark:hover:text-indigo-300"
                        >
                            {{ unwrap(b).title }}
                        </a>
                        <span v-else class="text-slate-900/85 dark:text-slate-100/85">
                            {{ unwrap(b).title }}
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
