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

const emits = defineEmits([
    'update:sortParam',
])

const updateSort = (event) => {
    emits(
        'update:sortParam',
        event.target.value
    )
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

            <option value="groupTitleAsc">{{ t('group') }} A→Z</option>
            <option value="groupTitleDesc">{{ t('group') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="codeAsc">{{ t('code') }} A→Z</option>
            <option value="codeDesc">{{ t('code') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="colorAsc">{{ t('typeColor') }} 0→F</option>
            <option value="colorDesc">{{ t('typeColor') }} F→0</option>
            <option disabled>─────────────────</option>

            <option value="typeAsc">{{ t('type') }} A→Z</option>
            <option value="typeDesc">{{ t('type') }} Z→A</option>
            <option value="unitAsc">{{ t('unit') }} A→Z</option>
            <option value="unitDesc">{{ t('unit') }} Z→A</option>
            <option disabled>─────────────────</option>

            <option value="valuesCountDesc">{{ t('values') }} 9→0</option>
            <option value="valuesCountAsc">{{ t('values') }} 0→9</option>
            <option disabled>─────────────────</option>

            <option value="requiredDesc">{{ t('required') }} ON→OFF</option>
            <option value="requiredAsc">{{ t('required') }} OFF→ON</option>
            <option value="required">{{ t('required') }}</option>
            <option value="notRequired">{{ t('notRequired') }}</option>
            <option disabled>─────────────────</option>

            <option value="filterableDesc">{{ t('showFilter') }} ON→OFF</option>
            <option value="filterableAsc">{{ t('showFilter') }} OFF→ON</option>
            <option value="filterable">{{ t('showFilter') }}</option>
            <option value="notFilterable">{{ t('notFilterable') }}</option>
            <option disabled>─────────────────</option>

            <option value="useForVariantsDesc">{{ t('variants') }} ON→OFF</option>
            <option value="useForVariantsAsc">{{ t('variants') }} OFF→ON</option>
            <option value="useForVariants">{{ t('variants') }}</option>
            <option value="notForVariants">{{ t('notForVariants') }}</option>
            <option disabled>─────────────────</option>

            <option value="visibleDesc">{{ t('show') }} ON→OFF</option>
            <option value="visibleAsc">{{ t('show') }} OFF→ON</option>
            <option value="visible">{{ t('show') }}</option>
            <option value="hidden">{{ t('availabilityUnlisted') }}</option>
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

            <option value="ownerNameAsc">{{ t('owner') }} A→Z</option>
            <option value="ownerNameDesc">{{ t('owner') }} Z→A</option>
            <option value="ownerEmailAsc">{{ t('ownerEmail') }} A→Z</option>
            <option value="ownerEmailDesc">{{ t('ownerEmail') }} Z→A</option>
        </select>
    </div>
</template>
