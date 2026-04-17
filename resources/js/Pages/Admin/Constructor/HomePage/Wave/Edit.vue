<script setup>
/**
 * Wave Edit (Admin)
 * - Редактирование: WaveSection + WaveTech[]
 */
import { computed, reactive, ref, watch } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LocaleTabs from '@/Components/Admin/Tab/LocaleTabs.vue'
import CodeTextarea from '@/Components/Admin/Textarea/CodeTextarea.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    wave:   { type: Object, required: true }, // WaveSectionResource
    teches: { type: Array,  required: true }  // WaveTechResource[]
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const mapWaveToForm = (w = {}) => ({
    locale: w.locale ?? 'ru',
    title: w.title ?? '',
    subtitle: w.subtitle ?? '',
    left_text: w.left_text ?? '',
    right_text: w.right_text ?? '',
    sort: Number.isFinite(w.sort) ? w.sort : 0,
    is_dark: !!w.is_dark,
    activity: !!w.activity
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.wave?.locale ?? 'ru')
const switching    = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.wave?.locale) return
    router.get(
        route('admin.home-page.wave.sections.edit', { section: props.wave.id, locale: loc }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onStart:  () => (switching.value = true),
            onFinish: () => (switching.value = false)
        }
    )
})

/* ---------- СЕКЦИЯ (редактирование текущей записи) ---------- */
const sectionForm = useForm(mapWaveToForm(props.wave))

const syncFormFromProps = (w) => {
    const data = mapWaveToForm(w || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => { if (k in sectionForm) sectionForm[k] = v })
}

watch(() => props.wave, (w) => syncFormFromProps(w))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.wave.sections.update', { section: props.wave.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.wave
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

/* ---------- TECH (бейджи) ---------- */
const techForms = reactive(
    Object.fromEntries(
        (props.teches || []).map(tech => [
            tech.id,
            useForm({
                wave_section_id: tech.wave_section_id ?? props.wave.id, // ← добавили
                title: tech.title ?? '',
                subtitle: tech.subtitle ?? '',
                description: tech.description ?? '',
                alt: tech.alt ?? '',
                sort: tech.sort ?? 0,
                activity: !!tech.activity,
                image_light: null,
                image_dark: null,
            }),
        ])
    )
)

const techPreviews = reactive(
    Object.fromEntries(
        (props.teches || []).map(tech => [
            tech.id,
            { light: null, dark: null }
        ])
    )
)

const onPickTechFile = (e, tech, key /* 'image_light' | 'image_dark' */) => {
    const file = e.target.files?.[0] ?? null
    techForms[tech.id][key] = file
    const previewKey = key === 'image_light' ? 'light' : 'dark'
    techPreviews[tech.id][previewKey] = file ? URL.createObjectURL(file) : null
}

const updateTech = (tech) => {
    techForms[tech.id]
        .transform((data) => {
            const d = { ...data, _method: 'put' }
            // если файл не выбран — не отправляем ключ
            if (!(d.image_light instanceof File)) delete d.image_light
            if (!(d.image_dark  instanceof File)) delete d.image_dark
            return d
        })
        .post(route('admin.home-page.wave.teches.update', { tech: tech.id }), {
            forceFormData: true,
            preserveScroll: true,
            onError: (errs) => console.error('WaveTech update errors:', errs),
            onSuccess: () => {
                techForms[tech.id].image_light = null
                techForms[tech.id].image_dark  = null
                techPreviews[tech.id].light = null
                techPreviews[tech.id].dark  = null
            },
        })
}

