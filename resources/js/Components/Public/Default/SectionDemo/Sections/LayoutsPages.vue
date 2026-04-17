<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/master/minimal-demo',
        imgLight: '/images/vulk/layout-minimal.png',
        imgDark:  '/images/vulk/layout-minimal-dark.png',
        alt: 'Demo screenshot',
        category: 'Master Layouts',
        title: 'Minimal'
    },
    {
        href: 'http://localhost:3000/master/default',
        imgLight: '/images/vulk/layout-default.png',
        imgDark:  '/images/vulk/layout-default-dark.png',
        alt: 'Demo screenshot',
        category: 'Master Layouts',
        title: 'Default Nav'
    },
    {
        href: 'http://localhost:3000/master/layout-a-demo',
        imgLight: '/images/vulk/layout-a-demo.png',
        imgDark:  '/images/vulk/layout-a-demo-dark.png',
        alt: 'Demo screenshot',
        category: 'Master Layouts',
        title: 'Nav A'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- Layouts pages -->
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
                <h3>Master Layouts</h3>
                <p>Prebuilt starter layouts</p>
            </div>
        </div>
        <div class="columns is-multiline">
            <!-- Demo item -->
            <LandingDemoItem
                v-for="(demo, index) in demos"
                :key="index"
                v-bind="demo"
            />
        </div>
    </div>
</template>

<style scoped></style>
