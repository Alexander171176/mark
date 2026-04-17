<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { unwrap } from '@/composables/useUnwrap.js'
import MainArticleImageAutoSlider from '@/Components/Public/Default/Blog/Rubric/MainArticleImageAutoSlider.vue'

const props = defineProps({
    articles: { type: [Array, Object], default: () => [] }
})

const unwrapList = (v) => v?.data ?? v ?? []
const list = computed(() => unwrapList(props.articles))
</script>

<template>
    <div
        v-if="list.length !== 0"
        class="blog-recent-posts gap-3 my-4 grid grid-cols-1 lg:grid-cols-2"
    >
        <div v-for="a in list" :key="unwrap(a).id">
            <div
                class="blog-recent-posts-inner mb-4 shadow-lg
                       border border-gray-300 rounded-lg
                       dark:bg-[hsl(240_33%_12%)]"
            >
                <!-- изображение -->
                <MainArticleImageAutoSlider :article="a" class="block" />

                <div class="post-content px-2 pb-2">
                    <!-- ссылка ТОЛЬКО на title -->
                    <Link
                        :href="`/blog/articles/${unwrap(a).url}`"
                        class="block text-center mb-2"
                    >
                        <h3 class="title is-7 is-semi is-leading">
                          <span class="text-[14px]
                                       text-gray-600 dark:text-gray-200 hover:text-blue-600">
                            {{ unwrap(a).title }}
                          </span>
                        </h3>
                    </Link>

                    <div class="meta flex justify-center">
                        <span class="text-[12px]
                                     text-gray-500 dark:text-gray-400 hover:text-blue-600">
                          {{ unwrap(a).author }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
