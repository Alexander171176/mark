<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание урока школы.
 *
 * Create использует тот же selector contract,
 * что и Edit:
 * - module.translation;
 * - module.course.translation;
 * - hashtag.translation;
 * - article/video Public/Admin Shared contract.
 */

import { computed, ref, watch } from 'vue'
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

import SelectStatus from '@/Components/Admin/School/SchoolLesson/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/School/SchoolLesson/Select/SelectAvailability.vue'
import SelectAccessType from '@/Components/Admin/School/SchoolLesson/Select/SelectAccessType.vue'
import SelectPreviewMode from '@/Components/Admin/School/SchoolLesson/Select/SelectPreviewMode.vue'
import LessonContentSelect from '@/Components/Admin/School/SchoolLesson/Block/LessonContentSelect.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    hashtags: { type: Array, default: () => [] },
    articles: { type: Array, default: () => [] },
    videos: { type: Array, default: () => [] },
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

const defaultLocale = props.currentLocale
    || props.availableLocales?.[0]
    || 'ru'

const activeLocale = ref(
    defaultLocale
)

/* ==========================================================
 * FORM
 * ========================================================== */

const form = useForm({
    school_module_id: null,

    activity: true,
    sort: 0,

    slug: '',

    difficulty: 0,
    duration: 0,

    access_type: 'free',
    availability: 'public',
    status: 'draft',
    published_at: '',

    preview_mode: 'none',
    preview_value: 0,

    content_type: '',
    content_id: '',

    hashtag_ids: [],
    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] =
            makeTranslation()
    }

    return form.translations[
        activeLocale.value
        ]
})

