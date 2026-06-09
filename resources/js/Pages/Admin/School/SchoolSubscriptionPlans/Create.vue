<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание тарифного плана школы
 */
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'

import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputMoney from '@/Components/Admin/UI/Input/InputMoney.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    currencies: { type: Array, default: () => [] },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
})

// Шаблон перевода для новой локали
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// Локаль по умолчанию
const defaultLocale = props.currentLocale || 'ru'

// Текущая активная локаль в переводах
const activeLocale = ref(defaultLocale)

// Форма создания тарифного плана
const form = useForm({
    activity: true,
    sort: 0,

    slug: '',

    published_at: '',
    available_from: '',
    available_until: '',

    billing_period: 'month',
    interval: 1,
    currency_id: null,
    price: '',
    trial_days: '',
    auto_renew: true,

    provider: '',
    provider_ref: '',
    provider_payload: '',
    config: '',

    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

// Текущий перевод активной локали
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Получение ошибки поля перевода для активной локали
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Ограничение количества элементов в выпадающем списке
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

// Список валют для VueMultiselect
const currencyOptions = computed(() =>
    (props.currencies || []).map(currency => {
        const code = currency.code || `#${currency.id}`
        const name = currency.name ? ` — ${currency.name}` : ''
        const symbol = currency.symbol ? ` (${currency.symbol})` : ''

        return {
            id: currency.id,
            label: `${code}${symbol}${name}`,
        }
    })
)

// Выбранная валюта
const selectedCurrency = ref(null)

// Синхронизация выбранной валюты с формой
watch(selectedCurrency, (value) => {
    form.currency_id = value?.id ?? null
})

// Новые изображения для загрузки
const newImages = ref([])

// Обновление списка новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// Автоматическая генерация slug из заголовка
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

// Обрезка текста до заданной длины
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''
    if (text.length <= maxLength) return text

    const cut = text.substr(0, text.lastIndexOf(' ', maxLength))

    return addEllipsis ? `${cut}...` : cut
}

// Удаление HTML-тегов из текста
const stripHtml = (value) => {
    return String(value || '').replace(/(<([^>]+)>)/gi, '')
}

// Очистка SEO-полей
const clearMetaFields = () => {
    currentTranslation.value.meta_title = ''
    currentTranslation.value.meta_keywords = ''
    currentTranslation.value.meta_desc = ''
}

// Автоматическая генерация SEO-полей
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 160)
    }

    if (translation.short && !translation.meta_keywords) {
        let text = stripHtml(translation.short)
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        translation.meta_keywords = truncateText(words.join(', '), 255)
    }

    if (translation.short && !translation.meta_desc) {
        translation.meta_desc = truncateText(stripHtml(translation.short), 255, true)
    }
}

// Подготовка денежного значения для отправки
const toMoneyString = (value) => {
    if (value === null || typeof value === 'undefined') return null

    if (typeof value === 'string') {
        const prepared = value.trim()

        if (prepared === '') return null

        return prepared.replace(',', '.')
    }

    return String(value)
}

// Преобразование JSON-строки в объект
const toJsonOrNull = (value) => {
    if (value === '' || value === null || typeof value === 'undefined') return null
    if (typeof value === 'object') return value

    if (typeof value === 'string') {
        const prepared = value.trim()

        if (!prepared) return null

        try {
            return JSON.parse(prepared)
        } catch (e) {
            return prepared
        }
    }

    return value
}

// Подготовка даты для отправки
const toDateOrNull = (value) => {
    return value ? String(value) : null
}

