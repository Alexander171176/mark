<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование трека школы
 * Мультиязычная архитектура:
 * - school_tracks
 * - school_track_translations
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
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'

import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'

import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectParentTrack from '@/Components/Admin/School/SchoolTrack/Select/SelectParentTrack.vue'

// ======================== i18n и уведомления ========================
const { t } = useI18n()      // перевод строк
const toast = useToast()     // toast-уведомления

// ======================== Props ========================
// track — редактируемый трек
// parents — доступные родительские треки
// currentLocale — текущая локаль
// availableLocales — список доступных языков
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    track: { type: Object, required: true, },
    parents: { type: Array, default: () => [], },
})

// ======================== Фабрика перевода ========================
// шаблон пустого перевода
const makeTranslation = () => ({
    name: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// ======================== Построение переводов ========================
// преобразует translations из массива в объект { ru: {...} }
const buildTranslations = () => {
    const result = {}

    ;(props.track.translations || []).forEach((translation) => {
        result[translation.locale] = {
            name: translation.name || '',
            short: translation.short || '',
            description: translation.description || '',
            meta_title: translation.meta_title || '',
            meta_keywords: translation.meta_keywords || '',
            meta_desc: translation.meta_desc || '',
        }
    })

    const defaultLocale =
        props.currentLocale ||
        props.track.translation?.locale ||
        props.availableLocales[0] ||
        'ru'

    if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation()
    }

    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

// ======================== Локаль ========================
// локаль по умолчанию
const defaultLocale =
    props.currentLocale ||
    props.track.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

// активная вкладка перевода
const activeLocale = ref(defaultLocale)

// ======================== форма редактирования трека ========================
const form = useForm({
    _method: 'PUT',

    parent_id: props.track.parent_id ?? null,
    sort: props.track.sort ?? 0,
    activity: Boolean(props.track.activity ?? true),
    slug: props.track.slug ?? '',

    translations: buildTranslations(),

    deletedImages: [],
})

// ======================== Текущий перевод ========================
// данные активной локали
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// ======================== Заголовок страницы ========================
// название для заголовка страницы
const pageTitle = computed(() => {
    return currentTranslation.value.name
        || props.track.translation?.name
        || props.track.name
        || `ID: ${props.track.id}`
})

// ======================== Ошибки ========================
// ошибка поля текущей локали
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

// ======================== Родительские треки ========================
// строит select с отступами по иерархии
function buildParentOptions(flatTracks, parentId = null, level = 0) {
    let result = []

    ;(flatTracks || [])
        .filter(track => track.parent_id === parentId)
        .sort((a, b) => (a.sort || 0) - (b.sort || 0))
        .forEach(track => {
            result.push({
                id: track.id,
                name: `${'— '.repeat(level)}${track.name || `ID: ${track.id}`}`,
            })

            result = result.concat(buildParentOptions(flatTracks, track.id, level + 1))
        })

    return result
}

// готовые опции для выбора родителя
const parentOptions = computed(() => buildParentOptions(props.parents || []))

// ======================== Изображения ========================
// изображения, уже сохранённые в БД
const existingImages = ref(
    (props.track.images || [])
        .filter(image => image.url)
        .map(image => ({
            id: image.id,
            url: image.webp_url || image.url,
            order: image.order || 0,
            alt: image.alt || '',
            caption: image.caption || '',
        }))
)

// новые изображения до сохранения
const newImages = ref([])

// обновляет существующие изображения
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images || []
}

// помечает изображение на удаление
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter(
        image => image.id !== deletedId
    )
}

// обновляет список новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// ======================== Slug ========================
// генерирует slug из названия
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.name) {
        form.slug = transliterate(currentTranslation.value.name.toLowerCase())
    }
}

// ======================== Вспомогательные функции ========================
// обрезает текст для SEO
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

// ======================== SEO ========================
// очищает SEO-поля текущей локали
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

// генерирует SEO-поля текущей локали
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (translation.name && !translation.meta_title) {
        translation.meta_title = truncateText(translation.name, 160)
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

// ======================== Отправка формы ========================
// отправляет форму обновления
const submitForm = () => {
    form.transform((data) => {
        const transformed = {
            ...data,
            parent_id: data.parent_id || null,
            activity: data.activity ? 1 : 0,
        }

        delete transformed.images
        delete transformed.deletedImages

        let index = 0

        existingImages.value.forEach((image) => {
            transformed[`images[${index}][id]`] = image.id
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        newImages.value.forEach((image) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        form.deletedImages.forEach((id, deletedIndex) => {
            transformed[`deletedImages[${deletedIndex}]`] = id
        })

        return transformed
    })

    form.post(route('admin.schoolTracks.update', {
        schoolTrack: props.track.id,
    }), {
        errorBag: 'editSchoolTrack',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Трек успешно обновлён!')
            newImages.value = []
            form.deletedImages = []
        },
        onError: (errors) => {
            console.error('Не удалось обновить трек:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editLearningCategory')">
        <template #header>
            <TitlePage>
                {{ t('editLearningCategory') }}: {{ pageTitle }} [ID: {{ props.track.id }}]
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
                    <DefaultButton :href="route('admin.schoolTracks.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
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
                    @submit.prevent="submitForm"
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                >
                    <div class="pb-12">
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
                                <div class="h-8 flex items-center">
                                    <LabelInput
                                        for="sort"
                                        :value="t('sort')"
                                        class="text-sm"
                                    />
                                </div>

                                <InputNumber
                                    id="sort"
                                    type="number"
                                    v-model.number="form.sort"
                                    autocomplete="sort"
                                    class="w-full lg:w-28"
                                />

                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.sort"
                                />
                            </div>
                        </div>

                        <SelectParentTrack
                            v-model="form.parent_id"
                            :options="parentOptions"
                            :errorMessage="form.errors.parent_id"
                        />

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="slug">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('slug') }}
                            </LabelInput>

                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                required
                                autocomplete="slug"
                                @focus="handleSlugFocus"
                            />

                            <InputError class="mt-2" :message="form.errors.slug" />
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
                                <div class="flex justify-between w-full">
                                    <LabelInput for="name">
                                        <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                        {{ t('name') }} [{{ activeLocale.toUpperCase() }}]
                                    </LabelInput>

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.name || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="name"
                                    type="text"
                                    v-model="currentTranslation.name"
                                    maxlength="255"
                                    required
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="getError('name')" />
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

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_title || '').length }} / 160 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="meta_title"
                                    type="text"
                                    v-model="currentTranslation.meta_title"
                                    maxlength="160"
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

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_keywords || '').length }} / 255 {{ t('characters')
                                        }}
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

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_desc || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    v-model="currentTranslation.meta_desc"
                                    maxlength="255"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="getError('meta_desc')" />
                            </div>

                            <div class="flex justify-end gap-2 mt-4">
                                <ClearMetaButton @click.prevent="clearMetaFields">
                                        {{ t('clearMetaFields') }}
                                </ClearMetaButton>

                                <MetatagsButton @click.prevent="generateMetaFields">
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

                            <div
                                v-if="newImages.length"
                                class="text-xs text-slate-600 dark:text-slate-300 mt-2"
                            >
                                {{ t('images') }}: {{ newImages.length }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.schoolTracks.index')">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
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
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100"
                                    viewBox="0 0 16 16"
                                >
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
