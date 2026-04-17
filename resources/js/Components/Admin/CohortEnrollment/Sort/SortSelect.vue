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
        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-52 px-3 py-0.5 form-select bg-white dark:bg-gray-200
           text-gray-600 dark:text-gray-900
           border border-slate-400 dark:border-slate-600
           rounded-sm shadow-sm"
        >
            <!-- ID -->
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>────────────────</option>

            <!-- Название потока -->
            <option value="scheduleTitleAsc">{{ t('sortScheduleTitleAsc') }}</option>
            <option value="scheduleTitleDesc">{{ t('sortScheduleTitleDesc') }}</option>
            <option disabled>────────────────</option>

            <!-- Статус -->
            <option value="statusAsc">{{ t('sortStatusAsc') }}</option>
            <option value="statusDesc">{{ t('sortStatusDesc') }}</option>
            <option disabled>────────────────</option>

            <!-- Дата зачисления -->
            <option value="enrolledAtDesc">{{ t('sortEnrolledAtDesc') }}</option>
            <option value="enrolledAtAsc">{{ t('sortEnrolledAtAsc') }}</option>
            <option disabled>────────────────</option>

            <!-- Имя пользователя -->
            <option value="userNameAsc">{{ t('sortUserNameAsc') }}</option>
            <option value="userNameDesc">{{ t('sortUserNameDesc') }}</option>
            <option disabled>────────────────</option>
        </select>
    </div>
</template>
