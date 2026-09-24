<script setup>
const advantages = [
    {
        title: 'Инженерный подход',
        text: 'Подбираем оборудование по техническим параметрам и требованиям объекта, а не только по наименованию в спецификации.',
        icon: 'engineering',
    },
    {
        title: 'Комплексная поставка',
        text: 'Помогаем собрать оборудование разных категорий в единое коммерческое предложение для комплектации объекта.',
        icon: 'package',
    },
    {
        title: 'Работа со спецификациями',
        text: 'Принимаем проектные спецификации, ведомости оборудования и технические задания на подбор и расчёт.',
        icon: 'document',
    },
    {
        title: 'Подбор аналогов',
        text: 'При необходимости рассматриваем альтернативные решения с подходящими техническими характеристиками.',
        icon: 'compare',
    },
    {
        title: 'B2B-сотрудничество',
        text: 'Работаем с проектировщиками, монтажными и строительными организациями, снабжением и техническими специалистами.',
        icon: 'business',
    },
    {
        title: 'Поставка по Казахстану',
        text: 'Организуем поставку вентиляционного и климатического оборудования для объектов в Астане и регионах Казахстана.',
        icon: 'delivery',
    },
]
</script>

<template>
    <section class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">
                <!-- Заголовок -->
                <div class="lg:sticky lg:top-28 lg:self-start">
                    <div class="text-sm font-bold uppercase tracking-[0.18em]
                                text-sky-600 dark:text-sky-400">
                        Почему Агровент
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900
                               dark:text-white sm:text-4xl lg:text-5xl">
                        Помогаем решить
                        <span class="text-sky-600 dark:text-sky-400">
                            задачу целиком
                        </span>
                    </h2>

                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-600
                              dark:text-slate-300 sm:text-lg">
                        От первичного подбора оборудования до формирования предложения
                        и комплектации объекта.
                    </p>

                    <div class="mt-8 hidden lg:block">
                        <div class="h-px w-24 bg-sky-600 dark:bg-sky-400"></div>

                        <p class="mt-5 max-w-sm text-sm leading-6 text-slate-500 dark:text-slate-400">
                            Работаем как с отдельными позициями оборудования,
                            так и с комплексными заявками по проектам.
                        </p>
                    </div>
                </div>

                <!-- Преимущества -->
                <div class="grid gap-px overflow-hidden rounded-3xl border-2
                            border-slate-200 bg-slate-200 dark:border-slate-700
                            dark:bg-slate-700 sm:grid-cols-2">
                    <article
                        v-for="advantage in advantages"
                        :key="advantage.title"
                        class="group bg-slate-50 p-6 transition duration-300
                               hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-950
                               sm:p-7 lg:p-8"
                    >
                        <!-- Иконка -->
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl
                                    bg-sky-50 text-sky-600 transition duration-300
                                    group-hover:bg-sky-600 group-hover:text-white
                                    dark:bg-sky-950/50 dark:text-sky-400
                                    dark:group-hover:bg-sky-600 dark:group-hover:text-white">
                            <!-- Инженерный подход -->
                            <svg
                                v-if="advantage.icon === 'engineering'"
                                class="h-6 w-6"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M9 4h6M12 4v4M7 8h10l2 3v8H5v-8l2-3ZM8 13h8M9 16h6"
                                    stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <!-- Комплексная поставка -->
                            <svg
                                v-else-if="advantage.icon === 'package'"
                                class="h-6 w-6"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="m4 7 8-4 8 4-8 4-8-4Zm0 0v10l8 4 8-4V7M12 11v10"
                                    stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>

                            <!-- Спецификации -->
                            <svg
                                v-else-if="advantage.icon === 'document'"
                                class="h-6 w-6"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M7 3h7l4 4v14H7V3Zm7 0v5h4M10 12h5M10 16h5"
                                    stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <!-- Аналоги -->
                            <svg
                                v-else-if="advantage.icon === 'compare'"
                                class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M7 7h12M16 4l3 3-3 3M17 17H5M8 14l-3 3 3 3"
                                    stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <!-- B2B -->
                            <svg
                                v-else-if="advantage.icon === 'business'"
                                class="h-6 w-6"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M4 20V8h6v12M10 20V4h10v16M7 11h1M7 14h1M13 8h1M17 8h1M13 12h1M17 12h1M13 16h1M17 16h1M2 20h20"
                                    stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <!-- Доставка -->
                            <svg
                                v-else class="h-6 w-6"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M3 6h11v11H3V6Zm11 4h4l3 4v3h-7v-7ZM7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM18 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
                                    stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>

                        <h3 class="mt-6 text-lg font-bold text-slate-900 dark:text-white">
                            {{ advantage.title }}
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            {{ advantage.text }}
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>
</template>
