<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
import LocaleSelectOption from '@/Components/Admin/Select/LocaleSelectOption.vue'

const page = usePage()
const { t, locale } = useI18n()

/** доступные локали */
const availableLocales = computed(() => {
    return Array.isArray(page.props?.availableLocales)
        ? page.props.availableLocales.map(item => String(item).toLowerCase())
        : ['ru', 'en', 'kk']
})

/** количество рубрик на модерации */
const rubricsUnderModerationCount = computed(() => {
    return page.props?.admin?.rubrics_under_moderation_count ?? 0
})

/** количество статей на модерации */
const articlesUnderModerationCount = computed(() => {
    return page.props?.admin?.articles_under_moderation_count ?? 0
})

/** количество тегов на модерации */
const tagsUnderModerationCount = computed(() => {
    return page.props?.admin?.tags_under_moderation_count ?? 0
})

/** количество баннеров на модерации */
const bannersUnderModerationCount = computed(() => {
    return page.props?.admin?.banners_under_moderation_count ?? 0
})

/** количество видео на модерации */
const videosUnderModerationCount = computed(() => {
    return page.props?.admin?.videos_under_moderation_count ?? 0
})

/** количество видео на модерации */
const commentsUnderModerationCount = computed(() => {
    return page.props?.admin?.comments_under_moderation_count ?? 0
})

/** текущая локаль */
const selectedLocale = ref(locale.value)

watch(
    () => locale.value,
    (newLocale) => {
        if (selectedLocale.value !== newLocale) {
            selectedLocale.value = newLocale
        }
    }
)

/** переключение локали */
watch(selectedLocale, (newLocale) => {
    const targetLocale = String(newLocale || '').toLowerCase()
    const currentLocale = String(locale.value || '').toLowerCase()

    if (!targetLocale || targetLocale === currentLocale) {
        return
    }

    if (!availableLocales.value.includes(targetLocale)) {
        return
    }

    locale.value = targetLocale

    const pathSegments = window.location.pathname
        .split('/')
        .filter(Boolean)

    if (pathSegments.length > 0 && availableLocales.value.includes(pathSegments[0].toLowerCase())) {
        pathSegments[0] = targetLocale
    } else {
        pathSegments.unshift(targetLocale)
    }

    const newPath = `/${pathSegments.join('/')}${window.location.search}`

    Inertia.visit(newPath, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    })
})

/** очистка кеша сайта */
const clearing = ref(false)

const buildBothSnapshots = async () => {
    await axios.post('/admin/settings/snapshots/build-public')
    await axios.post('/admin/settings/snapshots/build-admin')
}

/** комбинированная кнопка: снапшоты -> очистка кеша */
const clearCache = async () => {
    if (clearing.value) return
    clearing.value = true

    try {
        await buildBothSnapshots()
        await axios.post('/admin/cache/clear')
        window.location.reload()
    } catch (error) {
        console.error('Ошибка при создании снапшотов/очистке кэша:', error)
    } finally {
        clearing.value = false
    }
}
</script>

