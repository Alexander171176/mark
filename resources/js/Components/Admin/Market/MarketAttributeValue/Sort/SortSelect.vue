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
    <div class="flex justify-center items-center h-fit">
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
            class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200
           text-gray-600 dark:text-gray-900
           border border-slate-400 dark:border-slate-600
           rounded-sm shadow-sm"
        >
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>─────────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>─────────────────</option>

            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="attributeTitleAsc">{{ t('attribute') }} A→Z</option>
            <option value="attributeTitleDesc">{{ t('attribute') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="codeAsc">{{ t('code') }} A→Z</option>
            <option value="codeDesc">{{ t('code') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="colorAsc">{{ t('typeColor') }} 0→F</option>
            <option value="colorDesc">{{ t('typeColor') }} F→0</option>
            <option disabled>─────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────────</option>

            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>
            <option value="statusDraft">{{ t('statusDraft') }}</option>
            <option value="statusPublished">{{ t('statusPublished') }}</option>
            <option value="statusArchived">{{ t('statusArchived') }}</option>
            <option disabled>─────────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option value="showFromAtDesc">{{ t('showFromAt') }} ↓</option>
            <option value="showFromAtAsc">{{ t('showFromAt') }} ↑</option>

            <option value="showToAtDesc">{{ t('showToAt') }} ↓</option>
            <option value="showToAtAsc">{{ t('showToAt') }} ↑</option>
            <option disabled>─────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
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
