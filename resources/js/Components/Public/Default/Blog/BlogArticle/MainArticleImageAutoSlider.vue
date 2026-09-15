<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { unwrap } from '@/composables/useUnwrap.js'
import useArticleImage from '@/composables/useArticleImage.js'

const props = defineProps({
    article: { type: Object, required: true },

    aspectClass: {
        type: String,
        default: 'aspect-[16/9] sm:aspect-[4/3] lg:aspect-[16/10]',
    },

    intervalMs: { type: Number, default: 3500 },
    pauseOnHover: { type: Boolean, default: true },
    pauseOnHidden: { type: Boolean, default: true },
})

const { appUrl } = usePage().props
const { onImgError } = useArticleImage()

const current = ref(0)
const isHovered = ref(false)
const direction = ref(1) // +1 вперёд, -1 назад (сейчас нужен для анимации)

let timer = null

const getImgSrc = (imgPath) => {
    if (!imgPath) return ''
    const base = appUrl?.endsWith('/') ? appUrl.slice(0, -1) : (appUrl || '')
    const path = imgPath.startsWith('/') ? imgPath.slice(1) : imgPath
    return `${base}/storage/${path}`
}

const getDefaultImg = () => '/storage/article_images/default-image.png'

const hash32 = (str) => {
    let h = 0x811c9dc5
    for (let i = 0; i < str.length; i++) {
        h ^= str.charCodeAt(i)
        h = Math.imul(h, 0x01000193)
    }
    return h >>> 0
}

const hashToHsl = (seedStr) => {
    const h = hash32(seedStr)
    const hue = h % 360
    const sat = 70 + (h % 21)
    const light = 45 + ((h >>> 8) % 16)
    return `hsl(${hue} ${sat}% ${light}%)`
}

const images = computed(() => {
    const a = unwrap(props.article)
    const list = Array.isArray(a?.images) ? a.images : (a?.images?.data ?? [])

    const normalized = (Array.isArray(list) ? list : [])
        .slice()
        .sort((x, y) => Number(x?.order ?? 0) - Number(y?.order ?? 0))
        .map((img) => {
            const raw =
                img?.image_url ||
                img?.url ||
                img?.src ||
                img?.path ||
                img?.image ||
                null

            let src = ''
            if (raw && /^https?:\/\//i.test(raw)) src = raw
            else if (raw) src = getImgSrc(raw)
            else src = getDefaultImg()

            const id = img?.id ?? `${raw}-${img?.order ?? 0}`
            const seed = `${id}|${src}|${img?.order ?? 0}`

            return {
                id,
                src,
                alt: img?.alt ?? '',
                title: img?.title ?? img?.alt ?? '',
                order: Number(img?.order ?? 0),
                color: hashToHsl(seed),
            }
        })

    if (normalized.length === 0) {
        normalized.push({
            id: 'default',
            src: getDefaultImg(),
            alt: '',
            title: '',
            order: 0,
            color: hashToHsl('default'),
        })
    }

    return normalized
})

const hasMany = computed(() => images.value.length > 1)

watch(
    () => images.value.length,
    (len) => {
        if (!len) current.value = 0
        if (current.value > len - 1) current.value = 0
    }
)

const setSlide = (idx) => {
    const n = images.value.length
    if (n <= 1) return
    direction.value = idx > current.value ? 1 : -1
    const i = Number(idx)
    current.value = Number.isFinite(i) ? Math.min(Math.max(0, i), n - 1) : 0
}

const next = () => {
    const n = images.value.length
    if (n <= 1) return
    direction.value = 1
    current.value = (current.value + 1) % n
}

const stop = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

const canRun = () => {
    if (!hasMany.value) return false
    if (props.pauseOnHover && isHovered.value) return false
    if (props.pauseOnHidden && typeof document !== 'undefined' && document.hidden) return false
    return true
}

const start = () => {
    stop()
    if (!canRun()) return

    timer = setInterval(() => {
        if (canRun()) next()
    }, Math.max(1200, Number(props.intervalMs) || 3500))
}

const onVisibilityChange = () => start()

onMounted(() => {
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

watch(() => props.intervalMs, () => start())
watch(() => hasMany.value, () => start())
</script>

<template>
    <div
        class="main-img relative w-full overflow-hidden rounded-t-xl
               bg-slate-100 dark:bg-[hsl(240_33%_12%)]"
        :class="aspectClass"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        :style="{
      '--dir': direction === 1 ? '1' : '-1',
    }"
    >
        <Transition name="imgpro" mode="out-in">
            <img
                :key="images[current]?.id"
                class="main-img__img w-full h-full object-cover block"
                :src="images[current]?.src"
                :alt="images[current]?.alt"
                :title="images[current]?.title"
                loading="lazy"
                @error="onImgError"
            />
        </Transition>

        <!-- полоса блоков снизу -->
        <div v-if="hasMany" class="absolute left-0 right-0 bottom-0 px-2 pb-0.5">
            <div class="flex gap-1">
                <button
                    v-for="(img, idx) in images"
                    :key="img.id"
                    type="button"
                    @click="setSlide(idx)"
                    class="h-1.5 flex-1 rounded-sm transition-all duration-200"
                    :style="{
                        background: img.color,
                        opacity: idx === current ? 1 : 0.45,
                        transform: idx === current ? 'scaleY(1.35)' : 'scaleY(1)',
                        filter: idx === current ? 'saturate(1.1)' : 'saturate(0.9)',
                        transformOrigin: 'center',
                      }"
                    :aria-label="`image ${idx + 1}`"
                    :title="img.title"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Спокойный премиум fade: без движения */
.imgpro-enter-active {
    transition:
        opacity 900ms cubic-bezier(.25,.8,.25,1),
        filter 900ms cubic-bezier(.25,.8,.25,1);
    will-change: opacity, filter;
}

.imgpro-leave-active {
    transition:
        opacity 700ms ease,
        filter 700ms ease;
    will-change: opacity, filter;
}

/* вход */
.imgpro-enter-from {
    opacity: 0;
    filter: blur(5px);
}
.imgpro-enter-to {
    opacity: 1;
    filter: blur(0);
}

/* выход */
.imgpro-leave-from {
    opacity: 1;
    filter: blur(0);
}
.imgpro-leave-to {
    opacity: 0;
    filter: blur(4px);
}

/* защита от Vulk */
.main-img__img {
    display: block;
}
</style>
