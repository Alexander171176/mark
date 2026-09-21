<script setup>
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import Checkbox from '@/Components/Base/Checkbox.vue'
import InputError from '@/Components/Base/InputError.vue'
import InputLabel from '@/Components/Base/InputLabel.vue'
import PrimaryButton from '@/Components/Base/PrimaryButton.vue'
import TextInput from '@/Components/Base/TextInput.vue'

const props = defineProps({
    canResetPassword: {
        type: Boolean,
        default: false,
    },

    status: {
        type: String,
        default: '',
    },
})

const emit = defineEmits([
    'forgot',
    'register',
])

const { t } = useI18n()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            remember: form.remember
                ? 'on'
                : '',
        }))
        .post(route('login'), {
            preserveScroll: true,

            onFinish: () => {
                form.reset('password')
            },
        })
}
</script>

<template>
    <div>
        <!-- Status -->
        <div
            v-if="status"
            class="mb-4 rounded-lg
                   bg-emerald-50 px-3 py-2
                   text-sm font-medium
                   text-emerald-700
                   dark:bg-emerald-950/40
                   dark:text-emerald-300"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <!-- Email -->
            <div>
                <InputLabel
                    for="header-login-email"
                    :value="t('email')"
                />

                <TextInput
                    id="header-login-email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.email"
                />
            </div>

            <!-- Password -->
            <div class="mt-3">
                <InputLabel
                    for="header-login-password"
                    :value="t('password')"
                />

                <TextInput
                    id="header-login-password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password"
                />
            </div>

            <!-- Remember -->
            <div class="mt-3">
                <label
                    class="flex cursor-pointer
                           items-center"
                >
                    <Checkbox
                        v-model:checked="form.remember"
                        name="remember"
                    />

                    <span
                        class="ml-2 text-sm
                               text-slate-600
                               dark:text-slate-300"
                    >
                        {{ t('rememberMe') }}
                    </span>
                </label>
            </div>

            <!-- Actions -->
            <div
                class="mt-4 flex
                       items-center justify-between gap-3"
            >
                <button
                    v-if="canResetPassword"
                    type="button"
                    class="text-sm font-medium
                           text-blue-600
                           hover:underline
                           dark:text-blue-300"
                    @click="emit('forgot')"
                >
                    {{ t('forgotPassword') }}
                </button>

                <PrimaryButton
                    class="ml-auto"
                    :class="{
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    {{ t('login') }}
                </PrimaryButton>
            </div>
        </form>

        <!-- Registration switch -->
        <div
            class="mt-4 border-t
                   border-slate-200 pt-3
                   text-center
                   dark:border-slate-700"
        >
            <span
                class="text-sm text-slate-500
                       dark:text-slate-400"
            >
                {{ t('registerPrompt') }}
            </span>

            <button
                type="button"
                class="ml-1 text-sm font-semibold
                       text-blue-600
                       hover:underline
                       dark:text-blue-300"
                @click="emit('register')"
            >
                {{ t('register') }}
            </button>
        </div>
    </div>
</template>
