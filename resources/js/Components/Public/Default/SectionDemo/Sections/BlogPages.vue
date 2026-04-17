<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/blog/posts-1',
        imgLight: '/images/vulk/posts-1.png',
        imgDark:  '/images/vulk/posts-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 1'
    },
    {
        href: 'http://localhost:3000/blog/posts-2',
        imgLight: '/images/vulk/posts-2.png',
        imgDark:  '/images/vulk/posts-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 2'
    },
    {
        href: 'http://localhost:3000/blog/posts-3',
        imgLight: '/images/vulk/posts-3.png',
        imgDark:  '/images/vulk/posts-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 3'
    },
    {
        href: 'http://localhost:3000/blog/posts-4',
        imgLight: '/images/vulk/posts-4.png',
        imgDark:  '/images/vulk/posts-4-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 4'
    },
    {
        href: 'http://localhost:3000/blog/posts-5',
        imgLight: '/images/vulk/posts-5.png',
        imgDark:  '/images/vulk/posts-5-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 5'
    },
    {
        href: 'http://localhost:3000/blog/posts-6',
        imgLight: '/images/vulk/posts-6.png',
        imgDark:  '/images/vulk/posts-6-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 6'
    },
    {
        href: 'http://localhost:3000/blog/posts-7',
        imgLight: '/images/vulk/posts-7.png',
        imgDark:  '/images/vulk/posts-7-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 7'
    },
    {
        href: 'http://localhost:3000/blog/posts-8',
        imgLight: '/images/vulk/posts-8.png',
        imgDark:  '/images/vulk/posts-8-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 8'
    },
    {
        href: 'http://localhost:3000/blog/posts-9',
        imgLight: '/images/vulk/posts-9.png',
        imgDark:  '/images/vulk/posts-9-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 9'
    },
    {
        href: 'http://localhost:3000/blog/posts-10',
        imgLight: '/images/vulk/posts-10.png',
        imgDark:  '/images/vulk/posts-10-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 10'
    },
    {
        href: 'http://localhost:3000/blog/posts-11',
        imgLight: '/images/vulk/posts-1.png',
        imgDark:  '/images/vulk/posts-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 11'
    },
    {
        href: 'http://localhost:3000/blog/posts-12',
        imgLight: '/images/vulk/posts-2.png',
        imgDark:  '/images/vulk/posts-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Blog',
        title: 'Posts 12'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- Blog pages -->
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
                <h3>Blog pages</h3>
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
