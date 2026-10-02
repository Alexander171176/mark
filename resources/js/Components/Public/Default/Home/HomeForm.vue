<script setup>
import {
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue'
import PublicForm from '@/Components/Public/Default/Form/PublicForm.vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },

    form: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits([
    'close',
])

/**
 * Ключ экземпляра публичной формы.
 *
 * После успешной отправки создаём
 * новый экземпляр PublicForm,
 * чтобы при следующем открытии получить
 * полностью начальное состояние.
 */
const formKey = ref(0)

/**
 * Закрытие модального окна.
 */
const close = () => {
    emit('close')
}

/**
 * Успешная отправка формы.
 */
const handleSuccess = () => {
    formKey.value++

    close()
}

/**
 * Закрытие по клавише Escape.
 */
const handleKeydown = (event) => {
    if (event.key === 'Escape' && props.show) {
        close()
    }
}

/**
 * Блокировка прокрутки страницы
 * при открытом модальном окне.
 */
const updateBodyScroll = (show) => {
    document.body.style.overflow = show
        ? 'hidden'
        : ''
}

watch(
    () => props.show,
    (show) => {
        updateBodyScroll(show)
    }
)

onMounted(() => {
    window.addEventListener(
        'keydown',
        handleKeydown
    )

    if (props.show) {
        updateBodyScroll(true)
    }
})

onBeforeUnmount(() => {
    window.removeEventListener(
        'keydown',
        handleKeydown
    )

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

                <!-- Модальное окно -->
                <div
                    class="relative z-10 flex max-h-[calc(100vh-2rem)] w-full max-w-3xl
                           flex-col overflow-hidden rounded-2xl bg-white shadow-sm
                           shadow-slate-200 dark:shadow-slate-400
                           dark:bg-gray-800 sm:max-h-[calc(100vh-3rem)]"
                    @click.stop
                >

                    <!-- Заголовок -->
                    <div
                        class="flex items-start justify-between gap-4
                               border-b border-gray-200 px-3 py-2
                               dark:border-gray-700"
                    >
                        <div></div>
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

                        <!-- Закрыть -->
                        <button
                            type="button"
                            class="flex h-9 w-9 shrink-0 items-center justify-center
                                   rounded-lg text-gray-500 transition hover:bg-gray-200
                                   hover:text-red-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500
                                   dark:text-gray-400 dark:hover:bg-gray-700
                                   dark:hover:text-white"
                            aria-label="Закрыть"
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

                    <!-- Содержимое -->
                    <div class="overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
                        <p
                            v-if="form.translation?.description"
                            class="mb-6 text-sm leading-6 text-gray-600 dark:text-gray-300"
                        >
                            {{ form.translation.description }}
                        </p>

                        <!-- Содержимое -->
                        <div class="overflow-y-auto">
                            <PublicForm
                                :key="formKey"
                                :form="form"
                                @success="handleSuccess"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
