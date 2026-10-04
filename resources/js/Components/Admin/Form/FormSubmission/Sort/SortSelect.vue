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
            <!-- ID -->
            <option value="idDesc">
                ID ↓
            </option>
            <option value="idAsc">
                ID ↑
            </option>

            <option disabled>─────────────────</option>

            <!-- Дата отправки -->
            <option value="submittedAtDesc">
                {{ t('submittedAt') }} ↓
            </option>
            <option value="submittedAtAsc">
                {{ t('submittedAt') }} ↑
            </option>

            <option disabled>─────────────────</option>

            <!-- Статус -->
            <option value="statusAsc">
                {{ t('status') }} A→Z
            </option>
            <option value="statusDesc">
                {{ t('status') }} Z→A
            </option>

            <option disabled>─────────────────</option>

            <!-- Форма -->
            <option value="formTitleAsc">
                {{ t('form') }} A→Z
            </option>
            <option value="formTitleDesc">
                {{ t('form') }} Z→A
            </option>

            <option value="formIdDesc">
                {{ t('form') }} ID ↓
            </option>
            <option value="formIdAsc">
                {{ t('form') }} ID ↑
            </option>

            <option disabled>─────────────────</option>

            <!-- Отправитель -->
            <option value="userNameAsc">
                {{ t('sender') }} A→Z
            </option>
            <option value="userNameDesc">
                {{ t('sender') }} Z→A
            </option>

            <option disabled>─────────────────</option>

            <!-- Ответственный -->
            <option value="assignedUserNameAsc">
                {{ t('assignedUser') }} A→Z
            </option>
            <option value="assignedUserNameDesc">
                {{ t('assignedUser') }} Z→A
            </option>

            <option disabled>─────────────────</option>

            <!-- Источник -->
            <option value="sourceAsc">
                {{ t('source') }} A→Z
            </option>
            <option value="sourceDesc">
                {{ t('source') }} Z→A
            </option>

            <!-- Локаль -->
            <option value="localeAsc">
                {{ t('locale') }} A→Z
            </option>
            <option value="localeDesc">
                {{ t('locale') }} Z→A
            </option>

            <option disabled>─────────────────</option>

            <!-- Количество значений -->
            <option value="valuesDesc">
                {{ t('values') }} 9→0
            </option>
            <option value="valuesAsc">
                {{ t('values') }} 0→9
            </option>

            <!-- Количество файлов -->
            <option value="filesDesc">
                {{ t('files') }} 9→0
            </option>
            <option value="filesAsc">
                {{ t('files') }} 0→9
            </option>

            <option disabled>─────────────────</option>

            <!-- Дата начала обработки -->
            <option value="processedAtDesc">
                {{ t('processedAt') }} ↓
            </option>
            <option value="processedAtAsc">
                {{ t('processedAt') }} ↑
            </option>

            <!-- Дата завершения -->
            <option value="completedAtDesc">
                {{ t('completedAt') }} ↓
            </option>
            <option value="completedAtAsc">
                {{ t('completedAt') }} ↑
            </option>

            <option disabled>─────────────────</option>

            <!-- Дата создания -->
            <option value="createdAtDesc">
                {{ t('createdAt') }} ↓
            </option>
            <option value="createdAtAsc">
                {{ t('createdAt') }} ↑
            </option>

            <!-- Дата изменения -->
            <option value="updatedAtDesc">
                {{ t('updatedAt') }} ↓
            </option>
            <option value="updatedAtAsc">
                {{ t('updatedAt') }} ↑
            </option>
        </select>
    </div>
</template>
