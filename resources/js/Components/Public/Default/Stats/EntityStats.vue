<script setup>
import { useI18n } from 'vue-i18n'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

const { t } = useI18n()

const props = defineProps({
    views: {
        type: Number,
        default: 0,
    },

    likesCount: {
        type: Number,
        default: 0,
    },

    alreadyLiked: {
        type: Boolean,
        default: false,
    },

    routeName: {
        type: String,
        default: '',
    },

    routeParams: {
        type: [Object, Array, String, Number],
        default: () => ({}),
    },

    compact: {
        type: Boolean,
        default: false,
    },

    showLikesButton: {
        type: Boolean,
        default: false,
    },
})
</script>

<template>
    <div
        class="mt-1 inline-flex items-center gap-3"
        :class="compact ? 'text-xs' : 'text-sm'"
    >
        <div
            v-if="views > 0"
            class="inline-flex items-center gap-1 text-xs"
        >
            <span class="font-semibold text-slate-600/85 dark:text-slate-400/85">
                {{ t('views') }}:
            </span>
            <span class="font-semibold text-slate-600/85 dark:text-slate-400/85">
                {{ views }}
            </span>
            <svg
                class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 576 512"
                fill="currentColor"
            >
                <path
                    d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                />
            </svg>
        </div>

        <LikeButtonEntity
            v-if="showLikesButton && routeName"
            :likes-count="likesCount"
            :already-liked="alreadyLiked"
            :route-name="routeName"
            :route-params="routeParams"
            icon-class="w-3 h-3 hover:scale-110 active:scale-95"
        />

        <div
            v-else-if="likesCount > 0"
            class="inline-flex items-center gap-1"
        >
            <svg
                class="h-4 w-4 text-rose-500 dark:text-rose-300"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
            >
                <path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                <path
                    d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"
                ></path>
            </svg>

            <span class="font-semibold text-slate-500 dark:text-slate-400">
                {{ likesCount }}
            </span>
        </div>
    </div>
</template>
