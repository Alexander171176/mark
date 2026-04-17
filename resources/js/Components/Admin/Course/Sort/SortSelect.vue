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
                class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200
               text-gray-600 dark:text-gray-900
               border border-slate-400 dark:border-slate-600
               rounded-sm shadow-sm">

            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option value="sort">{{ t('sortNumber') }}</option>
            <option disabled>────────────────</option>

            <option value="title">{{ t('title') }}</option>
            <option value="published_at">{{ t('publishedAt') }}</option>
            <option value="students_count">{{ t('sortStudentsCount') }}</option>
            <option disabled>────────────────</option>

            <!-- Новинки / хиты / распродажа -->
            <option value="is_new">{{ t('sortIsNew') }}</option>
            <option value="is_hit">{{ t('sortIsHit') }}</option>
            <option value="is_sale">{{ t('sortIsSale') }}</option>
            <option disabled>────────────────</option>

            <!-- Уровень / статус / доступность -->
            <option value="level">{{ t('level') }}</option>
            <option value="status">{{ t('status') }}</option>
            <option value="availability">{{ t('availability') }}</option>
            <option disabled>────────────────</option>

            <!-- Метрики сложности и длительности -->
            <option value="difficulty">{{ t('sortDifficulty') }}</option>
            <option value="duration">{{ t('duration') }}</option>
            <option disabled>────────────────</option>

            <!-- Метрики курса -->
            <option value="popularity">{{ t('popularity') }}</option>
            <option value="rating_count">{{ t('ratingCount') }}</option>
            <option value="rating_avg">{{ t('ratingAvg') }}</option>
            <option disabled>────────────────</option>

            <!-- Уже существующие -->
            <option value="views">{{ t('views') }}</option>
            <option value="likes">{{ t('likes') }}</option>
            <option disabled>────────────────</option>

            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>────────────────</option>

            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>
            <option disabled>────────────────</option>

            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>
            <option disabled>────────────────</option>

            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>
            <option disabled>────────────────</option>
        </select>
    </div>
</template>
