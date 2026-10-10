<script setup>
/**
 * @version PulsarCMS 1.0
 * Карточное представление журнала отправки Email Notifications.
 */
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
    logs: { type: Array, default: () => [] },
    statuses: { type: Object, default: () => ({}) },
})

/* ===================== Details ===================== */

const expandedIds = ref([])

watch(
    () => props.logs.map((log) => log.id),
    (ids) => {
        expandedIds.value = expandedIds.value.filter((id) => ids.includes(id))
    }
)

const isExpanded = (id) => expandedIds.value.includes(id)

const toggleDetails = (id) => {
    expandedIds.value = isExpanded(id)
        ? expandedIds.value.filter((item) => item !== id)
        : [...expandedIds.value, id]
}

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
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
    })
}

const truncateText = (text, maxLength = 80) => {
    if (!text) return ''
    const value = String(text)
    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-400 dark:border-slate-500 relative">
        <div v-if="props.logs.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <article
                    v-for="log in props.logs"
                    :key="log.id"
                    class="relative flex flex-col min-w-0 rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Header -->
                    <header
                        class="flex items-center justify-between gap-2 px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500">
                        <span
                            class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                   border border-gray-400 bg-slate-200 dark:bg-slate-700
                                   text-slate-800 dark:text-blue-100">
                            ID: {{ log.id }}
                        </span>
                        <span
                            class="text-[10px] px-2 py-0.5 rounded-sm border font-semibold"
                            :class="getStatusClasses(log.status)">
                            {{ getStatusLabel(log.status) }}
                        </span>
                    </header>

                    <!-- Content -->
                    <div class="flex flex-col flex-1 px-3 py-3 space-y-2
                                text-slate-700 dark:text-slate-100">
                        <div class="flex justify-center">
                            <div
                                class="text-xs font-semibold text-center
                                       text-teal-600 dark:text-teal-300 bg-white
                                       dark:bg-slate-600 border-2 border-teal-300
                                       dark:border-teal-700 px-2 py-0.5 rounded-md
                                       break-words min-w-0"
                                :title="log.subject || t('untitled')"
                            >
                                {{ truncateText(log.subject || t('untitled'), 100) }}
                            </div>
                        </div>

                        <div
                            class="text-center text-xs font-semibold
                                   text-violet-700 dark:text-violet-300 break-all"
                            :title="log.recipient">
                            {{ log.recipient || '—' }}
                        </div>

                        <div
                            class="text-center text-[11px] font-mono text-slate-700
                                   dark:text-slate-300 break-all"
                            :title="log.event">
                            {{ truncateText(log.event, 90) || '—' }}
                        </div>

                        <div
                            class="flex justify-between gap-2 text-[10px]
                                   font-semibold text-slate-600 dark:text-slate-200">
                            <span>{{ t('sortAttemptIdDesc') }} {{ log.attempts ?? 0 }}</span>
                            <span class="text-right text-sky-700 dark:text-sky-300">
                                {{ formatDate(log.created_at) }}
                            </span>
                        </div>

                        <!-- Details -->
                        <div v-if="isExpanded(log.id)"
                             :id="`email-log-details-${log.id}`"
                             class="pt-2 border-t border-dashed
                                    border-slate-400 dark:border-slate-500">
                            <dl class="space-y-2 text-[11px]">
                                <div>
                                    <dt class="font-semibold">UUID</dt>
                                    <dd class="break-all">{{ log.uuid || '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">
                                        {{ t('queue') }} / {{ t('postalTransport') }}
                                    </dt>
                                    <dd class="break-all">
                                        {{ log.queue || '—' }} / {{ log.mailer || '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">{{ t('inLine') }}</dt>
                                    <dd>{{ formatDate(log.queued_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">{{ t('processingStarted') }}</dt>
                                    <dd>{{ formatDate(log.processing_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">{{ t('sent') }}</dt>
                                    <dd>{{ formatDate(log.sent_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">{{ t('errorRecorded') }}</dt>
                                    <dd>{{ formatDate(log.failed_at) }}</dd>
                                </div>
                                <div v-if="log.error_type">
                                    <dt class="font-semibold">{{ t('errorType') }}</dt>
                                    <dd class="break-all">{{ log.error_type }}</dd>
                                </div>
                                <div v-if="log.error_message">
                                    <dt class="font-semibold">{{ t('description') }}</dt>
                                    <dd class="break-words whitespace-pre-wrap">
                                        {{ log.error_message }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Actions -->
                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400 dark:border-slate-500">
                        <button
                            type="button"
                            class="text-xs font-semibold text-blue-600 dark:text-blue-300
                                   hover:underline focus-visible:outline focus-visible:outline-2
                                   focus-visible:outline-blue-500 rounded-sm"
                            :aria-expanded="isExpanded(log.id)"
                            :aria-controls="`email-log-details-${log.id}`"
                            @click="toggleDetails(log.id)"
                        >
                            {{ isExpanded(log.id) ? t('hide') : t('readMore') }}
                        </button>
                    </footer>
                </article>
            </div>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
