<script setup>
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
})

/*
|--------------------------------------------------------------------------
| Пагинация
|--------------------------------------------------------------------------
*/

const meta = computed(() => {
    return props.pagination?.meta || {}
})

const currentPage = computed(() => {
    return Number(meta.value.current_page || 1)
})

const lastPage = computed(() => {
    return Number(meta.value.last_page || 1)
})

const totalItems = computed(() => {
    return Number(meta.value.total || 0)
})

const fromItem = computed(() => {
    return Number(meta.value.from || 0)
})

const toItem = computed(() => {
    return Number(meta.value.to || 0)
})

const pageInput = ref(currentPage.value)

watch(
    currentPage,
    (value) => {
        pageInput.value = value
    }
)

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
        page = currentPage.value
    }

    page = Math.max(
        1,
        page
    )

    page = Math.min(
        page,
        lastPage.value
    )

    pageInput.value = page

    if (page === currentPage.value) {
        return
    }

    const query = Object.fromEntries(
        new URLSearchParams(
            window.location.search
        )
    )

    router.get(
        window.location.pathname,
        {
            ...query,
            page,
        },
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
        }
    )
}

const previousPage = () => {
    if (currentPage.value <= 1) {
        return
    }

    changePage(
        currentPage.value - 1
    )
}

const nextPage = () => {
    if (
        currentPage.value >=
        lastPage.value
    ) {
        return
    }

    changePage(
        currentPage.value + 1
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
                    :max="lastPage"
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
                    {{ t('of') }} {{ lastPage }}
                </span>
            </div>

            <button
                type="button"
                :disabled="currentPage >= lastPage"
                @click="nextPage"
                class="px-3 py-1 text-sm font-semibold border rounded"
                :class="
                    currentPage >= lastPage
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
            {{ currentPage }} / {{ lastPage }}
        </div>
    </div>
</template>
