<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import WelcomeCard from '@/Components/Admin/Welcome/WelcomeCard.vue'

defineProps({ t: { type: Function, required: true } })

const page = usePage()

const d = (key) => page.props.laravelLang?.admin?.welcome?.finance?.[key]?.description ?? ''

const descriptions = computed(() => ({
    orders: d('orders'),
    coursePrices: d('coursePrices'),
    bundlePrices: d('bundlePrices'),
    subscriptionPlans: d('subscriptionPlans'),
    currencies: d('currencies'),
}))
</script>

<template>
    <div
        class="p-4 bg-gray-200 dark:bg-slate-700 bg-opacity-95 dark:bg-opacity-95
               grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5
               border-dashed border-y border-gray-400"
    >
        <WelcomeCard
            :href="route('admin.orders.index')"
            :title="t('orders')"
            :description="descriptions.orders">
            <template #icon>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="0.5"
                    class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100"
                >
                    <path
                        d="M14,0H3A1,1,0,0,0,2,1V23a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V8H15a1,1,0,0,1-1-1ZM5.5,17h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,17Zm0-5h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,12Zm5-3h-5A.5.5,0,0,1,5,8.5v-1A.5.5,0,0,1,5.5,7h5a.5.5,0,0,1,.5.5v1A.5.5,0,0,1,10.5,9Z"></path>
                    <polygon points="21.414 6 16 6 16 0.586 21.414 6"></polygon>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.subscriptionPlans.index')"
            :title="t('subscriptionPlans')"
            :description="descriptions.subscriptionPlans">
            <template #icon>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="0.5"
                    class="w-6 h-6 stroke-gray-900 dark:stroke-gray-100"
                >
                    <path class="fill-none" stroke-width="0.5"
                          stroke-linecap="round" stroke-linejoin="round"
                          d="M10 19H6.2C5.0799 19 4.51984 19 4.09202 18.782C3.71569 18.5903 3.40973 18.2843 3.21799 17.908C3 17.4802 3 16.9201 3 15.8V8.2C3 7.07989 3 6.51984 3.21799 6.09202C3.40973 5.71569 3.71569 5.40973 4.09202 5.21799C4.51984 5 5.0799 5 6.2 5H17.8C18.9201 5 19.4802 5 19.908 5.21799C20.2843 5.40973 20.5903 5.71569 20.782 6.09202C21 6.51984 21 7.0799 21 8.2V8.5M9 9.5V8.5M9 9.5H11.0001M9 9.5C7.88279 9.49998 7.00244 9.62616 7.0001 10.8325C6.99834 11.7328 7.00009 12 9.00009 12C11.0001 12 11.0001 12.2055 11.0001 13.1667C11.0001 13.889 11 14.5 9 14.5M9 15.5V14.5M9 14.5L7.0001 14.5M14 10H17M14 20L16.025 19.595C16.2015 19.5597 16.2898 19.542 16.3721 19.5097C16.4452 19.4811 16.5147 19.4439 16.579 19.399C16.6516 19.3484 16.7152 19.2848 16.8426 19.1574L21 15C21.5523 14.4477 21.5523 13.5523 21 13C20.4477 12.4477 19.5523 12.4477 19 13L14.8426 17.1574C14.7152 17.2848 14.6516 17.3484 14.601 17.421C14.5561 17.4853 14.5189 17.5548 14.4903 17.6279C14.458 17.7102 14.4403 17.7985 14.405 17.975L14 20Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.coursePrices.index')"
            :title="t('coursePrices')"
            :description="descriptions.coursePrices">
            <template #icon>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="0.5"
                    class="w-6 h-6 stroke-gray-900 dark:stroke-gray-100"
                >
                    <path class="fill-none" stroke-width="0.5"
                          stroke-linecap="round" stroke-linejoin="round"
                          d="M10 19H6.2C5.0799 19 4.51984 19 4.09202 18.782C3.71569 18.5903 3.40973 18.2843 3.21799 17.908C3 17.4802 3 16.9201 3 15.8V8.2C3 7.07989 3 6.51984 3.21799 6.09202C3.40973 5.71569 3.71569 5.40973 4.09202 5.21799C4.51984 5 5.0799 5 6.2 5H17.8C18.9201 5 19.4802 5 19.908 5.21799C20.2843 5.40973 20.5903 5.71569 20.782 6.09202C21 6.51984 21 7.0799 21 8.2V8.5M9 9.5V8.5M9 9.5H11.0001M9 9.5C7.88279 9.49998 7.00244 9.62616 7.0001 10.8325C6.99834 11.7328 7.00009 12 9.00009 12C11.0001 12 11.0001 12.2055 11.0001 13.1667C11.0001 13.889 11 14.5 9 14.5M9 15.5V14.5M9 14.5L7.0001 14.5M14 10H17M14 20L16.025 19.595C16.2015 19.5597 16.2898 19.542 16.3721 19.5097C16.4452 19.4811 16.5147 19.4439 16.579 19.399C16.6516 19.3484 16.7152 19.2848 16.8426 19.1574L21 15C21.5523 14.4477 21.5523 13.5523 21 13C20.4477 12.4477 19.5523 12.4477 19 13L14.8426 17.1574C14.7152 17.2848 14.6516 17.3484 14.601 17.421C14.5561 17.4853 14.5189 17.5548 14.4903 17.6279C14.458 17.7102 14.4403 17.7985 14.405 17.975L14 20Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.bundlePrices.index')"
            :title="t('bundlePrices')"
            :description="descriptions.bundlePrices">
            <template #icon>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="0.5"
                    class="w-6 h-6 stroke-gray-900 dark:stroke-gray-100"
                >
                    <path class="fill-none" stroke-width="0.5"
                          stroke-linecap="round" stroke-linejoin="round"
                          d="M10 19H6.2C5.0799 19 4.51984 19 4.09202 18.782C3.71569 18.5903 3.40973 18.2843 3.21799 17.908C3 17.4802 3 16.9201 3 15.8V8.2C3 7.07989 3 6.51984 3.21799 6.09202C3.40973 5.71569 3.71569 5.40973 4.09202 5.21799C4.51984 5 5.0799 5 6.2 5H17.8C18.9201 5 19.4802 5 19.908 5.21799C20.2843 5.40973 20.5903 5.71569 20.782 6.09202C21 6.51984 21 7.0799 21 8.2V8.5M9 9.5V8.5M9 9.5H11.0001M9 9.5C7.88279 9.49998 7.00244 9.62616 7.0001 10.8325C6.99834 11.7328 7.00009 12 9.00009 12C11.0001 12 11.0001 12.2055 11.0001 13.1667C11.0001 13.889 11 14.5 9 14.5M9 15.5V14.5M9 14.5L7.0001 14.5M14 10H17M14 20L16.025 19.595C16.2015 19.5597 16.2898 19.542 16.3721 19.5097C16.4452 19.4811 16.5147 19.4439 16.579 19.399C16.6516 19.3484 16.7152 19.2848 16.8426 19.1574L21 15C21.5523 14.4477 21.5523 13.5523 21 13C20.4477 12.4477 19.5523 12.4477 19 13L14.8426 17.1574C14.7152 17.2848 14.6516 17.3484 14.601 17.421C14.5561 17.4853 14.5189 17.5548 14.4903 17.6279C14.458 17.7102 14.4403 17.7985 14.405 17.975L14 20Z"></path>
                </svg>
            </template>
        </WelcomeCard>
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
                    <path d="M0,5V19H24V5ZM7,9.749a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366A.517.517,0,0,1,3.507,16c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,4.431,10H1.5A.5.5,0,0,1,1,9.5v-1A.5.5,0,0,1,1.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,12.431,10H9.5A.5.5,0,0,1,9,9.5v-1A.5.5,0,0,1,9.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,20.431,10H17.5a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5h5a.5.5,0,0,1,.5.5Z" ></path>
                    <path d="M1,1H23a1,1,0,0,1,1,1V3a0,0,0,0,1,0,0H0A0,0,0,0,1,0,3V2A1,1,0,0,1,1,1Z"></path>
                    <path d="M0,21H24a0,0,0,0,1,0,0v1a1,1,0,0,1-1,1H1a1,1,0,0,1-1-1V21A0,0,0,0,1,0,21Z"></path>
                </svg>
            </template>
        </WelcomeCard>
    </div>
</template>
