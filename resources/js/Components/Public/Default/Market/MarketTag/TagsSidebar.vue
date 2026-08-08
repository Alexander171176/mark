<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    tags: { type: [Array, Object], default: () => [] },
})

/** Нормализованный список тегов */
const tagsList = computed(() => {
    if (Array.isArray(props.tags)) {
        return props.tags
    }

    if (Array.isArray(props.tags?.data)) {
        return props.tags.data
    }

    return []
})

/** Ссылка на тег */
const tagLink = (tag) => {
    return tag?.url
        ? route('public.marketTags.show', { url: tag.url })
        : '#'
}

/** Название тега */
const getTagTitle = (tag) => {
    return tag?.title
        || tag?.translation?.title
        || tag?.translations?.[0]?.title
        || ''
}

/** Проверка SVG-иконки */
const hasSvgIcon = (tag) => {
    return typeof tag?.icon === 'string'
        && tag.icon.trim().startsWith('<svg')
}
</script>

<template>
    <!-- Облако тегов маркетплейса -->
    <div
        v-if="tagsList.length"
        class="flex flex-wrap items-center gap-2"
    >
        <Link
            v-for="tag in tagsList"
            :key="tag.id"
            :href="tagLink(tag)"
            :title="getTagTitle(tag)"
            class="flex items-center justify-start gap-1.5
                   rounded-md border border-gray-400
                   px-3 py-1 text-xs font-semibold
                   text-gray-700 transition
                   hover:bg-slate-200
                   dark:border-gray-400 dark:text-gray-300
                   dark:hover:bg-slate-800 dark:hover:text-slate-300"
        >
            <!-- Иконка -->
            <span
                v-if="hasSvgIcon(tag)"
                class="flex h-3.5 w-3.5 shrink-0 items-center justify-center"
                v-html="tag.icon"
            />

            <!-- Цвет тега -->
            <span
                v-else-if="tag.color"
                class="h-2.5 w-2.5 shrink-0 rounded-full"
                :style="{ backgroundColor: tag.color }"
            />

            <!-- Название -->
            <span>
                #{{ getTagTitle(tag) }}
            </span>
        </Link>
    </div>
</template>
