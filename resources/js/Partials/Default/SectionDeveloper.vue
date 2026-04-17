<script setup>
import SectionHeader from '@/Components/Public/Default/Shared/SectionHeader.vue'
import DevFeature from '@/Components/Public/Default/SectionDeveloper/DevFeature.vue'
</script>

<template>
    <div class="dark:bg-[hsl(240_33%_9%)]">
        <div class="section-content">
            <div class="container">

                <SectionHeader class="mt-8"
                    subtitle="Developer Experience"
                    title="Build a professional website"
                />

                <div class="py-6">
                    <div class="columns is-multiline b-columns-half-tablet-p">
                        <DevFeature
                            title="Vue 3"
                            text="Vue 3 and its powerful composition API makes your experience
                                  pleasant along with killer features."
                            lightSrc="./../../../images/vulk/vuejs-light.svg"
                        />
                        <DevFeature
                            title="Typescript"
                            text="Typescript makes things much more simpler but remains optional.
                                  A good occasion to learn about it!"
                            lightSrc="./../../../images/vulk/typescript-light.svg"
                        />
                        <DevFeature
                            title="Vite JS"
                            text="Vite is blazing fast, does HOT reloading and parses
                                  all your components. 100x faster than Webpack."
                            lightSrc="./../../../images/vulk/vite-light.svg"
                        />
                        <DevFeature
                            title="VS Code Integration"
                            text="Vulk is fully integrated with VS Code to help you produce
                                  quality checked code. Supports Typescript."
                            lightSrc="./../../../images/vulk/vscode-light.svg"
                        />
                        <DevFeature
                            title="ES Lint"
                            text="ES Lint watches your javascript code and makes sure it matches
                                  the best standards. Code with standards."
                            lightSrc="./../../../images/vulk/eslint-light.svg"
                        />
                        <DevFeature
                            title="Prettier"
                            text="Prettier tidies your code and makes it look much more cleaner
                                  and easier to read. Read code easily."
                            lightSrc="./../../../images/vulk/prettier-light.svg"
                        />
                        <DevFeature
                            title="Stylelint"
                            text="Stylelint looks for poorly formatted styles and fixes
                                  everything for you. Learn to write valid CSS."
                            lightSrc="./../../../images/vulk/stylelint-light.svg"
                        />
                        <DevFeature
                            title="Docker"
                            text="Vulk ships with a Docker file to make your test deployments
                                  faster and easier. Get started fast!"
                            lightSrc="./../../../images/vulk/docker-light.svg"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
