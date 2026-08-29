<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Табличное отображение универсальных отзывов.
 *
 * Компонент не выполняет HTTP-запросы самостоятельно.
 * Все административные действия передаются родительской странице Index.vue.
 */

import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

/** Входящие данные таблицы. */
const props = defineProps({
    reviews: { type: Array, default: () => [] },
    selectedReviews: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

/** События для родительской страницы Index.vue. */
const emit = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'toggle-all',
    'view-details',
    'approve-review',
])

/** ID отзывов, отображаемых на текущей странице. */
const displayedIds = computed(() => {
    return props.reviews.map((review) => review.id)
})

/** Все отображаемые отзывы выбраны. */
const allDisplayedSelected = computed(() => {
    return displayedIds.value.length > 0
        && displayedIds.value.every((id) => props.selectedReviews.includes(id))
})

/** Выбор или снятие всех отзывов текущей страницы. */
const toggleAll = (event) => {
    emit('toggle-all', {
        checked: event.target.checked,
        ids: displayedIds.value,
    })
}

/** Форматирование даты и времени с учётом текущей локали. */
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

/** Ограничение длинного текста в ячейке. */
const truncate = (value, maxLength = 160) => {
    const text = String(value || '').trim()

    if (!text) return '—'
    if (text.length <= maxLength) return text

    return `${text.slice(0, maxLength)}…`
}

/** Отображаемый тип полиморфной сущности. */
const reviewableType = (review) => {
    return review?.reviewable?.type
        || review?.reviewable_type
        || '—'
}

/** ID полиморфной сущности. */
const reviewableId = (review) => {
    return review?.reviewable?.id
        ?? review?.reviewable_id
        ?? '—'
}

/** Нормализованное название полиморфной сущности. */
const reviewableTitle = (review) => {
    return review?.reviewable?.title || '—'
}

/** Основной артикул полиморфной сущности. */
const reviewableArticle = (review) => {
    return review?.reviewable?.sku
        || review?.reviewable?.vendor_code
        || ''
}

/** Штрихкод полиморфной сущности. */
const reviewableBarcode = (review) => {
    return review?.reviewable?.barcode || ''
}

/** Наличие ответа на отзыв. */
const hasReply = (review) => {
    return Boolean(
        review?.has_reply
        || String(review?.reply || '').trim()
    )
}

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

/** Цвет бейджа подтверждённого опыта. */
const verifiedBadgeClass = (verified) => {
    return verified
        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300'
        : 'bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300'
}
</script>

