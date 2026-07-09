<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    page: {
        type: Object,
        required: true,
    },
})

const isOpen = ref(false)

const getTranslation = (item) => {
    return item?.translation
        || item?.translations?.[0]
        || null
}

const getTitle = (item) => {
    return item?.title
        || getTranslation(item)?.title
        || `ID: ${item?.id}`
}

const getChildren = (item) => {
    return item?.public_menu_children || []
}

const isActiveUrl = (url) => {
    return window.location.pathname === url
        || window.location.pathname.startsWith(url + '/')
}

const isDropdownActive = computed(() => {
    if (isActiveUrl(props.page.url)) {
        return true
    }

    return getChildren(props.page).some(child => isActiveUrl(child.url))
})
</script>

<template>
    <div
        class="relative"
        @mouseenter="isOpen = true"
        @mouseleave="isOpen = false"
    >
        <Link
            :href="page.url"
            :class="[
                'font-semibold text-md flex items-center gap-1 transition',
                isDropdownActive ? 'top-link-active' : 'top-link'
            ]"
        >
            {{ getTitle(page) }}

            <svg
                v-if="getChildren(page).length"
                class="w-4 h-4 opacity-70 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path d="M5 7l5 5 5-5" />
            </svg>
        </Link>

        <div
            v-if="getChildren(page).length"
            v-show="isOpen"
            class="absolute left-0 top-full pt-1 z-50"
        >
            <div
                class="w-64 rounded-lg shadow-lg
                       bg-white dark:bg-gray-900 border
                       border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <Link
                    v-for="child in getChildren(page)"
                    :key="child.id"
                    :href="child.url"
                    :class="[
                        'flex items-center gap-3 px-4 py-2 text-sm transition',
                        isActiveUrl(child.url)
                            ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
                            : 'text-slate-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-gray-900'
                    ]"
                >
                    <span
                        v-if="child.icon"
                        class="shrink-0 flex items-center justify-center"
                        v-html="child.icon"
                    />

                    <span class="truncate">
                        {{ getTitle(child) }}
                    </span>
                </Link>
            </div>
        </div>
    </div>
</template>
