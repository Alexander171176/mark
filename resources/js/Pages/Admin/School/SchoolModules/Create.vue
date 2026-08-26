<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание модуля школы.
 *
 * Create использует тот же контракт
 * selector-ов и TranslationTabs, что Edit.
 */

import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputDecimalExt from '@/Components/Admin/UI/Input/InputDecimalExt.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectStatus from '@/Components/Admin/School/SchoolCourse/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/School/SchoolCourse/Select/SelectAvailability.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    courses: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * TRANSLATIONS
 * ========================================================== */

const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

const defaultLocale =
    props.currentLocale
    || props.availableLocales?.[0]
    || 'ru'

const activeLocale = ref(
    defaultLocale
)

/* ==========================================================
 * FORM
 * ========================================================== */

const form = useForm({
    school_course_id: null,

    sort: 0,
    activity: true,

    slug: '',

    status: 'draft',
    availability: 'public',
    published_at: '',

    difficulty: 0,
    duration: 0,

    translations: {
        [defaultLocale]:
            makeTranslation(),
    },
})

const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] =
            makeTranslation()
    }

    return form.translations[activeLocale.value]
})

const getError = (key) => {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/* ==========================================================
 * COURSE SELECT
 * ========================================================== */

const dynamicOptionsLimit = (items) => {
    return Array.isArray(items)
        ? items.length + 10
        : 10
}

const courseOptions = computed(() => {
    return (props.courses || []).map((course) => ({
        id: course.id,

        label:
            `[ID: ${course.id}] ${
                course?.translation?.title
                || course?.slug
                || `#${course.id}`
            }`,
    }))
})

const selectedCourse = computed({
    get() {
        return courseOptions.value.find(
            option =>
                Number(option.id)
                === Number(form.school_course_id)
        ) || null
    },

    set(value) {
        form.school_course_id =
            value?.id ?? null
    },
})

/* ==========================================================
 * IMAGES
 * ========================================================== */

const newImages = ref([])

const handleNewImagesUpdate = (images) => {
    newImages.value =
        Array.isArray(images)
            ? images
            : []
}

/* ==========================================================
 * SLUG
 * ========================================================== */

const handleSlugFocus = () => {
    if (
        !form.slug
        && currentTranslation.value.title
    ) {
        form.slug = transliterate(
            currentTranslation.value.title
                .toLowerCase()
        )
    }
}

/* ==========================================================
 * SEO
 * ========================================================== */

const truncateText = (
    text,
    maxLength,
    addEllipsis = false
) => {
    if (!text) {
        return ''
    }

    const value =
        String(text)

    if (value.length <= maxLength) {
        return value
    }

    const lastSpaceIndex =
        value.lastIndexOf(
            ' ',
            maxLength
        )

    const truncated =
        lastSpaceIndex === -1
            ? value.substring(0, maxLength)
            : value.substring(0, lastSpaceIndex)

    return addEllipsis
        ? `${truncated}...`
        : truncated
}

const clearMetaFields = () => {
    const translation =
        currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

const generateMetaFields = () => {
    const translation =
        currentTranslation.value

    if (
        translation.title
        && !translation.meta_title
    ) {
        translation.meta_title =
            truncateText(
                translation.title,
                160
            )
    }

    if (
        !translation.meta_keywords
        && translation.short
    ) {
        let text =
            String(
                translation.short
            ).replace(
                /(<([^>]+)>)/gi,
                ''
            )

        text = text.replace(
            /[.,!?;:()[\]{}"'«»]/g,
            ''
        )

        const words = text
            .split(/\s+/)
            .filter(
                word =>
                    word
                    && word.length >= 3
            )
            .map(
                word =>
                    word.toLowerCase()
            )
            .filter(
                (value, index, self) =>
                    self.indexOf(value)
                    === index
            )

        translation.meta_keywords =
            truncateText(
                words.join(', '),
                255
            )
    }

    if (
        translation.short
        && !translation.meta_desc
    ) {
        const description =
            String(
                translation.short
            ).replace(
                /(<([^>]+)>)/gi,
                ''
            )

        translation.meta_desc =
            truncateText(
                description,
                255,
                true
            )
    }
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const toNumber = (
    value,
    digits = 2
) => {
    if (
        value === ''
        || value === null
        || value === undefined
    ) {
        return null
    }

    const number =
        Number(value)

    return Number.isFinite(number)
        ? Number(
            number.toFixed(digits)
        )
        : null
}

const submit = () => {
    form.transform((data) => {
        let difficulty =
            toNumber(
                data.difficulty,
                2
            )

        if (difficulty !== null) {
            difficulty =
                Math.min(
                    5,
                    Math.max(
                        0,
                        difficulty
                    )
                )
        }

        const transformed = {
            ...data,

            school_course_id:
                selectedCourse.value?.id
                ?? null,

            activity:
                data.activity
                    ? 1
                    : 0,

            difficulty,

            duration:
                data.duration === ''
                || data.duration === null
                    ? null
                    : Number(data.duration),
        }

        delete transformed.images

        newImages.value.forEach(
            (image, index) => {
                transformed[
                    `images[${index}][file]`
                    ] = image.file

                transformed[
                    `images[${index}][order]`
                    ] = image.order ?? 0

                transformed[
                    `images[${index}][alt]`
                    ] = image.alt ?? ''

                transformed[
                    `images[${index}][caption]`
                    ] = image.caption ?? ''
            }
        )

        return transformed
    })

    form.post(
        route(
            'admin.schoolModules.store'
        ),
        {
            errorBag:
                'createSchoolModule',

            preserveScroll: true,
            forceFormData: true,

            onSuccess: () => {
                toast.success(
                    'Модуль успешно создан!'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка создания модуля:',
                    errors
                )

                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Проверьте корректность полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('createModule')">
        <template #header>
            <TitlePage>
                {{ t('createModule') }}
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
                    <DefaultButton :href="route('admin.schoolModules.index')">
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
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                    @submit.prevent="submit"
                >
                    <div class="pb-12">

                        <!-- Activity + sort -->
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
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                    class="text-sm"
                                />

                                <InputNumber
                                    id="sort"
                                    v-model.number="form.sort"
                                    type="number"
                                    class="w-full lg:w-28"
                                />

                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <!-- Status + availability -->
                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectStatus
                                v-model="form.status"
                                :errorMessage="form.errors.status"
                            />

                            <SelectAvailability
                                v-model="form.availability"
                                :errorMessage="form.errors.availability"
                            />
                        </div>

                        <!-- Publication / difficulty / duration -->
                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="published_at"
                                    :value="t('publishedAt')"
                                />

                                <InputText
                                    id="published_at"
                                    v-model="form.published_at"
                                    type="date"
                                    class="w-full max-w-56"
                                />

                                <InputError :message="form.errors.published_at" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="difficulty">
                                    {{ t('difficulty') }}
                                </LabelInput>

                                <InputDecimalExt
                                    id="difficulty"
                                    v-model="form.difficulty"
                                    :min="0"
                                    :max="5"
                                    :step="0.01"
                                    :fraction-digits="2"
                                    class="w-full lg:w-28"
                                />

                                <InputError :message="form.errors.difficulty" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="duration">
                                    {{ t('duration') }}
                                </LabelInput>

                                <InputNumber
                                    id="duration"
                                    v-model.number="form.duration"
                                    type="number"
                                    min="0"
                                    class="w-full lg:w-28"
                                />

                                <InputError :message="form.errors.duration" />
                            </div>
                        </div>

                        <!-- Course -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="school_course_id"
                                :value="t('course')"
                                class="mb-1"
                            />

                            <VueMultiselect
                                id="school_course_id"
                                v-model="selectedCourse"
                                :options="courseOptions"
                                :options-limit="dynamicOptionsLimit(courseOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-empty="false"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.school_course_id"
                            />
                        </div>

                        <!-- Slug -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="slug">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('slug') }}
                            </LabelInput>

                            <InputText
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                required
                                autocomplete="slug"
                                @focus="handleSlugFocus"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.slug"
                            />
                        </div>

                        <!-- Translations -->
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
                                    <LabelInput for="title">
                                        <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                        {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                                    </LabelInput>

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.title || '').length }}
                                        / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="title"
                                    v-model="currentTranslation.title"
                                    type="text"
                                    maxlength="255"
                                    required
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('title')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="subtitle"
                                        :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.subtitle || '').length }}
                                        / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    v-model="currentTranslation.subtitle"
                                    class="w-full"
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

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
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
                                    :height="500"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('description')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_title"
                                        :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_title || '').length }}
                                        / 160 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="meta_title"
                                    v-model="currentTranslation.meta_title"
                                    type="text"
                                    maxlength="160"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('meta_title')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_keywords"
                                        :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_keywords || '').length }}
                                        / 255 {{ t('characters') }}
                                    </div>
                                </div>

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
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_desc"
                                        :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.meta_desc || '').length }}
                                        / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    v-model="currentTranslation.meta_desc"
                                    maxlength="255"
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('meta_desc')"
                                />
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

                        <!-- Images -->
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
                        <DefaultButton :href="route('admin.schoolModules.index')">
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

<style src="/resources/css/vue-multiselect.min.css"></style>
