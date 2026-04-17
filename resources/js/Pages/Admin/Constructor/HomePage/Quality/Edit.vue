<script setup>
/**
 * Quality Edit (Admin)
 * - Редактирование: QualitySection (+ light/dark скриншоты через Spatie)
 * - Редактирование айтемов: QualityItem[] (без медиа, только мета + анимация)
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
    quality: { type: Object, required: true }, // QualitySectionResource
    items: { type: Array, required: true } // QualityItemResource[]
})

const renderSvgOrPlaceholder = (svg) =>
    (svg && String(svg).trim().length)
        ? svg
        : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80"><rect x="1" y="1" width="118" height="78" fill="none" stroke="#cbd5e1" stroke-width="2"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, monospace" font-size="10">No SVG</text></svg>'

const iconTab = reactive(
    Object.fromEntries((props.items ?? []).map(it => [it.id, 'light']))
)

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const mapSectionToForm = (s = {}) => ({
    locale: s.locale ?? 'ru',
    title: s.title ?? '',
    subtitle: s.subtitle ?? '',
    screenshot_alt: s.screenshot_alt ?? '',
    sort: Number.isFinite(s.sort) ? s.sort : 0,
    is_dark: !!s.is_dark,
    activity: !!s.activity
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.quality?.locale ?? 'ru')
const switching = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.quality?.locale) return
    router.get(
        route('admin.home-page.quality.sections.edit', { section: props.quality.id, locale: loc }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onStart: () => (switching.value = true),
            onFinish: () => (switching.value = false)
        }
    )
})

/* ---------- СЕКЦИЯ (редактирование текущей записи) ---------- */
const sectionForm = useForm(mapSectionToForm(props.quality))

const syncFormFromProps = (s) => {
    const data = mapSectionToForm(s || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

watch(() => props.quality, (s) => syncFormFromProps(s))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.quality.sections.update', { section: props.quality.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.quality
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

/* ---------- МЕДИА (light/dark скриншоты секции) ---------- */
const mediaForm = useForm({
    light: null,
    dark: null
})

const mediaPreview = reactive({
    light: null, // object URL
    dark: null  // object URL
})

const onPickScreen = (e, key /* 'light' | 'dark' */) => {
    const f = e.target.files?.[0] ?? null
    mediaForm[key] = f
    mediaPreview[key] = f ? URL.createObjectURL(f) : null
}

const uploadScreens = () => {
    mediaForm
        .transform((data) => ({
            ...data,
            _method: 'put',
            locale: sectionForm.locale
        }))
        .post(route('admin.home-page.quality.sections.update', { section: props.quality.id }), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                mediaForm.light = null
                mediaForm.dark = null
                mediaPreview.light = null
                mediaPreview.dark = null
            }
        })
}

/* ---------- Айтемы (QualityItem) ---------- */
const revealOptions = ['bottom', 'top', 'left', 'right']

const itemForms = reactive(
    Object.fromEntries(
        (props.items ?? []).map((it) => [
            it.id,
            useForm({
                section_id: props.quality.id,
                top_title: it.top_title ?? '',
                title: it.title ?? '',
                description: it.description ?? '',
                reveal_from: it.reveal_from ?? 'bottom',
                delay: Number.isFinite(it.delay) ? it.delay : 0,
                threshold: typeof it.threshold === 'number' ? it.threshold : 0.15,
                distance: Number.isFinite(it.distance) ? it.distance : 28,
                sort: Number.isFinite(it.sort) ? it.sort : 0,
                activity: !!it.activity,
                icon_svg_light: it.icon_svg_light ?? '',
                icon_svg_dark: it.icon_svg_dark ?? '',
                icon_alt: it.icon_alt ?? '',
            }),
        ])
    )
)

