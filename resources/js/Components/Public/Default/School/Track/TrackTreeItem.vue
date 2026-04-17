<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

defineOptions({
    name: 'TrackTreeItem',
})

import { computed, ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    item: { type: Object, required: true },
    depth: { type: Number, default: 0 },
})

const STORAGE_KEY = 'trackAccordionOpen'

const hasChildren = computed(() => {
    return Array.isArray(props.item?.children) && props.item.children.length > 0
})

const isOpen = ref(false)

const loadState = () => {
    try {
        const raw = localStorage.getItem(STORAGE_KEY)
        const openIds = raw ? JSON.parse(raw) : []

        if (Array.isArray(openIds)) {
            isOpen.value = openIds.includes(props.item.id)
        }
    } catch {
        isOpen.value = false
    }
}

const saveState = (open) => {
    try {
        const raw = localStorage.getItem(STORAGE_KEY)
        let openIds = raw ? JSON.parse(raw) : []

        if (!Array.isArray(openIds)) openIds = []

        if (open) {
            if (!openIds.includes(props.item.id)) {
                openIds.push(props.item.id)
            }
        } else {
            openIds = openIds.filter(id => id !== props.item.id)
        }

        localStorage.setItem(STORAGE_KEY, JSON.stringify(openIds))
    } catch {}
}

const toggleAccordion = () => {
    if (!hasChildren.value) return

    isOpen.value = !isOpen.value
    saveState(isOpen.value)
}

onMounted(() => {
    if (hasChildren.value) {
        loadState()
    }
})
</script>

<template>
    <div class="w-full">
        <div
            class="flex items-center justify-between gap-0.5
                   my-0.5 rounded-sm transition
                   hover:bg-slate-200 dark:hover:bg-slate-800"
            :style="{ paddingLeft: `${depth * 16}px` }"
        >
            <div class="flex min-w-0 flex-1 items-center gap-2 pr-2 py-1">
                <button
                    v-if="hasChildren"
                    type="button"
                    @click="toggleAccordion"
                    class="flex min-w-0 flex-1 items-center justify-start gap-1 text-left"
                >
                    <svg
                        class="h-3.5 w-3.5 shrink-0 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-90': isOpen }"
                        viewBox="0 0 320 512"
                        fill="currentColor"
                    >
                        <path d="M96 96l128 160L96 416z" />
                    </svg>

                    <span class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ item.name }}
                    </span>
                </button>

                <Link
                    v-else
                    :href="route('public.tracks.show', { slug: item.slug })"
                    class="min-w-0 flex-1"
                >
                    <span class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ item.name }}
                    </span>
                </Link>
            </div>

            <Link
                v-if="hasChildren"
                :href="route('public.tracks.show', { slug: item.slug })"
                class="mr-2 shrink-0 rounded-sm px-2 py-1 text-[10px]
                       font-semibold text-indigo-700 dark:text-indigo-300
                       hover:bg-indigo-500 hover:text-slate-100
                       dark:hover:bg-indigo-500"
                :title="t('openLink')"
            >
                →
            </Link>
        </div>

        <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[1000px]"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-[1000px]"
            leave-to-class="opacity-0 max-h-0"
        >
            <div
                v-if="hasChildren && isOpen"
                class="overflow-hidden"
            >
                <TrackTreeItem
                    v-for="child in item.children"
                    :key="child.id"
                    :item="child"
                    :depth="depth + 1"
                />
            </div>
        </transition>
    </div>
</template>
