<script setup>
import { Link } from '@inertiajs/vue3'
import { unwrap } from '@/composables/useUnwrap.js'
import { useI18n } from 'vue-i18n'
import ArticleCardImage from '@/Components/Public/Default/Blog/Rubric/ArticleCardImage.vue'

const { t } = useI18n()

const props = defineProps({
    article: { type: Object, required: true },
})
</script>

<template>
    <div class="card">
        <div class="dark:bg-[hsl(240_33%_12%)] card-content p-4">
            <div class="media is-align-items-center">
                <div class="media-content">
                    <h3 class="title is-6 is-semi h-16">
                        <span class="text-gray-700 dark:text-gray-300 leading-normal">
                          {{ unwrap(article).title }}
                        </span>
                    </h3>
                </div>
            </div>

            <div class="content">
                <p class="paragraph h-20">
                    {{ unwrap(article).short }}
                </p>
            </div>

            <div class="is-flex is-align-items-center is-justify-content-space-between">
                <Link :href="`/blog/articles/${unwrap(article).url}`"
                      class="is-flex is-align-items-center">
                      <span class="mr-2 text-indigo-700 dark:text-indigo-300
                                   hover:text-slate-700 dark:hover:text-slate-300">
                        {{ t('readMore') }}
                      </span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         aria-hidden="true" role="img" width="1em" height="1em"
                         viewBox="0 0 24 24" class="iconify iconify--feather">
                        <path fill="none"
                              stroke="currentColor"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 12h14m-7-7l7 7l-7 7"></path>
                    </svg>
                </Link>

                <div class="card-stats is-flex is-align-items-center">
                    <div class="is-flex is-align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             aria-hidden="true" role="img" width="1em" height="1em"
                             viewBox="0 0 24 24"
                             class="iconify iconify--feather text-gray-600 dark:text-gray-400">
                            <g fill="none"
                               stroke="currentColor"
                               stroke-linecap="round"
                               stroke-linejoin="round"
                               stroke-width="2">
                                <path d="M1 12s4-8 11-8s11 8 11 8s-4 8-11 8s-11-8-11-8"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </g>
                        </svg>
                        <span class="ml-1 text-gray-600 dark:text-gray-400">
              {{ unwrap(article).views }}
            </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Изображение -->
        <ArticleCardImage :article="article" />
    </div>
</template>
