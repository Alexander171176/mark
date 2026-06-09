<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание цены курса школы
 */
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputMoney from '@/Components/Admin/UI/Input/InputMoney.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import SelectEntity from '@/Components/Admin/UI/Select/SelectEntity.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    courses: { type: Array, default: () => [] },
    currencies: { type: Array, default: () => [] },

    defaultCourseId: { type: Number, default: null },
    defaultCurrencyId: { type: Number, default: null },
})

// Форма создания цены курса
const form = useForm({
    school_course_id: props.defaultCourseId,
    currency_id: props.defaultCurrencyId,

    price: '',
    sale_price: '',
    compare_at_price: '',

    starts_at: '',
    ends_at: '',

    activity: true,
    sort: 0,

    meta: null,
})

// Список курсов для выпадающего списка
const courseOptions = computed(() =>
    props.courses.map(course => ({
        id: course.id,
        label: [
            `[ID: ${course.id}]`,
            course.title || course.slug || `#${course.id}`,
            course.slug ? `(${course.slug})` : null,
        ].filter(Boolean).join(' '),
    }))
)

// Список валют для выпадающего списка
const currencyOptions = computed(() =>
    props.currencies.map(currency => ({
        id: currency.id,
        label: [
            currency.code || `#${currency.id}`,
            currency.name ? `— ${currency.name}` : null,
            currency.symbol ? `(${currency.symbol})` : null,
        ].filter(Boolean).join(' '),
    }))
)

// Преобразование денежного значения в строку для сохранения
const toMoneyString = (value) => {
    if (value === null || value === undefined) return null

    const stringValue = String(value).trim()

    if (stringValue === '') return null

    return stringValue.replace(',', '.')
}

// Преобразование даты в формат для сохранения
const toDateTimeStringOrNull = (value) => {
    if (!value) return null

    return String(value)
}

// Отправка формы создания цены курса
const submitForm = () => {
    form.transform((data) => ({
        ...data,

        school_course_id: data.school_course_id ? Number(data.school_course_id) : null,
        currency_id: data.currency_id ? Number(data.currency_id) : null,

        price: toMoneyString(data.price),
        sale_price: toMoneyString(data.sale_price),
        compare_at_price: toMoneyString(data.compare_at_price),

        starts_at: toDateTimeStringOrNull(data.starts_at),
        ends_at: toDateTimeStringOrNull(data.ends_at),

        activity: Boolean(data.activity),
        sort: Number.isFinite(Number(data.sort)) ? Number(data.sort) : 0,

        meta: data.meta,
    }))

    form.post(route('admin.schoolCoursePrices.store'), {
        preserveScroll: true,
        onSuccess: () => toast.success('Цена курса успешно создана.'),
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors?.[firstKey] || 'Проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('addCoursePrice')">
        <template #header>
            <TitlePage>
                {{ t('addCoursePrice') }}
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
                    <DefaultButton :href="route('admin.schoolCoursePrices.index')">
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

                <form @submit.prevent="submitForm" class="p-3 w-full space-y-4">
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.activity" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            </div>

                            <InputNumber
                                id="sort"
                                v-model="form.sort"
                                type="number"
                                min="0"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />

                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <SelectEntity
                            id="school_course_id"
                            v-model="form.school_course_id"
                            :label="t('course')"
                            :required="true"
                            :options="courseOptions"
                            :error-message="form.errors.school_course_id"
                            :placeholder="t('select')"
                        />

                        <SelectEntity
                            id="currency_id"
                            v-model="form.currency_id"
                            :label="t('currency')"
                            :required="true"
                            :options="currencyOptions"
                            :error-message="form.errors.currency_id"
                            :placeholder="t('select')"
                        />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
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
                            <LabelInput for="sale_price">
                                {{ t('salePrice') }}
                            </LabelInput>

                            <InputMoney
                                id="sale_price"
                                v-model="form.sale_price"
                                :min="0"
                                :step="0.01"
                                :fraction-digits="2"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.sale_price" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="compare_at_price">
                                {{ t('compareAtPrice') }}
                            </LabelInput>

                            <InputMoney
                                id="compare_at_price"
                                v-model="form.compare_at_price"
                                :min="0"
                                :step="0.01"
                                :fraction-digits="2"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.compare_at_price" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="starts_at">
                                {{ t('priceStartsAt') }}
                            </LabelInput>

                            <InputText
                                id="starts_at"
                                v-model="form.starts_at"
                                type="datetime-local"
                                autocomplete="off"
                                class="w-full max-w-xs"
                            />

                            <InputError class="mt-2" :message="form.errors.starts_at" />
                        </div>

                        <div class="flex flex-col items-start lg:items-end">
                            <LabelInput for="ends_at">
                                {{ t('priceEndsAt') }}
                            </LabelInput>

                            <InputText
                                id="ends_at"
                                v-model="form.ends_at"
                                type="datetime-local"
                                autocomplete="off"
                                class="w-full max-w-xs"
                            />

                            <InputError class="mt-2" :message="form.errors.ends_at" />
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton :href="route('admin.schoolCoursePrices.index')">
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
                            class="mb-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
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
