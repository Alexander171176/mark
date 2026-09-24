<script setup>
const scrollToSection = (id) => {
    if (typeof document === 'undefined') return

    document.getElementById(id)?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    })
}
</script>

<template>
    <section id="consultation"
             class="relative overflow-hidden bg-slate-800 dark:bg-slate-950 py-6 sm:py-8 lg:py-10">
        <!-- Декоративный фон -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="absolute -right-24 bottom-0 h-80 w-80
                        rounded-full bg-blue-500/10 blur-3xl"></div>

            <div
                class="absolute inset-0 opacity-[0.035]"
                style="background-image: linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px); background-size: 48px 48px;"
            ></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:gap-16">
                <!-- Основное предложение -->
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 text-xs
                                font-semibold uppercase tracking-[0.18em] text-sky-400">
                        <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                        Готовы обсудить задачу
                    </div>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-white
                               sm:text-4xl lg:text-5xl">
                        Подберём оборудование
                        <span class="text-sky-400">
                            под ваш проект
                        </span>
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                        Отправьте исходные данные или спецификацию.
                        Поможем подобрать оборудование, проверить характеристики,
                        подобрать аналоги и подготовить коммерческое предложение.
                    </p>

                    <!-- Основные действия -->
                    <div class="mt-8 flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="group inline-flex items-center justify-center gap-2 rounded-xl
                                   bg-sky-500 px-6 py-3 text-sm font-semibold text-white shadow-lg
                                   shadow-sky-950/30 transition hover:bg-sky-400 sm:text-base"
                            @click="scrollToSection('send-specification')"
                        >
                            Отправить спецификацию

                            <svg
                                class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="M4 10h12M11 5l5 5-5 5"
                                    stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl
                                   border border-white/20 bg-white/5 px-6 py-3 text-sm
                                   font-semibold text-white backdrop-blur-sm transition
                                   hover:border-white/40 hover:bg-white/10 sm:text-base"
                            @click="scrollToSection('equipment-selection')"
                        >
                            Нужна помощь с подбором
                        </button>
                    </div>

                    <!-- Короткие преимущества -->
                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 border-t border-white/10 pt-6
                                text-sm text-slate-300">
                        <div class="flex items-center gap-2">
                            <svg
                                class="h-4 w-4 shrink-0 text-sky-400"
                                viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="m4 10 4 4 8-8"
                                    stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            Инженерный подбор
                        </div>

                        <div class="flex items-center gap-2">
                            <svg
                                class="h-4 w-4 shrink-0 text-sky-400"
                                viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="m4 10 4 4 8-8"
                                    stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            Подбор аналогов
                        </div>

                        <div class="flex items-center gap-2">
                            <svg
                                class="h-4 w-4 shrink-0 text-sky-400"
                                viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="m4 10 4 4 8-8"
                                    stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            Комплектация объектов
                        </div>
                    </div>
                </div>

                <!-- Варианты обращения -->
                <div class="rounded-3xl border border-white/10 bg-white/[0.06] p-5
                            shadow-2xl shadow-black/20 backdrop-blur-sm sm:p-7">
                    <div class="mb-6">
                        <div class="text-xs font-semibold uppercase
                                    tracking-[0.16em] text-slate-400">
                            С чего начать
                        </div>

                        <h3 class="mt-2 text-xl font-bold text-white sm:text-2xl">
                            Выберите удобный вариант
                        </h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Спецификация -->
                        <button
                            type="button"
                            class="group flex w-full items-center gap-4 rounded-2xl
                                   border border-white/10 bg-white/[0.04] p-4 text-left
                                   transition hover:border-sky-400/40 hover:bg-white/[0.08]"
                            @click="scrollToSection('send-specification')"
                        >
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center
                                         rounded-xl bg-sky-500/15 text-sky-400">
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9m-6-6 6 6m-6-6v6h6M8 13h8M8 17h5"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold text-white">
                                    Есть спецификация
                                </span>

                                <span class="mt-1 block text-sm leading-5 text-slate-400">
                                    Проверим позиции и подготовим предложение
                                </span>
                            </span>

                            <svg class="h-5 w-5 shrink-0 text-slate-500 transition
                                        group-hover:translate-x-1 group-hover:text-sky-400"
                                 viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="M4 10h12M11 5l5 5-5 5"
                                    stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <!-- Подбор -->
                        <button
                            type="button"
                            class="group flex w-full items-center gap-4 rounded-2xl
                                   border border-white/10 bg-white/[0.04] p-4 text-left
                                   transition hover:border-sky-400/40 hover:bg-white/[0.08]"
                            @click="scrollToSection('equipment-selection')"
                        >
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center
                                         rounded-xl bg-sky-500/15 text-sky-400">
                                <svg class="h-5 w-5"
                                     viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M12 3v2m0 14v2M3 12h2m14 0h2M5.64 5.64l1.42 1.42m9.88 9.88 1.42 1.42m0-12.72-1.42 1.42M7.06 16.94l-1.42 1.42M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold text-white">
                                    Нужен подбор оборудования
                                </span>

                                <span class="mt-1 block text-sm leading-5 text-slate-400">
                                    Подберём решение по исходным данным
                                </span>
                            </span>

                            <svg class="h-5 w-5 shrink-0 text-slate-500 transition
                                        group-hover:translate-x-1 group-hover:text-sky-400"
                                 viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="M4 10h12M11 5l5 5-5 5"
                                    stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <!-- Консультация -->
                        <div class="flex items-center gap-4 rounded-2xl
                                    border border-white/10 bg-white/[0.04] p-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center
                                         rounded-xl bg-sky-500/15 text-sky-400">
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M8 10h8M8 14h5m8-2a9 9 0 1 1-4.22-7.63A9 9 0 0 1 21 12Z"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block font-semibold text-white">
                                    Есть вопрос
                                </span>

                                <span class="mt-1 block text-sm leading-5 text-slate-400">
                                    Обсудим задачу и определим следующий шаг
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-white/10 pt-5
                                text-sm leading-6 text-slate-400">
                        Можно начать с минимальной информации о проекте —
                        необходимые технические данные уточним в процессе.
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
