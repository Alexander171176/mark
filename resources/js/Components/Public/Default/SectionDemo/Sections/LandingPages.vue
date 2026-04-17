<script setup>
import LandingDemoItem from '@/Components/Public/Default/Columns/LandingDemoItem.vue'

const demos = [
    {
        href: 'http://localhost:3000/landing/landing-1',
        imgLight: '/images/vulk/landing-1.png',
        imgDark:  '/images/vulk/landing-1-dark.png',
        alt: 'Demo screenshot',
        category: 'Marketing',
        title: 'Landing 1'
    },
    {
        href: 'http://localhost:3000/landing/landing-2',
        imgLight: '/images/vulk/landing-2.png',
        imgDark:  '/images/vulk/landing-2-dark.png',
        alt: 'Demo screenshot',
        category: 'Marketing',
        title: 'Landing 2'
    },
    {
        href: 'http://localhost:3000/landing/landing-3',
        imgLight: '/images/vulk/landing-3.png',
        imgDark:  '/images/vulk/landing-3-dark.png',
        alt: 'Demo screenshot',
        category: 'Startup',
        title: 'Landing 3'
    },
    {
        href: 'http://localhost:3000/landing/landing-4',
        imgLight: '/images/vulk/landing-4.png',
        imgDark:  '/images/vulk/landing-4-dark.png',
        alt: 'Demo screenshot',
        category: 'Startup',
        title: 'Landing 4'
    },
    {
        href: 'http://localhost:3000/landing/landing-5',
        imgLight: '/images/vulk/landing-5.png',
        imgDark:  '/images/vulk/landing-5-dark.png',
        alt: 'Demo screenshot',
        category: 'Support',
        title: 'Landing 5'
    },
    {
        href: 'http://localhost:3000/landing/landing-6',
        imgLight: '/images/vulk/landing-6.png',
        imgDark:  '/images/vulk/landing-6-dark.png',
        alt: 'Demo screenshot',
        category: 'Support',
        title: 'Landing 6'
    },
    {
        href: 'http://localhost:3000/landing/landing-7',
        imgLight: '/images/vulk/landing-7.png',
        imgDark:  '/images/vulk/landing-7-dark.png',
        alt: 'Demo screenshot',
        category: 'Meetings',
        title: 'Landing 7'
    },
    {
        href: 'http://localhost:3000/landing/landing-8',
        imgLight: '/images/vulk/landing-8.png',
        imgDark:  '/images/vulk/landing-8-dark.png',
        alt: 'Demo screenshot',
        category: 'Meetings',
        title: 'Landing 8'
    },
    {
        href: 'http://localhost:3000/landing/landing-9',
        imgLight: '/images/vulk/landing-9.png',
        imgDark:  '/images/vulk/landing-9-dark.png',
        alt: 'Demo screenshot',
        category: 'Mentoring',
        title: 'Landing 9'
    },
    {
        href: 'http://localhost:3000/landing/landing-10',
        imgLight: '/images/vulk/landing-10.png',
        imgDark:  '/images/vulk/landing-10-dark.png',
        alt: 'Demo screenshot',
        category: 'Mentoring',
        title: 'Landing 10'
    },
    {
        href: 'http://localhost:3000/landing/landing-11',
        imgLight: '/images/vulk/landing-11.png',
        imgDark:  '/images/vulk/landing-11-dark.png',
        alt: 'Demo screenshot',
        category: 'Banking',
        title: 'Landing 11'
    },
    {
        href: 'http://localhost:3000/landing/landing-12',
        imgLight: '/images/vulk/landing-12.png',
        imgDark:  '/images/vulk/landing-12-dark.png',
        alt: 'Demo screenshot',
        category: 'Banking',
        title: 'Landing 12'
    },
    {
        href: 'http://localhost:3000/landing/landing-13',
        imgLight: '/images/vulk/landing-13.png',
        imgDark:  '/images/vulk/landing-13-dark.png',
        alt: 'Demo screenshot',
        category: 'Startup',
        title: 'Landing 13'
    },
    {
        href: 'http://localhost:3000/landing/landing-14',
        imgLight: '/images/vulk/landing-14.png',
        imgDark:  '/images/vulk/landing-14-dark.png',
        alt: 'Demo screenshot',
        category: 'Creators',
        title: 'Landing 14'
    },
    {
        href: 'http://localhost:3000/landing/landing-15',
        imgLight: '/images/vulk/landing-15.png',
        imgDark:  '/images/vulk/landing-15-dark.png',
        alt: 'Demo screenshot',
        category: 'Creators',
        title: 'Landing 15'
    },
    {
        href: 'http://localhost:3000/landing/landing-16',
        imgLight: '/images/vulk/landing-16.png',
        imgDark:  '/images/vulk/landing-16-dark.png',
        alt: 'Demo screenshot',
        category: 'Commerce',
        title: 'Landing 16'
    },
    {
        href: 'http://localhost:3000/landing/landing-17',
        imgLight: '/images/vulk/landing-17.png',
        imgDark:  '/images/vulk/landing-17-dark.png',
        alt: 'Demo screenshot',
        category: 'Commerce',
        title: 'Landing 17'
    },
    {
        href: 'http://localhost:3000/landing/landing-18',
        imgLight: '/images/vulk/landing-18.png',
        imgDark:  '/images/vulk/landing-18-dark.png',
        alt: 'Demo screenshot',
        category: 'Commerce',
        title: 'Landing 18'
    },
    {
        href: 'http://localhost:3000/landing/landing-19',
        imgLight: '/images/vulk/landing-19.png',
        imgDark:  '/images/vulk/landing-19-dark.png',
        alt: 'Demo screenshot',
        category: 'Development',
        title: 'Landing 19'
    },
    {
        href: 'http://localhost:3000/landing/landing-20',
        imgLight: '/images/vulk/landing-20.png',
        imgDark:  '/images/vulk/landing-20-dark.png',
        alt: 'Demo screenshot',
        category: 'Development',
        title: 'Landing 20'
    },
    {
        href: 'http://localhost:3000/landing/landing-21',
        imgLight: '/images/vulk/landing-21.png',
        imgDark:  '/images/vulk/landing-21-dark.png',
        alt: 'Demo screenshot',
        category: 'Conference',
        title: 'Landing 21'
    },
    {
        href: 'http://localhost:3000/landing/landing-22',
        imgLight: '/images/vulk/landing-22.png',
        imgDark:  '/images/vulk/landing-22-dark.png',
        alt: 'Demo screenshot',
        category: 'Conference',
        title: 'Landing 22'
    },
    {
        href: 'http://localhost:3000/landing/landing-23',
        imgLight: '/images/vulk/landing-23.png',
        imgDark:  '/images/vulk/landing-23-dark.png',
        alt: 'Demo screenshot',
        category: 'IT Business',
        title: 'Landing 23'
    },
    {
        href: 'http://localhost:3000/landing/landing-24',
        imgLight: '/images/vulk/landing-24.png',
        imgDark:  '/images/vulk/landing-24-dark.png',
        alt: 'Demo screenshot',
        category: 'IT Business',
        title: 'Landing 24'
    },
    {
        href: 'http://localhost:3000/landing/landing-25',
        imgLight: '/images/vulk/landing-25.png',
        imgDark:  '/images/vulk/landing-25-dark.png',
        alt: 'Demo screenshot',
        category: 'IT Business',
        title: 'Landing 25'
    },
    {
        href: 'http://localhost:3000/landing/landing-26',
        imgLight: '/images/vulk/landing-26.png',
        imgDark:  '/images/vulk/landing-26-dark.png',
        alt: 'Demo screenshot',
        category: 'Insurance',
        title: 'Landing 26'
    },
    {
        href: 'http://localhost:3000/landing/landing-27',
        imgLight: '/images/vulk/landing-27.png',
        imgDark:  '/images/vulk/landing-27-dark.png',
        alt: 'Demo screenshot',
        category: 'Insurance',
        title: 'Landing 27'
    },
    {
        href: 'http://localhost:3000/landing/landing-28',
        imgLight: '/images/vulk/landing-28.png',
        imgDark:  '/images/vulk/landing-28-dark.png',
        alt: 'Demo screenshot',
        category: 'IT Jobs',
        title: 'Landing 28'
    },
    {
        href: 'http://localhost:3000/landing/landing-29',
        imgLight: '/images/vulk/landing-29.png',
        imgDark:  '/images/vulk/landing-29-dark.png',
        alt: 'Demo screenshot',
        category: 'IT Jobs',
        title: 'Landing 29'
    },
    {
        href: 'http://localhost:3000/landing/landing-30',
        imgLight: '/images/vulk/landing-30.png',
        imgDark:  '/images/vulk/landing-30-dark.png',
        alt: 'Demo screenshot',
        category: 'IT Jobs',
        title: 'Landing 30'
    },
    {
        href: 'http://localhost:3000/landing/landing-31',
        imgLight: '/images/vulk/landing-31.png',
        imgDark:  '/images/vulk/landing-31-dark.png',
        alt: 'Demo screenshot',
        category: 'NFT Marketplace',
        title: 'Landing 31'
    },
    {
        href: 'http://localhost:3000/landing/landing-32',
        imgLight: '/images/vulk/landing-32.png',
        imgDark:  '/images/vulk/landing-32-dark.png',
        alt: 'Demo screenshot',
        category: 'NFT Marketplace',
        title: 'Landing 32'
    },
    {
        href: 'http://localhost:3000/landing/landing-33',
        imgLight: '/images/vulk/landing-33.png',
        imgDark:  '/images/vulk/landing-33-dark.png',
        alt: 'Demo screenshot',
        category: 'NFT Marketplace',
        title: 'Landing 33'
    },
    {
        href: 'http://localhost:3000/landing/landing-34',
        imgLight: '/images/vulk/landing-34.png',
        imgDark:  '/images/vulk/landing-34-dark.png',
        alt: 'Demo screenshot',
        category: 'Mobile App',
        title: 'Landing 34'
    },
    {
        href: 'http://localhost:3000/landing/landing-35',
        imgLight: '/images/vulk/landing-35.png',
        imgDark:  '/images/vulk/landing-35-dark.png',
        alt: 'Demo screenshot',
        category: 'Mobile App',
        title: 'Landing 35'
    },
    {
        href: 'http://localhost:3000/landing/landing-36',
        imgLight: '/images/vulk/landing-36.png',
        imgDark:  '/images/vulk/landing-36-dark.png',
        alt: 'Demo screenshot',
        category: 'Car Rental',
        title: 'Landing 36'
    },
    {
        href: 'http://localhost:3000/landing/landing-37',
        imgLight: '/images/vulk/landing-37.png',
        imgDark:  '/images/vulk/landing-37-dark.png',
        alt: 'Demo screenshot',
        category: 'Car Rental',
        title: 'Landing 37'
    },
    {
        href: 'http://localhost:3000/landing/landing-38',
        imgLight: '/images/vulk/landing-38.png',
        imgDark:  '/images/vulk/landing-38-dark.png',
        alt: 'Demo screenshot',
        category: 'Car Rental',
        title: 'Landing 38'
    },
    // ...добавь остальные элементы сюда
]
</script>

<template>
    <!-- Landing pages -->
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
            <div class="title-meta"><h3>Landing pages</h3>
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
