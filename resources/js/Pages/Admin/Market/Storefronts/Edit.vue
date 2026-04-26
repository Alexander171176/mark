<script setup>
/**
 * @version PulsarCMS 1.0
 * Редактирование витрины (MarketStorefront)
 */
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    storefront: {
        type: Object,
        required: true,
    },
    companies: {
        type: Array,
        default: () => [],
    },
    currencies: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    _method: 'PUT',

    company_id: props.storefront.company_id ?? '',
    sort: props.storefront.sort ?? 0,
    activity: Boolean(props.storefront.activity),

    slug: props.storefront.slug ?? '',
    is_main: Boolean(props.storefront.is_main),

    domain: props.storefront.domain ?? '',
    subdomain: props.storefront.subdomain ?? '',
    primary_host: props.storefront.primary_host ?? '',

    default_locale: props.storefront.default_locale ?? 'ru',
    default_currency_id: props.storefront.default_currency_id ?? '',

    note: props.storefront.note ?? '',
})

const localeOptions = [
    { value: '', label: '—' },
    { value: 'ru', label: 'Русский (ru)' },
    { value: 'kk', label: 'Қазақша (kk)' },
    { value: 'en', label: 'English (en)' },
]

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
        is_main: data.is_main ? 1 : 0,
        default_currency_id: data.default_currency_id || null,
    }))

    form.post(route('admin.marketStorefronts.update', props.storefront.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Витрина успешно обновлена!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении витрины:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}

const slugLength = computed(() => form.slug?.length || 0)
const domainLength = computed(() => form.domain?.length || 0)
const subdomainLength = computed(() => form.subdomain?.length || 0)
const primaryHostLength = computed(() => form.primary_host?.length || 0)
const localeLength = computed(() => form.default_locale?.length || 0)
const noteLength = computed(() => form.note?.length || 0)
</script>

<template>
    <AdminLayout :title="t('editMarketStorefront')">
        <template #header>
            <TitlePage>
                {{ t('editMarketStorefront') }} - {{ storefront.slug }} [ID: {{ storefront.id }}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketStorefronts.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full">
                    <!-- Активность / главная / сортировка -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.is_main" />
                                <LabelCheckbox
                                    for="is_main"
                                    :text="t('mainStorefront')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                    class="text-sm"
                                />
                            </div>
                            <InputNumber
                                id="sort"
                                type="number"
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />
                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Компания / slug -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start w-full">
                            <LabelInput for="company_id">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('marketCompany') }}
                            </LabelInput>

                            <select
                                id="company_id"
                                v-model="form.company_id"
                                class="w-full px-3 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 text-gray-600 dark:text-slate-100
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option value="">— {{ t('selectCompany') }} —</option>
                                <option
                                    v-for="company in companies"
                                    :key="company.id"
                                    :value="company.id"
                                >
                                    {{ company.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.company_id" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="slug">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('slug') }}
                            </LabelInput>
                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                required
                                autocomplete="slug"
                                placeholder="shop-main"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ slugLength }} / 191 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>
                    </div>

                    <!-- Домены -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="domain" :value="t('domain')" />
                            <InputText
                                id="domain"
                                type="text"
                                v-model="form.domain"
                                autocomplete="domain"
                                placeholder="example.kz"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ domainLength }} / 255 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.domain" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="subdomain" :value="t('subdomain')" />
                            <InputText
                                id="subdomain"
                                type="text"
                                v-model="form.subdomain"
                                autocomplete="subdomain"
                                placeholder="shop"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ subdomainLength }} / 191 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.subdomain" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="primary_host" :value="t('primaryHost')" />
                            <InputText
                                id="primary_host"
                                type="text"
                                v-model="form.primary_host"
                                autocomplete="primary_host"
                                placeholder="shop.example.kz"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ primaryHostLength }} / 255 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.primary_host" />
                        </div>
                    </div>

                    <!-- Локаль / валюта -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start w-full">
                            <LabelInput for="default_locale" :value="t('defaultLocale')" />
                            <select
                                id="default_locale"
                                v-model="form.default_locale"
                                class="w-full px-3 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 text-gray-600 dark:text-slate-100
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option
                                    v-for="item in localeOptions"
                                    :key="item.value"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ localeLength }} / 10 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.default_locale" />
                        </div>

                        <div class="flex flex-col items-start w-full">
                            <LabelInput for="default_currency_id" :value="t('defaultCurrency')" />
                            <select
                                id="default_currency_id"
                                v-model="form.default_currency_id"
                                class="w-full px-3 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 text-gray-600 dark:text-slate-100
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option value="">— {{ t('selectCurrency') }} —</option>
                                <option
                                    v-for="currency in currencies"
                                    :key="currency.id"
                                    :value="currency.id"
                                >
                                    {{ currency.name }}
                                    <span v-if="currency.code">({{ currency.code }})</span>
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.default_currency_id" />
                        </div>
                    </div>

                    <!-- Заметка -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="note" :value="t('note')" />
                        </div>
                        <MetaDescTextarea
                            id="note"
                            v-model="form.note"
                            class="w-full"
                        />
                        <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                            {{ noteLength }} / 255 {{ t('characters') }}
                        </div>
                        <InputError class="mt-2" :message="form.errors.note" />
                    </div>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.marketStorefronts.index')"
                            class="mb-3"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="mb-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
