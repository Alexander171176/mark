<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание тега товаров MarketTag
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
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'

import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import SvgIconField from '@/Components/Admin/UI/Icon/SvgIconField.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

const { t } = useI18n()
const toast = useToast()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Создание пустого перевода */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/** Локаль по умолчанию */
const defaultLocale = props.currentLocale || 'ru'

/** Активная локаль редактора */
const activeLocale = ref(defaultLocale)

/** Форма создания тега */
const form = useForm({
    url: '',
    icon: '',
    color: '#22c55e',

    sort: 0,
    activity: true,

    status: 'published',

    moderation_status: 1,
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

/** Получение ошибки текущего языка */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Проверка HEX-цвета */
const isValidHexColor = (value) => /^#([0-9A-Fa-f]{6})$/.test(value || '')

/** Цвет для color picker */
const colorForPicker = computed({
    get() {
        return isValidHexColor(form.color) ? form.color : '#22c55e'
    },
    set(value) {
        form.color = value
    },
})

/** Автоматическая генерация URL */
const handleUrlFocus = () => {
    if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/** Обрезка текста до указанной длины */
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
        translation.meta_desc = truncateText(descText, 255, true)
    }
}

/** Отправка формы */
const submitForm = () => {
    form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
        moderation_status: Number(data.moderation_status ?? 0),
        sort: Number(data.sort ?? 0),
    }))

    form.post(route('admin.marketTags.store'), {
        errorBag: 'createMarketTag',
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Тег успешно создан.')
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createTag')">
        <template #header>
            <TitlePage>{{ t('createTag') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketTags.index')">
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
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

                        <div class="flex flex-col items-start">
                            <LabelInput for="moderation_status" :value="t('moderationStatus')" />
                            <select
                                id="moderation_status"
                                v-model="form.moderation_status"
                                class="w-full px-2 py-0.5 form-select bg-white
                                       text-gray-600 border border-slate-400
                                       dark:border-slate-600 rounded-sm shadow-sm
                                       dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option :value="0">{{ t('underModeration') }}</option>
                                <option :value="1">{{ t('statusSelectApproved') }}</option>
                                <option :value="2">{{ t('statusSelectRejected') }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.moderation_status" />
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
                            <TinyEditor
                                v-model="currentTranslation.description"
                                :height="500"
                            />
                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="meta_title"
                                    :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                                />
                                <div class="text-xs text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.meta_title || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <InputText
                                id="meta_title"
                                type="text"
                                v-model="currentTranslation.meta_title"
                                maxlength="255"
                                autocomplete="off"
                            />

                            <InputError class="mt-2" :message="getError('meta_title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="meta_keywords"
                                    :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                                />
                                <div class="text-xs text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.meta_keywords || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <InputText
                                id="meta_keywords"
                                type="text"
                                v-model="currentTranslation.meta_keywords"
                                maxlength="255"
                                autocomplete="off"
                            />

                            <InputError class="mt-2" :message="getError('meta_keywords')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="meta_desc"
                                    :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                                />
                                <div class="text-xs text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.meta_desc || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <MetaDescTextarea
                                v-model="currentTranslation.meta_desc"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="getError('meta_desc')" />
                        </div>

                        <div class="flex justify-end mt-4">
                            <MetatagsButton @click.prevent="generateMetaFields">
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>



                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="url">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('url') }}
                            </LabelInput>

                            <InputText
                                id="url"
                                type="text"
                                v-model="form.url"
                                autocomplete="off"
                                required
                                @focus="handleUrlFocus"
                            />

                            <InputError class="mt-2" :message="form.errors.url" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="color" :value="t('typeColor')" />

                            <div class="flex items-center gap-3 w-full">
                                <InputText
                                    id="color_text"
                                    type="text"
                                    v-model="form.color"
                                    placeholder="#22c55e"
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
                        <DefaultButton :href="route('admin.marketTags.index')">
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
