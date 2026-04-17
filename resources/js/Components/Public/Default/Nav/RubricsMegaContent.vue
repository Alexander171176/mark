<script setup>
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'

const { t } = useI18n()

const rubrics = ref([])
const loading = ref(false)

const leftRubrics = computed(() => {
    const half = Math.ceil(rubrics.value.length / 2)
    return rubrics.value.slice(0, half)
})

const rightRubrics = computed(() => {
    const half = Math.ceil(rubrics.value.length / 2)
    return rubrics.value.slice(half)
})

onMounted(async () => {
    loading.value = true
    try {
        const { data } = await axios.get(route('public.rubrics.menu'))
        // console.log('menu rubrics response:', data)
        rubrics.value = data?.rubrics ?? []
    } catch (e) {
        console.error('menu rubrics error:', e)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="container">
        <div class="columns">
            <!-- Левый информационный блок оставляем как есть -->
            <div class="column is-4">
                <div class="megamenu-block">
                    <div class="media">
                        <div class="media-left is-resources">
                            <svg class="shrink-0 h-8 w-8" viewBox="0 0 24 24">
                                <circle class="fill-current text-cyan-600" cx="16" cy="8" r="8"></circle>
                                <circle class="fill-current text-cyan-400" cx="8" cy="16" r="8"></circle>
                            </svg>
                        </div>
                        <div class="media-content">
                            <h3>{{ t('rubrics') }}</h3>
                            <p>
                                Подборка материалов по ключевым направлениям: программирование,
                                архитектура, веб-разработка и современные технологии.
                            </p>

                            <Link :href="route('public.rubrics.index')" class="flex flex-row items-center">
                                <span>{{ t('readMore')}}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img"
                                     width="1em" height="1em" viewBox="0 0 24 24"
                                     class="iconify iconify--feather">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="M5 12h14m-7-7l7 7l-7 7"></path>
                                </svg>
                            </Link>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Колонка 1: рубрики -->
            <div class="column is-2">
                <div class="megamenu-block ml-6">
                    <h4>{{ t('rubrics') }}</h4>

                    <div v-if="loading" class="text-sm opacity-70">...</div>

                    <ul v-else>
                        <li v-for="r in leftRubrics" :key="r.id">
                            <Link
                                :href="route('public.rubrics.show', { url: r.url })"
                                class="list-link"
                            >
                                {{ r.title }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Колонка 2: рубрики -->
            <div class="column is-2">
                <div class="megamenu-block ml-6">
                    <h4>&nbsp;</h4>
                    <ul>
                        <li v-for="r in rightRubrics" :key="r.id">
                            <Link
                                :href="route('public.rubrics.show', { url: r.url })"
                                class="list-link"
                            >
                                {{ r.title }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Правая картинка оставляем -->
            <div class="column is-4 b-hidden-mobile b-hidden-tablet-p">
                <div class="megamenu-block is-left-bordered">
                    <div class="image-block h-32">
                        <div class="h-fit">
                            <img class="is-image is-image-block is-image is-image-light"
                                 src="./../../../../../images/vulk/landing-35.png"
                                 alt="" width="500" height="309" loading="lazy">
                            <img class="is-image is-image-block is-image is-image-dark"
                                 src="./../../../../../images/vulk/landing-35-dark.png"
                                 alt="" width="500" height="309" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
