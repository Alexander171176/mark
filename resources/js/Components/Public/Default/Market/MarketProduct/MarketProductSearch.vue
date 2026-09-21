<script setup>
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    mobile: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits([
    'submitted',
])

/**
 * Текущий поисковый запрос
 * из MarketProduct Index.
 */
const currentSearch = computed(() => {
    return String(
        page.props?.filters?.q ?? ''
    )
})

/**
 * Локальное значение поля поиска.
 */
const searchQuery = ref(
    currentSearch.value
)

/**
 * Синхронизировать поле после
 * Inertia-переходов.
 */
watch(
    currentSearch,
    (value) => {
        searchQuery.value = value
    }
)

/**
 * Есть текст в поле поиска.
 */
const hasSearch = computed(() => {
    return searchQuery.value.trim() !== ''
})

/**
 * Выполнить поиск товаров.
 */
const submitSearch = () => {
    const query =
        searchQuery.value.trim()

    if (!query) {
        return
    }

    router.get(
        route('public.marketProducts.index'),
        {
            q: query,
        },
        {
            preserveState: false,
            preserveScroll: false,
        }
    )

    emit('submitted')
}

/**
 * Сбросить поиск товаров.
 */
const resetSearch = () => {
    searchQuery.value = ''

    router.get(
        route('public.marketProducts.index'),
        {},
        {
            preserveState: false,
            preserveScroll: false,
        }
    )

    emit('submitted')
}
</script>

<template>
    <!-- Mobile -->
    <form
        v-if="mobile"
        class="mb-3"
        @submit.prevent="submitSearch"
    >
        <div
            class="flex overflow-hidden rounded-lg
                   border-2 border-blue-600"
        >
            <div class="relative min-w-0 flex-1">
                <input
                    v-model="searchQuery"
                    type="text"
                    class="w-full border-0
                           bg-white dark:bg-gray-900
                           px-3 py-2 pr-10
                           text-sm text-slate-800 dark:text-slate-100
                           placeholder:text-slate-400
                           focus:ring-0"
                    :placeholder="t('search')"
                />

                <!-- Сброс поиска -->
                <button
                    v-if="hasSearch"
                    type="button"
                    class="absolute right-2 top-1/2
                           z-20 -translate-y-1/2
                           flex h-7 w-7 items-center justify-center
                           rounded-full
                           text-slate-400
                           transition
                           hover:bg-slate-100 hover:text-blue-600
                           dark:text-slate-500
                           dark:hover:bg-slate-800
                           dark:hover:text-blue-400"
                    :title="t('reset')"
                    @click="resetSearch"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 384 512"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3l105.4 105.3c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256l105.3-105.4z"
                        />
                    </svg>
                </button>
            </div>

            <button
                type="submit"
                class="shrink-0
                       bg-blue-600 px-4
                       text-sm font-bold text-white
                       transition hover:bg-blue-700"
            >
                {{ t('find') }}
            </button>
        </div>
    </form>

    <!-- Desktop -->
    <form
        v-else
        class="flex w-full min-w-0 justify-center"
        @submit.prevent="submitSearch"
    >
        <div
            class="flex w-full max-w-[1200px]
                   items-stretch"
        >
            <!--
                Поле находится выше кнопки.
                Отрицательный margin позволяет кнопке
                немного заходить под правый край поля.
            -->
            <div
                class="relative z-20
                       min-w-0 flex-1
                       -mr-4"
            >
                <input
                    v-model="searchQuery"
                    type="text"
                    class="relative z-10
                           block h-full w-full
                           rounded-xl
                           border-2 border-blue-600
                           bg-white dark:bg-gray-900
                           px-4 py-2 pr-12
                           text-sm text-slate-800 dark:text-slate-100
                           placeholder:text-slate-400
                           outline-none
                           focus:border-blue-600 focus:ring-0"
                    :placeholder="t('search')"
                />

                <!-- Сброс поиска -->
                <button
                    v-if="hasSearch"
                    type="button"
                    class="absolute right-4 top-1/2
                           z-20 -translate-y-1/2
                           flex h-7 w-7 items-center justify-center
                           rounded-full
                           text-slate-600
                           transition
                           hover:bg-slate-100 hover:text-blue-600
                           dark:text-slate-400
                           dark:hover:bg-slate-800
                           dark:hover:text-blue-400"
                    :title="t('reset')"
                    @click="resetSearch"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 384 512"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3l105.4 105.3c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256l105.3-105.4z"
                        />
                    </svg>
                </button>
            </div>

            <!--
                Кнопка находится ниже поля.
                Дополнительный левый padding компенсирует
                участок, который скрыт под input.
            -->
            <button
                type="submit"
                class="relative z-10
                       shrink-0
                       rounded-r-xl
                       bg-blue-600
                       pl-9 pr-6
                       text-sm font-bold text-white
                       transition hover:bg-blue-700"
                :title="t('search')"
            >
                <svg
                    class="h-5 w-5 fill-current"
                    viewBox="0 0 512 512"
                    aria-hidden="true"
                >
                    <path
                        d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"
                    />
                </svg>
            </button>
        </div>
    </form>
</template>
