<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Редактирование тарифного плана (SubscriptionPlan)
 *
 * Паттерн как в Edit.vue:
 * - useForm + transform()
 * - toast уведомления
 * - автогенерация slug по фокусу
 * - генерация/очистка мета-полей
 * - MultiImageEdit + MultiImageUpload
 * - deletedImages
 * - forceFormData: true (из-за файлов)
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import ClearMetaButton from '@/Components/Admin/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'

import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'

import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputMoney from '@/Components/Admin/Input/InputMoney.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'

import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'

import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/Image/MultiImageEdit.vue'

import VueMultiselect from 'vue-multiselect'

// --- i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из контроллера:
 * return Inertia::render('Admin/SubscriptionPlans/Edit', [
 *   'subscriptionPlan' => new SubscriptionPlanResource($plan),
 *   'currencies'       => CurrencyResource::collection($currencies),
 *   'currentLocale'    => $currentLocale,
 *   'availableLocales' => $this->availableLocales,
 * ]);
 */
const props = defineProps({
    subscriptionPlan: { type: Object, required: true },
    currencies: { type: Array, default: () => [] },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
})

/** Форматирование дат */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d.getTime())) return ''
    return d.toISOString().split('T')[0]
}

/** Форма редактирования */
const form = useForm({
    _method: 'PUT',

    // управление
    activity: Boolean(props.subscriptionPlan.activity),
    sort: props.subscriptionPlan.sort ?? 0,

    // витрина / локаль
    locale: props.subscriptionPlan.locale ?? (props.currentLocale || ''),
    title: props.subscriptionPlan.title ?? '',
    slug: props.subscriptionPlan.slug ?? '',
    subtitle: props.subscriptionPlan.subtitle ?? '',
    short: props.subscriptionPlan.short ?? '',
    description: props.subscriptionPlan.description ?? '',

    // SEO
    meta_title: props.subscriptionPlan.meta_title ?? '',
    meta_keywords: props.subscriptionPlan.meta_keywords ?? '',
    meta_desc: props.subscriptionPlan.meta_desc ?? '',

    // публикация / доступность
    published_at: formatDate(props.subscriptionPlan.published_at),
    available_from: formatDate(props.subscriptionPlan.available_from),
    available_until: formatDate(props.subscriptionPlan.available_until),

    // биллинг
    billing_period: props.subscriptionPlan.billing_period ?? 'month',
    interval: props.subscriptionPlan.interval ?? 1,
    currency_id: props.subscriptionPlan.currency_id ?? props.subscriptionPlan.currency?.id ?? null,
    price: props.subscriptionPlan.price ?? '',
    trial_days: props.subscriptionPlan.trial_days ?? '',
    auto_renew: Boolean(props.subscriptionPlan.auto_renew),

    // провайдер оплаты
    provider: props.subscriptionPlan.provider ?? '',
    provider_ref: props.subscriptionPlan.provider_ref ?? '',
    provider_payload: props.subscriptionPlan.provider_payload ?? null,
    config: props.subscriptionPlan.config ?? null,

    // изображения
    deletedImages: [],
})

/** Универсальный лимит для options */
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

/** options валют (VueMultiselect). */
const currencyOptions = computed(() =>
    (props.currencies || []).map(cur => {
        const code = cur.code || `#${cur.id}`
        const name = cur.name ? ` — ${cur.name}` : ''
        const symbol = cur.symbol ? ` (${cur.symbol})` : ''
        return {
            id: cur.id,
            label: `${code}${symbol}${name}`,
        }
    })
)

/** Выбранная валюта в multiselect */
const selectedCurrency = ref(
    currencyOptions.value.find(o => o.id === (props.subscriptionPlan.currency_id ??
        props.subscriptionPlan.currency?.id)) ?? null
)

/** Синхронизируем выбранную валюту в форму */
watch(selectedCurrency, (val) => {
    form.currency_id = val?.id ?? null
})

/** Новые изображения */
const newImages = ref([])

/** Обновление существующих изображений */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images
}

/** Удаление существующих изображений */
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }
    existingImages.value = existingImages.value.filter(img => img.id !== deletedId)
}

/** Обновление новых изображений */
const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

/** Существующие изображения плана */
const existingImages = ref(
    (props.subscriptionPlan.images || [])
        .filter(img => img.url)
        .map(img => ({
            id: img.id,
            url: img.webp_url || img.url,
            order: img.order || 0,
            alt: img.alt || '',
            caption: img.caption || '',
        }))
)

/** Автогенерация slug по фокусу */
const handleSlugFocus = () => {
    if (form.title && !form.slug) {
        form.slug = transliterate(form.title.toLowerCase())
    }
}

/** Обрезка текста для мета-тегов */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    const truncated = text.substr(0, text.lastIndexOf(' ', maxLength))
    return addEllipsis ? `${truncated}...` : truncated
}

/** Очистка мета-полей */
const clearMetaFields = () => {
    form.meta_title = ''
    form.meta_keywords = ''
    form.meta_desc = ''
}