<template>
    <div class="relative bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-200 dark:border-slate-600">
        <!-- Верхняя панель выбора -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedReviews.length }}
            </div>

            <label v-if="reviews.length"
                   class="flex items-center text-xs text-slate-600 dark:text-slate-200
                          cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox"
                       class="mx-2"
                       :checked="allDisplayedSelected"
                       @change="toggleAll">
            </label>
        </div>

        <div class="overflow-x-auto">
            <table v-if="reviews.length"
                   class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                              border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('owner') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 min-w-72">
                        <div class="font-medium text-left">{{ t('review') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 min-w-52">
                        <div class="font-medium text-center">{{ t('object') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('status') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('actions') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            <input type="checkbox"
                                   :checked="allDisplayedSelected"
                                   @change="toggleAll">
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="review in reviews" :key="review.id"
                    class="text-sm font-semibold border-b-2
                           hover:bg-slate-100 dark:hover:bg-cyan-800">
                    <!-- ID -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap">
                        <div
                            class="text-center"
                            :title="`${formatDate(review.created_at)} / ${formatDate(review.updated_at)}`">
                            {{ review.id }}
                        </div>
                    </td>

                    <!-- Автор -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap">
                        <div class="flex flex-col">
                            <div class="font-semibold text-blue-700 dark:text-blue-300">
                                {{ review.author?.name || '—' }}
                            </div>

                            <div v-if="review.author?.email"
                                 class="text-[10px] text-gray-500 dark:text-gray-300
                                        break-all font-semibold">
                                {{ review.author.email }}
                            </div>

                            <div class="text-xs text-slate-500 dark:text-slate-300">
                                ID: {{ review.user_id }}
                            </div>
                        </div>
                    </td>

                    <!-- Отзыв и показатели -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2">
                        <button type="button"
                                class="block w-full text-left"
                                :title="review.comment || ''" @click="emit('view-details', review)">
                            <span class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="inline-flex items-center rounded-sm
                                                 border border-amber-300 bg-amber-100
                                                 px-2 py-0 text-xs font-bold text-amber-700
                                                 dark:bg-amber-900/40 dark:text-amber-300">
                                        ★ {{ Number(review.rating || 0) }}/5
                                    </span>

                                <span class="inline-flex items-center rounded-sm border
                                             px-2 py-0.5 text-[10px] font-semibold"
                                      :class="verifiedBadgeClass(review.verified)">
                                        {{ review.verified ? t('verified') : t('notVerified') }}
                                    </span>

                                <span class="text-[10px] text-slate-500 dark:text-slate-300">
                                        {{ t('likes') }}: {{ Number(review.likes || 0) }}
                                    </span>

                                <span class="text-[10px] text-slate-500 dark:text-slate-300">
                                        {{ t('images') }}: {{ Number(review.images_count || 0) }}
                                    </span>
                            </span>

                            <div class="text-xs text-slate-600 dark:text-slate-200
                                        whitespace-normal break-words">
                                {{ truncate(review.comment) }}
                            </div>

                            <div class="mt-1 text-[10px] font-semibold"
                                 :class="hasReply(review)
                                 ? 'text-emerald-700 dark:text-emerald-300'
                                 : 'text-slate-400 dark:text-slate-400'">
                                {{ hasReply(review) ? t('hasReply') : t('noReply') }}
                            </div>
                        </button>
                    </td>

                    <!-- Связанная сущность -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2">
                        <div class="flex flex-wrap items-center justify-center gap-1
                                    text-xs font-semibold text-violet-700 dark:text-violet-300">
                            <span>{{ reviewableType(review) }}</span>
                            <span>{{ t('id') }}: {{ reviewableId(review) }}</span>
                        </div>

                        <div class="mt-1 text-center text-xs text-fuchsia-700 dark:text-fuchsia-300
                                    whitespace-normal break-words line-clamp-2"
                             :title="reviewableTitle(review)">
                            {{ reviewableTitle(review) }}
                        </div>

                        <div v-if="reviewableArticle(review)"
                             class="mt-1 text-center text-[10px] text-slate-500
                                    dark:text-slate-300">
                            SKU: {{ reviewableArticle(review) }}
                        </div>

                        <div v-if="reviewableBarcode(review)"
                             class="mt-0.5 text-center text-[10px] text-slate-400
                                    dark:text-slate-400">
                            {{ reviewableBarcode(review) }}
                        </div>
                    </td>

                    <!-- Модерация -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                                <span class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                      :class="moderationBadge(review.moderation_status).class"
                                      :title="review.moderation_note ? `${review.moderation_note}${review.moderated_at ? ` [${formatDate(review.moderated_at)}]` : ''}` : null">
                                    {{ moderationBadge(review.moderation_status).text }}
                                </span>

                            <ModerationButton
                                :isAdmin="isAdmin"
                                :status="review.moderation_status ?? 0"
                                :initialNote="review.moderation_note || ''"
                                mode="toggle"
                                @submit="({ status, note }) =>
                                emit('approve-review', review, status, note)"
                            />
                        </div>
                    </td>

                    <!-- Действия -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button"
                                    :title="t('view')"
                                    class="flex items-center py-1 px-0 rounded border
                                           border-slate-300 hover:border-blue-500
                                           dark:border-blue-300 dark:hover:border-blue-100"
                                    @click="emit('view-details', review)">
                                <svg class="w-4 h-4 shrink-0 fill-current text-blue-500 mx-1"
                                     viewBox="0 0 16 16">
                                    <path d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z" />
                                </svg>
                            </button>

                            <ActivityToggle
                                :isActive="review.activity"
                                :title="review.activity ? t('enabled') : t('disabled')"
                                @toggle-activity="emit('toggle-activity', review)" />

                            <DeleteIconButton :title="t('delete')" @delete="emit('delete', review.id)" />
                        </div>
                    </td>

                    <!-- Выбор строки -->
                    <td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox"
                                   :checked="selectedReviews.includes(review.id)"
                                   @change="emit('toggle-select', review.id)">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div v-else class="py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
