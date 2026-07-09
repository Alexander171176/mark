<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'

const { t } = useI18n()

const props = defineProps({
    cmsPage: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Head>
        <title>{{ cmsPage.seo?.title || cmsPage.title }}</title>

        <meta
            name="title"
            :content="cmsPage.seo?.title || cmsPage.title || ''"
        />

        <meta
            name="keywords"
            :content="cmsPage.seo?.keywords || ''"
        />

        <meta
            name="description"
            :content="cmsPage.seo?.description || cmsPage.short || ''"
        />

        <meta
            property="og:title"
            :content="cmsPage.seo?.title || cmsPage.title || ''"
        />

        <meta
            property="og:description"
            :content="cmsPage.seo?.description || cmsPage.short || ''"
        />

        <meta property="og:type" content="website" />

        <meta
            property="og:url"
            :content="cmsPage.url || '/'"
        />

        <meta name="twitter:card" content="summary_large_image" />

        <meta
            name="twitter:title"
            :content="cmsPage.seo?.title || cmsPage.title || ''"
        />

        <meta
            name="twitter:description"
            :content="cmsPage.seo?.description || cmsPage.short || ''"
        />
    </Head>

    <DefaultLayout :title="cmsPage.title">
        <Navbar />

        <main class="min-h-screen px-3 pt-24 lg:pt-32 pb-10">
            <div class="mx-auto max-w-6xl">
                <nav class="text-sm mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center font-semibold">
                        <li>
                            <Link
                                :href="route('home')"
                                class="breadcrumb-link hover:underline"
                            >
                                {{ t('home') }}
                            </Link>
                        </li>

                        <li>
                            <span class="mx-2 breadcrumbs">/</span>
                        </li>

                        <li class="breadcrumbs">
                            {{ cmsPage.title }}
                        </li>
                    </ol>
                </nav>

                <section
                    class="rounded-xl border border-slate-200 dark:border-slate-700
                           bg-white dark:bg-slate-900
                           shadow-sm px-4 py-6 sm:px-6 lg:px-8"
                >
                    <header class="mb-6 text-center">
                        <h1 class="text-2xl sm:text-3xl font-bold title">
                            {{ cmsPage.title }}
                        </h1>

                        <p
                            v-if="cmsPage.subtitle"
                            class="mt-2 text-sm sm:text-base subtitle"
                        >
                            {{ cmsPage.subtitle }}
                        </p>

                        <p
                            v-if="cmsPage.short"
                            class="mt-3 text-sm text-slate-600 dark:text-slate-300"
                        >
                            {{ cmsPage.short }}
                        </p>
                    </header>

                    <article
                        v-if="cmsPage.show_content && cmsPage.description"
                        class="prose prose-slate dark:prose-invert max-w-none"
                        v-html="cmsPage.description"
                    />

                    <div
                        v-else
                        class="text-center text-slate-500 dark:text-slate-300"
                    >
                        {{ t('noData') }}
                    </div>
                </section>
            </div>
        </main>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
