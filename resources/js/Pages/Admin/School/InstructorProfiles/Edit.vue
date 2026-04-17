<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Редактирование инструктора (паттерн)
 */
import { useToast } from "vue-toastification";
import { useI18n } from 'vue-i18n';
import { transliterate } from '@/utils/transliteration';
import { computed, defineProps, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue';
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue';
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue';
import ClearMetaButton from '@/Components/Admin/Buttons/ClearMetaButton.vue';
import MetatagsButton from '@/Components/Admin/Buttons/MetatagsButton.vue';
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue';
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue';
import TinyEditor from "@/Components/Admin/TinyEditor/TinyEditor.vue";
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue';
import InputNumber from '@/Components/Admin/Input/InputNumber.vue';
import LabelInput from '@/Components/Admin/Input/LabelInput.vue';
import InputText from '@/Components/Admin/Input/InputText.vue';
import InputError from '@/Components/Admin/Input/InputError.vue';
import SelectLocale from "@/Components/Admin/Select/SelectLocale.vue";
import VueMultiselect from 'vue-multiselect';

// Импорт двух отдельных компонентов для работы с изображениями:
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'; // для загрузки новых изображений
import MultiImageEdit from '@/Components/Admin/Image/MultiImageEdit.vue';
import InputDecimal from '@/Components/Admin/Input/InputDecimal.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'     // для редактирования существующих

// --- Инициализация ---
const toast = useToast();
const { t } = useI18n();

/**  Входные свойства компонента. */
const props = defineProps({
    instructorProfile: { type: Object, required: true },
    users: { type: Array, default: () => [] }
});

/** Формируем форму редактирования. */
const form = useForm({
    _method: 'PUT',
    user_id: props.instructorProfile.user_id ?? props.instructorProfile.user?.id ?? null,
    sort: props.instructorProfile.sort ?? 0,
    locale: props.instructorProfile.locale ?? '',
    title: props.instructorProfile.title ?? '',
    slug: props.instructorProfile.slug ?? '',
    short: props.instructorProfile.short ?? '',
    bio: props.instructorProfile.bio ?? '',
    experience_years: Number(props.instructorProfile.experience_years ?? 0),
    hourly_rate: props.instructorProfile.hourly_rate != null ? String(props.instructorProfile.hourly_rate) : '',
    rating_count: Number(props.instructorProfile.rating.count ?? 0),
    rating_avg: props.instructorProfile.rating.avg != null ? String(props.instructorProfile.rating.avg) : '',
    social_links: props.instructorProfile.social_links ?? {},
    meta_title: props.instructorProfile.meta_title ?? '',
    meta_keywords: props.instructorProfile.meta_keywords ?? '',
    meta_desc: props.instructorProfile.meta_desc ?? '',
    activity: Boolean(props.instructorProfile.activity),
    deletedImages: [] // массив для хранения ID удалённых изображений
});

/** 🔹 Опции селекта инструктора */
const userOptions = computed(() =>
    props.users.map(u => ({ id: u.id, label: u.name || `#${u.id}` }))
)

/** 🔹 Один выбранный инструктор (single) */
const selectedInstructorProfile = ref(
    userOptions.value.find(o => o.id === (props.instructorProfile.user_id ?? props.instructorProfile.user?.id)) ?? null
)

/** 🔹 Синхронизируем выбранного инструктора */
watch(selectedInstructorProfile, val => { form.user_id = val?.id ?? null })

/** Массив существующих изображений. */
const existingImages = ref(
    (props.instructorProfile.images || [])
        .filter(img => img.url) // фильтруем изображения, у которых есть URL
        .map(img => ({
            id: img.id,
            // Если есть WebP-версия, используем её, иначе — оригинальный URL
            url: img.webp_url || img.url,
            order: img.order || 0,
            alt: img.alt || '',
            caption: img.caption || ''
        }))
);

/** Массив для новых изображений (будут содержать свойство file). */
const newImages = ref([]);

/** Обработчик обновления существующих изображений, приходящих из компонента MultiImageEdit. */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = images;
};

/** Обработчик удаления изображения из существующего списка. */
const handleDeleteExistingImage = (deletedId) => {
    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId);
    }
    existingImages.value = existingImages.value.filter(img => img.id !== deletedId);
    //console.log("Deleted IDs:", form.deletedImages);
    //console.log("Remaining images:", existingImages.value);
};

/** Обработчик обновления новых изображений из компонента MultiImageUpload. */
const handleNewImagesUpdate = (images) => {
    newImages.value = images;
};

