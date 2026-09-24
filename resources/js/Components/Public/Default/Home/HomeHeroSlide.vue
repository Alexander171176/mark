<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    slide: { type: Object, required: true },
    active: { type: Boolean, default: false },
})
</script>

<template>
    <article class="relative min-h-[620px] overflow-hidden
                    sm:min-h-[640px] lg:h-[clamp(560px,calc(100svh-10rem),900px)] lg:min-h-0">
        <!-- Фоновое изображение -->
        <div class="absolute inset-0">
            <img
                :src="slide.image"
                :alt="slide.imageAlt || slide.title || ''"
                class="h-full w-full object-cover"
                :class="slide.imagePosition || 'object-center'"
            />

            <!-- Затемнение изображения -->
            <div class="pointer-events-none absolute inset-0 bg-slate-950/45"></div>

            <!-- Градиент для читаемости контента -->
            <div class="pointer-events-none absolute inset-0
                        bg-gradient-to-r from-slate-950/90 via-slate-950/55 to-slate-950/5"></div>

            <!-- Нижний градиент -->
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t
                        from-slate-950/60 via-transparent to-slate-950/10"></div>
        </div>

        <!-- Контент -->
        <div class="relative z-10 mx-auto flex
                    min-h-[620px] max-w-7xl items-center px-4 pb-24 pt-16
                    sm:min-h-[640px] sm:px-6 lg:h-full lg:min-h-0 lg:px-8 lg:pb-24 lg:pt-16">
            <div class="max-w-4xl">
                <!-- Маркер -->
                <div
                    class="mb-4 inline-flex items-center rounded-full
                           border border-white/25 bg-white/10 px-4 py-2
                           text-xs font-semibold uppercase tracking-[0.16em]
                           text-white backdrop-blur-md sm:text-sm"
                >
                    {{ slide.label }}
                </div>

                <!-- Заголовок -->
                <h1 v-if="slide.isMain"
                    class="max-w-4xl text-3xl font-bold leading-[1.08] tracking-tight
                           text-white sm:text-4xl lg:text-5xl xl:text-5xl">
                    {{ slide.title }}

                    <span v-if="slide.accent" class="mt-1.5 block text-sky-400">
                        {{ slide.accent }}
                    </span>
                </h1>

                <h2 v-else
                    class="max-w-4xl text-3xl font-bold leading-[1.08] tracking-tight
                           text-white sm:text-4xl lg:text-5xl xl:text-5xl">
                    {{ slide.title }}

                    <span v-if="slide.accent" class="mt-1.5 block text-sky-400">
                        {{ slide.accent }}
                    </span>
                </h2>

                <!-- Описание -->
                <p v-if="slide.description"
                   class="mt-4 max-w-2xl text-base leading-7 text-slate-200 sm:text-lg lg:text-xl">
                    {{ slide.description }}
                </p>

                <!-- Кнопки -->
                <div v-if="slide.actions?.length"
                     class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <template v-for="(action, index) in slide.actions" :key="index">
                        <Link
                            v-if="action.route"
                            :href="route(action.route, action.params || {})"
                            class="inline-flex items-center justify-center gap-2 rounded-xl
                                   px-6 py-3 text-sm font-semibold transition sm:text-base"
                            :class="action.primary
                                ? 'bg-sky-600 text-white shadow-lg shadow-sky-950/20 hover:bg-sky-500'
                                : 'border border-white/35 bg-white/10 text-white ' +
                                 'backdrop-blur-sm hover:border-white/60 hover:bg-white/20'"
                        >
                            {{ action.label }}

                            <svg v-if="action.primary"
                                 class="h-4 w-4"
                                 viewBox="0 0 20 20"
                                 fill="none"
                                 aria-hidden="true">
                                <path
                                    d="M4 10h12M11 5l5 5-5 5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </Link>

                        <a
                            v-else
                            :href="action.href || '#'"
                            class="inline-flex items-center justify-center gap-2
                                   rounded-xl px-6 py-3 text-sm font-semibold
                                   transition sm:text-base"
                            :class="action.primary
                                ? 'bg-sky-600 text-white shadow-lg shadow-sky-950/20 ' +
                                 'hover:bg-sky-500'
                                : 'border border-white/35 bg-white/10 text-white ' +
                                 'backdrop-blur-sm hover:border-white/60 hover:bg-white/20'"
                        >
                            {{ action.label }}
                        </a>
                    </template>
                </div>

                <!-- Преимущества -->
                <div v-if="slide.advantages?.length"
                     class="mt-6 grid max-w-3xl gap-3 border-t border-white/20 pt-5 sm:grid-cols-3">
                    <div v-for="(advantage, index) in slide.advantages"
                         :key="index"
                         class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center
                                     rounded-full border border-sky-400/40
                                     bg-sky-400/10 text-sky-300">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path
                                    d="m5 10 3 3 7-7"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>

                        <div>
                            <div class="text-sm font-semibold text-white">
                                {{ advantage.title }}
                            </div>

                            <div v-if="advantage.text"
                                 class="mt-0.5 text-xs leading-5 text-slate-300">
                                {{ advantage.text }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>
