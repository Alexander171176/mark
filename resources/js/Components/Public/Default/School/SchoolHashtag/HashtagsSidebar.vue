<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

import {
    unwrap,
    unwrapList,
} from '@/composables/useUnwrap'

const props = defineProps({
    hashtags: {
        type: [Array, Object],
        default: () => [],
    },
})

const list = computed(() =>
    unwrapList(
        props.hashtags
    )
)

const hashtagName = (hashtag) => {
    const item = unwrap(hashtag)

    return item?.translation?.name
        || ''
}
</script>

<template>
    <!-- Облако хештегов школы -->
    <div
        v-if="list.length"
        class="flex flex-wrap items-center gap-2"
    >
        <Link
            v-for="hashtag in list"
            :key="unwrap(hashtag).id"
            :href="route(
                'public.schoolHashtags.show',
                {
                    slug:
                        unwrap(hashtag).slug,
                }
            )"
            class="flex items-center justify-start gap-2
                   rounded-md border border-gray-400
                   px-3 py-1 text-xs font-semibold
                   text-gray-700 transition
                   hover:bg-slate-200
                   dark:border-gray-400
                   dark:text-gray-300
                   dark:hover:bg-slate-800
                   dark:hover:text-slate-300"
        >
            {{ hashtagName(hashtag) }}
        </Link>
    </div>
</template>