</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Wave
                ({{ props.wave.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.wave.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
               border border-blue-400 dark:border-blue-200
               shadow-lg shadow-gray-500 dark:shadow-slate-400
               bg-opacity-95 dark:bg-opacity-95"
            >

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Кнопка назад -->
                    <DefaultButton :href="route('admin.home-page.index')">
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

                    <!-- Right: Actions -->
                    <div class="grid grid-flow-col sm:auto-cols-max
                                justify-start sm:justify-end gap-2">
                        <!-- Переключатель просмотра локали (табы) -->
                        <LocaleTabs
                            v-model="viewerLocale"
                            :locales="availableLocales"
                            :busy="switching"
                            :displayMap="{ ru: 'RU', en: 'EN', kk: 'KK' }"
                            size="sm"
                            rounded="lg"
                        />
                    </div>
                </div>

                <!-- Форма секции -->
                <form @submit.prevent="updateSection"
                      class="mb-3 pt-3 px-3 w-full bg-gray-200 dark:bg-gray-800
                             border border-gray-400 rounded-md
                             shadow-md shadow-gray-400 dark:shadow-gray-900">
                    <div class="pb-3 border-b border-gray-400 flex items-center justify-center">
                        <h2 class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          <span
                              class="inline-flex items-center gap-2 text-xs px-2 py-0.5
                                   rounded border border-slate-700 dark:border-slate-300
                                   text-black dark:text-white"
                          >
                            ID: {{ props.wave.id }}
                          </span>
                            {{ t('section') }} Wave
                        </h2>
                    </div>

                    <div class="space-y-1">
                        <div class="mt-3 flex justify-between flex-col
                        lg:flex-row items-center gap-4">

                            <!-- Активность -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="sectionForm.activity" />
                                <LabelCheckbox for="activity" :text="t('activity')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <!-- Тёмная тема -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="sectionForm.is_dark" />
                                <LabelCheckbox for="is_dark" :text="t('isDark')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <!-- Сортировка секции -->
                            <div class="flex flex-row items-center gap-2">
                                <div class="h-8 flex items-center">
                                    <LabelInput for="wave-sort" :value="t('sort')"
                                                class="text-sm" />
                                </div>
                                <InputNumber id="wave-sort" type="number"
                                             v-model="sectionForm.sort" autocomplete="sort"
                                             class="w-24" />
                                <InputError class="mt-2 lg:mt-0"
                                            :message="sectionForm.errors.sort" />
                            </div>

                            <!-- Локаль записи -->
                            <div class="flex flex-row items-center gap-2">
                                <SelectLocale v-model="sectionForm.locale"
                                              :errorMessage="sectionForm.errors.locale" />
                            </div>
                        </div>
                    </div>

                    <div class="py-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">

                            <!-- Подзаголовок -->
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text"
                                           v-model="sectionForm.subtitle" autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>

                            <!-- Левый текст -->
                            <div>
                                <LabelInput for="left_text" :value="t('textLeft')" />
                                <CodeTextarea
                                    v-model="sectionForm.left_text"
                                    :placeholder="(t('text'))"
                                    :rows="14"
                                    resize="y"
                                    wrap="off"
                                    class="h-40 md:h-24"
                                />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.left_text" />
                            </div>

                        </div>

                        <div class="space-y-3">

                            <!-- Заголовок -->
                            <div>
                                <LabelInput for="title" :value="t('heading')" />
                                <InputText id="title" type="text" v-model="sectionForm.title" autocomplete="title" />
                                <InputError class="mt-2" :message="sectionForm.errors.title" />
                            </div>

                            <!-- Правый текст -->
                            <div>
                                <LabelInput for="right_text" :value="t('textRight')" />
                                <CodeTextarea
                                    v-model="sectionForm.right_text"
                                    :placeholder="(t('text'))"
                                    :rows="14"
                                    resize="y"
                                    wrap="off"
                                    class="h-40 md:h-24"
                                />
                                <InputError class="mt-2" :message="sectionForm.errors.right_text" />
                            </div>

                        </div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit" class="ms-4 mb-0" :disabled="sectionForm.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16">
                                    <path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Tech badges -->
                <div class="w-full mt-6">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path
                                class="fill-current text-slate-700 dark:text-slate-300"
                                d="M23,2H1A1,1,0,0,0,0,3V21a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V3A1,1,0,0,0,23,2ZM22,4V14.3L17.759,9.35A1,1,0,0,0,17.005,9a.879.879,0,0,0-.757.342l-6.3,7.195L6.707,13.293A.988.988,0,0,0,5.955,13a1,1,0,0,0-.723.358L2,17.238V4Z"
                            />
                            <circle class="fill-current text-slate-700 dark:text-slate-300"
                                    cx="9" cy="8" r="2" />
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('badges') || 'Tech badges' }}
                        </span>
                    </h2>

                    <div v-for="tech in props.teches" :key="tech.id">
                        <form
                            @submit.prevent="updateTech(tech)"
                            class="flex flex-col gap-3 my-4 md:flex-row md:items-start md:gap-6
                                       py-3 px-3 w-full border border-gray-400
                                       bg-slate-200/70 dark:bg-slate-900/70 rounded-md
                                       shadow-md shadow-gray-400 dark:shadow-gray-900"
                            enctype="multipart/form-data"
                        >
                            <!-- Текстовые поля -->
                            <div class="w-full md:w-1/2 space-y-3">

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox v-model="techForms[tech.id].activity" />
                                        <LabelCheckbox for="activity" :text="t('activity')" class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <LabelInput :for="`tech-sort-${tech.id}`" :value="t('sort')" class="text-sm" />
                                        <InputNumber
                                            :id="`tech-sort-${tech.id}`"
                                            type="number"
                                            v-model="techForms[tech.id].sort"
                                            class="w-20"
                                        />
                                        <InputError class="mt-2 lg:mt-0" :message="techForms[tech.id].errors.sort" />
                                    </div>
                                </div>

                                <div>
                                    <LabelInput :for="`tech-title-${tech.id}`" :value="t('title')" />
                                    <InputText :id="`tech-title-${tech.id}`" type="text" v-model="techForms[tech.id].title" />
                                    <InputError class="mt-2" :message="techForms[tech.id].errors.title" />
                                </div>

                                <div>
                                    <LabelInput :for="`tech-subtitle-${tech.id}`" :value="t('subtitle')" />
                                    <InputText :id="`tech-subtitle-${tech.id}`" type="text" v-model="techForms[tech.id].subtitle" />
                                    <InputError class="mt-2" :message="techForms[tech.id].errors.subtitle" />
                                </div>

                                <div>
                                    <LabelInput :for="`tech-desc-${tech.id}`" :value="t('description')" />
                                    <MetaDescTextarea :id="`tech-desc-${tech.id}`" v-model="techForms[tech.id].description" />
                                    <InputError class="mt-2" :message="techForms[tech.id].errors.description" />
                                </div>

                            </div>

                            <!-- Файлы + превью -->
                            <div class="w-full md:w-1/2 pt-2">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- LIGHT -->
                                    <div class="space-y-2">
                                        <div class="text-center text-xs
                                                    text-slate-900/80 dark:text-slate-100/80">
                                            Light (SVG / Image)
                                        </div>
                                        <img
                                            class="w-full aspect-video object-contain
                                                       rounded bg-white p-2
                                                       border border-gray-400 shadow-md
                                                       shadow-gray-400 dark:shadow-gray-900"
                                            :src="techPreviews[tech.id].light ||
                                                        tech.image_light_thumb_url ||
                                                        tech.image_light_webp_url ||
                                                        tech.image_light_url"
                                            alt=""
                                        />
                                        <input
                                            type="file"
                                            accept="image/svg+xml,.svg"
                                            @change="(e) => onPickTechFile(e, tech, 'image_light')"
                                            class="block w-full text-md
                                                       text-gray-700 dark:text-gray-100
                                                       file:mr-4 file:py-0.5 file:px-2
                                                       file:border-0 file:text-sm file:font-semibold
                                                       file:bg-violet-600 file:text-white
                                                       hover:file:bg-violet-700"
                                        />
                                        <div v-if="techForms[tech.id].errors.image_light"
                                             class="form-error">
                                            {{ techForms[tech.id].errors.image_light }}
                                        </div>
                                    </div>

                                    <!-- DARK -->
                                    <div class="space-y-2">
                                        <div class="text-center text-xs
                                                    text-slate-900/80 dark:text-slate-100/80">
                                            Dark (SVG / Image)
                                        </div>
                                        <img
                                            class="w-full aspect-video object-contain
                                                       rounded bg-white p-2
                                                       border border-gray-400 shadow-md
                                                       shadow-gray-400 dark:shadow-gray-900"
                                            :src="techPreviews[tech.id].dark ||
                                                        tech.image_dark_thumb_url ||
                                                        tech.image_dark_webp_url ||
                                                        tech.image_dark_url"
                                            alt=""
                                        />
                                        <input
                                            type="file"
                                            accept="image/svg+xml,.svg"
                                            @change="(e) => onPickTechFile(e, tech, 'image_dark')"
                                            class="block w-full text-md
                                                       text-gray-700 dark:text-gray-100
                                                       file:mr-4 file:py-0.5 file:px-2
                                                       file:border-0 file:text-sm file:font-semibold
                                                       file:bg-violet-600 file:text-white
                                                       hover:file:bg-violet-700"
                                        />
                                        <div v-if="techForms[tech.id].errors.image_dark" class="form-error">
                                            {{ techForms[tech.id].errors.image_dark }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <LabelInput :for="`tech-alt-${tech.id}`" value="Alt" />
                                    <InputText :id="`tech-alt-${tech.id}`" type="text" v-model="techForms[tech.id].alt" />
                                    <InputError class="mt-2" :message="techForms[tech.id].errors.alt" />
                                </div>

                                <div class="flex justify-end mt-4">
                                    <PrimaryButton
                                        class="ms-4 mb-0"
                                        @click.prevent="updateTech(tech)"
                                        :disabled="techForms[tech.id].processing"
                                        :class="{ 'opacity-25': techForms[tech.id].processing }"
                                    >
                                        <template #icon>
                                            <svg class="w-3 h-3 fill-current text-slate-100" viewBox="0 0 24 24">
                                                <path
                                                    d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"
                                                />
                                            </svg>
                                        </template>
                                        {{ t('save') }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
