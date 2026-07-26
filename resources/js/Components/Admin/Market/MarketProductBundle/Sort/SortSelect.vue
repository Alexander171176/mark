<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: {
        type: String,
        default: 'idDesc',
    },
})

const emit = defineEmits([
    'update:sortParam',
])

/** Передача выбранного параметра сортировки родителю. */
const updateSort = (event) => {
    emit('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex h-fit items-center justify-center my-2">
        <label
            for="sortParam"
            class="hidden text-sm font-semibold tracking-wider
                   text-slate-600 dark:text-slate-100
                   sm:mr-2 lg:block"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            class="form-select w-64 rounded-sm border
                   border-slate-400 bg-white px-3 py-0.5
                   text-gray-600 shadow-sm
                   dark:border-slate-600 dark:bg-gray-200
                   dark:text-gray-900"
            @change="updateSort"
        >
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>────────────────────</option>

            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>────────────────────</option>

            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option value="urlAsc">URL A→Z</option>
            <option value="urlDesc">URL Z→A</option>
            <option disabled>────────────────────</option>

            <option value="skuAsc">SKU A→Z</option>
            <option value="skuDesc">SKU Z→A</option>
            <option value="vendorCodeAsc">{{ t('vendorCode') }} A→Z</option>
            <option value="vendorCodeDesc">{{ t('vendorCode') }} Z→A</option>
            <option value="barcodeAsc">{{ t('barcode') }} A→Z</option>
            <option value="barcodeDesc">{{ t('barcode') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="companyAsc">{{ t('marketCompany') }} A→Z</option>
            <option value="companyDesc">{{ t('marketCompany') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="shopAsc">{{ t('marketShop') }} A→Z</option>
            <option value="shopDesc">{{ t('marketShop') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="ownerNameAsc">{{ t('owner') }} A→Z</option>
            <option value="ownerNameDesc">{{ t('owner') }} Z→A</option>
            <option value="ownerEmailAsc">{{ t('ownerEmail') }} A→Z</option>
            <option value="ownerEmailDesc">{{ t('ownerEmail') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="calculatePriceDesc">{{ t('automaticPrice') }} ON→OFF</option>
            <option value="calculatePriceAsc">{{ t('automaticPrice') }} OFF→ON</option>
            <option value="calculatedPrice">{{ t('automaticPrice') }}</option>
            <option value="manualPrice">{{ t('manualPrice') }}</option>
            <option disabled>────────────────────</option>

            <option value="priceDesc">{{ t('price') }} 9→0</option>
            <option value="priceAsc">{{ t('price') }} 0→9</option>
            <option value="oldPriceDesc">{{ t('compareAtPrice') }} 9→0</option>
            <option value="oldPriceAsc">{{ t('compareAtPrice') }} 0→9</option>
            <option value="purchasePriceDesc">{{ t('purchasePrice') }} 9→0</option>
            <option value="purchasePriceAsc">{{ t('purchasePrice') }} 0→9</option>
            <option value="wholesalePriceDesc">{{ t('wholesalePrice') }} 9→0</option>
            <option value="wholesalePriceAsc">{{ t('wholesalePrice') }} 0→9</option>
            <option value="wholesaleMinQuantityDesc">{{ t('wholesaleMinQuantity') }} 9→0</option>
            <option value="wholesaleMinQuantityAsc">{{ t('wholesaleMinQuantity') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="viewsDesc">{{ t('views') }} 9→0</option>
            <option value="viewsAsc">{{ t('views') }} 0→9</option>
            <option value="likesDesc">{{ t('likes') }} 9→0</option>
            <option value="likesAsc">{{ t('likes') }} 0→9</option>
            <option value="ratingDesc">{{ t('rating') }} 9→0</option>
            <option value="ratingAsc">{{ t('rating') }} 0→9</option>
            <option value="ratingCountDesc">{{ t('ratingCount') }} 9→0</option>
            <option value="ratingCountAsc">{{ t('ratingCount') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="itemsDesc">{{ t('checkbox') }} 9→0</option>
            <option value="itemsAsc">{{ t('checkbox') }} 0→9</option>
            <option value="hasItems">{{ t('yes') }}</option>
            <option value="withoutItems">{{ t('no') }}</option>
            <option disabled>────────────────────</option>

            <option value="newDesc">{{ t('sortIsNew') }} ON→OFF</option>
            <option value="newAsc">{{ t('sortIsNew') }} OFF→ON</option>
            <option value="new">{{ t('sortIsNew') }}</option>
            <option value="notNew">{{ t('notNew') }}</option>
            <option disabled>────────────────────</option>

            <option value="hitDesc">{{ t('sortIsHit') }} ON→OFF</option>
            <option value="hitAsc">{{ t('sortIsHit') }} OFF→ON</option>
            <option value="hit">{{ t('sortIsHit') }}</option>
            <option value="notHit">{{ t('notHit') }}</option>
            <option disabled>────────────────────</option>

            <option value="saleDesc">{{ t('sortIsSale') }} ON→OFF</option>
            <option value="saleAsc">{{ t('sortIsSale') }} OFF→ON</option>
            <option value="sale">{{ t('sortIsSale') }}</option>
            <option value="notSale">{{ t('notSale') }}</option>
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

            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>
            <option value="statusDraft">{{ t('statusDraft') }}</option>
            <option value="statusPublished">{{ t('statusPublished') }}</option>
            <option value="statusArchived">{{ t('statusArchived') }}</option>
            <option disabled>────────────────────</option>

            <option value="moderationPending">{{ t('underModeration') }}</option>
            <option value="moderationApproved">{{ t('statusSelectApproved') }}</option>
            <option value="moderationRejected">{{ t('statusSelectRejected') }}</option>
            <option value="moderationStatusAsc">{{ t('moderationStatus') }} 0→2</option>
            <option value="moderationStatusDesc">{{ t('moderationStatus') }} 2→0</option>
            <option disabled>────────────────────</option>

            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>
            <option value="showFromAtDesc">{{ t('show') }} — {{ t('shortStarted') }} ↓</option>
            <option value="showFromAtAsc">{{ t('show') }} — {{ t('shortStarted') }} ↑</option>
            <option value="showToAtDesc">{{ t('show') }} — {{ t('shortExpires') }} ↓</option>
            <option value="showToAtAsc">{{ t('show') }} — {{ t('shortExpires') }} ↑</option>
            <option disabled>────────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>
            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>────────────────────</option>
        </select>
    </div>
</template>
