<script setup>
import {
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
    computed
} from 'vue'

import { usePage, Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { Inertia } from '@inertiajs/inertia'

import ThemeToggle from '@/Components/User/ThemeToggle/ThemeToggle.vue'
import DropdownLink from '@/Components/Base/DropdownLink.vue'
import Dropdown from '@/Components/Base/Dropdown.vue'
import LocaleSelectOption from '@/Components/Admin/UI/Select/LocaleSelectOption.vue'
import NavDropdown from '@/Components/Public/Default/Link/NavDropdown.vue'

import { publicDropdownMenus } from '@/utils/publicDropdownMenus'

const { t, locale } = useI18n()
const navMenus = computed(() => publicDropdownMenus(t))

const page = usePage()

const isAuth = computed(() => !!page.props?.auth?.user)
const user = computed(() => page.props?.auth?.user || null)
const managesProfilePhotos = computed(() => !!page.props?.jetstream?.managesProfilePhotos)

/** доступные локали */
const availableLocales = computed(() => {
    const locales = page.props?.availableLocales

    if (Array.isArray(locales) && locales.length > 0) {
        return locales
            .map(locale => String(locale).trim().toLowerCase())
            .filter(Boolean)
    }

    if (Array.isArray(page.props?.locales) && page.props.locales.length > 0) {
        return page.props.locales
            .map(locale => String(locale).trim().toLowerCase())
            .filter(Boolean)
    }

    return [String(locale.value || 'ru').toLowerCase()]
})

/** текущая локаль */
const selectedLocale = ref(locale.value)

watch(
    () => locale.value,
    (newLocale) => {
        if (selectedLocale.value !== newLocale) {
            selectedLocale.value = newLocale
        }
    }
)

/** переключение локали */
watch(selectedLocale, (newLocale) => {
    const targetLocale = String(newLocale || '').toLowerCase()
    const currentLocale = String(locale.value || '').toLowerCase()

    if (!targetLocale || targetLocale === currentLocale) {
        return
    }

    if (!availableLocales.value.includes(targetLocale)) {
        return
    }

    locale.value = targetLocale

    const pathSegments = window.location.pathname
        .split('/')
        .filter(Boolean)

    if (
        pathSegments.length > 0 &&
        availableLocales.value.includes(pathSegments[0].toLowerCase())
    ) {
        pathSegments[0] = targetLocale
    } else {
        pathSegments.unshift(targetLocale)
    }

    const newPath = `/${pathSegments.join('/')}${window.location.search}`

    Inertia.visit(newPath, {
        preserveState: false,
        preserveScroll: true,
        replace: true
    })
})

/** мобильное меню */
const showingNavigationDropdown = ref(false)

const toggleNavigationDropdown = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value
}

const closeMobileMenu = () => {
    showingNavigationDropdown.value = false
}

watch(showingNavigationDropdown, (isOpen) => {
    document.body.classList.toggle('overflow-hidden', isOpen)
})

onBeforeUnmount(() => {
    document.body.classList.remove('overflow-hidden')
})

/** шапка становится плотной при скролле */
const isSolid = ref(false)
const SCROLL_THRESHOLD = 60

const handleScroll = () => {
    const y = window.scrollY || window.pageYOffset || 0
    isSolid.value = y > SCROLL_THRESHOLD
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true })
    handleScroll()
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
    document.body.classList.remove('overflow-hidden')
})

