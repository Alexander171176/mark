<script setup>
defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close'])
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-[60] bg-black/30"
        @click.self="emit('close')"
    >
        <aside
            class="fixed right-0 top-0 z-[70] flex h-full w-full max-w-sm
                   flex-col border-l border-slate-200 bg-white shadow-2xl
                   transition-all duration-300
                   dark:border-slate-700 dark:bg-slate-900"
        >
            <div
                class="flex items-center justify-between border-b border-slate-200
                       px-4 py-3 dark:border-slate-700"
            >
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Telegram
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Быстрый доступ к Telegram
                    </p>
                </div>

                <button
                    type="button"
                    class="rounded-md px-2 py-1 text-slate-500 transition
                           hover:bg-slate-100 hover:text-slate-800
                           dark:text-slate-300 dark:hover:bg-slate-800"
                    @click="emit('close')"
                >
                    ✕
                </button>
            </div>

            <div class="flex flex-1 flex-col items-center justify-center gap-4 p-6 text-center">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full
                           bg-sky-500 text-white shadow-lg"
                >
                    <svg
                        class="h-8 w-8 fill-current"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M9.78 15.67 9.4 20.9c.55 0 .8-.23 1.09-.5l2.62-2.5 5.43 3.98c1 .55 1.7.26 1.97-.92L24 3.95c.35-1.63-.59-2.27-1.56-1.88L1.1 10.3c-1.46.57-1.44 1.38-.25 1.75l5.46 1.7L18.98 5.8c.6-.4 1.14-.18.69.22L9.78 15.67z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                        Наш Telegram
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Откройте канал или чат в новой вкладке.
                    </p>
                </div>

                <a
                    href="https://t.me/ai_web_it"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center rounded-lg
                           bg-sky-500 px-4 py-2 text-sm font-semibold text-white
                           transition hover:bg-sky-600"
                >
                    Открыть Telegram
                </a>
            </div>
        </aside>
    </div>
</template>
