<script setup>
import { defineProps, defineEmits, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    show: Boolean,
    comment: Object // Предполагается, что в этом объекте есть данные о пользователе, такие как user.name
})

const emits = defineEmits(['close'])

const closeModal = () => {
    emits('close')
}

// Закрытие модального окна по нажатию клавиши "Escape"
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        closeModal()
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape))

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date)
}

/** бейджи модерации */
const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 ' +
                'border-emerald-300 dark:bg-emerald-900/40 ' +
                'dark:text-emerald-300'
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 ' +
                'border-rose-300 dark:bg-rose-900/40 ' +
                'dark:text-rose-300'
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 ' +
            'border-amber-300 dark:bg-amber-900/40 ' +
            'dark:text-amber-300'
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition leave-active-class="duration-200">
            <div v-show="show"
                 class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
                 scroll-region>
                <!-- Фон модального окна -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="show" class="fixed inset-0 transform transition-all"
                         @click="closeModal">
                        <div class="absolute inset-0 bg-slate-800 opacity-25"></div>
                    </div>
                </Transition>

                <!-- Модальное окно -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div v-show="show"
                         class="bg-slate-100 dark:bg-slate-900
                                border border-gray-400
                                rounded-lg shadow-xl
                                transform transition-all
                                max-w-lg w-full max-h-full
                                sm:w-full sm:mx-auto
                                relative overflow-y-auto">

                        <!-- Кнопка закрытия в верхнем правом углу -->
                        <button @click="closeModal"
                                class="absolute top-0 right-1 m-1">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-6 w-6
                                       text-gray-400 hover:text-red-400
                                       dark:text-gray-300 dark:hover:text-red-300"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="px-3 py-1">
                            <h3 class="text-center text-md font-semibold
                                       text-sky-700 dark:text-sky-300
                                       pb-1 border-dashed border-b border-slate-400">
                                {{ t('commentDetails') }} - {{ t('id') }}: {{ comment?.id }}
                            </h3>

                            <!-- Отображение статуса модерации -->
                            <div v-if="comment"
                                 class="my-2 flex flex-row items-center justify-center gap-2">
                                <span class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('status') }}:
                                </span>
                                <span
                                    class="text-xs px-3 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(comment?.moderation_status).class"
                                    :title="comment?.moderation_note && comment?.moderated_at
                                ? `${comment.moderation_note} [${formatDate(comment.moderated_at)}]`
                                : null"
                                >
                                      {{ moderationBadge(comment?.moderation_status).text }}
                                    </span>
                            </div>

                            <!-- Отображение активности -->
                            <div v-if="comment"
                                 class="my-2 flex flex-row items-center justify-start gap-1">
                                <span class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('activity') }}:
                                </span>
                                <p class="font-semibold text-sm
                                          text-rose-600 dark:text-rose-400">
                                    {{ comment.activity ? t('active') : t('inactive') }}
                                </p>
                            </div>

                            <!-- Проверка на наличие пользователя и его имени -->
                            <div v-if="comment?.user"
                                 class="my-2">
                                <span class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('userCommented') }}
                                </span>
                                <div class="flex flex-row items-center justify-start gap-1
                                            text-sm font-semibold">
                                    <div class="text-slate-600 dark:text-slate-400">
                                        [ID: {{ comment.user_id }}]
                                    </div>
                                    <div class="text-blue-700 dark:text-blue-300">
                                        {{ comment.user?.name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Отображение комментария -->
                            <div v-if="comment" class="my-2">
                                <span class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('comment') }} :
                                </span>
                                <p class="font-semibold text-sm
                                          text-amber-700 dark:text-amber-300">
                                    {{ comment.content }}
                                </p>
                            </div>

                            <!-- Комментируемая модель -->
                            <div v-if="comment?.commentable_type" class="my-2">
                                <div class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('object') }} :
                                </div>
                                <div class="flex flex-row items-center justify-start gap-1
                                            text-sm font-semibold">
                                    <div v-if="comment?.commentable_id"
                                         class="text-slate-600 dark:text-slate-400">
                                        [{{ t('id') }}: {{ comment.commentable_id }}]
                                    </div>
                                    <div v-if="comment?.commentable_type"
                                        class="text-indigo-700 dark:text-indigo-300">
                                        [{{ (comment?.commentable_type || '').split('\\').pop() }}]
                                    </div>
                                    <!-- название  -->
                                    <div v-if="comment?.commentable_title"
                                         class="text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ comment.commentable_title }}
                                    </div>
                                </div>
                            </div>

                            <!-- Дата создания -->
                            <div v-if="comment"
                                 class="my-2 flex flex-row items-end justify-start gap-1">
                                <span class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('createdAt') }}:
                                </span>
                                <p class="font-semibold text-xs italic
                                          text-slate-600 dark:text-slate-400">
                                    {{ formatDate(comment.created_at) }}
                                </p>
                            </div>

                            <!-- Дата обновления -->
                            <div v-if="comment"
                                 class="my-2 flex flex-row items-end justify-start gap-1">
                                <span class="font-semibold text-sm
                                             text-gray-900 dark:text-gray-100">
                                    {{ t('updatedAt') }}:
                                </span>
                                <p class="font-semibold text-xs italic
                                          text-slate-600 dark:text-slate-400">
                                    {{ formatDate(comment.updated_at) }}
                                </p>
                            </div>

                            <!-- Кнопка закрытия в нижней части -->
                            <div class="my-1 sm:flex sm:flex-row-reverse space-x-2">
                                <button type="button"
                                        class="flex justify-center items-center float-right
                                               rounded-xs border border-transparent
                                               shadow-sm px-2 py-0.5
                                               bg-indigo-600
                                               text-base font-medium text-white
                                               hover:bg-indigo-700
                                               focus:outline-none focus:ring-2
                                               focus:ring-offset-2 focus:ring-indigo-500
                                               sm:w-auto sm:text-sm"
                                        @click="closeModal">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 text-gray-100"
                                         fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="ml-1">{{ t('close') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
