<script setup>
import {defineProps, defineEmits} from 'vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();

defineProps({
    sortParam: String
});

const emits = defineEmits(['update:sortParam']);

const updateSort = (event) => {
    emits('update:sortParam', event.target.value);
};
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label for="sortParam"
               class="hidden lg:block sm:mr-2 tracking-wider
                      text-sm font-semibold text-slate-600 dark:text-slate-100">
            {{ t('sort') }}
        </label>
        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-52 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>────────────────</option>

            <option value="scheduleTitleAsc">{{ t('sortScheduleTitleAsc') }}</option>
            <option value="scheduleTitleDesc">{{ t('sortScheduleTitleDesc') }}</option>
            <option disabled>────────────────</option>

            <option value="statusAsc">{{ t('sortStatusAsc') }}</option>
            <option value="statusDesc">{{ t('sortStatusDesc') }}</option>
            <option disabled>────────────────</option>

            <option value="enrolledAtDesc">{{ t('sortEnrolledAtDesc') }}</option>
            <option value="enrolledAtAsc">{{ t('sortEnrolledAtAsc') }}</option>
            <option disabled>────────────────</option>

            <option value="userNameAsc">{{ t('sortUserNameAsc') }}</option>
            <option value="userNameDesc">{{ t('sortUserNameDesc') }}</option>

            <option value="userEmailAsc">{{ t('email') }} A→Z</option>
            <option value="userEmailDesc">{{ t('email') }} Z→A</option>
            <option disabled>────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>────────────────</option>
        </select>
    </div>
</template>
