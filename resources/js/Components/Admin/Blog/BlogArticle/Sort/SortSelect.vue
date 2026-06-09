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
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>────────────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>────────────────────</option>

            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="urlAsc">URL A→Z</option>
            <option value="urlDesc">URL Z→A</option>
            <option disabled>────────────────────</option>

            <option value="ownerNameAsc">{{ t('owner') }} A→Z</option>
            <option value="ownerNameDesc">{{ t('owner') }} Z→A</option>

            <option value="ownerEmailAsc">{{ t('ownerEmail') }} A→Z</option>
            <option value="ownerEmailDesc">{{ t('ownerEmail') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>

            <option value="showFromAtDesc">
                {{ t('show') }} - {{ t('shortStarted') }} ↓
            </option>
            <option value="showFromAtAsc">
                {{ t('show') }} - {{ t('shortStarted') }} ↑
            </option>

            <option value="showToAtDesc">
                {{ t('show') }} - {{ t('shortExpires') }} ↓
            </option>
            <option value="showToAtAsc">
                {{ t('show') }} - {{ t('shortExpires') }} ↑
            </option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>────────────────────</option>

            <option value="viewsDesc">{{ t('views') }} 9→0</option>
            <option value="viewsAsc">{{ t('views') }} 0→9</option>

            <option value="likesDesc">{{ t('likes') }} 9→0</option>
            <option value="likesAsc">{{ t('likes') }} 0→9</option>

            <option value="commentsDesc">{{ t('comments') }} 9→0</option>
            <option value="commentsAsc">{{ t('comments') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="rubricsDesc">{{ t('rubrics') }} 9→0</option>
            <option value="rubricsAsc">{{ t('rubrics') }} 0→9</option>

            <option value="tagsDesc">{{ t('tags') }} 9→0</option>
            <option value="tagsAsc">{{ t('tags') }} 0→9</option>

            <option value="videosDesc">{{ t('videos') }} 9→0</option>
            <option value="videosAsc">{{ t('videos') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>

            <option value="relatedArticlesDesc">{{ t('relatedArticles') }} 9→0</option>
            <option value="relatedArticlesAsc">{{ t('relatedArticles') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>────────────────────</option>

            <option value="leftDesc">{{ t('inLeft') }} ON→OFF</option>
            <option value="leftAsc">{{ t('inLeft') }} OFF→ON</option>
            <option value="left">{{ t('inLeft') }}</option>
            <option value="noLeft">{{ t('notLeft') }}</option>
            <option disabled>────────────────────</option>

            <option value="mainDesc">{{ t('inMain') }} ON→OFF</option>
            <option value="mainAsc">{{ t('inMain') }} OFF→ON</option>
            <option value="main">{{ t('inMain') }}</option>
            <option value="noMain">{{ t('notMain') }}</option>
            <option disabled>────────────────────</option>

            <option value="rightDesc">{{ t('inRight') }} ON→OFF</option>
            <option value="rightAsc">{{ t('inRight') }} OFF→ON</option>
            <option value="right">{{ t('inRight') }}</option>
            <option value="noRight">{{ t('notRight') }}</option>
            <option disabled>────────────────────</option>

            <option value="moderationPending">{{ t('underModeration') }}</option>
            <option value="moderationApproved">{{ t('statusSelectApproved') }}</option>
            <option value="moderationRejected">{{ t('statusSelectRejected') }}</option>

            <option value="moderationStatusAsc">{{ t('moderationStatus') }} 0→2</option>
            <option value="moderationStatusDesc">{{ t('moderationStatus') }} 2→0</option>
            <option disabled>────────────────────</option>
        </select>
    </div>
</template>
