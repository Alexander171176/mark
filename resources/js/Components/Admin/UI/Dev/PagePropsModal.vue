<script setup>
import { computed, ref, watch, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const page = usePage()

const isOpen = ref(false)
const query = ref('')
const copied = ref(false)

const propsObject = computed(() => page.props ?? {})
const prettyJson = computed(() => JSON.stringify(propsObject.value, null, 2))

const filteredJson = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return prettyJson.value

    // Быстрый фильтр по строке JSON (достаточно для dev-окна)
    const lines = prettyJson.value.split('\n')
    const matched = lines.filter(l => l.toLowerCase().includes(q))

    // чтобы было читабельно, если совпадений слишком много — оставляем всё (чтобы не ломать скобки)
    if (matched.length === 0) return 'Совпадений не найдено.'
    if (matched.length > 600) return prettyJson.value

    return matched.join('\n')
})

watch(isOpen, (v) => {
    if (!v) {
        query.value = ''
        copied.value = false
    }
})

const open = () => (isOpen.value = true)
const close = () => (isOpen.value = false)

const copy = async () => {
    try {
        await navigator.clipboard.writeText(prettyJson.value)
        copied.value = true
        setTimeout(() => (copied.value = false), 1200)
    } catch (e) {
        copied.value = false
    }
}

// (опционально, но красиво и удобно) закрытие по Esc, без лишней возни
const onKeydown = (e) => {
    if (!isOpen.value) return
    if (e.key === 'Escape') close()
}
window.addEventListener('keydown', onKeydown)
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown))
</script>

<template>
    <div class="flex items-center justify-end">
        <button
            type="button"
            @click="open"
            class="inline-flex items-center gap-2 px-3 py-1 rounded-sm transition
                   text-sm font-medium text-slate-900 dark:text-slate-100
                   bg-white/80 hover:bg-white dark:bg-slate-900/60 dark:hover:bg-slate-900/80
                   border border-slate-400 dark:border-slate-900 shadow-sm hover:shadow
                   focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/70
                   dark:focus-visible:ring-slate-500/60"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                 class="w-4 h-4 fill-none stroke-current" stroke-width="1.5">
                <path d="M8 9h8M8 13h8M8 17h6" stroke-linecap="round" />
                <path
                    d="M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"
                    stroke-linecap="round" stroke-linejoin="round"
                />
            </svg>
            Props
        </button>
    </div>

    <teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-50">
            <!-- overlay -->
            <div
                class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-[2px]"
                @click="close"></div>

            <!-- panel -->
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div
                    class="w-full max-w-6xl rounded-2xl overflow-hidden
                           bg-white/95 dark:bg-slate-900/95
                           border border-slate-800 dark:border-slate-200/80
                           shadow-2xl dark:shadow-black/40"
                    role="dialog"
                    aria-modal="true"
                >
                    <!-- header -->
                    <div
                        class="flex items-center justify-between gap-4 px-4 py-3
                               bg-slate-50/80 dark:bg-slate-900/60
                               border-b border-slate-200 dark:border-slate-700/70"
                    >
                        <div class="min-w-0">
                            <div class="text-[11px] font-semibold
                                        text-slate-800 dark:text-slate-200 truncate">
                                Inertia $page.props
                            </div>
                            <div class="text-[9px] text-slate-500 dark:text-slate-400">
                                Просмотр всех props (dev-инструмент)
                            </div>
                        </div>

                        <input
                            v-model="query"
                            type="text"
                            placeholder="Поиск по props…"
                            class="w-full px-2 py-1 border border-slate-300 rounded-xs
                                   bg-white dark:bg-gray-800 text-sm font-semibold
                                   text-gray-700 dark:text-gray-300"
                        />

                        <div class="flex items-center gap-2">

                            <button
                                type="button"
                                @click="copy"
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-sm
                                       text-sm font-medium text-white dark:text-slate-900
                                       hover:text-yellow-300 dark:hover:text-yellow-700
                                       bg-slate-700 dark:bg-slate-300 transition
                                       hover:bg-slate-900 dark:hover:bg-slate-100
                                       border border-transparent dark:border-slate-200
                                       focus-visible:outline-none focus-visible:ring-2
                                       focus-visible:ring-slate-400/70
                                       dark:focus-visible:ring-slate-500/60"
                            >
                                <svg v-if="!copied" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 24 24"
                                     class="w-4 h-4 fill-none stroke-current" stroke-width="1.5">
                                    <path d="M9 9h10v10H9z" stroke-linejoin="round" />
                                    <path
                                        d="M5 15H4a1 1 0 01-1-1V4a1 1 0 011-1h10a1 1 0 011 1v1"
                                        stroke-linecap="round"
                                    />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                     class="w-4 h-4 fill-none stroke-current" stroke-width="1.5">
                                    <path d="M20 6L9 17l-5-5"
                                          stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ copied ? 'Скопировано' : 'Копировать' }}
                            </button>

                            <button
                                type="button"
                                @click="close"
                                class="p-1.5 rounded-sm transition
                                       hover:bg-gray-300/80 dark:hover:bg-gray-600/80
                                       text-slate-600 hover:text-red-500
                                       dark:text-slate-300 dark:hover:text-red-200
                                       focus-visible:outline-none focus-visible:ring-2
                                       focus-visible:ring-slate-400/70
                                       dark:focus-visible:ring-slate-500/60"
                                :aria-label="t('close')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                     class="w-5 h-5 fill-none stroke-current" stroke-width="1.5">
                                    <path d="M6 6l12 12M18 6l-12 12" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- body -->
                    <div>
                        <pre
                            class="max-h-[70vh] overflow-auto p-4 text-sm leading-5
                                   bg-slate-200/80 dark:bg-slate-800/80
                                   font-semibold text-indigo-800 dark:text-indigo-200
                                   border border-gray-300 dark:border-gray-400
                                   selection:bg-slate-300 dark:selection:bg-slate-700"
                        >
                            <code class="font-mono" style="background-color: unset">
                                {{ filteredJson }}
                            </code>
                        </pre>
                    </div>

                    <!-- footer -->
                    <div
                        class="flex items-center justify-between px-4 py-3
                               bg-slate-50/80 dark:bg-slate-900/60
                               border-b border-slate-200 dark:border-slate-700/70"
                    >
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Закрытие: Esc / кнопка / клик по оверлею
                        </div>

                        <button
                            type="button"
                            @click="close"
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-sm
                                   text-sm font-medium text-white dark:text-slate-900
                                   hover:text-yellow-300 dark:hover:text-yellow-700
                                   bg-slate-700 dark:bg-slate-300 transition
                                   hover:bg-slate-900 dark:hover:bg-slate-100
                                   border border-transparent dark:border-slate-200
                                   focus-visible:outline-none focus-visible:ring-2
                                   focus-visible:ring-slate-400/70
                                   dark:focus-visible:ring-slate-500/60"
                        >
                            {{ t('close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>
