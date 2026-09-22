<script setup>
/**
 * Горизонтальная карусель брендов маркетплейса.
 *
 * - отдельный универсальный Public-компонент
 * - максимум 4 карточки одновременно
 * - горизонтальная прокрутка
 * - кнопки назад / вперед
 * - touch / trackpad / mouse wheel
 * - strict translation уже разрешён Laravel
 * - ссылка на Public Show бренда
 */

import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref
} from 'vue'

import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import UniversalImageSlider
    from '@/Components/Public/Default/Images/UniversalImageSlider.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

const props = defineProps({
    brands: {
        type: Array,
        default: () => []
    }
})

/* ===================== DATA ===================== */

/** Публичные бренды */
const brandsData = computed(() => {
    return Array.isArray(props.brands)
        ? props.brands
        : []
})

/* ===================== SLIDER ===================== */

/** Контейнер горизонтальной ленты */
const sliderRef = ref(null)

/** Можно прокрутить влево */
const canScrollLeft = ref(false)

/** Можно прокрутить вправо */
const canScrollRight = ref(false)

/**
 * Проверить текущее положение карусели.
 */
const updateScrollState = () => {
    const slider = sliderRef.value

    if (!slider) {
        canScrollLeft.value = false
        canScrollRight.value = false
        return
    }

    const maxScrollLeft =
        slider.scrollWidth - slider.clientWidth

    canScrollLeft.value =
        slider.scrollLeft > 2

    canScrollRight.value =
        maxScrollLeft > 2
        && slider.scrollLeft < maxScrollLeft - 2
}

/**
 * Размер шага прокрутки.
 *
 * Повторяет механику карусели
 * недавно просмотренных товаров.
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
 * В начале карусели переходим
 * к последним карточкам.
 */
const scrollPrev = () => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

    const maxScrollLeft =
        slider.scrollWidth - slider.clientWidth

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
 * В конце карусели возвращаемся
 * к первой карточке.
 */
