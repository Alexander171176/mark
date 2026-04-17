<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import ArticleStats from '@/Components/Public/Default/Blog/Article/ArticleStats.vue'

const { t } = useI18n()

const props = defineProps({
    articles: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
    intervalMs: { type: Number, default: 4200 },
    pauseOnHover: { type: Boolean, default: true },
    pauseOnHidden: { type: Boolean, default: true }
})

const gridClass = computed(() => {
    return props.cols === 3
        ? 'grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3'
        : 'grid grid-cols-1 gap-4 sm:grid-cols-2'
})

const normalizedArticles = computed(() => {
    return Array.isArray(props.articles) ? props.articles : []
})

</script>

<template>
    <div :class="gridClass">
        <div
            v-for="article in normalizedArticles"
            :key="article.id"
            class="group flex h-full flex-col overflow-hidden rounded-md
                   border border-gray-200 bg-white shadow-sm
                   transition hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <Link :href="route('public.articles.show', article.url)">
                <UniversalImageSlider
                    :entity="article"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-center text-center">
                    <Link
                        :href="route('public.articles.show', article.url)"
                        class="inline-flex items-center gap-1"
                    >
                        <span
                            class="text-lg font-semibold text-slate-900/85 text-center
                                   group-hover:opacity-75 dark:text-slate-100/85
                                   dark:group-hover:opacity-75"
                        >
                            {{ article.title }}
                        </span>
                    </Link>
                </div>

                <div
                    v-if="article.short"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ article.short }}
                </div>

                <div v-if="article?.owner" class="mt-4 flex items-center justify-center gap-2">
                    <img
                        v-if="article.owner?.profile_photo_url"
                        :src="article.owner.profile_photo_url"
                        :alt="article.owner.name"
                        loading="lazy"
                        class="h-6 w-6 rounded-full object-cover
                               ring-1 ring-gray-200 dark:ring-gray-700"
                    />
                    <div class="min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85">
                        {{ article.owner?.name }}
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center">
                    <ArticleStats
                        :views="article.views || 0"
                        :likes-count="article.likes_count || 0"
                        :already-liked="article.already_liked || false"
                        route-name="articles.like"
                        :route-params="{ article: article.id }"
                        :show-likes-button="true"
                        compact
                    />
                </div>
                <div class="mt-auto pt-4">
                    <Link
                        :href="route('public.articles.show', article.url)"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-2 btn-default"
                    >
                        <span class="text-sm font-semibold">{{ t('readMore') }}</span>
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
        </div>
    </div>
</template>
