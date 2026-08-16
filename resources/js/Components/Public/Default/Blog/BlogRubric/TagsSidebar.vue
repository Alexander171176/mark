<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { unwrap, unwrapList } from '@/composables/useUnwrap'

const props = defineProps({
    tags: {
        type: [Array, Object],
        default: () => [],
    },
})

/** Нормализованный список тегов */
const list = computed(() => unwrapList(props.tags))

/** Имя тега из Public Resource */
const getTagName = (tag) => {
    const item = unwrap(tag)

    return item?.translation?.name || ''
}

/** URL страницы тега */
const getTagUrl = (tag) => {
    const item = unwrap(tag)

    return `/blog/tags/${item?.slug || ''}`
}
</script>

<template>
    <!-- Облако тегов блога -->
    <div class="flex flex-wrap items-center gap-2">
        <Link
            v-for="tag in list"
            :key="unwrap(tag).id"
            :href="getTagUrl(tag)"
            aria-current="page"
            class="flex items-center justify-start gap-2
                   px-3 py-1 transition rounded-md
                   font-semibold text-xs text-gray-700 dark:text-gray-300
                   dark:hover:text-slate-300
                   border border-gray-400 dark:border-gray-400
                   hover:bg-slate-200 dark:hover:bg-slate-800"
        >
            {{ getTagName(tag) }}
        </Link>
    </div>
</template>
