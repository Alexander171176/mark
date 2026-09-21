<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import InputError from '@/Components/Base/InputError.vue'
import InputLabel from '@/Components/Base/InputLabel.vue'
import PrimaryButton from '@/Components/Base/PrimaryButton.vue'
import TextInput from '@/Components/Base/TextInput.vue'

const props = defineProps({
    status: {
        type: String,
        default: '',
    },
})

const emit = defineEmits([
    'login',
])

const { t } = useI18n()

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'), {
        preserveScroll: true,
    })
}

const successfulStatus = computed(() => {
    return props.status || ''
})
</script>

<template>
    <div>
        <button
            type="button"
            class="mb-4 inline-flex
                   items-center gap-1
                   text-sm font-semibold
                   text-blue-600
                   hover:underline
                   dark:text-blue-300"
            @click="emit('login')"
        >
            <span>←</span>
            <span>{{ t('login') }}</span>
        </button>

        <div
            class="mb-4 text-sm font-semibold text-center
                   text-slate-600 dark:text-slate-300"
        >
            {{ t('forgotPassword') }}
        </div>

        <div
            v-if="successfulStatus"
            class="mb-4 rounded-lg
                   bg-emerald-50 px-3 py-2
                   text-sm font-medium
                   text-emerald-700
                   dark:bg-emerald-950/40
                   dark:text-emerald-300"
        >
            {{ successfulStatus }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel
                    for="header-forgot-email"
                    :value="t('email')"
                />

                <TextInput
                    id="header-forgot-email"
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

            <div
                class="mt-4 flex
                       items-center justify-center"
            >
                <PrimaryButton
                    :class="{
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    {{ t('emailPasswordResetLink') }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
