<script setup>
/**
 * История просмотренных товаров пользователя — Show
 *
 * Read-only административная страница:
 * - информация о пользователе;
 * - общая статистика просмотров;
 * - серверный поиск;
 * - серверная сортировка;
 * - выбор количества строк;
 * - серверная пагинация;
 * - таблица просмотренных товаров.
 */

import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'

import TitlePage
    from '@/Components/Admin/UI/Headlines/TitlePage.vue'

import AdminServerPagination
    from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'

import ServerSearchInput
    from '@/Components/Admin/UI/Search/ServerSearchInput.vue'

import CountTable
    from '@/Components/Admin/UI/Count/CountTable.vue'

import DefaultButton
    from '@/Components/Admin/UI/Buttons/DefaultButton.vue'

import ItemsPerPageSelect
    from '@/Components/Admin/Market/MarketRecentlyViewedProduct/Select/ItemsPerPageSelect.vue'

import SortSelect
    from '@/Components/Admin/Market/MarketRecentlyViewedProduct/Sort/SortSelect.vue'

import MarketRecentlyViewedProductTable
    from '@/Components/Admin/Market/MarketRecentlyViewedProduct/Table/MarketRecentlyViewedProductTable.vue'

const { t } = useI18n()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    /**
     * Пользователь,
     * историю которого просматриваем.
     */
    user: {
        type: Object,
        default: () => ({}),
    },

    /**
     * Серверная пагинированная история
     * просмотренных товаров.
     */
    history: {
        type: [Array, Object],
        default: () => [],
    },

    /**
     * Общее количество уникальных товаров
     * в истории пользователя.
     */
    historyCount: {
        type: Number,
        default: 0,
    },

    /**
     * Дата последнего просмотра товара.
     */
    lastViewedAt: {
        type: String,
        default: null,
    },

    search: {
        type: String,
        default: '',
    },

    /**
     * Текущие server-side фильтры.
     */
    filters: {
        type: Object,
        default: () => ({}),
    },

    /**
     * Текущая локаль Laravel.
     */
    locale: {
        type: String,
        default: 'ru',
    },

    error: {
        type: String,
        default: '',
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ==========================================================
 * HISTORY
 * ========================================================== */

/**
 * Нормализованный массив записей истории.
 *
 * MarketRecentlyViewedProductResource::collection()
 * с paginator приходит в history.data.
 */
const historyList = computed(() => {
    if (Array.isArray(props.history)) {
        return props.history
    }

    if (Array.isArray(props.history?.data)) {
        return props.history.data
    }

    return []
})

/**
 * Количество найденных записей
 * с учётом текущего server-side поиска.
 */
const historyFound = computed(() => {
    const metaTotal = Number(
        props.history?.meta?.total
    )

    if (Number.isFinite(metaTotal)) {
        return metaTotal
    }

    const total = Number(
        props.history?.total
    )

    if (Number.isFinite(total)) {
        return total
    }

    return historyList.value.length
})

/* ==========================================================
 * FILTERS
 * ========================================================== */

/**
 * Поисковая строка.
 *
 * ServerSearchInput самостоятельно
 * выполняет server-side поиск.
 */
const searchQuery = ref(
    props.search || ''
)

/**
 * Количество строк на странице.
 */
const itemsPerPage = ref(
    Number(props.filters?.per_page || 20)
)

/**
 * Текущая сортировка.
 */
const sortParam = ref(
    String(props.filters?.sort || 'viewedAtDesc')
)

/* ==========================================================
 * SERVER NAVIGATION
 * ========================================================== */

/**
 * Перезагрузить историю,
 * сохранив активный поиск,
 * количество строк и сортировку.
 *
 * Используется только при изменении:
 * - per_page;
 * - sort.
 *
 * Сам поиск обрабатывает ServerSearchInput.
 */
const reloadHistory = (overrides = {}) => {
    router.get(
        window.location.pathname,
        {
            search: searchQuery.value || undefined,
            per_page: itemsPerPage.value,
            sort: sortParam.value || undefined,
            page: undefined,

            ...overrides,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

/**
 * Изменение количества строк.
 */
const updateItemsPerPage = (value) => {
    const perPage = Number(value)

    if (!Number.isFinite(perPage)) {
        return
    }

    itemsPerPage.value = perPage

    reloadHistory({
        per_page: perPage,
    })
}

/**
 * Изменение сортировки.
 */
const updateSort = (value) => {
    const nextSort = String(
        value || 'viewedAtDesc'
    )

    sortParam.value = nextSort

    reloadHistory({
        sort: nextSort,
    })
}

/* ==========================================================
 * USER
 * ========================================================== */

/**
 * Имя пользователя.
 */
const userName = computed(() => {
    return props.user?.name || '—'
})

/**
 * Email пользователя.
 */
const userEmail = computed(() => {
    return props.user?.email || '—'
})

/**
 * Инициалы пользователя
 * для fallback-аватара.
 */
const userInitials = computed(() => {
    const name = String(
        props.user?.name || ''
    ).trim()

    if (!name) {
        return '?'
    }

    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase()
})

/* ==========================================================
 * DATE
 * ========================================================== */

/**
 * Форматирование даты.
 */
const formatDate = (dateString) => {
    if (!dateString) {
        return '—'
    }

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return new Intl.DateTimeFormat(
        props.locale || 'ru',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }
    ).format(date)
}
</script>

<template>
    <AdminLayout :title="t('recentlyViewedProducts')">
        <!-- Заголовок страницы -->
        <template #header>
            <TitlePage>
                {{ t('recentlyViewedProducts') }}
            </TitlePage>
        </template>

        <div
            class="px-4 py-8 w-full max-w-12xl mx-auto
                   sm:px-6 lg:px-8"
        >
            <div
                class="p-4
                       bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- ==================================================
                     НАЗАД
                =================================================== -->

                <div
                    class="mb-2
                           sm:flex sm:items-center sm:justify-between"
                >
                    <DefaultButton
                        :href="route(
                            'admin.marketRecentlyViewedProducts.index'
                        )"
                    >
                        <template #icon>
                            <svg
                                class="w-4 h-4 mr-2
                                       fill-current text-slate-100
                                       shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                       .7.7 1.2 1.7 1.4 2.7l2-.3
                                       c-.2-1.5-.9-2.8-1.9-3.8
                                       C10.1.4 5.7.4 2.9 3.1L.7.9
                                       0 7.3l6.4-.7-2.1-2.1z
                                       M15.6 8.7l-6.4.7 2.1 2.1
                                       c-1.9 1.9-5.1 1.9-7 0
                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3
                                       c.2 1.5.9 2.8 1.9 3.8
                                       1.4 1.4 3.1 2 4.9 2
                                       1.8 0 3.6-.7 4.9-2l2.2 2.2
                                       .8-6.4z"
                                />
                            </svg>
                        </template>

                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <!-- ==================================================
                     ПОЛЬЗОВАТЕЛЬ
                =================================================== -->

                <div
                    class="mb-4 flex flex-col gap-4
                           rounded-sm border
                           border-slate-300 dark:border-slate-500
                           bg-white dark:bg-slate-800
                           p-4
                           lg:flex-row
                           lg:items-center
                           lg:justify-between"
                >
                    <!-- Пользователь -->
                    <div class="flex min-w-0 items-center gap-3">
                        <!-- Аватар -->
                        <img
                            v-if="user?.profile_photo_url"
                            :src="user.profile_photo_url"
                            :alt="userName"
                            class="h-12 w-12 shrink-0
                                   rounded-full object-cover
                                   ring-2 ring-slate-200
                                   dark:ring-slate-600"
                        />

                        <!-- Fallback-аватар -->
                        <div
                            v-else
                            class="flex h-12 w-12 shrink-0
                                   items-center justify-center
                                   rounded-full
                                   bg-indigo-100
                                   text-sm font-bold text-indigo-700
                                   ring-2 ring-indigo-200
                                   dark:bg-indigo-900/50
                                   dark:text-indigo-300
                                   dark:ring-indigo-700"
                        >
                            {{ userInitials }}
                        </div>

                        <!-- Данные пользователя -->
                        <div class="min-w-0">
                            <div
                                class="truncate
                                       text-base font-semibold
                                       text-slate-800
                                       dark:text-slate-100"
                            >
                                {{ userName }}
                            </div>

                            <div
                                class="truncate
                                       text-xs font-semibold
                                       text-slate-500
                                       dark:text-slate-300"
                            >
                                {{ userEmail }}
                            </div>

                            <div
                                class="mt-0.5
                                       text-[11px] font-semibold
                                       text-slate-400
                                       dark:text-slate-400"
                            >
                                {{ t('id') }}:
                                {{ user?.id ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- Статистика -->
                    <div
                        class="grid grid-cols-1 gap-2
                               sm:grid-cols-2"
                    >
                        <!-- Просмотрено товаров -->
                        <div
                            class="min-w-44 px-4 py-2
                                   rounded-sm
                                   border
                                   border-cyan-400
                                   dark:border-cyan-200
                                   bg-cyan-200
                                   dark:bg-cyan-800
                                   text-center"
                        >
                            <div
                                class="text-[10px] font-semibold
                                       uppercase tracking-wide
                                       text-slate-800
                                       dark:text-slate-200"
                            >
                                {{ t('recentlyViewedProducts') }}
                            </div>

                            <div
                                class="mt-1
                                       text-lg font-bold
                                       text-gray-700
                                       dark:text-gray-300"
                            >
                                {{ historyCount }}
                            </div>
                        </div>

                        <!-- Последний просмотр -->
                        <div
                            class="min-w-44 px-4 py-2
                                   rounded-sm
                                   border
                                   border-indigo-400
                                   dark:border-indigo-200
                                   bg-indigo-50
                                   dark:bg-indigo-900
                                   text-center"
                        >
                            <div
                                class="text-[10px] font-semibold
                                       uppercase tracking-wide
                                       text-slate-800
                                       dark:text-slate-200"
                            >
                                {{ t('lastViewedAt') }}
                            </div>

                            <div
                                class="mt-1
                                       text-xs font-bold
                                       text-indigo-700
                                       dark:text-indigo-300"
                            >
                                {{ formatDate(lastViewedAt) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================================================
                     ПОИСК
                =================================================== -->

                <ServerSearchInput
                    v-if="historyCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <!-- ==================================================
                     КОЛИЧЕСТВО СТРОК / СОРТИРОВКА
                =================================================== -->

                <div
                    v-if="historyCount"
                    class="my-3
                           flex flex-col
                           items-center justify-between
                           gap-3
                           md:flex-row"
                >
                    <!-- Количество строк -->
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="updateItemsPerPage"
                    />

                    <!-- Сортировка -->
                    <SortSelect
                        :sort-param="sortParam"
                        @update:sortParam="updateSort"
                    />
                </div>

                <!-- ==================================================
                     СЧЁТЧИКИ
                =================================================== -->

                <div
                    v-if="historyCount"
                    class="mb-3
                           flex flex-col
                           items-center justify-between
                           gap-2
                           lg:flex-row"
                >
                    <!-- Всего -->
                    <div class="flex items-center gap-2">
                        <span
                            class="text-xs font-semibold
                                   text-slate-500
                                   dark:text-slate-300"
                        >
                            {{ t('total') }}:
                        </span>

                        <CountTable>
                            {{ historyCount }}
                        </CountTable>
                    </div>

                    <!-- Найдено -->
                    <div
                        v-if="searchQuery"
                        class="inline-flex items-center gap-2
                               rounded-sm border
                               border-emerald-200
                               bg-emerald-50
                               px-3 py-1
                               text-xs font-semibold
                               text-emerald-700
                               dark:border-emerald-800
                               dark:bg-emerald-950/40
                               dark:text-emerald-300"
                    >
                        <span>
                            {{ t('found') }}:
                        </span>

                        <span class="font-bold">
                            {{ historyFound }}
                        </span>
                    </div>
                </div>

                <!-- ==================================================
                     ВЕРХНЯЯ ПАГИНАЦИЯ
                =================================================== -->

                <div
                    v-if="historyFound"
                    class="mt-3
                           flex items-center justify-center"
                >
                    <AdminServerPagination
                        :pagination="history"
                    />
                </div>

                <!-- ==================================================
                     ТАБЛИЦА
                =================================================== -->

                <div class="mt-3">
                    <MarketRecentlyViewedProductTable
                        :history="historyList"
                    />
                </div>

                <!-- ==================================================
                     НИЖНЯЯ ПАГИНАЦИЯ
                =================================================== -->

                <div
                    v-if="historyFound"
                    class="mt-3
                           flex items-center justify-center"
                >
                    <AdminServerPagination
                        :pagination="history"
                    />
                </div>

                <!-- ==================================================
                     ОШИБКА
                =================================================== -->

                <div
                    v-if="props.error"
                    class="mt-3
                           text-sm font-semibold
                           text-rose-700
                           dark:text-rose-300"
                >
                    {{ props.error }}
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
