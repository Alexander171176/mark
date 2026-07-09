<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import { useI18n } from 'vue-i18n'

import ThemeToggle from '@/Components/User/ThemeToggle/ThemeToggle.vue'
import DropdownLink from '@/Components/Base/DropdownLink.vue'
import Dropdown from '@/Components/Base/Dropdown.vue'
import LocaleSelectOption from '@/Components/Admin/UI/Select/LocaleSelectOption.vue'

const { t, locale } = useI18n()
const page = usePage()

const isAuth = computed(() => !!page.props?.auth?.user)
const user = computed(() => page.props?.auth?.user || null)
const managesProfilePhotos = computed(() => !!page.props?.jetstream?.managesProfilePhotos)

const cmsMenu = computed(() => page.props?.cmsMenu || [])
const marketCatalog = computed(() => page.props?.marketCatalog || [])

const isCatalogOpen = ref(false)
const showingNavigationDropdown = ref(false)
const searchQuery = ref('')
const isSolid = ref(false)

const availableLocales = computed(() => {
    const locales = page.props?.availableLocales || page.props?.locales

    return Array.isArray(locales) && locales.length
        ? locales.map(item => String(item).trim().toLowerCase()).filter(Boolean)
        : [String(locale.value || 'ru').toLowerCase()]
})

const selectedLocale = ref(locale.value)

const currentLocale = computed(() => {
    return page.props?.locale || locale.value || 'ru'
})

watch(() => locale.value, (newLocale) => {
    if (selectedLocale.value !== newLocale) {
        selectedLocale.value = newLocale
    }
})

watch(selectedLocale, (newLocale) => {
    const targetLocale = String(newLocale || '').toLowerCase()
    const current = String(locale.value || '').toLowerCase()

    if (!targetLocale || targetLocale === current) return
    if (!availableLocales.value.includes(targetLocale)) return

    locale.value = targetLocale

    const pathSegments = window.location.pathname.split('/').filter(Boolean)

    if (
        pathSegments.length > 0 &&
        availableLocales.value.includes(pathSegments[0].toLowerCase())
    ) {
        pathSegments[0] = targetLocale
    } else {
        pathSegments.unshift(targetLocale)
    }

    Inertia.visit(`/${pathSegments.join('/')}${window.location.search}`, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    })
})

const getTranslation = (item) => {
    if (!item?.translations?.length) {
        return item?.translation || null
    }

    return item.translations.find(translation => translation.locale === currentLocale.value)
        || item.translations.find(translation => translation.locale === 'ru')
        || item.translations[0]
        || null
}

const getTitle = (item) => {
    return item?.title || getTranslation(item)?.title || `ID: ${item?.id}`
}

const getPageChildren = (item) => item?.public_menu_children || []
const getCategoryChildren = (item) => item?.public_catalog_children || []

const normalizePath = (url) => {
    if (!url) return '/'

    const cleanUrl = String(url).trim()

    if (cleanUrl.startsWith('http')) {
        return cleanUrl
    }

    return cleanUrl.startsWith('/') ? cleanUrl : `/${cleanUrl}`
}

const getHref = (item) => {
    const url = normalizePath(item?.url)
    const pathSegments = window.location.pathname.split('/').filter(Boolean)

    const hasLocalePrefix = pathSegments.length > 0 &&
        availableLocales.value.includes(pathSegments[0].toLowerCase())

    if (!hasLocalePrefix) return url

    const urlSegments = url.split('/').filter(Boolean)

    if (
        urlSegments.length > 0 &&
        availableLocales.value.includes(urlSegments[0].toLowerCase())
    ) {
        return url
    }

    return `/${currentLocale.value}${url === '/' ? '' : url}`
}

const isActiveUrl = (url) => {
    const cleanUrl = normalizePath(url)
    const currentPath = window.location.pathname

    return currentPath === cleanUrl ||
        currentPath.endsWith(cleanUrl) ||
        currentPath.includes(`${cleanUrl}/`)
}

const isPageActive = (item) => {
    if (isActiveUrl(item?.url)) return true

    return getPageChildren(item).some(child => {
        if (isActiveUrl(child?.url)) return true

        return getPageChildren(child).some(subChild => isActiveUrl(subChild?.url))
    })
}

