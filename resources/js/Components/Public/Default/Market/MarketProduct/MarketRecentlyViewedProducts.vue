<script setup>
/**
 * Горизонтальный carousel недавно просмотренных товаров.
 *
 * - отдельный компонент от каталога и рекомендаций
 * - горизонтальная прокрутка
 * - кнопки назад / вперед
 * - touch / trackpad / mouse wheel через native overflow
 * - компактные карточки
 * - цена, бренд, наличие, признаки товара
 * - лайк
 */

import {
    nextTick,
    onMounted,
    onBeforeUnmount,
    ref, computed
} from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

const { t, locale } = useI18n()

/* ===================== PROPS ===================== */

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    }
})

/* ===================== SLIDER ===================== */

/** Контейнер горизонтальной ленты */
const sliderRef = ref(null)

/** Можно прокрутить влево */
const canScrollLeft = ref(false)

/** Можно прокрутить вправо */
const canScrollRight = ref(false)

/**
 * Проверить текущее положение carousel.
 */
const updateScrollState = () => {
    const slider = sliderRef.value

    if (!slider) {
        canScrollLeft.value = false
        canScrollRight.value = false
        return
    }

    const maxScrollLeft = slider.scrollWidth - slider.clientWidth

    canScrollLeft.value = slider.scrollLeft > 2

    canScrollRight.value =
        maxScrollLeft > 2
        && slider.scrollLeft < maxScrollLeft - 2
}

/**
 * Размер шага прокрутки.
 *
 * Прокручиваем примерно на ширину видимой области,
 * но оставляем часть предыдущей карточки для контекста.
 */
const scrollAmount = computed(() => {
    const slider = sliderRef.value

    if (!slider) {
        return 300
    }

    return Math.max(
        240,
        slider.clientWidth * 0.8
    )
})

/**
 * Прокрутить назад.
 *
 * Если carousel находится в начале,
 * переходим к последним карточкам.
 */
const scrollPrev = () => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

    const maxScrollLeft =
        slider.scrollWidth - slider.clientWidth

    /**
     * Мы уже в начале.
     * Переносим carousel в конец.
     */
    if (!canScrollLeft.value) {
        slider.scrollTo({
            left: maxScrollLeft,
            behavior: 'smooth'
        })

        return
    }

    slider.scrollBy({
        left: -scrollAmount.value,
        behavior: 'smooth'
    })
}

/**
 * Прокрутить вперед.
 *
 * Если carousel дошёл до конца,
 * возвращаемся к первой карточке.
 */
const scrollNext = () => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

    /**
     * Мы уже дошли до конца.
     * Возвращаем carousel в начало.
     */
    if (!canScrollRight.value) {
        slider.scrollTo({
            left: 0,
            behavior: 'smooth'
        })

        return
    }

    slider.scrollBy({
        left: scrollAmount.value,
        behavior: 'smooth'
    })
}

/** Блокировка повторного wheel во время анимации */
let wheelLocked = false

/**
 * Управление carousel колесом мыши.
 *
 * Колесо вниз  → следующий блок товаров.
 * Колесо вверх → предыдущий блок товаров.
 */
const handleWheel = (event) => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

    const maxScrollLeft =
        slider.scrollWidth - slider.clientWidth

    if (maxScrollLeft <= 0) {
        return
    }

    /**
     * Определяем основное направление.
     * Поддерживает обычную мышь и trackpad.
     */
    const delta =
        Math.abs(event.deltaY) >= Math.abs(event.deltaX)
            ? event.deltaY
            : event.deltaX

    if (Math.abs(delta) < 2) {
        return
    }

    event.preventDefault()

    /**
     * Одно физическое движение колеса может породить
     * несколько wheel-событий. Выполняем только один шаг.
     */
    if (wheelLocked) {
        return
    }

    wheelLocked = true

    if (delta > 0) {
        scrollNext()
    } else {
        scrollPrev()
    }

    window.setTimeout(() => {
        wheelLocked = false
    }, 350)
}

