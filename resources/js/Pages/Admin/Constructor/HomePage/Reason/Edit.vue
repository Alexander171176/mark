<script setup>
/**
 * Reason Edit (Admin)
 * - Редактирование: ReasonSection (тексты + CTA)
 * - Редактирование айтемов: ReasonItem[] (мета + загрузка image / light / dark)
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
import DescriptionTextarea from '@/Components/Admin/Textarea/DescriptionTextarea.vue'
import LabelCheckbox from '@/Components/Admin/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'
import SelectLocale from '@/Components/Admin/Select/SelectLocale.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import LocaleTabs from '@/Components/Admin/Tab/LocaleTabs.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    reason: { type: Object, required: true }, // ReasonSectionResource
    items: { type: Array, required: true }  // ReasonItemResource[]
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const mapSectionToForm = (s = {}) => ({
    locale: s.locale ?? 'ru',
    subtitle: s.subtitle ?? '',
    title: s.title ?? '',
    cta_title: s.cta_title ?? '',
    cta_btn_text: s.cta_btn_text ?? '',
    cta_btn_url: s.cta_btn_url ?? '',
    cta_btn_target: s.cta_btn_target ?? '_self',
    sort: Number.isFinite(s.sort) ? s.sort : 0,
    activity: !!s.activity
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.reason?.locale ?? 'ru')
const switching = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.reason?.locale) return
    router.get(
        route('admin.home-page.reason.sections.edit', { section: props.reason.id, locale: loc }),
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
const sectionForm = useForm(mapSectionToForm(props.reason))

const syncFormFromProps = (s) => {
    const data = mapSectionToForm(s || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

watch(() => props.reason, (s) => syncFormFromProps(s))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.reason.sections.update', { section: props.reason.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.reason
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

/* ---------- Айтемы (ReasonItem) ---------- */
const itemForms = reactive(
    Object.fromEntries(
        (props.items ?? []).map((it) => [
            it.id,
            useForm({
                section_id: props.reason.id,
                title: it.title ?? '',
                text: it.text ?? '',
                align: it.align ?? 'left', // 'left' | 'right'
                image_alt: it.image_alt ?? '',
                light_alt: it.light_alt ?? '',
                dark_alt: it.dark_alt ?? '',
                sort: Number.isFinite(it.sort) ? it.sort : 0,
                activity: !!it.activity,

                // файлы
                image: null,
                light: null,
                dark: null
            })
        ])
    )
)

const itemPreviews = reactive(
    Object.fromEntries(
        (props.items ?? []).map((it) => [
            it.id,
            { image: null, light: null, dark: null } // object URLs
        ])
    )
)

const onPickItemFile = (e, item, key /* 'image' | 'light' | 'dark' */) => {
    const f = e.target.files?.[0] ?? null
    itemForms[item.id][key] = f
    itemPreviews[item.id][key] = f ? URL.createObjectURL(f) : null
}

