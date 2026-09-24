<script setup>
import BaseSlider from '@/Components/Public/Default/UI/Slider/BaseSlider.vue'
import SliderNavigation from '@/Components/Public/Default/UI/Slider/SliderNavigation.vue'
import SliderPagination from '@/Components/Public/Default/UI/Slider/SliderPagination.vue'
import HomeHeroSlide from '@/Components/Public/Default/Home/HomeHeroSlide.vue'

const slides = [
    {
        id: 1,
        isMain: true,
        label: 'Агровент',
        title: 'Вентиляционное и климатическое оборудование',
        accent: 'для объектов любой сложности',
        description: 'Подбор, поставка и техническое сопровождение оборудования для систем вентиляции, отопления и кондиционирования.',
        image: '/images/home/hero/hero-01.png',
        imageAlt: 'Вентиляционное и климатическое оборудование',
        actions: [
            {
                label: 'Каталог оборудования',
                route: 'public.marketProducts.index',
                primary: true,
            },
            {
                label: 'Подобрать оборудование',
                href: '#equipment-selection',
            },
            {
                label: 'Отправить спецификацию',
                href: '#specification',
            },
        ],
        advantages: [
            {
                title: 'Инженерный подбор',
                text: 'Под задачу и параметры объекта',
            },
            {
                title: 'Комплектация проектов',
                text: 'Работа со спецификациями',
            },
            {
                title: 'Поставка по Казахстану',
                text: 'Для коммерческих и промышленных объектов',
            },
        ],
    },
    {
        id: 2,
        label: 'Промышленная вентиляция',
        title: 'Эффективная вентиляция',
        accent: 'для промышленных объектов',
        description: 'Подберём оборудование для производственных, складских, технологических и коммерческих помещений.',
        image: '/images/home/hero/hero-02.png',
        imageAlt: 'Промышленная система вентиляции',
        actions: [
            {
                label: 'Смотреть оборудование',
                route: 'public.marketProducts.index',
                primary: true,
            },
            {
                label: 'Получить консультацию',
                href: '#consultation',
            },
            {
                label: 'Отправить спецификацию',
                href: '#specification',
            },
        ],
        advantages: [
            {
                title: 'Расчёт оборудования',
                text: 'С учётом параметров объекта',
            },
            {
                title: 'Проектные решения',
                text: 'Для новых и действующих объектов',
            },
            {
                title: 'Техническая поддержка',
                text: 'На этапе подбора и поставки',
            },
        ],
    },
    {
        id: 3,
        label: 'Системы кондиционирования',
        title: 'Климатические системы',
        accent: 'для коммерческих зданий',
        description: 'Чиллеры, ККБ, VRF-системы и другое оборудование для современных систем кондиционирования.',
        image: '/images/home/hero/hero-03.png',
        imageAlt: 'Климатическое оборудование для коммерческого здания',
        actions: [
            {
                label: 'Перейти в каталог',
                route: 'public.marketProducts.index',
                primary: true,
            },
            {
                label: 'Подобрать систему',
                href: '#equipment-selection',
            },
            {
                label: 'Отправить спецификацию',
                href: '#specification',
            },
        ],
        advantages: [
            {
                title: 'Чиллеры и ККБ',
                text: 'Для систем различной мощности',
            },
            {
                title: 'VRF-системы',
                text: 'Для коммерческих объектов',
            },
            {
                title: 'Комплексный подбор',
                text: 'Оборудование под проект',
            },
        ],
    },
]
</script>

<template>
    <section class="relative overflow-hidden">
        <BaseSlider
            :items="slides"
            :autoplay="7000"
            :speed="900"
            :loop="true"
            :keyboard="true"
            :pause-on-hover="true"
        >
            <template #default="{ item, active }">
                <HomeHeroSlide :slide="item" :active="active" />
            </template>

            <!-- Стрелки -->
            <template #navigation="{ prev, next }">
                <div class="pointer-events-none absolute inset-x-0 bottom-8 z-40">
                    <div class="mx-auto flex max-w-7xl justify-end px-4 sm:px-6 lg:px-8">
                        <SliderNavigation
                            class="pointer-events-auto"
                            @prev="prev"
                            @next="next"
                        />
                    </div>
                </div>
            </template>

            <!-- Индикаторы -->
            <template #pagination="{ activeIndex, count, goTo }">
                <div class="pointer-events-none absolute inset-x-0 bottom-11 z-30">
                    <div class="mx-auto flex max-w-7xl justify-start px-4 sm:px-6 lg:px-8">
                        <SliderPagination
                            class="pointer-events-auto"
                            :active-index="activeIndex"
                            :count="count"
                            @select="goTo"
                        />
                    </div>
                </div>
            </template>

        </BaseSlider>
    </section>
</template>
