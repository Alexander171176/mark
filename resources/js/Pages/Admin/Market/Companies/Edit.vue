<script setup>
/**
 * @version PulsarCMS 1.0
 * Редактирование компании (MarketCompany)
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
    company: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    _method: 'PUT',

    sort: props.company.sort ?? 0,
    activity: Boolean(props.company.activity),

    name: props.company.name ?? '',
    brand_name: props.company.brand_name ?? '',
    legal_name: props.company.legal_name ?? '',

    slug: props.company.slug ?? '',
    external_id: props.company.external_id ?? '',

    company_type: props.company.company_type ?? '',
    tax_regime: props.company.tax_regime ?? '',
    bin_iin: props.company.bin_iin ?? '',

    email: props.company.email ?? '',
    phone: props.company.phone ?? '',

    messenger_type: props.company.messenger_type ?? '',
    messenger_contact: props.company.messenger_contact ?? '',

    country: props.company.country ?? 'KZ',
    city: props.company.city ?? '',

    legal_address: props.company.legal_address ?? '',
    actual_address: props.company.actual_address ?? '',
})

const messengerOptions = [
    { value: '', label: '—' },
    { value: 'whatsapp', label: 'WhatsApp' },
    { value: 'telegram', label: 'Telegram' },
    { value: 'wechat', label: 'WeChat' },
    { value: 'viber', label: 'Viber' },
]

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
    }))

    form.post(route('admin.marketCompanies.update', props.company.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Компания успешно обновлена!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении компании:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}

const nameLength = computed(() => form.name?.length || 0)
const brandNameLength = computed(() => form.brand_name?.length || 0)
const legalNameLength = computed(() => form.legal_name?.length || 0)
const slugLength = computed(() => form.slug?.length || 0)
const externalIdLength = computed(() => form.external_id?.length || 0)
const companyTypeLength = computed(() => form.company_type?.length || 0)
const taxRegimeLength = computed(() => form.tax_regime?.length || 0)
const binIinLength = computed(() => form.bin_iin?.length || 0)
const emailLength = computed(() => form.email?.length || 0)
const phoneLength = computed(() => form.phone?.length || 0)
const messengerContactLength = computed(() => form.messenger_contact?.length || 0)
const countryLength = computed(() => form.country?.length || 0)
const cityLength = computed(() => form.city?.length || 0)
</script>

<template>
    <AdminLayout :title="t('editMarketCompany')">
        <template #header>
            <TitlePage>
                {{ t('editMarketCompany') }} - {{ company.name }} [ID: {{ company.id }}]
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
                    <DefaultButton :href="route('admin.marketCompanies.index')">
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
                    
                    <!-- Активность и сортировка -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
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

                    <!-- Основные поля -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Название магазина -->
                        <div class="md:col-span-2 flex flex-col items-start">
                            <LabelInput for="name">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('title') }}
                            </LabelInput>
                            <InputText
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                autocomplete="name"
                                placeholder="Pulsar Market"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ nameLength }} / 255 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Название Бренда -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="brand_name" :value="t('brand')" />
                            <InputText
                                id="brand_name"
                                type="text"
                                v-model="form.brand_name"
                                autocomplete="brand_name"
                                placeholder="Pulsar"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ brandNameLength }} / 255 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.brand_name" />
                        </div>

                        <!-- Зарегистрированное имя компании -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="legal_name" :value="t('legalName')" />
                            <InputText
                                id="legal_name"
                                type="text"
                                v-model="form.legal_name"
                                autocomplete="legal_name"
                                placeholder="ТОО Pulsar Market"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ legalNameLength }} / 255 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.legal_name" />
                        </div>

                    </div>

                    <!-- Тип компании / налоги / БИН -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        <!-- Тип компании -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="company_type" :value="t('companyType')" />
                            <InputText
                                id="company_type"
                                type="text"
                                v-model="form.company_type"
                                autocomplete="company_type"
                                placeholder="ИП, ТОО, LLC"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ companyTypeLength }} / 64 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.company_type" />
                        </div>

                        <!-- БИН -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="bin_iin" value="БИН / ИИН" />
                            <InputText
                                id="bin_iin"
                                type="text"
                                v-model="form.bin_iin"
                                autocomplete="bin_iin"
                                placeholder="888888000000"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ binIinLength }} / 32 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.bin_iin" />
                        </div>

                        <!-- Налоговый режим -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="tax_regime" :value="t('taxRegime')" />
                            <InputText
                                id="tax_regime"
                                type="text"
                                v-model="form.tax_regime"
                                autocomplete="tax_regime"
                                placeholder="НДС, УСН"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ taxRegimeLength }} / 64 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.tax_regime" />
                        </div>

                    </div>

                    <!-- Email, Телефон -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Телефон -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="phone" :value="t('phone')" />
                            <InputText
                                id="phone"
                                type="text"
                                v-model="form.phone"
                                autocomplete="phone"
                                placeholder="+7 777 000 00 00"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ phoneLength }} / 32 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="email" value="Email" />
                            <InputText
                                id="email"
                                type="email"
                                v-model="form.email"
                                autocomplete="email"
                                placeholder="company@example.com"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ emailLength }} / 191 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                    </div>

                    <!-- Мессенджеры -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start w-full">
                            <LabelInput for="messenger_type" :value="t('messengerType')" />
                            <select
                                id="messenger_type"
                                v-model="form.messenger_type"
                                class="w-full px-3 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 text-gray-600 dark:text-slate-100
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option
                                    v-for="item in messengerOptions"
                                    :key="item.value"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.messenger_type" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="messenger_contact" :value="t('messengerContact')" />
                            <InputText
                                id="messenger_contact"
                                type="text"
                                v-model="form.messenger_contact"
                                autocomplete="messenger_contact"
                                placeholder="Номер или username"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ messengerContactLength }} / 191 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.messenger_contact" />
                        </div>
                    </div>

                    <!-- Локация -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Страна -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="country" :value="t('country')" />
                            <InputText
                                id="country"
                                type="text"
                                v-model="form.country"
                                maxlength="2"
                                autocomplete="country"
                                placeholder="KZ"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ countryLength }} / 2 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.country" />
                        </div>

                        <!-- Город -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="city" :value="t('city')" />
                            <InputText
                                id="city"
                                type="text"
                                v-model="form.city"
                                autocomplete="city"
                                placeholder="Алматы"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ cityLength }} / 128 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.city" />
                        </div>

                    </div>

                    <!-- Адрес регистрации компании -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="legal_address" :value="t('addressLegal')" />
                        </div>
                        <MetaDescTextarea
                            id="legal_address"
                            v-model="form.legal_address"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.legal_address" />
                    </div>

                    <!-- Актуальный адрес -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="actual_address" :value="t('addressActual')" />
                        </div>
                        <MetaDescTextarea
                            id="actual_address"
                            v-model="form.actual_address"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.actual_address" />
                    </div>

                    <!-- Slug и внешний ID -->
                    <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="slug" :value="t('slug')" />
                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                autocomplete="slug"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ slugLength }} / 191 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="external_id" value="ID" />
                            <InputText
                                id="external_id"
                                type="text"
                                v-model="form.external_id"
                                autocomplete="external_id"
                                placeholder="ID"
                            />
                            <div class="w-full flex justify-end text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ externalIdLength }} / 191 {{ t('characters') }}
                            </div>
                            <InputError class="mt-2" :message="form.errors.external_id" />
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.marketCompanies.index')"
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
