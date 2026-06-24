<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание Магазина (MarketShop)
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
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImagePresetUpload from '@/Components/Admin/UI/Image/MultiImagePresetUpload.vue'

/** Сервисы страницы */
const toast = useToast()
const { t } = useI18n()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    imageProcessorEnabled: { type: Boolean, default: true },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Шаблон пустого перевода */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/** Локаль формы по умолчанию */
const defaultLocale = props.currentLocale || 'ru'

/** Активная локаль вкладок */
const activeLocale = ref(defaultLocale)

/** Новые изображения магазина */
const newImages = ref([])

/** Основная форма создания */
const form = useForm({
    user_id: page.props?.auth?.user?.id || null,
    market_company_id: '',

    url: '',
    email: '',
    phone: '',
    logo: null,

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

    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

/** Текущий активный перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Получение ошибки поля текущей локали */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Название компании для select */
const getCompanyTitle = (company) => {
    return company?.translation?.title
        || company?.legal_name
        || `ID: ${company?.id}`
}

/** Пресет для изображений галлереи */
const galleryPreset = {
    key: 'rectangle_large',
    shape: 'rectangle',
    width: 1200,
    height: 800,
    image_rotation_enabled: true,
    crop_rotation_enabled: true,
}

/** Автогенерация URL из названия */
const handleUrlInputFocus = () => {
    if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/** Обновление списка загружаемых изображений */
const handleNewImagesUpdate = (updatedImages) => {
    newImages.value = updatedImages || []
    form.images = updatedImages || []
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

/** Подготовка файла к отправке */
const fileOrNull = (value) => {
    return value instanceof File ? value : null
}

/** Отправка формы создания */
const submitForm = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,

            logo: fileOrNull(data.logo),

            social_links: Object.fromEntries(
                Object.entries(data.social_links || {})
                    .filter(([, value]) => String(value || '').trim() !== '')
            ),
        }

        delete transformed.images

        newImages.value.forEach((image, index) => {
            if (image.file) {
                transformed[`images[${index}][file]`] = image.file
                transformed[`images[${index}][order]`] = image.order ?? 0
                transformed[`images[${index}][alt]`] = image.alt ?? ''
                transformed[`images[${index}][caption]`] = image.caption ?? ''
            }
        })

        return transformed
    })

    form.post(route('admin.marketShops.store'), {
        forceFormData: true,
        errorBag: 'createMarketShop',
        preserveScroll: true,

        onSuccess: () => toast.success('Магазин успешно создан!'),

        onError: (errors) => {
            console.error('Не удалось отправить форму:', errors)

            const firstError = errors?.[Object.keys(errors)[0]]

            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('addMarketShop')">
        <template #header>
            <TitlePage>{{ t('addMarketShop') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketShops.index')">
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

                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-3 w-full">
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber id="sort" type="number" v-model="form.sort" class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.left" />
                            <LabelCheckbox for="left" :text="t('left')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.main" />
                            <LabelCheckbox for="main" :text="t('main')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.right" />
                            <LabelCheckbox for="right" :text="t('right')" class="text-sm h-8 flex items-center" />
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

                            <MetaDescTextarea v-model="currentTranslation.meta_desc" class="w-full" />
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
                            <LabelInput for="market_company_id">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('company') }}
                            </LabelInput>

                            <select
                                id="market_company_id"
                                v-model="form.market_company_id"
                                required
                                class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option value="">
                                    — {{ t('select') }} —
                                </option>

                                <option
                                    v-for="company in companies"
                                    :key="company.id"
                                    :value="company.id"
                                >
                                    [ID:{{ company.id }}] {{ getCompanyTitle(company) }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.market_company_id" />
                        </div>

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

                        <ImageFileInput
                            v-model="form.logo"
                            :label="t('logo')"
                            :button-text="t('selectLogo')"
                            :empty-text="t('noImage')"
                            accept="image/png,image/jpeg,image/webp"
                            :error="form.errors.logo"
                            preview-class="h-24 w-36 object-cover rounded-sm border border-slate-400"
                        />
                    </div>

                    <div
                        class="my-5 p-3 border border-slate-300 dark:border-slate-500
                               bg-white dark:bg-slate-800 rounded-sm"
                    >
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-3">
                            {{ t('socialLinks') }}
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="instagram" value="Instagram" />
                                <InputText id="instagram" type="url" v-model="form.social_links.instagram" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="whatsapp" value="WhatsApp" />
                                <InputText id="whatsapp" type="url" v-model="form.social_links.whatsapp" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="telegram" value="Telegram" />
                                <InputText id="telegram" type="url" v-model="form.social_links.telegram" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="facebook" value="Facebook" />
                                <InputText id="facebook" type="url" v-model="form.social_links.facebook" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="youtube" value="YouTube" />
                                <InputText id="youtube" type="url" v-model="form.social_links.youtube" />
                            </div>
                        </div>

                        <InputError class="mt-2" :message="form.errors.social_links" />
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

                    <div class="mt-4">
                        <MultiImagePresetUpload
                            v-if="imageProcessorEnabled"
                            :preset="galleryPreset"
                            @update:images="handleNewImagesUpdate"
                        />

                        <MultiImageUpload
                            v-else
                            @update:images="handleNewImagesUpdate"
                        />

                        <div v-if="newImages.length" class="text-xs text-slate-600 dark:text-slate-300 mt-2">
                            {{ t('images') }}: {{ newImages.length }}
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.marketShops.index')">
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
