<script setup>
const steps = [
    {
        number: '01',
        title: 'Получаем исходные данные',
        text: 'Проект, спецификацию, техническое задание или основные параметры объекта.',
    },
    {
        number: '02',
        title: 'Подбираем оборудование',
        text: 'Сравниваем характеристики и формируем подходящее техническое решение.',
    },
    {
        number: '03',
        title: 'Готовим предложение',
        text: 'Предлагаем оборудование, комплектацию и варианты поставки под задачу проекта.',
    },
]
</script>

<template>
    <section id="equipment-selection"
             class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
                <!-- Основное предложение -->
                <div>
                    <div class="text-sm font-bold uppercase tracking-[0.18em]
                                text-sky-600 dark:text-sky-400">
                        Инженерный подбор
                    </div>

                    <h2 class="mt-3 max-w-2xl text-3xl font-bold tracking-tight
                               text-slate-900 dark:text-white sm:text-4xl lg:text-5xl">
                        Не знаете, какое
                        <span class="block text-sky-600 dark:text-sky-400">
                            оборудование выбрать?
                        </span>
                    </h2>

                    <p class="mt-5 max-w-xl text-base leading-7
                              text-slate-600 dark:text-slate-300 sm:text-lg">
                        Отправьте нам параметры объекта, проект или готовую спецификацию.
                        Поможем подобрать вентиляционное и климатическое оборудование
                        под технические требования проекта.
                    </p>

                    <!-- CTA -->
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a
                            href="#consultation"
                            class="inline-flex items-center justify-center gap-2 rounded-xl
                                   bg-sky-600 px-6 py-3.5 text-sm font-semibold text-white
                                   shadow-lg shadow-sky-900/10 transition
                                   hover:bg-sky-500 sm:text-base"
                        >
                            Подобрать оборудование

                            <svg
                                class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="M4 10h12M11 5l5 5-5 5"
                                    stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>

                        <a
                            href="#specification"
                            class="inline-flex items-center justify-center rounded-xl
                                   border border-slate-300 bg-slate-100 px-6 py-3.5 text-sm
                                   font-semibold text-slate-800 transition
                                   hover:border-sky-300 hover:text-sky-600
                                   dark:border-slate-700 dark:bg-slate-800
                                   dark:text-white dark:hover:border-sky-600
                                   dark:hover:text-sky-400 sm:text-base"
                        >
                            Отправить спецификацию
                        </a>
                    </div>

                    <!-- Подсказка -->
                    <div
                        class="mt-6 flex items-start gap-3 text-sm leading-6
                               text-slate-500 dark:text-slate-400">
                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0 text-sky-600 dark:text-sky-400"
                            viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path
                                d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"
                                stroke="currentColor" stroke-width="1.6" />
                            <path
                                d="M10 9v5M10 6.5v.1"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>

                        <span>
                            Можно начать даже без готового проекта — достаточно основных параметров
                            и описания задачи.
                        </span>
                    </div>
                </div>

                <!-- Этапы подбора -->
                <div class="relative">
                    <div class="absolute -inset-6 rounded-[2rem] bg-gradient-to-br from-sky-500/10
                                via-transparent to-blue-500/10 blur-2xl"></div>

                    <div
                        class="relative overflow-hidden rounded-3xl border border-slate-200
                               bg-white p-6 shadow-xl shadow-slate-900/5 dark:border-slate-700
                               dark:bg-slate-900 sm:p-8">
                        <!-- Верх карточки -->
                        <div
                            class="flex items-center justify-between gap-4 border-b
                                   border-slate-200 pb-6 dark:border-slate-800">
                            <div>
                                <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                    Подбор оборудования
                                </div>

                                <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    От задачи до готового предложения
                                </div>
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center
                                       rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-500/10
                                       dark:text-sky-400">
                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M4 20h16M7 17V8l5-4 5 4v9M10 17v-5h4v5"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <!-- Этапы -->
                        <div class="divide-y divide-slate-200 dark:divide-slate-800">
                            <div
                                v-for="step in steps"
                                :key="step.number"
                                class="group flex gap-5 py-6 first:pt-7 last:pb-1">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                            rounded-full border border-sky-200 bg-sky-50 text-xs
                                            font-bold text-sky-600 transition
                                            group-hover:border-sky-500 group-hover:bg-sky-600
                                            group-hover:text-white dark:border-sky-800
                                            dark:bg-sky-500/10 dark:text-sky-400">
                                    {{ step.number }}
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-white">
                                        {{ step.title }}
                                    </h3>

                                    <p class="mt-1.5 text-sm leading-6 text-slate-600
                                              dark:text-slate-400">
                                        {{ step.text }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Результат -->
                        <div
                            class="mt-7 flex items-center gap-4 rounded-2xl bg-slate-100
                                   p-4 dark:bg-slate-800
                                   border-2 border-slate-200 dark:border-slate-700">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center
                                       rounded-full bg-emerald-100 text-emerald-600
                                       border-2 border-slate-300 dark:border-slate-700
                                       dark:bg-emerald-500/10 dark:text-emerald-400">
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path
                                        d="m5 10 3 3 7-7"
                                        stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            <div>
                                <div class="text-sm font-semibold
                                            text-slate-900 dark:text-white">
                                    Готовое техническое решение
                                </div>

                                <div class="mt-0.5 text-xs leading-5
                                            text-slate-500 dark:text-slate-400">
                                    Оборудование и комплектация под параметры объекта
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
