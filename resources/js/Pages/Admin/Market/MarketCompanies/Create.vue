<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание Комании (MarketCompany)
 */
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { transliterate } from '@/utils/transliteration'
import { useForm, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'

import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'

import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'
import ImageFileInput from '@/Components/Admin/UI/File/ImageFileInput.vue'
import GeoCoordinatesInput from '@/Components/Admin/UI/Geo/GeoCoordinatesInput.vue'

/** Сервисы страницы */
const toast = useToast()
const { t } = useI18n()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Шаблон нового перевода */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/** Текущая локаль формы */
const defaultLocale = props.currentLocale || 'ru'
const activeLocale = ref(defaultLocale)

/** Форма создания компании */
const form = useForm({
    user_id: page.props?.auth?.user?.id || null,

    url: '',
    company_type: 'company',
    bin_iin: '',
    legal_name: '',
    director_name: '',

    email: '',
    phone: '',
    website: '',

    logo: null,
    signature: null,
    stamp: null,

    country: 'Казахстан',
    region: '',
    city: '',
    legal_address: '',
    actual_address: '',
    latitude: '',
    longitude: '',

    bank_name: '',
    bank_account: '',
    bank_account_secondary: '',
    bank_bik: '',
    bank_iban: '',

    vat_enabled: false,
    vat_rate: '',

    social_links: {
        instagram: '',
        whatsapp: '',
        telegram: '',
        facebook: '',
        youtube: '',
    },

    sort: 0,
    activity: false,
    left: false,
    main: false,
    right: false,

    status: 'draft',
    moderation_status: 0,
    moderation_note: '',

    published_at: '',
    show_from_at: '',
    show_to_at: '',

    views: 0,

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

/** Активный перевод формы */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Ошибка поля активного перевода */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Генерация URL из названия */
const handleUrlInputFocus = () => {
    if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/** Обрезка текста без разрыва слов */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''

    const str = String(text)

    if (str.length <= maxLength) return str

    const lastSpaceIndex = str.lastIndexOf(' ', maxLength)
    const truncated = lastSpaceIndex === -1
        ? str.substring(0, maxLength)
        : str.substring(0, lastSpaceIndex)

    return addEllipsis ? `${truncated}...` : truncated
}

/** Генерация SEO-полей */
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 255)
    }

    if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, '')
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        translation.meta_keywords = truncateText(words.join(', '), 255)
    }

    if (translation.short && !translation.meta_desc) {
        const descText = String(translation.short).replace(/(<([^>]+)>)/gi, '')
        translation.meta_desc = truncateText(descText, 200, true)
    }
}

