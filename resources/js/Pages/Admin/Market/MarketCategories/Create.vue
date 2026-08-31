<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание Категорий товаров (MarketCategory)
 * мультиязычная архитектура
 */

import { computed, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'

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
import MultiImagePresetUpload from '@/Components/Admin/UI/Image/MultiImagePresetUpload.vue'
import SvgIconField from '@/Components/Admin/UI/Icon/SvgIconField.vue'

/** Сервисы страницы */
const { t } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    imageProcessorEnabled: { type: Boolean, default: false },
    imagePreset: { type: Object, default: null },

    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    parents: { type: Array, default: () => [] },
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
const defaultLocale = props.currentLocale
    || props.availableLocales[0]
    || 'ru'

/** Активная локаль вкладок */
const activeLocale = ref(defaultLocale)

/** Новые изображения категории */
const newImages = ref([])

/** Основная форма создания */
const form = useForm({
    user_id: page.props?.auth?.user?.id || null,

    parent_id: null,
    level: 1,

    url: '',
    icon: '',

    in_menu: true,
    sort: 0,
    activity: false,

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

/** Ошибка поля текущей локали */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Название родительской категории */
const getParentTitle = (category) => {
    return category?.translation?.title || `ID: ${category?.id}`
}

/** Построение иерархического списка родителей */
const buildParentOptions = (
    categories,
    parentId = null,
    depth = 0
) => {
    const items = Array.isArray(categories)
        ? categories
        : []

    let result = []

    items
        .filter((category) => category.parent_id === parentId)
        .sort((a, b) => {
            const sortResult = (a.sort ?? 0) - (b.sort ?? 0)

            return sortResult || Number(b.id) - Number(a.id)
        })
        .forEach((category) => {
            result.push({
                id: category.id,
                title: `${'— '.repeat(depth)}${getParentTitle(category)}`,
                level: category.level ?? depth + 1,
            })

            result = result.concat(
                buildParentOptions(
                    items,
                    category.id,
                    depth + 1
                )
            )
        })

    return result
}

/** Список родительских категорий */
const parentOptions = computed(() => {
    return buildParentOptions(props.parents)
})

/** Автогенерация URL из названия */
const handleUrlInputFocus = () => {
    if (
        !form.url
        && currentTranslation.value.title
    ) {
        form.url = transliterate(
            currentTranslation.value.title.toLowerCase()
        )
    }
}

/** Обновление новых изображений */
const handleNewImagesUpdate = (images) => {
    newImages.value = Array.isArray(images)
        ? images
        : []

    form.images = newImages.value
}

/** Обрезка текста без разрыва слов */
const truncateText = (
    text,
    maxLength,
    addEllipsis = false
) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    if (value.length <= maxLength) {
        return value
    }

    const lastSpaceIndex = value.lastIndexOf(
        ' ',
        maxLength
    )

    const truncated = lastSpaceIndex === -1
        ? value.substring(0, maxLength)
        : value.substring(0, lastSpaceIndex)

    return addEllipsis
        ? `${truncated}...`
        : truncated
}

/** Генерация SEO-полей */
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (
        translation.title
        && !translation.meta_title
    ) {
        translation.meta_title = truncateText(
            translation.title,
            255
        )
    }

    if (
        !translation.meta_keywords
        && translation.short
    ) {
        let text = String(translation.short)
            .replace(/(<([^>]+)>)/gi, '')

        text = text.replace(
            /[.,!?;:()[\]{}"'«»]/g,
            ''
        )

        const words = text
            .split(/\s+/)
            .filter((word) => word && word.length >= 3)
            .map((word) => word.toLowerCase())
            .filter((value, index, self) => {
                return self.indexOf(value) === index
            })

        translation.meta_keywords = truncateText(
            words.join(', '),
            255
        )
    }

    if (
        translation.short
        && !translation.meta_desc
    ) {
        const description = String(
            translation.short
        ).replace(/(<([^>]+)>)/gi, '')

        translation.meta_desc = truncateText(
            description,
            200,
            true
        )
    }
}

/** Отправка формы создания */
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

        newImages.value.forEach((image, index) => {
            if (!image.file) {
                return
            }

            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
        })

        return transformed
    })

    form.post(
        route('admin.marketCategories.store'),
        {
            forceFormData: true,
            errorBag: 'createMarketCategory',
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Категория успешно создана!'
                )
            },

            onError: (errors) => {
                console.error(
                    'Не удалось отправить форму:',
                    errors
                )

                const firstError = errors?.[
                    Object.keys(errors)[0]
                    ]

                toast.error(
                    firstError
                    || 'Пожалуйста, проверьте правильность заполнения полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('addMarketCategory')">
        <template #header>
            <TitlePage>
                {{ t('addMarketCategory') }}
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
                    <DefaultButton
                        :href="route('admin.marketCategories.index')"
                    >
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current
                                       text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>

                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    class="p-3 w-full"
                    enctype="multipart/form-data"
                    @submit.prevent="submitForm"
                >
                    <!-- Основные настройки -->
                    <div
                        class="mb-3 flex justify-between
                               flex-col lg:flex-row
                               items-center gap-4"
                    >
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox
                                v-model="form.activity"
                            />

                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox
                                v-model="form.in_menu"
                            />

                            <LabelCheckbox
                                for="in_menu"
                                :text="t('showInMenu')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput
                                for="sort"
                                :value="t('sort')"
                                class="text-sm"
                            />

                            <InputNumber
                                id="sort"
                                v-model="form.sort"
                                type="number"
                                class="w-full lg:w-28"
                            />

                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Переводы -->
                    <div
                        class="my-5 p-3
                               border border-slate-300 dark:border-slate-500
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
                                <span
                                    class="text-red-500 dark:text-red-300
                                           font-semibold"
                                >
                                    *
                                </span>

                                {{ t('title') }}
                                [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText
                                id="title"
                                v-model="currentTranslation.title"
                                type="text"
                                required
                                maxlength="255"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('title')"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="subtitle"
                                :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="subtitle"
                                v-model="currentTranslation.subtitle"
                                type="text"
                                maxlength="255"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('subtitle')"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="short"
                                    :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />

                                <div
                                    class="text-md text-gray-900
                                           dark:text-gray-400 mt-1"
                                >
                                    {{ (currentTranslation.short || '').length }}
                                    / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <MetaDescTextarea
                                v-model="currentTranslation.short"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('short')"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="description"
                                :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                            />

                            <TinyEditor
                                v-model="currentTranslation.description"
                                :height="400"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('description')"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="meta_title"
                                :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="meta_title"
                                v-model="currentTranslation.meta_title"
                                type="text"
                                maxlength="255"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('meta_title')"
                            />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="meta_keywords"
                                :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="meta_keywords"
                                v-model="currentTranslation.meta_keywords"
                                type="text"
                                maxlength="255"
                            />

                            <InputError
                                class="mt-2"
                                :message="getError('meta_keywords')"
                            />
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

                            <InputError
                                class="mt-2"
                                :message="getError('meta_desc')"
                            />
                        </div>

                        <div class="flex justify-end mt-4">
                            <MetatagsButton
                                @click.prevent="generateMetaFields"
                            >
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>
                    </div>

                    <!-- Родитель -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput
                            for="parent_id"
                            :value="t('parentCategory')"
                        />

                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="w-full px-2 py-0.5
                                   form-select rounded-sm shadow-sm
                                   bg-white dark:bg-cyan-800
                                   dark:text-slate-100 text-gray-600
                                   border border-slate-400
                                   dark:border-slate-600"
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
                                {{ parent.title }} [ID:{{ parent.id }}]
                            </option>
                        </select>

                        <InputError
                            class="mt-2"
                            :message="form.errors.parent_id"
                        />
                    </div>

                    <!-- URL -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="url">
                            <span
                                class="text-red-500 dark:text-red-300
                                       font-semibold"
                            >
                                *
                            </span>

                            URL
                        </LabelInput>

                        <InputText
                            id="url"
                            v-model="form.url"
                            type="text"
                            required
                            @focus="handleUrlInputFocus"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.url"
                        />
                    </div>

                    <!-- Публикация -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="status"
                                :value="t('status')"
                            />

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-2 py-0.5
                                       form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800
                                       dark:text-slate-100 text-gray-600
                                       border border-slate-400
                                       dark:border-slate-600"
                            >
                                <option value="draft">
                                    {{ t('statusDraft') }}
                                </option>

                                <option value="published">
                                    {{ t('statusPublished') }}
                                </option>

                                <option value="archived">
                                    {{ t('statusArchived') }}
                                </option>
                            </select>

                            <InputError
                                class="mt-2"
                                :message="form.errors.status"
                            />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="published_at"
                                :value="t('publishedAt')"
                            />

                            <InputText
                                id="published_at"
                                v-model="form.published_at"
                                type="date"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.published_at"
                            />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="show_from_at"
                                :value="t('showFromAt')"
                            />

                            <InputText
                                id="show_from_at"
                                v-model="form.show_from_at"
                                type="datetime-local"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.show_from_at"
                            />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="show_to_at"
                                :value="t('showToAt')"
                            />

                            <InputText
                                id="show_to_at"
                                v-model="form.show_to_at"
                                type="datetime-local"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.show_to_at"
                            />
                        </div>
                    </div>

                    <!-- Модерация -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput
                            for="moderation_note"
                            :value="t('moderationNote')"
                        />

                        <DescriptionTextarea
                            id="moderation_note"
                            v-model="form.moderation_note"
                            class="w-full"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.moderation_note"
                        />
                    </div>

                    <!-- SVG -->
                    <SvgIconField
                        v-model="form.icon"
                        :label="t('svg')"
                        :error="form.errors.icon"
                    />

                    <!-- Изображения -->
                    <div class="mb-4">
                        <MultiImagePresetUpload
                            v-if="imageProcessorEnabled && imagePreset"
                            :preset="imagePreset"
                            @update:images="handleNewImagesUpdate"
                        />

                        <MultiImageUpload
                            v-else
                            @update:images="handleNewImagesUpdate"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.images"
                        />
                    </div>

                    <!-- Кнопки -->
                    <div
                        class="flex items-center
                               justify-center mt-6 gap-3"
                    >
                        <DefaultButton
                            :href="route('admin.marketCategories.index')"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current
                                           text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
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