const getError = (key) => {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/* ==========================================================
 * MULTISELECT
 * ========================================================== */

const dynamicOptionsLimit = (items) => {
    return Array.isArray(items)
        ? items.length + 10
        : 10
}

/**
 * Новый контракт модуля:
 *
 * module.translation.title
 * module.course.translation.title
 */
const moduleOptions = computed(() => {
    return (props.modules || []).map((item) => {
        const moduleTitle = item?.translation?.title
            || item?.slug
            || `#${item.id}`

        const courseTitle = item?.course?.translation?.title
            || item?.course?.slug
            || null

        return {
            id: item.id,

            label: courseTitle
                ? `[ID: ${item.id}] [${courseTitle}] ${moduleTitle}`
                : `[ID: ${item.id}] ${moduleTitle}`,
        }
    })
})

const selectedModule = ref(null)

/**
 * Create тоже использует восстановление
 * selected option из текущих options.
 *
 * Сейчас ID обычно null, но эта механика
 * делает Create полностью симметричным Edit
 * и корректно работает после Inertia validation return.
 */
watch(
    moduleOptions,
    (options) => {
        if (!form.school_module_id) {
            selectedModule.value = null
            return
        }

        selectedModule.value = options.find(
            item =>
                Number(item.id)
                === Number(form.school_module_id)
        ) || null
    },
    { immediate: true }
)

watch(selectedModule, (value) => {
    form.school_module_id =
        value?.id
        ?? null
})

/**
 * Новый контракт хештега:
 *
 * hashtag.translation.name
 */
const hashtagOptions = computed(() => {
    return (props.hashtags || []).map((item) => ({
        id: item.id,

        label:
            `[ID: ${item.id}] ${
                item?.translation?.name
                || item?.slug
                || `#${item.id}`
            }`,

        color:
            item?.color
            || null,
    }))
})

const selectedHashtags = ref([])

watch(
    hashtagOptions,
    (options) => {
        const ids =
            (form.hashtag_ids || [])
                .map(Number)

        selectedHashtags.value =
            options.filter(
                item =>
                    ids.includes(
                        Number(item.id)
                    )
            )
    },
    { immediate: true }
)

watch(selectedHashtags, (value) => {
    form.hashtag_ids =
        Array.isArray(value)
            ? value.map(item => item.id)
            : []
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

    const str = String(text)

    if (str.length <= maxLength) {
        return str
    }

    const lastSpaceIndex =
        str.lastIndexOf(
            ' ',
            maxLength
        )

    const truncated =
        lastSpaceIndex === -1
            ? str.substring(
                0,
                maxLength
            )
            : str.substring(
                0,
                lastSpaceIndex
            )

    return addEllipsis
        ? `${truncated}...`
        : truncated
}

const clearMetaFields = () => {
    currentTranslation.value.meta_title = ''
    currentTranslation.value.meta_keywords = ''
    currentTranslation.value.meta_desc = ''
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
            String(translation.short)
                .replace(
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
        const text =
            String(translation.short)
                .replace(
                    /(<([^>]+)>)/gi,
                    ''
                )

        translation.meta_desc =
            truncateText(
                text,
                255,
                true
            )
    }
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submit = () => {
    form.transform((data) => {
        const toNum = (
            value,
            digits = 2
        ) => {
            if (
                value === ''
                || value === null
                || typeof value
                === 'undefined'
            ) {
                return null
            }

            const number =
                Number(value)

            return Number.isFinite(
                number
            )
                ? Number(
                    number.toFixed(
                        digits
                    )
                )
                : null
        }

        let difficulty =
            toNum(
                data.difficulty,
                2
            )

        if (difficulty !== null) {
            difficulty =
                Math.max(
                    0,
                    Math.min(
                        5,
                        difficulty
                    )
                )
        }

        const transformed = {
            ...data,

            school_module_id:
                selectedModule.value?.id
                ?? null,

            difficulty,

            duration:
                data.duration === ''
                || data.duration === null
                    ? null
                    : Number(
                        data.duration
                    ),

            preview_value:
                data.preview_value === ''
                || data.preview_value === null
                    ? null
                    : Number(
                        data.preview_value
                    ),

            activity:
                data.activity
                    ? 1
                    : 0,

            hashtag_ids:
                selectedHashtags.value
                    .map(
                        item => item.id
                    ),
        }

        if (
            !transformed.content_type
            || !transformed.content_id
        ) {
            transformed.content_type =
                null

            transformed.content_id =
                null
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
            'admin.schoolLessons.store'
        ),
        {
            errorBag:
                'createSchoolLesson',

            preserveScroll:
                true,

            forceFormData:
                true,

            onSuccess: () => {
                toast.success(
                    'Урок успешно создан!'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка создания урока:',
                    errors
                )

                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors[firstKey]
                    || 'Проверьте корректность полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('createLesson')">
        <template #header>
            <TitlePage>{{ t('createLesson') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolLessons.index')">
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

                <form
                    @submit.prevent="submit"
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
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    v-model.number="form.sort"
                                    class="w-full lg:w-28"
                                />
                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectStatus
                                v-model="form.status"
                                :errorMessage="form.errors.status"
                            />

                            <SelectAvailability
                                v-model="form.availability"
                                :errorMessage="form.errors.availability"
                            />

                            <SelectAccessType
                                v-model="form.access_type"
                                :errorMessage="form.errors.access_type"
                            />
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <SelectPreviewMode
                                v-model="form.preview_mode"
                                :errorMessage="form.errors.preview_mode"
                            />

                            <div class="flex flex-col items-start">
                                <LabelInput for="preview_value">
                                    {{ t('previewValue') }}
                                </LabelInput>

                                <InputNumber
                                    id="preview_value"
                                    type="number"
                                    min="0"
                                    v-model.number="form.preview_value"
                                    class="w-full lg:w-28"
                                />

                                <InputError :message="form.errors.preview_value" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="published_at" :value="t('publishedAt')" />
                                <InputText
                                    id="published_at"
                                    type="date"
                                    v-model="form.published_at"
                                    class="w-full max-w-56"
                                />
                                <InputError :message="form.errors.published_at" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="difficulty">{{ t('difficulty') }}</LabelInput>
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
                                <LabelInput for="duration">{{ t('duration') }}</LabelInput>
                                <InputNumber
                                    id="duration"
                                    type="number"
                                    min="0"
                                    v-model.number="form.duration"
                                    class="w-full lg:w-28"
                                />
                                <InputError :message="form.errors.duration" />
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="school_module_id" :value="t('module')" class="mb-1" />

                            <VueMultiselect
                                id="school_module_id"
                                v-model="selectedModule"
                                :options="moduleOptions"
                                :options-limit="dynamicOptionsLimit(moduleOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.school_module_id" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="slug">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('slug') }}
                            </LabelInput>

                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                class="w-full"
                                autocomplete="slug"
                                @focus="handleSlugFocus"
                                required
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
                                    <LabelInput for="title">
                                        <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                        {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                                    </LabelInput>

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.title || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="title"
                                    type="text"
                                    v-model="currentTranslation.title"
                                    maxlength="255"
                                    required
                                />

                                <InputError class="mt-2" :message="getError('title')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="subtitle"
                                    :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                />
                                <MetaDescTextarea v-model="currentTranslation.subtitle" class="w-full" />
                                <InputError class="mt-2" :message="getError('subtitle')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="short"
                                    :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />
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

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="meta_title"
                                    :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                                />
                                <InputText
                                    id="meta_title"
                                    type="text"
                                    v-model="currentTranslation.meta_title"
                                    maxlength="160"
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
                                <MetaDescTextarea
                                    v-model="currentTranslation.meta_desc"
                                    maxlength="255"
                                    class="w-full"
                                />
                                <InputError class="mt-2" :message="getError('meta_desc')" />
                            </div>

                            <div class="flex justify-end gap-2 mt-4">
                                <ClearMetaButton @click.prevent="clearMetaFields">
                                    <template #default>
                                        {{ t('clearMetaFields') }}
                                    </template>
                                </ClearMetaButton>

                                <MetatagsButton @click.prevent="generateMetaFields">
                                    {{ t('generateMetaTags') }}
                                </MetatagsButton>
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="hashtags"
                                :value="t('hashtags')"
                                class="mb-1"
                            />

                            <VueMultiselect
                                id="hashtags"
                                v-model="selectedHashtags"
                                :options="hashtagOptions"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.hashtag_ids" />
                        </div>

                        <LessonContentSelect
                            :articles="articles"
                            :videos="videos"
                            v-model:contentType="form.content_type"
                            v-model:contentId="form.content_id"
                            :error-type="form.errors.content_type"
                            :error-id="form.errors.content_id"
                        />

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
                        <DefaultButton :href="route('admin.schoolLessons.index')">
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

<style src="/resources/css/vue-multiselect.min.css"></style>
