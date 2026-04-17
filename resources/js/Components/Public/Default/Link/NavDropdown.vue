<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    links: {
        type: Array,
        default: () => [],
    },
})

const isOpen = ref(false)

const isActiveLink = (link) => {
    if (Array.isArray(link.active)) {
        return link.active.some(pattern => route().current(pattern))
    }

    if (typeof link.active === 'string' && link.active.length) {
        return route().current(link.active)
    }

    return route().current(link.route)
}

const isDropdownActive = computed(() => {
    return props.links.some(link => isActiveLink(link))
})
</script>

<template>
    <div
        class="relative"
        @mouseenter="isOpen = true"
        @mouseleave="isOpen = false"
    >
        <button
            type="button"
            :class="[
                'font-semibold text-md flex items-center gap-1 transition',
                isDropdownActive ? 'top-link-active' : 'top-link'
            ]"
        >
            {{ title }}

            <svg
                class="w-4 h-4 opacity-70 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path d="M5 7l5 5 5-5" />
            </svg>
        </button>

        <div
            v-show="isOpen"
            class="absolute left-0 top-full pt-1 z-50"
        >
            <div
                class="w-64 rounded-lg shadow-lg
                       bg-white dark:bg-gray-900 border
                       border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <Link
                    v-for="link in links"
                    :key="link.route"
                    :href="route(link.route)"
                    :class="[
                        'flex items-center gap-3 px-4 py-2 text-sm transition',
                        isActiveLink(link)
                            ? 'bg-indigo-100 text-indigo-700 ' +
                             'dark:bg-indigo-900/40 dark:text-indigo-300'
                            : 'text-slate-700 dark:text-slate-300 ' +
                             'hover:bg-gray-100 dark:hover:bg-gray-900'
                    ]"
                >
                    <span
                        v-if="link.icon"
                        class="shrink-0 flex items-center justify-center"
                        v-html="link.icon"
                    />

                    <span class="truncate">
                        {{ link.label }}
                    </span>
                </Link>
            </div>
        </div>
    </div>
</template>
