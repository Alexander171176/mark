<script setup>
/**
 * Hero Edit (Admin)
 * - Редактирование: HeroSection + HeroIcon[] + HeroScreenshot[]
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
import CodeTextarea from '@/Components/Admin/Textarea/CodeTextarea.vue'
import LocaleTabs from '@/Components/Admin/Tab/LocaleTabs.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    hero: { type: Object, required: true },          // HeroSectionResource
    icons: { type: Array, required: true },          // HeroIconResource[]
    screenshots: { type: Array, required: true },    // HeroScreenshotResource[]
    targets: { type: Array, default: () => ['_self', '_blank'] }
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const normalizeTarget = (v) => (v === '_blank' || v === '_self') ? v : '_self'
const mapHeroToForm = (h = {}) => ({
    locale: h.locale ?? 'ru',
    title: h.title ?? '',
    subtitle: h.subtitle ?? '',
    badge_text: h.badge_text ?? '',
    description: h.description ?? '',
    primary_btn_text: h.primary_btn_text ?? '',
    primary_btn_url: h.primary_btn_url ?? '',
    primary_btn_target: normalizeTarget(h.primary_btn_target ?? '_self'),
    secondary_btn_text: h.secondary_btn_text ?? '',
    secondary_btn_url: h.secondary_btn_url ?? '',
    secondary_btn_target: normalizeTarget(h.secondary_btn_target ?? '_self'),
    is_dark: !!h.is_dark,
    activity: !!h.activity
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.hero?.locale ?? 'ru')
const switching = ref(false)

// При смене локали — идём на этот же экран с query ?locale=..., без сохранения state
watch(viewerLocale, (loc) => {
    if (!loc || loc === props.hero?.locale) return
    router.get(
        route('admin.home-page.hero.sections.edit', { section: props.hero.id, locale: loc }),
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
const sectionForm = useForm(mapHeroToForm(props.hero))

// Универсальная синхронизация формы из props.hero
const syncFormFromProps = (h) => {
    const data = mapHeroToForm(h || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

// Если props.hero поменялся (после навигации/редиректа) — обновляем форму
watch(() => props.hero, (h) => syncFormFromProps(h))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.hero.sections.update', { section: props.hero.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                // Если сервер вернул обновлённого hero — синхронизируем форму
                const fresh = page.props?.value?.hero
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

/* ---------- ИКОНКИ ---------- */
const iconForms = reactive(
    Object.fromEntries(
        props.icons.map(icon => [
            icon.id,
            useForm({
                label: icon.label ?? '',
                svg: icon.svg ?? '',
                sort: icon.sort ?? 0,
                activity: !!icon.activity
            })
        ])
    )
)

const updateIcon = (icon) => {
    iconForms[icon.id].put(
        route('admin.home-page.hero.icons.update', { icon: icon.id }),
        { preserveScroll: true }
    )
}

/* ---------- СКРИНШОТЫ ---------- */
const shotForms = reactive(
    Object.fromEntries(
        props.screenshots.map(s => [
            s.id,
            useForm({
                alt: s.alt ?? '',
                sort: s.sort ?? 0,
                activity: !!s.activity,
                light: null,
                dark: null
            })
        ])
    )
)

const shotPreviews = reactive(
    Object.fromEntries(props.screenshots.map(s => [s.id, { light: null, dark: null }]))
)

const onPickFile = (e, shot, key /* 'light' | 'dark' */) => {
    const file = e.target.files?.[0] ?? null
    shotForms[shot.id][key] = file
    shotPreviews[shot.id][key] = file ? URL.createObjectURL(file) : null
}

