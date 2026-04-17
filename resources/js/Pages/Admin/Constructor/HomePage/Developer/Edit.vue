<script setup>
/**
 * Developer Edit (Admin)
 * - Редактирование: DeveloperSection + DeveloperItem[]
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
    developer: { type: Object, required: true }, // DeveloperSectionResource
    items: { type: Array, required: true }  // DeveloperItemResource[]
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const mapSectionToForm = (s = {}) => ({
    locale: s.locale ?? 'ru',
    title: s.title ?? '',
    subtitle: s.subtitle ?? '',
    sort: Number.isFinite(s.sort) ? s.sort : 0,
    is_dark: !!s.is_dark,
    activity: !!s.activity,
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.developer?.locale ?? 'ru')
const switching = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.developer?.locale) return
    router.get(
        route('admin.home-page.developer.sections.edit', { section: props.developer.id, locale: loc }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onStart: () => (switching.value = true),
            onFinish: () => (switching.value = false),
        }
    )
})

/* ---------- СЕКЦИЯ (редактирование текущей записи) ---------- */
const sectionForm = useForm(mapSectionToForm(props.developer))

const syncFormFromProps = (s) => {
    const data = mapSectionToForm(s || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

watch(() => props.developer, (s) => syncFormFromProps(s))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.developer.sections.update', { section: props.developer.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.developer
                if (fresh) syncFormFromProps(fresh)
            },
        }
    )
}

/* ---------- ITEMS (карточки) ---------- */
const itemForms = reactive({})

const buildItemForm = (item) => useForm({
    developer_section_id: item.developer_section_id ?? props.developer.id,
    title: item.title ?? '',
    subtitle: item.subtitle ?? '',
    description: item.description ?? '',
    image_light: item.image_light ?? '', // inline SVG (text)
    image_dark: item.image_dark ?? '',  // inline SVG (text)
    alt: item.alt ?? '',
    sort: item.sort ?? 0,
    activity: !!item.activity,
})

const initItemForms = (list) => {
    for (const k of Object.keys(itemForms)) delete itemForms[k]
    ;
    (list || []).forEach((it) => {
        itemForms[it.id] = buildItemForm(it)
    })
}

initItemForms(props.items)
watch(() => props.items, (list) => initItemForms(list), { deep: true })

const updateItem = (item) => {
    itemForms[item.id]
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(
            route('admin.home-page.developer.items.update', { item: item.id }),
            {
                preserveScroll: true,
                onError: (errs) => console.error('DeveloperItem update errors:', errs),
                onSuccess: () => { /* оставим значения */
                },
            }
        )
}

