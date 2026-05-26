<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import WelcomeCard from '@/Components/Admin/Welcome/WelcomeCard.vue'

defineProps({ t: { type: Function, required: true } })

const page = usePage()

const d = (key) => page.props.laravelLang?.admin?.welcome?.finance?.[key]?.description ?? ''

const descriptions = computed(() => ({
    currencies: d('currencies'),
    charts: d('charts'),
}))
</script>

<template>
    <div
        class="p-4 bg-gray-200 dark:bg-slate-700 bg-opacity-95 dark:bg-opacity-95
               grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5
               border-dashed border-y border-gray-400"
    >
        <WelcomeCard
            :href="route('admin.currencies.index')"
            :title="t('currencies')"
            :description="descriptions.currencies">
            <template #icon>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="0.5"
                    class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100"
                >
                    <path d="M0,5V19H24V5ZM7,9.749a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366A.517.517,0,0,1,3.507,16c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,4.431,10H1.5A.5.5,0,0,1,1,9.5v-1A.5.5,0,0,1,1.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,12.431,10H9.5A.5.5,0,0,1,9,9.5v-1A.5.5,0,0,1,9.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,20.431,10H17.5a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5h5a.5.5,0,0,1,.5.5Z"></path>
                    <path d="M1,1H23a1,1,0,0,1,1,1V3a0,0,0,0,1,0,0H0A0,0,0,0,1,0,3V2A1,1,0,0,1,1,1Z"></path>
                    <path d="M0,21H24a0,0,0,0,1,0,0v1a1,1,0,0,1-1,1H1a1,1,0,0,1-1-1V21A0,0,0,0,1,0,21Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.charts.index')"
            :title="t('charts')"
            :description="descriptions.charts">
            <template #icon>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="0.5"
                    class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100"
                >
                    <path
                        d="M13 6.068a6.035 6.035 0 0 1 4.932 4.933H24c-.486-5.846-5.154-10.515-11-11v6.067Z"></path>
                    <path
                        d="M18.007 13c-.474 2.833-2.919 5-5.864 5a5.888 5.888 0 0 1-3.694-1.304L4 20.731C6.131 22.752 8.992 24 12.143 24c6.232 0 11.35-4.851 11.857-11h-5.993Z"></path>
                    <path
                        d="M6.939 15.007A5.861 5.861 0 0 1 6 11.829c0-2.937 2.167-5.376 5-5.85V0C4.85.507 0 5.614 0 11.83c0 2.695.922 5.174 2.456 7.17l4.483-3.993Z"></path>
                </svg>
            </template>
        </WelcomeCard>
    </div>
</template>