/** Генерация meta-полей */
const generateMetaFields = () => {
    if (form.title && !form.meta_title) {
        form.meta_title = truncateText(form.title, 160)
    }

    if (!form.meta_keywords && form.short) {
        let text = form.short.replace(/(<([^>]+)>)/gi, '')
        text = text.replace(/[.,!?;:()\[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        form.meta_keywords = truncateText(words.join(', '), 255)
    }

    if (form.short && !form.meta_desc) {
        const descText = form.short.replace(/(<([^>]+)>)/gi, '')
        form.meta_desc = truncateText(descText, 255, true)
    }
}

/** Цена: допускаем строку "12,50" -> "12.50" */
const toMoneyString = (val) => {
    if (val === null || typeof val === 'undefined') return null
    if (typeof val === 'string') {
        const v = val.trim()
        if (v === '') return null
        return v.replace(',', '.')
    }
    return String(val)
}

/** JSON helper: строку JSON -> объект, иначе как есть */
const toJsonOrNull = (val) => {
    if (val === '' || val === null || typeof val === 'undefined') return null
    if (typeof val === 'object') return val
    if (typeof val === 'string') {
        const trimmed = val.trim()
        if (!trimmed) return null
        try {
            return JSON.parse(trimmed)
        } catch (e) {
            // оставляем строкой — пусть валидатор решает
            return trimmed
        }
    }
    return val
}

/** Дата: <input type="date"> -> "YYYY-MM-DD" либо null */
const toDateStringOrNull = (val) => {
    if (!val) return null
    return String(val)
}

/** submit */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,

            // булевые/числовые
            activity: data.activity ? 1 : 0,
            auto_renew: data.auto_renew ? 1 : 0,

            sort: Number.isFinite(Number(data.sort)) ? Number(data.sort) : 0,
            interval: Number.isFinite(Number(data.interval)) ? Number(data.interval) : 1,
            trial_days:
                data.trial_days === '' || data.trial_days === null
                    ? null
                    : Number(data.trial_days),

            currency_id: data.currency_id ? Number(data.currency_id) : null,

            // даты
            published_at: toDateStringOrNull(data.published_at),
            available_from: toDateStringOrNull(data.available_from),
            available_until: toDateStringOrNull(data.available_until),

            // цена
            price: toMoneyString(data.price),

            // JSON
            provider_payload: toJsonOrNull(data.provider_payload),
            config: toJsonOrNull(data.config),

            // изображения: новые + существующие (только мета), + удалённые
            images: [
                ...newImages.value.map(img => ({
                    file: img.file,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                })),
                ...existingImages.value.map(img => ({
                    id: img.id,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                })),
            ],
            deletedImages: form.deletedImages,
        }
    })

    form.post(route('admin.subscriptionPlans.update', props.subscriptionPlan.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Тарифный план успешно обновлён!'),
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении тарифного плана:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || (t('checkForm') || 'Пожалуйста, проверьте правильность заполнения полей.'))
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editSubscriptionPlan')">
        <template #header>
            <TitlePage>
                {{ t('editSubscriptionPlan') }} -
                {{ props.subscriptionPlan.title }} [ID: {{ props.subscriptionPlan.id }}]
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
                    <!-- Назад -->
                    <DefaultButton :href="route('admin.subscriptionPlans.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" enctype="multipart/form-data"
                      class="p-3 w-full">
                    <!-- Активность, локаль, сортировка -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')"
                                           class="text-sm h-8 flex items-center" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.activity" />
                        </div>

                        <div class="flex flex-row items-center gap-2 w-auto">
                            <SelectLocale v-model="form.locale"
                                          :errorMessage="form.errors.locale" />
                            <InputError class="mt-2 lg:mt-0"
                                        :message="form.errors.locale" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            </div>
                            <InputNumber
                                id="sort"
                                type="number"
                                min="0"
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <!-- Валюта + цена + trial + auto_renew -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="currency_id">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
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
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
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
                            <LabelInput for="trial_days">{{ t('trialDays') }}</LabelInput>
                            <InputNumber id="trial_days" type="number" min="0"
                                         v-model="form.trial_days"
                                         class="w-full" />
                            <InputError class="mt-2" :message="form.errors.trial_days" />
                        </div>

                        <div class="flex flex-row items-center gap-2 mt-6 lg:mt-7">
                            <ActivityCheckbox v-model="form.auto_renew" />
                            <LabelCheckbox for="auto_renew" :text="t('autoRenew')"
                                           class="text-sm h-8 flex items-center" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.auto_renew" />
                        </div>
                    </div>

                    <!-- Биллинг: период + интервал -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="billing_period">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('billingPeriod') }}
                            </LabelInput>

                            <select
                                id="billing_period"
                                v-model="form.billing_period"
                                class="block w-full py-0.5 border-slate-500 text-md
                                       focus:border-indigo-500 focus:ring-indigo-300
                                       rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
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
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('intervalPeriod') }}
                            </LabelInput>
                            <InputNumber id="interval" type="number" min="1"
                                         v-model="form.interval" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.interval" />
                        </div>
                    </div>

                    <!-- Даты -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="published_at" :value="t('publishedAt')" />
                            <InputText id="published_at" type="date" v-model="form.published_at"
                                       class="w-full max-w-xs" />
                            <InputError class="mt-2" :message="form.errors.published_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="available_from" :value="t('shortStarted')" />
                            <InputText id="available_from" type="date"
                                       v-model="form.available_from"
                                       class="w-full max-w-xs" />
                            <InputError class="mt-2" :message="form.errors.available_from" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="available_until" :value="t('shortExpires')" />
                            <InputText id="available_until" type="date"
                                       v-model="form.available_until"
                                       class="w-full max-w-xs" />
                            <InputError class="mt-2" :message="form.errors.available_until" />
                        </div>
                    </div>

                    <!-- Название -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="title">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('title') }}
                        </LabelInput>
                        <InputText id="title" type="text"
                                   v-model="form.title" required autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Slug -->
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
                            @focus="handleSlugFocus"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.slug" />
                    </div>

                    <!-- Подзаголовок -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="subtitle" :value="t('subtitle')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.subtitle.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.subtitle" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.subtitle" />
                    </div>

                    <!-- Краткое описание -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="short" :value="t('shortDescription')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.short.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.short" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.short" />
                    </div>

                    <!-- Описание -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="description" :value="t('description')" />
                        <TinyEditor v-model="form.description" :height="500" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <!-- Провайдер -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="provider" :value="t('provider')" />
                            <InputText id="provider" type="text"
                                       v-model="form.provider" autocomplete="off" />
                            <InputError class="mt-2" :message="form.errors.provider" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="provider_ref" :value="t('providerRef')" />
                            <InputText id="provider_ref" type="text"
                                       v-model="form.provider_ref" autocomplete="off" />
                            <InputError class="mt-2" :message="form.errors.provider_ref" />
                        </div>
                    </div>

                    <!-- provider_payload / config -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="provider_payload" :value="t('providerPayload')" />
                            <textarea
                                id="provider_payload"
                                v-model="form.provider_payload"
                                class="w-full min-h-[120px] rounded
                                       border border-slate-300 dark:border-slate-600
                                       bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100"
                                placeholder='{"key":"value"}'
                            />
                            <InputError class="mt-2" :message="form.errors.provider_payload" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="config" :value="t('config')" />
                            <textarea
                                id="config"
                                v-model="form.config"
                                class="w-full min-h-[120px] rounded
                                       border border-slate-300 dark:border-slate-600
                                       bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-100"
                                placeholder='{"key":"value"}'
                            />
                            <InputError class="mt-2" :message="form.errors.config" />
                        </div>
                    </div>

                    <!-- Мета Title -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_title" :value="t('metaTitle')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_title.length }} / 160 {{ t('characters') }}
                            </div>
                        </div>
                        <InputText id="meta_title" type="text"
                                   v-model="form.meta_title" maxlength="160"
                                   autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.meta_title" />
                    </div>

                    <!-- Мета Keywords -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_keywords" :value="t('metaKeywords')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_keywords.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <InputText id="meta_keywords" type="text"
                                   v-model="form.meta_keywords" maxlength="255"
                                   autocomplete="off" />
                        <InputError class="mt-2" :message="form.errors.meta_keywords" />
                    </div>

                    <!-- Мета Description -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_desc" :value="t('metaDescription')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_desc.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.meta_desc"
                                          maxlength="255" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.meta_desc" />
                    </div>

                    <!-- Кнопки мета-полей -->
                    <div class="flex justify-end mt-4">
                        <ClearMetaButton @clear="clearMetaFields" class="mr-4">
                            <template #default>
                                <svg class="w-4 h-4 fill-current text-gray-500 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3 9H5V7h6v2z" />
                                </svg>
                                {{ t('clearMetaFields') }}
                            </template>
                        </ClearMetaButton>

                        <MetatagsButton @click.prevent="generateMetaFields">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z" />
                                </svg>
                            </template>
                            {{ t('generateMetaTags') }}
                        </MetatagsButton>
                    </div>

                    <!-- Редактирование существующих изображений -->
                    <div class="mt-4">
                        <MultiImageEdit
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete-image="handleDeleteExistingImage"
                        />
                    </div>

                    <!-- Загрузка новых изображений -->
                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                        <InputError class="mt-2" :message="form.errors.images" />
                    </div>

                    <!-- Кнопки сохранить/назад -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton :href="route('admin.subscriptionPlans.index')"
                                       class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton class="mb-0" :class="{ 'opacity-25': form.processing }"
                                       :disabled="form.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
                                     viewBox="0 0 16 16">
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

<style src="/resources/css/vue-multiselect.min.css"></style>