/** Автогенерация slug по фокусу */
const handleUrlInputFocus = () => {
    if (form.title) {
        form.slug = transliterate(form.title.toLowerCase());
    }
};

/** Обрезка текста для мета-тегов */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (text.length <= maxLength) return text;
    const truncated = text.substr(0, text.lastIndexOf(' ', maxLength));
    return addEllipsis ? `${truncated}...` : truncated;
};

/** очистка мета-тегов. */
const clearMetaFields = () => {
    form.meta_title = '';
    form.meta_keywords = '';
    form.meta_desc = '';
};

/**
 * Генерирует значения для мета-полей (title, keywords, description),
 * если они не были заполнены вручную.
 */
const generateMetaFields = () => {
    if (form.title && !form.meta_title) {
        form.meta_title = truncateText(form.title, 160);
    }
    // Генерация meta_keywords из form.short
    if (!form.meta_keywords && form.short) {
        // 1. Удаляем HTML-теги (на случай, если они есть в form.short)
        let text = form.short.replace(/(<([^>]+)>)/gi, "");

        // 2. Удаляем знаки препинания, кроме дефисов внутри слов (опционально)
        //    Оставляем буквы (включая кириллицу/другие языки), цифры, дефисы и пробелы
        text = text.replace(/[.,!?;:()\[\]{}"'«»]/g, ''); // Удаляем основную пунктуацию
        // text = text.replace(/[^\p{L}\p{N}\s-]/gu, ''); // Более строгий вариант: оставить только буквы, цифры, пробелы, дефис

        // 3. Разбиваем текст на слова по пробелам
        const words = text.split(/\s+/)
            // 4. Фильтруем пустые строки и короткие слова (например, менее 3 символов), если нужно
            .filter(word => word && word.length >= 3)
            // 5. Приводим к нижнему регистру (стандартно для ключевых слов)
            .map(word => word.toLowerCase())
            // 6. Удаляем дубликаты слов
            .filter((value, index, self) => self.indexOf(value) === index);

        // 7. Объединяем слова через запятую и пробел
        const keywords = words.join(', ');

        // 8. Обрезаем результат до максимальной длины (если нужно)
        form.meta_keywords = truncateText(keywords, 255); // Используем вашу функцию truncateText
    }
    if (form.short && !form.meta_desc) {
        // Убираем HTML-теги для описания
        const descText = form.short.replace(/(<([^>]+)>)/gi, "");
        form.meta_desc = truncateText(descText, 255, true); // Используем другую длину и добавление ...
    }
};

/** Соц. ссылки как строки [{platform, url}] для удобства редактирования */
const socialRows = ref(
    Array.isArray(form.social_links)
        ? form.social_links
        : form.social_links && typeof form.social_links === 'object'
            ? Object.entries(form.social_links).map(([platform, url]) => ({ platform, url }))
            : []
)

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

/** Отправляет данные формы для обновления. */
const submitForm = () => {
    const social_links = buildSocialLinks()

    form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
        social_links, // <= вот ЭТО главное
        images: [
            ...newImages.value.map(img => ({
                file: img.file,
                order: img.order,
                alt: img.alt,
                caption: img.caption
            })),
            ...existingImages.value.map(img => ({
                id: img.id,
                order: img.order,
                alt: img.alt,
                caption: img.caption
            }))
        ],
        deletedImages: form.deletedImages
    }))

    form.post(route('admin.instructorProfiles.update', props.instructorProfile.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Инструктор успешно обновлен!')
        },
        onError: (errors) => {
            console.error("❌ Ошибка при обновлении инструктора:", errors)
            const firstError = errors[Object.keys(errors)[0]]
            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        }
    })
}

</script>