// Отправка формы создания тарифного плана
const submit = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            activity: data.activity ? 1 : 0,
            auto_renew: data.auto_renew ? 1 : 0,

            sort: data.sort === '' || data.sort === null ? 0 : Number(data.sort),
            interval: data.interval === '' || data.interval === null ? 1 : Number(data.interval),
            trial_days: data.trial_days === '' || data.trial_days === null
                ? null
                : Number(data.trial_days),

            currency_id: selectedCurrency.value?.id ?? null,

            published_at: toDateOrNull(data.published_at),
            available_from: toDateOrNull(data.available_from),
            available_until: toDateOrNull(data.available_until),

            provider_payload: toJsonOrNull(data.provider_payload),
            config: toJsonOrNull(data.config),

            price: toMoneyString(data.price),
        }

        delete transformed.images

        newImages.value.forEach((image, index) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
        })

        return transformed
    })

    form.post(route('admin.schoolSubscriptionPlans.store'), {
        errorBag: 'createSchoolSubscriptionPlan',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Тарифный план успешно создан!'),
        onError: (errors) => {
            console.error('Ошибка создания тарифного плана:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createSubscriptionPlan')">
        <template #header>
            <TitlePage>{{ t('createSubscriptionPlan') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolSubscriptionPlans.index')">
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

                <form
                    @submit.prevent="submit"
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                >
                    <div class="pb-12">
                        <div class="mb-3 flex justify-between flex-col
                                    lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                                <InputError :message="form.errors.activity" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    min="0"
                                    v-model.number="form.sort"
                                    class="w-full lg:w-28"
                                />
                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-4 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="currency_id">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">
                                        *
                                    </span>
                                    {{ t('currency') }}
                                </LabelInput>

                                <VueMultiselect
                                    id="currency_id"
                                    v-model="selectedCurrency"
                                    :options="currencyOptions"
                                    :options-limit="dynamicOptionsLimit(currencyOptions)"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :allow-empty="true"
                                    :placeholder="t('select')"
                                    label="label"
                                    track-by="id"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.currency_id" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="price">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">
                                        *
                                    </span>
                                    {{ t('price') }}
                                </LabelInput>

                                <InputMoney
                                    id="price"
                                    v-model="form.price"
                                    :min="0"
                                    :step="0.01"
                                    :fraction-digits="2"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="trial_days" :value="t('trialDays')" />

                                <InputNumber
                                    id="trial_days"
                                    type="number"
                                    min="0"
                                    v-model="form.trial_days"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.trial_days" />
                            </div>

                            <div class="flex flex-row items-center gap-2 mt-2 lg:mt-5">
                                <ActivityCheckbox v-model="form.auto_renew" />
                                <LabelCheckbox
                                    for="auto_renew"
                                    :text="t('autoRenew')"
                                    class="text-sm h-8 flex items-center"
                                />
                                <InputError :message="form.errors.auto_renew" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="billing_period">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">
                                        *
                                    </span>
                                    {{ t('billingPeriod') }}
                                </LabelInput>

                                <select
                                    id="billing_period"
                                    v-model="form.billing_period"
                                    class="block w-full py-0.5 border-slate-500 text-md
                                           focus:border-indigo-500 focus:ring-indigo-300
                                           rounded-sm shadow-sm dark:bg-cyan-800
                                           dark:text-slate-100"
                                >
                                    <option value="day">{{ t('days') }}</option>
                                    <option value="week">{{ t('weeks') }}</option>
                                    <option value="month">{{ t('months') }}</option>
                                    <option value="year">{{ t('years') }}</option>
                                </select>

                                <InputError class="mt-2" :message="form.errors.billing_period" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="interval">
                                    <span class="text-red-500 dark:text-red-300 font-semibold">
                                        *
                                    </span>
                                    {{ t('intervalPeriod') }}
                                </LabelInput>

                                <InputNumber
                                    id="interval"
                                    type="number"
                                    min="1"
                                    v-model.number="form.interval"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.interval" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="published_at" :value="t('publishedAt')" />
                                <InputText
                                    id="published_at"
                                    type="date"
                                    v-model="form.published_at"
                                    class="w-full max-w-xs"
                                />
                                <InputError class="mt-2" :message="form.errors.published_at" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="available_from" :value="t('shortStarted')" />
                                <InputText
                                    id="available_from"
                                    type="date"
                                    v-model="form.available_from"
                                    class="w-full max-w-xs"
                                />
                                <InputError class="mt-2" :message="form.errors.available_from" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="available_until" :value="t('shortExpires')" />
                                <InputText
                                    id="available_until"
                                    type="date"
                                    v-model="form.available_until"
                                    class="w-full max-w-xs"
                                />
                                <InputError class="mt-2" :message="form.errors.available_until" />
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="slug">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('slug') }}
                            </LabelInput>

                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                autocomplete="off"
                                class="w-full"
                                required
                                @focus="handleSlugFocus"
                            />

                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <div class="mb-4 rounded-sm border border-slate-300
                                    p-3 dark:border-slate-600">
                            <TranslationTabs
                                v-model="activeLocale"
                                :translations="form.translations"
                                :available-locales="availableLocales"
                                :make-translation="makeTranslation"
                                @update:translations="form.translations = $event"
                                @removed="toast.warning('Перевод удалён.')"
                                @added="toast.success('Локаль добавлена.')"
                            />

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput for="title">
                                        <span class="text-red-500 dark:text-red-300 font-semibold">
                                            *
                                        </span>
                                        {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                                    </LabelInput>

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                        {{ (currentTranslation.title || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="title"
                                    type="text"
                                    v-model="currentTranslation.title"
                                    maxlength="255"
                                    required
                                    autocomplete="off"
                                />

                                <InputError class="mt-2" :message="getError('title')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="subtitle"
                                    :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                />

                                <MetaDescTextarea
                                    v-model="currentTranslation.subtitle" class="w-full" />

                                <InputError class="mt-2" :message="getError('subtitle')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="short"
                                :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />

                                <MetaDescTextarea
                                    v-model="currentTranslation.short" class="w-full" />

                                <InputError class="mt-2" :message="getError('short')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="description"
                                    :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                                />

                                <TinyEditor
                                    v-model="currentTranslation.description"
                                    :height="500"
                                />

                                <InputError class="mt-2" :message="getError('description')" />
                            </div>

                            <div class="mt-4 border-t border-dashed border-slate-400 pt-4">
                                <div class="mb-3 flex items-center justify-end gap-2">
                                    <ClearMetaButton type="button" @click="clearMetaFields">
                                        <template #default>
                                            {{ t('clearMetaFields') }}
                                        </template>
                                    </ClearMetaButton>
                                    <MetatagsButton type="button" @click="generateMetaFields">
                                        <template #icon>
                                            <svg
                                                class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2"
                                                viewBox="0 0 16 16"
                                            >
                                                <path
                                                    d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z"
                                                />
                                            </svg>
                                        </template>
                                        {{ t('generateMetaTags') }}
                                    </MetatagsButton>
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
                                        class="w-full"
                                    />

                                    <InputError class="mt-2"
                                                :message="getError('meta_title')" />
                                </div>

                                <div class="mb-3 flex flex-col items-start">
                                    <LabelInput
                                        for="meta_keywords"
                                    :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <MetaDescTextarea
                                        v-model="currentTranslation.meta_keywords"
                                        class="w-full"
                                    />

                                    <InputError class="mt-2"
                                                :message="getError('meta_keywords')" />
                                </div>

                                <div class="mb-3 flex flex-col items-start">
                                    <LabelInput
                                        for="meta_desc"
                                :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <MetaDescTextarea
                                        v-model="currentTranslation.meta_desc"
                                        class="w-full"
                                    />

                                    <InputError class="mt-2"
                                                :message="getError('meta_desc')" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="provider" :value="t('provider')" />

                                <InputText
                                    id="provider"
                                    type="text"
                                    v-model="form.provider"
                                    autocomplete="off"
                                />

                                <InputError class="mt-2" :message="form.errors.provider" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="provider_ref" :value="t('providerRef')" />

                                <InputText
                                    id="provider_ref"
                                    type="text"
                                    v-model="form.provider_ref"
                                    autocomplete="off"
                                />

                                <InputError class="mt-2" :message="form.errors.provider_ref" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="provider_payload" :value="t('providerPayload')" />

                                <MetaDescTextarea
                                    id="provider_payload"
                                    v-model="form.provider_payload"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.provider_payload" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="config" :value="t('config')" />

                                <MetaDescTextarea
                                    id="config"
                                    v-model="form.config"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.config" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <MultiImageUpload @update:images="handleNewImagesUpdate" />

                            <div
                                v-if="newImages.length"
                                class="text-xs text-slate-600 dark:text-slate-300 mt-2"
                            >
                                {{ t('images') }}: {{ newImages.length }}
                            </div>

                            <InputError class="mt-2" :message="form.errors.images" />
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.schoolSubscriptionPlans.index')">
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
                            type="submit"
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

<style src="/resources/css/vue-multiselect.min.css"></style>
