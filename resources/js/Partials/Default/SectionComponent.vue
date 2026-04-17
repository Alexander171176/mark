<script setup>
import { ref } from 'vue'
import FeatureItem from '@/Components/Public/Default/SectionComponent/FeatureItem.vue'
import ComponentGrid from '@/Components/Public/Default/SectionComponent/ComponentGrid.vue'

const current = ref('base')

const tabs = [
    { key: 'base',         label: 'Base' },
    { key: 'intermediate', label: 'Intermediate' },
    { key: 'blocks',       label: 'Blocks' },
    { key: 'advanced',     label: 'Advanced' },
    { key: 'utilities',    label: 'Utilities' },
]

// Данные для каждой вкладки (1:1 из вашего шаблона)
const items = {
    base: [
        { href: '/blocks/avatars',     light: './../../../images/vulk/avatar.svg',          dark: './../../../images/vulk/avatar-dark.svg',          title: 'Avatar Component' },
        { href: '/blocks/buttons',     light: './../../../images/vulk/button.svg',          dark: './../../../images/vulk/button-dark.svg',          title: 'Button Component' },
        { href: '/blocks/cards',       light: './../../../images/vulk/card.svg',            dark: './../../../images/vulk/card-dark.svg',            title: 'Card Component' },
        { href: '/blocks/counters',    light: './../../../images/vulk/counter.svg',         dark: './../../../images/vulk/counter-dark.svg',         title: 'Counter Component' },
        { href: '/blocks/forms',       light: './../../../images/vulk/form.svg',            dark: './../../../images/vulk/form-dark.svg',            title: 'Form Components' },
        { href: '/blocks/icons',       light: './../../../images/vulk/icon.svg',            dark: './../../../images/vulk/icon-dark.svg',            title: 'Icon Component' },
        { href: '/blocks/images',      light: './../../../images/vulk/image.svg',           dark: './../../../images/vulk/image-dark.svg',           title: 'Image Component' },
        { href: '/blocks/typography',  light: './../../../images/vulk/typography.svg',      dark: './../../../images/vulk/typography-dark.svg',      title: 'Typography' },
        { href: '/blocks/tags',        light: './../../../images/vulk/tags.svg',            dark: './../../../images/vulk/tags-dark.svg',            title: 'Tag Component' },
    ],
    intermediate: [
        { href: '/blocks/modals',      light: './../../../images/vulk/modal.svg',           dark: './../../../images/vulk/modal-dark.svg',           title: 'Modal Component' },
        { href: '/blocks/placeholder', light: './../../../images/vulk/placeholder.svg',     dark: './../../../images/vulk/placeholder-dark.svg',     title: 'Placeholder Component' },
        { href: '/blocks/table',       light: './../../../images/vulk/table.svg',           dark: './../../../images/vulk/table-dark.svg',           title: 'Table Component' },
        { href: '/blocks/navtabs',     light: './../../../images/vulk/tabs.svg',            dark: './../../../images/vulk/tabs-dark.svg',            title: 'Tab Component' },
        { href: '/blocks/video',       light: './../../../images/vulk/video.svg',           dark: './../../../images/vulk/video-dark.svg',           title: 'Video Component' },
        { href: '/blocks/sections',    light: './../../../images/vulk/section.svg',         dark: './../../../images/vulk/section-dark.svg',         title: 'Section Component' },
    ],
    blocks: [
        { href: '/blocks/navbar',      light: './../../../images/vulk/block-navbar.svg',    dark: './../../../images/vulk/block-navbar-dark.svg',    title: 'Navbar Blocks' },
        { href: '/blocks/footer',      light: './../../../images/vulk/block-footer.svg',    dark: './../../../images/vulk/block-footer-dark.svg',    title: 'Footer Blocks' },
        { href: '/blocks/features',    light: './../../../images/vulk/block-features.svg',  dark: './../../../images/vulk/block-features-dark.svg',  title: 'Feature Blocks' },
        { href: '/blocks/content',     light: './../../../images/vulk/block-content.svg',   dark: './../../../images/vulk/block-content-dark.svg',   title: 'Content Blocks' },
        { href: '/blocks/call',        light: './../../../images/vulk/block-cta.svg',       dark: './../../../images/vulk/block-cta-dark.svg',       title: 'CTA Blocks' },
        { href: '/blocks/gallery',     light: './../../../images/vulk/block-gallery.svg',   dark: './../../../images/vulk/block-gallery-dark.svg',   title: 'Gallery Blocks' },
        { href: '/blocks/team',        light: './../../../images/vulk/block-team.svg',      dark: './../../../images/vulk/block-team-dark.svg',      title: 'Team Blocks' },
        { href: '/blocks/testimonials',light: './../../../images/vulk/block-testimonials.svg', dark: './../../../images/vulk/block-testimonials-dark.svg', title: 'Testimonial Blocks' },
    ],
    advanced: [
        { href: '/blocks/advanced/features',     light: './../../../images/vulk/features.svg',        dark: './../../../images/vulk/features-dark.svg',        title: 'Advanced Features' },
        { href: '/blocks/advanced/content',      light: './../../../images/vulk/block-content.svg',   dark: './../../../images/vulk/block-content-dark.svg',   title: 'Advanced Content' },
        { href: '/blocks/advanced/company',      light: './../../../images/vulk/company.svg',         dark: './../../../images/vulk/company-dark.svg',         title: 'Advanced Company' },
        { href: '/blocks/advanced/clients',      light: './../../../images/vulk/clients.svg',         dark: './../../../images/vulk/clients-dark.svg',         title: 'Advanced Clients' },
        { href: '/blocks/advanced/testimonials', light: './../../../images/vulk/testimonials.svg',    dark: './../../../images/vulk/testimonials-dark.svg',    title: 'Advanced Testimonials' },
        { href: '/blocks/advanced/blog',         light: './../../../images/vulk/blog.svg',            dark: './../../../images/vulk/blog-dark.svg',            title: 'Advanced Blog' },
        { href: '/blocks/advanced/contact',      light: './../../../images/vulk/contact.svg',         dark: './../../../images/vulk/contact-dark.svg',         title: 'Advanced Contact' },
        { href: '/blocks/advanced/faq',          light: './../../../images/vulk/faq.svg',             dark: './../../../images/vulk/faq-dark.svg',             title: 'Advanced FAQ' },
    ],
    utilities: [
        { href: '/blocks/colors',     light: './../../../images/vulk/colors.svg',      dark: './../../../images/vulk/colors-dark.svg',      title: 'Theme Colors' },
        { href: '/blocks/utilities',  light: './../../../images/vulk/utilities.svg',   dark: './../../../images/vulk/utilities-dark.svg',   title: 'Theme Utilities' },
        { href: '/blocks/plugins',    light: './../../../images/vulk/plugins.svg',     dark: './../../../images/vulk/plugins-dark.svg',     title: 'Theme Plugins' },
    ],
}
</script>

