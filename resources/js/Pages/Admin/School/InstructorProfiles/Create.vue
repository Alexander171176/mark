<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Создание инструктора (паттерн)
 */
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import VueMultiselect from 'vue-multiselect'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputDecimal from '@/Components/Admin/Input/InputDecimal.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'

// --- Инициализация ---
const { t } = useI18n()
const toast = useToast()

/**
 * Из контроллера: ['users' => User::select('id','name')->orderBy('name')->get()]
 */
const props = defineProps({
    users: { type: Array, default: () => [] },
})

/** 🔹 Один выбранный инструктор */
const selectedInstructorProfile = ref(null)

/** 🔹 Опции селекта инструктора */
const userOptions = computed(() =>
    props.users.map(u => ({ id: u.id, label: u.name || `#${u.id}` }))
)

/** Форма создания инструктора */
const form = useForm({
    activity: true,
    sort: 0,
    locale: '', // en, kk, ru
    title: '',
    slug: '',
    short: '', // Краткое описание
    bio: '',
    user_id: null,
    experience_years: 0,
    hourly_rate: '',     // строка, как в Edit.vue
    rating_count: 0,
    rating_avg: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
    social_links: {},    // будет объект вида { github: '...', telegram: '...' }
    images: [],          // массив объектов { file, order, alt, caption }
})

/** Новые изображения (из MultiImageUpload) */
const newImages = ref([])

/** Обновление новых изображений */
const handleNewImagesUpdate = (images) => {
    newImages.value = images
}

