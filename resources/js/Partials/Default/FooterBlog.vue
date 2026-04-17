<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<template>
    <footer class="relative bg-gray-100 dark:bg-gray-900
                   border-t border-gray-200 dark:border-gray-800">

        <!-- MAIN CONTENT -->
        <div class="mx-auto px-3 py-16">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                <!-- LOGO + TEXT -->
                <div class="space-y-6">

                    <div class="flex items-center gap-3">
                        <div>
                        <!-- LOGO -->
                        <Link :href="route('home')"
                              class="flex items-center justify-center gap-3 logo">
                            <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg"
                                 fill="currentColor" viewBox="0 0 640 512">
                                <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                            </svg>
                            <span class="font-semibold text-xs md:text-lg">
                                SW Community
                            </span>
                        </Link>
                        <h3 class="text-center subtitle">
                            <span class="font-semibold text-gradient">
                                {{ t('ourCommunityBlog') }}
                            </span>
                        </h3>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Изучайте статьи и руководства от экспертов сообщества.
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
                    <div class="grid grid-cols-1 gap-10
                                sm:grid-cols-2 md:grid-cols-4">

                        <!-- COLUMN -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                                Discover
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Get Started</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Product</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Features</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Sign Up</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Sign In</a></li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                                Company
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">About</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Training</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">FAQs</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Contact</a></li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                                Support
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Help Center</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Guides</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">API Docs</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Terms</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Privacy</a></li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                                Legal
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Policy</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Security</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition">Licenses</a></li>
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
