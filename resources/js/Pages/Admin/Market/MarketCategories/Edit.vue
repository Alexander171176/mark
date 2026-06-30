<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование Категорий товаров (MarketCategory)
 * мультиязычная архитектура
 */
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'
import { useForm, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'

import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'

import DescriptionTextarea from '@/Components/Admin/UI/Textarea/DescriptionTextarea.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import MultiImagePresetUpload from '@/Components/Admin/UI/Image/MultiImagePresetUpload.vue'
import MultiImagePresetEdit from '@/Components/Admin/UI/Image/MultiImagePresetEdit.vue'
import SvgIconField from '@/Components/Admin/UI/Icon/SvgIconField.vue'

/** Сервисы страницы */
const { t } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    category: { type: Object, required: true },

    imageProcessorEnabled: { type: Boolean, default: false },
    imagePreset: { type: Object, default: null },

    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    parents: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Данные редактируемой категории */
const categoryData = computed(() => props.category?.data ?? props.category)

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
const defaultLocale =
    props.currentLocale ||
    categoryData.value.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

/** Активная локаль вкладок */
const activeLocale = ref(defaultLocale)

/** Подготовка переводов категории */
const buildTranslations = () => {
    const result = {}

    ;(categoryData.value.translations || []).forEach((translation) => {
        result[translation.locale] = {
            title: translation.title || '',
            subtitle: translation.subtitle || '',
            short: translation.short || '',
            description: translation.description || '',
            meta_title: translation.meta_title || '',
            meta_keywords: translation.meta_keywords || '',
            meta_desc: translation.meta_desc || '',
        }
    })

    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

/** Основная форма редактирования */
const form = useForm({
    _method: 'PUT',

    user_id: categoryData.value.user_id || page.props?.auth?.user?.id || null,

    parent_id: categoryData.value.parent_id || null,
    level: categoryData.value.level || 1,

    url: categoryData.value.url || '',
    icon: categoryData.value.icon || '',

    in_menu: Boolean(categoryData.value.in_menu),
    sort: categoryData.value.sort ?? 0,
    activity: Boolean(categoryData.value.activity),

    status: categoryData.value.status || 'draft',

    moderation_status: categoryData.value.moderation_status ?? 0,
    moderation_note: categoryData.value.moderation_note || '',

    published_at: categoryData.value.published_at || '',
    show_from_at: categoryData.value.show_from_at || '',
    show_to_at: categoryData.value.show_to_at || '',

    translations: buildTranslations(),

    images: [],
    deletedImages: [],
})

/** Текущий активный перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Заголовок страницы */
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || categoryData.value.translation?.title
        || `ID: ${categoryData.value.id}`
})

/** Получение ошибки поля текущей локали */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Получение названия родительской категории */
const getParentTitle = (category) => {
    return category?.title
        || category?.translation?.title
        || category?.translations?.[0]?.title
        || `ID: ${category?.id}`
}

/** Построение списка родительских категорий */
function buildParentOptions(categories, parentId = null, level = 0) {
    let result = []

    ;(categories || [])
        .filter((category) => category.parent_id === parentId)
        .sort((a, b) => (a.sort || 0) - (b.sort || 0))
        .forEach((category) => {
            result.push({
                id: category.id,
                title: `${'— '.repeat(level)}${getParentTitle(category)}`,
                level: category.level || level + 1,
            })

            result = result.concat(
                buildParentOptions(categories, category.id, level + 1)
            )
        })

    return result
}

/** Список родительских категорий */
const parentOptions = computed(() => buildParentOptions(props.parents || []))

/** Существующие изображения категории */
const existingImages = ref(
    (categoryData.value.images || [])
        .filter(img => img.url || img.webp_url || img.image_url || img.thumb_url)
        .map(img => ({
            id: img.id,
            url: img.webp_url || img.image_url || img.thumb_url || img.url,
            order: img.order || 0,
            alt: img.alt || '',
            caption: img.caption || '',
        }))
)

/** Новые изображения категории */
const newImages = ref([])

/** Обновление существующих изображений */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images || []
}

/** Удаление существующего изображения */
const handleDeleteExistingImage = (deletedId) => {
    if (!deletedId) return

    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter(img => img.id !== deletedId)
}

/** Обновление новых изображений */
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

/** Автогенерация URL из названия */
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

/** Очистка SEO-полей */
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
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

