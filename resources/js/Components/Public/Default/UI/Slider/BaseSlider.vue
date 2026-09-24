<script setup>
import { computed, ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Keyboard } from 'swiper/modules'
import 'swiper/css'

const props = defineProps({
    items: { type: Array, default: () => [] },
    autoplay: { type: [Number, Boolean], default: false },
    speed: { type: Number, default: 700 },
    loop: { type: Boolean, default: false },
    keyboard: { type: Boolean, default: true },
    pauseOnHover: { type: Boolean, default: true },
    allowTouchMove: { type: Boolean, default: true },
    slidesPerView: { type: [Number, String], default: 1 },
    spaceBetween: { type: Number, default: 0 },
    breakpoints: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['change'])

const swiperInstance = ref(null)
const activeIndex = ref(0)
const isBeginning = ref(true)
const isEnd = ref(false)

const modules = [Autoplay, Keyboard]

const autoplayOptions = computed(() => {
    if (!props.autoplay) return false

    return {
        delay: typeof props.autoplay === 'number' ? props.autoplay : 7000,
        disableOnInteraction: false,
        pauseOnMouseEnter: props.pauseOnHover,
    }
})

const updateState = (swiper) => {
    activeIndex.value = swiper.realIndex ?? 0
    isBeginning.value = swiper.isBeginning
    isEnd.value = swiper.isEnd
}

const handleSwiper = (swiper) => {
    swiperInstance.value = swiper
    updateState(swiper)
}

const handleSlideChange = (swiper) => {
    updateState(swiper)

    emit('change', {
        activeIndex: activeIndex.value,
        item: props.items[activeIndex.value] ?? null,
    })
}

const prev = () => swiperInstance.value?.slidePrev()
const next = () => swiperInstance.value?.slideNext()

const goTo = (index) => {
    if (!swiperInstance.value) return

    props.loop
        ? swiperInstance.value.slideToLoop(index)
        : swiperInstance.value.slideTo(index)
}

defineExpose({
    prev,
    next,
    goTo,
    swiper: swiperInstance,
})
</script>

<template>
    <div class="relative">
        <Swiper
            :modules="modules"
            :slides-per-view="slidesPerView"
            :space-between="spaceBetween"
            :breakpoints="breakpoints"
            :speed="speed"
            :loop="loop && items.length > 1"
            :keyboard="{ enabled: keyboard }"
            :autoplay="autoplayOptions"
            :allow-touch-move="allowTouchMove"
            @swiper="handleSwiper"
            @slide-change="handleSlideChange"
        >
            <SwiperSlide v-for="(item, index) in items" :key="item?.id ?? index" class="pt-2">
                <slot
                    :item="item"
                    :index="index"
                    :active="activeIndex === index"
                />
            </SwiperSlide>
        </Swiper>

        <!-- Навигация -->
        <slot
            v-if="items.length > 1"
            name="navigation"
            :prev="prev"
            :next="next"
            :is-beginning="isBeginning"
            :is-end="isEnd"
        />

        <!-- Пагинация -->
        <slot
            v-if="items.length > 1"
            name="pagination"
            :active-index="activeIndex"
            :count="items.length"
            :go-to="goTo"
        />
    </div>
</template>
