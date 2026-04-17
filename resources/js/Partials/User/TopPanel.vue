<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const {t} = useI18n();
const isPanelOpen = ref(false)

const togglePanel = () => {
    isPanelOpen.value = !isPanelOpen.value
}
</script>

<template>
    <div>
        <button
            class="fixed top-0 right-3 z-50 hidden md:inline-block
                    px-3 py-3
                    cursor-pointer"
            @click="togglePanel">
            <svg class="w-6 h-6" viewBox="0 0 20 20">
                <circle fill="none" class="stroke-violet-500" cx="9.997" cy="10" r="3.31"></circle>
                <path fill="none" class="stroke-violet-500"
                      d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z"></path>
            </svg>
        </button>
        <transition name="top-panel">
            <div v-if="isPanelOpen"
                 class="fixed top-0 left-0 right-0
                        border-b-2 border-slate-200 dark:border-slate-800
                        bg-slate-700 bg-opacity-90 dark:bg-opacity-90
                        shadow-md font-semibold
                        text-center text-lg
                        z-40 h-12 py-2
                        overflow-y-auto
                        flex items-center justify-center
                        space-x-4">



            </div>
        </transition>
    </div>
</template>

<style scoped>
.top-panel-enter-active,
.top-panel-leave-active {
    transition: transform 0.3s ease;
}
.top-panel-enter-from,
.top-panel-leave-to {
    transform: translateY(-100%);
}
.top-panel-enter-to,
.top-panel-leave-from {
    transform: translateY(0);
}

.base-link {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #e2e8f0; /* slate-200 */
    font-size: 11px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.base-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
.active-link {
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-weight: 600;
}
</style>
