<script setup>
/**
 * Создание баннера блога
 * Новая мультиязычная архитектура:
 * - blog_banners
 * - blog_banner_translations
 */
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'

import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import MultiImageUpload from '@/Components/Admin/Image/MultiImageUpload.vue'
import TranslationTabs from '@/Components/Admin/Locale/TranslationTabs.vue'

const toast = useToast()
const { t } = useI18n()

const props = defineProps({
    targetLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },
    errors: { type: Object, default: () => ({}) },
})

const makeTranslation = () => ({
    title: '',
    link: '',
    short: '',
})

const defaultLocale = props.targetLocale || 'ru'
const activeLocale = ref(defaultLocale)
const newImages = ref([])

const form = useForm({
    sort: 0,

    activity: false,
    left: false,
    main: false,
    right: false,

    comment: '',

    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

const handleNewImagesUpdate = (updatedImages) => {
    newImages.value = updatedImages || []
    form.images = updatedImages || []
}

const submitForm = () => {
    form.transform((data) => {
        const transformed = {
            ...data,

            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,
        }

        newImages.value.forEach((image, index) => {
            if (image.file) {
                transformed[`images[${index}][file]`] = image.file
                transformed[`images[${index}][order]`] = image.order ?? 0
                transformed[`images[${index}][alt]`] = image.alt ?? ''
                transformed[`images[${index}][caption]`] = image.caption ?? ''
            }
        })

        return transformed
    })

    form.post(route('admin.blogBanners.store'), {
        errorBag: 'createBlogBanner',
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Баннер успешно создан!'),
        onError: (errors) => {
            console.error('Не удалось отправить форму:', errors)

            const firstError = errors?.[Object.keys(errors)[0]]

            toast.error(firstError || 'Пожалуйста, проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createBanner')">
        <template #header>
            <TitlePage>{{ t('createBanner') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.blogBanners.index')">
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-3 w-full">
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber id="sort" type="number" v-model="form.sort" class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.left" />
                            <LabelCheckbox for="left" :text="t('left')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.main" />
                            <LabelCheckbox for="main" :text="t('main')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.right" />
                            <LabelCheckbox for="right" :text="t('right')" class="text-sm h-8 flex items-center" />
                        </div>
                    </div>

                    <div class="mb-3 flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="comment" :value="t('comment')" />
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ (form.comment || '').length }} / 255 {{ t('characters') }}
                            </div>
                        </div>

                        <MetaDescTextarea v-model="form.comment" class="w-full" />
                        <InputError class="mt-2" :message="form.errors.comment" />
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
                                {{ t('bannerTitle') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText
                                id="title"
                                type="text"
                                v-model="currentTranslation.title"
                                required
                                maxlength="255"
                                autocomplete="title"
                            />

                            <InputError class="mt-2" :message="getError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                for="link"
                                :value="`${t('url')} [${activeLocale.toUpperCase()}]`"
                            />

                            <InputText
                                id="link"
                                type="url"
                                v-model="currentTranslation.link"
                                autocomplete="link"
                                pattern="^(https?:\/\/)?([\w\-]+\.)+[a-zA-Z]{2,}([\/?#][^\s]*)?$"
                                :title="t('urlVerification')"
                            />

                            <InputError class="mt-2" :message="getError('link')" />
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
                    </div>

                    <div class="mt-4">
                        <MultiImageUpload @update:images="handleNewImagesUpdate" />

                        <div v-if="newImages.length" class="text-xs text-slate-600 dark:text-slate-300 mt-2">
                            {{ t('images') }}: {{ newImages.length }}
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.blogBanners.index')" class="mb-3">
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
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
