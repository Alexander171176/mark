<script setup>
/**
 * Demo Edit (Admin)
 * - Редактирование: DemoSection + DemoGroup[] (+ inline SVG) + DemoItem[] (с медиа)
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
import CodeTextarea from '@/Components/Admin/Textarea/CodeTextarea.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LocaleTabs from '@/Components/Admin/Tab/LocaleTabs.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    demo: { type: Object, required: true },  // DemoSectionResource
    groups: { type: Array, required: true }   // DemoGroupResource[] (каждая включает items[])
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const mapSectionToForm = (s = {}) => ({
    locale: s.locale ?? 'ru',
    title: s.title ?? '',
    subtitle: s.subtitle ?? '',
    search_placeholder: s.search_placeholder ?? '',
    is_dark: !!s.is_dark,
    activity: !!s.activity,
    sort: Number.isFinite(s.sort) ? s.sort : 0
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.demo?.locale ?? 'ru')
const switching = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.demo?.locale) return
    router.get(
        route('admin.home-page.demo.sections.edit', { section: props.demo.id, locale: loc }),
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

/* ---------- СЕКЦИЯ ---------- */
const sectionForm = useForm(mapSectionToForm(props.demo))

const syncFormFromProps = (s) => {
    const data = mapSectionToForm(s || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

watch(() => props.demo, (s) => syncFormFromProps(s))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.demo.sections.update', { section: props.demo.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.demo
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

// ===== ГРУППЫ =====
const groupForms = reactive(
    Object.fromEntries(
        props.groups.map(g => [
            g.id,
            useForm({
                section_id: g.section_id,
                title: g.title ?? '',
                description: g.description ?? '',
                slug: g.slug ?? '',
                icon_alt: g.icon_alt ?? '',
                icon_svg_light: g.icon_svg_light ?? '',
                icon_svg_dark:  g.icon_svg_dark  ?? '',
                sort: g.sort ?? 0,
                activity: !!g.activity,
            }),
        ])
    )
)

const updateGroup = (group) => {
    groupForms[group.id].put(
        route('admin.home-page.demo.groups.update', { group: group.id }),
        { preserveScroll: true }
    )
}

// ===== АЙТЕМЫ =====
const itemForms = reactive(
    Object.fromEntries(
        // ВАЖНО: map делаем ВНУТРИ flatMap, чтобы был доступ к g при необходимости
        props.groups.flatMap(g =>
            (g.items ?? []).map(it => [
                it.id,
                useForm({
                    group_id: it.group_id,
                    href: it.href ?? '',
                    title: it.title ?? '',
                    category: it.category ?? '',
                    alt: it.alt ?? '',
                    sort: it.sort ?? 0,
                    activity: !!it.activity,
                    light: null,
                    dark:  null,
                }),
            ])
        )
    )
)

const itemPreviews = reactive(
    Object.fromEntries(
        props.groups.flatMap(g => (g.items ?? [])).map(it => [
            it.id, { light: null, dark: null }
        ])
    )
)

const onPickItemFile = (e, item, key /* 'light' | 'dark' */) => {
    const file = e.target.files?.[0] ?? null
    itemForms[item.id][key] = file
    itemPreviews[item.id][key] = file ? URL.createObjectURL(file) : null
}

const updateItem = (item) => {
    itemForms[item.id]
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(
            route('admin.home-page.demo.items.update', { item: item.id }),
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    itemForms[item.id].light = null
                    itemForms[item.id].dark = null
                    itemPreviews[item.id].light = null
                    itemPreviews[item.id].dark = null
                }
            }
        )
}
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Demo
                ({{ props.demo.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.demo.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
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
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <!-- Right: Actions -->
                    <div class="grid grid-flow-col
                                sm:auto-cols-max justify-start sm:justify-end gap-2">
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
                          <span class="inline-flex items-center gap-2 text-xs px-2 py-0.5
                                       rounded border border-slate-700 dark:border-slate-300
                                       text-black dark:text-white">
                            ID: {{ props.demo.id }}
                          </span>
                            {{ t('section') }} Demo
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
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.title" />
                            </div>
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text"
                                           v-model="sectionForm.subtitle"
                                           autocomplete="subtitle" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.subtitle" />
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <LabelInput for="search_placeholder"
                                            :value="t('searchPlaceholder')" />
                                <MetaDescTextarea
                                    v-model="sectionForm.search_placeholder" class="w-full" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.search_placeholder" />
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

                <!-- Группы -->
                <div class="w-full mt-2">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M3 5h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Z" />
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('groups') }}
                        </span>
                    </h2>

                    <div class="divide-y divide-slate-400">
                        <div v-for="group in props.groups" :key="group.id"
                             class="mb-3">
                            <h3 class="font-semibold text-center
                                       text-gray-700 dark:text-gray-300 my-3">
                                <span class="text-amber-600 dark:text-amber-200 mr-2">
                                {{ group.title }}
                                </span>
                                [ID {{ group.id }}]
                            </h3>
                            <form @submit.prevent="updateGroup(group)"
                                  class="flex flex-col gap-1 pt-3
                                         bg-slate-200/70 dark:bg-slate-900/70
                                         px-3 w-full border border-gray-400 rounded-md
                                         shadow-md shadow-gray-400 dark:shadow-gray-900">

                                <div class="my-1 flex justify-between flex-col
                                                    lg:flex-row items-center gap-4">
                                    <div class="flex flex-row items-center gap-2">
                                        <ActivityCheckbox
                                            v-model="groupForms[group.id].activity" />
                                        <LabelCheckbox for="activity" :text="t('activity')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex flex-row items-center gap-2">
                                        <div class="h-8 flex items-center">
                                            <LabelInput :for="`g-sort-${group.id}`"
                                                        :value="t('sort')"
                                                        class="text-sm" />
                                        </div>
                                        <InputNumber :id="`g-sort-${group.id}`" type="number"
                                                     v-model="groupForms[group.id].sort"
                                                     class="w-full lg:w-20" />
                                        <InputError class="mt-2 lg:mt-0"
                                                    :message="groupForms[group.id].errors.sort" />
                                    </div>
                                </div>

                                <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Метаданные -->
                                    <div class="space-y-2">
                                        <div>
                                            <LabelInput :for="`g-title-${group.id}`"
                                                        :value="t('heading')" />
                                            <InputText :id="`g-title-${group.id}`"
                                                       v-model="groupForms[group.id].title" />
                                            <InputError class="mt-1"
                                                        :message="groupForms[group.id].errors.title" />
                                        </div>

                                        <div>
                                            <LabelInput :for="`g-desc-${group.id}`"
                                                        :value="t('description')" />
                                            <InputText :id="`g-desc-${group.id}`"
                                                       v-model="groupForms[group.id].description" />
                                            <InputError class="mt-1"
                                                        :message="groupForms[group.id].errors.description" />
                                        </div>

                                        <!-- SVG light -->
                                        <div class="space-y-2">
                                            <LabelInput :for="`g-svg-light-${group.id}`"
                                                        :value="t('svg')" />
                                            <CodeTextarea
                                                :id="`g-svg-light-${group.id}`"
                                                v-model="groupForms[group.id].icon_svg_light"
                                                placeholder="<svg ...>...</svg>"
                                                :rows="16"
                                                resize="y"
                                                wrap="off"
                                                class="h-40 md:h-32"
                                            />
                                            <InputError class="mt-1"
                                                        :message="groupForms[group.id].errors.icon_svg_light" />
                                        </div>
                                    </div>

                                    <div class="space-y-2">

                                        <div>
                                            <LabelInput :for="`g-slug-${group.id}`"
                                                        :value="t('url')" />
                                            <InputText :id="`g-slug-${group.id}`"
                                                       v-model="groupForms[group.id].slug" />
                                            <InputError class="mt-1"
                                                        :message="groupForms[group.id].errors.slug" />
                                        </div>

                                        <div>
                                            <LabelInput :for="`g-alt-${group.id}`"
                                                        :value="t('seoAltImage')" />
                                            <InputText :id="`g-alt-${group.id}`"
                                                       v-model="groupForms[group.id].icon_alt" />
                                            <InputError class="mt-1"
                                                        :message="groupForms[group.id].errors.icon_alt" />
                                        </div>

                                        <!-- SVG dark -->
                                        <div class="space-y-2">
                                            <LabelInput :for="`g-svg-dark-${group.id}`"
                                                        value="SVG (dark)" />
                                            <CodeTextarea
                                                :id="`g-svg-dark-${group.id}`"
                                                v-model="groupForms[group.id].icon_svg_dark"
                                                placeholder="<svg ...>...</svg>"
                                                :rows="16"
                                                resize="y"
                                                wrap="off"
                                                class="h-40 md:h-32"
                                            />
                                            <InputError class="mt-1"
                                                        :message="groupForms[group.id].errors.icon_svg_dark" />
                                        </div>

                                    </div>

                                </div>

                                <div class="pt-3 flex justify-end">
                                    <PrimaryButton class="ms-4 mb-0"
                                                   :disabled="groupForms[group.id].processing"
                                                   :class="{ 'opacity-25': groupForms[group.id].processing }"
                                                   @click.prevent="updateGroup(group)">
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

                            <!-- Айтемы группы -->
                            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                <form v-for="it in (group.items || [])"
                                      :key="it.id"
                                      enctype="multipart/form-data"
                                      @submit.prevent="updateItem(it)"
                                      class="rounded overflow-hidden
                                             bg-gray-100/70 dark:bg-gray-800/70
                                             border border-gray-400 dark:border-gray-300
                                             shadow-md shadow-gray-400 dark:shadow-gray-900">

                                    <div class="px-3 pt-3 space-y-3">
                                        <div class="font-semibold text-center text-sm opacity-75
                                                    text-yellow-700 dark:text-yellow-200">
                                            #{{ it.sort }} — {{ it.title || '' }}
                                        </div>

                                        <div class="my-1 flex justify-between flex-col
                                                    lg:flex-row items-center gap-4">
                                            <div class="flex flex-row items-center gap-2">
                                                <ActivityCheckbox
                                                    v-model="itemForms[it.id].activity" />
                                                <LabelCheckbox for="activity" :text="t('activity')"
                                                               class="text-sm h-8 flex items-center" />
                                            </div>
                                            <div class="flex flex-row items-center gap-2">
                                                <div class="h-8 flex items-center">
                                                    <LabelInput :for="`i-sort-${it.id}`"
                                                                :value="t('sort')"
                                                                class="text-sm" />
                                                </div>
                                                <InputNumber :id="`i-sort-${it.id}`" type="number"
                                                             v-model="itemForms[it.id].sort"
                                                             class="w-full lg:w-20" />
                                                <InputError class="mt-2 lg:mt-0"
                                                            :message="itemForms[it.id].errors.sort" />
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
                                                    :src="itemPreviews[it.id].light ||
                                                            it.light_thumb_url ||
                                                            it.light_webp_url ||
                                                            it.light_url"
                                                    :alt="t('view')"
                                                />
                                                <div class="mt-4">
                                                    <input type="file" accept="image/*"
                                                           @change="(e) => onPickItemFile(e, it, 'light')"
                                                           class="block w-full text-md
                                                                  text-gray-700 dark:text-gray-100
                                                    file:mr-4 file:py-0.5 file:px-2
                                                    file:border-0 file:text-sm file:font-semibold
                                                    file:bg-violet-600 file:text-white
                                                    hover:file:bg-violet-700" />
                                                </div>
                                                <div v-if="itemForms[it.id].errors.light"
                                                     class="form-error">
                                                    {{ itemForms[it.id].errors.light }}
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
                                                    :src="itemPreviews[it.id].dark ||
                                                            it.dark_thumb_url ||
                                                            it.dark_webp_url ||
                                                            it.dark_url"
                                                    alt=""
                                                />
                                                <div class="mt-4">
                                                    <input type="file" accept="image/*"
                                                           @change="(e) => onPickItemFile(e, it, 'dark')"
                                                           class="block w-full text-md
                                                                  text-gray-700 dark:text-gray-100
                                                    file:mr-4 file:py-0.5 file:px-2
                                                    file:border-0 file:text-sm file:font-semibold
                                                    file:bg-violet-600 file:text-white
                                                    hover:file:bg-violet-700" />
                                                </div>
                                                <div v-if="itemForms[it.id].errors.dark"
                                                     class="form-error">
                                                    {{ itemForms[it.id].errors.dark }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Мета айтема -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <LabelInput :for="`i-href-${it.id}`" :value="t('url')" />
                                                <InputText :id="`i-href-${it.id}`" v-model="itemForms[it.id].href" />
                                                <InputError class="mt-1" :message="itemForms[it.id].errors.href" />
                                            </div>
                                            <div>
                                                <LabelInput :for="`i-alt-${it.id}`" value="Alt" />
                                                <InputText :id="`i-alt-${it.id}`" v-model="itemForms[it.id].alt" />
                                                <InputError class="mt-1" :message="itemForms[it.id].errors.alt" />
                                            </div>
                                            <div>
                                                <LabelInput :for="`i-title-${it.id}`" :value="t('heading')" />
                                                <InputText :id="`i-title-${it.id}`" v-model="itemForms[it.id].title" />
                                                <InputError class="mt-1" :message="itemForms[it.id].errors.title" />
                                            </div>
                                            <div>
                                                <LabelInput :for="`i-category-${it.id}`" :value="t('category')" />
                                                <InputText :id="`i-category-${it.id}`"
                                                           v-model="itemForms[it.id].category" />
                                                <InputError class="mt-1" :message="itemForms[it.id].errors.category" />
                                            </div>
                                        </div>

                                        <div class="flex justify-center">
                                            <PrimaryButton type="submit"
                                                           class="ms-4 mb-0"
                                                           :disabled="itemForms[it.id].processing"
                                                           :class="{ 'opacity-25': itemForms[it.id].processing }">
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
        </div>
    </AdminLayout>
</template>
