<script setup>
import {
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
    computed,
} from 'vue'

import { usePage, Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { Inertia } from '@inertiajs/inertia'

import ThemeToggle from '@/Components/User/ThemeToggle/ThemeToggle.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import Dropdown from '@/Components/Dropdown.vue'
import LocaleSelectOption from '@/Components/Admin/Select/LocaleSelectOption.vue'
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
    return Array.isArray(page.props?.availableLocales)
        ? page.props.availableLocales.map(item => String(item).toLowerCase())
        : ['ru', 'en', 'kk']
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
        replace: true,
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
                ? 'bg-gray-100/90 dark:bg-gray-900/90 backdrop-blur border-gray-200 dark:border-gray-700 shadow-md'
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
                                :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
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
                                           hover:text-cyan-700 dark:hover:text-cyan-300
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
            :class="{
                'block md:hidden': showingNavigationDropdown,
                'hidden': !showingNavigationDropdown
            }"
            class="border-t border-gray-200 dark:border-gray-700
                   bg-gray-100/95 dark:bg-gray-900/95 backdrop-blur"
        >
            <div class="px-3 py-3 space-y-2">
                <!-- mobile nav links -->
                <Link
                    :href="route('public.rubrics.index')"
                    @click="closeMobileMenu"
                    class="block rounded-sm px-3 py-2 text-sm font-semibold transition"
                    :class="
                        route().current('public.rubrics.*') ||
                        route().current('public.tags.*')
                            ? 'bg-cyan-400 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300'
                            : 'text-slate-700 hover:bg-slate-200 dark:text-slate-200 dark:hover:bg-slate-800'
                    "
                >
                    {{ t('rubrics') }}
                </Link>
                <Link
                    :href="route('public.articles.index')"
                    @click="closeMobileMenu"
                    class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                    :class="route().current('public.articles.*')
                        ? 'bg-cyan-400 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                >
                    {{ t('articles') }}
                </Link>
                <Link
                    :href="route('public.videos.index')"
                    @click="closeMobileMenu"
                    class="block rounded-md px-3 py-2 text-sm font-semibold transition"
                    :class="route().current('public.videos.*')
                        ? 'bg-cyan-400 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300'
                        : 'text-slate-700 hover:bg-slate-200 ' +
                         'dark:text-slate-200 dark:hover:bg-slate-800'"
                >
                    {{ t('videos') }}
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
    </nav>
</template>
