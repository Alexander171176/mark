<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

/** Входящие данные компонента. */
const props = defineProps({
    reviews: { type: Array, default: () => [] },
    selectedReviews: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

/** События родительской страницы. */
const emit = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'toggle-all',
    'view-details',
    'approve-review',
])

/** Все отзывы текущей страницы выбраны. */
const allSelected = computed(() => {
    return props.reviews.length > 0 && props.reviews.every((review) => props.selectedReviews.includes(review.id))
})

/** Выбор или снятие всех отзывов текущей страницы. */
const toggleAll = (event) => {
    emit('toggle-all', {
        checked: event.target.checked,
        ids: props.reviews.map((review) => review.id),
    })
}

/** Форматирование даты с учётом текущей локали. */
const formatDate = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return '—'

    return new Intl.DateTimeFormat(locale.value || 'ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** Название автора отзыва. */
const authorName = (review) => review?.author?.name || '—'

/** Email автора отзыва. */
const authorEmail = (review) => review?.author?.email || ''

/** Тип связанной сущности. */
const reviewableType = (review) => review?.reviewable?.type || review?.reviewable_type || '—'

/** Название связанной сущности. */
const reviewableTitle = (review) => {
    return review?.reviewable?.title || review?.reviewable?.name || review?.reviewable?.url || `ID: ${review?.reviewable_id ?? '—'}`
}

/** Основной текст карточки отзыва. */
const reviewText = (review) => {
    return review?.comment || review?.advantages || review?.disadvantages || '—'
}

/** Наличие ответа на отзыв. */
const hasReply = (review) => Boolean(review?.has_reply || review?.reply)

/** Бейдж статуса модерации. */
const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/** Бейдж подтверждённого опыта. */
const verifiedBadge = (verified) => {
    return verified
        ? {
            text: t('verified'),
            class: 'bg-sky-100 text-sky-700 border-sky-300 dark:bg-sky-900/40 dark:text-sky-300',
        }
        : {
            text: t('notVerified'),
            class: 'bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300',
        }
}

/** Отображение рейтинга в пределах от 0 до 5. */
const normalizedRating = (review) => Math.min(5, Math.max(0, Number(review?.rating || 0)))
</script>

<template>
    <div class="relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700">
        <!-- Верхняя панель массового выбора -->
        <div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedReviews.length }}
            </div>

            <label v-if="reviews.length"
                   class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" :checked="allSelected" @change="toggleAll">
            </label>
        </div>

        <!-- Сетка карточек -->
        <div v-if="reviews.length" class="p-3">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <article v-for="review in reviews" :key="review.id"
                         class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80">
                    <!-- ID и выбор карточки -->
                    <header
                        class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500">
                        <div
                            class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100">
                            ID: {{ review.id }}
                        </div>

                        <input type="checkbox" :checked="selectedReviews.includes(review.id)"
                               @change="$emit('toggle-select', review.id)">
                    </header>

                    <!-- Содержимое карточки -->
                    <div class="flex flex-1 flex-col space-y-2 px-3 py-2">
                        <!-- Автор -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="break-words text-center font-semibold text-blue-700 dark:text-blue-300">
                                {{ authorName(review) }}
                            </div>
                            <div v-if="authorEmail(review)"
                                 class="break-all text-center text-[10px] font-semibold text-gray-500 dark:text-gray-300">
                                {{ authorEmail(review) }}
                            </div>
                            <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                ID: {{ review.user_id }}
                            </div>
                        </div>

                        <!-- Рейтинг -->
                        <div class="flex items-center justify-center gap-1"
                             :title="`${t('rating')}: ${normalizedRating(review)}`">
                            <span v-for="star in 5" :key="star" class="text-base leading-none"
                                  :class="star <= normalizedRating(review) ? 'text-amber-500' : 'text-slate-300 dark:text-slate-600'">★</span>
                            <span
                                class="ml-1 text-xs font-semibold text-slate-600 dark:text-slate-300">{{ normalizedRating(review)
                                }}/5</span>
                        </div>

                        <!-- Текст отзыва -->
                        <div class="text-left">
                            <div
                                class="line-clamp-4 cursor-pointer text-xs font-semibold text-slate-600 dark:text-slate-200"
                                :title="reviewText(review)" @click="$emit('view-details', review)">
                                {{ reviewText(review) }}
                            </div>
                        </div>

                        <!-- Связанная сущность -->
                        <div class="space-y-0.5 text-center">
                            <div
                                class="flex items-center justify-center gap-2 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                <span>{{ reviewableType(review) }}</span>
                                <span>{{ t('id') }}: {{ review.reviewable_id }}</span>
                            </div>
                            <div class="line-clamp-2 text-xs font-semibold text-fuchsia-700 dark:text-fuchsia-300"
                                 :title="reviewableTitle(review)">
                                {{ reviewableTitle(review) }}
                            </div>
                        </div>

                        <!-- Подтверждение, ответ и статистика -->
                        <div class="flex flex-wrap items-center justify-center gap-1">
                            <span class="rounded-sm border px-2 py-1 text-[10px] font-semibold"
                                  :class="verifiedBadge(review.verified).class">
                                {{ verifiedBadge(review.verified).text }}
                            </span>
                            <span
                                class="rounded-sm border border-violet-300 bg-violet-100 px-2 py-1 text-[10px] font-semibold text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                                {{ t('likes') }}: {{ review.likes || 0 }}
                            </span>
                            <span
                                class="rounded-sm border border-cyan-300 bg-cyan-100 px-2 py-1 text-[10px] font-semibold text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300">
                                {{ t('images') }}: {{ review.images_count || 0 }}
                            </span>
                        </div>

                        <div class="flex justify-center">
                            <span class="rounded-sm border px-2 py-1 text-[10px] font-semibold"
                                  :class="hasReply(review) ? 'border-emerald-300 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'border-slate-300 bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'">
                                {{ hasReply(review) ? t('hasReply') : t('noReply') }}
                            </span>
                        </div>

                        <!-- Дата -->
                        <div class="flex items-center justify-center">
                            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400"
                                  :title="formatDate(review.created_at)">
                                {{ formatDate(review.updated_at || review.created_at) }}
                            </span>
                        </div>

                        <!-- Модерация -->
                        <div class="flex justify-center pt-1">
                            <div class="flex items-center justify-center gap-1">
                                <span class="rounded-sm border px-2 py-1 text-[10px] font-semibold"
                                      :class="moderationBadge(review.moderation_status).class"
                                      :title="review.moderation_note ? `${review.moderation_note} [${formatDate(review.moderated_at)}]` : null">
                                    {{ moderationBadge(review.moderation_status).text }}
                                </span>

                                <ModerationButton :isAdmin="isAdmin" :status="review.moderation_status ?? 0"
                                                  :initialNote="review.moderation_note || ''" mode="toggle"
                                                  @submit="({ status, note }) => $emit('approve-review', review, status, note)" />
                            </div>
                        </div>
                    </div>

                    <!-- Действия -->
                    <footer class="border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500">
                        <div class="flex items-center justify-between gap-2">
                            <button type="button"
                                    class="flex items-center rounded border border-slate-300 px-0 py-1 hover:border-blue-500 dark:border-blue-300 dark:hover:border-blue-100"
                                    :title="t('view')" @click="$emit('view-details', review)">
                                <svg class="mx-1 h-4 w-4 shrink-0 fill-current text-blue-500" viewBox="0 0 16 16">
                                    <path
                                        d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path>
                                </svg>
                            </button>

                            <div class="flex items-center gap-1">
                                <ActivityToggle :isActive="review.activity"
                                                :title="review.activity ? t('enabled') : t('disabled')"
                                                @toggle-activity="$emit('toggle-activity', review)" />
                                <DeleteIconButton @click="$emit('delete', review.id)" />
                            </div>
                        </div>
                    </footer>
                </article>
            </div>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
