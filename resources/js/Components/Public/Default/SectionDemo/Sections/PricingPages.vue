<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/pricing/pricing-1',
        imgLight: '/images/vulk/pricing-1.png',
        imgDark:  '/images/vulk/pricing-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Scroll Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-2',
        imgLight: '/images/vulk/pricing-2.png',
        imgDark:  '/images/vulk/pricing-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Solo Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-3',
        imgLight: '/images/vulk/pricing-3.png',
        imgDark:  '/images/vulk/pricing-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Long Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-4',
        imgLight: '/images/vulk/pricing-4.png',
        imgDark:  '/images/vulk/pricing-4-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Split Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-5',
        imgLight: '/images/vulk/pricing-5.png',
        imgDark:  '/images/vulk/pricing-5-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Block Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-6',
        imgLight: '/images/vulk/pricing-6.png',
        imgDark:  '/images/vulk/pricing-6-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Tabbed Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-7',
        imgLight: '/images/vulk/pricing-7.png',
        imgDark:  '/images/vulk/pricing-7-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Compact Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-8',
        imgLight: '/images/vulk/pricing-8.png',
        imgDark:  '/images/vulk/pricing-8-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Slide Pricing'
    },
    {
        href: 'http://localhost:3000/pricing/pricing-9',
        imgLight: '/images/vulk/pricing-9.png',
        imgDark:  '/images/vulk/pricing-9-dark.png',
        alt: 'Demo screenshot',
        category: 'Pricing',
        title: 'Solo Pricing'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- Pricing pages -->
    <div class="demo-section">
        <div class="demo-section-title">
            <div>
                <img class="is-image is-image-block is-image is-image-light"
                     src="./../../../../../../images/vulk/layout-1.svg"
                     alt="" loading="lazy">
                <img class="is-image is-image-block is-image is-image-dark"
                     src="./../../../../../../images/vulk/layout-1-dark.svg"
                     alt="" loading="lazy">
            </div>
            <div class="title-meta">
                <h3>Pricing pages</h3>
                <p>Prebuilt page demos</p>
            </div>
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
