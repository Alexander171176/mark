<script setup>

const solutions = [
    {
        title: 'Приточные установки',
        description: 'Оборудование для организованной подачи, фильтрации, нагрева и охлаждения наружного воздуха.',
        icon: 'air',
        href: '#',
    },
    {
        title: 'Чиллеры и ККБ',
        description: 'Оборудование для систем холодоснабжения и центрального кондиционирования объектов.',
        icon: 'snow',
        href: '#',
    },
    {
        title: 'VRF-системы',
        description: 'Мультизональные климатические системы для коммерческих и административных зданий.',
        icon: 'building',
        href: '#',
    },
    {
        title: 'Радиальные вентиляторы',
        description: 'Вентиляторы общего и специального назначения для промышленных вентиляционных систем.',
        icon: 'fan',
        href: '#',
    },
    {
        title: 'Калориферы',
        description: 'Водяные и электрические воздухонагреватели для вентиляционных и воздушно-отопительных систем.',
        icon: 'heat',
        href: '#',
    },
    {
        title: 'Инженерный подбор',
        description: 'Поможем подобрать оборудование по проекту, техническому заданию или готовой спецификации.',
        icon: 'engineering',
        href: '#equipment-selection',
        accent: true,
    },
]
</script>

<template>
    <section class="bg-white dark:bg-slate-900 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Заголовок секции -->
            <div class="max-w-3xl">
                <div class="text-sm font-bold uppercase tracking-[0.18em]
                            text-sky-600 dark:text-sky-400">
                    Оборудование
                </div>

                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white
                           sm:text-4xl lg:text-5xl">
                    Решения для вентиляции
                    <span class="block text-sky-600 dark:text-sky-400">
                        и климатических систем
                    </span>
                </h2>

                <p class="mt-5 max-w-2xl text-base leading-7
                          text-slate-600 dark:text-slate-300 sm:text-lg">
                    Подбираем оборудование для промышленных, коммерческих и инфраструктурных
                    объектов с учётом параметров проекта и условий эксплуатации.
                </p>
            </div>

            <!-- Направления -->
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:mt-12 lg:grid-cols-3">
                <a
                    v-for="solution in solutions"
                    :key="solution.title"
                    :href="solution.href"
                    class="group relative flex min-h-[250px] flex-col overflow-hidden rounded-2xl
                           border-2 p-4 transition duration-300 sm:p-5"
                    :class="solution.accent
                        ? 'border-sky-600 bg-sky-600 text-white shadow-lg ' +
                         'shadow-sky-950/10 hover:bg-sky-500'
                        : 'border-slate-200 bg-slate-100 dark:bg-slate-950 ' +
                         'text-slate-900 hover:-translate-y-1 ' +
                         'hover:border-sky-300 hover:bg-white hover:shadow-xl ' +
                          'hover:shadow-slate-900/5 dark:border-slate-800 dark:bg-slate-900 ' +
                           'dark:text-white dark:hover:border-sky-700 dark:hover:bg-slate-900'"
                >
                    <!-- Иконка -->
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl"
                        :class="solution.accent
                            ? 'bg-white/15 text-white'
                            : 'bg-sky-100 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400'"
                    >
                        <!-- Приточная установка -->
                        <svg v-if="solution.icon === 'air'"
                             class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M3 8h10.5a2.5 2.5 0 1 0-2.4-3.2"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            <path
                                d="M3 12h15.5a2.5 2.5 0 1 1-2.4 3.2"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            <path
                                d="M3 16h7"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>

                        <!-- Холод -->
                        <svg v-else-if="solution.icon === 'snow'"
                             class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M12 3v18M4.2 7.5l15.6 9M4.2 16.5l15.6-9"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            <path
                                d="m9.5 5.5 2.5 2 2.5-2M9.5 18.5l2.5-2 2.5 2"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>

                        <!-- Здание -->
                        <svg v-else-if="solution.icon === 'building'"
                             class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M5 21V4h10v17M15 9h4v12M8 8h4M8 12h4M8 16h4M3 21h18"
                                stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <!-- Вентилятор -->
                        <svg v-else-if="solution.icon === 'fan'"
                             class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.7" />
                            <path
                                d="M12 10c-1-4 1-7 3.5-6.5 2.2.5 2.4 3.2.8 5.2C15 10.2 13.7 10.7 12 10ZM14 12c4-1 7 1 6.5 3.5-.5 2.2-3.2 2.4-5.2.8C13.8 15 13.3 13.7 14 12ZM12 14c1 4-1 7-3.5 6.5-2.2-.5-2.4-3.2-.8-5.2C9 13.8 10.3 13.3 12 14ZM10 12c-4 1-7-1-6.5-3.5.5-2.2 3.2-2.4 5.2-.8C10.2 9 10.7 10.3 10 12Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                        </svg>

                        <!-- Нагрев -->
                        <svg v-else-if="solution.icon === 'heat'"
                             class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M7 20c-2-2.2-1.5-4.6.5-6.8 1.7-1.9 2.1-3.7 1.2-5.7 3.4 1.4 5 3.7 4.4 6.1.5-1.8 1.4-3.2 2.7-4.2 2.7 3.2 3.1 7.5.2 10.6"
                                stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <!-- Инженерный подбор -->
                        <svg v-else
                             class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M4 20h16M7 17V8l5-4 5 4v9M10 17v-5h4v5"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="m17 5 2-2M5 5 3 3"
                                  stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                    </div>

                    <h3 class="mt-6 text-xl font-bold">
                        {{ solution.title }}
                    </h3>

                    <p
                        class="mt-3 text-sm leading-6"
                        :class="solution.accent
                            ? 'text-sky-50'
                            : 'text-slate-600 dark:text-slate-300'"
                    >
                        {{ solution.description }}
                    </p>

                    <div class="mt-auto flex items-center gap-2 pt-6 text-sm font-semibold">
                        <span>{{ solution.accent ? 'Получить консультацию' : 'Подробнее' }}</span>

                        <svg
                            class="h-4 w-4 transition-transform group-hover:translate-x-1"
                            viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path
                                d="M4 10h12M11 5l5 5-5 5"
                                stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </section>
</template>
