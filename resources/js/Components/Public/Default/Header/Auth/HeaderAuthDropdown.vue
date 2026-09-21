<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DropdownExt from '@/Components/Base/DropdownExt.vue'

import HeaderLoginForm
    from '@/Components/Public/Default/Header/Auth/HeaderLoginForm.vue'

import HeaderRegisterForm
    from '@/Components/Public/Default/Header/Auth/HeaderRegisterForm.vue'

import HeaderForgotPasswordForm
    from '@/Components/Public/Default/Header/Auth/HeaderForgotPasswordForm.vue'

const { t } = useI18n()
const page = usePage()

const view = ref('login')

const canResetPassword = computed(() => {
    return Boolean(
        page.props?.canResetPassword
        ?? true
    )
})

const status = computed(() => {
    return page.props?.status || ''
})

const setView = (value) => {
    view.value = value
}
</script>

<template>
    <DropdownExt
        align="right"
        width="96"
        :close-on-content-click="false"
        :content-classes="[
        'bg-white',
        'dark:bg-slate-900',
    ]"
    >
        <!-- Trigger -->
        <template #trigger>
            <button
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

        <!-- Content -->
        <template #content>
            <div
                class="w-96 max-w-[calc(100vw-2rem)]
                       overflow-hidden rounded-xl
                       bg-white
                       dark:bg-slate-900"
            >
                <!-- Header -->
                <div
                    class="border-b
                           border-slate-200
                           dark:border-slate-700"
                >
                    <!-- Tabs -->
                    <div
                        v-if="view !== 'forgot-password'"
                        class="grid grid-cols-2"
                    >
                        <button
                            type="button"
                            class="relative px-4 py-3
                                   text-sm font-semibold
                                   transition"
                            :class="
                                view === 'login'
                                    ? 'text-blue-700 dark:text-blue-300'
                                    : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                            "
                            @click="setView('login')"
                        >
                            <span class="flex flex-row items-center justify-center gap-2">
                                <svg viewBox="0 0 512 512"
                                     class="h-4 w-4" fill="currentColor">
                                    <path
                                        d="M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z" />
                                </svg>
                                {{ t('login') }}
                            </span>

                            <span
                                v-if="view === 'login'"
                                class="absolute
                                       bottom-0 left-4 right-4
                                       h-0.5 rounded-full
                                       bg-blue-600
                                       dark:bg-blue-400"
                            ></span>
                        </button>

                        <button
                            type="button"
                            class="relative px-4 py-3
                                   text-sm font-semibold
                                   transition"
                            :class="
                                view === 'register'
                                    ? 'text-blue-700 dark:text-blue-300'
                                    : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                            "
                            @click="setView('register')"
                        >
                            <span class="flex flex-row items-center justify-center gap-2">
                                <svg viewBox="0 0 512 512"
                                     class="h-3 w-3" fill="currentColor">
                                    <path
                                        d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" />
                                </svg>
                                {{ t('register') }}
                            </span>

                            <span
                                v-if="view === 'register'"
                                class="absolute
                                       bottom-0 left-4 right-4
                                       h-0.5 rounded-full
                                       bg-blue-600
                                       dark:bg-blue-400"
                            ></span>
                        </button>
                    </div>

                    <!-- Forgot heading -->
                    <div
                        v-else
                        class="px-5 py-3"
                    >
                        <div
                            class="text-sm font-semibold
                                   text-slate-800
                                   dark:text-slate-100"
                        >
                            {{ t('forgotPassword') }}
                        </div>
                    </div>
                </div>

                <!-- Forms -->
                <div
                    class="max-h-[calc(100vh-8rem)]
                           overflow-y-auto
                           overscroll-contain
                           px-5 py-4"
                >
                    <HeaderLoginForm
                        v-if="view === 'login'"
                        :can-reset-password="canResetPassword"
                        :status="status"
                        @forgot="
                            setView('forgot-password')
                        "
                        @register="
                            setView('register')
                        "
                    />

                    <HeaderRegisterForm
                        v-else-if="view === 'register'"
                        @login="
                            setView('login')
                        "
                    />

                    <HeaderForgotPasswordForm
                        v-else
                        :status="status"
                        @login="
                            setView('login')
                        "
                    />
                </div>
            </div>
        </template>
    </DropdownExt>
</template>
