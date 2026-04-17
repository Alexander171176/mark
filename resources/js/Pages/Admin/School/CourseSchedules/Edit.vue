<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Редактирование расписания потока
 */
import { computed, onMounted, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import ClearMetaButton from '@/Components/Admin/Buttons/ClearMetaButton.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import SelectStatus from '@/Components/Admin/CourseSchedule/Select/SelectStatus.vue'
import SelectTimezone from '@/Components/Admin/CourseSchedule/Select/SelectTimezone.vue'

import VueMultiselect from 'vue-multiselect'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'
import MultiImageEdit from '@/Components/Admin/Image/MultiImageEdit.vue'

// --- Инициализация ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из контроллера:
 * - schedule        (CourseScheduleResource)
 * - courses         (CourseResource[])
 * - instructors     (InstructorProfileResource[])
 * - currentLocale
 */
const props = defineProps({
    schedule: { type: Object, required: true },
    courses: { type: Array, default: () => [] },
    instructors: { type: Array, default: () => [] },
    currentLocale: { type: String, default: 'ru' },
})

/** Инициализация формы на основе CourseScheduleResource */
const form = useForm({
    _method: 'PUT',

    // связи
    course_id:
        props.schedule.course_id
        ?? props.schedule.course?.id
        ?? (props.courses[0]?.id ?? null),

    // ВАЖНО: используем instructor_profile_id (как в Create.vue / Request)
    instructor_profile_id:
        props.schedule.instructor_profile_id
        ?? props.schedule.instructor_id
        ?? props.schedule.instructor?.id
        ?? null,

    activity: Boolean(props.schedule.activity),
    sort: props.schedule.sort ?? 0,

    // локаль и базовые поля
    locale: props.schedule.locale ?? props.currentLocale ?? 'ru',
    title: props.schedule.title ?? '',
    slug: props.schedule.slug ?? '',
    subtitle: props.schedule.subtitle ?? '',
    short: props.schedule.short ?? '',
    description: props.schedule.description ?? '',

    // параметры
    status: props.schedule.status ?? 'draft',
    starts_at: props.schedule.starts_at ?? '',
    ends_at: props.schedule.ends_at ?? '',
    enroll_starts_at: props.schedule.enroll_starts_at ?? '',
    enroll_ends_at: props.schedule.enroll_ends_at ?? '',
    capacity: props.schedule.capacity ?? 0,
    is_online: Boolean(props.schedule.is_online),
    location: props.schedule.location ?? '',
    meeting_url: props.schedule.meeting_url ?? '',
    timezone: props.schedule.timezone ?? '',
    notes: props.schedule.notes ?? '',

    // мета-поля
    meta_title: props.schedule.meta_title ?? '',
    meta_keywords: props.schedule.meta_keywords ?? '',
    meta_desc: props.schedule.meta_desc ?? '',

    // метрики
    views: props.schedule.views ?? 0,

    // удаление изображений
    deletedImages: [],
})

/**
 * Универсальный лимит для любых options:
 * количество элементов + 10 запас.
 */
const dynamicOptionsLimit = (options) => {
    const count = options?.length ?? 0
    return count + 10
}

/** Функция форматирования даты под input[type=date] */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    if (isNaN(date.getTime())) return ''
    return date.toISOString().split('T')[0]
}

/** Приводим даты к формату YYYY-MM-DD для инпутов */
onMounted(() => {
    if (form.starts_at) form.starts_at = formatDate(form.starts_at)
    if (form.ends_at) form.ends_at = formatDate(form.ends_at)
    if (form.enroll_starts_at) form.enroll_starts_at = formatDate(form.enroll_starts_at)
    if (form.enroll_ends_at) form.enroll_ends_at = formatDate(form.enroll_ends_at)
})

