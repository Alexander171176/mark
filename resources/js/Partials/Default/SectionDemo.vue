<script setup>
import LandingPages from '@/Components/Public/Default/SectionDemo/Sections/LandingPages.vue'
import AboutPages from '@/Components/Public/Default/SectionDemo/Sections/AboutPages.vue'
import PricingPages from '@/Components/Public/Default/SectionDemo/Sections/PricingPages.vue'
import BlogPages from '@/Components/Public/Default/SectionDemo/Sections/BlogPages.vue'
import SubPages from '@/Components/Public/Default/SectionDemo/Sections/SubPages.vue'
import AuthPages from '@/Components/Public/Default/SectionDemo/Sections/AuthPages.vue'
import LayoutsPages from '@/Components/Public/Default/SectionDemo/Sections/LayoutsPages.vue'
</script>

<template>
    <div class="bg-[hsl(228_33%_97%)] dark:bg-[hsl(240_33%_7%)]" id="template-demos">
        <div class="section-content">
            <div class="container">
                <div class="has-text-centered">
                    <h3 class="subtitle is-5 is-bold">
                        <span class="text-gradient">Premium Designs</span>
                    </h3>
                    <h2 class="title is-3 is-bold mb-2 dark:text-white">
                        <span>Beautiful demo pages</span>
                    </h2>
                </div>
                <div class="vulk-demos">
                    <div class="demo-search-section">
                        <div class="search-field-wrapper mx-auto max-w-3">
                            <div class="field">
                                <div class="control has-icon is-invalid">
                                    <input type="search" class="input is-rounded"
                                           placeholder="Banking, Startup, Freelancer ...">
                                    <div class="form-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             aria-hidden="true" role="img"
                                             width="1em" height="1em"
                                             viewBox="0 0 24 24"
                                             data-icon="feather:search"
                                             class="iconify iconify--feather">
                                            <g fill="none" stroke="currentColor"
                                               stroke-linecap="round"
                                               stroke-linejoin="round"
                                               stroke-width="2">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <path d="m21 21l-4.35-4.35"></path>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <LandingPages />
                    <AboutPages />
                    <PricingPages />
                    <BlogPages />
                    <SubPages />
                    <AuthPages />
                    <LayoutsPages />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
