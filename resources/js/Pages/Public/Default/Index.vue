<script setup>
import { Head } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import Navbar from '@/Partials/Default/Navbar.vue'
import SectionHero from '@/Partials/Default/SectionHero.vue';
import SectionWave from '@/Partials/Default/SectionWave.vue';
import SectionFeature from '@/Partials/Default/SectionFeature.vue';
import SectionDeveloper from '@/Partials/Default/SectionDeveloper.vue';
import SectionQuickstart from '@/Partials/Default/SectionQuickstart.vue';
import SectionDemo from '@/Partials/Default/SectionDemo.vue';
import SectionQuality from '@/Partials/Default/SectionQuality.vue';
import SectionComponent from '@/Partials/Default/SectionComponent.vue';
import SectionReason from '@/Partials/Default/SectionReason.vue';
import Footer from '@/Partials/Default/Footer.vue';
import '@/../css/vulk/vulk.css';

const { t } = useI18n();

defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ t('home') }}</title>
        <meta name="title" :content="''" />
        <meta name="keywords" :content="''" />
        <meta name="description" :content="''" />

        <meta property="og:title" :content="''" />
        <meta property="og:description" :content="''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/`" />
        <meta property="og:image" :content="''" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="''" />
        <meta name="twitter:description" :content="''" />
        <meta name="twitter:image" :content="''" />

        <meta name="DC.title" :content="''" />
        <meta name="DC.description" :content="''" />
        <meta name="DC.identifier" :content="`/`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>
    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div>
            <SectionHero />
            <SectionWave />
            <SectionFeature />
            <SectionDeveloper />
            <SectionQuickstart />
            <SectionDemo />
            <SectionQuality />
            <SectionComponent />
            <SectionReason />
            <Footer />
        </div>

    </DefaultLayout>
</template>
