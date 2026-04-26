<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Редактирование видео блога
 * Новая мультиязычная архитектура:
 * - blog_videos
 * - blog_video_translations
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
import ClearMetaButton from '@/Components/Admin/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'

import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'

import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/Image/MultiImageEdit.vue'
import VideoSourceFields from '@/Components/Admin/Video/Upload/VideoSourceFields.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/Locale/TranslationTabs.vue'

const toast = useToast()
const { t } = useI18n()

const props = defineProps({
    video: { type: Object, required: true },
    videoUrl: { type: String, default: null },
    relatedVideos: { type: Array, default: () => [] },
    targetLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },
    currentLocale: { type: String, default: 'ru' },
    errors: { type: Object, default: () => ({}) },
})

const makeTranslation = () => ({
    title: '',
    short: '',
    description: '',
    pseudonym: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

const buildTranslations = () => {
    const result = {}

    ;(props.video.translations || []).forEach((translation) => {
        result[translation.locale] = {
            title: translation.title || '',
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
        props.video.translation?.locale ||
        'ru'

    if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation()
    }

    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

const defaultLocale =
    props.targetLocale ||
    props.currentLocale ||
    props.video.translation?.locale ||
    'ru'

const activeLocale = ref(defaultLocale)

const form = useForm({
    _method: 'PUT',

    sort: props.video.sort ?? 0,

    activity: Boolean(props.video.activity),
    is_private: Boolean(props.video.is_private),

    left: Boolean(props.video.left),
    main: Boolean(props.video.main),
    right: Boolean(props.video.right),

    url: props.video.url ?? '',

    published_at: props.video.published_at ?? '',
    show_from_at: props.video.show_from_at ?? '',
    show_to_at: props.video.show_to_at ?? '',

    duration: props.video.duration ?? '',
    source_type: props.video.source_type ?? 'local',
    embed_code: props.video.source_type === 'code' ? props.video.embed_code ?? '' : '',
    external_video_id: props.video.external_video_id ?? '',
    video_file: null,
    video_url: props.videoUrl ?? props.video.video_url ?? '',

    related_videos: props.video.related_videos ?? [],

    deletedImages: [],
    translations: buildTranslations(),
})

const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

const pageTitle = computed(() => {
    return currentTranslation.value.title
        || props.video.translation?.title
        || `ID: ${props.video.id}`
})

const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

const relatedVideoLabel = (video) => {
    return video?.title || video?.translation?.title || `ID: ${video?.id}`
}

const existingImages = ref(
    (props.video.images || []).map((image) => ({
        id: image.id,
        url: image.webp_url || image.thumb_url || image.image_url || image.url,
        order: image.order || 0,
        alt: image.alt || '',
        caption: image.caption || '',
    }))
)

const newImages = ref([])

const handleExistingImagesUpdate = (images) => {
    existingImages.value = images
}

const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter((image) => image.id !== deletedId)
}

const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

const handleUrlInputFocus = () => {
    if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

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

const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

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

const submitForm = () => {
    form.transform((data) => ({
        ...data,

        activity: data.activity ? 1 : 0,
        is_private: data.is_private ? 1 : 0,
        left: data.left ? 1 : 0,
        main: data.main ? 1 : 0,
        right: data.right ? 1 : 0,

        related_videos: (data.related_videos || []).map((video, index) => ({
            id: video.id,
            sort: index,
        })),

        images: [
            ...newImages.value.map((image) => ({
                file: image.file,
                order: image.order,
                alt: image.alt,
                caption: image.caption,
            })),
            ...existingImages.value.map((image) => ({
                id: image.id,
                order: image.order,
                alt: image.alt,
                caption: image.caption,
            })),
        ],

        deletedImages: data.deletedImages,
    }))

    form.post(route('admin.blogVideos.update', { blogVideo: props.video.id }), {
        errorBag: 'editBlogVideo',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Видео успешно обновлено!'),
        onError: (errors) => {
            console.error('Ошибка при обновлении видео:', errors)
            const firstError = errors?.[Object.keys(errors)[0]]
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editVideo')">
        <template #header>
            <TitlePage>
                {{ t('editVideo') }}: {{ pageTitle }} [ID: {{ props.video.id }}]
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
                    <DefaultButton :href="route('admin.blogVideos.index')">
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
                            <ActivityCheckbox v-model="form.is_private" />
                            <LabelCheckbox for="is_private" :text="t('isPrivate')"
                                           class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber id="sort" type="number" v-model="form.sort" autocomplete="sort"
                                         class="w-full lg:w-28" />
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
                            {{ t('url') }}
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

                    <div class="mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4">
                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="published_at" :value="t('publishedAt')" class="mb-1 lg:mb-0 lg:mr-2" />
                            <InputText id="published_at" type="date" v-model="form.published_at"
                                       class="w-full max-w-56" />
                            <InputError class="mt-1 sm:mt-0" :message="form.errors.published_at" />
                        </div>

                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="duration" :value="t('duration')" class="mb-1 lg:mb-0 lg:mr-2" />
                            <InputNumber id="duration" type="number" v-model="form.duration" class="w-full max-w-24" />
                            <InputError class="mt-1 lg:mt-0" :message="form.errors.duration" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4">
                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="show_from_at" :value="t('showFromAt')" class="mb-1 lg:mb-0 lg:mr-2" />
                            <InputText id="show_from_at" type="datetime-local" v-model="form.show_from_at"
                                       class="w-full max-w-56" />
                            <InputError class="mt-1 sm:mt-0" :message="form.errors.show_from_at" />
                        </div>

                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="show_to_at" :value="t('showToAt')" class="mb-1 lg:mb-0 lg:mr-2" />
                            <InputText id="show_to_at" type="datetime-local" v-model="form.show_to_at"
                                       class="w-full max-w-56" />
                            <InputError class="mt-1 sm:mt-0" :message="form.errors.show_to_at" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col lg:flex-row lg:justify-between lg:space-x-4">
                        <div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1">
                            <LabelInput for="source_type" :value="t('sourceType')" class="mb-1 lg:mb-0 lg:mr-2" />

                            <select
                                id="source_type"
                                v-model="form.source_type"
                                class="form-select px-2 py-0.5 min-w-[12rem] font-semibold text-sm
                                       rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100
                                       border-slate-500 focus:border-indigo-500 focus:ring-indigo-300"
                            >
                                <option value="local">{{ t('local') }}</option>
                                <option value="youtube">{{ t('youtube') }}</option>
                                <option value="vimeo">{{ t('vimeo') }}</option>
                                <option value="code">{{ t('code') }}</option>
                            </select>

                            <InputError class="mt-1 lg:mt-0" :message="form.errors.source_type" />
                        </div>
                    </div>

                    <div class="flex flex-col items-start">
                        <VideoSourceFields
                            v-model="form.source_type"
                            v-model:video-url="form.video_url"
                            v-model:external-video-id="form.external_video_id"
                            v-model:video-file="form.video_file"
                            v-model:embed-code="form.embed_code"
                        />

                        <InputError class="mt-2" :message="form.errors.video_file" />
                        <InputError class="mt-2" :message="form.errors.embed_code" />
                        <InputError class="mt-2" :message="form.errors.external_video_id" />
                    </div>

                    <div v-if="form.source_type === 'code' && form.embed_code" class="mt-4 mb-4">
                        <LabelInput :value="t('view')" class="mb-1" />
                        <div v-html="form.embed_code" class="border rounded p-4 bg-white dark:bg-slate-800" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="related_videos" :value="t('relatedVideos')" class="mb-1" />

                        <VueMultiselect
                            v-model="form.related_videos"
                            :options="relatedVideos"
                            :multiple="true"
                            :close-on-select="true"
                            :placeholder="t('select')"
                            :custom-label="relatedVideoLabel"
                            track-by="id"
                        />

                        <InputError class="mt-2" :message="form.errors.related_videos" />
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
                                {{ t('name') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText id="title" type="text" v-model="currentTranslation.title" maxlength="255"
                                       required />
                            <InputError class="mt-2" :message="getError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput for="short"
                                            :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`" />
                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.short || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <MetaDescTextarea v-model="currentTranslation.short" class="w-full" />
                            <InputError class="mt-2" :message="getError('short')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="description"
                                        :value="`${t('description')} [${activeLocale.toUpperCase()}]`" />
                            <TinyEditor v-model="currentTranslation.description" :height="500" />
                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col lg:flex-row items-center">
                            <LabelInput
                                for="pseudonym"
                                :value="`${t('nickname')} / ${t('author')} [${activeLocale.toUpperCase()}]`"
                                class="w-40 mb-1 lg:mb-0 lg:mr-2"
                            />

                            <InputText id="pseudonym" type="text" v-model="currentTranslation.pseudonym"
                                       class="w-full" />
                            <InputError class="mt-1 sm:mt-0" :message="getError('pseudonym')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput for="meta_title"
                                            :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`" />
                                <div class="text-xs text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.meta_title || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <InputText id="meta_title" type="text" v-model="currentTranslation.meta_title"
                                       maxlength="255" />
                            <InputError class="mt-2" :message="getError('meta_title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput for="meta_keywords"
                                            :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`" />
                                <div class="text-xs text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.meta_keywords || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <InputText id="meta_keywords" type="text" v-model="currentTranslation.meta_keywords"
                                       maxlength="255" />
                            <InputError class="mt-2" :message="getError('meta_keywords')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput for="meta_desc"
                                            :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`" />
                                <div class="text-xs text-gray-900 dark:text-gray-400 mt-1">
                                    {{ (currentTranslation.meta_desc || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <MetaDescTextarea v-model="currentTranslation.meta_desc" class="w-full" />
                            <InputError class="mt-2" :message="getError('meta_desc')" />
                        </div>

                        <div class="flex justify-end mt-4">
                            <ClearMetaButton @clear="clearMetaFields" class="mr-4" />

                            <MetatagsButton @click.prevent="generateMetaFields">
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>
                    </div>

                    <div class="mt-4">
                        <MultiImageEdit
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete-image="handleDeleteExistingImage"
                        />
                    </div>

                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.blogVideos.index')" class="mb-3">
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
                            :disabled="form.processing"
                            :class="{ 'opacity-25': form.processing }"
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
