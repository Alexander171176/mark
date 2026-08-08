<script setup>
/**
 * Страница конкретной категории товаров маркетплейса.
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { computed, ref, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarMarket from '@/Components/Public/Default/Partials/LeftSidebarMarket.vue'
import RightSidebarMarket from '@/Components/Public/Default/Partials/RightSidebarMarket.vue'
import MarketCategoryGrid from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryGrid.vue'
import MarketCategoryRows from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryRows.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    category: { type: Object, default: () => ({}) },
    categoryTree: { type: Array, default: () => [] },

    locale: { type: String, default: 'ru' },
})

/* ===================== CATEGORY ===================== */

/** Текущая категория */
const category = computed(() => props.category || {})

/** Текущий перевод */
const categoryTranslation = computed(() => {
    return category.value.translation || {}
})

/** Название категории */
const categoryTitle = computed(() => {
    return categoryTranslation.value.title
        || category.value.title
        || ''
})

/** Подзаголовок категории */
const categorySubtitle = computed(() => {
    return categoryTranslation.value.subtitle
        || category.value.subtitle
        || ''
})

/** Краткое описание */
const categoryShort = computed(() => {
    return categoryTranslation.value.short
        || category.value.short
        || ''
})

/** Полное описание */
const categoryDescription = computed(() => {
    return categoryTranslation.value.description
        || category.value.description
        || categoryShort.value
        || ''
})

/* ===================== SEO ===================== */

/** SEO title */
const categoryMetaTitle = computed(() => {
    return categoryTranslation.value.meta_title
        || category.value.meta_title
        || categoryTitle.value
})

/** SEO keywords */
const categoryMetaKeywords = computed(() => {
    return categoryTranslation.value.meta_keywords
        || category.value.meta_keywords
        || ''
})

/** SEO description */
const categoryMetaDesc = computed(() => {
    return categoryTranslation.value.meta_desc
        || category.value.meta_desc
        || categoryShort.value
        || ''
})

/* ===================== DATA ===================== */

/** Дерево категорий для левого сайдбара */
const categoryTree = computed(() => {
    return Array.isArray(props.categoryTree)
        ? props.categoryTree
        : []
})

/** Дочерние категории */
const childCategories = computed(() => {
    return Array.isArray(category.value.children)
        ? category.value.children
        : []
})

/** Изображения категории */
const categoryImages = computed(() => {
    return Array.isArray(category.value.images)
        ? category.value.images
        : []
})

/** Есть изображения */
const hasCategoryImages = computed(() => {
    return categoryImages.value.length > 0
})

/** Родительская категория */
const parentCategory = computed(() => {
    return category.value.parent || null
})

/** Количество дочерних категорий */
const childrenCount = computed(() => {
    return Number(category.value.children_count ?? childCategories.value.length ?? 0)
})

/** Количество товаров */
const productsCount = computed(() => {
    return Number(category.value.products_count ?? 0)
})

/** Проверка SVG-иконки */
const hasSvgIcon = computed(() => {
    if (!category.value?.icon) {
        return false
    }

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(category.value.icon)
    )
})

/* ===================== VIEW ===================== */

/** Ключ режима отображения дочерних категорий */
const VIEW_KEY = 'public_market_category_children_view'

/** Режим отображения */
const viewMode = ref(
    localStorage.getItem(VIEW_KEY) || 'grid'
)

/** Сохраняем режим отображения */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/* ===================== SIDEBARS ===================== */

/** Глобальные настройки */
const page = usePage()
const siteSettings = page.props?.siteSettings || {}

/** Показ левой колонки */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
})

/** Показ правой колонки */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
})

/** Ключ левого сайдбара */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'

/** Ключ правого сайдбара */
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/** Получение boolean из localStorage */
const getStoredBoolean = (key, defaultValue = true) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/** Левый сайдбар по умолчанию свернут */
const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, true)
)

/** Правый сайдбар по умолчанию свернут */
const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, true)
)

/** Сохраняем состояние сайдбаров */
watch([leftCollapsed, rightCollapsed], () => {
    localStorage.setItem(LEFT_SIDEBAR_KEY, String(leftCollapsed.value))
    localStorage.setItem(RIGHT_SIDEBAR_KEY, String(rightCollapsed.value))
})

/**
 * Количество колонок дочерних категорий.
 *
 * Оба открыты  → 2.
 * Один свернут → 3.
 * Оба свернуты → 4.
 */
const categoryGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})
</script>