/** 🔹 Опции селекта курса */
const courseOptions = computed(() =>
    props.courses.map(c => {
        const localePart = c.locale ? ` [${c.locale}]` : ''
        const titlePart  = c.title || c.slug || `#${c.id}`
        const slugPart   = c.slug ? ` (${c.slug})` : ''

        return {
            id: c.id,
            label: `[ID: ${c.id}]${localePart} - ${titlePart}${slugPart}`,
        }
    })
)

/** 🔹 Выбранный курс */
const selectedCourse = ref(null)

/** Инициализируем выбранный курс по form.course_id */
watch(
    courseOptions,
    (options) => {
        if (!options.length) {
            selectedCourse.value = null
            form.course_id = null
            return
        }

        if (form.course_id) {
            const found = options.find(o => o.id === form.course_id)
            selectedCourse.value = found || null
        } else {
            selectedCourse.value = null
            form.course_id = null
        }
    },
    { immediate: true }
)

/** Синхронизация при смене selectedCourse */
watch(selectedCourse, (val) => {
    form.course_id = val ? val.id : null
})

/** 🔹 Опции селекта профилей инструктора */
const instructorOptions = computed(() =>
    props.instructors.map(i => {
        const userName = i.user?.name ?? null
        const title = i.title || ''

        let label = title || `#${i.id}`

        if (userName) {
            label = `[${userName}] - ${title || `#${i.id}`}`
        }

        return {
            id: i.id,
            label,
        }
    })
)

/** 🔹 Выбранный инструктор */
const selectedInstructor = ref(null)

/** Инициализация выбранного инструктора по form.instructor_profile_id */
watch(
    instructorOptions,
    (options) => {
        if (!options.length) {
            selectedInstructor.value = null
            form.instructor_profile_id = null
            return
        }

        if (form.instructor_profile_id) {
            const found = options.find(o => o.id === form.instructor_profile_id)
            selectedInstructor.value = found || null
        } else {
            selectedInstructor.value = null
            form.instructor_profile_id = null
        }
    },
    { immediate: true }
)

/** Синхронизация при смене selectedInstructor */
watch(selectedInstructor, (val) => {
    form.instructor_profile_id = val ? val.id : null
})

/** Существующие изображения расписания. */
const existingImages = ref(
    (props.schedule.images || [])
        .filter(img => img.url || img.webp_url)
        .map(img => ({
            id: img.id,
            url: img.webp_url || img.url,
            order: img.order || 0,
            alt: img.alt || '',
            caption: img.caption || '',
        }))
)

/** Новые изображения (из MultiImageUpload). */
const newImages = ref([])

/** Обработчики изображений */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images
}

const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }
    existingImages.value = existingImages.value.filter(img => img.id !== deletedId)
}

const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

/** Автогенерация slug по фокусу */
const handleSlugFocus = () => {
    if (form.title && !form.slug) {
        form.slug = transliterate(form.title.toLowerCase())
    }
}

/** Обрезка текста для мета-тегов */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    const truncated = text.substr(0, text.lastIndexOf(' ', maxLength))
    return addEllipsis ? `${truncated}...` : truncated
}

/** Очистка мета-полей */
const clearMetaFields = () => {
    form.meta_title = ''
    form.meta_keywords = ''
    form.meta_desc = ''
}

