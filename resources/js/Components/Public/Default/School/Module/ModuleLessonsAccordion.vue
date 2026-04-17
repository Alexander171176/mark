<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    lessons: { type: Array, default: () => [] },
})

const activeTooltipId = ref(null)

const showTooltip = (lessonId) => {
    activeTooltipId.value = lessonId
}

const hideTooltip = () => {
    activeTooltipId.value = null
}

const lessonImageUrl = (lesson) => {
    const images = Array.isArray(lesson?.images) ? lesson.images : []
    if (!images.length) return null

    const sorted = [...images].sort((a, b) => {
        const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
        const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
        return aOrder - bOrder
    })

    const first = sorted[0]

    return first?.webp_url
        || first?.url
        || first?.image_url
        || first?.thumb_url
        || null
}

const lessonHint = (lesson) => {
    return lesson?.short || lesson?.subtitle || ''
}

const lessonLink = (lesson) => {
    return lesson?.slug ? `/school/lessons/${lesson.slug}` : '#'
}
</script>

<template>
    <div
        v-if="lessons.length"
        class="mt-2 min-w-0 w-full max-w-full overflow-visible rounded-lg
               border border-slate-400 bg-slate-50 p-3 dark:bg-slate-950/40"
    >
        <ul class="space-y-2 min-w-0 w-full max-w-full">
            <li
                v-for="lesson in lessons"
                :key="lesson.id"
                class="min-w-0 w-full max-w-full"
            >
                <div
                    class="flex min-w-0 w-full max-w-full items-center gap-3
                           rounded-lg px-2 py-2 transition
                           hover:bg-slate-200 dark:hover:bg-slate-800"
                >
                    <Link
                        :href="lessonLink(lesson)"
                        class="shrink-0"
                    >
                        <img
                            v-if="lessonImageUrl(lesson)"
                            :src="lessonImageUrl(lesson)"
                            :alt="lesson.title"
                            class="h-10 w-10 rounded-md object-cover"
                            loading="lazy"
                        />

                        <div
                            v-else
                            class="flex h-10 w-10 items-center justify-center
                                   rounded-md bg-slate-200 text-slate-500
                                   dark:bg-slate-800 dark:text-slate-400"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M4 4h16v14H4zm2 2v10h12V6zm-2 14h16v2H4z"/>
                            </svg>
                        </div>
                    </Link>

                    <div class="min-w-0 flex-1 max-w-full overflow-hidden">
                        <Link
                            :href="lessonLink(lesson)"
                            class="block truncate text-sm font-medium text-slate-700
                                   transition hover:opacity-75 dark:text-slate-300"
                            :title="lesson.title"
                        >
                            {{ lesson.title }}
                        </Link>
                    </div>

                    <div
                        v-if="lesson.id !== undefined && lesson.id !== null"
                        class="relative shrink-0"
                    >
                        <button
                            type="button"
                            class="text-xs text-slate-400 dark:text-slate-600
                                   hover:text-slate-600 dark:hover:text-slate-400"
                            @mouseenter="showTooltip(lesson.id)"
                            @mouseleave="hideTooltip"
                        >
                            #{{ lesson.id }}
                        </button>

                        <div
                            v-if="activeTooltipId === lesson.id && lessonHint(lesson)"
                            class="pointer-events-none absolute bottom-full right-0 z-50 mb-2
                                   w-64 rounded-md border border-slate-400 dark:border-slate-600
                                   px-3 py-2 bg-white dark:bg-black shadow-lg
                                   text-left text-xs font-semibold leading-relaxed
                                   text-slate-700/95 dark:text-slate-300/95"
                        >
                            {{ lessonHint(lesson) }}

                            <div
                                class="absolute top-full right-3 h-2 w-2 rotate-45
                                       border-r border-b border-slate-400 dark:border-slate-600
                                       bg-white dark:bg-black"
                            />
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
