<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Редактирование статьи блога
 * Новая мультиязычная архитектура:
 * - blog_articles
 * - blog_article_translations
 */
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { transliterate } from '@/utils/transliteration'
import { useForm } from '@inertiajs/vue3'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'
import ClearMetaButton from '@/Components/Admin/Buttons/ClearMetaButton.vue'

import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'

import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/Image/MultiImageEdit.vue'
import TranslationTabs from '@/Components/Admin/Locale/TranslationTabs.vue'

const toast = useToast()
const { t } = useI18n()

const props = defineProps({
    article: { type: Object, required: true },

    targetLocale: { type: String, default: 'ru' },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },

    rubrics: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    videos: { type: Array, default: () => [] },

    related_articles: { type: Array, default: () => [] },
    relatedArticles: { type: Array, default: () => [] },

    errors: { type: Object, default: () => ({}) },
})

/**
 * Шаблон одного перевода
 */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    pseudonym: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/**
 * Преобразуем translations из API в объект:
 * { ru: {...}, en: {...}, kk: {...} }
 */
const buildTranslations = () => {
    const result = {}

    ;(props.article.translations || []).forEach((translation) => {
        result[translation.locale] = {
            title: translation.title || '',
            subtitle: translation.subtitle || '',
            short: translation.short || '',
            description: translation.description || '',
            pseudonym: translation.pseudonym || '',
            meta_title: translation.meta_title || '',
            meta_keywords: translation.meta_keywords || '',
            meta_desc: translation.meta_desc || '',
        }
    })

    const defaultLocale =
        props.targetLocale ||
        props.currentLocale ||
        props.article.translation?.locale ||
        'ru'

    if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation()
    }

    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

/**
 * Дефолтная и активная локаль
 */
const defaultLocale =
    props.targetLocale ||
    props.currentLocale ||
    props.article.translation?.locale ||
    'ru'

const activeLocale = ref(defaultLocale)

/**
 * Опции связанных статей
 */
const relatedArticlesOptions = computed(() => {
    return props.related_articles?.length
        ? props.related_articles
        : props.relatedArticles
})

/**
 * Основная форма редактирования
 */
const form = useForm({
    _method: 'PUT',

    sort: props.article.sort ?? 0,

    activity: Boolean(props.article.activity ?? false),
    left: Boolean(props.article.left ?? false),
    main: Boolean(props.article.main ?? false),
    right: Boolean(props.article.right ?? false),

    url: props.article.url ?? '',

    published_at: props.article.published_at ?? '',
    show_from_at: props.article.show_from_at ?? '',
    show_to_at: props.article.show_to_at ?? '',

    rubrics: props.article.rubrics ?? [],
    tags: props.article.tags ?? [],
    videos: props.article.videos ?? [],
    related_articles: props.article.related_articles ?? [],

    translations: buildTranslations(),

    deletedImages: [],
})

/**
 * Текущий активный перевод
 */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/**
 * Заголовок страницы
 */
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || props.article.translation?.title
        || `ID: ${props.article.id}`
})

/**
 * Ошибка поля текущей локали
 */
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/**
 * Локаль в верхнем регистре
 */
const upperLocale = (value) => {
    return value ? String(value).toUpperCase() : '-'
}

/**
 * Владелец элемента
 */
const ownerLabel = (item) => {
    const owner =
        item?.owner?.name ||
        item?.owner?.email ||
        item?.user?.name ||
        item?.user?.email ||
        item?.owner_name ||
        item?.owner_email ||
        ''

    return owner ? String(owner) : '-'
}

/**
 * Название элемента из shared/resource
 */
const baseName = (item) => {
    return String(
        item?.title ||
        item?.name ||
        item?.translation?.title ||
        item?.translation?.name ||
        ''
    ).trim() || '—'
}

/**
 * Универсальная подпись для multiselect
 */
const multiLabel = (item) => {
    if (!item) return ''

    const id = item?.id ?? '-'
    const owner = ownerLabel(item)
    const loc = upperLocale(item?.locale || item?.translation?.locale)

    return `[ID:${id}] [${owner}] [${loc}] ${baseName(item)}`
}

/**
 * Существующие изображения из БД
 */
const existingImages = ref(
    (props.article.images || [])
        .filter(img => img.url || img.webp_url || img.image_url || img.thumb_url)
        .map(img => ({
            id: img.id,
            url: img.webp_url || img.image_url || img.thumb_url || img.url,
            order: img.order || 0,
            alt: img.alt || '',
            caption: img.caption || '',
        }))
)

/**
 * Новые изображения
 */
const newImages = ref([])

/**
 * Обновление существующих изображений
 */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images || []
}

/**
 * Удаление существующего изображения
 */
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter(img => img.id !== deletedId)
}

/**
 * Обновление новых изображений
 */
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

/**
 * Автогенерация URL из title активной локали
 */
