<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import BaseSlider from '@/Components/Public/Default/UI/Slider/BaseSlider.vue'
import SliderNavigation from '@/Components/Public/Default/UI/Slider/SliderNavigation.vue'
import SliderPagination from '@/Components/Public/Default/UI/Slider/SliderPagination.vue'

const props = defineProps({
    brands: { type: [Array, Object], default: () => [] },
})

const brandsData = computed(() => {
    if (Array.isArray(props.brands)) return props.brands
    if (Array.isArray(props.brands?.data)) return props.brands.data
    return []
})

const sliderLoop = computed(() => brandsData.value.length > 5)

const sliderBreakpoints = {
    480: { slidesPerView: 2, spaceBetween: 16 },
    768: { slidesPerView: 3, spaceBetween: 20 },
    1024: { slidesPerView: 4, spaceBetween: 20 },
    1280: { slidesPerView: 5, spaceBetween: 24 },
}

const brandLink = (brand) => brand?.url
    ? route('public.marketBrands.show', { url: brand.url })
    : '#'

const brandTitle = (brand) => brand?.translation?.title || ''

const normalizeStorageUrl = (value) => {
    const url = String(value || '').trim()

    if (!url) return ''

    if (
        url.startsWith('http://')
        || url.startsWith('https://')
        || url.startsWith('//')
        || url.startsWith('/')
        || url.startsWith('data:')
        || url.startsWith('blob:')
    ) return url

    return `/storage/${url}`
}

const brandImages = (brand) => {
    if (Array.isArray(brand?.images)) return brand.images
    if (Array.isArray(brand?.images?.data)) return brand.images.data
    return []
}

const brandImage = (brand) => {
    const image = brandImages(brand)[0]

    return normalizeStorageUrl(
        image?.webp_url
        || image?.image_url
        || image?.url
        || image?.thumb_url
        || ''
    )
}

const brandLogo = (brand) => normalizeStorageUrl(brand?.logo)
const brandVisual = (brand) => brandLogo(brand) || brandImage(brand)
</script>

<template>
    <section v-if="brandsData.length"
             class="bg-slate-50 dark:bg-slate-950 py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Заголовок секции -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="text-sm font-bold uppercase tracking-[0.18em]
                                text-sky-600 dark:text-sky-400">
                        Производители
                    </div>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900
                               dark:text-white sm:text-4xl lg:text-5xl">
                        Бренды
                        <span class="text-sky-600 dark:text-sky-400">
                            оборудования
                        </span>
                    </h2>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600
                              dark:text-slate-300 sm:text-lg">
                        Работаем с производителями вентиляционного и климатического оборудования
                        для коммерческих и промышленных объектов.
                    </p>
                </div>

                <Link
                    :href="route('public.marketBrands.index')"
                    class="group inline-flex shrink-0 items-center gap-2 text-sm font-semibold
                           text-slate-900 transition hover:text-sky-600 dark:text-white
                           dark:hover:text-sky-400"
                >
                    Все бренды

                    <svg
                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                        viewBox="0 0 20 20" fill="none"
                         aria-hidden="true">
                        <path
                            d="M4 10h12M11 5l5 5-5 5"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                              stroke-linejoin="round" />
                    </svg>
                </Link>
            </div>

            <!-- Карусель -->
            <div class="mt-10 lg:mt-12">
                <BaseSlider
                    :items="brandsData"
                    :slides-per-view="1"
                    :space-between="12"
                    :breakpoints="sliderBreakpoints"
                    :autoplay="5000"
                    :speed="650"
                    :loop="sliderLoop"
                    :keyboard="true"
                    :pause-on-hover="true"
                    :allow-touch-move="true"
                >
                    <!-- Карточка бренда -->
                    <template #default="{ item: brand }">
                        <Link
                            :href="brandLink(brand)"
                            :title="brandTitle(brand)"
                            class="group flex h-44 items-center justify-center rounded-2xl
                                   border-2 border-slate-200 bg-slate-100 p-6 transition
                                   duration-300 hover:-translate-y-1 hover:border-sky-200
                                   hover:bg-white hover:shadow-lg hover:shadow-slate-200/60
                                   dark:border-slate-800 dark:bg-slate-900
                                   dark:hover:border-sky-800 dark:hover:bg-slate-950
                                   dark:hover:shadow-black/20 sm:h-48"
                        >
                            <div class="flex h-full w-full flex-col items-center justify-center">
                                <!-- Логотип -->
                                <div class="flex min-h-0 flex-1 items-center justify-center">
                                    <img
                                        v-if="brandVisual(brand)"
                                        :src="brandVisual(brand)"
                                        :alt="brandTitle(brand)"
                                        class="max-h-24 max-w-[180px] object-contain transition duration-300 group-hover:scale-105 sm:max-h-28 sm:max-w-[200px]"
                                        loading="lazy"
                                        decoding="async"
                                    >

                                    <!-- Fallback -->
                                    <div
                                        v-else
                                        class="flex h-20 w-20 items-center justify-center rounded-2xl border border-slate-200 bg-white text-xl font-bold text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500"
                                    >
                                        {{ brandTitle(brand).charAt(0) }}
                                    </div>
                                </div>

                                <!-- Название -->
                                <div
                                    class="mt-4 line-clamp-1 text-center text-sm font-bold text-slate-800 transition group-hover:text-sky-600 dark:text-slate-200 dark:group-hover:text-sky-400">
                                    {{ brandTitle(brand) }}
                                </div>
                            </div>
                        </Link>
                    </template>

                    <!-- Навигация -->
                    <template #navigation="{ prev, next, isBeginning, isEnd }">
                        <div class="mt-7 flex justify-end">
                            <SliderNavigation
                                :disabled-prev="!sliderLoop && isBeginning"
                                :disabled-next="!sliderLoop && isEnd"
                                @prev="prev"
                                @next="next"
                            />
                        </div>
                    </template>

                    <!-- Пагинация -->
                    <template #pagination="{ activeIndex, count, goTo }">
                        <div class="mt-6 flex justify-center">
                            <SliderPagination
                                :active-index="activeIndex"
                                :count="count"
                                label="Бренды оборудования"
                                @select="goTo"
                            />
                        </div>
                    </template>
                </BaseSlider>
            </div>
        </div>
    </section>
</template>
