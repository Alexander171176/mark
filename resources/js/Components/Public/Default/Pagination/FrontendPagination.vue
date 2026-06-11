<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    currentPage: { type: Number, default: 1 },
    itemsPerPage: { type: Number, default: 6 },
    totalItems: { type: Number, default: 0 },
})

const emit = defineEmits(['update:currentPage'])

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(props.totalItems / props.itemsPerPage))
})

const show = computed(() => totalPages.value > 1)

const pageInput = ref(props.currentPage)

watch(
    () => props.currentPage,
    (value) => {
        pageInput.value = value
    }
)

const changePage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(1, Math.min(value, totalPages.value))

    emit('update:currentPage', safePage)
    pageInput.value = safePage
}

const submit = () => {
    changePage(pageInput.value)
}
</script>

<template>
    <div v-if="show" class="mt-8 flex flex-col items-center gap-3">
        <div class="flex flex-wrap items-center justify-center gap-2">
            <button
                type="button"
                :disabled="currentPage <= 1"
                :title="t('back')"
                class="inline-flex items-center gap-2 rounded-sm px-2 py-2
                       text-sm font-semibold transition
                       bg-slate-100 dark:bg-slate-800
                       hover:bg-slate-50 dark:hover:bg-slate-700
                       border border-slate-400 dark:border-slate-700
                       text-indigo-500 dark:text-indigo-300
                       disabled:cursor-not-allowed disabled:opacity-50"
                @click="changePage(currentPage - 1)"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512">
                    <path d="M223.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L319.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L393.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34zm-192 34l136 136c9.4 9.4 24.6 9.4 33.9 0l22.6-22.6c9.4-9.4 9.4-24.6 0-33.9L127.9 256l96.4-96.4c9.4-9.4 9.4-24.6 0-33.9L201.7 103c-9.4-9.4-24.6-9.4-33.9 0l-136 136c-9.5 9.4-9.5 24.6-.1 34z"/>
                </svg>
            </button>

            <div class="px-3 py-2 flex items-center gap-2 rounded-sm
                        border border-slate-400 dark:border-slate-700
                        bg-slate-100 dark:bg-slate-800">
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">
                    {{ t('page') }}
                </span>

                <input
                    v-model="pageInput"
                    @keydown.enter.prevent="submit"
                    @blur="submit"
                    inputmode="numeric"
                    class="w-16 px-2 py-1 rounded-sm
                           text-center text-sm font-semibold
                           text-slate-900 dark:text-slate-100
                           bg-white dark:bg-gray-950
                           border border-slate-400 dark:border-slate-700
                           dark:focus:border-blue-500
                           dark:focus:ring-blue-900/40
                           outline-none focus:border-blue-400
                           focus:ring-2 focus:ring-blue-200"
                />

                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    {{ t('of') }} {{ totalPages }}
                </span>
            </div>

            <button
                type="button"
                :disabled="currentPage >= totalPages"
                :title="t('next')"
                class="inline-flex items-center gap-2 rounded-sm px-2 py-2
                       text-sm font-semibold transition
                       bg-slate-100 dark:bg-slate-800
                       hover:bg-slate-50 dark:hover:bg-slate-700
                       border border-slate-400 dark:border-slate-700
                       text-indigo-500 dark:text-indigo-300
                       disabled:cursor-not-allowed disabled:opacity-50"
                @click="changePage(currentPage + 1)"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512">
                    <path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"/>
                </svg>
            </button>
        </div>

        <div class="text-xs text-slate-600 dark:text-slate-400">
            {{ t('page') }}
            <span class="font-semibold">{{ currentPage }}/{{ totalPages }}</span>
            · {{ t('found') }}:
            <span class="font-semibold">{{ totalItems }}</span>
        </div>
    </div>
</template>
