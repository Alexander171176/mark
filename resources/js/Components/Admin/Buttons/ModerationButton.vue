<script setup>
import { computed, ref, nextTick, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/** входящие пропсы */
const props = defineProps({
    status: { type: [Number, String], default: 0 },
    isAdmin: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },

    /** toggle: 1 <-> 2 (0 тоже уходит в 1), cycle: 0->1->2->0 */
    mode: { type: String, default: 'toggle' },

    /** размеры и лимит текста */
    size: { type: String, default: 'xs' },
    noteMax: { type: Number, default: 500 },

    /** если хочешь подставлять существующую заметку */
    initialNote: { type: String, default: '' },

    /** можно включить обязательность note */
    noteRequired: { type: Boolean, default: false },
})

/**
 * emit:
 *  - submit: { status, note } — только после подтверждения в модалке
 *  - cancel: если закрыли
 */
const emit = defineEmits(['submit', 'cancel'])

/** наблюдатель статуса */
const s = computed(() => Number(props.status ?? 0))

/** перевод статусов */
const state = computed(() => {
    if (s.value === 1) return 'statusSelectApproved'
    if (s.value === 2) return 'statusSelectRejected'
    return 'underModeration'
})

const title = computed(() => t(state.value))

/** кнопка статуса */
const btnClass = computed(() => {
    if (s.value === 1) return 'bg-emerald-500 hover:bg-emerald-600 text-white border-gray-400'
    if (s.value === 2) return 'bg-rose-500 hover:bg-rose-600 text-white border-gray-400'
    return 'bg-yellow-300 hover:bg-yellow-400 text-slate-600 border-gray-400'
})

/** классы статусов */
const sizeClass = computed(() => (props.size === 'xs' ? 'px-1 py-1' : 'px-2 py-1'))
const iconClass = computed(() => (props.size === 'xs' ? 'w-3.5 h-3.5' : 'w-4 h-4'))

/** переключение статуса */
const nextStatus = () => {
    if (props.mode === 'cycle') return s.value === 0 ? 1 : (s.value === 1 ? 2 : 0)
    return s.value === 1 ? 2 : 1
}

/** modal state */
const show = ref(false)
const pendingStatus = ref(1)
const note = ref('')

/** состояние фокуса текстового поля */
const noteEl = ref(null)

/** набор клавиатурных сочетаний */
const onKeydown = (e) => {
    if (!show.value) return

    // Esc — закрыть
    if (e.key === 'Escape') {
        e.preventDefault()
        close()
        return
    }

    // Ctrl + Enter (или Cmd + Enter на Mac) — сохранить
    const isSubmit = e.key === 'Enter' && (e.ctrlKey || e.metaKey)

    if (isSubmit) {
        e.preventDefault()
        submit()
    }
}

/** наблюдатель клавиатурных сочетаний */
onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown)
})

/** фокус текстового поля, инициализация клавиатурных сочетаний */
const open = async (e) => {
    e?.stopPropagation?.()
    if (!props.isAdmin || props.disabled) return

    pendingStatus.value = nextStatus()
    note.value = (props.initialNote || '').toString().slice(0, props.noteMax)
    show.value = true

    window.addEventListener('keydown', onKeydown)

    await nextTick()
    noteEl.value?.focus?.()
}

/** ESC закрыть */
const close = () => {
    show.value = false
    window.removeEventListener('keydown', onKeydown)
    emit('cancel')
}

const canSubmit = computed(() => {
    const n = (note.value || '').trim()
    if ((note.value || '').length > props.noteMax) return false
    if (props.noteRequired && !n.length) return false
    return true
})

/** Сохранить */
const submit = () => {
    if (!canSubmit.value) return
    emit('submit', { status: pendingStatus.value, note: (note.value || '').trim() })
    show.value = false
    window.removeEventListener('keydown', onKeydown)
}

</script>

