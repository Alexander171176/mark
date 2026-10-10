<script setup>
/**
 * @version PulsarCMS 1.0
 * Табличное представление журнала Email Notifications.
 * Записи доступны только для просмотра.
 */

import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
    logs: { type: Array, default: () => [] },
    statuses: { type: Object, default: () => ({}) },
})

/* ===================== Status ===================== */

const statusLabels = {
    pending: 'inLine',
    processing: 'statusProcessing',
    retrying: 'retryAttempt',
    sent: 'sent',
    failed: 'error',
    skipped: 'omitted',
}

const statusClasses = {
    pending: 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-600 ' +
        'dark:text-slate-100 dark:border-slate-500',
    processing: 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900/40 ' +
        'dark:text-blue-200 dark:border-blue-700',
    retrying: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 ' +
        'dark:text-amber-200 dark:border-amber-700',
    sent: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 ' +
        'dark:text-emerald-200 dark:border-emerald-700',
    failed: 'bg-red-100 text-red-700 border-red-300 dark:bg-red-900/40 ' +
        'dark:text-red-200 dark:border-red-700',
    skipped: 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 ' +
        'dark:text-gray-200 dark:border-gray-600',
}

const getStatusLabel = (status) => props.statuses?.[status] || statusLabels[status] || status || '—'
const getStatusClasses = (status) => statusClasses[status] || statusClasses.pending

/* ===================== Helpers ===================== */

const formatDate = (value) => {
    if (!value) return '—'

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '—'

    return date.toLocaleString(locale.value || undefined, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    })
}

const truncateText = (value, maxLength = 70) => {
    if (!value) return ''
    const text = String(value)
    return text.length > maxLength ? `${text.slice(0, maxLength).trimEnd()}…` : text
}

/* ===================== Details ===================== */

const expandedIds = ref([])

const isExpanded = (id) => expandedIds.value.includes(id)

const toggleDetails = (id) => {
    expandedIds.value = isExpanded(id)
        ? expandedIds.value.filter((item) => item !== id)
        : [...expandedIds.value, id]
}

// При смене страницы не сохраняем раскрытые записи, отсутствующие в списке.
watch(
    () => props.logs,
    (logs) => {
        const visibleIds = new Set((logs || []).map((log) => log.id))
        expandedIds.value = expandedIds.value.filter((id) => visibleIds.has(id))
    }
)
</script>

<template>
    <div class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-200 dark:border-slate-600 relative">
        <div class="overflow-x-auto">
            <table
                v-if="logs.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="text-xs uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-1 py-3 whitespace-nowrap w-px text-center">ID</th>
                    <th class="px-2 py-3 text-left">{{ t('recipient') }}</th>
                    <th class="px-2 py-3 text-left">{{ t('event') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">{{ t('status') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">
                        {{ t('sortAttemptIdDesc') }}
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap text-left">{{ t('created') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-end">{{ t('actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="log in logs" :key="log.id">
                    <tr
                        class="text-xs border-b border-slate-200 dark:border-slate-600
                               hover:bg-slate-100 dark:hover:bg-cyan-800">
                        <td class="px-1 py-2 text-center whitespace-nowrap font-semibold">
                            {{ log.id }}
                        </td>
                        <td class="px-2 py-2 break-all font-medium
                                   text-violet-700 dark:text-violet-300"
                            :title="log.recipient || ''">
                            {{ log.recipient || '—' }}
                        </td>
                        <td class="px-2 py-2 min-w-48">
                            <div class="font-semibold text-teal-700 dark:text-teal-300"
                                 :title="log.subject || ''">
                                {{ truncateText(log.subject, 90) || t('untitled') }}
                            </div>
                            <div class="text-xs font-mono
                                        text-slate-700 dark:text-slate-300 break-all"
                                 :title="log.event || ''">
                                {{ truncateText(log.event, 90) || '—' }}
                            </div>
                        </td>
                        <td class="px-2 py-2 text-center whitespace-nowrap">
                            <span class="inline-block text-[10px] px-2 py-0.5 rounded-sm
                                         border font-semibold"
                                  :class="getStatusClasses(log.status)">
                                {{ getStatusLabel(log.status) }}
                            </span>
                        </td>
                        <td class="px-2 py-2 text-center tabular-nums">
                            {{ log.attempts ?? 0 }}
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs
                                   font-semibold text-sky-700 dark:text-sky-300 ">
                            {{ formatDate(log.created_at) }}
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-end">
                            <button
                                type="button"
                                class="px-2 py-0.5 text-xs font-semibold
                                       text-blue-600 dark:text-blue-300
                                       border border-blue-200 dark:border-slate-500
                                       rounded-sm hover:bg-blue-50 dark:hover:bg-slate-600"
                                :aria-expanded="isExpanded(log.id)"
                                :aria-controls="`email-log-details-${log.id}`"
                                @click="toggleDetails(log.id)"
                            >
                                {{ isExpanded(log.id) ? t('hide') : t('readMore') }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="isExpanded(log.id)"
                        :id="`email-log-details-${log.id}`"
                        class="bg-slate-50 dark:bg-slate-800/60 border-b-2
                               border-slate-300 dark:border-slate-500">
                        <td colspan="7" class="px-4 py-4">
                            <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4
                                       text-xs text-slate-700 dark:text-slate-100">
                                <div class="min-w-0">
                                    <dt class="font-semibold mb-1">UUID</dt>
                                    <dd class="break-all font-mono">{{ log.uuid || '—' }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="font-semibold mb-1">{{ t('queue') }}</dt>
                                    <dd class="break-all">{{ log.queue || '—' }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="font-semibold mb-1">{{ t('postalTransport') }}</dt>
                                    <dd class="break-all">{{ log.mailer || '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold mb-1">{{ t('inLine') }}</dt>
                                    <dd>{{ formatDate(log.queued_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold mb-1">{{ t('processingStarted') }}</dt>
                                    <dd>{{ formatDate(log.processing_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold mb-1">{{ t('sent') }}</dt>
                                    <dd>{{ formatDate(log.sent_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold mb-1">{{ t('errorRecorded') }}</dt>
                                    <dd>{{ formatDate(log.failed_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold mb-1">{{ t('lastUpdate') }}</dt>
                                    <dd>{{ formatDate(log.updated_at) }}</dd>
                                </div>
                                <div v-if="log.error_type" class="min-w-0">
                                    <dt class="font-semibold mb-1">{{ t('errorType') }}</dt>
                                    <dd class="break-all text-red-700 dark:text-red-300">
                                        {{ log.error_type }}
                                    </dd>
                                </div>
                                <div
                                    v-if="log.error_message"
                                    class="min-w-0 sm:col-span-2 lg:col-span-3">
                                    <dt class="font-semibold mb-1">{{ t('description') }}</dt>
                                    <dd class="break-words whitespace-pre-wrap
                                               text-red-700 dark:text-red-300">
                                        {{ log.error_message }}
                                    </dd>
                                </div>
                            </dl>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
            <div v-else
                 class="p-8 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
