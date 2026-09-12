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

/*
|--------------------------------------------------------------------------
| Форматирование
|--------------------------------------------------------------------------
*/

const formatDate = (value) => {
    if (!value) {
        return '—'
    }

    return new Date(value).toLocaleString()
}

const valueOrDash = (value) => {
    return value === null ||
    value === undefined ||
    value === ''
        ? '—'
        : value
}

const valueWithUnit = (
    value,
    unit
) => {
    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {
        return '—'
    }

    return `${value}${unit}`
}

/*
|--------------------------------------------------------------------------
| Общие классы
|--------------------------------------------------------------------------
*/

const cardClass =
    'p-3 bg-white dark:bg-slate-800 ' +
    'border border-slate-400 rounded'

const titleClass =
    'mb-2 font-semibold text-gray-800 ' +
    'dark:text-gray-100'

const labelClass =
    'text-indigo-700 dark:text-indigo-300'
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
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Назад -->
                <div class="mb-3">
                    <Link
                        :href="route('admin.analyticsVisitorLogs.index')"
                        class="inline-block px-2 py-0.5 text-sm
                               font-semibold text-white
                               bg-blue-600 rounded hover:bg-blue-700"
                    >
                        ← {{ t('back') }}
                    </Link>
                </div>

                <div
                    class="grid grid-cols-1 lg:grid-cols-2
                           gap-2 text-sm"
                >
                    <!-- Пользователь -->
                    <div :class="cardClass">
                        <h2 :class="titleClass">
                            {{ t('user') }}
                        </h2>

                        <p>
                            <b :class="labelClass">
                                {{ t('user') }} ID:
                            </b>

                            {{ valueOrDash(log.user_id) }}
                        </p>

                        <p class="break-all">
                            <b :class="labelClass">
                                {{ t('session') }} ID:
                            </b>

                            {{ valueOrDash(log.session_id) }}
                        </p>

                        <p class="break-all">
                            <b :class="labelClass">
                                Visitor UUID:
                            </b>

                            {{ valueOrDash(log.visitor_uuid) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('gender') }}:
                            </b>

                            {{ valueOrDash(log.user_gender) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('age') }}:
                            </b>

                            {{ valueOrDash(log.user_age) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('ageGroup') }}:
                            </b>

                            {{ valueOrDash(log.user_age_group) }}
                        </p>
                    </div>

                    <!-- Страница -->
                    <div :class="cardClass">
                        <h2 :class="titleClass">
                            {{ t('page') }}
                        </h2>

                        <p>
                            <b :class="labelClass">
                                {{ t('date') }}:
                            </b>

                            {{ formatDate(log.visited_at) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('method') }}:
                            </b>

                            {{ valueOrDash(log.method) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('status') }}:
                            </b>

                            {{ valueOrDash(log.status_code) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('title') }}:
                            </b>

                            {{ valueOrDash(log.page_title) }}
                        </p>

                        <p class="break-all">
                            <b :class="labelClass">
                                URL:
                            </b>

                            {{ valueOrDash(log.url) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Route:
                            </b>

                            {{ valueOrDash(log.route_name) }}
                        </p>
                    </div>

                    <!-- Контекст -->
                    <div :class="cardClass">
                        <h2 :class="titleClass">
                            {{ t('context') }}
                        </h2>

                        <p>
                            <b :class="labelClass">
                                Module:
                            </b>

                            {{ valueOrDash(log.module) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Entity type:
                            </b>

                            {{ valueOrDash(log.entity_type) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Entity ID:
                            </b>

                            {{ valueOrDash(log.entity_id) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Event type:
                            </b>

                            {{ valueOrDash(log.event_type) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Request type:
                            </b>

                            {{ valueOrDash(log.request_type) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Response time:
                            </b>

                            {{ valueWithUnit(log.response_time, ' ms') }}
                        </p>
                    </div>

                    <!-- География -->
                    <div :class="cardClass">
                        <h2 :class="titleClass">
                            {{ t('geography') }}
                        </h2>

                        <p>
                            <b :class="labelClass">
                                IP:
                            </b>

                            {{ valueOrDash(log.ip_address) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('country') }}:
                            </b>

                            {{ valueOrDash(log.country) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('region') }}:
                            </b>

                            {{ valueOrDash(log.region) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                {{ t('city') }}:
                            </b>

                            {{ valueOrDash(log.city) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Locale:
                            </b>

                            {{ valueOrDash(log.locale) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Timezone:
                            </b>

                            {{ valueOrDash(log.timezone) }}
                        </p>
                    </div>

                    <!-- Устройство -->
                    <div :class="cardClass">
                        <h2 :class="titleClass">
                            {{ t('device') }}
                        </h2>

                        <p>
                            <b :class="labelClass">
                                Device type:
                            </b>

                            {{ valueOrDash(log.device_type) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Device name:
                            </b>

                            {{ valueOrDash(log.device_name) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Browser:
                            </b>

                            {{ valueOrDash(log.browser) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Browser version:
                            </b>

                            {{ valueOrDash(log.browser_version) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                OS:
                            </b>

                            {{ valueOrDash(log.os) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                OS version:
                            </b>

                            {{ valueOrDash(log.os_version) }}
                        </p>
                    </div>

                    <!-- Frontend -->
                    <div :class="cardClass">
                        <h2 :class="titleClass">
                            Frontend {{ t('data') }}
                        </h2>

                        <p>
                            <b :class="labelClass">
                                Screen {{ t('width') }}:
                            </b>

                            {{ valueWithUnit(log.screen_width, ' px') }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Screen {{ t('height') }}:
                            </b>

                            {{ valueWithUnit(log.screen_height, ' px') }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Browser language:
                            </b>

                            {{ valueOrDash(log.browser_language) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Time on page:
                            </b>

                            {{ valueWithUnit(log.time_on_page, ' сек.') }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Scroll depth:
                            </b>

                            {{ valueWithUnit(log.scroll_depth, '%') }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Clicks:
                            </b>

                            {{ valueOrDash(log.clicks_count) }}
                        </p>
                    </div>

                    <!-- Источник -->
                    <div
                        :class="[
                            cardClass,
                            'lg:col-span-2',
                        ]"
                    >
                        <h2 :class="titleClass">
                            {{ t('source') }}
                        </h2>

                        <p class="break-all">
                            <b :class="labelClass">
                                Referer:
                            </b>

                            {{ valueOrDash(log.referer) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Source type:
                            </b>

                            {{ valueOrDash(log.source_type) }}
                        </p>

                        <p>
                            <b :class="labelClass">
                                Search engine:
                            </b>

                            {{ valueOrDash(log.search_engine) }}
                        </p>

                        <p class="break-all">
                            <b :class="labelClass">
                                User-Agent:
                            </b>

                            {{ valueOrDash(log.user_agent) }}
                        </p>
                    </div>

                    <!-- Системные данные -->
                    <div
                        :class="[
                            cardClass,
                            'lg:col-span-2',
                        ]"
                    >
                        <h2 :class="titleClass">
                            {{ t('data') }}
                        </h2>

                        <div
                            class="grid grid-cols-1
                                   md:grid-cols-3 gap-1"
                        >
                            <p>
                                <b :class="labelClass">
                                    ID:
                                </b>

                                {{ valueOrDash(log.id) }}
                            </p>

                            <p>
                                <b :class="labelClass">
                                    Created:
                                </b>

                                {{ formatDate(log.created_at) }}
                            </p>

                            <p>
                                <b :class="labelClass">
                                    Updated:
                                </b>

                                {{ formatDate(log.updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