/**
 * Подключить управление колесом мыши.
 */
const bindWheel = () => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

    slider.addEventListener(
        'wheel',
        handleWheel,
        {
            passive: false
        }
    )
}

/**
 * Отключить управление колесом мыши.
 */
const unbindWheel = () => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

    slider.removeEventListener(
        'wheel',
        handleWheel
    )
}

/**
 * После изменения размеров окна
 * пересчитываем доступность стрелок.
 */
const handleResize = async () => {
    await nextTick()
    updateScrollState()
}

/* ===================== PRODUCT HELPERS ===================== */

/** Ссылка на товар */
const productLink = (product) => {
    return product?.url
        ? route('public.marketProducts.show', {
            url: product.url
        })
        : '#'
}

/** Название товара */
const getProductTitle = (product) => {
    return product?.translation?.title || ''
}

/** Краткое описание */
const getProductShort = (product) => {
    return product?.translation?.short || ''
}

/** Название бренда */
const getBrandTitle = (product) => {
    return product?.brand?.translation?.title || ''
}

/** Числовое значение */
const toNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/** Цена */
const getPrice = (product) => {
    return toNumber(product?.price)
}

/** Старая цена */
const getOldPrice = (product) => {
    return toNumber(product?.old_price)
}

/** Есть старая цена */
const hasOldPrice = (product) => {
    return getOldPrice(product) > getPrice(product)
}

/** Валюта */
const getCurrency = (product) => {
    return product?.currency?.symbol
        || product?.currency?.sign
        || product?.currency?.code
        || ''
}

/** Код валюты для Schema.org */
const getCurrencyCode = (product) => {
    return product?.currency?.code || ''
}

/** Первое изображение товара для Schema.org */
const getProductImage = (product) => {
    const images = Array.isArray(product?.images)
        ? product.images
        : (
            Array.isArray(product?.images?.data)
                ? product.images.data
                : []
        )

    const image = images[0]

    return image?.webp_url
        || image?.image_url
        || image?.thumb_url
        || image?.url
        || ''
}

/** Абсолютная ссылка на товар */
const productAbsoluteUrl = (product) => {
    return product?.url
        ? route('public.marketProducts.show', {
            url: product.url
        })
        : ''
}

/** Schema.org availability */
const productAvailability = (product) => {
    return hasStock(product)
        ? 'https://schema.org/InStock'
        : 'https://schema.org/OutOfStock'
}

/** Количество оценок */
const ratingCount = (product) => {
    return Number(product?.rating_count ?? 0)
}

/** Есть корректный AggregateRating */
const hasAggregateRating = (product) => {
    return rating(product) > 0
        && ratingCount(product) > 0
}

