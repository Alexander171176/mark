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

const updateSort = (event) => {
    emit('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label
            for="sortParam"
            class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold
                   text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            class="w-64 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"
            @change="updateSort"
        >
            <!-- ID -->
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>────────────────────</option>

            <!-- Ручная сортировка -->
            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>────────────────────</option>

            <!-- Названия -->
            <option value="titleAsc">{{ t('title') }} A→Z</option>
            <option value="titleDesc">{{ t('title') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="productTitleAsc">{{ t('marketProduct') }} A→Z</option>
            <option value="productTitleDesc">{{ t('marketProduct') }} Z→A</option>
            <option disabled>────────────────────</option>

            <!-- Торговые коды -->
            <option value="codeAsc">{{ t('code') }} A→Z</option>
            <option value="codeDesc">{{ t('code') }} Z→A</option>

            <option value="skuAsc">SKU A→Z</option>
            <option value="skuDesc">SKU Z→A</option>

            <option value="vendorCodeAsc">{{ t('vendorCode') }} A→Z</option>
            <option value="vendorCodeDesc">{{ t('vendorCode') }} Z→A</option>

            <option value="barcodeAsc">{{ t('barcode') }} A→Z</option>
            <option value="barcodeDesc">{{ t('barcode') }} Z→A</option>
            <option disabled>────────────────────</option>

            <!-- Эффективные цены -->
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

            <!-- Остаток и наличие -->
            <option value="quantityDesc">{{ t('quantity') }} 9→0</option>
            <option value="quantityAsc">{{ t('quantity') }} 0→9</option>
            <option disabled>───────────────────</option>

            <option value="inStockDesc">{{ t('inStock') }} ON→OFF</option>
            <option value="inStockAsc">{{ t('inStock') }} OFF→ON</option>

            <option value="inStock">{{ t('inStock') }}</option>
            <option value="outOfStock">{{ t('outOfStock') }}</option>
            <option disabled>────────────────────</option>

            <!-- Эффективные физические параметры -->
            <option value="weightDesc">{{ t('weight') }} 9→0</option>
            <option value="weightAsc">{{ t('weight') }} 0→9</option>
            <option value="lengthDesc">{{ t('length') }} 9→0</option>
            <option value="lengthAsc">{{ t('length') }} 0→9</option>
            <option value="widthDesc">{{ t('width') }} 9→0</option>
            <option value="widthAsc">{{ t('width') }} 0→9</option>
            <option value="heightDesc">{{ t('height') }} 9→0</option>
            <option value="heightAsc">{{ t('height') }} 0→9</option>
            <option disabled>────────────────────</option>

            <!-- Основной вариант -->
            <option value="defaultDesc">{{ t('defaultVariant') }} ON→OFF</option>
            <option value="defaultAsc">{{ t('defaultVariant') }} OFF→ON</option>
            <option value="default">{{ t('defaultVariant') }}</option>
            <option value="notDefault">{{ t('notDefaultVariant') }}</option>
            <option disabled>────────────────────</option>

            <!-- Связи -->
            <option value="valuesDesc">{{ t('attributes') }} 9→0</option>
            <option value="valuesAsc">{{ t('attributes') }} 0→9</option>
            <option disabled>────────────────────</option>

            <option value="imagesDesc">{{ t('images') }} 9→0</option>
            <option value="imagesAsc">{{ t('images') }} 0→9</option>
            <option disabled>────────────────────</option>

            <!-- Активность -->
            <option value="activityDesc">{{ t('activity') }} ON→OFF</option>
            <option value="activityAsc">{{ t('activity') }} OFF→ON</option>
            <option value="activity">{{ t('active') }}</option>
            <option value="inactive">{{ t('inactive') }}</option>
            <option disabled>───────────────────</option>

            <!-- Статус публикации -->
            <option value="statusAsc">{{ t('status') }} A→Z</option>
            <option value="statusDesc">{{ t('status') }} Z→A</option>
            <option value="statusDraft">{{ t('statusDraft') }}</option>
            <option value="statusPublished">{{ t('statusPublished') }}</option>
            <option value="statusArchived">{{ t('statusArchived') }}</option>
            <option disabled>───────────────────</option>

            <!-- Модерация -->
            <option value="moderationPending">{{ t('underModeration') }}</option>
            <option value="moderationApproved">{{ t('statusSelectApproved') }}</option>
            <option value="moderationRejected">{{ t('statusSelectRejected') }}</option>
            <option value="moderationStatusAsc">{{ t('moderationStatus') }} 0→2</option>
            <option value="moderationStatusDesc">{{ t('moderationStatus') }} 2→0</option>
            <option disabled>───────────────────</option>

            <!-- Даты -->
            <option value="publishedAtDesc">{{ t('publishedAt') }} ↓</option>
            <option value="publishedAtAsc">{{ t('publishedAt') }} ↑</option>
            <option value="showFromAtDesc">{{ t('show') }} - {{ t('shortStarted') }} ↓</option>
            <option value="showFromAtAsc">{{ t('show') }} - {{ t('shortStarted') }} ↑</option>
            <option value="showToAtDesc">{{ t('show') }} - {{ t('shortExpires') }} ↓</option>
            <option value="showToAtAsc">{{ t('show') }} - {{ t('shortExpires') }} ↑</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>
            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>───────────────────</option>
        </select>
    </div>
</template>
