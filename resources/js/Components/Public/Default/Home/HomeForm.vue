<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import PublicForm from '@/Components/Public/Default/Form/PublicForm.vue'

const { t } = useI18n()

const props = defineProps({
    show: { type: Boolean, default: false },
    form: { type: Object, default: null },
})

const emit = defineEmits(['close'])

/** Новый экземпляр формы после успешной отправки. */
const formKey = ref(0)

const close = () => emit('close')

const handleSuccess = () => {
    formKey.value++
    close()
}

const handleKeydown = (event) => {
    if (event.key === 'Escape' && props.show) close()
}

/** Сохраняем исходное значение overflow, чтобы восстановить его при закрытии. */
let previousBodyOverflow = ''
let scrollLocked = false

const updateBodyScroll = (show) => {
    if (show && !scrollLocked) {
        previousBodyOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
        scrollLocked = true
    } else if (!show && scrollLocked) {
        document.body.style.overflow = previousBodyOverflow
        scrollLocked = false
    }
}

watch(() => props.show, updateBodyScroll)

onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
    updateBodyScroll(props.show)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown)
    updateBodyScroll(false)
})
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show && form"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="`home-form-title-${form.id}`"
            >
                <!-- Затемнение -->
                <div
                    class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm"
                    @click="close"
                />

                <!-- Модальное окно: шапка и подвал вне прокрутки -->
                <div
                    class="relative z-10 flex max-h-[calc(100dvh-2rem)] w-full max-w-3xl
                           flex-col overflow-hidden rounded-2xl bg-white shadow-sm
                           shadow-slate-200 dark:bg-gray-800 dark:shadow-slate-400
                           sm:max-h-[calc(100dvh-3rem)]"
                    @click.stop
                >
                    <!-- Неподвижная шапка -->
                    <div
                        class="grid shrink-0 grid-cols-[2.25rem_minmax(0,1fr)_2.25rem]
                               items-start gap-4 border-b border-gray-200 px-3 py-2
                               dark:border-gray-700"
                    >
                        <div aria-hidden="true" />

                        <div class="min-w-0 text-center">
                            <h2
                                :id="`home-form-title-${form.id}`"
                                class="text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ form.translation?.title }}
                            </h2>
                            <p
                                v-if="form.translation?.subtitle"
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ form.translation.subtitle }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="flex h-9 w-9 shrink-0 items-center justify-center
                                   rounded-lg text-gray-500 transition hover:bg-gray-200
                                   hover:text-red-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500
                                   dark:text-gray-400 dark:hover:bg-gray-700
                                   dark:hover:text-white"
                            :aria-label="t('close')"
                            @click="close"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- PublicForm сам управляет прокруткой и подвалом -->
                    <div class="flex min-h-0 flex-1 flex-col">
                        <PublicForm
                            :key="formKey"
                            :form="form"
                            :description="form.translation?.description || ''"
                            @success="handleSuccess"
                        />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