const scrollNext = () => {
    const slider = sliderRef.value

    if (!slider) {
        return
    }

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
 * Управление каруселью колесом мыши.
 *
 * Колесо вниз  → следующий блок.
 * Колесо вверх → предыдущий блок.
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

    const delta =
        Math.abs(event.deltaY) >= Math.abs(event.deltaX)
            ? event.deltaY
            : event.deltaX

    if (Math.abs(delta) < 2) {
        return
    }

    event.preventDefault()

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
 * пересчитать состояние карусели.
 */
const handleResize = async () => {
    await nextTick()
    updateScrollState()
}

/* ===================== BRAND HELPERS ===================== */

/** Ссылка на Public Show бренда */
const brandLink = (brand) => {
    return brand?.url
        ? route('public.marketBrands.show', {
            url: brand.url
        })
        : '#'
}

/** Название бренда */
const getBrandTitle = (brand) => {
    return brand?.translation?.title || ''
}

/** Краткое описание бренда */
const getBrandShort = (brand) => {
    return brand?.translation?.short || ''
}

/** Количество публичных товаров */
const getProductsCount = (brand) => {
    const count = Number(
        brand?.products_count ?? 0
    )

    return Number.isFinite(count)
        ? count
        : 0
}

/**
 * Нормализовать storage URL.
 *
 * Абсолютные URL оставляем без изменений.
 * Относительные пути storage превращаем
 * в публичный /storage/... URL.
 */
const normalizeStorageUrl = (value) => {
    const url = String(value || '').trim()

    if (!url) {
        return ''
    }

    if (
        url.startsWith('http://')
        || url.startsWith('https://')
        || url.startsWith('//')
        || url.startsWith('/')
        || url.startsWith('data:')
        || url.startsWith('blob:')
    ) {
        return url
    }

    return `/storage/${url}`
}

/** Нормализовать коллекцию изображений */
const getBrandImages = (brand) => {
    if (Array.isArray(brand?.images)) {
        return brand.images
    }

    if (Array.isArray(brand?.images?.data)) {
        return brand.images.data
    }

    return []
}

/** Основное большое изображение бренда */
const getBrandImage = (brand) => {
    const image = getBrandImages(brand)[0]

    const url =
        image?.webp_url
        || image?.image_url
        || image?.url
        || image?.thumb_url
        || ''

    return normalizeStorageUrl(url)
}

/** Логотип бренда */
const getBrandLogo = (brand) => {
    return normalizeStorageUrl(
        brand?.logo
    )
}

/**
 * Основное визуальное изображение.
 *
 * Сначала используется большое изображение,
 * затем логотип как fallback.
 */
const getBrandVisual = (brand) => {
    return getBrandImage(brand)
        || getBrandLogo(brand)
}

/** Alt основного изображения */
const getBrandImageAlt = (brand) => {
    const image = getBrandImages(brand)[0]

    return image?.alt
        || getBrandTitle(brand)
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
        v-if="brandsData.length"
        class="mx-auto mt-5 w-full min-w-0
               py-3 px-1 lg:px-6
               max-w-screen-2xl ext-color
               rounded-3xl border-2
               border-slate-300
               dark:border-slate-500"
        itemscope
        itemtype="https://schema.org/ItemList"
    >
        <meta
            itemprop="numberOfItems"
            :content="String(brandsData.length)"
        />

        <!-- Заголовок и управление -->
        <div
            class="mb-4 flex items-center
                   justify-center gap-3"
        >
            <!-- Назад -->
            <button
                type="button"
                @click="scrollPrev"
                :disabled="brandsData.length <= 1"
                class="shrink-0 items-center justify-center
                       rounded-sm border border-dotted
                       border-gray-600
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
                aria-label="Previous brands"
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
                    {{ t('brands') }}
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
                    {{ brandsData.length }}
                </span>
            </h2>

            <!-- Вперёд -->
            <button
                type="button"
                @click="scrollNext"
                :disabled="brandsData.length <= 1"
                class="shrink-0 items-center justify-center
                       rounded-sm border border-dotted
                       border-gray-600
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
                aria-label="Next brands"
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
            <!-- Карточка бренда -->
            <article
                v-for="(brand, index) in brandsData"
                :key="brand.id"
                class="group flex shrink-0 snap-start
                       w-full
                       sm:w-[calc(50%-0.5rem)]
                       md:w-[calc(33.333333%-0.667rem)]
                       lg:w-[calc(25%-0.75rem)]
                       flex-col overflow-hidden
                       rounded-2xl
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
                    itemtype="https://schema.org/Brand"
                >
                    <!-- Schema.org -->
                    <meta
                        itemprop="url"
                        :content="brandLink(brand)"
                    />

                    <meta
                        v-if="getBrandVisual(brand)"
                        itemprop="image"
                        :content="getBrandVisual(brand)"
                    />

                    <meta
                        v-if="getBrandShort(brand)"
                        itemprop="description"
                        :content="getBrandShort(brand)"
                    />

                    <!-- Изображения бренда -->
<!--                    <div class="relative">-->
<!--                        <Link-->
<!--                            :href="brandLink(brand)"-->
<!--                            itemprop="url"-->
<!--                            :title="getBrandTitle(brand)"-->
<!--                        >-->
<!--                            <UniversalImageSlider-->
<!--                                :entity="brand"-->
<!--                                :alt="getBrandTitle(brand)"-->
<!--                                height-class="h-40"-->
<!--                                rounded-class=""-->
<!--                                wrapper-class=""-->
<!--                                img-class="w-full h-full object-cover-->
<!--                       transition duration-300-->
<!--                       group-hover:scale-105"-->
<!--                            />-->
<!--                        </Link>-->
<!--                    </div>-->

                    <!-- Информация -->
                    <div
                        class="flex flex-1 flex-col
                               items-center p-3 text-center"
                    >
                        <!-- Логотип -->
                        <Link
                            v-if="
                                getBrandLogo(brand)
                                && getBrandLogo(brand)
                                    !== getBrandVisual(brand)
                            "
                            :href="brandLink(brand)"
                            class="mt-1 mb-2 flex h-32 w-auto
                                   items-center justify-center
                                   rounded-sm border
                                   border-slate-200
                                   bg-white p-2 shadow-sm
                                   dark:border-slate-700
                                   dark:bg-slate-950"
                            :title="getBrandTitle(brand)"
                        >
                            <img
                                :src="getBrandLogo(brand)"
                                :alt="getBrandTitle(brand)"
                                class="max-h-full max-w-full
                                       object-contain"
                                loading="lazy"
                                decoding="async"
                            >
                        </Link>

                        <!-- Название -->
                        <Link
                            :href="brandLink(brand)"
                            class="inline-flex"
                            :title="getBrandShort(brand)"
                        >
                            <span
                                itemprop="name"
                                class="line-clamp-2
                                       text-sm font-bold
                                       text-slate-900/90
                                       transition
                                       group-hover:text-indigo-600
                                       dark:text-slate-100/90
                                       dark:group-hover:text-indigo-400"
                            >
                                {{ getBrandTitle(brand) }}
                            </span>
                        </Link>

                        <!-- Краткое описание -->
                        <p
                            v-if="getBrandShort(brand)"
                            class="mt-2 line-clamp-3
                                   text-xs leading-5
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ getBrandShort(brand) }}
                        </p>

                        <!-- Количество товаров -->
                        <div
                            class="mt-auto pt-3"
                        >
                            <Link
                                :href="brandLink(brand)"
                                class="inline-flex items-center
                                       gap-1.5 rounded-full
                                       border border-teal-200
                                       bg-teal-50
                                       px-3 py-1
                                       text-[11px] font-semibold
                                       text-teal-700
                                       transition
                                       hover:border-teal-300
                                       hover:bg-teal-100
                                       dark:border-teal-800
                                       dark:bg-teal-950/50
                                       dark:text-teal-300
                                       dark:hover:border-teal-700
                                       dark:hover:bg-teal-950"
                            >
                                <span>
                                    {{ t('products') }}:
                                </span>

                                <span>
                                    {{ getProductsCount(brand) }}
                                </span>
                            </Link>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
</template>