<template>
    <Head>
        <title>{{ categoryMetaTitle }}</title>

        <meta name="title" :content="categoryMetaTitle" />
        <meta name="keywords" :content="categoryMetaKeywords" />
        <meta name="description" :content="categoryMetaDesc" />

        <meta property="og:title" :content="categoryMetaTitle" />
        <meta property="og:description" :content="categoryMetaDesc" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/catalog/categories/${category.url}`" />
        <meta property="og:image" :content="categoryImages?.[0]?.url || ''" />
        <meta property="og:locale" :content="locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="categoryMetaTitle" />
        <meta name="twitter:description" :content="categoryMetaDesc" />
        <meta name="twitter:image" :content="categoryImages?.[0]?.url || ''" />

        <meta name="DC.title" :content="categoryMetaTitle" />
        <meta name="DC.description" :content="categoryMetaDesc" />
        <meta name="DC.identifier" :content="`/${locale}/catalog/categories/${category.url}`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- Левая колонка -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarMarket
                        :category-tree="categoryTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- Центральная колонка -->
                <div class="w-full lg:mt-28 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Хлебные крошки -->
                        <nav class="mb-3 text-sm" aria-label="Breadcrumb">
                            <ol class="flex flex-wrap items-center font-semibold">
                                <li>
                                    <Link
                                        :href="route('home')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('home') }}
                                    </Link>
                                </li>

                                <li>
                                    <span class="mx-2 breadcrumbs">/</span>
                                </li>

                                <li>
                                    <Link
                                        :href="route('public.marketCategories.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('categories') }}
                                    </Link>
                                </li>

                                <template v-if="parentCategory">
                                    <li>
                                        <span class="mx-2 breadcrumbs">/</span>
                                    </li>

                                    <li>
                                        <Link
                                            :href="route('public.marketCategories.show', { url: parentCategory.url })"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ parentCategory.title || parentCategory.translation?.title }}
                                        </Link>
                                    </li>
                                </template>

                                <li>
                                    <span class="mx-2 breadcrumbs">/</span>
                                </li>

                                <li class="breadcrumbs">
                                    {{ categoryTitle }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Галерея -->
                        <div
                            v-if="hasCategoryImages"
                            class="flex items-center justify-center"
                        >
                            <div class="w-full">
                                <ImageGalleryMain
                                    :images="categoryImages"
                                    :alt="categoryTitle"
                                    rounded-class="rounded-lg"
                                    shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                    img-class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

                        <!-- Заголовок и статистика -->
                        <div class="flex items-center justify-between gap-1">

                            <!-- Подкатегории -->
                            <div
                                :title="t('subheadings')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-fuchsia-600/85 dark:text-fuchsia-200/85"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path d="M4 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm6-12h10v2H10V5Zm0 7h10v2H10v-2Zm0 7h10v2H10v-2Z" />
                                </svg>

                                <span class="text-center text-sm text-gray-500">
                                    {{ childrenCount }}
                                </span>
                            </div>

                            <!-- Название -->
                            <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                                <span
                                    v-if="hasSvgIcon"
                                    class="flex"
                                    v-html="category.icon"
                                />

                                <h1 class="text-2xl font-bold">
                                    {{ categoryTitle }}
                                </h1>
                            </div>

                            <!-- Просмотры -->
                            <div
                                :title="t('views')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    fill="currentColor"
                                >
                                    <path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" />
                                </svg>

                                <span class="text-center text-sm text-gray-500">
                                    {{ category.views || 0 }}
                                </span>
                            </div>
                        </div>

                        <!-- Подзаголовок -->
                        <div
                            v-if="categorySubtitle"
                            class="mb-2 text-center text-sm font-semibold
                                   text-slate-500 dark:text-slate-400"
                        >
                            {{ categorySubtitle }}
                        </div>

                        <!-- Описание -->
                        <div
                            v-if="categoryDescription"
                            class="mt-1 mb-3 text-sm subtitle text-center"
                        >
                            {{ categoryDescription }}
                        </div>

                        <!-- Черновой блок товаров -->
                        <div
                            class="my-5 flex items-center justify-center gap-3
                                   text-slate-700/85 dark:text-slate-300/85"
                        >
                            <svg
                                class="h-7 w-7 text-sky-600/85 dark:text-sky-200/85"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z" />
                            </svg>

                            <h2 class="text-xl font-semibold">
                                {{ t('products') }}
                            </h2>

                            <span class="text-sm text-gray-500">
                                {{ productsCount }}
                            </span>
                        </div>

                        <!-- Пока товары не подключены -->
                        <div
                            class="rounded-md border border-dashed border-gray-300
                                   px-4 py-6 text-center text-sm text-slate-500
                                   dark:border-gray-700 dark:text-slate-400"
                        >
                            {{ t('products') }} — блок будет подключён после реализации публичных товаров.
                        </div>

                        <!-- Дочерние категории -->
                        <div v-if="childCategories.length">
                            <div
                                class="mt-6 flex flex-wrap items-center justify-center gap-3
                                       text-slate-700/85 dark:text-slate-300/85"
                            >
                                <svg
                                    class="h-8 w-8 opacity-70"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path d="M4 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm6-12h10v2H10V5Zm0 7h10v2H10v-2Zm0 7h10v2H10v-2Z" />
                                </svg>

                                <h2 class="text-xl font-semibold">
                                    {{ t('subheadings') }}
                                </h2>
                            </div>

                            <!-- Переключатель пока используем простой -->
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-sm px-3 py-1 text-sm btn-default"
                                    @click="viewMode = 'grid'"
                                >
                                    Grid
                                </button>

                                <button
                                    type="button"
                                    class="rounded-sm px-3 py-1 text-sm btn-default"
                                    @click="viewMode = 'rows'"
                                >
                                    Rows
                                </button>
                            </div>

                            <div class="mt-4">
                                <MarketCategoryGrid
                                    v-if="viewMode === 'grid'"
                                    :categories="childCategories"
                                    :cols="categoryGridCols"
                                />

                                <MarketCategoryRows
                                    v-else
                                    :categories="childCategories"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Правая колонка -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarMarket
                        :collapsed="rightCollapsed"
                        @collapsed="rightCollapsed = $event"
                    />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
