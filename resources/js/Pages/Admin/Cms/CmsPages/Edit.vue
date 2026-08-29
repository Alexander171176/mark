<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование страницы CMS (CmsPage)
 * мультиязычная архитектура
 */

import { computed, ref } from 'vue'
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

import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'
import SvgIconField from '@/Components/Admin/UI/Icon/SvgIconField.vue'

/** Сервисы страницы */
const { t } = useI18n()
const toast = useToast()
const pageProps = usePage()

/** Входные параметры страницы */
const props = defineProps({
    page: { type: Object, required: true },

    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    parents: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Данные редактируемой страницы */
const pageData = computed(() => {
    return props.page?.data
        ?? props.page
})

/* ======================== Translations ======================== */

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
    props.currentLocale
    || pageData.value.translation?.locale
    || props.availableLocales[0]
    || 'ru'

/** Активная локаль вкладок */
const activeLocale = ref(
    defaultLocale
)

/**
 * Подготовка всех переводов страницы.
 *
 * CmsPageResource передаёт здесь
 * все translations намеренно.
 */
const buildTranslations = () => {
    const result = {}

    ;(pageData.value.translations || [])
        .forEach((translation) => {
            result[translation.locale] = {
                title:
                    translation.title || '',

                subtitle:
                    translation.subtitle || '',

                short:
                    translation.short || '',

                description:
                    translation.description || '',

                meta_title:
                    translation.meta_title || '',

                meta_keywords:
                    translation.meta_keywords || '',

                meta_desc:
                    translation.meta_desc || '',
            }
        })

    if (!result[defaultLocale]) {
        result[defaultLocale] =
            makeTranslation()
    }

    return result
}

/* ======================== Form ======================== */

/** Основная форма редактирования */
const form = useForm({
    _method: 'PUT',

    user_id:
        pageData.value.user_id
        || pageProps.props?.auth?.user?.id
        || null,

    parent_id:
        pageData.value.parent_id
        || null,

    level:
        pageData.value.level
        || 1,

    url:
        pageData.value.url
        || '',

    icon:
        pageData.value.icon
        || '',

    in_menu:
        Boolean(pageData.value.in_menu),

    in_footer:
        Boolean(pageData.value.in_footer),

    show_content:
        Boolean(pageData.value.show_content),

    show_seo:
        Boolean(pageData.value.show_seo),

    sort:
        pageData.value.sort
        ?? 0,

    activity:
        Boolean(pageData.value.activity),

    status:
        pageData.value.status
        || 'draft',

    published_at:
        pageData.value.published_at
        || '',

    show_from_at:
        pageData.value.show_from_at
        || '',

    show_to_at:
        pageData.value.show_to_at
        || '',

    views:
        pageData.value.views
        ?? 0,

    translations:
        buildTranslations(),
})

/** Текущий активный перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] =
            makeTranslation()
    }

    return form.translations[
        activeLocale.value
        ]
})

/** Заголовок страницы */
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || pageData.value.translation?.title
        || `ID: ${pageData.value.id}`
})

