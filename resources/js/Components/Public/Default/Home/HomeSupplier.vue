<script setup>
const steps = [
    {
        number: '01',
        title: 'Получаем спецификацию',
        text: 'Принимаем проект, ведомость оборудования, техническое задание или перечень необходимых позиций.',
    },
    {
        number: '02',
        title: 'Проверяем и подбираем',
        text: 'Уточняем характеристики, подбираем оборудование и предлагаем подходящие технические решения.',
    },
    {
        number: '03',
        title: 'Формируем предложение',
        text: 'Подготавливаем комплектацию объекта, стоимость оборудования и условия поставки.',
    },
    {
        number: '04',
        title: 'Организуем поставку',
        text: 'Комплектуем заказ и сопровождаем поставку оборудования на объект.',
    },
]
</script>

<template>
    <section id="specification"
             class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-slate-700 dark:bg-slate-950">
                <div class="grid lg:grid-cols-[0.9fr_1.1fr]">
                    <!-- Коммерческое предложение -->
                    <div class="relative overflow-hidden p-7 sm:p-10 lg:p-12 xl:p-14">
                        <!-- Декоративный фон -->
                        <div class="pointer-events-none absolute -left-24 -top-24
                                    h-72 w-72 rounded-full bg-sky-500/10 blur-3xl"></div>
                        <div class="pointer-events-none absolute -bottom-32 right-0 h-80 w-80
                                    rounded-full bg-cyan-400/10 blur-3xl"></div>

                        <div class="relative z-10">
                            <div class="text-sm font-bold uppercase
                                        tracking-[0.18em] text-sky-400">
                                Комплектация объектов
                            </div>

                            <h2 class="mt-4 max-w-xl text-3xl font-bold tracking-tight
                                       text-white sm:text-4xl lg:text-5xl">
                                Отправьте спецификацию —
                                <span class="text-sky-400">
                                    мы подготовим предложение
                                </span>
                            </h2>

                            <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                                Поможем подобрать и скомплектовать вентиляционное и климатическое
                                оборудование под требования вашего проекта.
                            </p>

                            <!-- Для кого -->
                            <div class="mt-8 flex flex-wrap gap-2">
                                <span class="rounded-full border border-white/15 bg-slate-600/85
                                             px-3 py-1.5 text-xs font-semibold text-slate-300">
                                    Проектировщикам
                                </span>

                                <span class="rounded-full border border-white/15 bg-slate-600/85
                                             px-3 py-1.5 text-xs font-semibold text-slate-300">
                                    Строительным компаниям
                                </span>

                                <span class="rounded-full border border-white/15 bg-slate-600/85
                                             px-3 py-1.5 text-xs font-semibold text-slate-300">
                                    Монтажным организациям
                                </span>

                                <span class="rounded-full border border-white/15 bg-slate-600/85
                                             px-3 py-1.5 text-xs font-semibold text-slate-300">
                                    Службам снабжения
                                </span>
                            </div>

                            <!-- Действия -->
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a
                                    href="#send-specification"
                                    class="inline-flex items-center justify-center rounded-xl
                                           bg-sky-600 px-5 py-3 text-sm font-semibold text-white
                                           transition hover:bg-sky-500"
                                >
                                    Отправить спецификацию

                                    <svg
                                        class="ml-2 h-4 w-4"
                                        viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path
                                            d="M4 10h12M11 5l5 5-5 5"
                                            stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>

                                <a
                                    href="#consultation"
                                    class="inline-flex items-center justify-center rounded-xl
                                           border border-white/20 bg-slate-600/85 px-5 py-3 text-sm
                                           font-semibold text-white transition
                                           hover:border-white/30 hover:bg-white/10"
                                >
                                    Обсудить проект
                                </a>
                            </div>

                            <!-- Дополнительная информация -->
                            <div class="mt-8 border-t border-white/10 pt-6">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-9 w-9 shrink-0
                                                items-center justify-center rounded-lg
                                                bg-sky-500/10 text-sky-400">
                                        <svg
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                                stroke="currentColor" stroke-width="1.6"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>

                                    <p class="max-w-md text-sm leading-6 text-slate-200">
                                        Можно отправить готовую спецификацию или исходные данные —
                                        наши специалисты помогут сформировать состав оборудования.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Этапы работы -->
                    <div class="bg-slate-100 p-7 dark:bg-slate-700/70 sm:p-10 lg:p-12 xl:p-14">
                        <div class="max-w-xl">
                            <div class="text-sm font-bold uppercase tracking-[0.18em]
                                        text-sky-600 dark:text-sky-400">
                                Как мы работаем
                            </div>

                            <h3 class="mt-3 text-2xl font-bold tracking-tight text-slate-900
                                       dark:text-white sm:text-3xl">
                                От исходных данных
                                до поставки оборудования
                            </h3>
                        </div>

                        <div class="mt-8 divide-y divide-slate-200 dark:divide-slate-700">
                            <div
                                v-for="step in steps"
                                :key="step.number"
                                class="group grid grid-cols-[auto_1fr] gap-4 py-5
                                       first:pt-0 last:pb-0 sm:gap-5"
                            >
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center
                                            rounded-xl border border-slate-400 bg-white
                                            text-md font-bold text-sky-600 transition
                                            group-hover:border-sky-300 group-hover:text-sky-700
                                            dark:border-slate-500 dark:bg-slate-900
                                            dark:text-sky-400 dark:group-hover:border-sky-700">
                                    {{ step.number }}
                                </div>

                                <div>
                                    <h4 class="text-base font-bold
                                               text-slate-800 dark:text-slate-200">
                                        {{ step.title }}
                                    </h4>

                                    <p class="mt-1.5 text-sm leading-6 text-slate-600
                                              dark:text-slate-300">
                                        {{ step.text }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Результат -->
                        <div class="mt-8 rounded-2xl border-2 border-sky-200 bg-sky-50 p-5
                                    dark:border-sky-900 dark:bg-sky-950/30">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                            rounded-xl bg-sky-600 text-white">
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path
                                            d="m5 12 4 4L19 6"
                                            stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>

                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        Результат
                                    </div>

                                    <p class="mt-1 text-sm leading-6
                                              text-slate-950 dark:text-white">
                                        Коммерческое предложение с подобранным оборудованием и
                                        комплектацией под ваш объект.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