const handleUrlInputFocus = () => {
    if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/**
 * Обрезка текста для SEO
 */
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

/**
 * Очистка SEO-полей активной локали
 */
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

/**
 * Генерация SEO-полей активной локали
 */
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

/**
 * Отправка формы обновления
 */
const submitForm = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,

            rubrics: (data.rubrics || []).map(item => item.id),
            tags: (data.tags || []).map(item => item.id),
            related_articles: (data.related_articles || []).map(item => item.id),

            videos: (data.videos || []).map((item, index) => ({
                id: item.id,
                sort: index,
            })),
        }

        delete transformed.images
        delete transformed.deletedImages

        let i = 0

        existingImages.value.forEach((img) => {
            transformed[`images[${i}][id]`] = img.id
            transformed[`images[${i}][order]`] = img.order ?? 0
            transformed[`images[${i}][alt]`] = img.alt ?? ''
            transformed[`images[${i}][caption]`] = img.caption ?? ''
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

    form.post(route('admin.blogArticles.update', { blogArticle: props.article.id }), {
        errorBag: 'editBlogArticle',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Статья успешно обновлена!')
            newImages.value = []
            form.deletedImages = []
        },
        onError: (errors) => {
            console.error('Не удалось обновить статью:', errors)
            const firstError = errors?.[Object.keys(errors)[0]]
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editArticle')">
        <template #header>
            <TitlePage>{{ t('editArticle') }}: {{ pageTitle }} [ID: {{ props.article.id }}]</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.blogArticles.index')">
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

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="url">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('postUrl') }}
                        </LabelInput>

                        <InputText
                            id="url"
                            type="text"
                            v-model="form.url"
                            required
                            autocomplete="url"
                            @focus="handleUrlInputFocus"
                        />

                        <InputError class="mt-2" :message="form.errors.url" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="rubrics" :value="t('rubrics')" class="mb-1" />

                        <VueMultiselect
                            v-model="form.rubrics"
                            :options="rubrics"
                            :multiple="true"
                            :close-on-select="true"
                            :placeholder="t('select')"
                            track-by="id"
                            :custom-label="multiLabel"
                        />

                        <InputError class="mt-2" :message="form.errors.rubrics" />
                    </div>

                    <div class="mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4">
                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="published_at" :value="t('publishedAt')" class="mb-1 lg:mb-0 lg:mr-2" />

                            <InputText
                                id="published_at"
                                type="date"
                                v-model="form.published_at"
                                autocomplete="published_at"
                                class="w-full max-w-56"
                            />

                            <InputError class="mt-1 sm:mt-0" :message="form.errors.published_at" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4">
                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="show_from_at" :value="t('showFromAt')" class="mb-1 lg:mb-0 lg:mr-2" />

                            <InputText
                                id="show_from_at"
                                type="datetime-local"
                                v-model="form.show_from_at"
                                autocomplete="show_from_at"
                                class="w-full max-w-56"
                            />

                            <InputError class="mt-1 sm:mt-0" :message="form.errors.show_from_at" />
                        </div>

                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="show_to_at" :value="t('showToAt')" class="mb-1 lg:mb-0 lg:mr-2" />

                            <InputText
                                id="show_to_at"
                                type="datetime-local"
                                v-model="form.show_to_at"
                                autocomplete="show_to_at"
                                class="w-full max-w-56"
                            />

                            <InputError class="mt-1 sm:mt-0" :message="form.errors.show_to_at" />
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
                                {{ t('postTitle') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText
                                id="title"
                                type="text"
                                v-model="currentTranslation.title"
                                required
                                maxlength="255"
                                autocomplete="title"
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
                                autocomplete="subtitle"
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

                            <TinyEditor v-model="currentTranslation.description" :height="500" />
                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col lg:flex-row items-center">
                            <LabelInput
                                for="pseudonym"
                                :value="`${t('nickname')} / ${t('author')} [${activeLocale.toUpperCase()}]`"
                                class="w-40 mb-1 lg:mb-0 lg:mr-2"
                            />

                            <InputText
                                id="pseudonym"
                                type="text"
                                v-model="currentTranslation.pseudonym"
                                autocomplete="pseudonym"
                                class="w-full"
                            />

                            <InputError class="mt-1 sm:mt-0" :message="getError('pseudonym')" />
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
                                autocomplete="meta_title"
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
                                autocomplete="meta_keywords"
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

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="tags" :value="t('tags')" class="mb-1" />

                        <VueMultiselect
                            v-model="form.tags"
                            :options="tags"
                            :multiple="true"
                            :close-on-select="true"
                            :placeholder="t('select')"
                            track-by="id"
                            :custom-label="multiLabel"
                        />

                        <InputError class="mt-2" :message="form.errors.tags" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="related_articles" :value="t('relatedArticles')" class="mb-1" />

                        <VueMultiselect
                            v-model="form.related_articles"
                            :options="relatedArticlesOptions"
                            :multiple="true"
                            :close-on-select="true"
                            :placeholder="t('select')"
                            track-by="id"
                            :custom-label="multiLabel"
                        />

                        <InputError class="mt-2" :message="form.errors.related_articles" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="videos" :value="t('videos')" class="mb-1" />

                        <VueMultiselect
                            v-model="form.videos"
                            :options="videos"
                            :multiple="true"
                            :close-on-select="true"
                            :placeholder="t('select')"
                            track-by="id"
                            :custom-label="multiLabel"
                        />

                        <InputError class="mt-2" :message="form.errors.videos" />
                    </div>

                    <div class="mt-4">
                        <MultiImageEdit
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete:image="handleDeleteExistingImage"
                        />

                        <MultiImageUpload @update:images="handleNewImagesUpdate" />

                        <div v-if="newImages.length" class="text-xs text-slate-600 dark:text-slate-300 mt-2">
                            {{ t('images') }}: {{ newImages.length }}
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.blogArticles.index')" class="mb-3">
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

<style src="/resources/css/vue-multiselect.min.css"></style>
