<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import TracksSidebar from '@/Components/Public/Default/School/Track/TracksSidebar.vue'
import RecentCoursesSidebar from '@/Components/Public/Default/School/Course/RecentCoursesSidebar.vue'
import LeftBannersSidebar from '@/Components/Public/Default/Blog/Banner/LeftBannersSidebar.vue'
import LeftVideosSidebar from '@/Components/Public/Default/Blog/Video/LeftVideosSidebar.vue'

const props = defineProps({
    trackTree: { type: Array, default: () => [] },
})

const emit = defineEmits(['collapsed'])

const { t } = useI18n()
const page = usePage()

const leftCourses = computed(() => page.props.leftCourses ?? [])
const leftBanners = computed(() => page.props.leftBanners ?? [])
const leftVideos = computed(() => page.props.leftVideos ?? [])

const isCollapsed = ref(false)

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value
    emit('collapsed', isCollapsed.value)
}
</script>

<template>
    <div class="relative">
        <div class="flex items-center justify-center">
            <button
                type="button"
                @click="toggleSidebar"
                class="focus:outline-none"
                :title="t('toggleSidebar')"
            >
                <svg
                    v-if="isCollapsed"
                    class="w-6 h-6 text-indigo-500 dark:text-indigo-400"
                    fill="currentColor"
                    viewBox="0 0 448 512"
                >
                    <path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"/>
                </svg>

                <svg
                    v-else
                    class="w-6 h-6 text-indigo-500 dark:text-indigo-400"
                    fill="currentColor"
                    viewBox="0 0 448 512"
                >
                    <path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"/>
                </svg>
            </button>
        </div>

        <div v-show="!isCollapsed" class="flex flex-col gap-4">
            <TracksSidebar :tracks="props.trackTree" />
            <RecentCoursesSidebar :courses="leftCourses" title-key="courses" />
            <LeftBannersSidebar :left-banners="leftBanners" />
            <LeftVideosSidebar :videos="leftVideos" />
        </div>
    </div>
</template>
