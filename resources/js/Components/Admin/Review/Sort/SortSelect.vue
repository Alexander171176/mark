<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Сортировка универсальных отзывов.
 *
 * Значения параметров должны полностью совпадать:
 * - с локальной сортировкой в Index.vue;
 * - со scopeSortByParam() модели Review.
 */

import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'idDesc',
    },
})

const emit = defineEmits(['update:sortParam'])

/** Передача выбранного параметра сортировки родительской странице. */
const updateSort = (event) => {
    emit('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label
            for="sortParam"
            class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"
            @change="updateSort"
        >
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>─────────────────</option>

            <option value="authorNameAsc">{{ t('user') }} A→Z</option>
            <option value="authorNameDesc">{{ t('user') }} Z→A</option>
            <option value="authorEmailAsc">Email A→Z</option>
            <option value="authorEmailDesc">Email Z→A</option>
            <option disabled>─────────────────</option>

            <option value="ratingDesc">{{ t('rating') }} 5→1</option>
            <option value="ratingAsc">{{ t('rating') }} 1→5</option>

            <option value="likesDesc">{{ t('likes') }} 9→0</option>
            <option value="likesAsc">{{ t('likes') }} 0→9</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>─────────────────</option>

            <option value="commentAsc">{{ t('reviews') }} A→Z</option>
            <option value="commentDesc">{{ t('reviews') }} Z→A</option>

            <option value="reviewableTypeAsc">{{ t('type') }} A→Z</option>
            <option value="reviewableTypeDesc">{{ t('type') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="verifiedDesc">{{ t('verified') }} ON→OFF</option>
            <option value="verifiedAsc">{{ t('verified') }} OFF→ON</option>
            <option value="verified">{{ t('verified') }}</option>
            <option value="notVerified">{{ t('notVerified') }}</option>
            <option disabled>─────────────────</option>

            <option value="replyDesc">{{ t('reply') }} ON→OFF</option>
            <option value="replyAsc">{{ t('reply') }} OFF→ON</option>
            <option value="hasReply">{{ t('hasReply') }}</option>
            <option value="noReply">{{ t('noReply') }}</option>
            <option disabled>─────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>
            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>─────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────────</option>

            <option value="moderationPending">{{ t('underModeration') }}</option>
            <option value="moderationApproved">{{ t('statusSelectApproved') }}</option>
            <option value="moderationRejected">{{ t('statusSelectRejected') }}</option>
            <option value="moderationStatusAsc">{{ t('moderationStatus') }} 0→2</option>
            <option value="moderationStatusDesc">{{ t('moderationStatus') }} 2→0</option>
            <option disabled>─────────────────</option>
        </select>
    </div>
</template>
