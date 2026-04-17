<script setup>
/**
 * Quickstart Edit (Admin)
 * - Редактирование: QuickstartSection + (постер/видео через Spatie)
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
    quickstart: { type: Object, required: true } // QuickstartSectionResource
})

const availableLocales = computed(() => page.props?.value?.locales ?? ['ru', 'en', 'kk'])

/* ---------- helpers ---------- */
const mapSectionToForm = (s = {}) => ({
    locale: s.locale ?? 'ru',
    title: s.title ?? '',
    subtitle: s.subtitle ?? '',

    // Primary button
    primary_label: s.primary_label ?? '',
    primary_url: s.primary_url ?? '',
    primary_icon: s.primary_icon ?? '', // inline SVG (text)

    // Secondary button (+popover)
    secondary_label: s.secondary_label ?? '',
    secondary_url: s.secondary_url ?? '',
    secondary_icon: s.secondary_icon ?? '', // inline SVG (text)
    secondary_popover_enabled: !!s.secondary_popover_enabled,
    secondary_popover_title: s.secondary_popover_title ?? '',
    secondary_popover_text: s.secondary_popover_text ?? '',

    // Video / poster meta
    video_alt: s.video_alt ?? '',
    video_caption: s.video_caption ?? '',

    // Видео-опции
    video_options: {
        controls: s.video_options?.controls ?? true,
        autoplay: s.video_options?.autoplay ?? false,
        loop: s.video_options?.loop ?? false,
        muted: s.video_options?.muted ?? true,
        playsinline: s.video_options?.playsinline ?? true,
        preload: s.video_options?.preload ?? 'metadata', // none|metadata|auto
    },

    // Flags / sort
    sort: Number.isFinite(s.sort) ? s.sort : 0,
    is_dark: !!s.is_dark,
    activity: !!s.activity,
})

/* ---------- Переключатель просмотра локали (НЕ редактирование) ---------- */
const viewerLocale = ref(props.quickstart?.locale ?? 'ru')
const switching = ref(false)

