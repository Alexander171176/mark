<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ArticleGrid from '@/Components/Public/Default/Blog/BlogArticle/ArticleGrid.vue'

const props = defineProps({
    articles: { type: [Array, Object], default: () => [] },
})

const articlesData = computed(() => {
    if (Array.isArray(props.articles)) return props.articles
    if (Array.isArray(props.articles?.data)) return props.articles.data
    return []
})
</script>

<template>
    <section v-if="articlesData.length"
             class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Заголовок секции -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="text-sm font-bold uppercase tracking-[0.18em]
                                text-sky-600 dark:text-sky-400">
                        Экспертные материалы
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight
                               text-slate-900 dark:text-white sm:text-4xl lg:text-5xl">
                        Полезно об оборудовании
                        <span class="text-sky-600 dark:text-sky-400">
                            и инженерных системах
                        </span>
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-7
                              text-slate-600 dark:text-slate-300 sm:text-lg">
                        Практические материалы о вентиляции, отоплении и кондиционировании:
                        подбор оборудования, технические решения и рекомендации.
                    </p>
                </div>

                <Link
                    :href="route('public.blogArticles.index')"
                    class="group inline-flex shrink-0 items-center gap-2 text-sm font-semibold
                           text-slate-900 transition hover:text-sky-600 dark:text-white
                           dark:hover:text-sky-400"
                >
                    Все статьи

                    <svg
                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                        viewBox="0 0 20 20" fill="none"
                         aria-hidden="true">
                        <path
                            d="M4 10h12M11 5l5 5-5 5"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </Link>
            </div>

            <!-- Статьи -->
            <div class="mt-10 lg:mt-12">
                <ArticleGrid :articles="articlesData" :cols="3" />
            </div>
        </div>
    </section>
</template>