/** Ошибка поля текущей локали */
const getError = (key) => {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/* ======================== Parent pages ======================== */

/**
 * Название родительской страницы.
 *
 * Список parents передаётся через
 * CmsPageSharedResource и содержит
 * только translation currentLocale.
 */
const getParentTitle = (cmsPage) => {
    return cmsPage?.translation?.title
        || `ID: ${cmsPage?.id}`
}

/** Построение иерархического списка родителей */
const buildParentOptions = (
    pages,
    parentId = null,
    level = 0
) => {
    let result = []

    ;(pages || [])
        .filter(
            (cmsPage) =>
                cmsPage.parent_id === parentId
        )
        .filter(
            (cmsPage) =>
                Number(cmsPage.id)
                !== Number(pageData.value.id)
        )
        .sort(
            (a, b) =>
                (a.sort || 0)
                - (b.sort || 0)
        )
        .forEach((cmsPage) => {
            result.push({
                id: cmsPage.id,

                title:
                    `${'— '.repeat(level)}${getParentTitle(cmsPage)}`,

                level:
                    cmsPage.level
                    || level + 1,
            })

            result = result.concat(
                buildParentOptions(
                    pages,
                    cmsPage.id,
                    level + 1
                )
            )
        })

    return result
}

/** Список родительских страниц */
const parentOptions = computed(() => {
    return buildParentOptions(
        props.parents || []
    )
})

/* ======================== URL ======================== */

/** Нормализация URL */
const normalizeCmsUrl = (value) => {
    const raw = String(value || '').trim()

    if (!raw) {
        return ''
    }

    const cleaned = raw.replace(
        /^\/+|\/+$/g,
        ''
    )

    return '/' + cleaned
        .split('/')
        .filter(Boolean)
        .map(
            (segment) =>
                transliterate(
                    segment.toLowerCase()
                )
        )
        .join('/')
}

/** Автогенерация URL из названия */
const handleUrlInputFocus = () => {
    if (
        !form.url
        && currentTranslation.value.title
    ) {
        form.url = normalizeCmsUrl(
            currentTranslation.value.title
        )
    }
}

/* ======================== SEO ======================== */

/** Обрезка текста без разрыва слов */
const truncateText = (
    text,
    maxLength,
    addEllipsis = false
) => {
    if (!text) {
        return ''
    }

    const string = String(text)
        .replace(/(<([^>]+)>)/gi, '')

    if (string.length <= maxLength) {
        return string
    }

    const lastSpaceIndex =
        string.lastIndexOf(
            ' ',
            maxLength
        )

    const truncated =
        lastSpaceIndex === -1
            ? string.substring(
                0,
                maxLength
            )
            : string.substring(
                0,
                lastSpaceIndex
            )

    return addEllipsis
        ? `${truncated}...`
        : truncated
}

/** Очистка SEO-полей текущего перевода */
const clearMetaFields = () => {
    const translation =
        currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

/** Генерация SEO-полей текущего перевода */
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
                255
            )
    }

    const sourceText =
        translation.short
        || translation.description
        || ''

    if (
        !translation.meta_keywords
        && sourceText
    ) {
        let text = String(sourceText)
            .replace(/(<([^>]+)>)/gi, '')

        text = text.replace(
            /[.,!?;:()[\]{}"'«»]/g,
            ''
        )

        const words = text
            .split(/\s+/)
            .filter(
                (word) =>
                    word
                    && word.length >= 3
            )
            .map(
                (word) =>
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
        !translation.meta_desc
        && sourceText
    ) {
        translation.meta_desc =
            truncateText(
                sourceText,
                200,
                true
            )
    }
}

/* ======================== Submit ======================== */

/** Отправка формы редактирования */
const submitForm = () => {
    form.transform((data) => ({
        ...data,

        parent_id:
            data.parent_id || null,

        in_menu:
            data.in_menu ? 1 : 0,

        in_footer:
            data.in_footer ? 1 : 0,

        show_content:
            data.show_content ? 1 : 0,

        show_seo:
            data.show_seo ? 1 : 0,

        activity:
            data.activity ? 1 : 0,

        url:
            normalizeCmsUrl(data.url),
    }))

    form.post(
        route(
            'admin.cmsPages.update',
            {
                cmsPage:
                pageData.value.id,
            }
        ),
        {
            errorBag: 'updateCmsPage',
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'CMS страница успешно обновлена!'
                )
            },

            onError: (errors) => {
                const firstError =
                    errors?.[
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
    <AdminLayout :title="t('editPage')">
        <template #header>
            <TitlePage>
                {{ t('editPage') }}: {{ pageTitle }} [ID: {{ pageData.id }}]
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
                    <DefaultButton :href="route('admin.cmsPages.index')">
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
                    <div class="mb-3 flex justify-between flex-col xl:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber
                                id="sort"
                                type="number"
                                v-model="form.sort"
                                class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between flex-col xl:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.in_menu" />
                            <LabelCheckbox for="in_menu" :text="t('showInMenu')"
                                           class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.in_footer" />
                            <LabelCheckbox for="in_footer" :text="t('showInFooter')"
                                           class="text-sm h-8 flex items-center" />
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between flex-col xl:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.show_content" />
                            <LabelCheckbox for="show_content" :text="t('showHtml')"
                                           class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.show_seo" />
                            <LabelCheckbox
                                for="show_seo"
                                :text="t('showSeo')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                    </div>

                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="parent_id" :value="t('parentPage')" />

                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm
                                   bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600
                                   border border-slate-400 dark:border-slate-600"
                        >
                            <option :value="null">
                                — {{ t('rootPage') }} —
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

                        <InputError class="mt-2" :message="form.errors.parent_id" />
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
                            :value="`${t('pageContents')} / HTML [${activeLocale.toUpperCase()}]`"
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

                            <MetaDescTextarea
                                v-model="currentTranslation.meta_desc"
                                class="w-full" />
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
                        <LabelInput for="url">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            URL
                        </LabelInput>

                        <InputText
                            id="url"
                            type="text"
                            v-model="form.url"
                            required
                            placeholder="/contacts"
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

                    <SvgIconField
                        v-model="form.icon"
                        :label="t('svg')"
                        :error="form.errors.icon"
                    />

                    <div class="flex items-center justify-center mt-6 gap-3">
                        <DefaultButton :href="route('admin.cmsPages.index')">
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
