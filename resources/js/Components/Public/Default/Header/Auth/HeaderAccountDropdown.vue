<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import Dropdown from '@/Components/Base/Dropdown.vue'
import DropdownLink from '@/Components/Base/DropdownLink.vue'

const { t } = useI18n()
const page = usePage()

const isAuth = computed(() => {
    return !!page.props?.auth?.user
})

const user = computed(() => {
    return page.props?.auth?.user || null
})

const managesProfilePhotos = computed(() => {
    return !!page.props?.jetstream?.managesProfilePhotos
})

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <Dropdown
        align="right"
        width="60"
        class="relative"
    >
        <!-- Trigger -->
        <template #trigger>
            <!-- Авторизованный пользователь -->
            <button
                v-if="isAuth"
                type="button"
                class="inline-flex h-9 w-9
                       items-center justify-center
                       overflow-hidden rounded-full
                       border border-slate-200
                       bg-white
                       text-slate-600
                       transition
                       hover:border-blue-300
                       hover:bg-slate-100
                       hover:text-blue-700
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-300
                       dark:border-slate-700
                       dark:bg-gray-900
                       dark:text-slate-300
                       dark:hover:border-blue-700
                       dark:hover:bg-gray-800
                       dark:hover:text-blue-300"
                :title="user?.name || t('account')"
                :aria-label="user?.name || t('account')"
            >
                <!-- Аватар -->
                <img
                    v-if="
                        managesProfilePhotos
                        && user?.profile_photo_url
                    "
                    class="h-full w-full object-cover"
                    :src="user.profile_photo_url"
                    :alt="user.name"
                />

                <!-- Fallback icon -->
                <svg
                    v-else
                    class="h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 448 512"
                    aria-hidden="true"
                >
                    <path
                        d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"
                    />
                </svg>
            </button>

            <!-- Гость -->
            <button
                v-else
                type="button"
                class="inline-flex h-9 w-9
                       items-center justify-center
                       rounded-full
                       border border-slate-200
                       bg-white
                       text-slate-600
                       transition
                       hover:border-blue-300
                       hover:bg-slate-100
                       hover:text-blue-700
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-300
                       dark:border-slate-700
                       dark:bg-gray-900
                       dark:text-slate-300
                       dark:hover:border-blue-700
                       dark:hover:bg-gray-800
                       dark:hover:text-blue-300"
                :title="t('account')"
                :aria-label="t('account')"
            >
                <svg
                    class="h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 448 512"
                    aria-hidden="true"
                >
                    <path
                        d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"
                    />
                </svg>
            </button>
        </template>

        <!-- Dropdown -->
        <template #content>
            <!-- Авторизованный пользователь -->
            <template v-if="isAuth">
                <!-- Информация о пользователе -->
                <div
                    class="px-4 py-4
                           border-b border-gray-200
                           dark:border-gray-700"
                >
                    <div class="flex items-center gap-3">
                        <!-- Аватар -->
                        <div
                            class="flex h-10 w-10 shrink-0
                                   items-center justify-center
                                   overflow-hidden rounded-full
                                   bg-slate-100
                                   text-slate-500
                                   dark:bg-gray-800
                                   dark:text-slate-300"
                        >
                            <img
                                v-if="
                                    managesProfilePhotos
                                    && user?.profile_photo_url
                                "
                                class="h-full w-full object-cover"
                                :src="user.profile_photo_url"
                                :alt="user.name"
                            />

                            <svg
                                v-else
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 448 512"
                                aria-hidden="true"
                            >
                                <path
                                    d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"
                                />
                            </svg>
                        </div>

                        <!-- Имя / email -->
                        <div class="min-w-0">
                            <div
                                class="truncate text-sm font-semibold
                                       text-slate-800
                                       dark:text-slate-100"
                            >
                                {{ user?.name }}
                            </div>

                            <div
                                class="mt-0.5 truncate
                                       text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                {{ user?.email }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Заголовок -->
                <div
                    class="px-4 py-2
                           text-xs font-semibold
                           text-slate-400
                           dark:text-slate-500"
                >
                    {{ t('accountManagement') }}
                </div>

                <!-- Профиль -->
                <DropdownLink :href="route('profile.show')">
                    {{ t('profile') }}
                </DropdownLink>

                <div
                    class="border-t border-gray-200
                           dark:border-gray-700"
                ></div>

                <!-- Выход -->
                <form @submit.prevent="logout">
                    <DropdownLink as="button">
                        {{ t('logout') }}
                    </DropdownLink>
                </form>
            </template>

            <!-- Гость -->
            <template v-else>
                <!-- Информация -->
                <div
                    class="px-4 py-4
                           border-b border-gray-200
                           dark:border-gray-700"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0
                                   items-center justify-center
                                   rounded-full
                                   bg-slate-100
                                   text-slate-500
                                   dark:bg-gray-800
                                   dark:text-slate-300"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 448 512"
                                aria-hidden="true"
                            >
                                <path
                                    d="M224 256A128 128 0 10224 0a128 128 0 000 256zm89.6 32h-16.7a174.1 174.1 0 01-145.8 0h-16.7A134.4 134.4 0 000 422.4V464a48 48 0 0048 48h352a48 48 0 0048-48v-41.6A134.4 134.4 0 00313.6 288z"
                                />
                            </svg>
                        </div>

                        <div>
                            <div
                                class="text-sm font-semibold
                                       text-slate-800
                                       dark:text-slate-100"
                            >
                                {{ t('guest') }}
                            </div>

                            <div
                                class="mt-0.5 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                {{ t('account') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вход -->
                <DropdownLink :href="route('login')">
                    {{ t('login') }}
                </DropdownLink>

                <!-- Регистрация -->
                <DropdownLink :href="route('register')">
                    {{ t('register') }}
                </DropdownLink>
            </template>
        </template>
    </Dropdown>
</template>
