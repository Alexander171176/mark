<script setup>
import SectionHeader from '@/Components/Public/Default/Shared/SectionHeader.vue'
import ParagraphColumns from '@/Components/Public/Default/SectionWave/ParagraphColumns.vue'
import TechBadgesRow from '@/Components/Public/Default/SectionWave/TechBadgesRow.vue'
import WaveSeparator from '@/Components/Public/Default/Shared/WaveSeparator.vue'

const leftText = `Vulk is built on top of Vue 3 and its new Composition API.
One of the most significant changes is that this new API allows function-based ways
of writing your components, inspired by React Hooks. It makes it easy to create components
in a matter of minutes.`

const rightText = `Vulk's philosophy is ultra modular and optimized for better performance.
It also provides other quality standards such as web accessibility. Although it is built with Sass,
all used variables are native CSS variables, which makes it a lot easier to theme.`

const tech = [
    {
        img: './../../../images/vulk/vuejs-light.svg',
        alt: 'Vue.js logo',
        title: 'Vue 3',
        subtitle: 'Frontend framework',
        description: 'Vue 3 is a robust and overpowered rewrite of Vue 2.'
    },
    {
        img: './../../../images/vulk/vite-light.svg',
        alt: 'Vite logo',
        title: 'Vite JS',
        subtitle: 'Compiler / Bundler',
        description: 'Vite is a brand new compiler that is 100x faster than Webpack.'
    },
    {
        img: './../../../images/vulk/typescript-light.svg',
        alt: 'TypeScript logo',
        title: 'Typescript',
        subtitle: 'Javascript framework',
        description: 'TypeScript is a strongly typed language that builds on JS.'
    },
    {
        img: './../../../images/vulk/sass-light.svg',
        alt: 'Sass logo',
        title: 'Sass',
        subtitle: 'CSS Preprocessor',
        description: 'Sass is a compiler that builds SCSS and SASS to CSS.'
    },
    {
        img: './../../../images/vulk/bulma-light.svg',
        alt: 'Bulma logo',
        title: 'Bulma',
        subtitle: 'CSS Framework',
        description: 'Bulma is a mobile-first CSS framework based on Flexbox.'
    }
]
</script>

<template>
    <div class="dark:bg-[hsl(240_33%_9%)]">
        <div class="section-content">
            <div class="container">
                <div class="pt-6 mx-auto max-w-8">
                    <div class="has-text-centered mb-1">
                        <SectionHeader
                            subtitle="Beautiful, Powerful, Reliable"
                            title="The Ultimate Web UI Kit"
                        />
                    </div>

                    <ParagraphColumns :left="leftText" :right="rightText" />
                    <TechBadgesRow :items="tech" />
                </div>
            </div>
        </div>
        <WaveSeparator />
    </div>
</template>