/** Отправка формы редактирования */
const submitForm = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            in_menu: data.in_menu ? 1 : 0,
            activity: data.activity ? 1 : 0,

            parent_id: data.parent_id || null,
            moderated_by: null,
            moderated_at: null,
        }

        delete transformed.images
        delete transformed.deletedImages

        let i = 0

        existingImages.value.forEach((img) => {
            transformed[`images[${i}][id]`] = img.id
            transformed[`images[${i}][order]`] = img.order ?? 0
            transformed[`images[${i}][alt]`] = img.alt ?? ''
            transformed[`images[${i}][caption]`] = img.caption ?? ''

            if (img.file instanceof File) {
                transformed[`images[${i}][file]`] = img.file
            }

            i++
        })

        newImages.value.forEach((img) => {
            if (img.file) {
                transformed[`images[${i}][file]`] = img.file
                transformed[`images[${i}][order]`] = img.order ?? 0
                transformed[`images[${i}][alt]`] = img.alt ?? ''
                transformed[`images[${i}][caption]`] = img.caption ?? ''
                i++
            }
        })

        form.deletedImages.forEach((id, index) => {
            transformed[`deletedImages[${index}]`] = id
        })

        return transformed
    })

    form.post(
        route('admin.marketCategories.update', {
            marketCategory: categoryData.value.id,
        }),
        {
            forceFormData: true,
            errorBag: 'updateMarketCategory',
            preserveScroll: true,

            onSuccess: () => {
                toast.success('Категория успешно обновлена!')
                newImages.value = []
                form.deletedImages = []
            },

            onError: (errors) => {
                console.error('Не удалось обновить категорию:', errors)

                const firstError = errors?.[Object.keys(errors)[0]]

                toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('editMarketCategory')">
        <template #header>
            <TitlePage>
                {{ t('editMarketCategory') }}: {{ pageTitle }} [ID: {{ categoryData.id }}]
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
                    <DefaultButton :href="route('admin.marketCategories.index')">
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
                            <ActivityCheckbox v-model="form.in_menu" />
                            <LabelCheckbox for="in_menu" :text="t('showInMenu')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber id="sort" type="number" v-model="form.sort" class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
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

                        <div class="flex justify-end gap-2 mt-4">
                            <ClearMetaButton @click.prevent="clearMetaFields">
                                {{ t('clearMetaFields') }}
                            </ClearMetaButton>

                            <MetatagsButton @click.prevent="generateMetaFields">
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="parent_id" :value="t('parentCategory')" />

                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm
                                   bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600
                                   border border-slate-400 dark:border-slate-600"
                        >
                            <option :value="null">
                                — {{ t('rootCategory') }} —
                            </option>

                            <option
                                v-for="parent in parentOptions"
                                :key="parent.id"
                                :value="parent.id"
                                :disabled="parent.level >= 3"
                            >
                                [ID:{{ parent.id }}] {{ parent.title }}
                            </option>
                        </select>

                        <InputError class="mt-2" :message="form.errors.parent_id" />
                    </div>

                    <div class="mb-4 flex flex-col items-start">
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

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
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
                            <LabelInput for="published_at" :value="t('publishedAt')" />

                            <InputText
                                id="published_at"
                                type="date"
                                v-model="form.published_at"
                            />

                            <InputError class="mt-2" :message="form.errors.published_at" />
                        </div>

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

                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="moderation_note" :value="t('moderationNote')" />

                        <DescriptionTextarea
                            id="moderation_note"
                            v-model="form.moderation_note"
                            class="w-full"
                        />

                        <InputError class="mt-2" :message="form.errors.moderation_note" />
                    </div>

                    <SvgIconField
                        v-model="form.icon"
                        :label="t('svg')"
                        :error="form.errors.icon"
                    />

                    <div class="mt-4">
                        <template v-if="imageProcessorEnabled && imagePreset">
                            <MultiImagePresetEdit
                                :images="existingImages"
                                :preset="imagePreset"
                                @update:images="handleExistingImagesUpdate"
                                @delete:image="handleDeleteExistingImage"
                            />

                            <MultiImagePresetUpload
                                :preset="imagePreset"
                                @update:images="handleNewImagesUpdate"
                            />
                        </template>

                        <template v-else>
                            <MultiImageEdit
                                :images="existingImages"
                                @update:images="handleExistingImagesUpdate"
                                @delete:image="handleDeleteExistingImage"
                            />

                            <MultiImageUpload
                                @update:images="handleNewImagesUpdate"
                            />
                        </template>

                        <div v-if="newImages.length" class="text-xs text-slate-600 dark:text-slate-300 mt-2">
                            {{ t('images') }}: {{ newImages.length }}
                        </div>

                        <InputError class="mt-2" :message="form.errors.images" />
                    </div>

                    <div class="flex items-center justify-center mt-6">
                        <DefaultButton :href="route('admin.marketCategories.index')">
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