/** закрывать мобильное меню при смене маршрута */
watch(
    () => page.url,
    () => {
        showingNavigationDropdown.value = false
    }
)

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <nav
        class="fixed top-0 left-0 right-0 z-30 transition-all duration-300 border-b"
        :class="[
            isSolid
                ? 'bg-gray-100/90 dark:bg-gray-900/90 backdrop-blur ' +
                 'border-gray-200 dark:border-gray-700 shadow-md'
                : 'bg-transparent border-transparent'
        ]"
        role="navigation"
        aria-label="main navigation"
    >
        <div class="px-3">
            <div class="flex h-14 items-center justify-between gap-1">

                <!-- LEFT SIDE -->
                <div class="flex items-center gap-1">
                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        @click="toggleNavigationDropdown"
                        class="inline-flex items-center justify-center md:hidden
                               rounded-sm p-1 mr-1 text-gray-600
                               hover:bg-gray-100 hover:text-gray-900
                               dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white
                               focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                        :aria-expanded="showingNavigationDropdown"
                        :aria-label="t('menu')"
                    >
                        <svg
                            class="h-5 w-5"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{ hidden: showingNavigationDropdown,
                                'inline-flex': !showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ hidden: !showingNavigationDropdown,
                                'inline-flex': showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                    <!-- LOGO -->
                    <Link
                        :href="route('home')"
                        class="flex items-center justify-center gap-3 logo"
                        @click="closeMobileMenu"
                    >
                        <svg
                            class="h-10 w-10"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                            viewBox="0 0 640 512"
                        >
                            <path
                                d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"
                            />
                        </svg>

                        <span class="font-semibold text-xs md:text-lg">
                            SW Community
                        </span>
                    </Link>
                </div>

                <!-- CENTER DESKTOP -->
                <div class="hidden md:flex items-center gap-4">
                    <NavDropdown
                        v-for="menu in navMenus"
                        :key="menu.key"
                        :title="menu.title"
                        :links="menu.links"
                    />
                </div>

                <!-- RIGHT SIDE -->
                <div class="flex items-center gap-1">
                    <!-- Переключатель локали -->
                    <LocaleSelectOption
                        v-model="selectedLocale"
                        :locales="availableLocales"
                        placement="bottom-end"
                    />

                    <!-- Переключатель темы -->
                    <ThemeToggle class="relative z-10" />

                    <div class="relative">
                        <!-- AUTH USER -->
                        <Dropdown
                            v-if="isAuth"
                            align="right"
                            width="60"
                            class="relative z-10"
                        >
                            <template #trigger>
                                <button
                                    v-if="managesProfilePhotos"
                                    class="flex items-center px-2 py-0.5
                                           border-2 border-transparent rounded-full
                                           focus:outline-none focus:border-gray-400 transition"
                                >
                                    <img
                                        class="h-6 w-6 mr-2 rounded-full object-cover"
                                        :src="user.profile_photo_url"
                                        :alt="user.name"
                                    />

                                    <div class="hidden md:flex flex-col">
                                        <span
                                            class="font-semibold text-xs
                                                   text-slate-700 dark:text-slate-100"
                                        >
                                            {{ user.name }}
                                        </span>
                                        <span
                                            class="font-semibold text-[10px]
                                                   text-slate-400 dark:text-slate-300"
                                        >
                                            {{ user.email }}
                                        </span>
                                    </div>
                                </button>

                                <span v-else class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center bg-white dark:bg-gray-900
                                               active:bg-gray-50 px-3 py-1 border border-transparent
                                               rounded-md text-sm leading-4 font-medium
                                               text-slate-500 hover:text-slate-700
                                               dark:text-slate-200 dark:hover:text-white
                                               focus:outline-none focus:bg-gray-50
                                               dark:focus:bg-gray-800 transition"
                                    >
                                        {{ user.name }}
                                        <svg
                                            class="ms-2 -me-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <div class="block px-4 py-2 text-sm text-slate-400">
                                    {{ t('accountManagement') }}
                                </div>

                                <DropdownLink :href="route('profile.show')">
                                    {{ t('profile') }}
                                </DropdownLink>

                                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        {{ t('logout') }}
                                    </DropdownLink>
                                </form>
                            </template>
                        </Dropdown>

                        <!-- GUEST USER -->
                        <Dropdown
                            v-else
                            align="right"
                            width="60"
                            class="relative z-10"
                        >
                            <template #trigger>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-1
                                           text-sm font-semibold bg-white/80 dark:bg-gray-900/80
                                           border border-gray-200 dark:border-gray-700
                                           text-slate-700/85 dark:text-slate-300/85
                                           hover:text-blue-700 dark:hover:text-blue-300
                                           hover:bg-white dark:hover:bg-gray-900
                                           transition"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 448 512"
                                    >
                                        <path
                                            d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"
                                        />
                                    </svg>

                                    <span>{{ t('account') }}</span>

                                    <svg
                                        class="h-4 w-4 opacity-70"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                        />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="block px-4 py-2 text-md text-slate-400">
                                    {{ t('guest') }}
                                </div>

                                <DropdownLink :href="route('login')">
                                    {{ t('login') }}
                                </DropdownLink>

                                <DropdownLink :href="route('register')">
                                    {{ t('register') }}
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </div>

        <!-- MOBILE MENU -->
        <div
            v-if="showingNavigationDropdown"
            class="fixed left-3 right-3 top-14 bottom-3 z-40 md:hidden
                   rounded-md border border-gray-200 dark:border-gray-700
                   bg-gray-100/95 dark:bg-gray-900/95 backdrop-blur
                   shadow-xl overflow-hidden"
        >
            <div
                class="h-full overflow-y-auto overflow-x-hidden overscroll-contain
                       px-3 py-3"
            >
                <div class="space-y-2 pb-4">
                    <!-- mobile nav links -->
                    <Link
                        :href="route('public.blogRubrics.index')"
                        @click="closeMobileMenu"
                        class="block rounded-sm px-3 py-2 text-sm font-semibold transition"
                        :class="
                        route().current('public.blogRubrics.*') ||
                        route().current('public.tags.*')
                            ? 'bg-blue-200 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                            : 'text-slate-700 hover:bg-slate-200 dark:text-slate-200 dark:hover:bg-slate-800'
                    "
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                 fill="currentColor">
                                <path
                                    d="M489.2 287.9h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6V146.2c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-6-8-4.6-11.7-4.6v-38c8.3-2 17.1-3.4 25.7-3.4 10.9 0 20.9 4.3 31.4 4.3 4.6 0 27.7-1.1 27.7-8v-60c0-2.6-2-4.6-4.6-4.6-5.1 0-15.1 4.3-24 4.3-9.7 0-20.9-4.3-32.6-4.3-8 0-16 1.1-23.7 2.9v-4.9c5.4-2.6 9.1-8.3 9.1-14.3 0-20.7-31.4-20.8-31.4 0 0 6 3.7 11.7 9.1 14.3v111.7c-3.7 0-11.7-1.4-11.7 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32H128v-32c0-2.6-2-4.6-4.6-4.6H96c-2.6 0-4.6 2-4.6 4.6v178.3H54.8v-32c0-2.6-2-4.6-4.6-4.6H22.8c-2.6 0-4.6 2-4.6 4.6V512h182.9v-96c0-72.6 109.7-72.6 109.7 0v96h182.9V292.5c.1-2.6-1.9-4.6-4.5-4.6zm-288.1-4.5c0 2.6-2 4.6-4.6 4.6h-27.4c-2.6 0-4.6-2-4.6-4.6v-64c0-2.6 2-4.6 4.6-4.6h27.4c2.6 0 4.6 2 4.6 4.6v64zm146.4 0c0 2.6-2 4.6-4.6 4.6h-27.4c-2.6 0-4.6-2-4.6-4.6v-64c0-2.6 2-4.6 4.6-4.6h27.4c2.6 0 4.6 2 4.6 4.6v64z"></path>
                            </svg>
                            {{ t('rubrics') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.blogArticles.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.blogArticles.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"
                                 fill="currentColor">
                                <path
                                    d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zM256 51.9V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z"></path>
                            </svg>
                            {{ t('articles') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.blogVideos.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.blogVideos.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 576 512">
                                <path
                                    d="M336.2 64H47.8C21.4 64 0 85.4 0 111.8v288.4C0 426.6 21.4 448 47.8 448h288.4c26.4 0 47.8-21.4 47.8-47.8V111.8c0-26.4-21.4-47.8-47.8-47.8zm189.4 37.7L416 177.3v157.4l109.6 75.5c21.2 14.6 50.4-.3 50.4-25.8V127.5c0-25.4-29.1-40.4-50.4-25.8z"></path>
                            </svg>
                            {{ t('videos') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.schoolInstructors.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.schoolInstructors.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 640 512">
                                <path
                                    d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                            </svg>
                            {{ t('instructors') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.schoolTracks.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.schoolTracks.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"></path>
                                <path
                                    d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"></path>
                                <path
                                    d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"></path>
                                <path
                                    d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"></path>
                            </svg>
                            {{ t('tracks') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.schoolCourses.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.schoolCourses.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512">
                                <path
                                    d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path>
                            </svg>
                            {{ t('courses') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.schoolModules.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.schoolModules.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                                <path class="fill-current text-slate-400"
                                      d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                                <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                                <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                            </svg>
                            {{ t('modules') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.schoolLessons.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.schoolLessons.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            {{ t('lessons') }}
                        </div>
                    </Link>
                    <Link
                        :href="route('public.schoolAssignments.index')"
                        @click="closeMobileMenu"
                        class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                        :class="route().current('public.schoolAssignments.*')
                        ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                    >
                        <div class="flex items-center justify-start gap-3">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"></path>
                                <path
                                    d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"></path>
                            </svg>
                            {{ t('assignments') }}
                        </div>
                    </Link>

                    <!-- mobile account -->
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                        <template v-if="isAuth">
                            <div class="flex items-center gap-3 px-3 py-2">
                                <img
                                    v-if="user?.profile_photo_url"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                    class="h-9 w-9 rounded-full object-cover"
                                />
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold
                                            text-slate-700 dark:text-slate-100">
                                        {{ user?.name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ user?.email }}
                                    </div>
                                </div>
                            </div>

                            <Link
                                :href="route('profile.show')"
                                @click="closeMobileMenu"
                                class="block rounded-md px-3 py-2 text-sm font-semibold transition
                                   text-slate-700 hover:bg-slate-200
                                   dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                {{ t('profile') }}
                            </Link>

                            <button
                                type="button"
                                @click="logout"
                                class="block w-full text-left rounded-md px-3 py-2
                                   text-sm font-semibold transition
                                   text-slate-700 hover:bg-slate-200
                                   dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                {{ t('logout') }}
                            </button>
                        </template>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                @click="closeMobileMenu"
                                class="block rounded-md px-3 py-2 text-sm font-semibold transition
                                   text-slate-700 hover:bg-slate-200
                                   dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                {{ t('login') }}
                            </Link>

                            <Link
                                :href="route('register')"
                                @click="closeMobileMenu"
                                class="block rounded-md px-3 py-2 text-sm font-semibold transition
                                   text-slate-700 hover:bg-slate-200
                                   dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                {{ t('register') }}
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
