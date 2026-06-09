<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { unwrap, unwrapList } from '@/composables/useUnwrap'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const props = defineProps({
    tags: { type: [Array, Object], default: () => [] },
})

const list = computed(() => unwrapList(props.tags))
</script>

<template>
    <!-- Блок тегов блога -->
    <div class="flex flex-wrap items-center gap-2">
        <Link
            v-for="tag in list"
            :key="unwrap(tag).id"
            :href="`/blog/tags/${unwrap(tag).slug}`"
            aria-current="page"
            class="flex items-center justify-start gap-2
                   px-3 py-1 transition rounded-md
                   font-semibold text-xs text-gray-700 dark:text-gray-300
                   dark:hover:text-slate-300
                   border border-gray-400 dark:border-gray-400
                   hover:bg-slate-200 dark:hover:bg-slate-800"
        >
            {{ unwrap(tag).name }}
        </Link>
    </div>
</template>