/** Автогенерация slug по фокусу */
const handleSlugFocus = () => {
    if (!form.slug && form.title) {
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

/**
 * Соц. ссылки как строки [{platform, url}]
 */
const socialRows = ref([]) // [{ platform:'github', url:'https://github.com/...' }, ...]

/** Добавить новую строку контакта */
const addSocialRow = () => socialRows.value.push({ platform: '', url: '' })

/** Удалить строку контакта */
const removeSocialRow = (idx) => socialRows.value.splice(idx, 1)

/**
 * Собираем соц. ссылки в формат:
 * {
 *   github: 'https://...',
 *   telegram: 'https://...',
 * }
 */
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

/** Генерация meta-полей, если не заданы вручную. */
const generateMetaFields = () => {
    // meta_title
    if (form.title && !form.meta_title) {
        form.meta_title = truncateText(form.title, 160)
    }

    // meta_keywords из short
    if (!form.meta_keywords && form.short) {
        // 1. убираем HTML
        let text = form.short.replace(/(<([^>]+)>)/gi, '')

        // 2. убираем пунктуацию
        text = text.replace(/[.,!?;:()\[\]{}"'«»]/g, '')

        // 3. разбиваем на слова
        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        const keywords = words.join(', ')
        form.meta_keywords = truncateText(keywords, 255)
    }

    // meta_desc из short
    if (form.short && !form.meta_desc) {
        const descText = form.short.replace(/(<([^>]+)>)/gi, '')
        form.meta_desc = truncateText(descText, 255, true)
    }
}

/**
 * Отправка формы создания инструктора.
 * Логика приведена к той же схеме, что и в Edit.vue:
 * - activity -> 1/0
 * - social_links -> объект
 * - images -> массив объектов { file, order, alt, caption }
 * - forceFormData: true
 */
const submit = () => {
    const social_links = buildSocialLinks()

    form.transform((data) => {
        const toNum = (val, digits = 2) => {
            if (val === '' || val === null || typeof val === 'undefined') return null
            const n = Number(val)
            return Number.isFinite(n) ? Number(n.toFixed(digits)) : null
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
            social_links,
            images: newImages.value.map((img) => ({
                file: img.file,
                order: img.order ?? 0,
                alt: img.alt ?? '',
                caption: img.caption ?? '',
            })),
        }
    })

    form.post(route('admin.instructorProfiles.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Инструктор успешно создан!')
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createInstructor')">
        <template #header>
            <TitlePage>{{ t('createInstructor') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.instructorProfiles.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                    <div
                        class="grid grid-flow-col
                               sm:auto-cols-max justify-start sm:justify-end gap-2"
                    />
                </div>

                <form
                    @submit.prevent="submit"
                    enctype="multipart/form-data"
                    class="p-3 w-full"
                >
                    <div class="pb-12">
                        <!-- Активность + Локализация + Сортировка -->
                        <div
                            class="mb-3 flex justify-between flex-col
                                   lg:flex-row items-center gap-4"
                        >
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="form.activity" />
                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <!-- Локализация -->
                            <div class="flex flex-row items-center w-auto">
                                <SelectLocale
                                    v-model="form.locale"
                                    :errorMessage="form.errors.locale"
                                />
                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.locale"
                                />
                            </div>

                            <!-- Сортировка -->
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
                                    class="w-full lg:w-28"
                                />
                                <InputError
                                    class="mt-2 lg:mt-0"
                                    :message="form.errors.sort"
                                />
                            </div>
                        </div>

                        <!-- Название -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="title">
                                <span
                                    class="text-red-500 dark:text-red-300 font-semibold"
                                >*</span
                                >
                                {{ t('title') }}
                            </LabelInput>
                            <InputText
                                id="title"
                                type="text"
                                v-model="form.title"
                                required
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <!-- Slug: автогенерация по фокусу -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="slug">
                                <span
                                    class="text-red-500 dark:text-red-300 font-semibold"
                                >*</span
                                >
                                {{ t('slug') }}
                            </LabelInput>
                            <InputText
                                id="slug"
                                type="text"
                                v-model="form.slug"
                                autocomplete="off"
                                class="w-full"
                                @focus="handleSlugFocus"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <!-- Привязка к пользователю -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="user_id"
                                :value="t('instructor')"
                                class="mb-1"
                            />
                            <VueMultiselect
                                id="user_id"
                                v-model="selectedInstructorProfile"
                                :options="userOptions"
                                :multiple="false"
                                :close-on-select="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="min-w-[240px] w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.user_id"
                            />
                        </div>

                        <!-- Опыт и ставка -->
                        <div
                            class="mb-3 flex justify-between flex-col
                                   lg:flex-row items-center gap-4"
                        >
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

                        <!-- Рейтинги -->
                        <div
                            class="mb-3 flex justify-between flex-col
                                   lg:flex-row items-center gap-4"
                        >
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
                                    min="0"
                                    v-model.number="form.rating_count"
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

                        <!-- Краткое описание инструктора -->
                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    for="short"
                                    :value="t('shortDescription')"
                                />
                                <div
                                    class="text-md text-gray-900 dark:text-gray-400 mt-1"
                                >
                                    {{ form.short.length }} / 255 {{ t('characters') }}
                                </div>
                            </div>
                            <MetaDescTextarea v-model="form.short" class="w-full" />
                            <InputError class="mt-2" :message="form.errors.short" />
                        </div>

                        <!-- Биография -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="bio" :value="t('bio')" />
                            <TinyEditor v-model="form.bio" :height="500" />
                            <InputError class="mt-2" :message="form.errors.bio" />
                        </div>

                        <!-- Социальные ссылки -->
                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="social" :value="t('socialLinks')" />
                            <div class="w-full space-y-2">
                                <div
                                    v-for="(row, idx) in socialRows"
                                    :key="idx"
                                    class="flex flex-col lg:flex-row gap-2
                                           items-start lg:items-center"
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
                                    <!-- Удалить ссылку -->
                                    <DeleteIconButton
                                        :title="t('delete')"
                                        @click="removeSocialRow(idx)"
                                    />
                                </div>

                                <!-- Добавить ссылку -->
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

                        <!-- Мета title -->
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
                                autocomplete="url"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.meta_title"
                            />
                        </div>

                        <!-- Мета keywords -->
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
                                autocomplete="url"
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

                        <!-- Кнопка генерации метатегов -->
                        <div class="flex justify-end mt-4">
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

                        <!-- Блок загрузки новых изображений -->
                        <div class="mt-4">
                            <MultiImageUpload @update:images="handleNewImagesUpdate" />
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton
                            :href="route('admin.instructorProfiles.index')"
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
