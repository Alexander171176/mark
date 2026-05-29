<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание расписания обучения
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
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

import SelectStatus from '@/Components/Admin/School/CourseSchedule/Select/SelectStatus.vue'
import SelectTimezone from '@/Components/Admin/School/CourseSchedule/Select/SelectTimezone.vue'

// Локализация и Toast уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    instructors: { type: Array, default: () => [] },
})

// Пустая структура перевода
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

// Локаль по умолчанию
const defaultLocale = props.currentLocale || 'ru'

// Активная локаль вкладки
const activeLocale = ref(defaultLocale)

// Форма создания расписания
const form = useForm({
    school_course_id: null,
    school_instructor_profile_id: null,

    activity: true,
    sort: 0,

    slug: '',

    starts_at: '',
    ends_at: '',
    enroll_starts_at: '',
    enroll_ends_at: '',

    capacity: 0,
    is_online: true,
    location: '',
    meeting_url: '',
    timezone: '',
    status: 'draft',
    notes: '',

    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

// Текущий перевод активной локали
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

// Получение ошибки перевода
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

// Динамический лимит multiselect
const dynamicOptionsLimit = (items) => {
    if (!items) return 10

    return items.length + 10
}

// Опции курсов
const courseOptions = computed(() =>
    props.courses.map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${item.title || item.slug || `#${item.id}`}`,
    }))
)

// Опции преподавателей
const instructorOptions = computed(() =>
    props.instructors.map((item) => {
        const title = item.public_name || item.title || item.name || `#${item.id}`
        const userName = item.user?.name || item.user?.email || ''

        return {
            id: item.id,
            label: userName
                ? `[ID: ${item.id}] ${title} — ${userName}`
                : `[ID: ${item.id}] ${title}`,
        }
    })
)

// Выбранный курс и преподаватель
const selectedCourse = ref(null)
const selectedInstructor = ref(null)

// Синхронизация курса с формой
watch(selectedCourse, (val) => {
    form.school_course_id = val?.id ?? null
})

// Синхронизация преподавателя с формой
watch(selectedInstructor, (val) => {
    form.school_instructor_profile_id = val?.id ?? null
})

// Новые изображения
const newImages = ref([])

// Обновление новых изображений
const handleNewImagesUpdate = (images) => {
    newImages.value = images || []
}

