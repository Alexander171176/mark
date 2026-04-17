<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/profile',
        imgLight: '/images/vulk/profile-overview.png',
        imgDark:  '/images/vulk/profile-overview-dark.png',
        alt: 'Demo screenshot',
        category: 'Profile',
        title: 'Overview'
    },
    {
        href: 'http://localhost:3000/profile/projects',
        imgLight: '/images/vulk/profile-projects.png',
        imgDark:  '/images/vulk/profile-projects-dark.png',
        alt: 'Demo screenshot',
        category: 'Profile',
        title: 'Projects'
    },
    {
        href: 'http://localhost:3000/profile/network',
        imgLight: '/images/vulk/profile-network.png',
        imgDark:  '/images/vulk/profile-network-dark.png',
        alt: 'Demo screenshot',
        category: 'Profile',
        title: 'Network'
    },
    {
        href: 'http://localhost:3000/subpages/jobs/backend-developer',
        imgLight: '/images/vulk/job-details.png',
        imgDark:  '/images/vulk/job-details-dark.png',
        alt: 'Demo screenshot',
        category: 'Jobs',
        title: 'Job Details'
    },
    {
        href: 'http://localhost:3000/subpages/nfts/nft-list',
        imgLight: '/images/vulk/nft-list.png',
        imgDark:  '/images/vulk/nft-list-dark.png',
        alt: 'Demo screenshot',
        category: 'Marketplace',
        title: 'NFT List'
    },
    {
        href: 'http://localhost:3000/subpages/nfts/the-smoking-eagle',
        imgLight: '/images/vulk/nft-details.png',
        imgDark:  '/images/vulk/nft-details-dark.png',
        alt: 'Demo screenshot',
        category: 'Marketplace',
        title: 'NFT Details'
    },
    {
        href: 'http://localhost:3000/subpages/help',
        imgLight: '/images/vulk/help-center-home.png',
        imgDark:  '/images/vulk/help-center-home-dark.png',
        alt: 'Demo screenshot',
        category: 'Support',
        title: 'Help Center'
    },
    {
        href: 'http://localhost:3000/subpages/help/category/project-module-category',
        imgLight: '/images/vulk/help-center-category.png',
        imgDark:  '/images/vulk/help-center-category-dark.png',
        alt: 'Demo screenshot',
        category: 'Support',
        title: 'Help Category'
    },
    {
        href: 'http://localhost:3000/subpages/help/category/article/managing-tasks',
        imgLight: '/images/vulk/help-center-article.png',
        imgDark:  '/images/vulk/help-center-article-dark.png',
        alt: 'Demo screenshot',
        category: 'Support',
        title: 'Help Article'
    },
    {
        href: 'http://localhost:3000/subpages/contact',
        imgLight: '/images/vulk/contact-1.png',
        imgDark:  '/images/vulk/contact-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Contact',
        title: 'Contact 1'
    },
    {
        href: 'http://localhost:3000/subpages/contact/contact-2',
        imgLight: '/images/vulk/contact-2.png',
        imgDark:  '/images/vulk/contact-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Contact',
        title: 'Contact 2'
    },
    {
        href: 'http://localhost:3000/subpages/contact/contact-3',
        imgLight: '/images/vulk/contact-3.png',
        imgDark:  '/images/vulk/contact-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Contact',
        title: 'Contact 3'
    },
    {
        href: 'http://localhost:3000/subpages/terms',
        imgLight: '/images/vulk/terms-1.png',
        imgDark:  '/images/vulk/terms-1-dark.png',
        alt: 'Demo screenshot',
        category: 'ToS',
        title: 'Terms of Service'
    },
    {
        href: 'http://localhost:3000/subpages/error',
        imgLight: '/images/vulk/404-2.png',
        imgDark:  '/images/vulk/404-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Error',
        title: '404 v2'
    },
    {
        href: 'http://localhost:3000/',
        imgLight: '/images/vulk/404-1.png',
        imgDark:  '/images/vulk/404-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Error',
        title: '404 v1'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- Sub pages -->
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
                <h3>Sub pages</h3>
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
