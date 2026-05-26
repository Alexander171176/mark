<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование курса (паттерн)
 */
import { computed, ref, onMounted, watch } from 'vue'
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
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectStatus from '@/Components/Admin/School/Module/Select/SelectStatus.vue'
import SelectAvailability from '@/Components/Admin/School/Module/Select/SelectAvailability.vue'

// Локализация и уведомления
const toast = useToast()
const { t } = useI18n()

// Входящие свойства страницы
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    module: { type: Object, required: true },
    courses: { type: Array, default: () => [] },
})

// Создание пустого объекта перевода
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// Построение переводов из ресурса
const buildTranslations = () => {
    const result = {}

    ;(props.module.translations || []).forEach((translation) => {
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

    const defaultLocale =
        props.currentLocale ||
        props.module.translation?.locale ||
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

// Локаль по умолчанию
const defaultLocale =
    props.currentLocale ||
    props.module.translation?.locale ||
    props.availableLocales[0] ||
    'ru'

// Активная локаль вкладки
const activeLocale = ref(defaultLocale)

// Форма редактирования модуля
const form = useForm({
    _method: 'PUT',

    school_course_id:
        props.module.school_course_id ??
        props.module.course?.id ??
        null,

    sort: props.module.sort ?? 0,
    activity: Boolean(props.module.activity),

    slug: props.module.slug ?? '',

    difficulty: props.module.difficulty ?? 0,
    duration: Number(props.module.duration ?? 0),
    availability: props.module.availability ?? 'public',
    status: props.module.status ?? 'draft',
    published_at: props.module.published_at ?? '',

    lessons_count: props.module.lessons_count ?? 0,
    popularity: props.module.popularity ?? 0,
    rating_count: props.module.rating_count ?? 0,
    rating_avg: props.module.rating_avg ?? 0,
    views: props.module.views ?? 0,
    likes: props.module.likes ?? 0,

    translations: buildTranslations(),

    deletedImages: [],
})

// Текущий активный перевод
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Заголовок страницы редактирования
const pageTitle = computed(() => {
    return currentTranslation.value.title
        || props.module.translation?.title
        || props.module.title
        || `ID: ${props.module.id}`
})

// Получение ошибки поля перевода
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Форматирование даты для input[type="date"]
const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (isNaN(date.getTime())) return ''

    return date.toISOString().split('T')[0]
}

// Подготовка даты после загрузки страницы
onMounted(() => {
    if (form.published_at) {
        form.published_at = formatDate(form.published_at)
    }
})

// Динамический лимит multiselect
const dynamicOptionsLimit = (items) => {
    if (!items) return 10
    return items.length + 10
}

// Список курсов для multiselect
const courseOptions = computed(() =>
    props.courses.map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${item.title || item.slug || `#${item.id}`}`,
    }))
)

// Выбранный курс
const selectedCourse = ref(
    courseOptions.value.find(item => item.id === form.school_course_id) || null
)

// Синхронизация выбранного курса с формой
watch(selectedCourse, (val) => {
    form.school_course_id = val?.id ?? null
})

// Существующие изображения модуля
const existingImages = ref(
    (props.module.images || [])
        .filter(image => image.webp_url || image.url || image.image_url)
        .map(image => ({
            id: image.id,
            url: image.webp_url || image.url || image.image_url,
            order: image.order || 0,
            alt: image.alt || '',
            caption: image.caption || '',
        }))
)

// Новые загруженные изображения
const newImages = ref([])

// Обновление существующих изображений
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images || []
}

// Удаление существующего изображения
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value.filter(image => image.id !== deletedId)
}

// Обновление новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// Автогенерация slug из названия
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

// Обрезка текста до нужной длины
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

// Очистка SEO-полей
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

// Автоматическая генерация SEO-полей
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

// Отправка формы редактирования
const submitForm = () => {
    form.transform((data) => {
        // Преобразование числовых значений
        const toNum = (val, digits = 2) => {
            if (val === '' || val === null || typeof val === 'undefined') return null

            const n = Number(val)

            return Number.isFinite(n) ? Number(n.toFixed(digits)) : null
        }

        let difficulty = toNum(data.difficulty, 2)

        if (difficulty !== null) {
            if (difficulty < 0) difficulty = 0
            if (difficulty > 5) difficulty = 5
        }

        // Подготовка данных перед отправкой
        const transformed = {
            ...data,

            school_course_id: selectedCourse.value?.id ?? null,

            difficulty,
            duration: data.duration === '' || data.duration === null
                ? null
                : Number(data.duration),

            lessons_count: data.lessons_count === '' || data.lessons_count === null
                ? 0
                : Number(data.lessons_count),

            popularity: data.popularity === '' || data.popularity === null
                ? 0
                : Number(data.popularity),

            rating_count: data.rating_count === '' || data.rating_count === null
                ? 0
                : Number(data.rating_count),

            rating_avg: data.rating_avg === '' || data.rating_avg === null
                ? 0
                : Number(data.rating_avg),

            views: data.views === '' || data.views === null
                ? 0
                : Number(data.views),

            likes: data.likes === '' || data.likes === null
                ? 0
                : Number(data.likes),

            activity: data.activity ? 1 : 0,
        }

        // Исключение служебных полей
        delete transformed.images
        delete transformed.deletedImages

        let index = 0

        // Добавление существующих изображений
        existingImages.value.forEach((image) => {
            transformed[`images[${index}][id]`] = image.id
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        // Добавление новых изображений
        newImages.value.forEach((image) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
            index++
        })

        // Добавление удалённых изображений
        form.deletedImages.forEach((id, deletedIndex) => {
            transformed[`deletedImages[${deletedIndex}]`] = id
        })

        return transformed
    })

    // Отправка формы на сервер
    form.post(route('admin.schoolModules.update', {
        schoolModule: props.module.id,
    }), {
        errorBag: 'editSchoolModule',
        preserveScroll: true,
        forceFormData: true,
        // Успешное обновление модуля
        onSuccess: () => {
            toast.success('Модуль успешно обновлён!')
            newImages.value = []
            form.deletedImages = []
        },
        // Обработка ошибок формы
        onError: (errors) => {
            console.error('Ошибка обновления модуля:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editModule')">
        <template #header>
            <TitlePage>
                {{ t('editModule') }}: {{ pageTitle }} [ID: {{ props.module.id }}]
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
                            <LabelInput for="school_course_id" :value="t('course')" class="mb-1" />

                            <VueMultiselect
                                id="school_course_id"
                                v-model="selectedCourse"
                                :options="courseOptions"
                                :options-limit="dynamicOptionsLimit(courseOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.school_course_id" />
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
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="subtitle"
                                        :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                        {{ (currentTranslation.subtitle || '').length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea v-model="currentTranslation.subtitle" class="w-full" />
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
                                    <template #default>
                                        {{ t('clearMetaFields') }}
                                    </template>
                                </ClearMetaButton>

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
