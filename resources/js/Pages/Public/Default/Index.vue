<script setup>
import { Head } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import HomeNavbar from '@/Partials/Default/HomeNavbar.vue'
import Footer from '@/Partials/Default/Footer.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import HomeHero from '@/Components/Public/Default/Home/HomeHero.vue'
import HomeSolutions from '@/Components/Public/Default/Home/HomeSolutions.vue'
import HomeEquipmentSelector from '@/Components/Public/Default/Home/HomeEquipmentSelector.vue'
import HomeMarketCategories from '@/Components/Public/Default/Home/HomeMarketCategories.vue'
import HomeMarketProducts from '@/Components/Public/Default/Home/HomeMarketProducts.vue'
import HomeSupplier from '@/Components/Public/Default/Home/HomeSupplier.vue'
import HomeAdvantages from '@/Components/Public/Default/Home/HomeAdvantages.vue'
import HomeMarketBrands from '@/Components/Public/Default/Home/HomeMarketBrands.vue'
import HomeArticles from '@/Components/Public/Default/Home/HomeArticles.vue'
import HomeSchool from '@/Components/Public/Default/Home/HomeSchool.vue'
import HomeContactCta from '@/Components/Public/Default/Home/HomeContactCta.vue'

const { t } = useI18n()

const props = defineProps({
    marketCategories: { type: [Array, Object], default: () => [] },
    marketProducts: { type: [Array, Object], default: () => [] },
    marketBrandCarousel: { type: [Array, Object], default: () => [] },
    blogArticles: { type: [Array, Object], default: () => [] },
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ t('home') }}</title>
        <meta name="title" :content="t('home')" />
        <meta name="description" content="" />

        <meta property="og:title" :content="t('home')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="route('home')" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" :content="t('home')" />

        <meta name="DC.title" :content="t('home')" />
        <meta name="DC.identifier" :content="route('home')" />
    </Head>

    <DefaultLayout>
        <!-- Шапка -->
        <HomeNavbar />

        <!-- Главная -->
        <main class="min-h-screen">
            <!-- Первый экран -->
            <HomeHero />
            <HomeSolutions />
            <HomeMarketCategories :categories="marketCategories" />
            <HomeMarketProducts :products="marketProducts" />
            <HomeEquipmentSelector />
            <HomeSupplier />
            <HomeAdvantages />
            <HomeMarketBrands :brands="marketBrandCarousel" />
            <HomeArticles :articles="blogArticles" />
            <HomeSchool />
            <HomeContactCta />
            <!-- Следующие экраны будут подключаться здесь -->
        </main>

        <!-- Подвал -->
        <Footer />

        <!-- Прогресс -->
        <Progress />
    </DefaultLayout>
</template>
