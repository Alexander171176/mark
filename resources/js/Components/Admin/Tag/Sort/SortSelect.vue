<script setup>
import {defineProps, defineEmits} from 'vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();

const props = defineProps({
    sortParam: String
});

const emits = defineEmits(['update:sortParam']);

const updateSort = (event) => {
    emits('update:sortParam', event.target.value);
};
</script>

<template>
    <div class="flex justify-center items-center h-fit sm:mr-4 mt-2 mb-2">
        <label for="sortParam"
               class="hidden lg:block sm:mr-2 tracking-wider
                      text-sm font-semibold text-slate-600 dark:text-slate-100">
            {{ t('sort') }}
        </label>
        <select id="sortParam" :value="sortParam" @change="updateSort"
                class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                       text-gray-600 dark:text-gray-900
                       border border-slate-400 dark:border-slate-600
                       rounded-sm shadow-sm">
            <!-- Новые варианты для id -->
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option value="sort">{{ t('sortNumber') }}</option>
            <option disabled>─────────────────</option>

            <option value="ownerNameAsc">{{ t('owner') }} (A→Z)</option>
            <option value="ownerNameDesc">{{ t('owner') }} (Z→A)</option>
            <option value="ownerEmailAsc">{{ t('ownerEmail') }} (A→Z)</option>
            <option value="ownerEmailDesc">{{ t('ownerEmail') }} (Z→A)</option>
            <option disabled>─────────────────</option>

            <option value="name">{{ t('name') }}</option>
            <option value="locale">{{ t('localization') }}</option>
            <option disabled>─────────────────</option>

            <option value="views">{{ t('views') }}</option>
            <option disabled>─────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>─────────────────</option>

            <option value="moderation_pending">{{ t('underModeration') }}</option>
            <option value="moderation_approved">{{ t('statusSelectApproved') }}</option>
            <option value="moderation_rejected">{{ t('statusSelectRejected') }}</option>
            <option disabled>─────────────────</option>

            <option value="moderation_statusAsc">{{ t('moderationStatus') }} (0 → 2)</option>
            <option value="moderation_statusDesc">{{ t('moderationStatus') }} (2 → 0)</option>
            <option disabled>─────────────────</option>
        </select>
    </div>
</template>
