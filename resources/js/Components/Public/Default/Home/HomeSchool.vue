<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <section class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-[1fr_0.85fr] lg:gap-16">
                <!-- Контент -->
                <div class="max-w-3xl">
                    <div class="text-sm font-bold uppercase tracking-[0.18em]
                                text-sky-600 dark:text-sky-400">
                        Онлайн-школа
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900
                               dark:text-white sm:text-4xl lg:text-5xl">
                        Практические знания
                        <span class="text-sky-600 dark:text-sky-400">
                            для специалистов
                        </span>
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600
                              dark:text-slate-300 sm:text-lg">
                        Обучение и практические материалы по вентиляции, климатическому
                        оборудованию и инженерным системам —
                        от подбора оборудования до эксплуатации.
                    </p>

                    <div class="mt-8">
                        <Link
                            :href="route('public.schoolCourses.index')"
                            class="group inline-flex items-center justify-center gap-2 rounded-xl
                                   bg-sky-600 px-6 py-3 text-sm font-semibold text-white
                                   shadow-lg shadow-sky-950/10 transition
                                   hover:bg-sky-500 sm:text-base"
                        >
                            Перейти в онлайн-школу

                            <svg
                                class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="M4 10h12M11 5l5 5-5 5"
                                    stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Направления обучения -->
                <div class="relative">
                    <div class="absolute -inset-6 rounded-[2rem]
                                bg-sky-500/5 blur-2xl dark:bg-sky-400/5"></div>

                    <div class="relative overflow-hidden rounded-3xl
                                border-2 border-slate-200 dark:border-slate-700
                                bg-slate-100 dark:bg-slate-700/70 p-6
                                dark:bg-slate-900 sm:p-8">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-bold uppercase tracking-[0.16em]
                                            text-slate-500 dark:text-slate-400">
                                    Направления
                                </div>

                                <div class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
                                    От теории к практике
                                </div>
                            </div>

                            <div class="flex h-11 w-11 items-center justify-center
                                        rounded-xl bg-sky-600 text-white
                                        shadow-lg shadow-sky-950/10">
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M4 19.5V6.75A2.75 2.75 0 0 1 6.75 4H20v15.5H6.75A2.75 2.75 0 0 0 4 22.25M4 19.5A2.5 2.5 0 0 1 6.5 17H20"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-200 dark:divide-slate-800">
                            <div class="group flex items-center gap-5 py-4 first:pt-0">
                                <span class="text-md font-bold tabular-nums
                                             text-sky-600 dark:text-sky-400">
                                    01
                                </span>

                                <div class="flex-1">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        Оборудование
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-300">
                                        Устройство, характеристики и подбор
                                    </div>
                                </div>
                            </div>

                            <div class="group flex items-center gap-5 py-4">
                                <span class="text-md font-bold tabular-nums
                                             text-sky-600 dark:text-sky-400">
                                    02
                                </span>

                                <div class="flex-1">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        Проектирование
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-300">
                                        Расчёты и инженерные решения
                                    </div>
                                </div>
                            </div>

                            <div class="group flex items-center gap-5 py-4">
                                <span class="text-md font-bold tabular-nums
                                             text-sky-600 dark:text-sky-400">
                                    03
                                </span>

                                <div class="flex-1">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        Монтаж
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-300">
                                        Практика монтажа инженерных систем
                                    </div>
                                </div>
                            </div>

                            <div class="group flex items-center gap-5 py-4 last:pb-0">
                                <span class="text-md font-bold tabular-nums
                                             text-sky-600 dark:text-sky-400">
                                    04
                                </span>

                                <div class="flex-1">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        Эксплуатация
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-300">
                                        Обслуживание и работа оборудования
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-7 rounded-2xl border-2 border-sky-200 bg-sky-50 p-4
                                    dark:border-sky-900/60 dark:bg-sky-950/30">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0
                                             items-center justify-center rounded-full
                                             bg-sky-600 text-white">
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path
                                            d="m5 10 3 3 7-7"
                                            stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <div>
                                    <div class="text-sm font-semibold
                                                text-slate-900 dark:text-white">
                                        Прикладной подход
                                    </div>

                                    <div class="mt-1 text-sm leading-6
                                                text-slate-600 dark:text-slate-300">
                                        Знания, которые можно применять
                                        в реальных инженерных задачах.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
