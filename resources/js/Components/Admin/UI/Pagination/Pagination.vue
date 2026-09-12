<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    itemsPerPage: {
        type: Number,
        required: true,
    },
    totalItems: {
        type: Number,
        required: true,
    },
})

const emit = defineEmits([
    'update:currentPage',
])

/*
|--------------------------------------------------------------------------
| Страницы
|--------------------------------------------------------------------------
*/

const totalPages = computed(() => {
    if (props.totalItems <= 0 || props.itemsPerPage <= 0) {
        return 1
    }

    return Math.max(
        1,
        Math.ceil(
            props.totalItems / props.itemsPerPage
        )
    )
})

const pageInput = ref(
    props.currentPage
)

watch(
    () => props.currentPage,
    (value) => {
        pageInput.value = value
    }
)

/*
|--------------------------------------------------------------------------
| Диапазон записей
|--------------------------------------------------------------------------
*/

const fromItem = computed(() => {
    if (props.totalItems <= 0) {
        return 0
    }

    return (
        (props.currentPage - 1) *
        props.itemsPerPage
    ) + 1
})

const toItem = computed(() => {
    if (props.totalItems <= 0) {
        return 0
    }

    return Math.min(
        props.currentPage *
        props.itemsPerPage,
        props.totalItems
    )
})

/*
|--------------------------------------------------------------------------
| Навигация
|--------------------------------------------------------------------------
*/

const changePage = (value) => {
    let page = Number.parseInt(
        value,
        10
    )

    if (!Number.isInteger(page)) {
        page = props.currentPage
    }

    page = Math.max(
        1,
        page
    )

    page = Math.min(
        page,
        totalPages.value
    )

    pageInput.value = page

    if (page === props.currentPage) {
        return
    }

    emit(
        'update:currentPage',
        page
    )
}

const previousPage = () => {
    if (props.currentPage <= 1) {
        return
    }

    changePage(
        props.currentPage - 1
    )
}

const nextPage = () => {
    if (
        props.currentPage >=
        totalPages.value
    ) {
        return
    }

    changePage(
        props.currentPage + 1
    )
}

const handlePageInput = () => {
    changePage(
        pageInput.value
    )
}
</script>

<template>
    <div
        v-if="totalItems > 0"
        class="w-full flex flex-col xl:flex-row items-center
               justify-between gap-2 mt-3 mb-1"
    >
        <!-- Количество записей -->
        <div
            class="text-sm font-semibold
                   text-gray-600 dark:text-gray-300"
        >
            {{ fromItem }}
            –
            {{ toItem }}
            из
            {{ totalItems }}
        </div>

        <!-- Навигация -->
        <div class="flex items-center justify-center flex-wrap gap-2">
            <button
                type="button"
                :disabled="currentPage <= 1"
                @click="previousPage"
                class="px-3 py-1 text-sm font-semibold border rounded"
                :class="
                    currentPage <= 1
                        ? 'text-slate-400 dark:text-slate-500 cursor-not-allowed'
                        : 'bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white'
                "
            >
                ← {{ t('previous') }}
            </button>

            <div class="flex items-center gap-2">
                <input
                    v-model="pageInput"
                    type="number"
                    min="1"
                    :max="totalPages"
                    @change="handlePageInput"
                    @keyup.enter="handlePageInput"
                    class="w-20 px-2 py-1 text-center
                           border border-slate-500
                           font-semibold text-sm rounded-sm
                           focus:border-indigo-500
                           focus:ring-indigo-300
                           dark:bg-cyan-800
                           dark:text-slate-100"
                />

                <span
                    class="text-sm font-semibold
                           text-gray-700 dark:text-gray-200"
                >
                    {{ t('of') }} {{ totalPages }}
                </span>
            </div>

            <button
                type="button"
                :disabled="currentPage >= totalPages"
                @click="nextPage"
                class="px-3 py-1 text-sm font-semibold border rounded"
                :class="
                    currentPage >= totalPages
                        ? 'text-slate-400 dark:text-slate-500 cursor-not-allowed'
                        : 'bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white'
                "
            >
                {{ t('next') }} →
            </button>
        </div>

        <!-- Текущая страница -->
        <div
            class="text-sm font-semibold
                   text-gray-600 dark:text-gray-300"
        >
            {{ currentPage }} / {{ totalPages }}
        </div>
    </div>
</template>