<template>
    <AdminLayout :title="t('editInstructor')">
        <template #header>
            <TitlePage>
                {{ t('editInstructor') }} -
                {{ props.instructorProfile.title }} [ID: {{ props.instructorProfile.id }}]
            </TitlePage>
        </template>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        shadow-lg shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Кнопка назад -->
                    <DefaultButton :href="route('admin.instructorProfiles.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path>
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <!-- Right: Actions -->
                    <div class="grid grid-flow-col sm:auto-cols-max
                                justify-start sm:justify-end gap-2">
                        <!-- Datepicker built with flatpickr -->
                    </div>
                </div>

                <form @submit.prevent="submitForm"
                      enctype="multipart/form-data" class="p-3 w-full">

                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')"
                                           class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2 w-auto">
                            <SelectLocale v-model="form.locale" :errorMessage="form.errors.locale" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.locale" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            </div>
                            <InputNumber id="sort" type="number" v-model="form.sort"
                                         autocomplete="sort" class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="title">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('title') }}
                        </LabelInput>
                        <InputText id="title" type="text" v-model="form.title"
                                   required autocomplete="title" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="slug">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('slug') }}
                        </LabelInput>
                        <InputText id="slug" type="text" v-model="form.slug"
                                   required autocomplete="slug" @focus="handleUrlInputFocus" />
                        <InputError class="mt-2" :message="form.errors.slug" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="short" :value="t('shortDescription')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.short.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.short" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.short" />
                    </div>

                    <!-- Привязка к пользователю -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="user" :value="t('instructor')" class="mb-1" />
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
                        <InputError class="mt-2" :message="form.errors.user_id" />
                    </div>

                    <!-- Опыт и ставка -->
                    <div class="mb-3 flex justify-between flex-col
                                    lg:flex-row items-center gap-4">
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
                            <InputError class="mt-2" :message="form.errors.experience_years" />
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
                            <InputError class="mt-2" :message="form.errors.hourly_rate" />
                        </div>
                    </div>

                    <!-- Рейтинги -->
                    <div class="mb-3 flex justify-between flex-col
                                    lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="rating_count" :value="t('ratingCount')"
                                            class="text-sm" />
                            </div>
                            <InputNumber
                                id="rating_count"
                                type="number"
                                v-model.number="form.rating_count"
                                min="0"
                                autocomplete="rating_count"
                                class="w-full lg:w-28"
                            />
                            <InputError class="mt-2 lg:mt-0"
                                        :message="form.errors.rating_count" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <div class="h-8 flex items-center">
                                <LabelInput for="rating_avg" :value="t('ratingAvg')"
                                            class="text-sm" />
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
                            <InputError class="mt-2 lg:mt-0"
                                        :message="form.errors.rating_avg" />
                        </div>
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
                                <DeleteIconButton :title="t('delete')"
                                                  @click="removeSocialRow(idx)"/>
                            </div>

                            <!-- Добавить ссылку -->
                            <PrimaryButton type="button" class="mt-2 float-end"
                                           @click="addSocialRow">
                                <template #icon>
                                    <svg class="w-4 h-4 fill-current text-slate-100"
                                         viewBox="0 0 24 24">
                                        <path d="M12 5v14M5 12h14" stroke="currentColor"
                                              stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </template>
                                {{ t('addLink') }}
                            </PrimaryButton>
                        </div>
                        <InputError class="mt-2" :message="form.errors.social_links" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_title" :value="t('metaTitle')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_title.length }} / 160 {{ t('characters') }}
                            </div>
                        </div>
                        <InputText id="meta_title" type="text" v-model="form.meta_title"
                                   maxlength="160" autocomplete="url" />
                        <InputError class="mt-2" :message="form.errors.meta_title" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_keywords" :value="t('metaKeywords')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_keywords.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <InputText id="meta_keywords"
                                   type="text"
                                   v-model="form.meta_keywords"
                                   maxlength="255"
                                   autocomplete="url" />
                        <InputError class="mt-2" :message="form.errors.meta_keywords" />
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="meta_desc" :value="t('metaDescription')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.meta_desc.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea v-model="form.meta_desc" maxlength="255" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.meta_desc" />
                    </div>

                    <div class="flex justify-end mt-4">
                        <!-- Кнопка очистки мета-полей -->
                        <ClearMetaButton @clear="clearMetaFields" class="mr-4">
                            <template #default>
                                <svg class="w-4 h-4 fill-current text-gray-500 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M8 0C3.58 0 0 3.58 0 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3 9H5V7h6v2z"/>
                                </svg>
                                {{ t('clearMetaFields') }}
                            </template>
                        </ClearMetaButton>
                        <!-- Кнопка генерации мета-полей -->
                        <MetatagsButton @click.prevent="generateMetaFields">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z"></path>
                                </svg>
                            </template>
                            {{ t('generateMetaTags') }}
                        </MetatagsButton>

                    </div>

                    <!-- Блок редактирования существующих изображений -->
                    <div class="mt-4">
                        <MultiImageEdit
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete-image="handleDeleteExistingImage" />
                    </div>

                    <!-- Блок загрузки новых изображений -->
                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.instructorProfiles.index')" class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"></path>
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>
                        <PrimaryButton class="ms-4 mb-0"
                                       :disabled="form.processing"
                                       :class="{ 'opacity-25': form.processing }">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
                                     viewBox="0 0 16 16">
                                    <path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"></path>
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