/** Отправка формы */
const submitForm = () => {
    form.transform((data) => ({
        ...data,

        activity: data.activity ? 1 : 0,
        left: data.left ? 1 : 0,
        main: data.main ? 1 : 0,
        right: data.right ? 1 : 0,

        vat_enabled: data.vat_enabled ? 1 : 0,
        vat_rate: data.vat_enabled && data.vat_rate !== ''
            ? data.vat_rate
            : null,

        social_links: Object.fromEntries(
            Object.entries(data.social_links || {})
                .filter(([, value]) => String(value || '').trim() !== '')
        ),
    }))

    form.post(route('admin.marketCompanies.store'), {
        forceFormData: true,
        errorBag: 'createMarketCompany',
        preserveScroll: true,

        onSuccess: () => toast.success('Компания успешно создана!'),

        onError: (errors) => {
            console.error('Не удалось отправить форму:', errors)

            const firstError = errors?.[Object.keys(errors)[0]]

            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('addMarketCompany')">
        <template #header>
            <TitlePage>{{ t('addMarketCompany') }}</TitlePage>
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
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full">
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber
                                id="sort"
                                type="number"
                                v-model="form.sort"
                                class="w-full lg:w-28"
                            />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.left" />
                            <LabelCheckbox
                                for="left"
                                :text="t('left')"
                                class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.main" />
                            <LabelCheckbox
                                for="main"
                                :text="t('main')"
                                class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.right" />
                            <LabelCheckbox
                                for="right"
                                :text="t('right')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                    </div>

                    <div
                        class="my-5 p-3 border border-slate-300 dark:border-slate-500
                               bg-white dark:bg-slate-800 rounded-sm"
                    >
                        <TranslationTabs
                            v-model="activeLocale"
                            :translations="form.translations"
                            :available-locales="availableLocales"
                            :make-translation="makeTranslation"
                            @update:translations="form.translations = $event"
                            @removed="toast.warning(t('translationRemoved'))"
                            @added="toast.success(t('localeAdded'))"
                        />

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="title">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText
                                id="title"
                                type="text"
                                v-model="currentTranslation.title"
                                required
                                maxlength="255"
                            />

                            <InputError class="mt-2" :message="getError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="subtitle"
                                :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="subtitle"
                                type="text"
                                v-model="currentTranslation.subtitle"
                                maxlength="255"
                            />

                            <InputError class="mt-2" :message="getError('subtitle')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="short"
                                :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />

                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                        {{ (currentTranslation.short || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <MetaDescTextarea v-model="currentTranslation.short" class="w-full" />
                            <InputError class="mt-2" :message="getError('short')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="description"
                                :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                            />

                            <TinyEditor v-model="currentTranslation.description" :height="400" />
                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="meta_title"
                                :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="meta_title"
                                type="text"
                                v-model="currentTranslation.meta_title"
                                maxlength="255"
                            />

                            <InputError class="mt-2" :message="getError('meta_title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="meta_keywords"
                                :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="meta_keywords"
                                type="text"
                                v-model="currentTranslation.meta_keywords"
                                maxlength="255"
                            />

                            <InputError class="mt-2" :message="getError('meta_keywords')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="meta_desc"
                                :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                            />

                            <MetaDescTextarea
                                v-model="currentTranslation.meta_desc" class="w-full" />
                            <InputError class="mt-2" :message="getError('meta_desc')" />
                        </div>

                        <div class="flex justify-end mt-4">
                            <MetatagsButton @click.prevent="generateMetaFields">
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="url">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                URL
                            </LabelInput>

                            <InputText
                                id="url"
                                type="text"
                                v-model="form.url"
                                required
                                @focus="handleUrlInputFocus"
                            />

                            <InputError class="mt-2" :message="form.errors.url" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="company_type">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('companyType') }}
                            </LabelInput>

                            <select
                                id="company_type"
                                v-model="form.company_type"
                                class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option value="company">{{ t('company') }}</option>
                                <option value="entrepreneur">{{ t('soleProprietor') }}</option>
                                <option value="individual">{{ t('individual') }}</option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.company_type" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="status" :value="t('status')" />

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option value="draft">{{ t('statusDraft') }}</option>
                                <option value="published">{{ t('statusPublished') }}</option>
                                <option value="archived">{{ t('statusArchived') }}</option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="legal_name" :value="t('legalName')" />
                            <InputText id="legal_name" type="text" v-model="form.legal_name" />
                            <InputError class="mt-2" :message="form.errors.legal_name" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="director_name" :value="t('director')" />
                            <InputText
                                id="director_name"
                                type="text" v-model="form.director_name" />
                            <InputError class="mt-2" :message="form.errors.director_name" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="bin_iin" :value="t('billingTaxId')" />
                            <InputText id="bin_iin" type="text" v-model="form.bin_iin" />
                            <InputError class="mt-2" :message="form.errors.bin_iin" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="website" :value="t('site')" />
                            <InputText id="website" type="url" v-model="form.website" />
                            <InputError class="mt-2" :message="form.errors.website" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="email" value="Email" />
                            <InputText id="email" type="email" v-model="form.email" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="phone" :value="t('phone')" />
                            <InputText id="phone" type="text" v-model="form.phone" />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="country" :value="t('country')" />
                            <InputText id="country" type="text" v-model="form.country" />
                            <InputError class="mt-2" :message="form.errors.country" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="region" :value="t('region')" />
                            <InputText id="region" type="text" v-model="form.region" />
                            <InputError class="mt-2" :message="form.errors.region" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="city" :value="t('city')" />
                            <InputText id="city" type="text" v-model="form.city" />
                            <InputError class="mt-2" :message="form.errors.city" />
                        </div>

                        <div class="flex flex-col items-start lg:col-span-3">
                            <LabelInput for="legal_address" :value="t('addressLegal')" />
                            <InputText
                                id="legal_address"
                                type="text"
                                v-model="form.legal_address" />
                            <InputError class="mt-2" :message="form.errors.legal_address" />
                        </div>

                        <div class="flex flex-col items-start lg:col-span-3">
                            <LabelInput for="actual_address" :value="t('addressActual')" />
                            <InputText
                                id="actual_address"
                                type="text"
                                v-model="form.actual_address" />
                            <InputError class="mt-2" :message="form.errors.actual_address" />
                        </div>

                        <GeoCoordinatesInput
                            v-model:latitude="form.latitude"
                            v-model:longitude="form.longitude"
                            :latitude-error="form.errors.latitude"
                            :longitude-error="form.errors.longitude"
                        />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="bank_name" :value="t('bank')" />
                            <InputText id="bank_name" type="text" v-model="form.bank_name" />
                            <InputError class="mt-2" :message="form.errors.bank_name" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="bank_bik" value="БИК" />
                            <InputText id="bank_bik" type="text" v-model="form.bank_bik" />
                            <InputError class="mt-2" :message="form.errors.bank_bik" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="bank_account"
                                :value="`${t('bankAccount')} №1`" />
                            <InputText id="bank_account" type="text" v-model="form.bank_account" />
                            <InputError class="mt-2" :message="form.errors.bank_account" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="bank_account_secondary"
                                :value="`${t('bankAccount')} №2`" />
                            <InputText
                                id="bank_account_secondary"
                                type="text"
                                v-model="form.bank_account_secondary" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.bank_account_secondary" />
                        </div>

                        <div class="flex flex-col items-start lg:col-span-2">
                            <LabelInput for="bank_iban" value="IBAN" />
                            <InputText id="bank_iban" type="text" v-model="form.bank_iban" />
                            <InputError class="mt-2" :message="form.errors.bank_iban" />
                        </div>
                    </div>

                    <div class="mb-4 flex flex-col lg:flex-row gap-4
                                items-center justify-between">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.vat_enabled" />
                            <LabelCheckbox
                                for="vat_enabled"
                                :text="t('vat')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput
                                for="vat_rate"
                                :value="t('vatRate')"
                                class="text-sm h-8 flex items-center justify-end w-full"
                            />

                            <input
                                id="vat_rate"
                                type="number"
                                step="0.01"
                                v-model="form.vat_rate"
                                :disabled="!form.vat_enabled"
                                class="w-full px-2 py-0.5
                                       border border-slate-400 dark:border-slate-600
                                       rounded-sm shadow-sm bg-white dark:bg-cyan-800
                                       dark:text-slate-100 text-gray-600 disabled:opacity-60"
                            />

                            <InputError class="mt-2" :message="form.errors.vat_rate" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                        <ImageFileInput
                            v-model="form.logo"
                            :label="t('companyLogo')"
                            :button-text="t('selectLogo')"
                            :empty-text="t('noImage')"
                            accept="image/png,image/jpeg,image/webp"
                            :error="form.errors.logo"
                            preview-class="h-24 w-36 object-cover rounded-sm
                                           border border-slate-400"
                        />

                        <ImageFileInput
                            v-model="form.signature"
                            :label="t('signaturePng')"
                            :button-text="t('selectSignature')"
                            :empty-text="t('noSignature')"
                            accept="image/png"
                            :error="form.errors.signature"
                            preview-class="h-24 w-36 object-contain rounded-sm
                                           border border-slate-400 bg-white"
                        />

                        <ImageFileInput
                            v-model="form.stamp"
                            :label="t('stampPng')"
                            :button-text="t('selectStamp')"
                            :empty-text="t('noStamp')"
                            accept="image/png"
                            :error="form.errors.stamp"
                            preview-class="h-24 w-36 object-contain rounded-sm
                                           border border-slate-400 bg-white"
                        />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="published_at" :value="t('publishedAt')" />
                            <InputText id="published_at" type="date" v-model="form.published_at" />
                            <InputError class="mt-2" :message="form.errors.published_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="show_from_at" :value="t('showFromAt')" />
                            <InputText id="show_from_at" type="datetime-local" v-model="form.show_from_at" />
                            <InputError class="mt-2" :message="form.errors.show_from_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="show_to_at" :value="t('showToAt')" />
                            <InputText id="show_to_at" type="datetime-local" v-model="form.show_to_at" />
                            <InputError class="mt-2" :message="form.errors.show_to_at" />
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.marketCompanies.index')">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
