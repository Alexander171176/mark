<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close'])

const sourceLanguage = ref('ru')
const targetLanguage = ref('en')
const sourceText = ref('')
const translatedText = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

const languages = [
    { code: 'auto', label: 'Авто' },
    { code: 'ru', label: 'Русский' },
    { code: 'en', label: 'English' },
    { code: 'kk', label: 'Қазақша' },
    { code: 'zh', label: '简体中文' },
]

const canTranslate = computed(() => {
    return sourceText.value.trim().length > 0
        && targetLanguage.value
        && sourceLanguage.value !== targetLanguage.value
})

const translateText = async () => {
    if (!canTranslate.value) return

    isLoading.value = true
    errorMessage.value = ''
    translatedText.value = ''

    try {
        const query = encodeURIComponent(sourceText.value.trim())
        const url = `https://lingva.ml/api/v1/${sourceLanguage.value}/${targetLanguage.value}/${query}`

        const response = await fetch(url)

        if (!response.ok) {
            throw new Error('Ошибка сервиса перевода')
        }

        const data = await response.json()

        translatedText.value = data.translation || ''
    } catch (error) {
        console.error('Translation error:', error)
        errorMessage.value = 'Не удалось выполнить перевод. Попробуйте позже.'
    } finally {
        isLoading.value = false
    }
}

const swapLanguages = () => {
    if (sourceLanguage.value === 'auto') return

    const oldSource = sourceLanguage.value
    sourceLanguage.value = targetLanguage.value
    targetLanguage.value = oldSource

    const oldText = sourceText.value
    sourceText.value = translatedText.value
    translatedText.value = oldText
}

const copied = ref(false)

const copyResult = async () => {
    if (!translatedText.value) return

    try {
        await navigator.clipboard.writeText(translatedText.value)

        copied.value = true

        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (error) {
        console.error(error)
    }
}
</script>

<template>
    <aside
        class="fixed right-[4rem] top-0 z-40 h-screen w-full max-w-md
               border-l border-slate-200 bg-white shadow-2xl
               transition-transform duration-300 ease-in-out
               dark:border-slate-700 dark:bg-slate-900"
        :class="props.isOpen ? 'translate-x-0' : 'translate-x-[calc(100%+4rem)]'"
    >
        <div class="flex h-full flex-col">
            <div
                class="flex items-center justify-between border-b border-slate-400
                       px-4 py-1 dark:border-slate-700"
            >
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('translator') }}
                    </h2>
                    <p class="font-semibold text-xs text-indigo-700 dark:text-indigo-300">
                        RU / EN / KK / 中文
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-sm px-2 py-0.5 text-sm
                           hover:bg-slate-200 dark:hover:bg-slate-800
                           hover:border hover:border-slate-400
                           text-slate-500 dark:text-slate-400
                           hover:text-red-800 dark:hover:text-red-200"
                    @click="emit('close')"
                >
                    ✕
                </button>
            </div>

            <div class="flex-1 space-y-2 overflow-y-auto p-2">
                <div class="grid grid-cols-[1fr_auto_1fr] items-end gap-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold
                                      text-blue-700 dark:text-blue-300">
                            {{ t('whereFrom') }}
                        </label>

                        <select
                            v-model="sourceLanguage"
                            class="w-full rounded-sm border border-slate-400 bg-white px-3 py-1
                                   text-sm text-slate-900 focus:border-cyan-500 focus:outline-none
                                   dark:border-slate-500 dark:bg-slate-800 dark:text-slate-100"
                        >
                            <option
                                v-for="language in languages"
                                :key="language.code"
                                :value="language.code"
                            >
                                {{ language.label }}
                            </option>
                        </select>
                    </div>

                    <button
                        type="button"
                        class="rounded-sm border border-indigo-400 px-3 py-1
                               text-sm text-indigo-900 hover:bg-indigo-100
                               dark:dark:border-indigo-500 dark:text-indigo-300
                               dark:hover:bg-indigo-800"
                        @click="swapLanguages"
                    >
                        ⇄
                    </button>

                    <div>
                        <label class="mb-1 block text-sm font-semibold
                                      text-blue-700 dark:text-blue-300">
                            {{ t('whereTo') }}
                        </label>

                        <select
                            v-model="targetLanguage"
                            class="w-full rounded-sm border border-slate-400 bg-white px-3 py-1
                                   text-sm text-slate-900 focus:border-cyan-500 focus:outline-none
                                   dark:border-slate-500 dark:bg-slate-800 dark:text-slate-100"
                        >
                            <option
                                v-for="language in languages.filter(item => item.code !== 'auto')"
                                :key="language.code"
                                :value="language.code"
                            >
                                {{ language.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold
                                  text-blue-700 dark:text-blue-300">
                        {{ t('sourceText') }}
                    </label>

                    <textarea
                        v-model="sourceText"
                        rows="7"
                        class="w-full resize-none rounded-sm
                               border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-800 px-3 py-1 text-sm
                               text-slate-900 dark:text-slate-100
                               focus:border-cyan-500 focus:outline-none"
                        :placeholder="t('enterTextToTranslate')"
                    />
                </div>

                <button
                    type="button"
                    class="w-full rounded-sm bg-cyan-700 px-3 py-1
                           text-xs font-semibold
                           text-white transition hover:bg-cyan-800
                           disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!canTranslate || isLoading"
                    @click="translateText"
                >
                    {{ isLoading ? `${t('translator')}...` : `${t('translate')}` }}
                </button>

                <p
                    v-if="errorMessage"
                    class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700
                           dark:bg-red-900/30 dark:text-red-300"
                >
                    {{ errorMessage }}
                </p>

                <div>
                    <div class="mb-1 flex items-center justify-between">
                        <label class="block text-sm font-semibold
                                      text-blue-700 dark:text-blue-300">
                            {{ t('result') }}
                        </label>

                        <button
                            v-if="translatedText"
                            type="button"
                            class="inline-flex items-center gap-1 rounded-md px-2 py-1
                                   text-xs font-medium transition-all duration-300"
                            :class="copied
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                    : 'text-cyan-700 hover:bg-cyan-50 dark:text-cyan-400 dark:hover:bg-slate-800'"
                            @click="copyResult"
                        >
                            <svg
                                v-if="!copied"
                                class="h-3.5 w-3.5"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"
                                />
                            </svg>

                            <svg
                                v-else
                                class="h-3.5 w-3.5 animate-bounce"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M9 16.2L4.8 12 3.4 13.4 9 19 21 7 19.6 5.6 9 16.2Z"
                                />
                            </svg>

                            <span>
                                {{ copied ? `${t('copied')}` :`${t('copy')}` }}
                            </span>
                        </button>
                    </div>

                    <textarea
                        v-model="translatedText"
                        rows="7"
                        readonly
                        class="w-full resize-none rounded-sm
                               border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-800 px-3 py-1 text-sm
                               text-slate-900 dark:text-slate-100
                               focus:border-cyan-500 focus:outline-none"
                        :placeholder="t('translationWillAppearHere')"
                    />
                </div>
            </div>
        </div>
    </aside>
</template>
