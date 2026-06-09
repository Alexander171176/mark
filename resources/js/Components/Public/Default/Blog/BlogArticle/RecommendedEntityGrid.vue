<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    items: {
        type: [Array, Object],
        default: () => [],
    },

    title: {
        type: String,
        default: '',
    },

    routeName: {
        type: String,
        required: true,
    },

    routeParamKey: {
        type: String,
        default: 'url',
    },

    columnsClass: {
        type: String,
        default: 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-3',
    },

    showViews: {
        type: Boolean,
        default: true,
    },

    emptyTitleFallback: {
        type: String,
        default: '',
    },
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const itemsList = computed(() => normalizeList(props.items))

const buildHref = (item) => {
    const routeParamValue = item?.[props.routeParamKey]

    if (!routeParamValue) return '#'

    return route(props.routeName, routeParamValue)
}

const getImage = (item) => {
    if (Array.isArray(item?.images) && item.images.length) {
        return item.images[0]
    }

    if (item?.thumbnail_url) {
        return {
            webp_url: item.thumbnail_url,
            url: item.thumbnail_url,
            image_url: item.thumbnail_url,
            alt: item?.title || props.emptyTitleFallback || '',
        }
    }

    return null
}
</script>

<template>
    <div v-if="itemsList.length" class="mt-8">
        <h2
            v-if="title"
            class="mb-4 tracking-wide text-center font-semibold text-lg
                   text-gray-700 dark:text-gray-300"
        >
            {{ title }}
        </h2>

        <div :class="columnsClass">
            <Link
                v-for="item in itemsList"
                :key="item.id"
                :href="buildHref(item)"
                class="rounded-sm overflow-hidden
                       border border-slate-400 dark:border-slate-500
                       shadow-md shadow-gray-400 dark:shadow-gray-800"
            >
                <div class="relative w-full">
                    <div
                        v-if="getImage(item)"
                        class="w-full aspect-[4/3] overflow-hidden"
                    >
                        <img
                            :src="getImage(item).webp_url || getImage(item).url || getImage(item).image_url"
                            :alt="getImage(item).alt || item.title || emptyTitleFallback"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3 title my-3 px-2">
                        <h3 class="text-sm font-bold text-center">
                            {{ item.title || emptyTitleFallback }}
                        </h3>

                        <div
                            v-if="showViews"
                            :title="t('views')"
                            class="flex items-center justify-center gap-1"
                        >
                            <svg
                                class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 576 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                />
                            </svg>

                            <span class="text-center text-sm text-gray-500">
                                {{ item.views || 0 }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="item.short"
                        class="px-2 mt-1 mb-3 text-xs subtitle text-center"
                    >
                        {{ item.short }}
                    </div>

                    <!-- Owner -->
                    <div v-if="item?.owner"
                         class="my-4 flex items-center justify-center gap-2">
                        <img
                            v-if="item.owner?.profile_photo_url"
                            :src="item.owner.profile_photo_url"
                            :alt="item.owner.name"
                            loading="lazy"
                            class="h-8 w-8 rounded-full object-cover
                                           ring-1 ring-gray-200 dark:ring-gray-700"
                        />
                        <div class="min-w-0 text-sm font-semibold
                                text-slate-700/85 dark:text-slate-300/85">
                            {{ item.owner?.name }}
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