const updateItem = (item) => {
    itemForms[item.id]
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(route('admin.home-page.reason.items.update', { item: item.id }), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                itemForms[item.id].image = null
                itemForms[item.id].light = null
                itemForms[item.id].dark = null
                itemPreviews[item.id].image = null
                itemPreviews[item.id].light = null
                itemPreviews[item.id].dark = null
            }
        })
}
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Reason
                ({{ props.reason.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.reason.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
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
                                ID: {{ props.reason.id }}
                              </span>
                            {{ t('section') }} Reason
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

                    <div class="pt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text" v-model="sectionForm.subtitle"
                                           autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>
                            <div>
                                <LabelInput for="cta_title" value="CTA Title" />
                                <InputText id="cta_title" type="text" v-model="sectionForm.cta_title" />
                                <InputError class="mt-2" :message="sectionForm.errors.cta_title" />
                            </div>
                            <div>
                                <LabelInput for="cta_btn_url" value="CTA Button URL" />
                                <InputText id="cta_btn_url" type="text"
                                           v-model="sectionForm.cta_btn_url" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.cta_btn_url" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <LabelInput for="title" :value="t('heading')" />
                                <InputText id="title" type="text"
                                           v-model="sectionForm.title" autocomplete="title" />
                                <InputError class="mt-2" :message="sectionForm.errors.title" />
                            </div>
                            <div>
                                <LabelInput for="cta_btn_text" value="CTA Button Text" />
                                <InputText id="cta_btn_text" type="text"
                                           v-model="sectionForm.cta_btn_text" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.cta_btn_text" />
                            </div>
                            <div>
                                <LabelInput for="cta_btn_target" value="CTA Target" />
                                <select
                                    id="cta_btn_target"
                                    v-model="sectionForm.cta_btn_target"
                                    class="block w-full py-0.5 rounded-sm shadow-sm
                                           font-semibold text-sm border-slate-500
                                           focus:border-indigo-500 focus:ring-indigo-300
                                           dark:bg-cyan-800 dark:text-slate-100"
                                >
                                    <option value="_self">_self</option>
                                    <option value="_blank">_blank</option>
                                </select>
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.cta_btn_target" />
                            </div>
                        </div>
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

                <!-- Айтемы -->
                <div class="w-full mt-1">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-1" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M3 5h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2Z" />
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('sections') }}
                        </span>
                    </h2>

                    <div class="mt-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <form
                            v-for="it in (props.items || [])"
                            :key="it.id"
                            enctype="multipart/form-data"
                            @submit.prevent="updateItem(it)"
                            class="rounded overflow-hidden
                                   bg-gray-100/70 dark:bg-gray-800/70
                                   border border-gray-400 dark:border-gray-300
                                   shadow-md shadow-gray-400 dark:shadow-gray-900"
                        >
                            <div class="px-3 pt-3 space-y-3">
                                <div
                                    class="font-semibold text-center text-sm opacity-75
                                           text-yellow-700 dark:text-yellow-200">
                                    #{{ itemForms[it.id].sort }} —
                                    {{ itemForms[it.id].title || '' }} (ID {{ it.id }})
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

                                <!-- позиция изображения -->
                                <div class="my-1 flex justify-between items-center gap-2">
                                    <LabelInput
                                        :for="`i-align-${it.id}`"
                                        :value="t('imagePosition')"
                                    />
                                    <select
                                        :id="`i-align-${it.id}`"
                                        v-model="itemForms[it.id].align"
                                        class="block w-40 py-0.5 rounded-sm shadow-sm
                                               font-semibold text-sm border-slate-500
                                               focus:border-indigo-500 focus:ring-indigo-300
                                               dark:bg-cyan-800 dark:text-slate-100"
                                    >
                                        <option value="left">{{ t('isLeft') }}</option>
                                        <option value="right">{{ t('isRight') }}</option>
                                    </select>
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.align" />
                                </div>

                                <div class="my-1 flex flex-row items-center gap-2">
                                    <LabelInput :for="`i-title-${it.id}`"
                                                :value="t('heading')" />
                                    <InputText :id="`i-title-${it.id}`"
                                               v-model="itemForms[it.id].title" />
                                    <InputError class="mt-1"
                                                :message="itemForms[it.id].errors.title" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="md:col-span-2">
                                        <LabelInput :for="`i-text-${it.id}`"
                                                    :value="t('description')" />
                                        <DescriptionTextarea :id="`i-text-${it.id}`"
                                                          v-model="itemForms[it.id].text" />
                                        <InputError class="mt-1"
                                                    :message="itemForms[it.id].errors.text" />
                                    </div>
                                </div>

                                <!-- Превью/аплоады: SINGLE -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                    <!-- LIGHT -->
                                    <div class="space-y-2">
                                        <div class="text-xs opacity-70
                                                    text-center text-black dark:text-white">
                                            Light
                                        </div>
                                        <img
                                            class="w-full object-contain
                                                   border border-gray-400 rounded
                                                   shadow-md shadow-gray-400 dark:shadow-gray-900"
                                            :src="itemPreviews[it.id].light ||
                                                    it.light_webp_url ||
                                                    it.light_image_url ||
                                                    it.light_thumb_url"
                                            :alt="itemForms[it.id].light_alt || 'light'"
                                        />
                                        <input
                                            type="file"
                                            accept="image/*"
                                            @change="(e) => onPickItemFile(e, it, 'light')"
                                            class="block w-full
                                                   text-md text-gray-700 dark:text-gray-100
                                                   file:mr-4 file:py-0.5 file:px-2
                                                   file:border-0 file:text-sm file:font-semibold
                                                   file:bg-violet-600 file:text-white
                                                   hover:file:bg-violet-700"
                                        />
                                        <InputError v-if="itemForms[it.id].errors.light"
                                                    class="mt-1"
                                                    :message="itemForms[it.id].errors.light" />
                                        <LabelInput :for="`i-light-alt-${it.id}`"
                                                    value="ALT (light)" />
                                        <InputText :id="`i-light-alt-${it.id}`"
                                                   v-model="itemForms[it.id].light_alt" />
                                        <InputError class="mt-1"
                                                    :message="itemForms[it.id].errors.light_alt" />
                                    </div>

                                    <!-- DARK -->
                                    <div class="space-y-2">
                                        <div class="text-xs opacity-70
                                                    text-center text-black dark:text-white">
                                            Dark
                                        </div>
                                        <img
                                            class="w-full object-contain
                                                   border border-gray-400 rounded
                                                   shadow-md shadow-gray-400 dark:shadow-gray-900"
                                            :src="itemPreviews[it.id].dark ||
                                                    it.dark_webp_url ||
                                                    it.dark_image_url ||
                                                    it.dark_thumb_url"
                                            :alt="itemForms[it.id].dark_alt || 'dark'"
                                        />
                                        <input
                                            type="file"
                                            accept="image/*"
                                            @change="(e) => onPickItemFile(e, it, 'dark')"
                                            class="block w-full text-md text-gray-700 dark:text-gray-100
                             file:mr-4 file:py-0.5 file:px-2 file:border-0 file:text-sm file:font-semibold
                             file:bg-violet-600 file:text-white hover:file:bg-violet-700"
                                        />
                                        <InputError v-if="itemForms[it.id].errors.dark" class="mt-1"
                                                    :message="itemForms[it.id].errors.dark" />
                                        <LabelInput :for="`i-dark-alt-${it.id}`" value="ALT (dark)" />
                                        <InputText :id="`i-dark-alt-${it.id}`" v-model="itemForms[it.id].dark_alt" />
                                        <InputError class="mt-1" :message="itemForms[it.id].errors.dark_alt" />
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <PrimaryButton
                                        type="submit"
                                        class="ms-4 mb-2"
                                        :disabled="itemForms[it.id].processing"
                                        :class="{ 'opacity-25': itemForms[it.id].processing }"
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

<style scoped>
.svg-fit svg {
    width: 100%;
    height: 100%;
}
</style>
