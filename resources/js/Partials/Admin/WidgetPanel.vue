<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'
import AdminTranslationWidget from '@/Components/Admin/UI/Widget/AdminTranslationWidget.vue'

const { t } = useI18n()

const page = usePage()

const adminSettings = computed(() => page.props?.adminSettings || {})

const isDarkMode = ref(false)
const isTranslatorOpen = ref(false)

let observer

const checkDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark')
}

const toggleTranslator = () => {
    isTranslatorOpen.value = !isTranslatorOpen.value
    localStorage.setItem('adminTranslatorOpen', String(isTranslatorOpen.value))
}

const closeTranslator = () => {
    isTranslatorOpen.value = false
    localStorage.setItem('adminTranslatorOpen', 'false')
}

onMounted(() => {
    checkDarkMode()

    isTranslatorOpen.value = localStorage.getItem('adminTranslatorOpen') === 'true'

    observer = new MutationObserver(checkDarkMode)
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    })
})

onUnmounted(() => {
    if (observer) observer.disconnect()
})

const bgColorClass = computed(() => {
    return isDarkMode.value
        ? (adminSettings.value.adminSidebarDarkColor || 'bg-gray-700')
        : (adminSettings.value.adminSidebarLightColor || 'bg-cyan-800');
})

const colorText = computed(() => {
    return isDarkMode.value
        ? (adminSettings.value.adminSidebarDarkText || 'text-slate-200')
        : (adminSettings.value.adminSidebarLightText || 'text-slate-200')
})

const colorTextHover = computed(() => {
    return isDarkMode.value
        ? (adminSettings.value.adminSidebarDarkHoverText || 'text-orange-300')
        : (adminSettings.value.adminSidebarLightHoverText || 'text-orange-300')
})

const hoveredIcon = ref(null)

const setHoveredIcon = (icon) => {
    hoveredIcon.value = icon
}

const clearHoveredIcon = () => {
    hoveredIcon.value = null
}

const iconClass = computed(() => {
    return `w-4 h-4 shrink-0 mr-2 transition duration-150 ease-in-out`
})

const iconPathClass = (key) => {
    return hoveredIcon.value === key
        ? colorTextHover.value
        : colorText.value
}
</script>

<template>
    <div class="row-span-full">
        <div
            id="widgetPanel"
            :class="[bgColorClass]"
            class="flex-col items-center
                     h-full w-4 z-50
                     dark:border-l dark:border-gray-600
                     overflow-y-scroll
                     hidden md:flex md:z-50
                     no-scrollbar
                     transition-all duration-200 ease-in-out"
        >
            <a
                href="/"
                target="_blank"
                class="mt-16 ml-1"
                :title="t('website')"
                @mouseenter="setHoveredIcon('site')"
                @mouseleave="clearHoveredIcon"
            >
                <svg
                    :class="iconClass"
                    viewBox="0 0 16 16"
                >
                    <path
                        :class="['fill-current', iconPathClass('site')]"
                        d="M10 16h4c.6 0 1-.4 1-.998V6.016c0-.3-.1-.6-.4-.8L8.6.226c-.4-.3-.9-.3-1.3 0l-6 4.992c-.2.2-.3.5-.3.799v8.986C1 15.6 1.4 16 2 16h4c.6 0 1-.4 1-.998v-2.996h2v2.996c0 .599.4.998 1 .998Zm-4-5.99c-.6 0-1 .399-1 .998v2.995H3V6.515L8 2.32l5 4.194v7.488h-2v-2.995c0-.6-.4-.999-1-.999H6Z"
                    />
                </svg>
            </a>

            <a
                href="/dashboard"
                target="_blank"
                class="mt-3 ml-1"
                :title="t('dashboard')"
                @mouseenter="setHoveredIcon('dashboard')"
                @mouseleave="clearHoveredIcon"
            >
                <svg :class="iconClass" viewBox="0 0 16 16">
                    <path
                        :class="['fill-current', iconPathClass('dashboard')]"
                        d="M12.311 9.527c-1.161-.393-1.85-.825-2.143-1.175A3.991 3.991 0 0012 5V4c0-2.206-1.794-4-4-4S4 1.794 4 4v1c0 1.406.732 2.639 1.832 3.352-.292.35-.981.782-2.142 1.175A3.942 3.942 0 001 13.26V16h14v-2.74c0-1.69-1.081-3.19-2.689-3.733zM6 4c0-1.103.897-2 2-2s2 .897 2 2v1c0 1.103-.897 2-2 2s-2-.897-2-2V4zm7 10H3v-.74c0-.831.534-1.569 1.33-1.838 1.845-.624 3-1.436 3.452-2.422h.436c.452.986 1.607 1.798 3.453 2.422A1.943 1.943 0 0113 13.26V14z"
                    />
                </svg>
            </a>
            <button
                type="button"
                class="mt-3 ml-1"
                :title="t('translator')"
                @mouseenter="setHoveredIcon('translator')"
                @mouseleave="clearHoveredIcon"
                @click="toggleTranslator"
            >
                <svg :class="iconClass" viewBox="0 0 24 24">
                    <path
                        :class="['fill-current', iconPathClass('translator')]"
                        d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0014.07 6H17V4h-7V2H8v2H1v2h11.17A15.7 15.7 0 019 11.17 15.16 15.16 0 016.91 8H4.91a17.39 17.39 0 002.77 4.36l-5.09 5.02L4 18.8l5-5 3.11 3.11.76-1.84zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z"
                    />
                </svg>
            </button>
        </div>
    </div>
    <AdminTranslationWidget
        :is-open="isTranslatorOpen"
        @close="closeTranslator"
    />
</template>

<style scoped>
#widgetPanel {
    width: 4rem;
    height: 100%;
}
svg:hover {
    cursor: pointer;
}
</style>
