<script setup>
/**
 * Component Edit (Admin)
 * - Редактирование: ComponentSection + ComponentFeature[] + ComponentTab[] + ComponentTile[]
 *   (у плиток — загрузка light/dark, как у HeroScreenshot)
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
    component: { type: Object, required: true },  // ComponentSectionResource
    features: { type: Array, required: true },  // ComponentFeatureResource[]
    tabs: { type: Array, required: true }  // ComponentTabResource[] (каждый tab может содержать tiles[])
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk']);

/* ---------- helpers ---------- */
const mapSectionToForm = (s = {}) => ({
    locale: s.locale ?? 'ru',
    title: s.title ?? '',
    subtitle: s.subtitle ?? '',
    cta_text: s.cta_text ?? 'All Components',
    cta_url: s.cta_url ?? '/blocks',
    sort: Number.isFinite(s.sort) ? s.sort : 0,
    activity: !!s.activity
})

const renderSvgOrPlaceholder = (svg) =>
    (svg && String(svg).trim().length)
        ? svg
        : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80"><rect x="1" y="1" width="118" height="78" fill="none" stroke="#cbd5e1" stroke-width="2"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, monospace" font-size="10">No SVG</text></svg>'

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.component?.locale ?? 'ru')
const switching = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.component?.locale) return
    router.get(
        route('admin.home-page.component.sections.edit', {
            section: props.component.id,
            locale: loc
        }),
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
const sectionForm = useForm(mapSectionToForm(props.component))

const syncFormFromProps = (s) => {
    const data = mapSectionToForm(s || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

watch(() => props.component, (s) => syncFormFromProps(s))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.component.sections.update', { section: props.component.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.component
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

/* ---------- ФИЧИ (3 инфо-блока) ---------- */
const featureForms = reactive(
    Object.fromEntries(
        (props.features ?? []).map((f) => [
            f.id,
            useForm({
                section_id: props.component.id,
                box_class: f.box_class ?? 'is-primary',
                title: f.title ?? '',
                text: f.text ?? '',
                icon_svg_light: f.icon_svg_light ?? '',
                icon_svg_dark: f.icon_svg_dark ?? '',
                icon_alt: f.icon_alt ?? '',
                sort: Number.isFinite(f.sort) ? f.sort : 0,
                activity: !!f.activity
            })
        ])
    )
)

// Табы-переключатели предпросмотра svg для каждой фичи
const featureIconTab = reactive(
    Object.fromEntries((props.features ?? []).map((f) => [f.id, 'light']))
)

const updateFeature = (feature) => {
    featureForms[feature.id].put(
        route('admin.home-page.component.features.update', { feature: feature.id }),
        { preserveScroll: true }
    )
}

/* ---------- ВКЛАДКИ ---------- */
const tabForms = reactive(
    Object.fromEntries(
        (props.tabs ?? []).map((t) => [
            t.id,
            useForm({
                section_id: t.section_id ?? props.component.id,
                slug: t.slug ?? '',
                label: t.label ?? '',
                sort: Number.isFinite(t.sort) ? t.sort : 0,
                activity: !!t.activity
            })
        ])
    )
)

const updateTab = (tab) => {
    tabForms[tab.id].put(
        route('admin.home-page.component.tabs.update', { tab: tab.id }),
        { preserveScroll: true }
    )
}

/* ---------- ТАЙЛЫ (внутри вкладок) ---------- */
const tileForms = reactive({})
const tilePreviews = reactive({}) // { [tileId]: { light: objectURL|null, dark: objectURL|null } }

;(props.tabs ?? []).forEach((tab) => {
    ;(tab.tiles ?? []).forEach((tile) => {
        tileForms[tile.id] = useForm({
            tab_id: tile.tab_id,
            href: tile.href ?? '',
            title: tile.title ?? '',
            light_alt: tile.light_alt ?? '',
            dark_alt: tile.dark_alt ?? '',
            sort: Number.isFinite(tile.sort) ? tile.sort : 0,
            activity: !!tile.activity,
            light: null,
            dark: null
        })
        tilePreviews[tile.id] = { light: null, dark: null }
    })
})

const onPickTileFile = (e, tile, key /* 'light' | 'dark' */) => {
    const f = e.target.files?.[0] ?? null
    tileForms[tile.id][key] = f
    tilePreviews[tile.id][key] = f ? URL.createObjectURL(f) : null
}

const updateTile = (tile) => {
    tileForms[tile.id]
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(
            route('admin.home-page.component.tiles.update', { tile: tile.id }),
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    tileForms[tile.id].light = null
                    tileForms[tile.id].dark = null
                    tilePreviews[tile.id].light = null
                    tilePreviews[tile.id].dark = null
                }
            }
        )
}
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Component
                ({{ props.component.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.component.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
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
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <!-- Переключатель локалей -->
                    <div class="grid grid-flow-col sm:auto-cols-max
                                justify-start sm:justify-end gap-2">
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
                <form
                    @submit.prevent="updateSection"
                    class="mb-3 pt-3 px-3 w-full bg-gray-200 dark:bg-gray-800
                           border border-gray-400 rounded-md
                           shadow-md shadow-gray-400 dark:shadow-gray-900"
                >
                    <div class="pb-3 border-b border-gray-400 flex items-center justify-center">
                        <h2 class="font-semibold text-md text-slate-700 dark:text-slate-300">
              <span
                  class="inline-flex items-center gap-2 text-xs px-2 py-0.5
                         rounded border border-slate-700 dark:border-slate-300
                         text-black dark:text-white"
              >
                ID: {{ props.component.id }}
              </span>
                            {{ t('section') }} Component
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
                                <div class="h-8 flex items-center">
                                    <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                                </div>
                                <InputNumber id="sort" type="number"
                                             v-model="sectionForm.sort" class="w-24" />
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

                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text" v-model="sectionForm.subtitle"
                                           autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <LabelInput for="cta_text" value="CTA Text" />
                                <InputText id="cta_text" type="text" v-model="sectionForm.cta_text"
                                           autocomplete="cta_text" />
                                <InputError class="mt-2" :message="sectionForm.errors.cta_text" />
                            </div>

                            <div>
                                <LabelInput for="cta_url" value="CTA URL" />
                                <InputText id="cta_url" type="text" v-model="sectionForm.cta_url"
                                           autocomplete="cta_url" />
                                <InputError class="mt-2" :message="sectionForm.errors.cta_url" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit" class="ms-4 mb-0"
                                       :disabled="sectionForm.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Три инфо-блока (Features) -->
                <div class="w-full mt-1">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M3 5h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Z" />
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('sections') }} (Features)
                        </span>
                    </h2>

                    <form
                        v-for="f in (props.features || [])"
                        :key="f.id"
                        @submit.prevent="updateFeature(f)"
                        class="mt-4 px-3 pt-2 bg-slate-200/70 dark:bg-slate-900/70
                               rounded-md border border-gray-400
                               shadow-md shadow-gray-400 dark:shadow-gray-900"
                    >
                        <div class="text-center text-sm font-medium
                                    text-orange-700 dark:text-orange-200 opacity-70">
                            #{{ featureForms[f.id].sort }} —
                            {{ featureForms[f.id].title || '' }} [ID {{ f.id }}]
                        </div>

                        <div class="my-1 flex justify-between flex-col
                                    lg:flex-row items-center gap-4">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox v-model="featureForms[f.id].activity" />
                                <LabelCheckbox for="activity" :text="t('activity')"
                                               class="text-sm h-8 flex items-center" />
                            </div>
                            <div class="flex flex-row items-center gap-2">
                                <div class="h-8 flex items-center">
                                    <LabelInput :for="`f-sort-${f.id}`"
                                                :value="t('sort')" class="text-sm" />
                                </div>
                                <InputNumber :id="`f-sort-${f.id}`" type="number"
                                             v-model="featureForms[f.id].sort"
                                             class="w-20" />
                                <InputError class="mt-2 lg:mt-0"
                                            :message="featureForms[f.id].errors.sort" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="space-y-3">
                                <div>
                                    <LabelInput :for="`f-title-${f.id}`" :value="t('heading')" />
                                    <InputText :id="`f-title-${f.id}`"
                                               v-model="featureForms[f.id].title" />
                                    <InputError class="mt-1"
                                                :message="featureForms[f.id].errors.title" />
                                </div>
                                <div>
                                    <LabelInput :for="`f-text-${f.id}`" :value="t('description')" />
                                    <MetaDescTextarea :id="`f-text-${f.id}`"
                                                      v-model="featureForms[f.id].text" />
                                    <InputError class="mt-1"
                                                :message="featureForms[f.id].errors.text" />
                                </div>
                                <div>
                                    <LabelInput :for="`f-box-${f.id}`" value="Box class" />
                                    <InputText :id="`f-box-${f.id}`"
                                               v-model="featureForms[f.id].box_class"
                                               placeholder="is-primary / is-green / is-info ..." />
                                    <InputError class="mt-1"
                                                :message="featureForms[f.id].errors.box_class" />
                                </div>
                            </div>

                            <!-- SVG editors with tabs -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        SVG (Light / Dark)
                                    </div>
                                    <div
                                        class="inline-flex rounded overflow-hidden border border-slate-300 dark:border-slate-600">
                                        <button
                                            type="button"
                                            class="px-2 py-1 text-xs font-semibold"
                                            :class="featureIconTab[f.id]==='light' ? 'bg-indigo-600 text-white' : 'bg-transparent text-slate-700 dark:text-slate-200'"
                                            @click.prevent="featureIconTab[f.id]='light'">
                                            Light
                                        </button>
                                        <button
                                            type="button"
                                            class="px-2 py-1 text-xs font-semibold"
                                            :class="featureIconTab[f.id]==='dark' ? 'bg-indigo-600 text-white' : 'bg-transparent text-slate-700 dark:text-slate-200'"
                                            @click.prevent="featureIconTab[f.id]='dark'">
                                            Dark
                                        </button>
                                    </div>
                                </div>

                                <!-- Light -->
                                <div v-show="featureIconTab[f.id]==='light'"
                                     class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    <div
                                        class="md:col-span-2 rounded h-fit
                                               border border-gray-400 bg-white dark:bg-slate-300">
                                        <div
                                            class="flex items-center justify-center
                                                   overflow-hidden px-2 py-1 text-slate-800 svg-fit"
                                            v-html="renderSvgOrPlaceholder(featureForms[f.id].icon_svg_light)" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <CodeTextarea
                                            :id="`f-svg-light-${f.id}`"
                                            v-model="featureForms[f.id].icon_svg_light"
                                            :rows="7" wrap="off" resize="y" class="font-mono"
                                            placeholder="<svg ...>...</svg>"
                                        />
                                        <InputError class="mt-1"
                                                    :message="featureForms[f.id].errors.icon_svg_light" />
                                    </div>
                                </div>

                                <!-- Dark -->
                                <div v-show="featureIconTab[f.id]==='dark'"
                                     class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    <div class="md:col-span-2 rounded border border-gray-400 bg-slate-900">
                                        <div
                                            class="flex items-center justify-center overflow-hidden px-2 py-1 text-slate-200 svg-fit"
                                            v-html="renderSvgOrPlaceholder(featureForms[f.id].icon_svg_dark)" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <CodeTextarea
                                            :id="`f-svg-dark-${f.id}`"
                                            v-model="featureForms[f.id].icon_svg_dark"
                                            :rows="7" wrap="off" resize="y" class="font-mono"
                                            placeholder="<svg ...>...</svg>"
                                        />
                                        <InputError class="mt-1" :message="featureForms[f.id].errors.icon_svg_dark" />
                                    </div>
                                </div>

                                <div>
                                    <LabelInput :for="`f-alt-${f.id}`" :value="t('seoAlt')" />
                                    <InputText :id="`f-alt-${f.id}`"
                                               v-model="featureForms[f.id].icon_alt" />
                                    <InputError class="mt-1"
                                                :message="featureForms[f.id].errors.icon_alt" />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <PrimaryButton
                                type="submit"
                                class="ms-4 mb-0"
                                :disabled="featureForms[f.id].processing"
                                :class="{ 'opacity-25': featureForms[f.id].processing }"
                            >
                                <template #icon>
                                    <svg class="w-3 h-3 fill-current text-slate-100"
                                         viewBox="0 0 24 24">
                                        <path
                                            d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z" />
                                    </svg>
                                </template>
                                {{ t('save') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Вкладки и плитки -->
                <div class="w-full mt-6">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M23,2H1A1,1,0,0,0,0,3V21a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V3A1,1,0,0,0,23,2ZM22,4V14.3L17.759,9.35A1,1,0,0,0,17.005,9a.879.879,0,0,0-.757.342l-6.3,7.195L6.707,13.293A.988.988,0,0,0,5.955,13a1,1,0,0,0-.723.358L2,17.238V4Z" />
                            <circle class="fill-current text-slate-700 dark:text-slate-300"
                                    cx="9" cy="8"
                                    r="2"></circle>
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('tabsTiles') }}
                        </span>
                    </h2>

                    <div v-for="tab in (props.tabs || [])" :key="tab.id" class="mt-4">
                        <!-- Форма таба -->
                        <form
                            @submit.prevent="updateTab(tab)"
                            class="px-3 py-2 bg-gray-100/70 dark:bg-gray-800/70
                                   rounded-md border border-gray-400
                                   shadow-md shadow-gray-400 dark:shadow-gray-900"
                        >
                            <div class="flex flex-col md:flex-row
                                        md:items-center md:justify-between gap-3">
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <LabelInput :for="`t-slug-${tab.id}`"
                                                    :value="t('url')" />
                                        <InputText :id="`t-slug-${tab.id}`"
                                                   v-model="tabForms[tab.id].slug" />
                                        <InputError class="mt-1"
                                                    :message="tabForms[tab.id].errors.slug" />
                                    </div>
                                    <div>
                                        <LabelInput :for="`t-label-${tab.id}`"
                                                    :value="t('label')" />
                                        <InputText :id="`t-label-${tab.id}`"
                                                   v-model="tabForms[tab.id].label" />
                                        <InputError class="mt-1"
                                                    :message="tabForms[tab.id].errors.label" />
                                    </div>
                                    <div class="flex items-end gap-3">
                                        <div class="flex flex-row items-center gap-2">
                                            <ActivityCheckbox v-model="tabForms[tab.id].activity" />
                                            <LabelCheckbox for="activity" :text="t('activity')"
                                                           class="text-sm h-8 flex items-center" />
                                        </div>
                                        <div class="flex flex-row items-center gap-2">
                                            <div class="h-8 flex items-center">
                                                <LabelInput :for="`t-sort-${tab.id}`"
                                                            :value="t('sort')"
                                                            class="text-sm" />
                                            </div>
                                            <InputNumber :id="`t-sort-${tab.id}`" type="number"
                                                         v-model="tabForms[tab.id].sort"
                                                         class="w-20" />
                                            <InputError class="mt-2 lg:mt-0"
                                                        :message="tabForms[tab.id].errors.sort" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <PrimaryButton
                                    type="submit"
                                    class="ms-4 mt-3"
                                    :disabled="tabForms[tab.id].processing"
                                    :class="{ 'opacity-25': tabForms[tab.id].processing }"
                                >
                                    <template #icon>
                                        <svg class="w-3 h-3 fill-current text-slate-100"
                                             viewBox="0 0 24 24">
                                            <path
                                                d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z" />
                                        </svg>
                                    </template>
                                    {{ t('save') }}
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- Плитки этого таба -->
                        <div class="mt-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <form
                                v-for="tile in (tab.tiles || [])"
                                :key="tile.id"
                                enctype="multipart/form-data"
                                @submit.prevent="updateTile(tile)"
                                class="rounded overflow-hidden
                                       bg-gray-100/70 dark:bg-gray-800/70
                                       border border-gray-400 dark:border-gray-300
                                       shadow-md shadow-gray-400 dark:shadow-gray-900"
                            >
                                <div class="px-3 pt-3 space-y-3">
                                    <div
                                        class="font-semibold text-center text-sm opacity-75
                                               text-yellow-700 dark:text-yellow-200">
                                        #{{ tileForms[tile.id].sort }} —
                                        {{ tileForms[tile.id].title || '' }} (ID {{ tile.id }})
                                    </div>

                                    <div class="my-1 flex justify-between flex-col
                                                lg:flex-row items-center gap-4">
                                        <div class="flex flex-row items-center gap-2">
                                            <ActivityCheckbox
                                                v-model="tileForms[tile.id].activity" />
                                            <LabelCheckbox for="activity" :text="t('activity')"
                                                           class="text-sm h-8 flex items-center" />
                                        </div>
                                        <div class="flex flex-row items-center gap-2">
                                            <div class="h-8 flex items-center">
                                                <LabelInput :for="`tl-sort-${tile.id}`"
                                                            :value="t('sort')"
                                                            class="text-sm" />
                                            </div>
                                            <InputNumber :id="`tl-sort-${tile.id}`" type="number"
                                                         v-model="tileForms[tile.id].sort"
                                                         class="w-20" />
                                            <InputError class="mt-2 lg:mt-0"
                                                        :message="tileForms[tile.id].errors.sort" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <LabelInput :for="`tl-title-${tile.id}`"
                                                        :value="t('heading')" />
                                            <InputText :id="`tl-title-${tile.id}`"
                                                       v-model="tileForms[tile.id].title" />
                                            <InputError class="mt-1"
                                                        :message="tileForms[tile.id].errors.title" />
                                        </div>
                                        <div>
                                            <LabelInput :for="`tl-href-${tile.id}`"
                                                        :value="t('url')" />
                                            <InputText :id="`tl-href-${tile.id}`"
                                                       v-model="tileForms[tile.id].href" />
                                            <InputError class="mt-1"
                                                        :message="tileForms[tile.id].errors.href" />
                                        </div>
                                        <div>
                                            <LabelInput :for="`tl-lalt-${tile.id}`"
                                                        value="Light ALT" />
                                            <InputText :id="`tl-lalt-${tile.id}`"
                                                       v-model="tileForms[tile.id].light_alt" />
                                            <InputError class="mt-1"
                                                        :message="tileForms[tile.id].errors.light_alt" />
                                        </div>
                                        <div>
                                            <LabelInput :for="`tl-dalt-${tile.id}`"
                                                        value="Dark ALT" />
                                            <InputText :id="`tl-dalt-${tile.id}`"
                                                       v-model="tileForms[tile.id].dark_alt" />
                                            <InputError class="mt-1"
                                                        :message="tileForms[tile.id].errors.dark_alt" />
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
                                                class="w-full object-contain
                                                       border border-gray-400 rounded
                                                       shadow-md shadow-gray-400
                                                       dark:shadow-gray-900"
                                                :src="tilePreviews[tile.id].light ||
                                                        tile.light_webp_url ||
                                                        tile.light_image_url ||
                                                        tile.light_thumb_url"
                                                :alt="tileForms[tile.id].light_alt || 'light'"
                                            />
                                            <input
                                                type="file"
                                                accept="image/*"
                                                @change="(e) => onPickTileFile(e, tile, 'light')"
                                                class="block w-full text-md
                                                       text-gray-700 dark:text-gray-100
                                                       file:mr-4 file:py-0.5 file:px-2
                                                       file:border-0 file:text-sm file:font-semibold
                                                       file:bg-violet-600 file:text-white
                                                       hover:file:bg-violet-700"
                                            />
                                            <div v-if="tileForms[tile.id].errors.light"
                                                 class="form-error">
                                                {{ tileForms[tile.id].errors.light }}
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <div class="text-xs opacity-70 text-center
                                                        text-black dark:text-white">
                                                Dark
                                            </div>
                                            <img
                                                class="w-full object-contain
                                                       border border-gray-400 rounded
                                                       shadow-md shadow-gray-400
                                                       dark:shadow-gray-900"
                                                :src="tilePreviews[tile.id].dark ||
                                                        tile.dark_webp_url ||
                                                        tile.dark_image_url ||
                                                        tile.dark_thumb_url"
                                                :alt="tileForms[tile.id].dark_alt || 'dark'"
                                            />

                                            <input
                                                type="file"
                                                accept="image/*"
                                                @change="(e) => onPickTileFile(e, tile, 'dark')"
                                                class="block w-full text-md
                                                       text-gray-700 dark:text-gray-100
                                                       file:mr-4 file:py-0.5 file:px-2
                                                       file:border-0 file:text-sm file:font-semibold
                                                       file:bg-violet-600 file:text-white
                                                       hover:file:bg-violet-700"
                                            />
                                            <div v-if="tileForms[tile.id].errors.dark"
                                                 class="form-error">
                                                {{ tileForms[tile.id].errors.dark }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-center">
                                        <PrimaryButton
                                            type="submit"
                                            class="ms-4 mb-2"
                                            :disabled="tileForms[tile.id].processing"
                                            :class="{ 'opacity-25': tileForms[tile.id].processing }"
                                        >
                                            <template #icon>
                                                <svg class="w-3 h-3 fill-current text-slate-100"
                                                     viewBox="0 0 24 24">
                                                    <path
                                                        d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z" />
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
        </div>
    </AdminLayout>
</template>

<style scoped>
.svg-fit svg {
    width: 100%;
    height: 100%;
}
</style>
