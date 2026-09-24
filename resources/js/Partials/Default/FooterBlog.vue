<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()
const inertiaPage = usePage()

const cmsFooter = computed(() => inertiaPage.props.cmsFooter || [])

const currentLocale = computed(() => {
    return inertiaPage.props.locale || locale.value || 'ru'
})

const fallbackLocale = 'ru'

const getTranslation = (item) => {
    if (!item?.translations?.length) {
        return item?.translation || null
    }

    return item.translations.find(translation => translation.locale === currentLocale.value)
        || item.translations.find(translation => translation.locale === fallbackLocale)
        || item.translations[0]
        || null
}

const getTitle = (item) => {
    return item?.title
        || getTranslation(item)?.title
        || `ID: ${item?.id}`
}

const getFooterChildren = (item) => {
    return item?.public_footer_children || []
}
</script>

<template>
    <footer class="mx-1 lg:mx-6 my-3 lg:my-5 relative bg-gray-100 dark:bg-gray-900
                   border-2 border-slate-300 dark:border-slate-500 rounded-t-3xl">

        <!-- MAIN CONTENT -->
        <div class="py-6 lg:py-14 px-3 lg:px-12">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                <!-- LOGO + TEXT -->
                <div class="space-y-6">

                    <div class="flex items-center gap-3">
                        <div>
                            <!-- LOGO -->
                            <Link :href="route('home')"
                                  class="flex items-center justify-center gap-3 logo">
                                <svg
                                    class="w-6 h-6 fill-current text-teal-500"
                                    viewBox="0 0 576 512">
                                    <path
                                        d="M546.2 9.7c-5.6-12.5-21.6-13-28.3-1.2C486.9 62.4 431.4 96 368 96h-80C182 96 96 182 96 288c0 7 .8 13.7 1.5 20.5C161.3 262.8 253.4 224 384 224c8.8 0 16 7.2 16 16s-7.2 16-16 16C132.6 256 26 410.1 2.4 468c-6.6 16.3 1.2 34.9 17.5 41.6 16.4 6.8 35-1.1 41.8-17.3 1.5-3.6 20.9-47.9 71.9-90.6 32.4 43.9 94 85.8 174.9 77.2C465.5 467.5 576 326.7 576 154.3c0-50.2-10.8-102.2-29.8-144.6z">
                                    </path>
                                </svg>
                                <span class="inline-flex font-bold text-xl
                                             sm:text-2xl text-sky-600 truncate">
                                    AGROVENT
                                </span>
                            </Link>
                            <h3 class="text-center subtitle flex flex-col gap-1">
                                <span class="font-semibold text-gradient">
                                    Инженерные системы
                                </span>
                                <span class="font-bold text-orange-500 dark:text-yellow-300">
                                    +7 (701) 111-11-11
                                </span>
                            </h3>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Выбирайте лучшие, инновационные решения для бизнеса.
                    </p>

                    <!-- SOCIAL -->
                    <div class="flex items-center gap-4">

                        <a href="#"
                           title="Facebook"
                           class="text-gray-500 hover:text-sky-600
                                  dark:hover:text-sky-400 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="0.54em" height="1em"
                                 viewBox="0 0 896 1664">
                                <path fill="currentColor"
                                      d="M895 12v264H738q-86 0-116 36t-30 108v189h293l-39 296H592v759H286V905H31V609h255V391q0-186 104-288.5T667 0q147 0 228 12"></path>
                            </svg>
                        </a>

                        <a href="#"
                           title="Twitter"
                           class="text-gray-500 hover:text-sky-600
                                  dark:hover:text-sky-400 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="1.25em" height="1em"
                                 viewBox="0 0 1600 1280">
                                <path fill="currentColor"
                                      d="M1588 152q-67 98-162 167q1 14 1 42q0 130-38 259.5T1273.5 869T1089 1079.5t-258 146t-323 54.5q-271 0-496-145q35 4 78 4q225 0 401-138q-105-2-188-64.5T189 777q33 5 61 5q43 0 85-11q-112-23-185.5-111.5T76 454v-4q68 38 146 41q-66-44-105-115T78 222q0-88 44-163q121 149 294.5 238.5T788 397q-8-38-8-74q0-134 94.5-228.5T1103 0q140 0 236 102q109-21 205-78q-37 115-142 178q93-10 186-50"></path>
                            </svg>
                        </a>

                        <a href="#"
                           title="Linkedin"
                           class="text-gray-500 hover:text-sky-600
                                  dark:hover:text-sky-400 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="1.03em" height="1em"
                                 viewBox="0 0 1536 1504">
                                <path fill="currentColor"
                                      d="M349 497v991H19V497zm21-306q1 73-50.5 122T184 362h-2q-82 0-132-49T0 191q0-74 51.5-122.5T186 20t133 48.5T370 191m1166 729v568h-329V958q0-105-40.5-164.5T1040 734q-63 0-105.5 34.5T871 854q-11 30-11 81v553H531q2-399 2-647t-1-296l-1-48h329v144h-2q20-32 41-56t56.5-52t87-43.5T1157 474q171 0 275 113.5T1536 920"></path>
                            </svg>
                        </a>

                        <a href="#"
                           title="GitHub"
                           class="text-gray-500 hover:text-sky-600
                                  dark:hover:text-sky-400 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="1.03em" height="1em"
                                 viewBox="0 0 1536 1504">
                                <path fill="currentColor"
                                      d="M768 0q209 0 385.5 103T1433 382.5T1536 768q0 251-146.5 451.5T1011 1497q-27 5-40-7t-13-30q0-3 .5-76.5t.5-134.5q0-97-52-142q57-6 102.5-18t94-39t81-66.5t53-105T1258 728q0-119-79-206q37-91-8-204q-28-9-81 11t-92 44l-38 24q-93-26-192-26t-192 26q-16-11-42.5-27T450 331.5T365 318q-45 113-8 204q-79 87-79 206q0 85 20.5 150T351 983t80.5 67t94 39t102.5 18q-39 36-49 103q-21 10-45 15t-57 5t-65.5-21.5T356 1146q-19-32-48.5-52t-49.5-24l-20-3q-21 0-29 4.5t-5 11.5t9 14t13 12l7 5q22 10 43.5 38t31.5 51l10 23q13 38 44 61.5t67 30t69.5 7t55.5-3.5l23-4q0 38 .5 88.5t.5 54.5q0 18-13 30t-40 7q-232-77-378.5-277.5T0 768q0-209 103-385.5T382.5 103T768 0M291 1103q3-7-7-12q-10-3-13 2q-3 7 7 12q9 6 13-2m31 34q7-5-2-16q-10-9-16-3q-7 5 2 16q10 10 16 3m30 45q9-7 0-19q-8-13-17-6q-9 5 0 18t17 7m42 42q8-8-4-19q-12-12-20-3q-9 8 4 19q12 12 20 3m57 25q3-11-13-16q-15-4-19 7t13 15q15 6 19-6m63 5q0-13-17-11q-16 0-16 11q0 13 17 11q16 0 16-11m58-10q-2-11-18-9q-16 3-14 15t18 8t14-14"></path>
                            </svg>
                        </a>

                    </div>
                </div>

                <!-- LINKS COLUMNS -->
                <div class="md:col-span-3">
                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 md:grid-cols-4">

                        <div
                            v-for="rootPage in cmsFooter"
                            :key="rootPage.id"
                        >
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                                <Link
                                    :href="rootPage.url"
                                    class="hover:text-sky-600 dark:hover:text-sky-400 transition"
                                >
                                    {{ getTitle(rootPage) }}
                                </Link>
                            </h3>

                            <ul class="space-y-3 text-sm">
                                <li
                                    v-for="child in getFooterChildren(rootPage)"
                                    :key="child.id"
                                >
                                    <Link
                                        :href="child.url"
                                        class="text-gray-600 dark:text-gray-400
                               hover:text-sky-600 dark:hover:text-sky-400 transition"
                                    >
                                        {{ getTitle(child) }}
                                    </Link>

                                    <ul
                                        v-if="getFooterChildren(child).length"
                                        class="mt-2 space-y-2"
                                    >
                                        <li
                                            v-for="subChild in getFooterChildren(child)"
                                            :key="subChild.id"
                                        >
                                            <Link
                                                :href="subChild.url"
                                                class="text-xs text-gray-500 dark:text-gray-500
                                       hover:text-sky-600 dark:hover:text-sky-400 transition"
                                            >
                                                {{ getTitle(subChild) }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- ILLUSTRATION -->
        <div class="absolute bottom-0 left-0 right-0
            flex justify-center
            w-full pointer-events-none
            opacity-40 dark:opacity-40">

            <img
                src="/assets/illustrations/footer/footer-cityscape.png"
                class="max-w-full h-auto"
                alt=""
            >
        </div>

        <!-- COPYRIGHT -->
        <div class="border-t border-gray-200 dark:border-gray-800">
            <div class="mx-auto max-w-6xl px-6 py-6 font-semibold
                        text-center text-sm text-gray-700 dark:text-gray-300">
                {{ new Date().getFullYear() }} © Community. All rights reserved.
            </div>
        </div>

    </footer>
</template>
