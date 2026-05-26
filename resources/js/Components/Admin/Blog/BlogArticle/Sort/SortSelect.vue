<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: String,
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit mt-2 mb-2">
        <label
            for="sortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
                   text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>

            <option value="sortAsc">{{ t('sortNumber') }} (0 → 9)</option>
            <option value="sortDesc">{{ t('sortNumber') }} (9 → 0)</option>

            <option disabled>─────────────────</option>

            <option value="ownerNameAsc">{{ t('owner') }} (A → Z)</option>
            <option value="ownerNameDesc">{{ t('owner') }} (Z → A)</option>
            <option value="ownerEmailAsc">{{ t('ownerEmail') }} (A → Z)</option>
            <option value="ownerEmailDesc">{{ t('ownerEmail') }} (Z → A)</option>

            <option disabled>─────────────────</option>

            <option value="titleAsc">{{ t('title') }} (A → Z)</option>
            <option value="titleDesc">{{ t('title') }} (Z → A)</option>
            <option value="locale">{{ t('localization') }}</option>

            <option disabled>─────────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option disabled>─────────────────</option>

            <option value="viewsDesc">{{ t('views') }} (9 → 0)</option>
            <option value="viewsAsc">{{ t('views') }} (0 → 9)</option>

            <option value="likesDesc">{{ t('likes') }} (9 → 0)</option>
            <option value="likesAsc">{{ t('likes') }} (0 → 9)</option>

            <option value="commentsDesc">{{ t('comments') }} (9 → 0)</option>
            <option value="commentsAsc">{{ t('comments') }} (0 → 9)</option>

            <option disabled>─────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>

            <option disabled>─────────────────</option>

            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>

            <option disabled>─────────────────</option>

            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>

            <option disabled>─────────────────</option>

            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>

            <option disabled>─────────────────</option>

            <option value="moderation_pending">{{ t('underModeration') }}</option>
            <option value="moderation_approved">{{ t('statusSelectApproved') }}</option>
            <option value="moderation_rejected">{{ t('statusSelectRejected') }}</option>

            <option disabled>─────────────────</option>

            <option value="moderation_statusAsc">{{ t('moderationStatus') }} (0 → 2)</option>
            <option value="moderation_statusDesc">{{ t('moderationStatus') }} (2 → 0)</option>
        </select>
    </div>
</template>