<template>
    <footer
        class="sticky px-3 py-1 bottom-0 bg-gradient-to-b
               from-slate-100 to-slate-300 dark:bg-gradient-to-b
               dark:from-slate-700 dark:to-slate-900
               border-t border-slate-200 dark:border-slate-700 z-20"
    >
        <div class="flex items-center justify-center sm:justify-between flex-wrap">
            <!-- виджеты модерации -->
            <div class="space-x-2">
                <Link v-if="rubricsUnderModerationCount > 0"
                      :href="route('admin.rubrics.index')"
                      class="relative inline-flex"
                >
                    <svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24">
                        <circle class="fill-current text-cyan-600" cx="16" cy="8" r="8"></circle>
                        <circle class="fill-current text-cyan-400" cx="8" cy="16" r="8"></circle>
                    </svg>
                    <span
                        class="absolute -top-1.5 -right-1.5
                               min-w-[16px] h-[16px]
                               pt-0.5 px-0.5 flex items-center justify-center
                               text-[10px] font-bold leading-none
                               rounded-full
                               border border-red-700 dark:border-red-300
                               bg-white dark:bg-slate-800
                               text-red-700 dark:text-red-300"
                        :title="`${t('underModeration')} ${t('rubrics')}: ${rubricsUnderModerationCount}`"
                    >
                        {{ rubricsUnderModerationCount }}
                    </span>
                </Link>
                <Link v-if="articlesUnderModerationCount > 0"
                      :href="route('admin.articles.index')"
                      class="relative inline-flex"
                >
                    <svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24">
                        <path
                            class="fill-current text-cyan-500"
                            d="M21,0H3c-.552,0-1,.448-1,1V23c0,.552,.448,1,1,1H21c.552,0,1-.448,1-1V1c0-.552-.448-1-1-1Zm-3,19.5c0,.276-.224,.5-.5,.5H6.5c-.276,0-.5-.224-.5-.5v-1c0-.276,.224-.5,.5-.5h11c.276,0,.5,.224,.5,.5v1Zm0-5c0,.276-.224,.5-.5,.5H6.5c-.276,0-.5-.224-.5-.5v-1c0-.276,.224-.5,.5-.5h11c.276,0,.5,.224,.5,.5v1Zm0-5c0,.276-.224,.5-.5,.5H6.5c-.276,0-.5-.224-.5-.5V4.5c0-.276,.224-.5,.5-.5h11c.276,0,.5,.224,.5,.5v5Z"
                        ></path>
                    </svg>
                    <span
                        class="absolute -top-1.5 -right-1.5
                               min-w-[16px] h-[16px]
                               pt-0.5 px-0.5 flex items-center justify-center
                               text-[10px] font-bold leading-none
                               rounded-full
                               border border-red-700 dark:border-red-300
                               bg-white dark:bg-slate-800
                               text-red-700 dark:text-red-300"
                        :title="`${t('underModeration')} ${t('articles')}: ${articlesUnderModerationCount}`"
                    >
            {{ articlesUnderModerationCount }}
          </span>
                </Link>
                <Link v-if="tagsUnderModerationCount > 0"
                      :href="route('admin.tags.index')"
                      class="relative inline-flex"
                >
                    <svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24">
                        <path class="fill-current text-cyan-400"
                              d="M 19.152344 13.753906 C 19.152344 13.316406 18.996094 12.933594 18.683594 12.605469 L 9.644531 3.578125 C 9.324219 3.257812 8.894531 2.984375 8.355469 2.761719 C 7.816406 2.539062 7.324219 2.425781 6.878906 2.425781 L 1.617188 2.425781 C 1.179688 2.425781 0.800781 2.585938 0.480469 2.90625 C 0.160156 3.226562 0 3.605469 0 4.046875 L 0 9.304688 C 0 9.75 0.113281 10.246094 0.335938 10.785156 C 0.558594 11.324219 0.832031 11.75 1.152344 12.0625 L 10.191406 21.113281 C 10.5 21.425781 10.882812 21.582031 11.328125 21.582031 C 11.765625 21.582031 12.148438 21.425781 12.476562 21.113281 L 18.6875 14.894531 C 18.996094 14.582031 19.152344 14.203125 19.152344 13.753906 Z M 5.191406 7.617188 C 4.875 7.933594 4.492188 8.089844 4.046875 8.089844 C 3.597656 8.089844 3.21875 7.933594 2.902344 7.617188 C 2.585938 7.300781 2.425781 6.917969 2.425781 6.472656 C 2.425781 6.027344 2.585938 5.644531 2.902344 5.328125 C 3.21875 5.011719 3.597656 4.855469 4.046875 4.855469 C 4.492188 4.855469 4.875 5.011719 5.191406 5.328125 C 5.503906 5.644531 5.664062 6.027344 5.664062 6.472656 C 5.664062 6.917969 5.503906 7.300781 5.191406 7.617188 Z M 5.191406 7.617188 "
                        />
                        <path class="fill-current text-cyan-600"
                              d="M 23.539062 12.605469 L 14.5 3.578125 C 14.179688 3.257812 13.75 2.984375 13.210938 2.761719 C 12.671875 2.539062 12.179688 2.425781 11.730469 2.425781 L 8.898438 2.425781 C 9.347656 2.425781 9.839844 2.539062 10.378906 2.761719 C 10.917969 2.984375 11.347656 3.257812 11.667969 3.578125 L 20.707031 12.605469 C 21.019531 12.933594 21.175781 13.316406 21.175781 13.753906 C 21.175781 14.203125 21.019531 14.582031 20.707031 14.894531 L 14.765625 20.835938 C 15.019531 21.097656 15.242188 21.285156 15.4375 21.402344 C 15.628906 21.523438 15.878906 21.582031 16.183594 21.582031 C 16.621094 21.582031 17.003906 21.425781 17.332031 21.113281 L 23.539062 14.894531 C 23.851562 14.582031 24.007812 14.203125 24.007812 13.753906 C 24.007812 13.316406 23.851562 12.933594 23.539062 12.605469 Z M 23.539062 12.605469 "
                        />
                    </svg>
                    <span
                        class="absolute -top-1.5 -right-1.5
                               min-w-[16px] h-[16px]
                               pt-0.5 px-0.5 flex items-center justify-center
                               text-[10px] font-bold leading-none
                               rounded-full
                               border border-red-700 dark:border-red-300
                               bg-white dark:bg-slate-800
                               text-red-700 dark:text-red-300"
                        :title="`${t('underModeration')} ${t('tags')}: ${tagsUnderModerationCount}`"
                    >
                        {{ tagsUnderModerationCount }}
                    </span>
                </Link>
                <Link v-if="bannersUnderModerationCount > 0"
                      :href="route('admin.banners.index')"
                      class="relative inline-flex"
                >
                    <svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24">
                        <path class="fill-current text-cyan-600"
                              d="M23,2H1A1,1,0,0,0,0,3V21a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V3A1,1,0,0,0,23,2ZM22,4V14.3L17.759,9.35A1,1,0,0,0,17.005,9a.879.879,0,0,0-.757.342l-6.3,7.195L6.707,13.293A.988.988,0,0,0,5.955,13a1,1,0,0,0-.723.358L2,17.238V4Z"
                        />
                        <circle class="fill-current text-cyan-600" cx="9" cy="8" r="2"></circle>
                    </svg>
                    <span
                        class="absolute -top-1.5 -right-1.5
                               min-w-[16px] h-[16px]
                               pt-0.5 px-0.5 flex items-center justify-center
                               text-[10px] font-bold leading-none
                               rounded-full
                               border border-red-700 dark:border-red-300
                               bg-white dark:bg-slate-800
                               text-red-700 dark:text-red-300"
                        :title="`${t('underModeration')} ${t('banners')}: ${bannersUnderModerationCount}`"
                    >
                        {{ bannersUnderModerationCount }}
                    </span>
                </Link>
                <Link v-if="videosUnderModerationCount > 0"
                      :href="route('admin.videos.index')"
                      class="relative inline-flex"
                >
                    <svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24">
                        <path class="fill-current text-cyan-600"
                              d="M 9.601562 0 L 21.601562 0 C 22.921875 0 24 1.34375 24 3 L 24 13.5 C 24 15.15625 22.921875 16.5 21.601562 16.5 L 9.601562 16.5 C 8.277344 16.5 7.199219 15.15625 7.199219 13.5 L 7.199219 3 C 7.199219 1.34375 8.277344 0 9.601562 0 Z M 17.851562 5 C 17.679688 4.6875 17.398438 4.5 17.101562 4.5 C 16.800781 4.5 16.519531 4.6875 16.351562 5 L 14.25 8.9375 L 13.601562 7.921875 C 13.429688 7.65625 13.171875 7.5 12.898438 7.5 C 12.628906 7.5 12.367188 7.65625 12.199219 7.921875 L 9.796875 11.671875 C 9.582031 12.007812 9.539062 12.472656 9.691406 12.863281 C 9.839844 13.25 10.15625 13.5 10.5 13.5 L 20.699219 13.5 C 21.035156 13.5 21.335938 13.269531 21.496094 12.90625 C 21.652344 12.539062 21.632812 12.09375 21.449219 11.75 Z M 12.601562 4.5 C 12.601562 3.671875 12.0625 3 11.398438 3 C 10.738281 3 10.199219 3.671875 10.199219 4.5 C 10.199219 5.328125 10.738281 6 11.398438 6 C 12.0625 6 12.601562 5.328125 12.601562 4.5 Z M 2.398438 6 L 6 6 L 6 19.5 C 6 20.328125 6.535156 21 7.199219 21 L 12 21 C 12.664062 21 13.199219 20.328125 13.199219 19.5 L 13.199219 18 L 19.199219 18 L 19.199219 21 C 19.199219 22.65625 18.125 24 16.800781 24 L 2.398438 24 C 1.078125 24 0 22.65625 0 21 L 0 9 C 0 7.34375 1.078125 6 2.398438 6 Z"
                        />
                    </svg>
                    <span
                        class="absolute -top-1.5 -right-1.5
                               min-w-[16px] h-[16px]
                               pt-0.5 px-0.5 flex items-center justify-center
                               text-[10px] font-bold leading-none
                               rounded-full
                               border border-red-700 dark:border-red-300
                               bg-white dark:bg-slate-800
                               text-red-700 dark:text-red-300"
                        :title="`${t('underModeration')} ${t('videos')}: ${videosUnderModerationCount}`"
                    >
                        {{ videosUnderModerationCount }}
                    </span>
                </Link>
                <Link v-if="commentsUnderModerationCount > 0"
                      :href="route('admin.comments.index')"
                      class="relative inline-flex"
                >
                    <svg class="shrink-0 h-5 w-5"
                         viewBox="0 0 512 512">
                        <path class="fill-current text-cyan-600"
                              d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"/>
                    </svg>
                    <span
                        class="absolute -top-1.5 -right-1.5
                               min-w-[16px] h-[16px]
                               pt-0.5 px-0.5 flex items-center justify-center
                               text-[10px] font-bold leading-none
                               rounded-full
                               border border-red-700 dark:border-red-300
                               bg-white dark:bg-slate-800
                               text-red-700 dark:text-red-300"
                        :title="`${t('underModeration')} ${t('comments')}: ${commentsUnderModerationCount}`"
                    >
                        {{ commentsUnderModerationCount }}
                    </span>
                </Link>
            </div>
        </div>

        <div class="flex items-center justify-center sm:justify-between flex-wrap">
            <!-- левая сторона телега службы поддержки -->
            <div
                class="flex flex-row items-center justify-start gap-2
               text-xs sm:text-sm text-gray-500 dark:text-gray-400"
            >
                <a
                    href="https://t.me/k_a_v_www"
                    target="_blank"
                    :title="t('supportService')"
                    class="flex items-center space-x-2
                           text-blue-500 hover:text-blue-700
                           dark:text-blue-300 dark:hover:text-blue-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor"
                         viewBox="0 0 24 24"
                         class="w-5 h-5 sm:w-6 sm:h-6">
                        <path
                            d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.175 8.89l-1.4 6.63c-.105.467-.405.578-.82.36l-2.27-1.67-1.093 1.054c-.12.12-.222.222-.45.222l.168-2.39 4.35-3.923c.19-.168-.04-.263-.29-.095L8.78 11.167l-2.42-.76c-.464-.14-.474-.464.096-.684l9.452-3.65c.44-.16.82.108.66.717z"
                        />
                    </svg>
                </a>
                © {{ new Date().getFullYear() }}
                <a href="/admin"
                   target="_blank"
                   class="font-semibold text-red-400 hover:text-rose-300">
                    Pulsar CMS
                </a>
            </div>
            <!-- Генерация снапшотов, очиска кэша, смена локали -->
            <div class="flex flex-row items-center justify-end gap-1">

                <!-- Кнопка очистки кэша -->
                <button
                    type="button"
                    @click="clearCache"
                    :disabled="clearing"
                    :title="t('clearCache')"
                    class="flex items-center btn px-1 py-0.5
                           text-slate-900 dark:text-slate-100
                           rounded-sm border-2 border-slate-400
                           disabled:opacity-50"
                >
                    <svg class="w-4 h-4 fill-current text-red-400 shrink-0" viewBox="0 0 16 16">
                        <path
                            d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                        />
                    </svg>
                </button>

                <LocaleSelectOption
                    v-model="selectedLocale"
                    placement="top-end"
                />
            </div>
        </div>
    </footer>
</template>