watch(viewerLocale, (loc) => {
    if (!loc || loc === props.quickstart?.locale) return
    router.get(
        route('admin.home-page.quickstart.sections.edit',
            { section: props.quickstart.id, locale: loc }),
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
const sectionForm = useForm(mapSectionToForm(props.quickstart))

const syncFormFromProps = (s) => {
    const data = mapSectionToForm(s || {})
    sectionForm.defaults(data)
    sectionForm.reset()
    Object.entries(data).forEach(([k, v]) => {
        if (k in sectionForm) sectionForm[k] = v
    })
}

watch(() => props.quickstart, (s) => syncFormFromProps(s))

const updateSection = () => {
    sectionForm.put(
        route('admin.home-page.quickstart.sections.update', { section: props.quickstart.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                const fresh = page.props?.value?.quickstart
                if (fresh) syncFormFromProps(fresh)
            }
        }
    )
}

/* ---------- МЕДИА (постер/видео) ---------- */
const mediaForm = useForm({
    poster: null,
    video: null,
})

const mediaPreview = reactive({
    poster: null, // object URL
    video: null, // object URL
})

const onPickPoster = (e) => {
    const f = e.target.files?.[0] ?? null
    mediaForm.poster = f
    mediaPreview.poster = f ? URL.createObjectURL(f) : null
}
const onPickVideo = (e) => {
    const f = e.target.files?.[0] ?? null
    mediaForm.video = f
    mediaPreview.video = f ? URL.createObjectURL(f) : null
}

const uploadMedia = () => {
    mediaForm
        .transform((data) => ({
            ...data,
            _method: 'put',
            // подкинем locale текущей формы секции
            locale: sectionForm.locale,
        }))
        .post(route('admin.home-page.quickstart.sections.update', { section: props.quickstart.id }), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                mediaForm.poster = null
                mediaForm.video = null
                mediaPreview.poster = null
                mediaPreview.video = null
            },
        })
}

/* ---------- UI helpers ---------- */
const preloadOptions = ['none', 'metadata', 'auto']
// Покажем заглушку для пустых SVG
const renderOrPlaceholder = (svg) =>
    (svg && String(svg).trim().length)
        ? svg
        : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80"><rect x="1" y="1" width="118" height="78" fill="none" stroke="#cbd5e1" stroke-width="2"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, monospace" font-size="10">No SVG</text></svg>'
</script>

<template>
    <AdminLayout :title="t('edit')">
        <template #header>
            <TitlePage>
                {{ t('pages') }}: {{ t('home') }} — Quickstart
                ({{ props.quickstart.locale?.toUpperCase() ?? 'RU' }})
            </TitlePage>
        </template>

        <div :key="props.quickstart.id" class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        shadow-lg shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Назад -->
                    <DefaultButton :href="route('admin.home-page.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
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
                <form @submit.prevent="updateSection"
                      class="mb-3 pt-3 px-3 w-full bg-gray-200 dark:bg-gray-800
                             border border-gray-400 rounded-md
                             shadow-md shadow-gray-400 dark:shadow-gray-900">

                    <div class="pb-3 border-b border-gray-400 flex items-center justify-center">
                        <h2 class="font-semibold text-md text-slate-700 dark:text-slate-300">
                           <span class="inline-flex items-center gap-2 text-xs px-2 py-0.5
                                        rounded border border-slate-700 dark:border-slate-300
                                        text-black dark:text-white">
                                ID: {{ props.quickstart.id }}
                           </span>
                            {{ t('section') }} Quickstart
                        </h2>
                    </div>

                    <div class="space-y-1">
                        <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
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
                                <InputNumber id="sort" type="number" v-model="sectionForm.sort" class="w-24" />
                                <InputError class="mt-2 lg:mt-0" :message="sectionForm.errors.sort" />
                            </div>
                            <div class="flex flex-row items-center gap-2">
                                <SelectLocale v-model="sectionForm.locale" :errorMessage="sectionForm.errors.locale" />
                            </div>
                        </div>
                    </div>

                    <div class="py-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="space-y-3">
                            <!-- Заголовок -->
                            <div>
                                <LabelInput for="title" :value="t('heading')" />
                                <InputText id="title" type="text" v-model="sectionForm.title" autocomplete="title" />
                                <InputError class="mt-2" :message="sectionForm.errors.title" />
                            </div>

                            <!-- Primary button -->
                            <div class="pt-2">
                                <div class="text-center text-sm font-semibold opacity-70 mb-1
                                            text-black dark:text-white">
                                    {{ `${t('button')} 1` }}
                                </div>
                                <LabelInput for="primary_label" :value="t('title')" />
                                <InputText id="primary_label" type="text"
                                           v-model="sectionForm.primary_label" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.primary_label" />

                                <LabelInput for="primary_url" class="mt-2" value="URL" />
                                <InputText id="primary_url" type="text"
                                           v-model="sectionForm.primary_url" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.primary_url" />

                                <LabelInput for="primary_icon" class="mt-2"
                                            :value="t('svg')" />
                                <CodeTextarea
                                    id="primary_icon"
                                    v-model="sectionForm.primary_icon"
                                    :rows="6"
                                    wrap="off"
                                    resize="y"
                                    class="font-mono"
                                    placeholder="<svg ...>...</svg>"
                                />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.primary_icon" />

                                <div class="mt-1 flex items-center justify-center gap-2">
                                    <div class="text-xs opacity-70 mb-1
                                                text-black dark:text-white">
                                        {{ t('icon') }}
                                    </div>
                                    <div class="w-fit rounded bg-white p-1.5
                                                overflow-hidden
                                                border border-slate-400
                                                bg-white/90 dark:bg-cyan-800/60
                                                text-black dark:text-white"
                                         v-html="renderOrPlaceholder(sectionForm.primary_icon)" />
                                </div>

                            </div>

                            <!-- Видео: подписи и опции -->
                            <div class="pt-2">
                                <div class="text-center text-sm font-semibold opacity-70 mb-1
                                            text-black dark:text-white">
                                    {{ t('videoOptions') }}
                                </div>
                                <div class="mb-4 flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox
                                            v-model="sectionForm.video_options.controls" />
                                        <LabelCheckbox for="opt_controls"
                                                       :text="t('videoControls')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox
                                            v-model="sectionForm.video_options.autoplay" />
                                        <LabelCheckbox for="opt_autoplay"
                                                       :text="t('videoAutoplay')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox v-model="sectionForm.video_options.loop" />
                                        <LabelCheckbox for="opt_loop" :text="t('videoLoop')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox v-model="sectionForm.video_options.muted" />
                                        <LabelCheckbox for="opt_muted"
                                                       :text="t('videoMuted')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <ActivityCheckbox
                                            v-model="sectionForm.video_options.playsinline" />
                                        <LabelCheckbox for="opt_inline"
                                                       :text="t('videoPlaysInline')"
                                                       class="text-sm h-8 flex items-center" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <LabelInput for="opt_preload"
                                                    :value="t('videoPreload')"
                                                    class="text-sm" />
                                        <select id="opt_preload"
                                                v-model="sectionForm.video_options.preload"
                                                class="block w-full py-0.5 border-slate-500
                                                       font-semibold text-sm
                                                       focus:border-indigo-500 focus:ring-indigo-300
                                                       rounded-sm shadow-sm
                                                       dark:bg-cyan-800 dark:text-slate-100">
                                            <option v-for="p in preloadOptions" :key="p"
                                                    :value="p">{{ p }}</option>
                                        </select>
                                    </div>
                                </div>

                                <LabelInput for="video_alt" :value="t('videoAlt')" />
                                <InputText id="video_alt" type="text"
                                           v-model="sectionForm.video_alt" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.video_alt" />

                                <LabelInput for="video_caption" class="mt-2"
                                            :value="t('videoCaption')" />
                                <MetaDescTextarea id="video_caption"
                                                  v-model="sectionForm.video_caption" />
                                <InputError class="mt-2" :message="sectionForm.errors.video_caption" />

                            </div>

                        </div>

                        <div class="space-y-3">
                            <!-- Подзаголовок -->
                            <div>
                                <LabelInput for="subtitle" :value="t('subtitle')" />
                                <InputText id="subtitle" type="text" v-model="sectionForm.subtitle"
                                           autocomplete="subtitle" />
                                <InputError class="mt-2" :message="sectionForm.errors.subtitle" />
                            </div>

                            <!-- Secondary button + popover -->
                            <div class="pt-2">
                                <div class="text-center text-sm font-semibold opacity-70 mb-1
                                            text-black dark:text-white">
                                    {{ `${t('button')} 2` }}
                                </div>
                                <LabelInput for="secondary_label" :value="t('title')" />
                                <InputText id="secondary_label" type="text"
                                           v-model="sectionForm.secondary_label" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.secondary_label" />

                                <LabelInput for="secondary_url" class="mt-2" value="URL" />
                                <InputText id="secondary_url" type="text"
                                           v-model="sectionForm.secondary_url" />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.secondary_url" />

                                <LabelInput for="secondary_icon" class="mt-2"
                                            :value="t('svg')" />
                                <CodeTextarea
                                    id="secondary_icon"
                                    v-model="sectionForm.secondary_icon"
                                    :rows="6"
                                    wrap="off"
                                    resize="y"
                                    class="font-mono"
                                    placeholder="<svg ...>...</svg>"
                                />
                                <InputError class="mt-2"
                                            :message="sectionForm.errors.secondary_icon" />

                                <div class="mt-1 flex items-center justify-center gap-2">
                                    <div class="text-xs opacity-70 mb-1
                                                text-black dark:text-white">
                                        {{ t('icon') }}
                                    </div>
                                    <div class="w-fit rounded bg-white p-1.5 overflow-hidden
                                                border border-slate-400
                                                bg-white/90 dark:bg-cyan-800/60
                                                text-black dark:text-white"
                                         v-html="renderOrPlaceholder(sectionForm.secondary_icon)" />
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="flex flex-row items-center gap-2 mb-2">
                                    <ActivityCheckbox
                                        v-model="sectionForm.secondary_popover_enabled" />
                                    <LabelCheckbox for="secondary_popover_enabled"
                                                   :text="t('popoverEnabled')"
                                                   class="text-sm h-8 flex items-center" />
                                </div>

                                <div class="grid grid-cols-1 gap-2"
                                     v-if="sectionForm.secondary_popover_enabled">
                                    <div>
                                        <LabelInput for="secondary_popover_title"
                                                    :value="t('title')" />
                                        <InputText id="secondary_popover_title" type="text"
                                                   v-model="sectionForm.secondary_popover_title" />
                                        <InputError class="mt-2"
                                                    :message="sectionForm.errors.secondary_popover_title" />
                                    </div>
                                    <div>
                                        <LabelInput for="secondary_popover_text" :value="t('description')" />
                                        <MetaDescTextarea id="secondary_popover_text"
                                                          v-model="sectionForm.secondary_popover_text" />
                                        <InputError class="mt-2" :message="sectionForm.errors.secondary_popover_text" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end">
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

                <!-- Медиа: постер + видео -->
                <div class="w-full mt-6">
                    <h2 class="pb-2 flex flex-row items-center border-b border-gray-400">
                        <svg class="shrink-0 h-4 w-4 mr-2" viewBox="0 0 24 24">
                            <path class="fill-current text-slate-700 dark:text-slate-300"
                                  d="M 9.601562 0 L 21.601562 0 C 22.921875 0 24 1.34375 24 3 L 24 13.5 C 24 15.15625 22.921875 16.5 21.601562 16.5 L 9.601562 16.5 C 8.277344 16.5 7.199219 15.15625 7.199219 13.5 L 7.199219 3 C 7.199219 1.34375 8.277344 0 9.601562 0 Z M 17.851562 5 C 17.679688 4.6875 17.398438 4.5 17.101562 4.5 C 16.800781 4.5 16.519531 4.6875 16.351562 5 L 14.25 8.9375 L 13.601562 7.921875 C 13.429688 7.65625 13.171875 7.5 12.898438 7.5 C 12.628906 7.5 12.367188 7.65625 12.199219 7.921875 L 9.796875 11.671875 C 9.582031 12.007812 9.539062 12.472656 9.691406 12.863281 C 9.839844 13.25 10.15625 13.5 10.5 13.5 L 20.699219 13.5 C 21.035156 13.5 21.335938 13.269531 21.496094 12.90625 C 21.652344 12.539062 21.632812 12.09375 21.449219 11.75 Z M 12.601562 4.5 C 12.601562 3.671875 12.0625 3 11.398438 3 C 10.738281 3 10.199219 3.671875 10.199219 4.5 C 10.199219 5.328125 10.738281 6 11.398438 6 C 12.0625 6 12.601562 5.328125 12.601562 4.5 Z M 2.398438 6 L 6 6 L 6 19.5 C 6 20.328125 6.535156 21 7.199219 21 L 12 21 C 12.664062 21 13.199219 20.328125 13.199219 19.5 L 13.199219 18 L 19.199219 18 L 19.199219 21 C 19.199219 22.65625 18.125 24 16.800781 24 L 2.398438 24 C 1.078125 24 0 22.65625 0 21 L 0 9 C 0 7.34375 1.078125 6 2.398438 6 Z M 2.699219 9 C 2.371094 9 2.101562 9.335938 2.101562 9.75 L 2.101562 10.5 C 2.101562 10.914062 2.371094 11.25 2.699219 11.25 L 3.300781 11.25 C 3.628906 11.25 3.898438 10.914062 3.898438 10.5 L 3.898438 9.75 C 3.898438 9.335938 3.628906 9 3.300781 9 Z M 2.699219 13.875 C 2.371094 13.875 2.101562 14.210938 2.101562 14.625 L 2.101562 15.375 C 2.101562 15.789062 2.371094 16.125 2.699219 16.125 L 3.300781 16.125 C 3.628906 16.125 3.898438 15.789062 3.898438 15.375 L 3.898438 14.625 C 3.898438 14.210938 3.628906 13.875 3.300781 13.875 Z M 2.699219 18.75 C 2.371094 18.75 2.101562 19.085938 2.101562 19.5 L 2.101562 20.25 C 2.101562 20.664062 2.371094 21 2.699219 21 L 3.300781 21 C 3.628906 21 3.898438 20.664062 3.898438 20.25 L 3.898438 19.5 C 3.898438 19.085938 3.628906 18.75 3.300781 18.75 Z M 15.300781 19.5 L 15.300781 20.25 C 15.300781 20.664062 15.570312 21 15.898438 21 L 16.5 21 C 16.828125 21 17.101562 20.664062 17.101562 20.25 L 17.101562 19.5 C 17.101562 19.085938 16.828125 18.75 16.5 18.75 L 15.898438 18.75 C 15.570312 18.75 15.300781 19.085938 15.300781 19.5 Z M 15.300781 19.5 "></path>
                        </svg>
                        <span class="font-semibold text-md text-slate-700 dark:text-slate-300">
                          {{ t('poster') }} / {{ t('video') }}
                        </span>
                    </h2>

                    <form
                        enctype="multipart/form-data"
                        @submit.prevent="uploadMedia"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-3
                               bg-slate-200 dark:bg-slate-800
                               border border-gray-400 pt-3 px-3">

                        <!-- Постер -->
                        <div class="space-y-3">
                            <div class="text-xs opacity-70 text-black dark:text-white">
                                {{ t('poster') }}
                            </div>
                            <img
                                class="w-full aspect-video object-cover
                                       rounded border border-slate-300
                                       shadow-md shadow-gray-400 dark:shadow-gray-900"
                                :src="mediaPreview.poster || props.quickstart.poster_url"
                                alt=""
                            />
                            <input
                                type="file"
                                accept="image/*"
                                @change="onPickPoster"
                                class="block w-full text-md
                                       text-gray-700 dark:text-gray-100
                                       file:mr-4 file:py-0.5 file:px-2
                                       file:border-0 file:text-sm file:font-semibold
                                       file:bg-violet-600 file:text-white
                                       hover:file:bg-violet-700"
                            />
                            <div v-if="mediaForm.errors.poster" class="form-error">
                                {{ mediaForm.errors.poster }}
                            </div>
                        </div>

                        <!-- Видео -->
                        <div class="space-y-3">
                            <div class="text-xs opacity-70 text-black dark:text-white">
                                {{ t('video') }} (mp4)
                            </div>

                            <video
                                class="w-full aspect-video object-cover
                                       rounded border border-slate-300
                                       shadow-md shadow-gray-400 dark:shadow-gray-900 bg-black"
                                :src="mediaPreview.video || props.quickstart.video_url || undefined"
                                :poster="mediaPreview.poster || props.quickstart.poster_url || undefined"
                                :controls="true"
                            />

                            <input
                                type="file"
                                accept="video/mp4,video/webm,video/ogg"
                                @change="onPickVideo"
                                class="block w-full text-md
                       text-gray-700 dark:text-gray-100
                       file:mr-4 file:py-0.5 file:px-2
                       file:border-0 file:text-sm file:font-semibold
                       file:bg-violet-600 file:text-white
                       hover:file:bg-violet-700"
                            />
                            <div v-if="mediaForm.errors.video" class="form-error">
                                {{ mediaForm.errors.video }}
                            </div>
                        </div>

                        <div class="lg:col-span-2 flex justify-end">
                            <PrimaryButton type="submit"
                                           class="ms-4 mb-0"
                                           :disabled="mediaForm.processing"
                                           :class="{ 'opacity-25': mediaForm.processing }">
                                <template #icon>
                                    <svg class="w-3 h-3 fill-current text-slate-100" viewBox="0 0 24 24">
                                        <path
                                            d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z" />
                                    </svg>
                                </template>
                                {{ t('save') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
