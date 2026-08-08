<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/** Props */
defineProps({
    categories: { type: Array, default: () => [] },
})

/** Ссылка на категорию */
const categoryLink = (category) => {
    return category?.url
        ? route('public.marketCategories.show', { url: category.url })
        : '#'
}

/** Название категории */
const getCategoryTitle = (category) => {
    return category?.title
        || category?.translation?.title
        || category?.translations?.[0]?.title
        || ''
}

/** Подзаголовок категории */
const getCategorySubtitle = (category) => {
    return category?.subtitle
        || category?.translation?.subtitle
        || category?.translations?.[0]?.subtitle
        || ''
}

/** Краткое описание */
const getCategoryShort = (category) => {
    return category?.short
        || category?.translation?.short
        || category?.translations?.[0]?.short
        || ''
}

/** Количество дочерних категорий */
const childrenCount = (category) => {
    return Number(category?.children_count ?? 0)
}

/** Количество товаров */
const productsCount = (category) => {
    return Number(category?.products_count ?? 0)
}

/** Есть SVG-иконка */
const hasSvgIcon = (category) => {
    return typeof category?.icon === 'string'
        && category.icon.trim().startsWith('<svg')
}
</script>

<template>
    <div class="space-y-4">
        <div
            v-for="category in categories"
            :key="category.id"
            class="group flex flex-col gap-3 rounded-md
                   border border-gray-200 bg-white p-3 shadow-sm
                   transition hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900
                   sm:flex-row"
        >
            <!-- Изображение -->
            <Link
                :href="categoryLink(category)"
                class="shrink-0"
            >
                <div
                    class="w-full overflow-hidden rounded-md
                           border border-gray-400 dark:border-gray-600
                           sm:w-60"
                >
                    <img
                        v-if="category.thumbnail_url"
                        :src="category.thumbnail_url"
                        :alt="getCategoryTitle(category)"
                        loading="lazy"
                        class="h-44 w-full object-cover transition
                               duration-300 group-hover:scale-105"
                    />

                    <!-- Заглушка -->
                    <div
                        v-else
                        class="flex h-44 w-full items-center justify-center
                               bg-slate-100 text-slate-400
                               dark:bg-slate-800 dark:text-slate-500"
                    >
                        <span
                            v-if="hasSvgIcon(category)"
                            class="flex h-12 w-12 items-center justify-center"
                            v-html="category.icon"
                        />

                        <svg
                            v-else
                            class="h-12 w-12"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" />
                        </svg>
                    </div>
                </div>
            </Link>

            <!-- Основная информация -->
            <div class="flex min-w-0 flex-1 flex-col justify-around">
                <div class="min-w-0 flex-1">
                    <!-- Название -->
                    <div class="flex items-start justify-between gap-3">
                        <Link
                            :href="categoryLink(category)"
                            :title="`${t('products')}: ${productsCount(category)}`"
                            class="inline-flex min-w-0 items-center gap-2"
                        >
                            <span
                                v-if="hasSvgIcon(category)"
                                class="flex shrink-0"
                                v-html="category.icon"
                            />

                            <span
                                class="truncate text-lg font-semibold
                                       text-slate-900/85 group-hover:opacity-75
                                       dark:text-slate-100/85"
                            >
                                {{ getCategoryTitle(category) }}
                            </span>
                        </Link>

                        <!-- Уровень -->
                        <div
                            v-if="category.level"
                            class="shrink-0 rounded-sm border border-slate-400
                                   px-2 py-1 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400"
                        >
                            {{ t('level') }}:
                            {{ category.level }}
                        </div>
                    </div>

                    <!-- Подзаголовок -->
                    <div
                        v-if="getCategorySubtitle(category)"
                        class="mt-1 text-xs font-semibold
                               text-slate-500 dark:text-slate-400"
                    >
                        {{ getCategorySubtitle(category) }}
                    </div>

                    <!-- Краткое описание -->
                    <div
                        v-if="getCategoryShort(category)"
                        class="mt-2 line-clamp-2 text-sm
                               text-slate-700 dark:text-slate-300"
                    >
                        {{ getCategoryShort(category) }}
                    </div>
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400"
                >
                    <!-- Подкатегории -->
                    <div
                        v-if="childrenCount(category)"
                        class="flex items-center justify-center gap-1
                               rounded-sm border border-slate-600
                               px-2 py-1 dark:border-slate-400"
                        :title="t('subheadings')"
                    >
                        <svg
                            class="h-3 w-3 shrink-0
                                   text-fuchsia-600/85 dark:text-fuchsia-200/85"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path d="M4 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm6-12h10v2H10V5Zm0 7h10v2H10v-2Zm0 7h10v2H10v-2Z" />
                        </svg>

                        {{ t('subheadings') }}:
                        {{ childrenCount(category) }}
                    </div>

                    <!-- Товары -->
                    <div
                        v-if="productsCount(category)"
                        class="flex items-center justify-center gap-1
                               rounded-sm border border-slate-600
                               px-2 py-1 dark:border-slate-400"
                        :title="t('products')"
                    >
                        <svg
                            class="h-3 w-3 shrink-0
                                   text-sky-600/85 dark:text-sky-200/85"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z" />
                        </svg>

                        {{ t('products') }}:
                        {{ productsCount(category) }}
                    </div>

                    <!-- Изображения -->
                    <div
                        v-if="category.images_count"
                        class="flex items-center justify-center gap-1
                               rounded-sm border border-slate-600
                               px-2 py-1 dark:border-slate-400"
                        :title="t('images')"
                    >
                        <svg
                            class="h-3 w-3 shrink-0
                                   text-emerald-600/85 dark:text-emerald-200/85"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path d="M4 3h16a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm0 2v11.2l4.2-4.2 3 3 2.8-2.8L20 18.2V5H4Zm3 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>

                        {{ category.images_count }}
                    </div>
                </div>

                <!-- Нижняя строка -->
                <div class="mt-3 flex items-center justify-end gap-3">
                    <Link
                        :href="categoryLink(category)"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-1 btn-default
                               sm:w-1/2"
                    >
                        <span class="text-sm font-semibold">
                            {{ t('readMore') }}
                        </span>

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
