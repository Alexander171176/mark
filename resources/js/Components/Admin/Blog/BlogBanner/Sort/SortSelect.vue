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
    <div class="flex justify-center items-center h-fit my-2">
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
            <option disabled>─────────────────</option>

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
            <option disabled>─────────────────</option>

            <option value="imagesDesc">{{ t('images') }} (9 → 0)</option>
            <option value="imagesAsc">{{ t('images') }} (0 → 9)</option>
            <option disabled>─────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────────</option>

            <option value="leftDesc">{{ t('inLeft') }} ON→OFF</option>
            <option value="leftAsc">{{ t('inLeft') }} OFF→ON</option>
            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>
            <option disabled>─────────────────</option>

            <option value="mainDesc">{{ t('inMain') }} ON→OFF</option>
            <option value="mainAsc">{{ t('inMain') }} OFF→ON</option>
            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>
            <option disabled>─────────────────</option>

            <option value="rightDesc">{{ t('inRight') }} ON→OFF</option>
            <option value="rightAsc">{{ t('inRight') }} OFF→ON</option>
            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>
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