const updateScreenshot = (shot) => {
    shotForms[shot.id]
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(
            route('admin.home-page.hero.screenshots.update', { screenshot: shot.id }),
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    shotForms[shot.id].light = null
                    shotForms[shot.id].dark = null
                    shotPreviews[shot.id].light = null
                    shotPreviews[shot.id].dark = null
                }
            }
        )
}
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Hero
                ({{ props.hero.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.hero.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        shadow-lg shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Кнопка назад -->
                    <DefaultButton :href="route('admin.home-page.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path>
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

                    <div class="pb-3 border-b border-gray-400
                                flex items-center justify-center">
                        <h2 class="font-semibold text-md text-slate-700 dark:text-slate-300">
                            <span class="inline-flex items-center gap-2 text-xs px-2 py-0.5
                                     rounded border border-slate-700 dark:border-slate-300
                                     text-black dark:text-white">
                                    ID: {{ props.hero.id }}
                            </span>
                            {{ t('section') }} Hero
                        </h2>
                    </div>

                    <div class="space-y-1">

                        <div class="mt-3 flex justify-between flex-col lg:flex-row
                                    items-center gap-4">

                            <!-- Вариант для тёмной темы -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="sectionForm.activity" />
                                <LabelCheckbox for="activity" :text="t('activity')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <!-- Вариант для тёмной темы -->
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="sectionForm.is_dark" />
                                <LabelCheckbox for="is_dark" :text="t('isDark')"
                                               class="text-sm h-8 flex items-center" />
                            </div>

                            <div class="flex flex-row items-center gap-2">
                                <SelectLocale v-model="sectionForm.locale"
                                              :errorMessage="sectionForm.errors.locale" />
                            </div>

                        </div>
                    </div>

                    <div class="py-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="space-y-3">
                            <!-- Заголовок -->
                            <div>
                                <LabelInput for="title" :value="t('heading')" />
                                <InputText id="title" type="text" v-model="sectionForm.title"
                                           autocomplete="title" />
                                <InputError class="mt-2" :message="sectionForm.errors.title" />
                            </div>
                            <!-- Подзаголовок -->
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="title" type="text" v-model="sectionForm.subtitle"
                                           autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>
                            <!-- Бейдж текст -->
                            <div>
                                <LabelInput for="badge_text" :value="t('badge')" />
                                <InputText id="badge_text" type="text"
                                           v-model="sectionForm.badge_text"
                                           autocomplete="badge_text" />
                                <InputError class="mt-2" :message="sectionForm.errors.badge_text" />
                            </div>
                            <!-- Описание -->
                            <div>
                                <LabelInput for="description" :value="t('description')" />
                                <MetaDescTextarea v-model="sectionForm.description" class="w-full" />
                                <InputError class="mt-2" :message="sectionForm.errors.description" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <!-- Кнопка 1 -->
                            <div class="space-y-1">
                                <LabelInput for="description" :value="`${t('button')} 1`" />
                                <select v-model="sectionForm.primary_btn_target"
                                        class="block w-1/2 py-0.5 border-slate-500
                                               font-semibold text-sm
                                               focus:border-indigo-500 focus:ring-indigo-300
                                               rounded-sm shadow-sm
                                               dark:bg-cyan-800 dark:text-slate-100">
                                    <option v-for="tgt in targets" :key="tgt" :value="tgt">
                                        {{ tgt }}
                                    </option>
                                </select>
                                <InputText id="primary_btn_text" type="text"
                                           v-model="sectionForm.primary_btn_text"
                                           :placeholder="t('text')"
                                           autocomplete="primary_btn_text" />
                                <InputText id="primary_btn_url" type="text"
                                           v-model="sectionForm.primary_btn_url"
                                           :placeholder="t('link')"
                                           autocomplete="primary_btn_url" />
                            </div>
                            <!-- Кнопка 2 -->
                            <div class="space-y-1 mt-1">
                                <LabelInput for="description" :value="`${t('button')} 2`" />
                                <select v-model="sectionForm.secondary_btn_target"
                                        class="block w-1/2 py-0.5 border-slate-500
                                               font-semibold text-sm
                                               focus:border-indigo-500 focus:ring-indigo-300
                                               rounded-sm shadow-sm
                                               dark:bg-cyan-800 dark:text-slate-100">
                                    <option v-for="tgt in targets" :key="tgt" :value="tgt">
                                        {{ tgt }}
                                    </option>
                                </select>
                                <InputText id="secondary_btn_text" type="text"
                                           v-model="sectionForm.secondary_btn_text"
                                           :placeholder="t('text')"
                                           autocomplete="secondary_btn_text" />
                                <InputText id="secondary_btn_url" type="text"
                                           v-model="sectionForm.secondary_btn_url"
                                           :placeholder="t('link')"
                                           autocomplete="secondary_btn_url" />
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit"
                                       class="ms-4 mb-0"
                                       :disabled="sectionForm.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"></path>
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>

                </form>

                <!-- Иконки стека -->
                <div class="w-full mt-6">

                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M23,2H1A1,1,0,0,0,0,3V21a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V3A1,1,0,0,0,23,2ZM22,4V14.3L17.759,9.35A1,1,0,0,0,17.005,9a.879.879,0,0,0-.757.342l-6.3,7.195L6.707,13.293A.988.988,0,0,0,5.955,13a1,1,0,0,0-.723.358L2,17.238V4Z"></path>
                            <circle class="fill-current text-slate-700 dark:text-slate-300"
                                    cx="9" cy="8" r="2"></circle>
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                                {{ t('stackIcons') }}
                        </span>
                    </h2>

                    <div
                        v-for="icon in props.icons"
                        :key="icon.id">

                        <form @submit.prevent="updateIcon(icon)"
                              class="flex flex-col gap-3 my-4 md:flex-row md:items-start md:gap-6
                                       pt-3 px-3 w-full border border-gray-400
                                       bg-slate-200/70 dark:bg-slate-900/70 rounded-md
                                       shadow-md shadow-gray-400 dark:shadow-gray-900">

                            <div class="w-full md:w-1/3">

                                <div class="mt-1">
                                    <LabelInput :for="`label-${icon.id}`" :value="t('label')" />
                                    <InputText :id="`label-${icon.id}`" type="text"
                                               v-model="iconForms[icon.id].label"
                                               autocomplete="label" />
                                    <InputError class="mt-2"
                                                :message="iconForms[icon.id].errors.label" />
                                </div>

                                <div class="my-1 flex justify-between flex-col
                                            lg:flex-row items-center gap-4">
                                    <div class="flex flex-row items-center gap-2">
                                        <ActivityCheckbox v-model="iconForms[icon.id].activity" />
                                        <LabelCheckbox for="activity" :text="t('activity')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex flex-row items-center gap-2">
                                        <div class="h-8 flex items-center">
                                            <LabelInput :for="`sort-${icon.id}`"
                                                        :value="t('sort')" class="text-sm" />
                                        </div>
                                        <InputNumber :id="`sort-${icon.id}`" type="number"
                                                     v-model="iconForms[icon.id].sort"
                                                     autocomplete="sort" class="w-full lg:w-20" />
                                        <InputError class="mt-2 lg:mt-0"
                                                    :message="iconForms[icon.id].errors.sort" />
                                    </div>
                                </div>
                            </div>

                            <div class="w-full md:flex-1">
                                <div class="mt-1">
                                    <LabelInput :for="`svg-${icon.id}`" :value="t('svg')" />
                                    <CodeTextarea
                                        :id="`svg-${icon.id}`"
                                        v-model="iconForms[icon.id].svg"
                                        placeholder="<svg ...>...</svg>"
                                        :rows="14"
                                        resize="y"
                                        wrap="off"
                                        class="h-40 md:h-24"
                                    />
                                    <InputError class="mt-2"
                                                :message="iconForms[icon.id].errors.svg" />
                                </div>
                                <div class="flex justify-end mt-3">
                                    <PrimaryButton class="ms-4 mb-0"
                                                   @click.prevent="updateIcon(icon)"
                                                   :disabled="iconForms[icon.id].processing"
                                                   :class="{ 'opacity-25': iconForms[icon.id].processing }">
                                        <template #icon>
                                            <svg class="w-3 h-3 fill-current text-slate-100"
                                                 viewBox="0 0 24 24">
                                                <path
                                                    d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
                                            </svg>
                                        </template>
                                        {{ t('save') }}
                                    </PrimaryButton>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Скриншоты -->
                <div class="w-full">

                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M23,2H1A1,1,0,0,0,0,3V21a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V3A1,1,0,0,0,23,2ZM22,4V14.3L17.759,9.35A1,1,0,0,0,17.005,9a.879.879,0,0,0-.757.342l-6.3,7.195L6.707,13.293A.988.988,0,0,0,5.955,13a1,1,0,0,0-.723.358L2,17.238V4Z"></path>
                            <circle class="fill-current text-slate-700 dark:text-slate-300"
                                    cx="9" cy="8" r="2"></circle>
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                                {{ t('screenshots') }}
                        </span>
                    </h2>

                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <form v-for="shot in props.screenshots"
                              :key="shot.id"
                              enctype="multipart/form-data"
                              @submit.prevent="updateScreenshot(shot)"
                              class="rounded overflow-hidden
                                     bg-gray-100/70 dark:bg-gray-800/70
                                     border border-gray-400 dark:border-gray-300
                                     shadow-md shadow-gray-400 dark:shadow-gray-900">

                            <div class="px-3 pt-3 space-y-3">

                                <div class="font-semibold text-center text-sm opacity-75
                                            text-yellow-700 dark:text-yellow-200">
                                    #{{ shot.sort }} — {{ shot.alt || '' }}
                                </div>

                                <div class="my-1 flex justify-between flex-col
                                            lg:flex-row items-center gap-4">
                                    <div class="flex flex-row items-center gap-2">
                                        <ActivityCheckbox v-model="shotForms[shot.id].activity" />
                                        <LabelCheckbox for="activity" :text="t('activity')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex flex-row items-center gap-2">
                                        <div class="h-8 flex items-center">
                                            <LabelInput for="sort" :value="t('sort')"
                                                        class="text-sm" />
                                        </div>
                                        <InputNumber id="sort" type="number"
                                                     v-model="shotForms[shot.id].sort"
                                                     autocomplete="sort" class="w-full lg:w-20" />
                                        <InputError class="mt-2 lg:mt-0"
                                                    :message="shotForms[shot.id].errors.sort" />
                                    </div>
                                </div>

                                <!-- Превью LIGHT/DARK -->
                                <div class="grid grid-cols-2 gap-2">

                                    <div class="space-y-2">
                                        <div class="text-xs opacity-70 text-center
                                                    text-black dark:text-white">
                                            Light
                                        </div>
                                        <img
                                            class="w-full aspect-video object-cover rounded
                                                   border border-gray-400 shadow-md
                                                   shadow-gray-400 dark:shadow-gray-900"
                                            :src="shotPreviews[shot.id].light ||
                                                    shot.light_thumb_url ||
                                                    shot.light_webp_url ||
                                                    shot.light_url"
                                            :alt="t('view')"
                                        />
                                        <div class="mt-4">
                                            <input
                                                type="file"
                                                accept="image/*"
                                                @change="(e) => onPickFile(e, shot, 'light')"
                                                class="block w-full text-md
                                                       text-gray-700 dark:text-gray-100
                                                       file:mr-4 file:py-0.5 file:px-2
                                                       file:border-0 file:text-sm file:font-semibold
                                                       file:bg-violet-600 file:text-white
                                                       hover:file:bg-violet-700"
                                            />
                                        </div>
                                        <div v-if="shotForms[shot.id].errors.light"
                                             class="form-error">
                                            {{ shotForms[shot.id].errors.light }}
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="text-xs opacity-70 text-center
                                                    text-black dark:text-white">
                                            Dark
                                        </div>
                                        <img
                                            class="w-full aspect-video object-cover rounded
                                                   border border-gray-400 shadow-md
                                                   shadow-gray-400 dark:shadow-gray-900"
                                            :src="shotPreviews[shot.id].dark ||
                                                    shot.dark_thumb_url ||
                                                    shot.dark_webp_url ||
                                                    shot.dark_url"
                                            alt=""
                                        />
                                        <div class="mt-4">
                                            <input
                                                type="file"
                                                accept="image/*"
                                                @change="(e) => onPickFile(e, shot, 'dark')"
                                                class="block w-full text-md
                                                       text-gray-700 dark:text-gray-100
                                                       file:mr-4 file:py-0.5 file:px-2
                                                       file:border-0 file:text-sm file:font-semibold
                                                       file:bg-violet-600 file:text-white
                                                       hover:file:bg-violet-700"
                                            />
                                        </div>
                                        <div v-if="shotForms[shot.id].errors.dark"
                                             class="form-error">
                                            {{ shotForms[shot.id].errors.dark }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center gap-2">
                                    <LabelInput :for="`alt-${shot.id}`" value="Alt" />
                                    <InputText :id="`alt-${shot.id}`" type="text"
                                               v-model="shotForms[shot.id].alt"
                                               autocomplete="alt" />
                                    <InputError class="mt-2"
                                                :message="shotForms[shot.id].errors.alt" />
                                </div>

                                <div class="flex justify-center">
                                    <PrimaryButton type="submit"
                                                   class="ms-4 mb-0"
                                                   :disabled="shotForms[shot.id].processing"
                                                   :class="{ 'opacity-25': shotForms[shot.id].processing }">
                                        <template #icon>
                                            <svg class="w-3 h-3 fill-current text-slate-100"
                                                 viewBox="0 0 24 24">
                                                <path
                                                    d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
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