<template>
    <div class="bg-[hsl(228_33%_97%)] dark:bg-[hsl(240_33%_7%)]" id="template-components">
        <div class="section-content">
            <div class="container">
                <!-- Заголовок секции -->
                <div class="has-text-centered">
                    <h3 class="subtitle is-5 is-bold">
                        <span class="text-gradient">Reusable Components</span>
                    </h3>
                    <h2 class="title is-3 is-bold dark:text-white">
                        <span>Explore the Component Library</span>
                    </h2>
                </div>

                <!-- Три инфо-блока -->
                <div class="py-6">
                    <div class="columns is-multiline b-columns-half-tablet-p">
                        <div class="column is-4">
                            <FeatureItem
                                boxClass="is-primary"
                                title="Reusable Components"
                                text="Vulk ships with a ton of ready to use and reusable components. You can use them as is or duplicate to create your own variations and styles."
                            >
                                <template #icon>
                                    <!-- ваш SVG #1 -->
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 512 512" class="iconify iconify--ion">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                                              d="M461.81 53.81a4.4 4.4 0 0 0-3.3-3.39c-54.38-13.3-180 34.09-248.13 102.17a295 295 0 0 0-33.09 39.08c-21-1.9-42-.3-59.88 7.5c-50.49 22.2-65.18 80.18-69.28 105.07a9 9 0 0 0 9.8 10.4l81.07-8.9a180 180 0 0 0 1.1 18.3a18.15 18.15 0 0 0 5.3 11.09l31.39 31.39a18.15 18.15 0 0 0 11.1 5.3a180 180 0 0 0 18.19 1.1l-8.89 81a9 9 0 0 0 10.39 9.79c24.9-4 83-18.69 105.07-69.17c7.8-17.9 9.4-38.79 7.6-59.69a294 294 0 0 0 39.19-33.09c68.38-68 115.47-190.86 102.37-247.95M298.66 213.67a42.7 42.7 0 1 1 60.38 0a42.65 42.65 0 0 1-60.38 0"/>
                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                                              d="M109.64 352a45.06 45.06 0 0 0-26.35 12.84C65.67 382.52 64 448 64 448s65.52-1.67 83.15-19.31A44.73 44.73 0 0 0 160 402.32"/>
                                    </svg>
                                </template>
                            </FeatureItem>
                        </div>

                        <div class="column is-4">
                            <FeatureItem
                                boxClass="is-green"
                                title="Modular Code"
                                text="Vulk's code is very modular and very versatile. The template only loads what you need as every component is bundled with its own styles."
                            >
                                <template #icon>
                                    <!-- ваш SVG #2 -->
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 512 512" class="iconify iconify--ion">
                                        <rect width="80" height="80" x="64" y="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="216" y="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="368" y="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="64" y="216" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="216" y="216" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="368" y="216" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="64" y="368" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="216" y="368" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                        <rect width="80" height="80" x="368" y="368" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" rx="40" ry="40"/>
                                    </svg>
                                </template>
                            </FeatureItem>
                        </div>

                        <div class="column is-4">
                            <FeatureItem
                                boxClass="is-info"
                                title="Easy Theming"
                                text="Although it is built with Sass, Vulk leverages the power of native CSS variables, giving you the power to create any theme in a few minutes."
                            >
                                <template #icon>
                                    <!-- ваш SVG #3 -->
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 512 512" class="iconify iconify--ion">
                                        <path fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"
                                              d="M430.11 347.9c-6.6-6.1-16.3-7.6-24.6-9c-11.5-1.9-15.9-4-22.6-10c-14.3-12.7-14.3-31.1 0-43.8l30.3-26.9c46.4-41 46.4-108.2 0-149.2c-34.2-30.1-80.1-45-127.8-45c-55.7 0-113.9 20.3-158.8 60.1c-83.5 73.8-83.5 194.7 0 268.5c41.5 36.7 97.5 55 152.9 55.4h1.7c55.4 0 110-17.9 148.8-52.4c14.4-12.7 11.99-36.6.1-47.7Z"/>
                                        <circle cx="144" cy="208" r="32" fill="currentColor"/>
                                        <circle cx="152" cy="311" r="32" fill="currentColor"/>
                                        <circle cx="224" cy="144" r="32" fill="currentColor"/>
                                        <circle cx="256" cy="367" r="48" fill="currentColor"/>
                                        <circle cx="328" cy="144" r="32" fill="currentColor"/>
                                    </svg>
                                </template>
                            </FeatureItem>
                        </div>
                    </div>
                </div>

                <!-- Кнопка -->
                <div class="buttons is-relative z-1 is-centered">
                    <a href="/blocks" class="button is-primary is-raised is-long-3">
                        <span>All Components</span>
                    </a>
                </div>

                <!-- Вертикальные табы -->
                <div class="py-6 vulk-components">
                    <div class="vertical-tabs-wrapper">
                        <div class="tabs">
                            <ul>
                                <li v-for="t in tabs" :key="t.key" :class="{ 'is-active': t.key === current }">
                                    <a href="#" @click.prevent="current = t.key">{{ t.label }}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="content-wrap has-text-left">
                            <!-- один контейнер таба, ключом служит текущая вкладка -->
                            <Transition name="tabfade" mode="out-in">
                                <div :key="current" class="tab-content is-active">
                                    <ComponentGrid :items="items[current]" />
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* 1) базовый стиль темы скрывает табы */
.content-wrap .tab-content { display: none; }
/* 2) активный таб — показываем */
.content-wrap .tab-content.is-active { display: block; }

/* Плавное появление/исчезновение всего таба */
.tabfade-enter-active,
.tabfade-leave-active {
    transition: opacity .25s ease, transform .25s ease;
}
.tabfade-enter-from,
.tabfade-leave-to {
    opacity: 0;
    transform: translateY(8px);
}
</style>
