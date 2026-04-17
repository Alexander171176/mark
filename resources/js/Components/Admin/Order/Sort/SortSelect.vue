<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    sortParam: {
        type: String,
        default: 'createdDesc',
    },
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit sm:mr-4 mt-2 mb-2">
        <label for="sortParam"
               class="hidden lg:block sm:mr-2 tracking-wider
                      text-sm font-semibold text-slate-600 dark:text-slate-100">
            {{ t('sort') }}
        </label>
        <select :value="sortParam" @change="updateSort"
                class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                       text-gray-600 dark:text-gray-900
                       border border-slate-400 dark:border-slate-600
                       rounded-sm shadow-sm">

            <!-- ID -->
            <option value="idDesc">{{ t('idDesc') }}</option>
            <option value="idAsc">{{ t('idAsc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Номер заказа -->
            <option value="numberAsc">{{ t('sortNumberAsc') }}</option>
            <option value="numberDesc">{{ t('sortNumberDesc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Дата создания -->
            <option value="createdDesc">{{ t('sortNewestFirst') }}</option>
            <option value="createdAsc">{{ t('sortOldestFirst') }}</option>
            <option disabled>──────────────────</option>

            <!-- Итоговая сумма -->
            <option value="totalDesc">{{ t('sortTotalDesc') }}</option>
            <option value="totalAsc">{{ t('sortTotalAsc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Покупатель -->
            <option value="buyerAsc">{{ t('sortBuyerAsc') }}</option>
            <option value="buyerDesc">{{ t('sortBuyerDesc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Статус заказа -->
            <option value="statusAsc">{{ t('sortStatusAsc') }}</option>
            <option value="statusDesc">{{ t('sortStatusDesc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Статус оплаты -->
            <option value="paymentStatusAsc">{{ t('sortPaymentStatusAsc') }}</option>
            <option value="paymentStatusDesc">{{ t('sortPaymentStatusDesc') }}</option>
            <option disabled>──────────────────</option>

            <!-- Оплачен / не оплачен -->
            <option value="paidFirst">{{ t('sortPaidFirst') }}</option>
            <option value="paidLast">{{ t('sortPaidLast') }}</option>
            <option disabled>──────────────────</option>
        </select>
    </div>
</template>
