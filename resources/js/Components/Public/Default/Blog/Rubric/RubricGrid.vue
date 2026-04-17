<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'

const { t } = useI18n()

const props = defineProps({
    rubrics: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
})

const gridClass = computed(() => {
    return props.cols === 3
        ? 'grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3'
        : 'grid grid-cols-1 gap-4 sm:grid-cols-2'
})
</script>

<template>
    <div :class="gridClass">
        <div
            v-for="rubric in rubrics"
            :key="rubric.id"
            class="group flex h-full flex-col overflow-hidden rounded-md
                  border border-gray-200 bg-white shadow-sm
                  transition hover:-translate-y-0.5 hover:shadow-md
                  dark:border-gray-700 dark:bg-gray-900"
        >
            <Link :href="route('public.rubrics.show', rubric.url)">
                <UniversalImageSlider
                    :entity="rubric"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-between gap-2">
                    <Link
                        :href="route('public.rubrics.show', rubric.url)"
                        :title="`${t('articles')}: ${rubric.articles_count}`"
                        class="inline-flex items-center gap-1"
                    >
                        <span v-if="rubric.icon" class="flex" v-html="rubric.icon" />
                        <span
                            class="text-lg font-semibold text-slate-900/85
                                   group-hover:opacity-75 dark:text-slate-100/85
                                   dark:group-hover:opacity-75"
                        >
                            {{ rubric.title }}
                        </span>
                    </Link>

                    <div v-if="rubric?.views > 0" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                             xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 576 512"
                             fill="currentColor">
                            <path
                                d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                            />
                        </svg>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            {{ rubric.views }}
                        </span>
                    </div>
                </div>

                <div v-if="rubric.short"
                     class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300">
                    {{ rubric.short }}
                </div>

                <div v-if="rubric?.owner" class="mt-4 flex items-center justify-center gap-2">
                    <img
                        v-if="rubric.owner?.profile_photo_url"
                        :src="rubric.owner.profile_photo_url"
                        :alt="rubric.owner.name"
                        loading="lazy"
                        class="h-6 w-6 rounded-full object-cover
                               ring-1 ring-gray-200 dark:ring-gray-700"
                    />
                    <div class="min-w-0 text-xs font-semibold
                                text-slate-700/85 dark:text-slate-300/85">
                        {{ rubric.owner?.name }}
                    </div>
                </div>

                <div class="mt-auto pt-4">
                    <Link
                        :href="route('public.rubrics.show', rubric.url)"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-2 btn-default"
                    >
                        <span class="text-sm font-semibold">{{ t('readMore') }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                  clip-rule="evenodd" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
