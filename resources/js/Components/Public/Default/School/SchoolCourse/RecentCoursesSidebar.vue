<script setup>
import {
    computed,
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
} from 'vue'

import {
    Link,
} from '@inertiajs/vue3'

import {
    unwrap,
    unwrapList,
} from '@/composables/useUnwrap.js'

const props = defineProps({
    titleKey: {
        type: String,
        default: 'courses',
    },

    courses: {
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

/* ===================== COURSES ===================== */

/**
 * Нормализованный список курсов.
 *
 * Поддерживает:
 * - обычный массив;
 * - Laravel ResourceCollection.
 */
const list = computed(() =>
    unwrapList(
        props.courses
    )
)

/**
 * Получить нормализованный объект курса.
 */
const getCourse = (course) => {
    return unwrap(course) ?? {}
}

/**
 * Заголовок курса.
 *
 * Новый Public-контракт:
 *
 * course.translation.title
 *
 * Backend уже выполняет:
 *
 * current locale
 * → fallback locale
 * → первый доступный.
 */
const getCourseTitle = (course) => {
    const item =
        getCourse(course)

    return item?.translation?.title
        || item?.slug
        || `ID: ${item?.id ?? ''}`
}

/**
 * Ссылка на Public Show курса.
 */
const getCourseLink = (course) => {
    const item =
        getCourse(course)

    if (!item?.slug) {
        return '#'
    }

    return route(
        'public.schoolCourses.show',
        {
            slug: item.slug,
        }
    )
}

/* ===================== IMAGES ===================== */

/**
 * Изображения курса.
 *
 * Новый Public Resource уже отдаёт
 * готовые URL изображений.
 *
 * Frontend больше не строит
 * /storage/... самостоятельно.
 */
const courseImages = (course) => {
    const item =
        getCourse(course)

    const images =
        Array.isArray(item?.images)
            ? item.images
            : Array.isArray(item?.images?.data)
                ? item.images.data
                : []

    return images
        .slice()
        .sort(
            (a, b) =>
                Number(
                    a?.order ?? 0
                )
                -
                Number(
                    b?.order ?? 0
                )
        )
        .map(
            (image, index) => {
                const src =
                    image?.webp_url
                    || image?.image_url
                    || image?.url
                    || image?.thumb_url
                    || ''

                return {
                    id:
                        image?.id
                        ?? `course-image-${index}`,

                    src,

                    alt:
                        image?.alt
                        || getCourseTitle(
                            course
                        ),

                    title:
                        image?.title
                        || image?.caption
                        || image?.alt
                        || getCourseTitle(
                            course
                        ),

                    order:
                        Number(
                            image?.order ?? 0
                        ),
                }
            }
        )
        .filter(
            image =>
                Boolean(
                    image.src
                )
        )
}

/* ===================== SLIDER STATE ===================== */

/**
 * Текущий индекс изображения
 * для каждого курса.
 */
const currentByCourse =
    ref({})

/**
 * Состояние hover
 * для каждого курса.
 */
const hoveredByCourse =
    ref({})

let timer = null

/**
 * Получить текущий индекс
 * изображения курса.
 */
const getCurrent = (
    courseId
) => {
    return Number(
        currentByCourse.value?.[
            courseId
            ]
        ?? 0
    )
}

/**
 * Установить текущий индекс.
 */
const setCurrent = (
    courseId,
    index,
    total
) => {
    const count =
        Number(total)
        || 0

    if (count <= 1) {
        currentByCourse.value = {
            ...currentByCourse.value,

            [courseId]: 0,
        }

        return
    }

    const value =
        Number(index)

    const safeIndex =
        Number.isFinite(value)
            ? Math.min(
                Math.max(
                    0,
                    value
                ),
                count - 1
            )
            : 0

    currentByCourse.value = {
        ...currentByCourse.value,

        [courseId]:
        safeIndex,
    }
}

/* ===================== AUTOPLAY ===================== */

/**
 * Можно ли сейчас
 * выполнять autoplay.
 */
const canRun = () => {
    if (
        props.pauseOnHidden
        && typeof document !== 'undefined'
        && document.hidden
    ) {
        return false
    }

    return true
}

/**
 * Следующий кадр
 * для всех активных слайдеров.
 */
const tick = () => {
    if (!canRun()) {
        return
    }

    const nextState = {
        ...currentByCourse.value,
    }

    for (
        const course
        of list.value
        ) {
        const item =
            getCourse(course)

        const id =
            item?.id

        if (!id) {
            continue
        }

        const images =
            courseImages(
                course
            )

        if (
            images.length <= 1
        ) {
            continue
        }

        if (
            props.pauseOnHover
            && hoveredByCourse.value?.[
                id
                ]
        ) {
            continue
        }

        const current =
            Number(
                nextState[id]
                ?? 0
            )

        nextState[id] =
            (
                current + 1
            )
            % images.length
    }

    currentByCourse.value =
        nextState
}

/**
 * Остановить autoplay.
 */
const stop = () => {
    if (!timer) {
        return
    }

    clearInterval(
        timer
    )

    timer = null
}

/**
 * Запустить autoplay.
 */
const start = () => {
    stop()

    const hasAnySlider =
        list.value.some(
            course =>
                courseImages(
                    course
                ).length > 1
        )

    if (!hasAnySlider) {
        return
    }

    timer = setInterval(
        tick,
        Math.max(
            1500,
            Number(
                props.intervalMs
            )
            || 4200
        )
    )
}

/**
 * Обработка изменения
 * видимости вкладки.
 */
const onVisibilityChange = () => {
    start()
}

/**
 * Сбросить состояния
 * текущих изображений.
 */
const resetCurrentState = () => {
    const state = {}

    for (
        const course
        of list.value
        ) {
        const id =
            getCourse(
                course
            )?.id

        if (id) {
            state[id] = 0
        }
    }

    currentByCourse.value =
        state
}

/* ===================== LIFECYCLE ===================== */

onMounted(() => {
    resetCurrentState()

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

/**
 * При изменении списка курсов
 * или интервала перезапускаем
 * локальное состояние слайдеров.
 */
watch(
    () => [
        list.value.length,
        props.intervalMs,
    ],
    () => {
        resetCurrentState()

        start()
    }
)
</script>

<template>
    <div
        v-if="list.length"
        class="w-full"
    >
        <div
            v-for="course in list"
            :key="getCourse(course).id"
        >
            <div class="mb-4">
                <Link
                    :href="
                        getCourseLink(
                            course
                        )
                    "
                    class="flex gap-2"
                >
                    <!-- Images -->
                    <div
                        v-if="
                            courseImages(
                                course
                            ).length > 0
                        "
                        class="
                            post-image
                            relative
                            overflow-hidden
                            rounded-md
                            bg-slate-100
                            dark:bg-slate-900
                            w-auto
                            h-[64px]
                            shrink-0
                        "
                        @mouseenter="
                            hoveredByCourse = {
                                ...hoveredByCourse,

                                [getCourse(course).id]:
                                    true,
                            }
                        "
                        @mouseleave="
                            hoveredByCourse = {
                                ...hoveredByCourse,

                                [getCourse(course).id]:
                                    false,
                            }
                        "
                    >
                        <Transition
                            name="imgfx"
                            mode="out-in"
                        >
                            <img
                                :key="
                                    courseImages(
                                        course
                                    )[
                                        getCurrent(
                                            getCourse(
                                                course
                                            ).id
                                        )
                                    ]?.id
                                "
                                class="
                                    w-full
                                    h-full
                                    object-cover
                                "
                                :src="
                                    courseImages(
                                        course
                                    )[
                                        getCurrent(
                                            getCourse(
                                                course
                                            ).id
                                        )
                                    ]?.src
                                "
                                :alt="
                                    courseImages(
                                        course
                                    )[
                                        getCurrent(
                                            getCourse(
                                                course
                                            ).id
                                        )
                                    ]?.alt
                                "
                                :title="
                                    courseImages(
                                        course
                                    )[
                                        getCurrent(
                                            getCourse(
                                                course
                                            ).id
                                        )
                                    ]?.title
                                "
                                loading="lazy"
                            />
                        </Transition>

                        <!-- Slider dots -->
                        <div
                            v-if="
                                courseImages(
                                    course
                                ).length > 1
                            "
                            class="
                                absolute
                                left-0
                                right-0
                                bottom-0
                                px-1
                                pb-1
                            "
                        >
                            <div
                                class="
                                    flex
                                    items-center
                                    justify-center
                                    gap-1
                                "
                            >
                                <button
                                    v-for="(
                                        image,
                                        index
                                    ) in courseImages(
                                        course
                                    )"
                                    :key="image.id"
                                    type="button"
                                    class="
                                        h-1.5
                                        w-1.5
                                        rounded-full
                                        transition-all
                                    "
                                    :class="
                                        index
                                        ===
                                        getCurrent(
                                            getCourse(
                                                course
                                            ).id
                                        )
                                            ? 'bg-orange-400 shadow ring-1 ring-black/40'
                                            : 'bg-white/60 hover:bg-orange-400/80'
                                    "
                                    @click.prevent.stop="
                                        setCurrent(
                                            getCourse(
                                                course
                                            ).id,
                                            index,
                                            courseImages(
                                                course
                                            ).length
                                        )
                                    "
                                    :aria-label="
                                        `image ${index + 1}`
                                    "
                                    :title="
                                        image.title
                                    "
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="title">
                        <span
                            class="
                                font-semibold
                                text-xs
                                text-gray-700
                                dark:text-gray-300
                                hover:text-indigo-600
                            "
                        >
                            {{
                                getCourseTitle(
                                    course
                                )
                            }}
                        </span>
                    </h3>
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.imgfx-enter-active {
    transition:
        opacity 520ms ease,
        filter 520ms ease;

    will-change:
        opacity,
        filter;
}

.imgfx-leave-active {
    transition:
        opacity 280ms ease,
        filter 280ms ease;

    will-change:
        opacity,
        filter;
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
