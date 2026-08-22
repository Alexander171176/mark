<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Страница редактирование инструктора школы
 * - мультиязычные переводы
 * - изображения
 * - соц. ссылки
 * - SEO
 */
import { ref, computed, watch } from 'vue'
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
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import InputDecimal from '@/Components/Admin/UI/Input/InputDecimal.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

/* ======================== UI и сервисы ======================== */
const { t } = useI18n()
const toast = useToast()

/* ======================== Props ======================== */
// instructorProfile — редактируемый инструктор
// users — список пользователей для привязки
// targetLocale — активная локаль
// availableLocales — доступные языки
const props = defineProps({
    instructorProfile: {
        type: Object,
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
    },
    targetLocale: {
        type: String,
        default: 'ru',
    },
    availableLocales: {
        type: Array,
        default: () => ['ru', 'en', 'kk'],
    },
})

/* ======================== Переводы ======================== */

// шаблон пустого перевода
const makeTranslation = () => ({
    title: '',
    short: '',
    bio: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// собирает переводы из массива в объект { ru: {...}, en: {...} }
const buildTranslations = () => {
    const result = {}

    ;(props.instructorProfile.translations || []).forEach((translation) => {
        result[translation.locale] = {
            title: translation.title || '',
            short: translation.short || '',
            bio: translation.bio || '',
            meta_title: translation.meta_title || '',
            meta_keywords: translation.meta_keywords || '',
            meta_desc: translation.meta_desc || '',
        }
    })

    const defaultLocale =
        props.targetLocale || 'ru'

    if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation()
    }

    if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation()
    }

    return result
}

// локаль по умолчанию
const defaultLocale =
    props.targetLocale || 'ru'

// активная вкладка перевода
const activeLocale = ref(defaultLocale)

/* ======================== Пользователь ======================== */

// список пользователей для VueMultiselect
const userOptions = computed(() =>
    props.users.map(user => ({
        id: user.id,
        label: user.name || `#${user.id}`,
    }))
)

// выбранный пользователь инструктора
const selectedInstructorProfile = ref(
    userOptions.value.find(
        option => option.id === (
            props.instructorProfile.user_id ??
            props.instructorProfile.user?.id
        )
    ) ?? null
)

/* ======================== Форма ======================== */

// форма редактирования
const form = useForm({
    _method: 'PUT',

    user_id: props.instructorProfile.user_id ?? props.instructorProfile.user?.id ?? null,
    sort: props.instructorProfile.sort ?? 0,
    activity: Boolean(props.instructorProfile.activity),
    slug: props.instructorProfile.slug ?? '',

    experience_years: Number(props.instructorProfile.experience_years ?? 0),

    hourly_rate: props.instructorProfile.hourly_rate != null
        ? String(props.instructorProfile.hourly_rate)
        : '',

    rating_count: Number(
        props.instructorProfile.rating_count ?? 0
    ),

    rating_avg: props.instructorProfile.rating_avg != null
        ? String(props.instructorProfile.rating_avg)
        : '',

    social_links: props.instructorProfile.social_links ?? {},
    deletedImages: [],

    translations: buildTranslations(),
})

// синхронизация user_id при выборе пользователя
watch(selectedInstructorProfile, (value) => {
    form.user_id = value?.id ?? null
})

/* ======================== Текущий перевод ======================== */

// текущий перевод активной вкладки
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// заголовок страницы
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || `ID: ${props.instructorProfile.id}`
})

// ошибка поля текущей локали
const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/* ======================== Изображения ======================== */

// существующие изображения
const existingImages = ref(
    (props.instructorProfile.images || [])
        .filter(image => image.url)
        .map(image => ({
            id: image.id,
            url: image.webp_url || image.url,
            order: image.order || 0,
            alt: image.alt || '',
            caption: image.caption || '',
        }))
)

// новые изображения
const newImages = ref([])

// обновление порядка/данных существующих изображений
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images
}

// пометка существующего изображения на удаление
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter(
        image => image.id !== deletedId
    )
}

// обновление новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

/* ======================== Slug ======================== */

// генерация slug из title текущей локали
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/* ======================== Утилиты ======================== */

// обрезка текста для SEO
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

/* ======================== SEO ======================== */

// очистка SEO-полей текущей локали
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

// автогенерация SEO-полей текущей локали
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 160)
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

/* ======================== Социальные сети ======================== */

// строки социальных ссылок из social_links
const socialRows = ref(
    Array.isArray(form.social_links)
        ? form.social_links
        : form.social_links && typeof form.social_links === 'object'
            ? Object.entries(form.social_links).map(([platform, url]) => ({
                platform,
                url,
            }))
            : []
)

// добавить строку социальной ссылки
const addSocialRow = () => {
    socialRows.value.push({
        platform: '',
        url: '',
    })
}

// удалить строку социальной ссылки
const removeSocialRow = (idx) => {
    socialRows.value.splice(idx, 1)
}

// собрать social_links в объект { platform: url }
const buildSocialLinks = () => {
    const links = {}

    socialRows.value.forEach(row => {
        const platform = (row.platform || '').trim()
        const url = (row.url || '').trim()

        if (!platform || !url) return

        links[platform] = url
    })

    return links
}

/* ======================== Отправка формы ======================== */

