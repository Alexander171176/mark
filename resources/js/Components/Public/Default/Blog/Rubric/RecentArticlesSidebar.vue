<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { unwrap, unwrapList } from '@/composables/useUnwrap.js'

const props = defineProps({
    titleKey: { type: String, default: 'articles' },
    articles: { type: [Array, Object], default: () => [] },
    intervalMs: { type: Number, default: 4200 },
    pauseOnHover: { type: Boolean, default: true },
    pauseOnHidden: { type: Boolean, default: true },
})

const { appUrl } = usePage().props
const list = computed(() => unwrapList(props.articles))

const getImgSrc = (imgPath) => {
    if (!imgPath) return ''

    const base = appUrl?.endsWith('/')
        ? appUrl.slice(0, -1)
        : (appUrl || '')

    const path = imgPath.startsWith('/')
        ? imgPath.slice(1)
        : imgPath

    return `${base}/storage/${path}`
}

const normalizeSrc = (raw) => {
    if (!raw) return ''
    if (/^https?:\/\//i.test(raw)) return raw
    if (raw.startsWith('/')) return raw
    return getImgSrc(raw)
}

const articleImages = (article) => {
    const a = unwrap(article)

    const imgsRaw = Array.isArray(a?.images)
        ? a.images
        : (a?.images?.data ?? [])

    return (Array.isArray(imgsRaw) ? imgsRaw : [])
        .slice()
        .sort((x, y) => Number(x?.order ?? 0) - Number(y?.order ?? 0))
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
                id: img?.id ?? `${raw ?? 'img'}-${img?.order ?? idx}`,
                src,
                alt: img?.alt ?? '',
                title: img?.title ?? img?.alt ?? '',
                order: Number(img?.order ?? 0),
            }
        })
        .filter(img => !!img.src)
}

const currentByArticle = ref({})
const hoveredByArticle = ref({})
let timer = null

const getCurrent = (articleId) => Number(currentByArticle.value?.[articleId] ?? 0)

const setCurrent = (articleId, idx, total) => {
    const n = Number(total) || 0

    if (n <= 1) {
        currentByArticle.value = {
            ...currentByArticle.value,
            [articleId]: 0,
        }
        return
    }

    const i = Number(idx)
    const safe = Number.isFinite(i)
        ? Math.min(Math.max(0, i), n - 1)
        : 0

    currentByArticle.value = {
        ...currentByArticle.value,
        [articleId]: safe,
    }
}

const canRun = () => {
    return !(props.pauseOnHidden && typeof document !== 'undefined' && document.hidden)
}

const tick = () => {
    if (!canRun()) return

    const nextState = { ...currentByArticle.value }

    for (const a of list.value) {
        const article = unwrap(a)
        const id = article?.id

        if (!id) continue

        const imgs = articleImages(a)

        if (imgs.length <= 1) continue
        if (props.pauseOnHover && hoveredByArticle.value?.[id]) continue

        const cur = Number(nextState[id] ?? 0)
        nextState[id] = (cur + 1) % imgs.length
    }

    currentByArticle.value = nextState
}

const stop = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

const start = () => {
    stop()

    const hasAnySlider = list.value.some((a) => articleImages(a).length > 1)
    if (!hasAnySlider) return

    timer = setInterval(tick, Math.max(1500, Number(props.intervalMs) || 4200))
}

const onVisibilityChange = () => {
    start()
}

onMounted(() => {
    const init = {}

    for (const a of list.value) {
        const id = unwrap(a)?.id
        if (id) init[id] = 0
    }

    currentByArticle.value = init

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
    () => {
        const init = {}

        for (const a of list.value) {
            const id = unwrap(a)?.id
            if (id) init[id] = 0
        }

        currentByArticle.value = init
        start()
    }
)
</script>

<template>
    <!-- Блок статей -->
    <div v-if="list.length !== 0">
        <div v-for="a in list" :key="unwrap(a).id">
            <div class="mb-4">
                <Link
                    :href="`/blog/articles/${unwrap(a).url}`"
                    class="flex gap-2"
                >
                    <!-- IMAGE + DOTS -->
                    <div
                        v-if="articleImages(a).length > 0"
                        class="post-image relative overflow-hidden rounded-md
                               bg-slate-100 dark:bg-slate-900
                               w-auto h-[64px] shrink-0"
                        @mouseenter="hoveredByArticle = { ...hoveredByArticle, [unwrap(a).id]: true }"
                        @mouseleave="hoveredByArticle = { ...hoveredByArticle, [unwrap(a).id]: false }"
                    >
                        <Transition name="imgfx" mode="out-in">
                            <img
                                :key="articleImages(a)[getCurrent(unwrap(a).id)]?.id"
                                class="w-full h-full object-cover"
                                :src="articleImages(a)[getCurrent(unwrap(a).id)]?.src"
                                :alt="articleImages(a)[getCurrent(unwrap(a).id)]?.alt"
                                :title="articleImages(a)[getCurrent(unwrap(a).id)]?.title"
                                loading="lazy"
                            />
                        </Transition>

                        <!-- DOTS -->
                        <div
                            v-if="articleImages(a).length > 1"
                            class="absolute left-0 right-0 bottom-0 px-1 pb-1"
                        >
                            <div class="flex items-center justify-center gap-1">
                                <button
                                    v-for="(img, idx) in articleImages(a)"
                                    :key="img.id"
                                    type="button"
                                    class="h-1.5 w-1.5 rounded-full transition-all"
                                    :class="idx === getCurrent(unwrap(a).id)
                                        ? 'bg-orange-400 shadow ring-1 ring-black/40'
                                        : 'bg-white/60 hover:bg-orange-400/80'"
                                    @click.prevent.stop="setCurrent(unwrap(a).id, idx, articleImages(a).length)"
                                    :aria-label="`image ${idx + 1}`"
                                    :title="img.title"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <h3 class="title">
                        <span class="font-semibold text-xs
                                     text-gray-700 dark:text-gray-300 hover:text-indigo-600">
                            {{ unwrap(a).title }}
                        </span>
                    </h3>
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.imgfx-enter-active {
    transition: opacity 520ms ease, filter 520ms ease;
    will-change: opacity, filter;
}

.imgfx-leave-active {
    transition: opacity 280ms ease, filter 280ms ease;
    will-change: opacity, filter;
}

.imgfx-enter-from {
    opacity: 0;
    filter: blur(6px);
}

.imgfx-enter-to {
    opacity: 1;
    filter: blur(0);
}

.imgfx-leave-from {
    opacity: 1;
    filter: blur(0);
}

.imgfx-leave-to {
    opacity: 0;
    filter: blur(5px);
}
</style>
