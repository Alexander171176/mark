<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import CurrencyRatesTable from '@/Components/Admin/CurrencyRate/Table/CurrencyRatesTable.vue'

const { t } = useI18n()

const props = defineProps({
    currency: { type: Object, required: true },
    rates: { type: [Array, Object], required: true },   // ✅ важно
    currencies: { type: Array, required: true },
})

const ratesArray = computed(() => {
    // поддержка обоих форматов: [] и {data: []}
    return Array.isArray(props.rates) ? props.rates : (props.rates?.data ?? [])
})
</script>

<template>
    <AdminLayout :title="`${t('currencyRates')} — ${currency.code}`">
        <template #header>
            <TitlePage>
                {{ t('currencyRates') + ': ' }}
                <span class="font-semibold">{{ currency.name }} ({{ currency.code }})</span>
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                  overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                  bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.currencies.index')">
                        <template #icon>...</template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <CurrencyRatesTable
                    :currency="currency"
                    :rates="ratesArray"
                    :currencies="currencies"
                />
            </div>
        </div>
    </AdminLayout>
</template>
