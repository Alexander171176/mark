<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/about/about-1',
        imgLight: '/images/vulk/about-1.png',
        imgDark:  '/images/vulk/about-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Startup',
        title: 'About 1'
    },
    {
        href: 'http://localhost:3000/about/about-2',
        imgLight: '/images/vulk/about-2.png',
        imgDark:  '/images/vulk/about-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Startup',
        title: 'About 2'
    },
    {
        href: 'http://localhost:3000/about/about-3',
        imgLight: '/images/vulk/about-3.png',
        imgDark:  '/images/vulk/about-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Agency',
        title: 'About 3'
    },
    {
        href: 'http://localhost:3000/about/about-4',
        imgLight: '/images/vulk/about-4.png',
        imgDark:  '/images/vulk/about-4-dark.png',
        alt: 'Demo screenshot',
        category: 'Agency',
        title: 'About 4'
    },
    {
        href: 'http://localhost:3000/about/about-5',
        imgLight: '/images/vulk/about-5.png',
        imgDark:  '/images/vulk/about-5-dark.png',
        alt: 'Demo screenshot',
        category: 'Agency',
        title: 'About 5'
    },
    {
        href: 'http://localhost:3000/about/about-6',
        imgLight: '/images/vulk/about-6.png',
        imgDark:  '/images/vulk/about-6-dark.png',
        alt: 'Demo screenshot',
        category: 'Agency',
        title: 'About 6'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- About pages -->
    <div class="demo-section">
        <div class="demo-section-title">
            <div>
                <img class="is-image is-image-block is-image is-image-light"
                     src="./../../../../../../images/vulk/layout-1.svg"
                     alt="" loading="lazy">
                <img class="is-image is-image-block is-image is-image-dark"
                     src="./../../../../../../images/vulk/layout-1-dark.svg"
                     alt="" loading="lazy"></div>
            <div class="title-meta">
                <h3>About pages</h3>
                <p>Prebuilt page demos</p></div>
        </div>
        <div class="columns is-multiline">
            <LandingDemoItem
                v-for="(demo, index) in demos"
                :key="index"
                v-bind="demo"
            />
        </div>
    </div>
</template>

<style scoped></style>
