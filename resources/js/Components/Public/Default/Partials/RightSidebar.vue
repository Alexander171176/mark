<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import TagsSidebar from '@/Components/Public/Default/Blog/Rubric/TagsSidebar.vue'
import RecentArticlesSidebar from '@/Components/Public/Default/Blog/Rubric/RecentArticlesSidebar.vue'
import RightBannersSidebar from '@/Components/Public/Default/Blog/Banner/RightBannersSidebar.vue'
import RightVideosSidebar from '@/Components/Public/Default/Blog/Video/RightVideosSidebar.vue'

const emit = defineEmits(['collapsed'])

const { t } = useI18n()
const page = usePage()

/** данные страницы */
const tags = computed(() => page.props.tags ?? [])
const rightArticles = computed(() => page.props.rightArticles ?? [])
const rightBanners = computed(() => page.props.rightBanners ?? [])
const rightVideos = computed(() => page.props.rightVideos ?? [])

/** collapse */
const isCollapsed = ref(false)
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value
    emit('collapsed', isCollapsed.value)
}

/** базовые классы */
const sidebarClasses = computed(() => {
    return [
        'transition-all',
        'duration-300',
        'p-2',
        'w-full',
    ].join(' ')
})
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
                <!-- right: как было -->
                <svg
                    v-if="isCollapsed"
                    class="w-6 h-6 text-indigo-500 dark:text-indigo-400"
                    fill="currentColor"
                    viewBox="0 0 448 512">
                    <path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"/>
                </svg>
                <svg
                    v-else
                    class="w-6 h-6 text-indigo-500 dark:text-indigo-400"
                    fill="currentColor"
                    viewBox="0 0 448 512">
                    <path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"/>
                </svg>
            </button>
        </div>

        <!-- содержимое только когда развернуто -->
        <div v-show="!isCollapsed" class="flex flex-col gap-4">
            <TagsSidebar :tags="tags" />
            <RecentArticlesSidebar :articles="rightArticles" title-key="articles" />
            <RightBannersSidebar :right-banners="rightBanners" />
            <RightVideosSidebar :videos="rightVideos" />
        </div>
    </div>
</template>
