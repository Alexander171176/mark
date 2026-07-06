<script setup>
import { computed, ref, watch } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: 'ru' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
})

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

const privacy = computed(() => page.props.laravelLang?.public?.privacy?.page ?? {})

const sections = computed(() => privacy.value.sections ?? [])

/** Показ левой колонки */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
})

/** Показ правой колонки */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
})

/** Ключ localStorage для левого сайдбара */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'

/** Ключ localStorage для правого сайдбара */
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/** Получение boolean из localStorage */
const getStoredBoolean = (key, defaultValue = false) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/** Состояние левого сайдбара */
const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, false)
)

/** Состояние правого сайдбара */
const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, false)
)

/** Сохраняем состояние левого сайдбара */
watch(leftCollapsed, (value) => {
    localStorage.setItem(LEFT_SIDEBAR_KEY, String(value))
})

/** Сохраняем состояние правого сайдбара */
watch(rightCollapsed, (value) => {
    localStorage.setItem(RIGHT_SIDEBAR_KEY, String(value))
})
</script>

<template>
    <Head :title="privacy.meta_title ?? privacy.title" />

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />
        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 sm:mt-16 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                </aside>

                <!-- CENTER -->
                <div class="w-full lg:mt-16 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md
                                    dark:border-gray-700 dark:bg-gray-900 sm:p-8">
                            <div class="mb-8">
                                <h1 class="text-2xl font-bold text-center
                                           text-gray-900 dark:text-white sm:text-3xl">
                                    {{ privacy.title }}
                                </h1>

                                <p class="mt-2 font-semibold text-xs text-center
                                          text-gray-500 dark:text-gray-400">
                                    {{ privacy.version_label }}: {{ privacy.version }}
                                </p>

                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ privacy.description }}
                                </p>
                            </div>

                            <div class="space-y-6">
                                <section
                                    v-for="section in sections"
                                    :key="section.title"
                                    class="rounded-md border border-gray-200
                                           p-5 dark:border-gray-700"
                                >
                                    <h2 class="mb-3 text-lg font-semibold
                                               text-gray-900 dark:text-white">
                                        {{ section.title }}
                                    </h2>

                                    <p
                                        v-if="section.description"
                                        class="mb-4 text-sm leading-6
                                               text-gray-700 dark:text-gray-300"
                                    >
                                        {{ section.description }}
                                    </p>

                                    <ul
                                        v-if="section.items?.length"
                                        class="list-disc space-y-2 pl-5
                                               text-sm leading-6 text-gray-700 dark:text-gray-300"
                                    >
                                        <li
                                            v-for="item in section.items"
                                            :key="item"
                                        >
                                            {{ item }}
                                        </li>
                                    </ul>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                </aside>

            </main>
        </div>
        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
