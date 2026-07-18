<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Модальное окно подробной информации об универсальном отзыве.
 */

import { computed, onMounted, onUnmounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/** Входящие данные модального окна. */
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },

    review: {
        type: Object,
        default: null,
    },
})

/** Событие закрытия модального окна. */
const emit = defineEmits(['close'])

/** Закрытие модального окна. */
const closeModal = () => {
    emit('close')
}

/** Закрытие окна клавишей Escape. */
const closeOnEscape = (event) => {
    if (event.key === 'Escape' && props.show) {
        closeModal()
    }
}

/** Блокировка прокрутки страницы под открытым окном. */
watch(
    () => props.show,
    (show) => {
        document.body.style.overflow = show ? 'hidden' : ''
    },
)

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape)
    document.body.style.overflow = ''
})

/** Форматирование даты и времени. */
const formatDate = (dateString) => {
    if (!dateString) return '—'

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return new Intl.DateTimeFormat('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** Отображаемый тип полиморфной сущности. */
const reviewableType = computed(() => {
    return props.review?.reviewable?.type
        || props.review?.reviewable_type
        || '—'
})

/** Название связанной сущности. */
const reviewableTitle = computed(() => {
    return props.review?.reviewable?.title
        || props.review?.reviewable?.name
        || props.review?.reviewable?.url
        || '—'
})

/** Изображения отзыва. */
const images = computed(() => {
    return Array.isArray(props.review?.images)
        ? props.review.images
        : []
})

/** Наличие ответа на отзыв. */
const hasReply = computed(() => {
    return Boolean(
        props.review?.has_reply
        || String(props.review?.reply || '').trim()
    )
})

/** Бейдж статуса модерации. */
const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/** Бейдж активности. */
const activityBadge = (activity) => {
    return activity
        ? {
            text: t('active'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
        : {
            text: t('inactive'),
            class:
                'bg-slate-100 text-slate-600 border-slate-300 ' +
                'dark:bg-slate-700 dark:text-slate-300',
        }
}

/** Бейдж подтверждённого опыта. */
const verifiedBadge = (verified) => {
    return verified
        ? {
            text: t('verified'),
            class:
                'bg-blue-100 text-blue-700 border-blue-300 ' +
                'dark:bg-blue-900/40 dark:text-blue-300',
        }
        : {
            text: t('notVerified'),
            class:
                'bg-slate-100 text-slate-600 border-slate-300 ' +
                'dark:bg-slate-700 dark:text-slate-300',
        }
}

/** URL изображения с учётом доступных форматов ресурса. */
const imageUrl = (image) => {
    return image?.webp_url
        || image?.url
        || image?.thumb_url
        || ''
}
</script>

<template>
    <Teleport to="body">
        <Transition leave-active-class="duration-200">
            <div
                v-show="show"
                class="fixed inset-0 z-50 flex items-center justify-center
                       overflow-y-auto px-3 py-6"
                scroll-region
                role="dialog"
                aria-modal="true"
            >
                <!-- Затемнение фона -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="show"
                        class="fixed inset-0 transform transition-all"
                        @click="closeModal"
                    >
                        <div class="absolute inset-0 bg-slate-900/50"></div>
                    </div>
                </Transition>

                <!-- Содержимое модального окна -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="show"
                        class="relative w-full max-w-4xl max-h-[90vh]
                               overflow-y-auto rounded-lg
                               border border-gray-400
                               bg-slate-100 dark:bg-slate-900
                               shadow-xl transform transition-all"
                        @click.stop
                    >
                        <!-- Кнопка закрытия -->
                        <button
                            type="button"
                            class="absolute top-1 right-1 z-10 rounded-sm p-1
                                   text-gray-400 hover:text-red-500
                                   dark:text-gray-300 dark:hover:text-red-300"
                            :title="t('close')"
                            @click="closeModal"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>

                        <div class="px-4 py-3 sm:px-6">
                            <!-- Заголовок -->
                            <h3
                                class="pr-8 pb-2 text-center text-base font-semibold
                                       text-sky-700 dark:text-sky-300
                                       border-b border-dashed border-slate-400"
                            >
                                {{ t('reviewDetails') }}
                                —
                                {{ t('id') }}: {{ review?.id }}
                            </h3>

                            <template v-if="review">
                                <!-- Основные статусы -->
                                <div class="my-4 flex flex-wrap items-center justify-center gap-2">
                                    <span
                                        class="rounded-sm border px-3 py-1
                                               text-xs font-semibold"
                                        :class="moderationBadge(review.moderation_status).class"
                                        :title="review.moderation_note
                                            ? `${review.moderation_note}${review.moderated_at
                                                ? ` [${formatDate(review.moderated_at)}]`
                                                : ''}`
                                            : null"
                                    >
                                        {{ moderationBadge(review.moderation_status).text }}
                                    </span>

                                    <span
                                        class="rounded-sm border px-3 py-1
                                               text-xs font-semibold"
                                        :class="activityBadge(review.activity).class"
                                    >
                                        {{ activityBadge(review.activity).text }}
                                    </span>

                                    <span
                                        class="rounded-sm border px-3 py-1
                                               text-xs font-semibold"
                                        :class="verifiedBadge(review.verified).class"
                                    >
                                        {{ verifiedBadge(review.verified).text }}
                                    </span>

                                    <span
                                        class="rounded-sm border border-amber-300
                                               bg-amber-100 px-3 py-1
                                               text-xs font-bold text-amber-700
                                               dark:bg-amber-900/40 dark:text-amber-300"
                                    >
                                        ★ {{ Number(review.rating || 0) }}/5
                                    </span>
                                </div>

                                <!-- Автор и показатели -->
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <section
                                        class="rounded-sm border border-slate-300
                                               bg-white p-3 dark:border-slate-600
                                               dark:bg-slate-800"
                                    >
                                        <h4
                                            class="mb-2 text-sm font-semibold
                                                   text-slate-900 dark:text-slate-100"
                                        >
                                            {{ t('author') }}
                                        </h4>

                                        <div class="space-y-1 text-sm">
                                            <div>
                                                <span class="font-semibold">
                                                    {{ t('id') }}:
                                                </span>
                                                {{ review.author?.id || review.user_id || '—' }}
                                            </div>

                                            <div>
                                                <span class="font-semibold">
                                                    {{ t('name') }}:
                                                </span>
                                                <span class="text-blue-700 dark:text-blue-300">
                                                    {{ review.author?.name || '—' }}
                                                </span>
                                            </div>

                                            <div>
                                                <span class="font-semibold">Email:</span>
                                                <span class="break-all">
                                                    {{ review.author?.email || '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    </section>

                                    <section
                                        class="rounded-sm border border-slate-300
                                               bg-white p-3 dark:border-slate-600
                                               dark:bg-slate-800"
                                    >
                                        <h4
                                            class="mb-2 text-sm font-semibold
                                                   text-slate-900 dark:text-slate-100"
                                        >
                                            {{ t('statistics') }}
                                        </h4>

                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <span class="font-semibold">
                                                    {{ t('likes') }}:
                                                </span>
                                                {{ Number(review.likes || 0) }}
                                            </div>

                                            <div>
                                                <span class="font-semibold">
                                                    {{ t('images') }}:
                                                </span>
                                                {{ Number(review.images_count ?? images.length) }}
                                            </div>

                                            <div>
                                                <span class="font-semibold">
                                                    {{ t('reply') }}:
                                                </span>
                                                {{ hasReply ? t('yes') : t('no') }}
                                            </div>

                                            <div>
                                                <span class="font-semibold">
                                                    {{ t('rating') }}:
                                                </span>
                                                {{ Number(review.rating || 0) }}
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Связанная сущность -->
                                <section
                                    class="mt-3 rounded-sm border border-slate-300
                                           bg-white p-3 dark:border-slate-600
                                           dark:bg-slate-800"
                                >
                                    <h4
                                        class="mb-2 text-sm font-semibold
                                               text-slate-900 dark:text-slate-100"
                                    >
                                        {{ t('object') }}
                                    </h4>

                                    <div class="flex flex-wrap items-center gap-2 text-sm font-semibold">
                                        <span class="text-slate-600 dark:text-slate-300">
                                            [{{ t('id') }}:
                                            {{ review.reviewable?.id || review.reviewable_id }}]
                                        </span>

                                        <span class="text-indigo-700 dark:text-indigo-300">
                                            [{{ reviewableType }}]
                                        </span>

                                        <span class="text-fuchsia-700 dark:text-fuchsia-300">
                                            {{ reviewableTitle }}
                                        </span>
                                    </div>

                                    <div
                                        v-if="review.reviewable?.sku
                                            || review.reviewable?.code
                                            || review.reviewable?.url
                                            || review.reviewable?.slug"
                                        class="mt-2 grid grid-cols-1 gap-1 text-xs
                                               text-slate-500 dark:text-slate-300 sm:grid-cols-2"
                                    >
                                        <div v-if="review.reviewable?.sku">
                                            SKU: {{ review.reviewable.sku }}
                                        </div>

                                        <div v-if="review.reviewable?.code">
                                            {{ t('code') }}: {{ review.reviewable.code }}
                                        </div>

                                        <div v-if="review.reviewable?.url" class="break-all">
                                            URL: {{ review.reviewable.url }}
                                        </div>

                                        <div v-if="review.reviewable?.slug" class="break-all">
                                            Slug: {{ review.reviewable.slug }}
                                        </div>
                                    </div>
                                </section>

                                <!-- Содержимое отзыва -->
                                <div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-2">
                                    <section
                                        v-if="review.advantages"
                                        class="rounded-sm border border-emerald-300
                                               bg-emerald-50 p-3
                                               dark:border-emerald-700
                                               dark:bg-emerald-950/30"
                                    >
                                        <h4
                                            class="mb-1 text-sm font-semibold
                                                   text-emerald-700 dark:text-emerald-300"
                                        >
                                            {{ t('advantages') }}
                                        </h4>

                                        <p
                                            class="whitespace-pre-wrap break-words
                                                   text-sm text-slate-700
                                                   dark:text-slate-200"
                                        >
                                            {{ review.advantages }}
                                        </p>
                                    </section>

                                    <section
                                        v-if="review.disadvantages"
                                        class="rounded-sm border border-rose-300
                                               bg-rose-50 p-3
                                               dark:border-rose-700
                                               dark:bg-rose-950/30"
                                    >
                                        <h4
                                            class="mb-1 text-sm font-semibold
                                                   text-rose-700 dark:text-rose-300"
                                        >
                                            {{ t('disadvantages') }}
                                        </h4>

                                        <p
                                            class="whitespace-pre-wrap break-words
                                                   text-sm text-slate-700
                                                   dark:text-slate-200"
                                        >
                                            {{ review.disadvantages }}
                                        </p>
                                    </section>
                                </div>

                                <section
                                    class="mt-3 rounded-sm border border-amber-300
                                           bg-amber-50 p-3 dark:border-amber-700
                                           dark:bg-amber-950/30"
                                >
                                    <h4
                                        class="mb-1 text-sm font-semibold
                                               text-amber-700 dark:text-amber-300"
                                    >
                                        {{ t('review') }}
                                    </h4>

                                    <p
                                        class="whitespace-pre-wrap break-words
                                               text-sm font-semibold
                                               text-slate-700 dark:text-slate-200"
                                    >
                                        {{ review.comment || '—' }}
                                    </p>
                                </section>

                                <!-- Ответ -->
                                <section
                                    v-if="hasReply"
                                    class="mt-3 rounded-sm border border-blue-300
                                           bg-blue-50 p-3 dark:border-blue-700
                                           dark:bg-blue-950/30"
                                >
                                    <h4
                                        class="mb-1 text-sm font-semibold
                                               text-blue-700 dark:text-blue-300"
                                    >
                                        {{ t('reply') }}
                                    </h4>

                                    <p
                                        class="whitespace-pre-wrap break-words
                                               text-sm text-slate-700
                                               dark:text-slate-200"
                                    >
                                        {{ review.reply }}
                                    </p>

                                    <div
                                        class="mt-2 flex flex-wrap gap-x-4 gap-y-1
                                               text-xs text-slate-500
                                               dark:text-slate-300"
                                    >
                                        <span v-if="review.replier">
                                            {{ review.replier.name || '—' }}
                                            [ID: {{ review.replier.id }}]
                                        </span>

                                        <span v-if="review.replier?.email">
                                            {{ review.replier.email }}
                                        </span>

                                        <span v-if="review.replied_at">
                                            {{ formatDate(review.replied_at) }}
                                        </span>
                                    </div>
                                </section>

                                <!-- Галерея изображений отзыва -->
                                <section
                                    v-if="images.length"
                                    class="mt-3 rounded-sm border border-slate-300 bg-white p-3
                                           dark:border-slate-600 dark:bg-slate-800"
                                >
                                    <h4 class="mb-3 text-sm font-semibold
                                               text-slate-900 dark:text-slate-100">
                                        {{ t('reviewImages') }}: {{ images.length }}
                                    </h4>

                                    <div class="grid grid-cols-1 gap-3
                                                sm:grid-cols-2 lg:grid-cols-3">
                                        <a
                                            v-for="image in images"
                                            :key="image.id"
                                            :href="imageUrl(image)"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="group overflow-hidden rounded-sm
                                                   border border-slate-300 dark:border-slate-600"
                                        >
                                            <img
                                                :src="imageUrl(image)"
                                                :alt="image.alt || ''"
                                                class="h-44 w-full object-cover transition-transform
                                                       duration-200 group-hover:scale-105"
                                            >

                                            <div v-if="image.caption || image.alt"
                                                 class="space-y-1 p-2 text-xs
                                                        text-slate-600 dark:text-slate-300">
                                                <div v-if="image.caption">
                                                    {{ image.caption }}
                                                </div>

                                                <div v-if="image.alt"
                                                     class="break-words text-[10px]">
                                                    Alt: {{ image.alt }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </section>

                                <!-- Модерация -->
                                <section
                                    v-if="review.moderation_note
                                        || review.moderator
                                        || review.moderated_at"
                                    class="mt-3 rounded-sm border border-slate-300
                                           bg-white p-3 dark:border-slate-600
                                           dark:bg-slate-800"
                                >
                                    <h4
                                        class="mb-2 text-sm font-semibold
                                               text-slate-900 dark:text-slate-100"
                                    >
                                        {{ t('moderation') }}
                                    </h4>

                                    <div class="space-y-1 text-sm">
                                        <div v-if="review.moderation_note">
                                            <span class="font-semibold">
                                                {{ t('moderationNote') }}:
                                            </span>
                                            <span class="whitespace-pre-wrap break-words">
                                                {{ review.moderation_note }}
                                            </span>
                                        </div>

                                        <div v-if="review.moderator">
                                            <span class="font-semibold">
                                                {{ t('moderator') }}:
                                            </span>
                                            {{ review.moderator.name || '—' }}
                                            [ID: {{ review.moderator.id }}]
                                            <span v-if="review.moderator.email">
                                                — {{ review.moderator.email }}
                                            </span>
                                        </div>

                                        <div v-if="review.moderated_at">
                                            <span class="font-semibold">
                                                {{ t('moderatedAt') }}:
                                            </span>
                                            {{ formatDate(review.moderated_at) }}
                                        </div>
                                    </div>
                                </section>

                                <!-- Даты -->
                                <div
                                    class="mt-3 flex flex-col gap-1
                                           border-t border-dashed border-slate-400
                                           pt-3 text-xs italic
                                           text-slate-600 dark:text-slate-400
                                           sm:flex-row sm:justify-between"
                                >
                                    <div>
                                        <span class="font-semibold not-italic">
                                            {{ t('createdAt') }}:
                                        </span>
                                        {{ formatDate(review.created_at) }}
                                    </div>

                                    <div>
                                        <span class="font-semibold not-italic">
                                            {{ t('updatedAt') }}:
                                        </span>
                                        {{ formatDate(review.updated_at) }}
                                    </div>
                                </div>
                            </template>

                            <div
                                v-else
                                class="py-8 text-center text-sm
                                       text-slate-500 dark:text-slate-300"
                            >
                                {{ t('noData') }}
                            </div>

                            <!-- Нижняя кнопка закрытия -->
                            <div class="mt-4 flex justify-center">
                                <button
                                    type="button"
                                    class="flex items-center gap-1
                                           rounded-sm bg-slate-600 px-3 py-1
                                           text-sm font-semibold text-white
                                           hover:bg-slate-700
                                           dark:bg-slate-500
                                           dark:hover:bg-slate-400"
                                    @click="closeModal"
                                >
                                    <svg class="h-5 w-5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>{{ t('close') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
