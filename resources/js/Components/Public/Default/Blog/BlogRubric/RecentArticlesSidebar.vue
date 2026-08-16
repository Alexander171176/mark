<script setup>
import { Link } from '@inertiajs/vue3'
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
    articles: {
        type: [Array, Object],
        default: () => [],
    },

    intervalMs: {
        type: Number,
        default: 4200,
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

/** Нормализованный список статей */
const list = computed(() =>
    unwrapList(props.articles)
)

/**
 * Заголовок статьи
 * из Public BlogArticleSharedResource.
 */
const getArticleTitle = (article) => {
    const item = unwrap(article)

    return item?.translation?.title || ''
}

/** URL страницы статьи */
const getArticleUrl = (article) => {
    const item = unwrap(article)

    return `/blog/articles/${item?.url || ''}`
}

/**
 * Изображения статьи.
 *
 * Public Resource уже отдаёт готовые
 * URL изображений через BlogArticleImageResource.
 */
const articleImages = (article) => {
    const item = unwrap(article)

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
                    || getArticleTitle(article),

                title:
                    image?.title
                    || image?.alt
                    || getArticleTitle(article),

                order: Number(
                    image?.order ?? 0
                ),
            }
        })
        .filter(image => Boolean(image.src))
}

const currentByArticle = ref({})
const hoveredByArticle = ref({})

let timer = null

/** Текущий индекс изображения статьи */
const getCurrent = (articleId) =>
    Number(
        currentByArticle.value?.[articleId] ?? 0
    )

/** Установить текущее изображение */
const setCurrent = (
    articleId,
    index,
    total
) => {
    const count = Number(total) || 0

    if (count <= 1) {
        currentByArticle.value = {
            ...currentByArticle.value,
            [articleId]: 0,
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

    currentByArticle.value = {
        ...currentByArticle.value,
        [articleId]: safeIndex,
    }
}

/** Можно ли запускать автопрокрутку */
const canRun = () => {
    return !(
        props.pauseOnHidden
        && typeof document !== 'undefined'
        && document.hidden
    )
}

/** Следующее изображение */
const tick = () => {
    if (!canRun()) {
        return
    }

    const nextState = {
        ...currentByArticle.value,
    }

    for (const articleItem of list.value) {
        const article = unwrap(articleItem)
        const id = article?.id

        if (!id) {
            continue
        }

        const images =
            articleImages(articleItem)

        if (images.length <= 1) {
            continue
        }

        if (
            props.pauseOnHover
            && hoveredByArticle.value?.[id]
        ) {
            continue
        }

        const current =
            Number(nextState[id] ?? 0)

        nextState[id] =
            (current + 1) % images.length
    }

    currentByArticle.value =
        nextState
}

/** Остановить таймер */
const stop = () => {
    if (!timer) {
        return
    }

    clearInterval(timer)
    timer = null
}

/** Запустить таймер */
const start = () => {
    stop()

    const hasAnySlider =
        list.value.some(
            article =>
                articleImages(article).length > 1
        )

    if (!hasAnySlider) {
        return
    }

    timer = setInterval(
        tick,
        Math.max(
            1500,
            Number(props.intervalMs) || 4200
        )
    )
}

/** Изменение видимости вкладки */
const onVisibilityChange = () => {
    start()
}

onMounted(() => {
    const initialState = {}

    for (const article of list.value) {
        const id = unwrap(article)?.id

        if (id) {
            initialState[id] = 0
        }
    }

    currentByArticle.value =
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
        const initialState = {}

        for (const article of list.value) {
            const id = unwrap(article)?.id

            if (id) {
                initialState[id] = 0
            }
        }

        currentByArticle.value =
            initialState

        start()
    }
)
</script>

<template>
    <!-- Блок статей -->
    <div v-if="list.length !== 0">
        <div
            v-for="article in list"
            :key="unwrap(article).id"
        >
            <div class="mb-4">
                <Link
                    :href="getArticleUrl(article)"
                    class="flex gap-2"
                >
                    <!-- IMAGE + DOTS -->
                    <div
                        v-if="articleImages(article).length > 0"
                        class="post-image relative overflow-hidden rounded-md
                               bg-slate-100 dark:bg-slate-900
                               w-auto h-[64px] shrink-0"
                        @mouseenter="
                            hoveredByArticle = {
                                ...hoveredByArticle,
                                [unwrap(article).id]: true
                            }
                        "
                        @mouseleave="
                            hoveredByArticle = {
                                ...hoveredByArticle,
                                [unwrap(article).id]: false
                            }
                        "
                    >
                        <Transition
                            name="imgfx"
                            mode="out-in"
                        >
                            <img
                                :key="
                                    articleImages(article)[
                                        getCurrent(
                                            unwrap(article).id
                                        )
                                    ]?.id
                                "
                                class="w-full h-full object-cover"
                                :src="
                                    articleImages(article)[
                                        getCurrent(
                                            unwrap(article).id
                                        )
                                    ]?.src
                                "
                                :alt="
                                    articleImages(article)[
                                        getCurrent(
                                            unwrap(article).id
                                        )
                                    ]?.alt
                                "
                                :title="
                                    articleImages(article)[
                                        getCurrent(
                                            unwrap(article).id
                                        )
                                    ]?.title
                                "
                                loading="lazy"
                            />
                        </Transition>

                        <!-- DOTS -->
                        <div
                            v-if="
                                articleImages(article).length > 1
                            "
                            class="absolute left-0 right-0 bottom-0 px-1 pb-1"
                        >
                            <div
                                class="flex items-center justify-center gap-1"
                            >
                                <button
                                    v-for="(image, index) in articleImages(article)"
                                    :key="image.id"
                                    type="button"
                                    class="h-1.5 w-1.5 rounded-full transition-all"
                                    :class="
                                        index === getCurrent(
                                            unwrap(article).id
                                        )
                                            ? 'bg-orange-400 shadow ring-1 ring-black/40'
                                            : 'bg-white/60 hover:bg-orange-400/80'
                                    "
                                    :aria-label="`image ${index + 1}`"
                                    :title="image.title"
                                    @click.prevent.stop="
                                        setCurrent(
                                            unwrap(article).id,
                                            index,
                                            articleImages(article).length
                                        )
                                    "
                                />
                            </div>
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <h3 class="title">
                        <span
                            class="font-semibold text-xs
                                   text-gray-700 dark:text-gray-300
                                   hover:text-indigo-600"
                        >
                            {{ getArticleTitle(article) }}
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