/** Генерация meta-полей, если не заданы вручную. */
const generateMetaFields = () => {
    if (form.title && !form.meta_title) {
        form.meta_title = truncateText(form.title, 160)
    }

    if (!form.meta_keywords && form.short) {
        let text = form.short.replace(/(<([^>]+)>)/gi, '')
        text = text.replace(/[.,!?;:()\[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        const keywords = words.join(', ')
        form.meta_keywords = truncateText(keywords, 255)
    }

    if (form.short && !form.meta_desc) {
        const descText = form.short.replace(/(<([^>]+)>)/gi, '')
        form.meta_desc = truncateText(descText, 255, true)
    }
}

/** Отправка формы обновления потока */
const submitForm = () => {
    form.transform((data) => {
        return {
            ...data,
            activity: data.activity ? 1 : 0,
            is_online: data.is_online ? 1 : 0,

            images: [
                // новые
                ...newImages.value.map(img => ({
                    file: img.file,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                })),
                // существующие
                ...existingImages.value.map(img => ({
                    id: img.id,
                    order: img.order ?? 0,
                    alt: img.alt ?? '',
                    caption: img.caption ?? '',
                })),
            ],
            deletedImages: data.deletedImages,
        }
    })

    form.post(route('admin.courseSchedules.update', props.schedule.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Расписание потока успешно обновлено!')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении Расписания потока:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editSchedule')">
        <template #header>
            <TitlePage>
                {{ t('editSchedule') }} - {{schedule.title}} [ID: {{schedule.id}}]
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
                    <!-- Кнопка назад -->
                    <DefaultButton :href="route('admin.courseSchedules.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    @submit.prevent="submitForm"
                    enctype="multipart/form-data"
                    class="p-3 w-full">

                    <!-- Активность, локаль, сортировка -->
                    <div
                        class="mb-3 flex justify-between flex-col
                               lg:flex-row items-center gap-4">

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2 w-auto">
                            <SelectLocale
                                v-model="form.locale"
                                :errorMessage="form.errors.locale"
                            />
                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.locale"
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
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />
                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Места, Статус -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                        <!-- Capacity -->
                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="capacity"
                                :value="t('capacity')"
                            />
                            <InputNumber
                                id="capacity"
                                type="number"
                                v-model="form.capacity"
                                min="0"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.capacity" />
                        </div>

                        <!-- Статус -->
                        <SelectStatus v-model="form.status"
                                      :errorMessage="form.errors.status" />

                    </div>

                    <!-- Даты потока: starts_at / ends_at -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="starts_at"
                                :value="t('scheduleStartsAt')"
                            />
                            <InputText
                                id="starts_at"
                                type="date"
                                v-model="form.starts_at"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.starts_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="ends_at"
                                :value="t('scheduleEndsAt')"
                            />
                            <InputText
                                id="ends_at"
                                type="date"
                                v-model="form.ends_at"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.ends_at" />
                        </div>
                    </div>

                    <!-- Даты записи: enroll_starts_at / enroll_ends_at -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="enroll_starts_at"
                                :value="t('scheduleEnrollStartsAt')"
                            />
                            <InputText
                                id="enroll_starts_at"
                                type="date"
                                v-model="form.enroll_starts_at"
                                autocomplete="off"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.enroll_starts_at"
                            />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="enroll_ends_at"
                                :value="t('scheduleEnrollEndsAt')"
                            />
                            <InputText
                                id="enroll_ends_at"
                                type="date"
                                v-model="form.enroll_ends_at"
                                autocomplete="off"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.enroll_ends_at"
                            />
                        </div>
                    </div>

                    <!-- Параметры потока: is_online / ссылка на трансляцию -->
                    <div class="mb-3 flex justify-between flex-col
                                lg:flex-row items-center gap-4">

                        <!-- Формат: онлайн/офлайн -->
                        <div class="flex flex-row items-start w-fit">
                            <div class="flex items-center gap-2 h-10">
                                <ActivityCheckbox v-model="form.is_online" />
                                <LabelCheckbox
                                    for="is_online"
                                    :text="t('online')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.is_online" />
                        </div>

                        <!-- meeting_url (для онлайн) -->
                        <div class="flex flex-row items-center w-full
                                     pl-1 border-l border-slate-400">
                            <LabelInput
                                class="w-fit"
                                for="meeting_url"
                                :value="t('meetingUrl')"
                            />
                            <svg class="w-4 h-3 fill-red-500 dark:fill-red-200 mr-2"
                                 viewBox="0 0 16 12">
                                <path d="m16 2-4 2.4V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.6l4 2.4V2ZM2 10V2h8v8H2Z"></path>
                            </svg>
                            <InputText
                                id="meeting_url"
                                type="url"
                                v-model="form.meeting_url"
                                autocomplete="off"
                                placeholder="https://..."
                            />
                            <InputError class="mt-2" :message="form.errors.meeting_url" />
                        </div>
                    </div>

                    <!-- Часовой пояс и Локация -->
                    <div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4">

                        <!-- Часовой пояс -->
                        <SelectTimezone
                            v-model="form.timezone"
                            :errorMessage="form.errors.timezone"
                        />

                        <!-- Локация (для оффлайн) -->
                        <div class="flex flex-col items-start">
                            <LabelInput
                                for="location"
                                :value="t('location')"
                            />
                            <InputText
                                id="location"
                                type="text"
                                v-model="form.location"
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.location" />
                        </div>
                    </div>

                    <!-- Курс -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="course"
                            :value="t('course')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="course"
                            v-model="selectedCourse"
                            :options="courseOptions"
                            :options-limit="dynamicOptionsLimit(courseOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.course_id" />
                    </div>

                    <!-- Инструктор -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="instructor"
                            :value="t('instructor')"
                            class="mb-1"
                        />
                        <VueMultiselect
                            id="instructor"
                            v-model="selectedInstructor"
                            :options="instructorOptions"
                            :options-limit="dynamicOptionsLimit(instructorOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.instructor_profile_id" />
                    </div>

                    <!-- Название -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="title">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('title') }}
                        </LabelInput>
                        <InputText
                            id="title"
                            type="text"
                            v-model="form.title"
                            required
                            autocomplete="title"
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <!-- Slug -->
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

                    <!-- Подзаголовок/оффер -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="subtitle"
                                :value="t('subtitle')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.subtitle.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.subtitle" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.subtitle" />
                    </div>

                    <!-- Краткое описание -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="short"
                                :value="t('shortDescription')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.short.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.short" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.short" />
                    </div>

                    <!-- Описание курса -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="description" :value="t('description')" />
                        <TinyEditor v-model="form.description" :height="500" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
                        />
                    </div>

                    <!-- Заметки администратора -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput
                            for="notes"
                            :value="t('notes')"
                        />
                        <MetaDescTextarea
                            id="notes"
                            v-model="form.notes"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>

                    <!-- Мета Title -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_title"
                                :value="t('metaTitle')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.meta_title.length }} / 160
                                {{ t('characters') }}
                            </div>
                        </div>
                        <InputText
                            id="meta_title"
                            type="text"
                            v-model="form.meta_title"
                            maxlength="160"
                            autocomplete="off"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.meta_title"
                        />
                    </div>

                    <!-- Мета Keywords -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_keywords"
                                :value="t('metaKeywords')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.meta_keywords.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <InputText
                            id="meta_keywords"
                            type="text"
                            v-model="form.meta_keywords"
                            maxlength="255"
                            autocomplete="off"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.meta_keywords"
                        />
                    </div>

                    <!-- Мета Description -->
                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput
                                for="meta_desc"
                                :value="t('metaDescription')"
                            />
                            <div
                                class="text-md text-gray-900 dark:text-gray-400 mt-1"
                            >
                                {{ form.meta_desc.length }} / 255
                                {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea
                            v-model="form.meta_desc"
                            maxlength="255"
                            class="w-full"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.meta_desc"
                        />
                    </div>

                    <!-- Кнопки мета-полей -->
                    <div class="flex justify-end mt-4">
                        <ClearMetaButton @clear="clearMetaFields" class="mr-4">
                            <template #default>
                                <svg
                                    class="w-4 h-4 fill-current text-gray-500 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3 9H5V7h6v2z"
                                    />
                                </svg>
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

                    <!-- Редактирование существующих изображений -->
                    <div class="mt-4">
                        <MultiImageEdit
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete-image="handleDeleteExistingImage"
                        />
                    </div>

                    <!-- Загрузка новых изображений -->
                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                    </div>

                    <!-- Кнопки сохранить/назад -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.courseSchedules.index')"
                            class="mb-3"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
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

<style src="/resources/css/vue-multiselect.min.css"></style>
