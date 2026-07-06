<script setup>
import { computed, onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const CONSENT_ACCEPTED_KEY = 'privacy_user_consent_accepted'
const CONSENT_VERSION_KEY = 'privacy_user_consent_version'
const POLICY_VERSION = '1'

const page = usePage()

const privacy = computed(() => page.props.laravelLang?.public?.privacy?.popup ?? {})

const isVisible = ref(false)
const isLoading = ref(false)

const hasLocalConsent = () => {
    return localStorage.getItem(CONSENT_ACCEPTED_KEY) === 'true'
        && localStorage.getItem(CONSENT_VERSION_KEY) === POLICY_VERSION
}

const saveLocalConsent = () => {
    localStorage.setItem(CONSENT_ACCEPTED_KEY, 'true')
    localStorage.setItem(CONSENT_VERSION_KEY, POLICY_VERSION)
}

const acceptAll = async () => {
    isLoading.value = true

    try {
        saveLocalConsent()

        await axios.post('/api/privacy/consent', {
            accepted: true,
            policy_version: Number(POLICY_VERSION),
        })

        isVisible.value = false
    } catch (error) {
        console.error('Ошибка сохранения согласия:', error)
        isVisible.value = false
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    isVisible.value = !hasLocalConsent()
})
</script>

<template>
    <div
        v-if="isVisible"
        class="fixed inset-x-0 bottom-0 z-50 px-4 pb-4 sm:px-6 sm:pb-6"
    >
        <div
            class="mx-auto max-w-5xl rounded-2xl border-2 border-gray-200
                   bg-white p-5 shadow-2xl dark:border-gray-700 dark:bg-gray-900"
        >
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-200">
                    <h3 class="mb-1 text-base font-semibold text-gray-900 dark:text-white">
                        {{ privacy.title }}
                    </h3>

                    <p>
                        {{ privacy.description }}
                        <a
                            href="/privacy"
                            target="_blank"
                            class="font-medium text-blue-600 underline
                                   hover:text-blue-700 dark:text-blue-400"
                        >
                            {{ privacy.policy }}
                        </a>.
                    </p>
                </div>

                <button
                    type="button"
                    :disabled="isLoading"
                    @click="acceptAll"
                    class="shrink-0 rounded-md bg-blue-600 px-3 py-1
                           text-sm font-semibold text-white transition
                           hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ isLoading ? privacy.saving : privacy.accept }}
                </button>
            </div>
        </div>
    </div>
</template>
