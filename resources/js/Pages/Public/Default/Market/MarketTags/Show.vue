<script setup>
/**
 * Страница конкретного тега товаров маркетплейса.
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

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    tag: { type: Object, default: () => ({}) },
    categoryTree: { type: Array, default: () => [] },

    locale: { type: String, default: 'ru' },
})

/* ===================== TAG ===================== */

/** Текущий тег */
const tag = computed(() => props.tag || {})

/** Активный перевод тега */
const tagTranslation = computed(() => {
    return tag.value.translation || {}
})

/** Название тега */
const tagTitle = computed(() => {
    return tagTranslation.value.title
        || tag.value.title
        || ''
})

/** Подзаголовок */
const tagSubtitle = computed(() => {
    return tagTranslation.value.subtitle
        || tag.value.subtitle
        || ''
})

/** Краткое описание */
const tagShort = computed(() => {
    return tagTranslation.value.short
        || tag.value.short
        || ''
})

/** Полное описание */
const tagDescription = computed(() => {
    return tagTranslation.value.description
        || tag.value.description
        || tagShort.value
        || ''
})

/** Количество товаров */
const productsCount = computed(() => {
    return Number(tag.value.products_count ?? 0)
})

/** Проверка SVG-иконки */
const hasSvgIcon = computed(() => {
    if (!tag.value?.icon) {
        return false
    }

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(tag.value.icon)
    )
})

/* ===================== SEO ===================== */

/** SEO заголовок */
const tagMetaTitle = computed(() => {
    return tagTranslation.value.meta_title
        || tag.value.meta_title
        || tagTitle.value
})

/** SEO ключевые слова */
const tagMetaKeywords = computed(() => {
    return tagTranslation.value.meta_keywords
        || tag.value.meta_keywords
        || ''
})

/** SEO описание */
const tagMetaDesc = computed(() => {
    return tagTranslation.value.meta_desc
        || tag.value.meta_desc
        || tagShort.value
        || ''
})

/* ===================== PAGE ===================== */

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Дерево категорий */
const categoryTree = computed(() => {
    return Array.isArray(props.categoryTree)
        ? props.categoryTree
        : []
})

/* ===================== SIDEBARS ===================== */

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
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ tagMetaTitle }}</title>

        <meta name="title" :content="tagMetaTitle" />
        <meta name="keywords" :content="tagMetaKeywords" />
        <meta name="description" :content="tagMetaDesc" />

        <meta property="og:title" :content="tagMetaTitle" />
        <meta property="og:description" :content="tagMetaDesc" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/catalog/tags/${tag.url || ''}`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="tagMetaTitle" />
        <meta name="twitter:description" :content="tagMetaDesc" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="tagMetaTitle" />
        <meta name="DC.description" :content="tagMetaDesc" />
        <meta name="DC.identifier" :content="`/${locale}/catalog/tags/${tag.url || ''}`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
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
                        <nav class="text-sm" aria-label="Breadcrumb">
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

                                <li>
                                    <span class="mx-2 breadcrumbs">/</span>
                                </li>

                                <li class="breadcrumbs">
                                    {{ t('tag') }} #{{ tagTitle }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Товары / название / просмотры -->
                        <div class="flex items-center justify-between gap-1">

                            <!-- Количество товаров -->
                            <div
                                :title="t('products')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-sky-600/85 dark:text-sky-200/85"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z" />
                                </svg>

                                <span class="text-center text-sm text-gray-500">
                                    {{ productsCount }}
                                </span>
                            </div>

                            <!-- Название -->
                            <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                                <span
                                    v-if="hasSvgIcon"
                                    class="flex"
                                    v-html="tag.icon"
                                />

                                <h1
                                    class="text-2xl font-bold"
                                    :style="tag.color ? { color: tag.color } : undefined"
                                >
                                    #{{ tagTitle }}
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
                                    {{ tag.views || 0 }}
                                </span>
                            </div>
                        </div>

                        <!-- Подзаголовок -->
                        <div
                            v-if="tagSubtitle"
                            class="mt-1 text-sm subtitle text-center"
                        >
                            {{ tagSubtitle }}
                        </div>

                        <!-- Описание -->
                        <div
                            v-if="tagDescription"
                            class="mt-1 mb-3 text-sm subtitle text-center"
                        >
                            {{ tagDescription }}
                        </div>

                        <!-- Товары тега -->
                        <div
                            class="my-5 flex flex-wrap items-center justify-center gap-3
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

                        <!-- Черновой блок товаров -->
                        <div
                            class="rounded-md border border-dashed border-gray-300
                                   px-4 py-8 text-center
                                   dark:border-gray-700"
                        >
                            <div
                                class="text-sm font-semibold
                                       text-slate-600 dark:text-slate-300"
                            >
                                {{ t('products') }}
                            </div>

                            <div
                                class="mt-2 text-xs
                                       text-slate-500 dark:text-slate-400"
                            >
                                Список товаров с этим тегом будет подключён после реализации публичного каталога товаров.
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

        <!-- Подвал -->
        <FooterBlog />

        <!-- Прогресс -->
        <Progress />
    </DefaultLayout>
</template>