// Генерация slug из title
const handleSlugFocus = () => {
    if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

// Обрезка текста
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

// Генерация SEO полей
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

// Отправка формы создания
const submit = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            school_course_id: selectedCourse.value?.id ?? null,
            school_instructor_profile_id: selectedInstructor.value?.id ?? null,

            activity: data.activity ? 1 : 0,
            is_online: data.is_online ? 1 : 0,

            sort: data.sort === '' || data.sort === null
                ? 0
                : Number(data.sort),

            capacity: data.capacity === '' || data.capacity === null
                ? 0
                : Number(data.capacity),
        }

        delete transformed.images

        newImages.value.forEach((image, index) => {
            transformed[`images[${index}][file]`] = image.file
            transformed[`images[${index}][order]`] = image.order ?? 0
            transformed[`images[${index}][alt]`] = image.alt ?? ''
            transformed[`images[${index}][caption]`] = image.caption ?? ''
        })

        return transformed
    })

    form.post(route('admin.schoolCourseSchedules.store'), {
        errorBag: 'createSchoolCourseSchedule',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Расписание успешно создано!'),
        onError: (errors) => {
            console.error('Ошибка создания расписания:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createCourseSchedule')">
        <template #header>
            <TitlePage>{{ t('createCourseSchedule') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolCourseSchedules.index')">
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
                        <div class="mb-3 flex justify-between flex-col lg:flex-row
                                    items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                                <InputError :message="form.errors.activity" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.is_online" />
                                <LabelCheckbox
                                    for="is_online"
                                    :text="t('online')"
                                    class="text-sm h-8 flex items-center"
                                />
                                <InputError :message="form.errors.is_online" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                                <InputNumber
                                    id="sort"
                                    type="number"
                                    min="0"
                                    v-model.number="form.sort"
                                    class="w-full lg:w-28"
                                />
                                <InputError :message="form.errors.sort" />
                            </div>
                        </div>

                        <div class="mb-3 flex justify-between flex-col lg:flex-row
                                    items-center gap-4">
                            <SelectStatus
                                v-model="form.status"
                                :errorMessage="form.errors.status"
                            />

                            <SelectTimezone
                                v-model="form.timezone"
                                :errorMessage="form.errors.timezone"
                            />

                            <div class="flex flex-col items-start">
                                <LabelInput for="capacity" :value="t('capacity')" />
                                <InputNumber
                                    id="capacity"
                                    type="number"
                                    min="0"
                                    v-model.number="form.capacity"
                                    class="w-full lg:w-36"
                                />
                                <InputError :message="form.errors.capacity" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="starts_at" :value="t('scheduleStartsAt')" />
                                <InputText
                                    id="starts_at"
                                    type="datetime-local"
                                    v-model="form.starts_at"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.starts_at" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="ends_at" :value="t('scheduleEndsAt')" />
                                <InputText
                                    id="ends_at"
                                    type="datetime-local"
                                    v-model="form.ends_at"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.ends_at" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="enroll_starts_at"
                                    :value="t('scheduleEnrollStartsAt')" />
                                <InputText
                                    id="enroll_starts_at"
                                    type="datetime-local"
                                    v-model="form.enroll_starts_at"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.enroll_starts_at" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="enroll_ends_at"
                                    :value="t('scheduleEnrollEndsAt')" />
                                <InputText
                                    id="enroll_ends_at"
                                    type="datetime-local"
                                    v-model="form.enroll_ends_at"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.enroll_ends_at" />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="location" :value="t('location')" />
                                <InputText
                                    id="location"
                                    type="text"
                                    v-model="form.location"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.location" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="meeting_url" :value="t('meetingUrl')" />
                                <InputText
                                    id="meeting_url"
                                    type="url"
                                    v-model="form.meeting_url"
                                    placeholder="https://..."
                                    class="w-full"
                                />
                                <InputError :message="form.errors.meeting_url" />
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="school_course_id" class="mb-1">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('course') }}
                            </LabelInput>

                            <VueMultiselect
                                id="school_course_id"
                                v-model="selectedCourse"
                                :options="courseOptions"
                                :options-limit="dynamicOptionsLimit(courseOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.school_course_id" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="school_instructor_profile_id"
                                :value="t('instructor')" class="mb-1" />

                            <VueMultiselect
                                id="school_instructor_profile_id"
                                v-model="selectedInstructor"
                                :options="instructorOptions"
                                :options-limit="dynamicOptionsLimit(instructorOptions)"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :allow-empty="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError class="mt-2"
                                        :message="form.errors.school_instructor_profile_id" />
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

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="notes" :value="t('notes')" />
                            <MetaDescTextarea
                                id="notes"
                                v-model="form.notes"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.notes" />
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
                                        <span class="text-red-500 dark:text-red-300 font-semibold">
                                            *
                                        </span>
                                        {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                                    </LabelInput>

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ currentTranslation.title.length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="title"
                                    type="text"
                                    v-model="currentTranslation.title"
                                    required
                                    class="w-full"
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
                                {{ currentTranslation.subtitle.length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    id="subtitle"
                                    v-model="currentTranslation.subtitle"
                                    class="w-full"
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
                                {{ currentTranslation.short.length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    id="short"
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
                                    :height="300"
                                />

                                <InputError class="mt-2" :message="getError('description')" />
                            </div>

                            <div class="mb-3 flex justify-end">
                                <MetatagsButton type="button" @click="generateMetaFields">
                                    {{ t('generateMetaTags') }}
                                </MetatagsButton>
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                                    <LabelInput
                                        for="meta_title"
                                        :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                                    />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                            {{ currentTranslation.meta_title.length }} / 160 {{ t('characters') }}
                                    </div>
                                </div>

                                <InputText
                                    id="meta_title"
                                    type="text"
                                    v-model="currentTranslation.meta_title"
                                    class="w-full"
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
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="getError('meta_keywords')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_desc"
                                :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                            />

                                    <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                            {{ currentTranslation.meta_desc.length }} / 255 {{ t('characters') }}
                                    </div>
                                </div>

                                <MetaDescTextarea
                                    id="meta_desc"
                                    v-model="currentTranslation.meta_desc"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="getError('meta_desc')" />
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="images"
                                :value="t('images')"
                                class="mb-1"
                            />

                            <MultiImageUpload
                                v-model:images="newImages"
                                @update:images="handleNewImagesUpdate"
                            />

                            <InputError class="mt-2" :message="form.errors.images" />
                        </div>

                        <div class="flex items-center justify-center gap-3">
                            <DefaultButton :href="route('admin.schoolCourseSchedules.index')">
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
                            <PrimaryButton type="submit" :disabled="form.processing">
                                {{ t('save') }}
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
