<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    categories: { type: Array, default: () => [] },
})
</script>

<template>
    <section v-if="categories.length"
             class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Заголовок секции -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="text-sm font-bold uppercase tracking-[0.18em]
                                text-sky-600 dark:text-sky-400">
                        Каталог оборудования
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900
                               dark:text-white sm:text-4xl lg:text-5xl">
                        Категории
                        <span class="text-sky-600 dark:text-sky-400">
                            оборудования
                        </span>
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-7
                              text-slate-600 dark:text-slate-300 sm:text-lg">
                        Выберите направление и перейдите к оборудованию, характеристикам
                        и техническим решениям для вашего проекта.
                    </p>
                </div>

                <Link
                    :href="route('public.marketCategories.index')"
                    class="group inline-flex shrink-0 items-center gap-2 text-sm
                           font-semibold text-slate-900 transition hover:text-sky-600
                           dark:text-white dark:hover:text-sky-400"
                >
                    Все категории

                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1"
                         viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path
                            d="M4 10h12M11 5l5 5-5 5"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </Link>
            </div>

            <!-- Категории -->
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:mt-12 lg:grid-cols-3">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="route('public.marketCategories.show', { url: category.url })"
                    class="group relative min-h-[260px] overflow-hidden rounded-2xl bg-slate-900"
                >
                    <!-- Изображение -->
                    <picture v-if="category.images?.length">
                        <source
                            v-if="category.images[0].webp_url"
                            :srcset="category.images[0].webp_url"
                            type="image/webp"
                        />

                        <img
                            :src="category.images[0].url"
                            :alt="category.images[0].alt || category.translation?.title || ''"
                            class="absolute inset-0 h-full w-full object-cover transition
                                   duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </picture>

                    <!-- Фон, если изображения нет -->
                    <div v-else
                         class="absolute inset-0 bg-gradient-to-br
                                from-slate-500 via-slate-400 to-slate-300
                                dark:from-slate-700 dark:via-slate-600 dark:to-slate-500">
                        <div
                            class="absolute right-4 top-4 flex h-12 w-12
                                   items-center justify-center rounded-xl border border-slate-300
                                   bg-white/50 text-slate-500 dark:border-white/10
                                   dark:bg-white/5 dark:text-slate-500">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M4 20h16M6 20V7h12v13M9 11h6M9 15h6M9 7V4h6v3"
                                    stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>

                    <!-- Затемнение -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60
                                to-slate-950/5 transition duration-300
                                group-hover:via-slate-950/50">
                    </div>

                    <!-- Контент -->
                    <div class="relative z-10 flex min-h-[260px] flex-col justify-end p-4 sm:p-5">
                        <div v-if="category.translation?.subtitle"
                             class="mb-2 text-xs font-bold uppercase
                                    tracking-[0.14em] text-sky-400">
                            {{ category.translation.subtitle }}
                        </div>

                        <h3 class="max-w-md text-xl font-bold leading-tight text-white sm:text-2xl">
                            {{ category.translation?.title }}
                        </h3>

                        <p v-if="category.translation?.short"
                           class="mt-3 line-clamp-2 max-w-md text-sm leading-6 text-slate-300">
                            {{ category.translation.short }}
                        </p>

                        <div class="mt-4 flex items-center justify-between gap-4
                                    border-t border-white/15 pt-3">
                            <span class="text-sm font-semibold text-white">
                                Смотреть оборудование
                            </span>

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center
                                         rounded-full border border-white/20 bg-white/10
                                         text-white transition duration-300
                                         group-hover:border-sky-400 group-hover:bg-sky-600">
                                <svg
                                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                                    viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path
                                        d="M4 10h12M11 5l5 5-5 5"
                                        stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>
