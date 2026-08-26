<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'idDesc',
    },
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
            class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>──────────────────</option>

            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>

            <option value="progressDesc">{{ t('progress') }} 9→0</option>
            <option value="progressAsc">{{ t('progress') }} 0→9</option>
            <option disabled>──────────────────</option>

            <option value="startedAtDesc">{{ t('shortStarted') }} ↓</option>
            <option value="startedAtAsc">{{ t('shortStarted') }} ↑</option>

            <option value="expiresAtDesc">{{ t('shortExpires') }} ↓</option>
            <option value="expiresAtAsc">{{ t('shortExpires') }} ↑</option>

            <option value="completedAtDesc">{{ t('shortCompleted') }} ↓</option>
            <option value="completedAtAsc">{{ t('shortCompleted') }} ↑</option>
            <option disabled>──────────────────</option>

            <option value="userNameAsc">{{ t('user') }} A→Z</option>
            <option value="userNameDesc">{{ t('user') }} Z→A</option>

            <option value="userEmailAsc">{{ t('email') }} A→Z</option>
            <option value="userEmailDesc">{{ t('email') }} Z→A</option>
            <option disabled>──────────────────</option>

            <option value="courseTitleAsc">{{ t('course') }} A→Z</option>
            <option value="courseTitleDesc">{{ t('course') }} Z→A</option>

            <option value="scheduleTitleAsc">{{ t('schedule') }} A→Z</option>
            <option value="scheduleTitleDesc">{{ t('schedule') }} Z→A</option>
            <option disabled>──────────────────</option>

            <option value="progressRecordsDesc">{{ t('progress') }} 9→0</option>
            <option value="progressRecordsAsc">{{ t('progress') }} 0→9</option>
            <option disabled>──────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>──────────────────</option>
        </select>
    </div>
</template>
