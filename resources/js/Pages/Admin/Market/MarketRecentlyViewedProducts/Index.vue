<script setup>
/**
 * Пользователи с историей просмотренных товаров — Index
 *
 * Read-only административная страница:
 * - список пользователей с историей просмотров;
 * - серверный поиск;
 * - серверная сортировка;
 * - выбор количества строк;
 * - серверная пагинация;
 * - переход к истории конкретного пользователя.
 */

import { computed, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
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

import ItemsPerPageSelect
    from '@/Components/Admin/Market/MarketRecentlyViewedProduct/Select/ItemsPerPageSelect.vue'

const { t, locale } = useI18n()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    /**
     * Серверная пагинированная коллекция пользователей,
     * имеющих историю просмотренных товаров.
     */
    users: {
        type: [Array, Object],
        default: () => [],
    },

    /**
     * Общее количество пользователей,
     * имеющих историю просмотров.
     */
    usersCount: {
        type: Number,
        default: 0,
    },

    /**
     * Текущая строка server-side поиска.
     */
    search: {
        type: String,
        default: '',
    },

    /**
     * Остальные server-side фильтры.
     */
    filters: {
        type: Object,
        default: () => ({}),
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
 * USERS
 * ========================================================== */

/**
 * Нормализованный массив пользователей.
 *
 * Laravel paginator приходит в users.data.
 */
const usersList = computed(() => {
    if (Array.isArray(props.users)) {
        return props.users
    }

    if (Array.isArray(props.users?.data)) {
        return props.users.data
    }

    return []
})

/**
 * Количество найденных пользователей
 * с учётом текущего server-side поиска.
 */
const usersFound = computed(() => {
    const metaTotal = Number(
        props.users?.meta?.total
    )

    if (Number.isFinite(metaTotal)) {
        return metaTotal
    }

    const total = Number(
        props.users?.total
    )

    if (Number.isFinite(total)) {
        return total
    }

    return usersList.value.length
})

/* ==========================================================
 * FILTERS
 * ========================================================== */

/**
 * Поисковая строка.
 *
 * ServerSearchInput самостоятельно
 * выполняет server-side поиск
 * через query-параметр search.
 */
const searchQuery = ref(
    props.search || ''
)

/**
 * Количество пользователей на странице.
 */
const itemsPerPage = ref(
    Number(props.filters?.per_page || 20)
)

/**
 * Текущая сортировка пользователей.
 */
const sortParam = ref(
    String(
        props.filters?.sort
        || 'lastViewedAtDesc'
    )
)

/* ==========================================================
 * SERVER NAVIGATION
 * ========================================================== */

/**
 * Перезагрузить список пользователей,
 * сохранив активный поиск,
 * количество строк и сортировку.
 *
 * Используется только при изменении:
 * - per_page;
 * - sort.
 *
 * Сам поиск обрабатывает ServerSearchInput.
 */
const reloadUsers = (overrides = {}) => {
    router.get(
        window.location.pathname,
        {
            search: searchQuery.value || undefined,
            per_page: itemsPerPage.value,
            sort: sortParam.value || undefined,

            /**
             * При изменении сортировки
             * или количества строк
             * возвращаемся на первую страницу.
             */
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

    reloadUsers({
        per_page: perPage,
    })
}

/**
 * Изменение сортировки.
 */
const updateSort = (value) => {
    const nextSort = String(
        value || 'lastViewedAtDesc'
    )

    sortParam.value = nextSort

    reloadUsers({
        sort: nextSort,
    })
}

/* ==========================================================
 * USER HELPERS
 * ========================================================== */

/**
 * Инициалы пользователя
 * для fallback-аватара.
 */
const userInitials = (user) => {
    const name = String(
        user?.name || ''
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
}

/**
 * Количество просмотренных
 * пользователем уникальных товаров.
 */
const viewedCount = (user) => {
    return Number(
        user?.recently_viewed_market_products_count
        ?? 0
    )
}

/**
 * Ссылка на историю просмотров
 * конкретного пользователя.
 */
const showRoute = (user) => {
    return route(
        'admin.users.marketRecentlyViewedProducts.show',
        {
            user: user.id,
        }
    )
}

/* ==========================================================
 * DATE
 * ========================================================== */

/**
 * Форматирование даты последнего просмотра.
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
        locale.value || 'ru',
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
                     ПОИСК
                =================================================== -->

                <ServerSearchInput
                    v-if="usersCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <!-- ==================================================
                     КОЛИЧЕСТВО СТРОК / СОРТИРОВКА
                =================================================== -->

                <div
                    v-if="usersCount"
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
                    <div
                        class="flex items-center justify-center
                               h-fit my-2"
                    >
                        <label
                            for="recentlyViewedUsersSort"
                            class="hidden lg:block
                                   sm:mr-2
                                   tracking-wider
                                   text-sm font-semibold
                                   text-slate-600
                                   dark:text-slate-100"
                        >
                            {{ t('sort') }}
                        </label>

                        <select
                            id="recentlyViewedUsersSort"
                            :value="sortParam"
                            @change="updateSort($event.target.value)"
                            class="w-56 px-3 py-0.5
                                   form-select
                                   bg-white dark:bg-gray-200
                                   text-gray-600 dark:text-gray-900
                                   border border-slate-400
                                   dark:border-slate-600
                                   rounded-sm shadow-sm"
                        >
                            <!-- Последний просмотр -->
                            <option value="lastViewedAtDesc">
                                {{ t('lastViewedAt') }} ↓
                            </option>

                            <option value="lastViewedAtAsc">
                                {{ t('lastViewedAt') }} ↑
                            </option>

                            <option disabled>
                                ─────────────────
                            </option>

                            <!-- Количество просмотренных товаров -->
                            <option value="viewsCountDesc">
                                {{ t('recentlyViewedProducts') }} 9→0
                            </option>

                            <option value="viewsCountAsc">
                                {{ t('recentlyViewedProducts') }} 0→9
                            </option>

                            <option disabled>
                                ─────────────────
                            </option>

                            <!-- Имя пользователя -->
                            <option value="nameAsc">
                                {{ t('user') }} A→Z
                            </option>

                            <option value="nameDesc">
                                {{ t('user') }} Z→A
                            </option>

                            <!-- Email -->
                            <option value="emailAsc">
                                Email A→Z
                            </option>

                            <option value="emailDesc">
                                Email Z→A
                            </option>

                            <option disabled>
                                ─────────────────
                            </option>

                            <!-- ID -->
                            <option value="idDesc">
                                ID ↓
                            </option>

                            <option value="idAsc">
                                ID ↑
                            </option>
                        </select>
                    </div>
                </div>

                <!-- ==================================================
                     СЧЁТЧИКИ
                =================================================== -->

                <div
                    v-if="usersCount"
                    class="mb-3
                           flex flex-col
                           items-center justify-between
                           gap-2
                           lg:flex-row"
                >
                    <!-- Всего пользователей -->
                    <div class="flex items-center gap-2">
                        <span
                            class="text-xs font-semibold
                                   text-slate-500
                                   dark:text-slate-300"
                        >
                            {{ t('total') }}:
                        </span>

                        <CountTable>
                            {{ usersCount }}
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
                            {{ usersFound }}
                        </span>
                    </div>
                </div>

                <!-- ==================================================
                     ВЕРХНЯЯ ПАГИНАЦИЯ
                =================================================== -->

                <div
                    v-if="usersFound"
                    class="mt-3
                           flex items-center justify-center"
                >
                    <AdminServerPagination
                        :pagination="users"
                    />
                </div>

                <!-- ==================================================
                     ТАБЛИЦА
                =================================================== -->

                <div
                    class="mt-3
                           bg-white dark:bg-slate-700
                           shadow-lg rounded-sm
                           border border-slate-200
                           dark:border-slate-600
                           relative"
                >
                    <div class="overflow-x-auto">
                        <table
                            v-if="usersList.length"
                            class="table-auto w-full
                                   text-slate-700
                                   dark:text-slate-100"
                        >
                            <thead
                                class="text-sm uppercase
                                       bg-slate-200 dark:bg-cyan-900
                                       border border-solid
                                       border-gray-300
                                       dark:border-gray-700"
                            >
                            <tr>
                                <!-- ID -->
                                <th
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-3
                                               whitespace-nowrap
                                               w-px"
                                >
                                    <div
                                        class="font-medium text-center"
                                    >
                                        {{ t('id') }}
                                    </div>
                                </th>

                                <!-- Пользователь -->
                                <th
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-3
                                               whitespace-nowrap"
                                >
                                    <div
                                        class="font-medium text-left"
                                    >
                                        {{ t('user') }}
                                    </div>
                                </th>

                                <!-- Email -->
                                <th
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-3
                                               whitespace-nowrap"
                                >
                                    <div
                                        class="font-medium text-left"
                                    >
                                        Email
                                    </div>
                                </th>

                                <!-- Количество просмотренных товаров -->
                                <th
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-3
                                               whitespace-nowrap"
                                >
                                    <div
                                        class="font-medium text-center"
                                    >
                                        {{ t('recentlyViewedProducts') }}
                                    </div>
                                </th>

                                <!-- Последний просмотр -->
                                <th
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-3
                                               whitespace-nowrap"
                                >
                                    <div
                                        class="font-medium text-center"
                                    >
                                        {{ t('lastViewedAt') }}
                                    </div>
                                </th>

                                <!-- Действие -->
                                <th
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-3
                                               whitespace-nowrap
                                               w-px"
                                >
                                    <div
                                        class="font-medium text-center"
                                    >
                                        {{ t('actions') }}
                                    </div>
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr
                                v-for="user in usersList"
                                :key="user.id"
                                class="text-sm font-semibold
                                           border-b-2
                                           hover:bg-slate-100
                                           dark:hover:bg-cyan-800"
                            >
                                <!-- ID -->
                                <td
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-2
                                               whitespace-nowrap"
                                >
                                    <div class="text-center">
                                        {{ user.id }}
                                    </div>
                                </td>

                                <!-- Пользователь -->
                                <td
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-2
                                               min-w-52"
                                >
                                    <div
                                        class="flex items-center gap-3"
                                    >
                                        <!-- Аватар -->
                                        <img
                                            v-if="user.profile_photo_url"
                                            :src="user.profile_photo_url"
                                            :alt="user.name || ''"
                                            class="h-9 w-9 shrink-0
                                                       rounded-full
                                                       object-cover
                                                       ring-1 ring-slate-200
                                                       dark:ring-slate-600"
                                        />

                                        <!-- Fallback-аватар -->
                                        <div
                                            v-else
                                            class="flex h-9 w-9 shrink-0
                                                       items-center
                                                       justify-center
                                                       rounded-full
                                                       bg-indigo-100
                                                       text-xs font-bold
                                                       text-indigo-700
                                                       dark:bg-indigo-900/50
                                                       dark:text-indigo-300"
                                        >
                                            {{ userInitials(user) }}
                                        </div>

                                        <!-- Имя -->
                                        <div class="min-w-0">
                                            <Link
                                                :href="showRoute(user)"
                                                class="block truncate
                                                           font-semibold
                                                           text-blue-700
                                                           hover:underline
                                                           dark:text-blue-300"
                                            >
                                                {{ user.name || '—' }}
                                            </Link>

                                            <div
                                                class="mt-0.5
                                                           text-[10px]
                                                           text-slate-500
                                                           dark:text-slate-300"
                                            >
                                                ID:
                                                {{ user.id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-2"
                                >
                                    <div
                                        class="text-xs font-semibold
                                                   text-slate-600
                                                   dark:text-slate-200
                                                   break-all"
                                    >
                                        {{ user.email || '—' }}
                                    </div>
                                </td>

                                <!-- Количество просмотренных товаров -->
                                <td
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-2
                                               whitespace-nowrap"
                                >
                                    <div
                                        class="flex justify-center"
                                    >
                                            <span
                                                class="inline-flex
                                                       items-center
                                                       justify-center
                                                       min-w-8
                                                       px-2 py-1
                                                       rounded-full
                                                       border
                                                       border-cyan-300
                                                       bg-cyan-50
                                                       text-xs font-bold
                                                       text-cyan-700
                                                       dark:border-cyan-700
                                                       dark:bg-cyan-950/40
                                                       dark:text-cyan-300"
                                            >
                                                {{ viewedCount(user) }}
                                            </span>
                                    </div>
                                </td>

                                <!-- Последний просмотр -->
                                <td
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-2
                                               whitespace-nowrap"
                                >
                                    <div class="text-center">
                                            <span
                                                class="inline-flex
                                                       rounded-sm border
                                                       border-indigo-200
                                                       bg-indigo-50
                                                       px-2 py-1
                                                       text-xs font-semibold
                                                       text-indigo-700
                                                       dark:border-indigo-800
                                                       dark:bg-indigo-950/40
                                                       dark:text-indigo-300"
                                            >
                                                {{
                                                    formatDate(
                                                        user.last_viewed_at
                                                    )
                                                }}
                                            </span>
                                    </div>
                                </td>

                                <!-- Просмотр истории -->
                                <td
                                    class="px-2
                                               first:pl-5 last:pr-5
                                               py-2
                                               whitespace-nowrap"
                                >
                                    <div
                                        class="flex justify-center"
                                    >
                                        <Link
                                            :href="showRoute(user)"
                                            :title="t('view')"
                                            class="flex
                                                       items-center
                                                       justify-center
                                                       rounded-sm
                                                       px-1 py-1
                                                       border
                                                       border-blue-500
                                                       dark:border-blue-300
                                                       text-blue-600
                                                       dark:text-blue-300
                                                       transition
                                                       hover:border-blue-700
                                                       hover:bg-blue-100
                                                       dark:hover:bg-cyan-900"
                                        >
                                            <svg
                                                class="w-4 h-4
                                                           shrink-0
                                                           fill-current
                                                           text-blue-500
                                                           mx-1"
                                                viewBox="0 0 16 16"
                                            >
                                                <path
                                                    d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"
                                                />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Нет данных -->
                        <div
                            v-else
                            class="p-5
                                   text-center
                                   text-slate-700
                                   dark:text-slate-100"
                        >
                            {{ t('noData') }}
                        </div>
                    </div>
                </div>

                <!-- ==================================================
                     НИЖНЯЯ ПАГИНАЦИЯ
                =================================================== -->

                <div
                    v-if="usersFound"
                    class="mt-3
                           flex items-center justify-center"
                >
                    <AdminServerPagination
                        :pagination="users"
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
