<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    lessons: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
})

/**
 * Количество колонок карточек.
 *
 * 2 — оба сайдбара открыты.
 * 3 — свернут один.
 * 4 — свернуты оба.
 */
const gridClass = computed(() => {
    switch (props.cols) {
        case 4:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4'

        case 3:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3'

        case 2:
        default:
            return 'grid-cols-1 sm:grid-cols-2'
    }
})

/** Ссылка на урок */
const lessonLink = (lesson) => {
    return route(
        'public.schoolLessons.show',
        { slug: lesson.slug }
    )
}

/** Название урока */
const getTitle = (lesson) => {
    return lesson?.translation?.title || t('lesson')
}

/** Краткое описание */
const getShort = (lesson) => {
    return lesson?.translation?.short || ''
}

/** Тип доступа */
const getAccessType = (lesson) => {
    return lesson?.access_type || ''
}

/** Перевод типа доступа */
const translateAccessType = (value) => {
    const normalized = String(value ?? '')
        .trim()
        .toLowerCase()

    const map = {
        free: 'free',
        paid: 'paid',
        preview: 'preview',
    }

    return map[normalized]
        ? t(map[normalized])
        : value
}

/** Продолжительность */
const getDuration = (lesson) => {
    const duration = Number(
        lesson?.duration ?? 0
    )

    return Number.isFinite(duration)
        ? duration
        : 0
}

/** Средний рейтинг */
const getRating = (lesson) => {
    const rating = Number(
        lesson?.rating_avg ?? 0
    )

    return Number.isFinite(rating)
        ? rating
        : 0
}
</script>

<template>
    <div
        class="grid gap-4"
        :class="gridClass"
        itemscope
        itemtype="https://schema.org/ItemList"
    >
        <article
            v-for="(lesson, index) in lessons"
            :key="lesson.id"
            class="group flex h-full flex-col overflow-hidden
                   rounded-md border border-gray-200
                   bg-white shadow-sm transition
                   hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
            itemprop="itemListElement"
            itemscope
            itemtype="https://schema.org/LearningResource"
        >
            <meta
                itemprop="position"
                :content="String(index + 1)"
            />

            <meta
                itemprop="learningResourceType"
                content="Lesson"
            />

            <meta
                v-if="lesson.translation?.locale"
                itemprop="inLanguage"
                :content="lesson.translation.locale"
            />

            <meta
                v-if="lesson.published_at"
                itemprop="datePublished"
                :content="lesson.published_at"
            />

            <meta
                v-if="lesson.difficulty !== null && lesson.difficulty !== undefined"
                itemprop="educationalLevel"
                :content="String(lesson.difficulty)"
            />

            <meta
                v-if="getDuration(lesson) > 0"
                itemprop="timeRequired"
                :content="`PT${Math.round(getDuration(lesson))}M`"
            />
            <Link
                :href="lessonLink(lesson)"
                :aria-label="getTitle(lesson)"
                itemprop="url"
            >
                <UniversalImageSlider
                    :entity="lesson"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                   duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-center text-center">
                    <h2 itemprop="name">
                        <Link
                            :href="lessonLink(lesson)"
                            class="inline-flex items-center gap-2"
                        >
                            <span
                                class="text-base font-semibold
                                       text-slate-900/85 group-hover:opacity-75
                                       dark:text-slate-100/85
                                       dark:group-hover:opacity-75"
                            >
                                {{ getTitle(lesson) }} #{{ lesson.id }}
                            </span>
                        </Link>
                    </h2>
                </div>

                <div
                    v-if="getShort(lesson)"
                    class="mt-3 line-clamp-3 text-sm
                           text-slate-700 dark:text-slate-300"
                    itemprop="description"
                >
                    {{ getShort(lesson) }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div
                        v-if="getAccessType(lesson)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('access')"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                class="fill-current text-teal-600 dark:text-teal-300"
                                d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 116 0v3H9z"
                            />
                        </svg>
                        {{ translateAccessType(getAccessType(lesson)) }}
                    </div>

                    <div
                        v-if="getDuration(lesson) > 0"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('duration')"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 24 24">
                            <path
                                class="fill-current text-blue-700 dark:text-blue-300"
                                d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                            />
                        </svg>
                        {{ getDuration(lesson) }} {{ t('minutes') }}
                    </div>

                    <div
                        v-if="getRating(lesson) > 0"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                border border-slate-600 dark:border-slate-400"
                        :title="t('rating')"
                        itemprop="aggregateRating"
                        itemscope
                        itemtype="https://schema.org/AggregateRating"
                    >
                        <meta
                            itemprop="ratingValue"
                            :content="getRating(lesson).toFixed(1)"
                        />

                        <meta
                            itemprop="bestRating"
                            content="5"
                        />

                        <meta
                            itemprop="worstRating"
                            content="1"
                        />

                        <meta
                            v-if="lesson.rating_count > 0"
                            itemprop="ratingCount"
                            :content="String(lesson.rating_count)"
                        />

                        <svg
                            viewBox="0 0 24 24"
                            class="shrink-0 h-3 w-3"
                            aria-hidden="true"
                        >
                            <path
                                class="fill-current text-red-400 dark:text-red-300"
                                d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                            />
                        </svg>

                        {{ getRating(lesson).toFixed(1) }}
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center">
                    <EntityStats
                        :views="lesson.views || 0"
                        :likes-count="lesson.likes_count || 0"
                        :already-liked="lesson.already_liked || false"
                        route-name="public.schoolLessons.like"
                        :route-params="lesson.id"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <div class="mt-auto pt-4">
                    <Link
                        :href="lessonLink(lesson)"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-2 btn-default"
                        :aria-label="`${t('readMore')}: ${getTitle(lesson)}`"
                    >
                        <span class="text-sm font-semibold">
                            {{ t('readMore') }}
                        </span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </Link>
                </div>
            </div>
        </article>
    </div>
</template>