const updateItem = (item) => {
    itemForms[item.id].put(
        route('admin.home-page.quality.items.update', { item: item.id }),
        { preserveScroll: true }
    )
}
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Quality
                ({{ props.quality.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.quality.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
               border border-blue-400 dark:border-blue-200
               shadow-lg shadow-gray-500 dark:shadow-slate-400
               bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Назад -->
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

                    <!-- Табы локали -->
                    <div class="grid grid-flow-col
                                sm:auto-cols-max justify-start sm:justify-end gap-2">
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
                            ID: {{ props.quality.id }}
                          </span>
                            {{ t('section') }} Quality
                        </h2>
                    </div>

                    <div class="space-y-1">
                        <div class="mt-3 flex justify-between flex-col
                                    lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="sectionForm.activity" />
                                <LabelCheckbox for="activity" :text="t('activity')"
                                               class="text-sm h-8 flex items-center" />
                            </div>
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="sectionForm.is_dark" />
                                <LabelCheckbox for="is_dark" :text="t('isDark')"
                                               class="text-sm h-8 flex items-center" />
                            </div>
                            <div class="flex flex-row items-center gap-2">
                                <div class="h-8 flex items-center">
                                    <LabelInput for="sort" :value="t('sort')"
                                                class="text-sm" />
                                </div>
                                <InputNumber id="sort" type="number" v-model="sectionForm.sort"
                                             class="w-24" />
                                <InputError class="mt-2 lg:mt-0"
                                            :message="sectionForm.errors.sort" />
                            </div>
                            <div class="flex flex-row items-center gap-2">
                                <SelectLocale v-model="sectionForm.locale"
                                              :errorMessage="sectionForm.errors.locale" />
                            </div>
                        </div>
                    </div>

                    <div class="py-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <LabelInput for="title" :value="t('heading')" />
                                <InputText id="title" type="text"
                                           v-model="sectionForm.title" autocomplete="title" />
                                <InputError class="mt-2" :message="sectionForm.errors.title" />
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text"
                                           v-model="sectionForm.subtitle" autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>
                        </div>
                    </div>

                    <!-- ALT -->
                    <div>
                        <LabelInput for="screenshot_alt" :value="t('seoAlt')" />
                        <InputText id="screenshot_alt" type="text"
                                   v-model="sectionForm.screenshot_alt" />
                        <InputError class="mt-2"
                                    :message="sectionForm.errors.screenshot_alt" />
                    </div>

                    <div class="pt-3 flex justify-end">
                        <PrimaryButton type="submit" class="ms-4 mb-0"
                                       :disabled="sectionForm.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Скриншоты секции: LIGHT / DARK -->
                <div class="w-full mt-1">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-2" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M23,2H1A1,1,0,0,0,0,3V21a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V3A1,1,0,0,0,23,2ZM22,4V14.3L17.759,9.35A1,1,0,0,0,17.005,9a.879.879,0,0,0-.757.342l-6.3,7.195L6.707,13.293A.988.988,0,0,0,5.955,13a1,1,0,0,0-.723.358L2,17.238V4Z"></path>
                            <circle class="fill-current text-slate-700 dark:text-slate-300"
                                    cx="9" cy="8" r="2"></circle>
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          Light / Dark {{ t('poster') }}
                        </span>
                    </h2>

                    <form
                        enctype="multipart/form-data"
                        @submit.prevent="uploadScreens"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-3"
                    >
                        <!-- Light -->
                        <div class="space-y-2">
                            <div class="text-center text-xs opacity-70 text-black dark:text-white">
                                Light
                            </div>
                            <img
                                class="w-full object-contain border border-gray-400
                                       shadow-md shadow-gray-400 dark:shadow-gray-900"
                                :src="mediaPreview.light  ||
                                      props.quality.light_webp_url ||
                                      props.quality.light_url ||
                                      props.quality.light_thumb_url"
                                :alt="t('view')"
                            />
                            <input
                                type="file"
                                accept="image/*"
                                @change="(e) => onPickScreen(e, 'light')"
                                class="block w-full text-md
                                       text-gray-700 dark:text-gray-100
                                       file:mr-4 file:py-0.5 file:px-2
                                       file:border-0 file:text-sm file:font-semibold
                                       file:bg-violet-600 file:text-white
                                       hover:file:bg-violet-700"
                            />
                            <div v-if="mediaForm.errors.light" class="form-error">
                                {{ mediaForm.errors.light }}
                            </div>
                        </div>

                        <!-- Dark -->
                        <div class="space-y-2">
                            <div class="text-center text-xs opacity-70 text-black dark:text-white">
                                Dark
                            </div>
                            <img
                                class="w-full object-contain border border-gray-400
                                       rounded shadow-md shadow-gray-400 dark:shadow-gray-900"
                                :src="mediaPreview.dark ||
                                      props.quality.dark_webp_url ||
                                      props.quality.dark_url ||
                                      props.quality.dark_thumb_url"
                                :alt="t('view')"
                            />
                            <input
                                type="file"
                                accept="image/*"
                                @change="(e) => onPickScreen(e, 'dark')"
                                class="block w-full text-md
                                       text-gray-700 dark:text-gray-100
                                       file:mr-4 file:py-0.5 file:px-2
                                       file:border-0 file:text-sm file:font-semibold
                                       file:bg-violet-600 file:text-white
                                       hover:file:bg-violet-700"
                            />
                            <div v-if="mediaForm.errors.dark" class="form-error">
                                {{ mediaForm.errors.dark }}
                            </div>
                        </div>

                        <div class="lg:col-span-2 flex justify-end">
                            <PrimaryButton
                                type="submit"
                                class="ms-4 mb-0"
                                :disabled="mediaForm.processing"
                                :class="{ 'opacity-25': mediaForm.processing }"
                            >
                                <template #icon>
                                    <svg class="w-3 h-3 fill-current text-slate-100"
                                         viewBox="0 0 24 24">
                                        <path
                                            d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"
                                        />
                                    </svg>
                                </template>
                                {{ t('save') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Айтемы -->
                <div class="w-full mt-1">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path
                                class="fill-current text-slate-700 dark:text-slate-300"
                                d="M3 5h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Z"
                            />
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('sections') }}
                        </span>
                    </h2>

                    <div class="divide-y divide-slate-300">
                        <form
                            v-for="it in (props.items || [])"
                            :key="it.id"
                            @submit.prevent="updateItem(it)"
                            class="mt-4 px-3 pt-2 bg-slate-200/70 dark:bg-slate-900/70
                                   rounded-md border border-gray-400
                                   shadow-md shadow-gray-400 dark:shadow-gray-900"
                        >
                            <div class="text-center text-sm font-medium
                                        text-orange-700 dark:text-orange-200 opacity-70">
                                #{{ it.sort }} — {{ it.title || '' }} [ID {{ it.id }}]
                            </div>

                            <div class="my-1 flex justify-between flex-col
                                        lg:flex-row items-center gap-4">
                                <div class="flex flex-row items-center gap-2">
                                    <ActivityCheckbox v-model="itemForms[it.id].activity" />
                                    <LabelCheckbox for="activity" :text="t('activity')"
                                                   class="text-sm h-8 flex items-center" />
                                </div>
                                <div class="flex flex-row items-center gap-2">
                                    <div class="h-8 flex items-center">
                                        <LabelInput :for="`i-sort-${it.id}`"
                                                    :value="t('sort')" class="text-sm" />
                                    </div>
                                    <InputNumber :id="`i-sort-${it.id}`" type="number"
                                                 v-model="itemForms[it.id].sort" class="w-20" />
                                    <InputError class="mt-2 lg:mt-0"
                                                :message="itemForms[it.id].errors.sort" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                <!-- ICONS (SVG) -->
                                <div class="md:col-span-2 mt-2 overflow-hidden">

                                    <!-- Заголовок + табы -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="text-sm font-semibold
                                                    text-slate-700 dark:text-slate-200">
                                            {{ t('svg') }}
                                        </div>
                                        <div class="inline-flex rounded overflow-hidden
                                                    border border-slate-300 dark:border-slate-600">
                                            <button
                                                type="button"
                                                class="px-2 py-1 text-xs font-semibold"
                                                :class="iconTab[it.id]==='light'
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-transparent text-slate-700 dark:text-slate-200'"
                                                @click.prevent="iconTab[it.id]='light'"
                                            >
                                                Light
                                            </button>
                                            <button
                                                type="button"
                                                class="px-2 py-1 text-xs font-semibold"
                                                :class="iconTab[it.id]==='dark'
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-transparent text-slate-700 dark:text-slate-200'"
                                                @click.prevent="iconTab[it.id]='dark'"
                                            >
                                                Dark
                                            </button>
                                        </div>
                                    </div>

                                    <LabelInput
                                        :for="`i-svg-${iconTab[it.id]}-${it.id}`"
                                        :value="`${t('svg')} (${iconTab[it.id] === 'light' ? 'Light' : 'Dark'})`"
                                        class="text-center"
                                    />

                                    <!-- Редактор активной вкладки svg -->
                                    <div class="grid grid-cols-1 gap-1">

                                        <!-- Light editor -->
                                        <div v-show="iconTab[it.id]==='light'"
                                             class="grid grid-cols-1
                                                    md:grid-cols-4 xl:grid-cols-6 gap-3">

                                            <!-- превью: 1/4 на md, 1/6 на xl -->
                                            <div class="md:col-span-1 xl:col-span-1 rounded
                                                        border border-gray-400
                                                        bg-white dark:bg-slate-300">
                                                <div
                                                    class="flex items-center justify-center
                                                           overflow-hidden px-2 py-1
                                                           text-slate-800 svg-fit"
                                                    v-html="renderSvgOrPlaceholder(itemForms[it.id].icon_svg_light)"
                                                />
                                            </div>

                                            <!-- редактор: 3/4 на md, 5/6 на xl -->
                                            <div class="md:col-span-3 xl:col-span-5">
                                                <CodeTextarea
                                                    :id="`i-svg-light-${it.id}`"
                                                    v-model="itemForms[it.id].icon_svg_light"
                                                    :rows="6"
                                                    wrap="off"
                                                    resize="y"
                                                    class="font-mono"
                                                    placeholder="<svg ...>...</svg>"
                                                />
                                                <InputError class="mt-1"
                                                            :message="itemForms[it.id].errors.icon_svg_light" />
                                            </div>

                                        </div>

                                        <!-- Dark editor -->
                                        <div v-show="iconTab[it.id]==='dark'"
                                             class="grid grid-cols-1 md:grid-cols-4
                                                    xl:grid-cols-6 gap-3">

                                            <!-- превью: 1/4 на md, 1/6 на xl -->
                                            <div class="md:col-span-1 xl:col-span-1 rounded
                                                        border border-gray-400
                                                        bg-slate-900">
                                                <div
                                                    class="flex items-center justify-center
                                                           overflow-hidden px-2 py-1
                                                           text-slate-200 svg-fit"
                                                    v-html="renderSvgOrPlaceholder(itemForms[it.id].icon_svg_dark)"
                                                />
                                            </div>

                                            <!-- редактор: 3/4 на md, 5/6 на xl -->
                                            <div class="md:col-span-3 xl:col-span-5">
                                                <CodeTextarea
                                                    :id="`i-svg-dark-${it.id}`"
                                                    v-model="itemForms[it.id].icon_svg_dark"
                                                    :rows="6"
                                                    wrap="off"
                                                    resize="y"
                                                    class="font-mono"
                                                    placeholder="<svg ...>...</svg>"
                                                />
                                                <InputError class="mt-1"
                                                            :message="itemForms[it.id].errors.icon_svg_dark" />
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div>
                                    <LabelInput :for="`i-top-${it.id}`" :value="t('subtitle')" />
                                    <InputText :id="`i-top-${it.id}`"
                                               v-model="itemForms[it.id].top_title" />
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.top_title" />
                                </div>
                                <div>
                                    <LabelInput :for="`i-title-${it.id}`" :value="t('heading')" />
                                    <InputText :id="`i-title-${it.id}`"
                                               v-model="itemForms[it.id].title" />
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.title" />
                                </div>
                                <div class="md:col-span-2">
                                    <LabelInput :for="`i-desc-${it.id}`" :value="t('description')" />
                                    <MetaDescTextarea :id="`i-desc-${it.id}`"
                                                      v-model="itemForms[it.id].description" />
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.description" />
                                </div>
                                <div>
                                    <LabelInput :for="`i-reveal-${it.id}`" value="Reveal from" />
                                    <select
                                        :id="`i-reveal-${it.id}`"
                                        v-model="itemForms[it.id].reveal_from"
                                        class="block w-full py-0.5 border-slate-500
                                               font-semibold text-sm
                                               focus:border-indigo-500 focus:ring-indigo-300
                                               rounded-sm shadow-sm
                                               dark:bg-cyan-800 dark:text-slate-100"
                                    >
                                        <option v-for="r in revealOptions" :key="r" :value="r">
                                            {{ r }}
                                        </option>
                                    </select>
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.reveal_from" />
                                </div>
                                <div>
                                    <LabelInput :for="`i-delay-${it.id}`" value="Delay (ms)" />
                                    <InputNumber :id="`i-delay-${it.id}`" type="number"
                                                 v-model="itemForms[it.id].delay"
                                                 class="w-full" />
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.delay" />
                                </div>
                                <div>
                                    <LabelInput :for="`i-threshold-${it.id}`"
                                                value="Threshold (0..1)" />
                                    <input
                                        :id="`i-threshold-${it.id}`"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="1"
                                        v-model.number="itemForms[it.id].threshold"
                                        class="block w-full px-2 py-0.5 text-sm
                                               rounded-sm border border-slate-500
                                               focus:border-indigo-500 focus:ring-indigo-300
                                               dark:bg-cyan-800 dark:text-slate-100"
                                    />
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.threshold" />
                                </div>
                                <div>
                                    <LabelInput :for="`i-distance-${it.id}`" value="Distance (px)" />
                                    <InputNumber :id="`i-distance-${it.id}`" type="number"
                                                 v-model="itemForms[it.id].distance" class="w-full" />
                                    <InputError class="mt-1" :message="itemForms[it.id].errors.distance" />
                                </div>
                            </div>

                            <div class="flex justify-end mt-3">
                                <PrimaryButton
                                    type="submit"
                                    class="ms-4 mb-0"
                                    :disabled="itemForms[it.id].processing"
                                    :class="{ 'opacity-25': itemForms[it.id].processing }"
                                >
                                    <template #icon>
                                        <svg class="w-3 h-3 fill-current text-slate-100"
                                             viewBox="0 0 24 24">
                                            <path
                                                d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"
                                            />
                                        </svg>
                                    </template>
                                    {{ t('save') }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
