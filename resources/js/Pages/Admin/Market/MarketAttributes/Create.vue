<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание характеристики MarketAttribute
 */

import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import SvgIconField from '@/Components/Admin/UI/Icon/SvgIconField.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

/** Локализация */
const { t } = useI18n()

/** Toast уведомления */
const toast = useToast()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    groups: { type: [Array, Object], default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Список групп для select */
const groupsList = computed(() => {
    if (Array.isArray(props.groups)) return props.groups
    if (Array.isArray(props.groups?.data)) return props.groups.data

    return []
})

/** Создание пустого перевода */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
})

/** Локаль по умолчанию */
const defaultLocale = props.currentLocale || 'ru'

/** Активная локаль редактора */
const activeLocale = ref(defaultLocale)

/** Форма создания */
const form = useForm({
    market_attribute_group_id: '',

    code: '',
    icon: '',
    color: '#3b82f6',
    type: 'string',
    unit: '',

    required: false,
    filterable: false,
    visible: true,

    sort: 0,
    activity: true,

    status: 'draft',

    moderation_status: 0,
    moderated_by: null,
    moderated_at: null,
    moderation_note: '',

    published_at: '',
    show_from_at: '',
    show_to_at: '',

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

/** Текущий перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Получение ошибки текущей локали */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Список типов характеристик */
const attributeTypes = [
    { value: 'string', label: t('string') },
    { value: 'text', label: t('text') },
    { value: 'integer', label: t('integer') },
    { value: 'decimal', label: t('float') },
    { value: 'boolean', label: t('boolean') },
    { value: 'date', label: t('date') },
    { value: 'datetime', label: t('datetime') },
    { value: 'select', label: t('typeSelect') },
    { value: 'multiselect', label: t('multiselect') },
]

/** Проверка корректности HEX-цвета */
const isValidHexColor = (value) => /^#([0-9A-Fa-f]{6})$/.test(value || '')

/** Цвет для color picker */
const colorForPicker = computed({
    get() {
        return isValidHexColor(form.color) ? form.color : '#3b82f6'
    },
    set(value) {
        form.color = value
    },
})

/** Название группы */
const groupTitle = (group) => {
    return group?.title || group?.translation?.title || group?.code || `ID: ${group?.id}`
}

/** Автоматическая генерация системного кода */
const handleCodeFocus = () => {
    if (!form.code && currentTranslation.value.title) {
        form.code = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/** Отправка формы */
const submitForm = () => {
    form.transform((data) => ({
        ...data,
        market_attribute_group_id: Number(data.market_attribute_group_id),
        required: data.required ? 1 : 0,
        filterable: data.filterable ? 1 : 0,
        visible: data.visible ? 1 : 0,
        activity: data.activity ? 1 : 0,
        moderation_status: Number(data.moderation_status ?? 0),
        sort: Number(data.sort ?? 0),
    }))

    form.post(route('admin.marketAttributes.store'), {
        errorBag: 'createMarketAttribute',
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Характеристика успешно создана.')
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('addMarketAttribute')">
        <template #header>
            <TitlePage>{{ t('addMarketAttribute') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketAttributes.index')">
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
                                v-model.number="form.sort"
                                class="w-full lg:w-28"
                            />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-wrap items-center justify-between gap-5">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.required" />
                            <LabelCheckbox
                                for="required"
                                :text="t('required')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.filterable" />
                            <LabelCheckbox
                                for="filterable"
                                :text="t('showFilter')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.visible" />
                            <LabelCheckbox
                                for="visible"
                                :text="t('visibleCard')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="market_attribute_group_id">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('group') }}
                        </LabelInput>

                        <select
                            id="market_attribute_group_id"
                            v-model="form.market_attribute_group_id"
                            required
                            class="w-full px-2 py-0.5 form-select bg-white
                                       text-gray-600 border border-slate-400
                                       dark:border-slate-600 rounded-sm shadow-sm
                                       dark:bg-cyan-800 dark:text-slate-100"
                        >
                            <option value="">{{ t('select') }}</option>
                            <option
                                v-for="group in groupsList"
                                :key="group.id"
                                :value="group.id"
                            >
                                {{ groupTitle(group) }}
                            </option>
                        </select>

                        <InputError class="mt-2" :message="form.errors.market_attribute_group_id" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div class="flex flex-col items-start">
                            <LabelInput for="type" :value="t('type')" />

                            <select
                                id="type"
                                v-model="form.type"
                                class="w-full px-2 py-0.5 form-select bg-white
                                       text-gray-600 border border-slate-400
                                       dark:border-slate-600 rounded-sm shadow-sm
                                       dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option
                                    v-for="type in attributeTypes"
                                    :key="type.value"
                                    :value="type.value"
                                >
                                    {{ type.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.type" />
                        </div>
                        <div class="flex flex-col items-start">
                            <LabelInput for="unit" :value="t('unit')" />
                            <InputText
                                id="unit"
                                type="text"
                                v-model="form.unit"
                                maxlength="50"
                                autocomplete="off"
                                placeholder="mm, cm, kg, W, %, шт"
                            />
                            <InputError class="mt-2" :message="form.errors.unit" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div class="flex flex-col items-start">
                            <LabelInput for="status" :value="t('status')" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-2 py-0.5 form-select bg-white
                                       text-gray-600 border border-slate-400
                                       dark:border-slate-600 rounded-sm shadow-sm
                                       dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option value="draft">{{ t('statusDraft') }}</option>
                                <option value="published">{{ t('statusPublished') }}</option>
                                <option value="archived">{{ t('statusArchived') }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="published_at" :value="t('publishedAt')" />
                            <InputText
                                id="published_at"
                                type="date"
                                v-model="form.published_at"
                            />
                            <InputError class="mt-2" :message="form.errors.published_at" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div class="flex flex-col items-start">
                            <LabelInput for="show_from_at" :value="t('showFromAt')" />
                            <InputText
                                id="show_from_at"
                                type="datetime-local"
                                v-model="form.show_from_at"
                            />
                            <InputError class="mt-2" :message="form.errors.show_from_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="show_to_at" :value="t('showToAt')" />
                            <InputText
                                id="show_to_at"
                                type="datetime-local"
                                v-model="form.show_to_at"
                            />
                            <InputError class="mt-2" :message="form.errors.show_to_at" />
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
                            @removed="toast.warning('Перевод удалён.')"
                            @added="toast.success('Локаль добавлена.')"
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
                            <InputText
                                id="subtitle"
                                type="text"
                                v-model="currentTranslation.subtitle"
                                maxlength="255"
                                autocomplete="off"
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

                            <MetaDescTextarea
                                v-model="currentTranslation.short"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="getError('short')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="description"
                                :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                            />

                            <MetaDescTextarea
                                v-model="currentTranslation.description"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="code">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('code') }}
                            </LabelInput>

                            <InputText
                                id="code"
                                type="text"
                                v-model="form.code"
                                autocomplete="off"
                                required
                                @focus="handleCodeFocus"
                            />

                            <InputError class="mt-2" :message="form.errors.code" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="color" :value="t('typeColor')" />

                            <div class="flex items-center gap-3 w-full">
                                <InputText
                                    id="color_text"
                                    type="text"
                                    v-model="form.color"
                                    placeholder="#3b82f6"
                                    maxlength="50"
                                    autocomplete="off"
                                />

                                <input
                                    id="color"
                                    type="color"
                                    v-model="colorForPicker"
                                    class="h-9 w-16 rounded bg-transparent cursor-pointer"
                                />
                            </div>

                            <InputError class="mt-2" :message="form.errors.color" />
                        </div>

                        <SvgIconField
                            v-model="form.icon"
                            :label="t('svg')"
                            :error="form.errors.icon"
                        />
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton :href="route('admin.marketAttributes.index')">
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
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