<template>
    <!-- кнопка -->
    <button
        v-if="isAdmin"
        type="button"
        class="rounded-sm border inline-flex items-center justify-center"
        :class="[sizeClass, btnClass, disabled && 'opacity-60 cursor-not-allowed']"
        :title="title"
        :disabled="disabled"
        @click="open"
    >
        <!-- underModeration -->
        <svg v-if="state === 'underModeration'"
             :class="iconClass" viewBox="0 0 128 128" fill="currentColor">
            <path
                d="M72.8,65c4.3,0,7.7,3.5,7.5,7.8c-0.2,4.1-3.7,7.2-7.8,7.2H53.1c-2.1,0-3.8-0.6-5.1-2c-1.4-1.4-2-3.1-2-5.1V44.2c0-4.1,2.9-7.6,7-7.8c4.3-0.2,7.7,3.2,7.7,7.5l0,14.8c0,3.6,2.9,6.3,6.5,6.3H72.8z M126,48.1c-2.2-1.9-5.7-1.7-7.5,0.6l-3.3,4.1C110.2,26,86.6,5.5,58.3,5.5C26.4,5.5,0.5,31.5,0.5,63.4s25.9,57.8,57.8,57.8c19.2,0,37.2-9.5,47.9-25.5c2-3,1.2-7-1.7-9c-3-2-7-1.2-9,1.7c-8.4,12.4-22.3,19.8-37.2,19.8c-24.7,0-44.8-20.1-44.8-44.8c0-24.7,20.1-44.8,44.8-44.8c21.9,0,40.1,15.8,44,36.5l-5-4c-2.3-1.8-5.7-1.3-7.4,1.1c-1.6,2.3-1.1,5.5,1.1,7.2l15.7,12.4c1.1,0.9,2.7,1.3,4,1.1c1.3-0.2,2.7-0.8,3.6-1.9l12.4-15.7C128.4,53.2,128.1,49.9,126,48.1z" />
        </svg>

        <!-- approved -->
        <svg v-else-if="state === 'statusSelectApproved'"
             :class="iconClass" viewBox="0 0 1000 1000"
             fill="currentColor">
            <path
                d="M124.5,972.6h683.1c66.2,0,120-53.8,120-120V385.5l-120,120v347.1l-683.1,0v-683h547.3l96.3-96.3c9.1-9.1,19.1-17,29.8-23.7H124.5c-66.2,0-120,53.8-120,120v683.1C4.5,918.8,58.3,972.6,124.5,972.6z" />
            <path
                d="M962.7,116.9c-19.4-20.1-44.8-30.8-70.5-32.3c-2.1-0.1-4.1-0.2-6.2-0.2c-27.3,0-54.6,10.4-75.5,31.3l-334,334c-11.3-12.1-81.1-82.4-81.1-82.4c-20.8-20.8-48.2-31.3-75.5-31.3c-27.3,0-54.6,10.4-75.5,31.3c-41.7,41.7-41.7,109.3,0,151l81.8,81.8l75.5,75.5c20,20,47.2,31.3,75.5,31.3s55.5-11.2,75.5-31.3l406.2-406.2C1000.5,227.7,1003.7,159.2,962.7,116.9z" />
        </svg>

        <!-- rejected -->
        <svg v-else :class="iconClass" viewBox="0 0 128 128" fill="currentColor">
            <path
                d="M11.6,119H116c7.3,0,12-7.7,8.7-14.2L72.5,12.3c-3.6-7.2-13.9-7.2-17.5,0L2.8,104.8C-0.4,111.3,4.3,119,11.6,119z M72,97.5c0,4.8-3.5,8.5-8.5,8.5s-8.5-3.7-8.5-8.5v-0.2c0-4.8,3.5-8.5,8.5-8.5s8.5,3.7,8.5,8.5V97.5z M58.9,35h9.8c2.7,0,4.3,2.3,4,5.2l-4.3,37.6c-0.3,2.7-2.1,4.4-4.6,4.4s-4.3-1.7-4.6-4.4l-4.3-37.6C54.6,37.3,56.2,35,58.9,35z" />
        </svg>
    </button>

    <!-- ✅ модалка внутри кнопки -->
    <teleport to="body">
        <div v-if="show" class="fixed inset-0 z-[9999]">
            <div class="absolute inset-0 bg-black/50" @click="close"></div>

            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div
                    class="w-full max-w-sm rounded-md bg-white dark:bg-slate-800
                           border border-slate-500 dark:border-slate-400 shadow-xl">
                    <div class="p-4 border-b border-slate-200 dark:border-slate-700
                                flex items-center justify-center gap-3">
                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ t('moderationNoteTitle') }}
                        </div>
                        <div class="text-sm font-semibold text-orange-700 dark:text-orange-300">
                            {{ pendingStatus === 1 ? t('statusSelectApproved') : t('statusSelectRejected') }}
                        </div>
                    </div>

                    <div class="p-4">
                        <label class="block text-center text-md font-semibold
                                      text-blue-900 dark:text-blue-100 mb-2">
                            {{ t('moderationNoteLabel') }}
                        </label>

                        <textarea
                            ref="noteEl"
                            v-model="note"
                            rows="5"
                            :maxlength="noteMax"
                            class="w-full rounded-sm border border-slate-500 dark:border-slate-400
                                   bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300
                                   text-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                   font-semibold"
                            :placeholder="t('moderationNotePlaceholder')"
                        />

                        <div
                            class="mt-2 flex items-center justify-between
                                   text-[11px] text-slate-700 dark:text-slate-300">
                              <span v-if="noteRequired" class="text-rose-700 dark:text-rose-300">
                                {{ t('required') }}
                              </span>
                            <span class="ml-auto">{{ (note || '').length }}/{{ noteMax }}</span>
                        </div>
                    </div>

                    <div
                        class="p-4 border-t border-slate-200 dark:border-slate-700
                               flex items-center justify-end gap-2">
                        <button
                            type="button"
                            class="px-3 py-1 rounded-sm
                                   border border-slate-500 dark:border-slate-400
                                   hover:bg-slate-200 dark:hover:bg-slate-900
                                   flex items-center justify-center gap-1"
                            @click="close"
                        >
                            <svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24">
                                <path class="fill-current text-slate-700 dark:text-slate-300"
                                      d="M23.625,5.219l-5-4A1,1,0,0,0,17,2V5H4A3,3,0,0,0,1,8v3a1,1,0,0,0,2,0V8A1,1,0,0,1,4,7H17v3a1,1,0,0,0,1.625.781l5-4a1,1,0,0,0,0-1.562Z"></path>
                                <path class="fill-current text-slate-700 dark:text-slate-300"
                                      d="M22,12a1,1,0,0,0-1,1v3a1,1,0,0,1-1,1H7V14a1,1,0,0,0-1.625-.781l-5,4a1,1,0,0,0,0,1.562l5,4A1,1,0,0,0,7,22V19H20a3,3,0,0,0,3-3V13A1,1,0,0,0,22,12Z"></path>
                            </svg>
                            <span class="text-sm text-slate-700 dark:text-slate-100">
                                {{ t('cancel') }}
                            </span>
                        </button>

                        <button
                            type="button"
                            class="px-3 py-1 rounded-sm border border-blue-600
                                   bg-blue-600 hover:bg-blue-700
                                   flex items-center justify-center gap-1"
                            :disabled="!canSubmit"
                            @click="submit"
                        >
                            <svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24">
                                <path class="fill-current text-slate-100"
                                      d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
                            </svg>
                            <span class="text-sm text-white">{{ t('save') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>
