<script setup>
/**
 * Редактирование попытки (QuizAttempt)
 * Контекст попытки + аудит (ip/user_agent) — readonly
 * percent не редактируем и не отправляем (считается на бэке)
 */
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'

import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    attempt: { type: Object, required: true } // QuizAttemptResource
})

/**
 * ---- helpers ----
 */
const toDatetimeLocal = (v) => {
    if (!v) return ''
    const d = new Date(v)
    if (!Number.isFinite(d.getTime())) return ''
    const pad = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const fromDatetimeLocal = (v) => {
    if (!v) return null
    const d = new Date(v)
    if (!Number.isFinite(d.getTime())) return null
    return d.toISOString()
}

const normalizeToEmptyString = (v) => (v === null || typeof v === 'undefined') ? '' : v

/**
 * ---- status map ----
 */
const statusMap = computed(() => ({
    in_progress: t('setStatusInProgress'),
    completed: t('setStatusCompleted'),
    graded: t('setStatusGraded')
}))

const statusOptions = computed(() => ([
    { value: 'in_progress', label: statusMap.value.in_progress },
    { value: 'completed', label: statusMap.value.completed },
    { value: 'graded', label: statusMap.value.graded }
]))

/**
 * ---- readonly display strings ----
 */
const userLabel = computed(() => {
    const u = props.attempt?.user || null
    if (!u) return props.attempt?.user_id ? `[ID: ${props.attempt.user_id}]` : '—'
    return `[ID: ${u.id}] • ${u.name || '—'} (${u.email || '—'})`
})

const quizLabel = computed(() => {
    const q = props.attempt?.quiz || null
    if (!q) return props.attempt?.quiz_id ? `[ID: ${props.attempt.quiz_id}]` : '—'
    return `[ID: ${q.id}] • ${q.title || q.slug || '—'} [${q.locale || '—'}]`
})

const enrollmentLabel = computed(() => {
    const e = props.attempt?.enrollment || null
    if (e?.id) return `[ID: ${e.id}]`
    return props.attempt?.enrollment_id ? `[ID: ${props.attempt.enrollment_id}]` : '—'
})

const courseLabel = computed(() => {
    const c = props.attempt?.course || null
    if (c?.id) return `[ID: ${c.id}] [${c.locale || '—'}] • ${c.title || '—'}`
    return props.attempt?.course_id ? `[ID: ${props.attempt.course_id}]` : '—'
})

const moduleLabel = computed(() => {
    const m = props.attempt?.module || null
    if (m?.id) return `[ID: ${m.id}] [${m.locale || '—'}] • ${m.title || '—'}`
    return props.attempt?.module_id ? `[ID: ${props.attempt.module_id}]` : '—'
})

const lessonLabel = computed(() => {
    const l = props.attempt?.lesson || null
    if (l?.id) return `[ID: ${l.id}] [${l.locale || '—'}] • ${l.title || '—'}`
    return props.attempt?.lesson_id ? `[ID: ${props.attempt.lesson_id}]` : '—'
})

const ipLabel = computed(() => props.attempt?.ip_address || '—')
const uaLabel = computed(() => props.attempt?.user_agent || '—')

/**
 * ---- form (редактируем только допустимые поля) ----
 * percent/ip/user_agent НЕ включаем
 */
const form = useForm({
    status: props.attempt?.status ?? 'in_progress',

    score: normalizeToEmptyString(props.attempt?.score),
    max_score: normalizeToEmptyString(props.attempt?.max_score),

    started_at: toDatetimeLocal(props.attempt?.started_at),
    finished_at: toDatetimeLocal(props.attempt?.finished_at),
    duration_seconds: normalizeToEmptyString(props.attempt?.duration_seconds)
})

/**
 * ---- submit ----
 */
const submitForm = () => {
    form.transform((data) => {
        const toNum = (v) => {
            if (v === '' || v === null || typeof v === 'undefined') return null
            const n = Number(v)
            return Number.isFinite(n) ? n : null
        }

        const payload = {
            status: data.status,
            started_at: fromDatetimeLocal(data.started_at),
            finished_at: fromDatetimeLocal(data.finished_at),
            score: toNum(data.score),
            max_score: toNum(data.max_score),
            duration_seconds: toNum(data.duration_seconds),
        }

        // ✅ убрать null-значения, чтобы не триггерить "sometimes"
        Object.keys(payload).forEach((k) => payload[k] === null && delete payload[k])

        return payload
    })

    form.put(route('admin.quizAttempts.update', { quizAttempt: props.attempt.id }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Попытка успешно обновлена!'),
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении попытки:', errors)
            const firstKey = Object.keys(errors || {})[0]
            toast.error(firstKey ? errors[firstKey] : 'Проверь поля формы.')
        }
    })
}
</script>

<template>
    <AdminLayout :title="t('editQuizAttempt')">
        <template #header>
            <TitlePage>
                {{ t('editQuizAttempt') }} ID:{{ attempt.id }}
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="px-4 pt-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.quizAttempts.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.2-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <!-- READONLY CONTEXT + AUDIT -->
                <div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300
                            bg-white/60 dark:bg-slate-800/40">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-sm">

                        <div class="space-y-3">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('user') }}:
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ userLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('ipAddress') }}:
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ ipLabel }}
                                </span>
                            </div>

                            <div class="lg:col-span-2">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('userAgent') }}:
                                </span>
                                <div class="text-slate-800 dark:text-slate-200 opacity-80
                                            break-words whitespace-pre-wrap mt-1">
                                    {{ uaLabel }}
                                </div>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('quizAttempts') }} :
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ attempt.percent ?? '—' }}{{ t('percent') }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('quiz') }}:
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ quizLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('course') }}: </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ courseLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('module') }}: </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ moduleLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('lesson') }}:
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ lessonLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('attemptNumber') }}:
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ attempt.attempt_number }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('enrollment') }}:
                                </span>
                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ enrollmentLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="pt-3 w-full">
                    <!-- Dates -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">

                        <div class="flex flex-col items-start">
                            <LabelInput for="duration_seconds" class="mb-1">{{ t('duration') }}</LabelInput>
                            <InputNumber id="duration_seconds" type="number" min="0" v-model="form.duration_seconds"
                                         class="w-full" />
                            <InputError class="mt-2" :message="form.errors.duration_seconds" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="started_at" class="mb-1">{{ t('shortStarted') }}</LabelInput>
                            <InputText id="started_at" type="datetime-local" v-model="form.started_at" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.started_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="finished_at" class="mb-1">{{ t('shortExpires') }}</LabelInput>
                            <InputText id="finished_at" type="datetime-local" v-model="form.finished_at"
                                       class="w-full" />
                            <InputError class="mt-2" :message="form.errors.finished_at" />
                        </div>

                    </div>

                    <!-- Score / Max / Status -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">

                        <div class="flex flex-col items-start">
                            <LabelInput for="score" class="mb-1">{{ t('score') }}</LabelInput>
                            <InputNumber id="score" type="number" min="0" v-model="form.score" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="max_score" class="mb-1">{{ t('maxScore') }}</LabelInput>
                            <InputNumber id="max_score" type="number" min="0" v-model="form.max_score" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.max_score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="status" class="mb-1">{{ t('status') }}</LabelInput>

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full py-0.5 font-semibold text-sm
                                   border border-slate-500 rounded-sm shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-300
                                   dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                    </div>

                    <!-- Save -->
                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.quizAttempts.index')" class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4 .7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.2-1.4-2.7l-2 .3c-.2 1.5 .9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton class="mb-0" :class="{ 'opacity-25': form.processing }"
                                       :disabled="form.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1 .7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