/** Форматирование цены */
const formatPrice = (value) => {
    return new Intl.NumberFormat(locale.value, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(toNumber(value))
}

/** Наличие */
const hasStock = (product) => {
    return Boolean(
        product?.has_stock
        ?? (
            product?.in_stock
            && Number(product?.quantity ?? 0) > 0
        )
    )
}

/** Количество */
const quantity = (product) => {
    return Number(product?.quantity ?? 0)
}

/** Рейтинг */
const rating = (product) => {
    return toNumber(product?.rating_avg)
}

/** Количество отзывов */
const reviewsCount = (product) => {
    return Number(product?.reviews_count ?? 0)
}

/** Есть маркетинговые признаки */
const hasMarketingFlags = (product) => {
    return Boolean(
        product?.is_new
        || product?.is_hit
        || product?.is_sale
    )
}

/* ===================== LIFECYCLE ===================== */

onMounted(async () => {
    await nextTick()

    updateScrollState()
    bindWheel()

    window.addEventListener(
        'resize',
        handleResize
    )
})

onBeforeUnmount(() => {
    unbindWheel()

    window.removeEventListener(
        'resize',
        handleResize
    )
})
</script>

<template>
    <section
        v-if="products.length"
        class="mx-auto mt-5 w-full min-w-0 py-3 px-1 lg:px-6 max-w-screen-2xl ext-color
               rounded-3xl border-2 border-slate-300 dark:border-slate-500"
        itemscope
        itemtype="https://schema.org/ItemList"
    >
        <meta
            itemprop="numberOfItems"
            :content="String(products.length)"
        />
        <!-- Заголовок и управление -->
        <div
            class="mb-4 flex items-center justify-center gap-3"
        >
            <!-- Назад -->
            <button
                type="button"
                @click="scrollPrev"
                :disabled="products.length <= 1"
                class="shrink-0 items-center justify-center
                       rounded-sm border border-dotted border-gray-600
                       bg-slate-50 text-slate-600 shadow-sm
                       transition
                       hover:border-2 hover:border-solid
                       hover:border-indigo-400
                       hover:bg-slate-200
                       hover:text-indigo-500
                       disabled:cursor-default
                       disabled:opacity-30
                       dark:border-gray-400
                       dark:bg-slate-950
                       dark:text-slate-300
                       dark:hover:border-indigo-500
                       dark:hover:bg-slate-800
                       sm:flex h-8 w-8"
                :title="t('previous')"
                aria-label="Previous products"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>

            <!-- Заголовок -->
            <h2
                itemprop="name"
                class="flex items-center justify-center
                       text-sm sm:text-xl font-semibold
                       text-slate-800 dark:text-slate-200"
            >
                <span>
                    {{ t('recentlyViewedProducts') }}
                </span>

                <span
                    class="ml-2 inline-flex min-w-6
                           items-center justify-center
                           rounded-full
                           border border-teal-200
                           bg-teal-50
                           px-2 py-0.5
                           text-xs font-bold
                           text-teal-600
                           shadow-sm
                           dark:border-teal-700/70
                           dark:bg-teal-950/60
                           dark:text-teal-300"
                >
                    {{ products.length }}
                </span>
            </h2>

            <!-- Вперёд -->
            <button
                type="button"
                @click="scrollNext"
                :disabled="products.length <= 1"
                class="shrink-0 items-center justify-center
                       rounded-sm border border-dotted border-gray-600
                       bg-slate-50 text-slate-600 shadow-sm
                       transition
                       hover:border-2 hover:border-solid
                       hover:border-indigo-400
                       hover:bg-slate-200
                       hover:text-indigo-500
                       disabled:cursor-default
                       disabled:opacity-30
                       dark:border-gray-400
                       dark:bg-slate-950
                       dark:text-slate-300
                       dark:hover:border-indigo-500
                       dark:hover:bg-slate-800
                       sm:flex h-8 w-8"
                :title="t('next')"
                aria-label="Next products"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </button>
        </div>

        <!-- Горизонтальная лента -->
        <div
            ref="sliderRef"
            @scroll.passive="updateScrollState"
            class="flex min-w-0 w-full
                   snap-x snap-mandatory
                   gap-4 overflow-x-auto
                   scroll-smooth pb-2
                   [scrollbar-width:none]
                   [&::-webkit-scrollbar]:hidden"
        >
            <!-- Карточка -->
            <article
                v-for="(product, index) in products"
                :key="product.id"
                class="group flex shrink-0 snap-start
                       w-full
                       sm:w-[calc(50%-0.5rem)]
                       md:w-[calc(33.333333%-0.667rem)]
                       lg:w-[calc(25%-0.75rem)]
                       flex-col overflow-hidden rounded-2xl
                       border border-gray-200
                       bg-white shadow-sm transition
                       hover:-translate-y-0.5
                       hover:shadow-md
                       dark:border-gray-700
                       dark:bg-gray-900"
                itemprop="itemListElement"
                itemscope
                itemtype="https://schema.org/ListItem"
            >
                <meta
                    itemprop="position"
                    :content="String(index + 1)"
                />

                <div
                    class="flex h-full flex-col"
                    itemprop="item"
                    itemscope
                    itemtype="https://schema.org/Product"
                >
                    <meta
                        v-if="getProductImage(product)"
                        itemprop="image"
                        :content="getProductImage(product)"
                    />
                    <!-- Изображение -->
                    <div class="relative">
                        <Link
                            :href="productLink(product)"
                            itemprop="url"
                        >
                            <UniversalImageSlider
                                :entity="product"
                                height-class="h-40"
                                rounded-class=""
                                wrapper-class=""
                                img-class="w-full h-full object-cover
                                       transition duration-300
                                       group-hover:scale-105"
                            />
                        </Link>

                        <!-- Маркетинговые признаки -->
                        <div
                            v-if="hasMarketingFlags(product)"
                            class="absolute left-2 top-2 z-10
                               flex flex-wrap gap-1"
                        >
                        <span
                            v-if="product.is_new"
                            class="rounded-sm bg-teal-500
                                   px-1.5 py-0.5
                                   text-[9px] font-bold
                                   uppercase tracking-wide
                                   text-white shadow-sm"
                        >
                            NEW
                        </span>

                            <span
                                v-if="product.is_hit"
                                class="rounded-sm bg-amber-500
                                   px-1.5 py-0.5
                                   text-[9px] font-bold
                                   uppercase tracking-wide
                                   text-slate-900 shadow-sm"
                            >
                            HIT
                        </span>

                            <span
                                v-if="product.is_sale"
                                class="rounded-sm bg-red-500
                                   px-1.5 py-0.5
                                   text-[9px] font-bold
                                   uppercase tracking-wide
                                   text-white shadow-sm"
                            >
                            SALE
                        </span>
                        </div>
                    </div>

                    <!-- Информация -->
                    <div class="flex flex-1 flex-col p-3">
                        <!-- Бренд -->
                        <div
                            v-if="getBrandTitle(product)"
                            class="mb-1 text-center"
                            itemprop="brand"
                            itemscope
                            itemtype="https://schema.org/Brand"
                        >
                            <span
                                itemprop="name"
                                class="text-[10px] font-semibold
                                       uppercase tracking-wide
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                {{ getBrandTitle(product) }}
                            </span>
                        </div>

                        <!-- Название -->
                        <div class="text-center">
                            <Link
                                :href="productLink(product)"
                                :title="getProductShort(product)"
                                class="inline-flex"
                            >
                                <span
                                    itemprop="name"
                                    class="line-clamp-2 text-xs
                                           font-semibold
                                           text-slate-900/85
                                           transition
                                           group-hover:opacity-75
                                           dark:text-slate-100/85"
                                                            >
                                    {{ getProductTitle(product) }}
                                </span>
                            </Link>
                        </div>

                        <meta
                            v-if="getProductShort(product)"
                            itemprop="description"
                            :content="getProductShort(product)"
                        />

                        <meta
                            v-if="product.sku"
                            itemprop="sku"
                            :content="product.sku"
                        />

                        <!-- Предложение -->
                        <div
                            itemprop="offers"
                            itemscope
                            itemtype="https://schema.org/Offer"
                        >
                            <meta
                                itemprop="url"
                                :content="productAbsoluteUrl(product)"
                            />

                            <meta
                                itemprop="price"
                                :content="String(getPrice(product))"
                            />

                            <meta
                                v-if="getCurrencyCode(product)"
                                itemprop="priceCurrency"
                                :content="getCurrencyCode(product)"
                            />

                            <link
                                itemprop="availability"
                                :href="productAvailability(product)"
                            />

                                <!-- Цена -->
                                <div
                                    class="mt-2 flex flex-col
                                       items-center justify-center"
                                >
                                <span
                                    class="text-sm font-bold
                                           text-teal-600
                                           dark:text-teal-400"
                                >
                                    {{ formatPrice(getPrice(product)) }}
                                    {{ getCurrency(product) }}
                                </span>

                                    <span
                                        v-if="hasOldPrice(product)"
                                        class="text-[11px] font-semibold
                                           text-slate-400 line-through
                                           dark:text-slate-500"
                                    >
                                    {{ formatPrice(getOldPrice(product)) }}
                                    {{ getCurrency(product) }}
                                </span>
                                </div>

                                <!-- Наличие -->
                                <div
                                    class="mt-1 flex items-center
                                       justify-center gap-1
                                       text-[10px] font-semibold
                                       text-slate-500
                                       dark:text-slate-400"
                                >
                                    <svg
                                        class="h-3 w-3 shrink-0"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        :class="hasStock(product)
                                        ? 'text-emerald-600 dark:text-emerald-300'
                                        : 'text-red-500 dark:text-red-300'"
                                    >
                                        <path
                                            v-if="hasStock(product)"
                                            d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"
                                        />

                                        <path
                                            v-else
                                            d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"
                                        />
                                    </svg>

                                    <span>
                                    {{ hasStock(product) ? t('remainder') : t('outOfStock') }}
                                </span>

                                    <span
                                        v-if="hasStock(product)
                                        && quantity(product) > 0"
                                    >
                                    [{{ quantity(product) }}]
                                </span>
                                </div>

                            </div>

                        <!-- Статистика -->
                        <div
                            class="mt-2 flex flex-wrap
                               items-center justify-center gap-2"
                        >
                            <!-- Просмотры -->
                            <div
                                v-if="product.views > 0"
                                class="inline-flex items-center gap-1
                                   text-[10px] font-semibold
                                   text-slate-500
                                   dark:text-slate-400"
                            >
                                <svg
                                    class="h-3 w-3
                                       text-blue-600/85
                                       dark:text-blue-200/85"
                                    viewBox="0 0 576 512"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                    />
                                </svg>

                                {{ product.views }}
                            </div>

                            <!-- Рейтинг -->
                            <div
                                v-if="rating(product) > 0"
                                class="inline-flex items-center gap-1
                                   text-[10px] font-semibold
                                   text-slate-500
                                   dark:text-slate-400"
                            >
                                <svg
                                    class="h-3 w-3
                                       text-amber-500
                                       dark:text-amber-300"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                    />
                                </svg>

                                {{ rating(product).toFixed(1) }}
                            </div>

                            <!-- Отзывы -->
                            <div
                                v-if="reviewsCount(product) > 0"
                                class="text-[10px] font-semibold
                                   text-slate-500
                                   dark:text-slate-400"
                            >
                                {{ t('reviews') }}:
                                {{ reviewsCount(product) }}
                            </div>
                        </div>

                        <!-- Schema.org AggregateRating -->
                        <div
                            v-if="hasAggregateRating(product)"
                            itemprop="aggregateRating"
                            itemscope
                            itemtype="https://schema.org/AggregateRating"
                            class="hidden"
                        >
                            <meta
                                itemprop="ratingValue"
                                :content="String(rating(product))"
                            />

                            <meta
                                itemprop="ratingCount"
                                :content="String(ratingCount(product))"
                            />

                            <meta
                                itemprop="bestRating"
                                content="5"
                            />

                            <meta
                                itemprop="worstRating"
                                content="1"
                            />

                            <meta
                                v-if="reviewsCount(product) > 0"
                                itemprop="reviewCount"
                                :content="String(reviewsCount(product))"
                            />
                        </div>

                        <!-- Лайк -->
                        <div
                            class="mt-auto flex
                               items-center justify-center pt-2"
                        >
                            <LikeButtonEntity
                                :likes-count="product.likes_count || 0"
                                :already-liked="product.already_liked || false"
                                route-name="public.marketProducts.like"
                                :route-params="{ id: product.id }"
                                icon-class="w-3 h-3
                                        hover:scale-110
                                        active:scale-95"
                            />
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
