<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import Checkbox from '@/Components/Base/Checkbox.vue'
import InputError from '@/Components/Base/InputError.vue'
import InputLabel from '@/Components/Base/InputLabel.vue'
import PrimaryButton from '@/Components/Base/PrimaryButton.vue'
import TextInput from '@/Components/Base/TextInput.vue'

const emit = defineEmits([
    'login',
])

const { t } = useI18n()
const page = usePage()

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
})

const submit = () => {
    form.post(route('register'), {
        preserveScroll: true,

        onFinish: () => {
            form.reset(
                'password',
                'password_confirmation'
            )
        },
    })
}
</script>

<template>
    <div>
        <form @submit.prevent="submit">
            <!-- Name -->
            <div>
                <InputLabel
                    for="header-register-name"
                    :value="t('name')"
                />

                <TextInput
                    id="header-register-name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.name"
                />
            </div>

            <!-- Email -->
            <div class="mt-3">
                <InputLabel
                    for="header-register-email"
                    :value="t('email')"
                />

                <TextInput
                    id="header-register-email"
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
                    for="header-register-password"
                    :value="t('password')"
                />

                <TextInput
                    id="header-register-password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password"
                />
            </div>

            <!-- Password confirmation -->
            <div class="mt-3">
                <InputLabel
                    for="header-register-password-confirmation"
                    :value="t('confirmPassword')"
                />

                <TextInput
                    id="header-register-password-confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="
                        form.errors.password_confirmation
                    "
                />
            </div>

            <!-- Terms -->
            <div
                v-if="
                    page.props?.jetstream?.hasTermsAndPrivacyPolicyFeature"
                class="mt-2"
            >
                <label
                    class="flex items-start gap-2
                           text-sm text-slate-600
                           dark:text-slate-300"
                >
                    <Checkbox
                        id="header-register-terms"
                        v-model:checked="form.terms"
                        name="terms"
                        required
                    />

                    <span>
                        {{ t('agreeTerms1') }}

                        <a
                            :href="route('terms.show')"
                            target="_blank"
                            class="font-semibold
                                   text-blue-600
                                   hover:underline
                                   dark:text-blue-300"
                        >
                            {{ t('termsOfService') }}
                        </a>

                        {{ t('agreeTerms2') }}

                        <a
                            :href="route('policy.show')"
                            target="_blank"
                            class="font-semibold
                                   text-blue-600
                                   hover:underline
                                   dark:text-blue-300"
                        >
                            {{ t('privacyPolicy') }}
                        </a>
                    </span>
                </label>

                <InputError
                    class="mt-2"
                    :message="form.errors.terms"
                />
            </div>

            <!-- Submit -->
            <div
                class="mt-2 flex
                       items-center justify-end"
            >
                <PrimaryButton
                    :class="{
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    {{ t('register') }}
                </PrimaryButton>
            </div>
        </form>

        <!-- Login switch -->
        <div
            class="mt-2 border-t
                   border-slate-200 pt-3
                   text-center
                   dark:border-slate-700"
        >
            <button
                type="button"
                class="text-sm font-semibold
                       text-blue-600
                       hover:underline
                       dark:text-blue-300"
                @click="emit('login')"
            >
                {{ t('alreadyRegistered') }}
            </button>
        </div>
    </div>
</template>