// Helper-превью для SVG (покажем заглушку при пустых данных)
const renderOrPlaceholder = (svg) =>
    (svg && String(svg).trim().length)
        ? svg
        : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80"><rect x="1" y="1" width="118" height="78" fill="none" stroke="#cbd5e1" stroke-width="2"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, monospace" font-size="10">No SVG</text></svg>'
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Developer
                ({{ props.developer.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.developer.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
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
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <!-- Right: Actions -->
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
                            ID: {{ props.developer.id }}
                          </span>
                            {{ t('section') }} Developer
                        </h2>
                    </div>

                    <div class="space-y-1">
                        <div class="mt-3 flex justify-between flex-col lg:flex-row items-center">
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
                                    <LabelInput for="developer-sort" :value="t('sort')" class="text-sm" />
                                </div>
                                <InputNumber id="developer-sort" type="number" v-model="sectionForm.sort"
                                             autocomplete="sort" class="w-24" />
                                <InputError class="mt-2 lg:mt-0" :message="sectionForm.errors.sort" />
                            </div>

                            <!-- Локаль записи -->
                            <div class="flex flex-row items-center gap-2">
                                <SelectLocale v-model="sectionForm.locale" :errorMessage="sectionForm.errors.locale" />
                            </div>
                        </div>
                    </div>

                    <div class="py-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-3">
                            <!-- Подзаголовок -->
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text" v-model="sectionForm.subtitle"
                                           autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <!-- Заголовок -->
                            <div>
                                <LabelInput for="title" :value="t('heading')" />
                                <InputText id="title" type="text" v-model="sectionForm.title" autocomplete="title" />
                                <InputError class="mt-2" :message="sectionForm.errors.title" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit" class="ms-4 mb-0" :disabled="sectionForm.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Developer items -->
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
                          {{ t('cards') || 'Developer items' }}
                        </span>
                    </h2>

                    <div v-for="item in props.items" :key="item.id">
                        <form
                            @submit.prevent="updateItem(item)"
                            class="flex flex-col gap-3 my-4 md:flex-row md:items-start md:gap-6
                                   pt-3 px-3 w-full border border-gray-400
                                   bg-slate-200/70 dark:bg-slate-900/70 rounded-md
                                   shadow-md shadow-gray-400 dark:shadow-gray-900"
                        >
                            <!-- Левая колонка: тексты -->
                            <div class="w-full md:w-1/2 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox v-model="itemForms[item.id].activity" />
                                        <LabelCheckbox for="activity" :text="t('activity')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <LabelInput :for="`item-sort-${item.id}`" :value="t('sort')"
                                                    class="text-sm" />
                                        <InputNumber
                                            :id="`item-sort-${item.id}`"
                                            type="number"
                                            v-model="itemForms[item.id].sort"
                                            class="w-20"
                                        />
                                        <InputError class="mt-2 lg:mt-0"
                                                    :message="itemForms[item.id].errors.sort" />
                                    </div>
                                </div>

                                <div>
                                    <LabelInput :for="`item-title-${item.id}`" :value="t('title')" />
                                    <InputText :id="`item-title-${item.id}`" type="text"
                                               v-model="itemForms[item.id].title" />
                                    <InputError class="mt-2"
                                                :message="itemForms[item.id].errors.title" />
                                </div>

                                <div>
                                    <LabelInput :for="`item-subtitle-${item.id}`"
                                                :value="t('subtitle')" />
                                    <InputText :id="`item-subtitle-${item.id}`" type="text"
                                               v-model="itemForms[item.id].subtitle" />
                                    <InputError class="mt-2"
                                                :message="itemForms[item.id].errors.subtitle" />
                                </div>

                                <div>
                                    <LabelInput :for="`item-desc-${item.id}`"
                                                :value="t('description')" />
                                    <MetaDescTextarea :id="`item-desc-${item.id}`"
                                                      v-model="itemForms[item.id].description" />
                                    <InputError class="mt-2"
                                                :message="itemForms[item.id].errors.description" />
                                </div>
                            </div>

                            <!-- Правая колонка: inline SVG (текст + превью) -->
                            <div class="w-full md:w-1/2 pt-2">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- LIGHT SVG -->
                                    <div class="space-y-2">
                                        <div class="text-center text-xs
                                                    text-slate-900/80 dark:text-slate-100/80">
                                            Light (inline SVG)
                                        </div>
                                        <div
                                            class="w-full h-24 flex items-center justify-between
                                                   rounded bg-white overflow-hidden
                                                   border border-gray-400 shadow-md
                                                   shadow-gray-400 dark:shadow-gray-900"
                                        v-html="renderOrPlaceholder(itemForms[item.id].image_light)"
                                        />
                                        <CodeTextarea
                                            :id="`item-light-${item.id}`"
                                            v-model="itemForms[item.id].image_light"
                                            :rows="4"
                                            wrap="off"
                                            resize="y"
                                            class="font-mono"
                                            :placeholder="`<svg ...>...</svg>`"
                                        />
                                        <InputError class="mt-2"
                                                :message="itemForms[item.id].errors.image_light" />
                                    </div>

                                    <!-- DARK SVG -->
                                    <div class="space-y-2">
                                        <div class="text-center text-xs
                                                    text-slate-900/80 dark:text-slate-100/80">
                                            Dark (inline SVG)
                                        </div>
                                        <div
                                            class="w-full h-24 flex items-center justify-between
                                                   rounded bg-slate-700 overflow-hidden
                                                   border border-gray-400 shadow-md
                                                   shadow-gray-400 dark:shadow-gray-900"
                                        v-html="renderOrPlaceholder(itemForms[item.id].image_dark)"
                                        />
                                        <CodeTextarea
                                            :id="`item-dark-${item.id}`"
                                            v-model="itemForms[item.id].image_dark"
                                            :rows="4"
                                            wrap="off"
                                            resize="y"
                                            class="font-mono"
                                            :placeholder="`<svg ...>...</svg>`"
                                        />
                                        <InputError class="mt-2"
                                                :message="itemForms[item.id].errors.image_dark" />
                                    </div>
                                </div>

                                <div class="space-y-2 mt-1">
                                    <LabelInput :for="`item-alt-${item.id}`" value="Alt" />
                                    <InputText :id="`item-alt-${item.id}`" type="text"
                                               v-model="itemForms[item.id].alt" />
                                    <InputError class="mt-2"
                                                :message="itemForms[item.id].errors.alt" />
                                </div>

                                <div class="flex justify-end mt-4">
                                    <PrimaryButton
                                        class="ms-4 mb-0"
                                        @click.prevent="updateItem(item)"
                                        :disabled="itemForms[item.id].processing"
                                        :class="{ 'opacity-25': itemForms[item.id].processing }"
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
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