// обновление инструктора
const submitForm = () => {
    const socialLinks = buildSocialLinks()

    form.transform((data) => {
        const toNum = (value, digits = 2) => {
            if (value === '' || value === null || typeof value === 'undefined') {
                return null
            }

            const number = Number(value)

            return Number.isFinite(number)
                ? Number(number.toFixed(digits))
                : null
        }

        let rating = toNum(data.rating_avg, 2)

        if (rating !== null) {
            if (rating < 0) rating = 0
            if (rating > 5) rating = 5
        }

        return {
            ...data,
            user_id: selectedInstructorProfile.value?.id ?? null,
            activity: data.activity ? 1 : 0,
            hourly_rate: toNum(data.hourly_rate, 2),
            rating_avg: rating,
            social_links: socialLinks,

            images: [
                ...newImages.value.map(image => ({
                    file: image.file,
                    order: image.order ?? 0,
                    alt: image.alt ?? '',
                    caption: image.caption ?? '',
                })),
                ...existingImages.value.map(image => ({
                    id: image.id,
                    order: image.order ?? 0,
                    alt: image.alt ?? '',
                    caption: image.caption ?? '',
                })),
            ],

            deletedImages: data.deletedImages,
        }
    })

    form.post(route('admin.schoolInstructorProfiles.update', {
        schoolInstructorProfile: props.instructorProfile.id,
    }), {
        errorBag: 'editSchoolInstructorProfile',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Инструктор успешно обновлён!')
        },
        onError: (errors) => {
            console.error('Ошибка при обновлении инструктора:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editInstructor')">
        <template #header>
            <TitlePage>
                {{ t('editInstructor') }}: {{ pageTitle }} [ID: {{ props.instructorProfile.id }}]
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
                    <DefaultButton :href="route('admin.schoolInstructorProfiles.index')">
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

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="user"
                                :value="t('instructor')"
                                class="mb-1"
                            />

                            <VueMultiselect
                                id="user"
                                v-model="selectedInstructorProfile"
                                :options="userOptions"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.user_id"
                            />
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="experience_years">
                                    {{ t('experienceYears') }}
                                </LabelInput>

                                <InputNumber
                                    id="experience_years"
                                    type="number"
                                    min="0"
                                    v-model.number="form.experience_years"
                                    class="w-full lg:w-28"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.experience_years"
                                />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="hourly_rate">
                                    {{ t('hourlyRate') }}
                                </LabelInput>

                                <InputDecimal
                                    id="hourly_rate"
                                    v-model="form.hourly_rate"
                                    :min="0"
                                    :step="0.01"
                                    :fraction-digits="2"
                                    class="w-full lg:w-28"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.hourly_rate"
                                />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <div class="h-8 flex items-center">
                                    <LabelInput
                                        for="rating_count"
                                        :value="t('ratingCount')"
                                        class="text-sm"
                                    />
                                </div>

                                <InputNumber
                                    id="rating_count"
                                    type="number"
                                    v-model.number="form.rating_count"
                                    min="0"
                                    autocomplete="rating_count"
                                    class="w-full lg:w-28"
                                />

                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.rating_count"
                                />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <div class="h-8 flex items-center">
                                    <LabelInput
                                        for="rating_avg"
                                        :value="t('ratingAvg')"
                                        class="text-sm"
                                    />
                                </div>

                                <InputDecimal
                                    id="rating_avg"
                                    v-model="form.rating_avg"
                                    :min="0"
                                    :max="5"
                                    :step="0.01"
                                    :fraction-digits="2"
                                    class="w-full lg:w-28"
                                />

                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.rating_avg"
                                />
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
                                    required
                                    autocomplete="title"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('title')"
                                />
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

                                <InputError
                                    class="mt-2"
                                    :message="getError('short')"
                                />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput
                                    for="bio"
                                    :value="`${t('bio')} [${activeLocale.toUpperCase()}]`"
                                />

                                <TinyEditor
                                    v-model="currentTranslation.bio"
                                    :height="500"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('bio')"
                                />
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
                                        {{ (currentTranslation.meta_desc || '').length }} / 255 {{ t('characters') }}
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
                                    <template #default>
                                        {{ t('clearMetaFields') }}
                                    </template>
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

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="social"
                                :value="t('socialLinks')"
                            />

                            <div class="w-full space-y-2">
                                <div
                                    v-for="(row, idx) in socialRows"
                                    :key="idx"
                                    class="flex flex-col lg:flex-row gap-2 items-start lg:items-center"
                                >
                                    <InputText
                                        :id="`social_platform_${idx}`"
                                        v-model="row.platform"
                                        :placeholder="t('platform')"
                                        class="w-full lg:w-1/3"
                                        autocomplete="off"
                                    />

                                    <InputText
                                        :id="`social_url_${idx}`"
                                        v-model="row.url"
                                        :placeholder="t('link') || 'https://...'"
                                        class="w-full lg:flex-1"
                                        autocomplete="off"
                                    />

                                    <DeleteIconButton
                                        :title="t('delete')"
                                        @click="removeSocialRow(idx)"
                                    />
                                </div>

                                <PrimaryButton
                                    type="button"
                                    class="mt-2 float-end"
                                    @click="addSocialRow"
                                >
                                    <template #icon>
                                        <svg
                                            class="w-4 h-4 fill-current text-slate-100"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 5v14M5 12h14"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                    {{ t('addLink') }}
                                </PrimaryButton>
                            </div>

                            <InputError
                                class="mt-2"
                                :message="form.errors.social_links"
                            />
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
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.schoolInstructorProfiles.index')">
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
                            :disabled="form.processing"
                            :class="{ 'opacity-25': form.processing }"
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

<style src="/resources/css/vue-multiselect.min.css"></style>