const isCategoryActive = (item) => {
    if (isActiveUrl(item?.url)) return true

    return getCategoryChildren(item).some(child => {
        if (isActiveUrl(child?.url)) return true

        return getCategoryChildren(child).some(subChild => isActiveUrl(subChild?.url))
    })
}

const toggleCatalog = () => {
    isCatalogOpen.value = !isCatalogOpen.value
    showingNavigationDropdown.value = false
}

const closeCatalog = () => {
    isCatalogOpen.value = false
}

const toggleMobileMenu = () => {
    isCatalogOpen.value = false
    showingNavigationDropdown.value = !showingNavigationDropdown.value
}

const closeMobileMenu = () => {
    showingNavigationDropdown.value = false
}

const closeAllMenus = () => {
    closeCatalog()
    closeMobileMenu()
}

watch(showingNavigationDropdown, (isOpen) => {
    document.body.classList.toggle('overflow-hidden', isOpen)
})

const handleScroll = () => {
    isSolid.value = (window.scrollY || window.pageYOffset || 0) > 40
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true })
    handleScroll()
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
    document.body.classList.remove('overflow-hidden')
})

watch(() => page.url, () => closeAllMenus())

const submitSearch = () => {
    const query = searchQuery.value.trim()

    if (!query) return

    Inertia.visit(`${getHref({ url: '/search' })}?q=${encodeURIComponent(query)}`, {
        preserveState: false,
        preserveScroll: false,
    })
}

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <nav
        class="fixed top-0 left-0 right-0 z-[100] transition-all duration-300 border-b lg:px-8"
        :class="[
            isSolid || isCatalogOpen || showingNavigationDropdown
                ? 'bg-white/95 dark:bg-gray-900/95 backdrop-blur ' +
                 'border-gray-200 dark:border-gray-800 shadow-md'
                : 'bg-white/80 dark:bg-slate-800/80 backdrop-blur border-transparent'
        ]"
    >
        <div class="px-3">
            <!-- TOP BAR -->
            <div
                class="grid h-16 items-center gap-2
                       grid-cols-[1fr_auto]
                       sm:grid-cols-[180px_1fr_auto]
                       lg:grid-cols-[200px_1fr_200px]
                       xl:grid-cols-[220px_1fr_220px]
                       2xl:grid-cols-[240px_1fr_240px]
                       lg:gap-6"
            >
                <div class="flex items-center lg:justify-between gap-2 min-w-0">
                    <button
                        type="button"
                        @click="toggleMobileMenu"
                        class="inline-flex items-center justify-center lg:hidden
                               rounded-md p-2 text-gray-600
                               hover:bg-gray-100 hover:text-gray-900
                               dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white
                               focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                    >
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                v-if="!showingNavigationDropdown"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                    <Link
                        :href="route('home')"
                        class="flex items-center justify-center gap-2 shrink-0 logo"
                        @click="closeAllMenus"
                    >
                        <span class="inline-flex font-bold text-xl sm:text-2xl text-blue-600 truncate">
                            AGROVENT
                        </span>
                    </Link>

                    <button
                        type="button"
                        @click="toggleCatalog"
                        class="hidden lg:inline-flex items-center gap-2 rounded-lg
                               bg-blue-600 px-3 py-3 text-sm font-bold text-white
                               hover:bg-blue-700 active:bg-blue-800 transition"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                v-if="!isCatalogOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <form
                    class="hidden lg:flex justify-center w-full min-w-0"
                    @submit.prevent="submitSearch"
                >
                    <div class="flex w-full max-w-[1100px] bg-white dark:bg-gray-900 overflow-hidden">
                        <input
                            v-model="searchQuery"
                            type="search"
                            class="w-full rounded-xl border-2 border-blue-600 bg-white px-4 py-2
                                   text-sm text-slate-800 dark:text-slate-100 -mr-3 z-10
                                   placeholder:text-slate-400 focus:ring-0"
                            :placeholder="t('search')"
                        />

                        <button
                            type="submit"
                            class="px-8 bg-blue-600 text-white font-bold text-sm
                                   hover:bg-blue-700 transition rounded-r-xl"
                        >
                            <svg class="h-5 w-5 fill-current text-white" viewBox="0 0 512 512">
                                <path
                                    d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="flex justify-end items-center gap-1 sm:gap-2 min-w-0">
                    <LocaleSelectOption
                        v-model="selectedLocale"
                        :locales="availableLocales"
                        placement="bottom-end"
                    />

                    <ThemeToggle class="relative z-10" />

                    <Dropdown
                        v-if="isAuth"
                        align="right"
                        width="60"
                        class="relative z-10"
                    >
                        <template #trigger>
                            <button
                                v-if="managesProfilePhotos"
                                class="flex items-center px-2 py-1 border-2 border-transparent rounded-full
                                       hover:bg-slate-100 dark:hover:bg-gray-800
                                       focus:outline-none focus:border-gray-400 transition"
                            >
                                <img
                                    class="h-7 w-7 rounded-full object-cover"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />

                                <div class="hidden xl:flex flex-col ml-2 text-left">
                                    <span class="font-semibold text-xs text-slate-700 dark:text-slate-100">
                                        {{ user.name }}
                                    </span>
                                    <span class="font-semibold text-[10px] text-slate-400 dark:text-slate-300">
                                        {{ user.email }}
                                    </span>
                                </div>
                            </button>

                            <span v-else class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center bg-white dark:bg-gray-900
                                           px-3 py-1 border border-gray-200 dark:border-gray-700
                                           rounded-md text-sm font-medium
                                           text-slate-600 hover:text-blue-700
                                           dark:text-slate-200 dark:hover:text-blue-300
                                           focus:outline-none transition"
                                >
                                    {{ user.name }}
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

                    <Dropdown
                        v-else
                        align="right"
                        width="60"
                        class="relative z-10"
                    >
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                                       text-sm font-semibold bg-white dark:bg-gray-900
                                       border border-gray-200 dark:border-gray-700
                                       text-slate-700 dark:text-slate-300
                                       hover:text-blue-700 dark:hover:text-blue-300 transition"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 448 512">
                                    <path
                                        d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z" />
                                </svg>

                                <span class="hidden sm:inline">{{ t('account') }}</span>
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

            <!-- CMS PAGES ROW -->
            <div
                v-if="cmsMenu.length"
                class="hidden lg:flex h-10 items-center justify-center gap-6"
            >
                <div
                    v-for="rootPage in cmsMenu"
                    :key="rootPage.id"
                    class="relative group h-full flex items-center"
                >
                    <Link
                        :href="getHref(rootPage)"
                        class="flex items-center gap-2 text-sm font-semibold transition
                               text-slate-700 dark:text-slate-200
                               hover:text-blue-700 dark:hover:text-blue-300"
                        :class="{ 'text-blue-700 dark:text-blue-300': isPageActive(rootPage) }"
                    >
                        <span
                            v-if="rootPage.icon"
                            class="w-4 h-4 flex items-center justify-center"
                            v-html="rootPage.icon"
                        />

                        <span>{{ getTitle(rootPage) }}</span>
                    </Link>

                    <div
                        v-if="getPageChildren(rootPage).length"
                        class="invisible opacity-0 translate-y-2
                               group-hover:visible group-hover:opacity-100 group-hover:translate-y-0
                               absolute left-0 top-full z-[300]
                               min-w-64 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-white dark:bg-gray-950 shadow-xl
                               transition-all duration-200"
                    >
                        <div class="p-3 space-y-1">
                            <div
                                v-for="child in getPageChildren(rootPage)"
                                :key="child.id"
                                class="relative group/child"
                            >
                                <Link
                                    :href="getHref(child)"
                                    class="flex items-center justify-between gap-3
                                           rounded-lg px-3 py-2
                                           text-sm font-semibold
                                           text-slate-700 dark:text-slate-200
                                           hover:bg-blue-50 dark:hover:bg-gray-900
                                           hover:text-blue-700 dark:hover:text-blue-300"
                                >
                                    <div class="flex items-center justify-center gap-2">
                                        <span
                                            v-if="child.icon"
                                            class="w-4 h-4 flex items-center justify-center"
                                            v-html="child.icon"
                                        />
                                        <span>
                                            {{ getTitle(child) }}
                                        </span>
                                    </div>

                                    <span v-if="getPageChildren(child).length">›</span>
                                </Link>

                                <div
                                    v-if="getPageChildren(child).length"
                                    class="invisible opacity-0 translate-x-2
                                           group-hover/child:visible
                                           group-hover/child:opacity-100
                                           group-hover/child:translate-x-0
                                           absolute left-full top-0 z-[310]
                                           min-w-60 rounded-xl border
                                           border-gray-200 dark:border-gray-700
                                           bg-white dark:bg-gray-950 shadow-xl
                                           transition-all duration-200"
                                >
                                    <div class="p-3 space-y-1">
                                        <Link
                                            v-for="subChild in getPageChildren(child)"
                                            :key="subChild.id"
                                            :href="getHref(subChild)"
                                            class="flex items-center justify-start gap-2
                                                   rounded-lg px-3 py-2
                                                   text-sm font-semibold
                                                   text-slate-600 dark:text-slate-300
                                                   hover:bg-blue-50 dark:hover:bg-gray-900
                                                   hover:text-blue-700 dark:hover:text-blue-300"
                                        >
                                            <span
                                                v-if="subChild.icon"
                                                class="w-3.5 h-3.5 flex items-center justify-center"
                                                v-html="subChild.icon"
                                            />
                                            <span>
                                                {{ getTitle(subChild) }}
                                            </span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESKTOP CATALOG: MARKET CATEGORIES -->
        <div
            v-if="isCatalogOpen"
            class="hidden lg:block absolute left-0 right-0 top-full z-50
                   border-t border-gray-200 dark:border-gray-800
                   bg-white dark:bg-gray-950 shadow-2xl"
        >
            <div class="px-4 py-5">
                <div class="grid grid-cols-4 gap-5">
                    <div
                        v-for="rootCategory in marketCatalog"
                        :key="rootCategory.id"
                        class="rounded-xl border border-gray-200 dark:border-gray-800
                               bg-gray-50 dark:bg-gray-900 p-4"
                    >
                        <Link
                            :href="getHref(rootCategory)"
                            class="flex items-center gap-2 font-bold
                                   text-slate-900 dark:text-white
                                   hover:text-blue-700 dark:hover:text-blue-300 transition"
                            :class="{ 'text-blue-700 dark:text-blue-300': isCategoryActive(rootCategory) }"
                            @click="closeCatalog"
                        >
                            <span
                                v-if="rootCategory.icon"
                                class="w-5 h-5 flex items-center justify-center
                                       text-slate-500 dark:text-slate-300"
                                v-html="rootCategory.icon"
                            />

                            <span>{{ getTitle(rootCategory) }}</span>
                        </Link>

                        <ul
                            v-if="getCategoryChildren(rootCategory).length"
                            class="mt-3 space-y-2"
                        >
                            <li
                                v-for="child in getCategoryChildren(rootCategory)"
                                :key="child.id"
                            >
                                <Link
                                    :href="getHref(child)"
                                    class="flex items-center gap-2 text-sm font-semibold
                                           text-slate-700 dark:text-slate-300
                                           hover:text-blue-700 dark:hover:text-blue-300 transition"
                            :class="{ 'text-blue-700 dark:text-blue-300': isCategoryActive(child) }"
                                    @click="closeCatalog"
                                >
                                    <span
                                        v-if="child.icon"
                                        class="w-4 h-4 flex items-center
                                               justify-center text-slate-400"
                                        v-html="child.icon"
                                    />

                                    <span>{{ getTitle(child) }}</span>
                                </Link>

                                <ul
                                    v-if="getCategoryChildren(child).length"
                                    class="mt-1 ml-6 space-y-1"
                                >
                                    <li
                                        v-for="subChild in getCategoryChildren(child)"
                                        :key="subChild.id"
                                    >
                                        <Link
                                            :href="getHref(subChild)"
                                            class="flex items-center gap-2 text-xs
                                                   text-slate-500 dark:text-slate-400
                                                   hover:text-blue-700 dark:hover:text-blue-300 transition"
                                            :class="{ 'text-blue-700 dark:text-blue-300': isCategoryActive(subChild) }"
                                            @click="closeCatalog"
                                        >
                                            <span
                                                v-if="subChild.icon"
                                                class="w-3.5 h-3.5 opacity-70"
                                                v-html="subChild.icon"
                                            />

                                            <span>{{ getTitle(subChild) }}</span>
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- MOBILE MENU -->
    <Teleport to="body">
        <div
            v-if="showingNavigationDropdown"
            class="fixed inset-0 z-[9999] lg:hidden"
        >
            <div
                class="absolute inset-0 bg-black/40"
                @click="closeMobileMenu"
            />

            <div
                class="absolute left-3 right-3 top-20 bottom-3
                       rounded-xl border border-gray-200 dark:border-gray-700
                       bg-white dark:bg-gray-950 shadow-xl overflow-hidden"
            >
                <div
                    class="h-full overflow-y-auto overflow-x-hidden
                           overscroll-contain px-3 py-3"
                >
                    <form class="mb-3" @submit.prevent="submitSearch">
                        <div class="flex rounded-lg border-2 border-blue-600 overflow-hidden">
                            <input
                                v-model="searchQuery"
                                type="search"
                                class="w-full border-0 px-3 py-2 text-sm
                                       bg-white dark:bg-gray-900
                                       text-slate-800 dark:text-slate-100
                                       focus:ring-0"
                                :placeholder="t('search')"
                            />

                            <button
                                type="submit"
                                class="px-4 bg-blue-600 text-white text-sm font-bold"
                            >
                                {{ t('find') }}
                            </button>
                        </div>
                    </form>

                    <div class="space-y-2 pb-4">
                        <div
                            v-for="rootPage in cmsMenu"
                            :key="rootPage.id"
                            class="rounded-lg border border-gray-200 dark:border-gray-800
                                   bg-gray-50 dark:bg-gray-900 overflow-hidden"
                        >
                            <Link
                                :href="getHref(rootPage)"
                                @click="closeMobileMenu"
                                class="flex items-center gap-3 px-3 py-2 text-sm font-bold transition"
                                :class="
                                    isPageActive(rootPage)
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                                        : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800'
                                "
                            >
                                <span
                                    v-if="rootPage.icon"
                                    class="h-4 w-4 flex items-center justify-center"
                                    v-html="rootPage.icon"
                                />

                                <span>{{ getTitle(rootPage) }}</span>
                            </Link>

                            <div
                                v-if="getPageChildren(rootPage).length"
                                class="px-3 pb-2 space-y-1"
                            >
                                <div
                                    v-for="child in getPageChildren(rootPage)"
                                    :key="child.id"
                                >
                                    <Link
                                        :href="getHref(child)"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold transition"
                                        :class="
                                            isPageActive(child)
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                                                : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                        "
                                    >
                                        <span
                                            v-if="child.icon"
                                            class="h-4 w-4 flex items-center justify-center"
                                            v-html="child.icon"
                                        />

                                        <span>{{ getTitle(child) }}</span>
                                    </Link>

                                    <div
                                        v-if="getPageChildren(child).length"
                                        class="ml-5 mt-1 space-y-1"
                                    >
                                        <Link
                                            v-for="subChild in getPageChildren(child)"
                                            :key="subChild.id"
                                            :href="getHref(subChild)"
                                            @click="closeMobileMenu"
                                            class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-semibold transition"
                                            :class="
                                                isPageActive(subChild)
                                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                                                    : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800'
                                            "
                                        >
                                            <span
                                                v-if="subChild.icon"
                                                class="h-3.5 w-3.5 flex items-center justify-center"
                                                v-html="subChild.icon"
                                            />

                                            <span>{{ getTitle(subChild) }}</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                            <template v-if="isAuth">
                                <Link
                                    :href="route('profile.show')"
                                    @click="closeMobileMenu"
                                    class="block rounded-md px-3 py-2 text-sm font-semibold
                                           text-slate-700 hover:bg-slate-100
                                           dark:text-slate-200 dark:hover:bg-slate-800"
                                >
                                    {{ t('profile') }}
                                </Link>

                                <button
                                    type="button"
                                    @click="logout"
                                    class="block w-full text-left rounded-md px-3 py-2 text-sm font-semibold
                                           text-slate-700 hover:bg-slate-100
                                           dark:text-slate-200 dark:hover:bg-slate-800"
                                >
                                    {{ t('logout') }}
                                </button>
                            </template>

                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    @click="closeMobileMenu"
                                    class="block rounded-md px-3 py-2 text-sm font-semibold
                                           text-slate-700 hover:bg-slate-100
                                           dark:text-slate-200 dark:hover:bg-slate-800"
                                >
                                    {{ t('login') }}
                                </Link>

                                <Link
                                    :href="route('register')"
                                    @click="closeMobileMenu"
                                    class="block rounded-md px-3 py-2 text-sm font-semibold
                                           text-slate-700 hover:bg-slate-100
                                           dark:text-slate-200 dark:hover:bg-slate-800"
                                >
                                    {{ t('register') }}
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
