<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'

const { t } = useI18n()

const props = defineProps({
    visitorLog: {
        type: Object,
        default: () => ({}),
    },
})

const log = props.visitorLog.data ?? props.visitorLog

const formatDate = (value) => {
    if (!value) {
        return '—'
    }

    return new Date(value).toLocaleString()
}

const valueOrDash = (value) => {
    return value === null || value === undefined || value === ''
        ? '—'
        : value
}
</script>

<template>
    <AdminLayout title="Analytics Visitor Log">
        <template #header>
            <TitlePage>
                {{ t('analyticsLogs') }} #{{ log.id }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-7xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="mb-4">
                    <Link
                        :href="route('admin.analyticsVisitorLogs.index')"
                        class="inline-block px-2 py-0.5 text-sm
                               font-semibold text-white bg-blue-600 rounded hover:bg-blue-700"
                    >
                        ← {{ t('back') }}
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('user') }}
                        </h2>

                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('user') }} ID:</b>
                            {{ valueOrDash(log.user_id) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('session')}} ID:</b>
                            {{ valueOrDash(log.session_id) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Visitor UUID:</b>
                            {{ valueOrDash(log.visitor_uuid) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('gender') }}:</b>
                            {{ valueOrDash(log.user_gender) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('age') }}:</b>
                            {{ valueOrDash(log.user_age) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('ageGroup') }}:</b>
                            {{ valueOrDash(log.user_age_group) }}
                        </p>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('page') }}
                        </h2>

                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('date') }}:</b>
                            {{ formatDate(log.visited_at) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('method') }}:</b>
                            {{ valueOrDash(log.method) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('status') }}:</b>
                            {{ valueOrDash(log.status_code) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('title') }}:</b>
                            {{ valueOrDash(log.page_title) }}
                        </p>
                        <p class="break-all">
                            <b class="text-indigo-700 dark:text-indigo-300">URL:</b>
                            {{ valueOrDash(log.url) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Route:</b>
                            {{ valueOrDash(log.route_name) }}
                        </p>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('context') }}
                        </h2>

                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Module:</b>
                            {{ valueOrDash(log.module) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Entity type:</b>
                            {{ valueOrDash(log.entity_type) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Entity ID:</b>
                            {{ valueOrDash(log.entity_id) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Event type:</b>
                            {{ valueOrDash(log.event_type) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Request type:</b>
                            {{ valueOrDash(log.request_type) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Response time:</b>
                            {{ valueOrDash(log.response_time) }}
                        </p>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('geography') }}
                        </h2>

                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">IP:</b>
                            {{ valueOrDash(log.ip_address) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('country') }}:</b>
                            {{ valueOrDash(log.country) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('region') }}:</b>
                            {{ valueOrDash(log.region) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">{{ t('city') }}:</b>
                            {{ valueOrDash(log.city) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Locale:</b>
                            {{ valueOrDash(log.locale) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Timezone:</b>
                            {{ valueOrDash(log.timezone) }}
                        </p>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('device') }}
                        </h2>

                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Device type:</b>
                            {{ valueOrDash(log.device_type) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Device name:</b>
                            {{ valueOrDash(log.device_name) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Browser:</b>
                            {{ valueOrDash(log.browser) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Browser version:</b>
                            {{ valueOrDash(log.browser_version) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">OS:</b>
                            {{ valueOrDash(log.os) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">OS version:</b>
                            {{ valueOrDash(log.os_version) }}
                        </p>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            Frontend {{ t('data') }}
                        </h2>

                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Screen {{ t('width') }}:</b>
                            {{ valueOrDash(log.screen_width) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Screen {{ t('height') }}:</b>
                            {{ valueOrDash(log.screen_height) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Browser language:</b>
                            {{ valueOrDash(log.browser_language) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Time on page:</b>
                            {{ valueOrDash(log.time_on_page) }} сек.
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Scroll depth:</b>
                            {{ valueOrDash(log.scroll_depth) }}%
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Clicks:</b>
                            {{ valueOrDash(log.clicks_count) }}
                        </p>
                    </div>

                    <div class="p-4 bg-white dark:bg-slate-800 border border-slate-400
                                rounded md:col-span-2">
                        <h2 class="mb-3 font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('source') }} User-Agent
                        </h2>

                        <p class="break-all">
                            <b class="text-indigo-700 dark:text-indigo-300">Referer:</b>
                            {{ valueOrDash(log.referer) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Source type:</b>
                            {{ valueOrDash(log.source_type) }}
                        </p>
                        <p>
                            <b class="text-indigo-700 dark:text-indigo-300">Search engine:</b>
                            {{ valueOrDash(log.search_engine) }}
                        </p>
                        <p class="break-all">
                            <b class="text-indigo-700 dark:text-indigo-300">User-Agent:</b>
                            {{ valueOrDash(log.user_agent) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
