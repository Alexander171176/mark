<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/auth/login-1',
        imgLight: '/images/vulk/login-1.png',
        imgDark:  '/images/vulk/login-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Auth',
        title: 'Login 1'
    },
    {
        href: 'http://localhost:3000/auth/login-2',
        imgLight: '/images/vulk/login-2.png',
        imgDark:  '/images/vulk/login-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Auth',
        title: 'Login 2'
    },
    {
        href: 'http://localhost:3000/auth/login-3',
        imgLight: '/images/vulk/login-3.png',
        imgDark:  '/images/vulk/login-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Auth',
        title: 'Login 3'
    },
    {
        href: 'http://localhost:3000/auth/signup-1',
        imgLight: '/images/vulk/signup-1.png',
        imgDark:  '/images/vulk/signup-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Auth',
        title: 'Signup 1'
    },
    {
        href: 'http://localhost:3000/auth/signup-2',
        imgLight: '/images/vulk/signup-2.png',
        imgDark:  '/images/vulk/signup-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Auth',
        title: 'Signup 2'
    },
    {
        href: 'http://localhost:3000/auth/signup-3',
        imgLight: '/images/vulk/signup-3.png',
        imgDark:  '/images/vulk/signup-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Auth',
        title: 'Signup 3'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- Auth pages -->
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
                <h3>Auth pages</h3>
                <p>Prebuilt page demos</p>
            </div>
        </div>
        <div class="columns is-multiline"><!-- Demo item -->
            <LandingDemoItem
                v-for="(demo, index) in demos"
                :key="index"
                v-bind="demo"
            />
        </div>
    </div>
</template>

<style scoped></style>
