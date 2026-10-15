<script setup>
/**
 * Общая форма создания/редактирования универсального слайдера.
 * Вложенное дерево сохраняется одним запросом через SliderBuilderService.
 */
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import SliderSlideBuilder from './SliderSlideBuilder.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

const props = defineProps({
    slider: { type: Object, default: null },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
})

const { t } = useI18n()
const toast = useToast()
const record = computed(() => props.slider?.data ?? props.slider ?? {})
const editing = computed(() => Boolean(record.value.id))
const initial = record.value
const builder = ref(null)
const activeLocale = ref(props.currentLocale || 'ru')
const slidesInitialized = ref(false)

// Состояние секций сохраняем отдельно для каждого слайдера.
const storageKey = computed(() => `pulsarcms.slider.form.${record.value.id ?? 'create'}`)
const defaultSections = {
    main: true,
    translations: true,
    publication: false,
    swiper: false,
    advanced: false
}

const readSections = () => {
    try {
        const saved = JSON.parse(localStorage.getItem(storageKey.value) || '{}')
        return { ...defaultSections, ...(saved && typeof saved === 'object' && !Array.isArray(saved) ? saved : {}) }
    } catch {
        return { ...defaultSections }
    }
}

const sections = ref(readSections())

const toggleSection = (key, event) => {
    sections.value[key] = event.target.open
    try {
        localStorage.setItem(storageKey.value, JSON.stringify(sections.value))
    } catch {
        // Редактор продолжит работать, если хранилище недоступно.
    }
}

watch(storageKey, () => {
    sections.value = readSections()
})

const localeCodes = computed(() => {
    const codes = props.availableLocales
        .map(item => typeof item === 'string' ? item : (item?.code ?? item?.locale))
        .filter(Boolean)
    return [...new Set([...(codes.length ? codes : []), props.currentLocale || 'ru'])]
})

const makeTranslation = () => ({
    title: '', subtitle: '', description: '', meta_title: '', meta_description: ''
})

/** Поддерживает ResourceCollection {data: []}, массив и словарь {ru: {...}}. */
const normalizeTranslations = (source) => {
    const result = {}
    const raw = source?.data ?? source ?? []
    const fields = Object.keys(makeTranslation())

    if (Array.isArray(raw)) {
        for (const item of raw) {
            if (!item?.locale) continue
            result[item.locale] = Object.fromEntries(fields.map(key => [key, item[key] ?? '']))
        }
    } else if (raw && typeof raw === 'object') {
        for (const [locale, item] of Object.entries(raw)) {
            if (!item || typeof item !== 'object' || Array.isArray(item)) continue
            result[locale] = Object.fromEntries(fields.map(key => [key, item[key] ?? '']))
        }
    }

    for (const code of localeCodes.value) result[code] ??= makeTranslation()
    return result
}

const unwrapCollection = source => Array.isArray(source)
    ? source
    : (Array.isArray(source?.data) ? source.data : [])

/** datetime-local принимает локальную строку YYYY-MM-DDTHH:mm. */
const dateTime = value => {
    if (!value) return ''
    const text = String(value)
    return text.length >= 16 ? text.slice(0, 16) : text
}

const form = useForm({
    code: initial.code ?? '',
    type: initial.type ?? 'hero',
    status: initial.status ?? 'draft',
    sort: Number(initial.sort ?? 0),
    activity: Boolean(initial.activity ?? true),
    published_at: dateTime(initial.published_at),
    show_from_at: dateTime(initial.show_from_at),
    show_to_at: dateTime(initial.show_to_at),
    autoplay_delay: initial.autoplay_delay ?? '',
    autoplay_disable_on_interaction: Boolean(initial.autoplay_disable_on_interaction ?? false),
    pause_on_hover: Boolean(initial.pause_on_hover ?? true),
    effect: initial.effect ?? 'slide',
    speed: Number(initial.speed ?? 700),
    effect_options: initial.effect_options ?? {},
    loop: Boolean(initial.loop ?? false),
    keyboard: Boolean(initial.keyboard ?? true),
    allow_touch_move: Boolean(initial.allow_touch_move ?? true),
    grab_cursor: Boolean(initial.grab_cursor ?? true),
    slides_per_view: Number(initial.slides_per_view ?? 1),
    space_between: Number(initial.space_between ?? 0),
    auto_height: Boolean(initial.auto_height ?? false),
    breakpoints: initial.breakpoints ?? {},
    show_navigation: Boolean(initial.show_navigation ?? true),
    show_pagination: Boolean(initial.show_pagination ?? true),
    settings: initial.settings ?? {},
    translations: normalizeTranslations(initial.translations),
    slides: []
})

/**
 * Нормализацию пока предоставляет существующий SliderSlideBuilder.
 * Инициализируем после появления ref, без @vue:mounted и без потери слайдов.
 * При переработке Builder перенесём normalize в общий JS-модуль.
 */
const initializeSlides = () => {
    if (slidesInitialized.value || !builder.value?.normalize) return
    form.slides = builder.value.normalize(unwrapCollection(initial.slides))
    slidesInitialized.value = true
}
watch(builder, initializeSlides, { flush: 'post' })
onMounted(() => nextTick(initializeSlides))

const currentTranslation = computed(() => {
    form.translations[activeLocale.value] ??= makeTranslation()
    return form.translations[activeLocale.value]
})

const jsonFields = ['effect_options', 'breakpoints', 'settings']
const jsonText = ref(Object.fromEntries(jsonFields.map(key => [
    key, JSON.stringify(form[key] ?? {}, null, 2)
])))
const jsonErrors = ref({})

const parseJsonFields = () => {
    jsonErrors.value = {}

    for (const key of jsonFields) {
        let parsed

        try {
            parsed = JSON.parse(jsonText.value[key]?.trim() || '{}')
        } catch (exception) {
            jsonErrors.value[key] = `Неверный JSON: ${exception.message}`
            continue
        }

        if (parsed === null || Array.isArray(parsed) || typeof parsed !== 'object') {
            jsonErrors.value[key] = 'Ожидается JSON-объект'
            continue
        }

        form[key] = parsed
    }

    return Object.keys(jsonErrors.value).length === 0
}

const submit = () => {
    if (form.processing) return
    form.clearErrors()
    if (!parseJsonFields()) {
        toast.error('Проверьте JSON-настройки слайдера.')
        return
    }
    initializeSlides()
    if (!slidesInitialized.value || !builder.value?.serialize) {
        toast.error('Редактор слайдов ещё не готов. Повторите попытку.')
        return
    }

    // Включаем полное дерево: иначе [] в BuilderService удалит существующие слайды.
    form.slides = builder.value.serialize()

    // Для файлов используем POST + method spoofing; Laravel обработает UploadedFile.
    form.transform(data => ({
        ...data,
        autoplay_delay: data.autoplay_delay === '' ? null : Number(data.autoplay_delay),
        published_at: data.published_at || null,
        show_from_at: data.show_from_at || null,
        show_to_at: data.show_to_at || null,
        ...(editing.value ? { _method: 'PUT' } : {})
    }))

    const options = {
        forceFormData: true,
        preserveScroll: true,
        onError: errors => {
            toast.error(Object.values(errors)[0] || 'Проверьте заполнение полей.')
        }
    }

    if (editing.value) {
        form.post(route('admin.sliders.update', { slider: initial.id }), options)
    } else {
        form.post(route('admin.sliders.store'), options)
    }
}

const fieldClass = 'w-full rounded border border-slate-400 dark:border-slate-500 ' +
    'bg-white dark:bg-slate-900 px-3 py-1 text-sm text-slate-800 dark:text-slate-100'
const error = key => form.errors[key] || ''
</script>

<template>
    <AdminLayout
        :title="editing ? t('editSlider') : t('addSlider')">
        <template #header>
            <TitlePage>
                {{ editing ? t('editSlider') : t('addSlider') }}
            </TitlePage>
        </template>
        <div
            class="px-2 py-2 w-full max-w-12xl mx-auto"
        >
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >

                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <a :href="route('admin.sliders.index')"
                       class="flex items-center btn px-3 py-1 rounded shadow-md
                             bg-sky-600 text-white text-sm font-semibold
                             transition-colors duration-300 ease-in-out
                             hover:bg-sky-700 focus:bg-sky-700 focus:outline-none">
                        ← {{ t('back') }}
                    </a>
                    <span v-if="editing" class="text-sm text-slate-500">
                        ID: {{ record.id }} · {{ record.code }}
                    </span>
                </div>

                <form class="w-full space-y-5" @submit.prevent="submit">
                    <SliderSlideBuilder
                        ref="builder"
                        v-model="form.slides"
                        :errors="form.errors"
                        :current-locale="currentLocale"
                        :available-locales="availableLocales"
                        :storage-key="`pulsarcms.slider.builder.${record.id ?? 'create'}`"
                    />
                    <section
                        class="rounded border border-slate-300 bg-white p-4 shadow-sm
                               dark:border-slate-600 dark:bg-slate-800 space-y-5">
                        <details
                            :open="sections.main"
                            @toggle="toggleSection('main', $event)"
                            class="rounded border border-slate-400 bg-white p-3 shadow-sm
                               dark:border-slate-600 dark:bg-slate-800"
                        >
                            <summary
                                class="cursor-pointer select-none font-semibold text-md">
                            <span class="text-slate-900 dark:text-slate-100 tracking-wide">
                                {{ t('keyParameters') }}
                            </span>
                            </summary>
                            <div class="mt-2 flex flex-col xs:flex-row
                                    items-center justify-between">
                                <label class="flex items-center gap-2 text-sm">
                                    <input
                                        v-model="form.activity"
                                        type="checkbox" />
                                    <span class="font-semibold text-blue-700 dark:text-blue-300 mr-1">
                                    {{ t('activity') }}
                                </span>
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300 mr-2">
                                    {{ t('sort') }}
                                </span>
                                    <input
                                        class="max-w-20"
                                        v-model.number="form.sort"
                                        type="number"
                                        min="0"
                                        :class="fieldClass" />
                                </label>
                            </div>
                            <div class="mt-2 grid gap-4 md:grid-cols-3">
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('code') }} *
                                </span>
                                    <input
                                        v-model="form.code"
                                        :class="fieldClass" maxlength="150"
                                        required
                                        placeholder="home-hero" />
                                    <span
                                        class="text-red-500">
                                    {{ error('code') }}
                                </span>
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('type') }} *
                                </span>
                                    <select
                                        v-model="form.type"
                                        :class="fieldClass">
                                        <option value="hero">
                                            Hero
                                        </option>
                                        <option value="banner">
                                            {{ t('banner') }}
                                        </option>
                                        <option value="carousel">
                                            {{ t('carousel') }}
                                        </option>
                                    </select>
                                    <span class="text-red-500">
                                    {{ error('type') }}
                                </span>
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('status') }} *
                                </span>
                                    <select
                                        v-model="form.status"
                                        :class="fieldClass">
                                        <option value="draft">
                                            {{ t('statusDraft') }}
                                        </option>
                                        <option value="published">
                                            {{ t('statusPublished') }}
                                        </option>
                                        <option value="archived">
                                            {{ t('statusArchived') }}
                                        </option>
                                    </select>
                                </label>
                            </div>
                        </details>
                        <details
                            :open="sections.publication"
                            @toggle="toggleSection('publication', $event)"
                            class="rounded border border-slate-400 bg-white p-3 shadow-sm
                               dark:border-slate-600 dark:bg-slate-800"
                        >
                            <summary
                                class="cursor-pointer select-none font-semibold text-md">
                            <span class="text-slate-900 dark:text-slate-100 tracking-wide">
                                {{ t('datetime') }}
                            </span>
                            </summary>
                            <div class="mt-2 grid gap-4 md:grid-cols-3">
                                <label
                                    v-for="item in [
                                    {
                                        key: 'published_at',
                                        label: t('publishedAt')
                                    },
                                    {
                                        key: 'show_from_at',
                                        label: t('show')+' '+t('from')
                                    },
                                    {
                                        key: 'show_to_at',
                                        label: t('show')+' '+t('to')
                                    }
                                ]"
                                    :key="item.key"
                                    class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ item.label }}
                                        </span>
                                    <input
                                        v-model="form[item.key]"
                                        type="datetime-local"
                                        :class="fieldClass" />
                                    <span class="text-red-500">
                                    {{ error(item.key) }}
                                </span>
                                </label>
                            </div>
                        </details>
                        <details
                            :open="sections.translations"
                            @toggle="toggleSection('translations', $event)"
                            class="rounded border border-slate-400 bg-white p-3 shadow-sm
                                   dark:border-slate-600 dark:bg-slate-800"
                        >
                            <summary
                                class="cursor-pointer select-none font-semibold text-md">
                            <span class="text-slate-900 dark:text-slate-100 tracking-wide">
                                {{ t('translations') }}
                            </span>
                            </summary>
                            <TranslationTabs
                                class="mt-2"
                                v-model="activeLocale"
                                :translations="form.translations"
                                :available-locales="availableLocales"
                                :make-translation="makeTranslation"
                                @update:translations="form.translations = $event"
                            />
                            <div class="mt-2 grid gap-4 md:grid-cols-2">
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('title') }}
                                    <span class="uppercase">
                                        [{{ activeLocale }}]
                                    </span>
                                </span>
                                    <input
                                        v-model="currentTranslation.title"
                                        :class="fieldClass"
                                        maxlength="255" />
                                    <span
                                        class="text-red-500">
                                    {{ error(`translations.${activeLocale}.title`) }}
                                </span>
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('subtitle') }}
                                    <span class="uppercase">
                                        [{{ activeLocale }}]
                                    </span>
                                </span>
                                    <input
                                        v-model="currentTranslation.subtitle"
                                        :class="fieldClass"
                                        maxlength="255" />
                                </label>
                                <label class="space-y-1 text-sm md:col-span-2">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('description') }}
                                    <span class="uppercase">
                                        [{{ activeLocale }}]
                                    </span>
                                </span>
                                    <textarea
                                        v-model="currentTranslation.description"
                                        :class="fieldClass"
                                        rows="3" />
                                </label>
                            </div>
                            <div class="mt-2">
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        Meta title
                                        <span class="uppercase">
                                            [{{ activeLocale }}]
                                        </span>
                                    </span>
                                    <input
                                        v-model="currentTranslation.meta_title"
                                        :class="fieldClass"
                                        maxlength="255" />
                                </label>
                            </div>
                            <div class="mt-2">
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        Meta description
                                        <span class="uppercase">
                                            [{{ activeLocale }}]
                                        </span>
                                    </span>
                                    <textarea
                                        v-model="currentTranslation.meta_description"
                                        :class="fieldClass"
                                        rows="2" />
                                </label>
                            </div>
                            <p
                                v-if="error('translations')"
                                class="mt-2 text-sm text-red-500">
                                {{ error('translations') }}
                            </p>
                        </details>
                        <details
                            :open="sections.swiper"
                            @toggle="toggleSection('swiper', $event)"
                            class="rounded border border-slate-400 bg-white p-3 shadow-sm
                                   dark:border-slate-600 dark:bg-slate-800"
                        >
                            <summary
                                class="cursor-pointer select-none font-semibold text-md">
                            <span class="text-slate-900 dark:text-slate-100 tracking-wide">
                                {{ t('settings') }} Swiper
                            </span>
                            </summary>
                            <div class="mt-2 grid gap-4 md:grid-cols-3">
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('effect') }}
                                </span>
                                    <select
                                        v-model="form.effect"
                                        :class="fieldClass">
                                        <option
                                            v-for="effect in [
                                            'slide',
                                            'fade',
                                            'cube',
                                            'coverflow',
                                            'flip',
                                            'creative'
                                        ]"
                                            :key="effect" :value="effect">{{ effect }}
                                        </option>
                                    </select>
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('animationSpeed') }}
                                </span>
                                    <input
                                        v-model.number="form.speed"
                                        type="number" min="0"
                                        :class="fieldClass" />
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('autoScroll') }} (мс; пусто — выключена)
                                </span>
                                    <input
                                        v-model="form.autoplay_delay"
                                        type="number" min="1"
                                        :class="fieldClass" />
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('slidesScreen') }}
                                </span>
                                    <input
                                        v-model.number="form.slides_per_view"
                                        type="number"
                                        min="0.01"
                                        max="999.99"
                                        step="0.01"
                                        :class="fieldClass" />
                                </label>
                                <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('slideSpacing') }}
                                </span>
                                    <input
                                        v-model.number="form.space_between"
                                        type="number"
                                        min="0"
                                        :class="fieldClass" />
                                </label>
                            </div>
                            <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <label
                                    v-for="item in [
                                    {
                                        key:'loop',
                                        label: t('cycle')
                                    },
                                    {
                                        key:'keyboard',
                                        label: t('keyboardControls')
                                    },
                                    {
                                        key:'allow_touch_move',
                                        label: t('DragWithMouseTouch')
                                    },
                                    {
                                        key:'grab_cursor',
                                        label: t('captureCursor')
                                    },
                                    {
                                        key:'auto_height',
                                        label: t('automaticHeight')
                                    },
                                    {
                                        key:'pause_on_hover',
                                        label: t('pauseOnHover')
                                    },
                                    {
                                        key:'autoplay_disable_on_interaction',
                                        label: t('disableAutoScrolling')
                                    },
                                    {
                                        key:'show_navigation',
                                        label: t('showArrows')
                                    },
                                    {
                                        key:'show_pagination',
                                        label: t('showPagination')
                                    }
                                ]"
                                    :key="item.key"
                                    class="flex items-center gap-2 text-sm">
                                    <input
                                        v-model="form[item.key]"
                                        type="checkbox" />
                                    {{ item.label }}
                                </label>
                            </div>
                        </details>
                        <details
                            :open="sections.advanced"
                            @toggle="toggleSection('advanced', $event)"
                            class="rounded border border-slate-400 bg-white p-3 shadow-sm
                               dark:border-slate-600 dark:bg-slate-800"
                        >
                            <summary
                                class="cursor-pointer select-none font-semibold text-md">
                            <span class="text-slate-900 dark:text-slate-100 tracking-wide">
                                {{ t('advancedSettings') }}
                            </span>
                            </summary>
                            <p class="mb-4 text-xs text-slate-500">
                                {{ t('jsonObjectsDesc') }}
                            </p>
                            <div class="mt-2 grid gap-4 lg:grid-cols-3">
                                <label
                                    v-for="item in [
                                    {
                                        key:'effect_options',
                                        label: t('effectParameters')
                                    },
                                    {
                                        key:'breakpoints',
                                        label: t('adaptivePoints')
                                    },
                                    {
                                        key:'settings',
                                        label: t('additionalSettings')
                                    }
                                ]"
                                    :key="item.key"
                                    class="space-y-1 text-sm">
                                    {{ item.label }}
                                    <textarea
                                        v-model="jsonText[item.key]"
                                        rows="7"
                                        spellcheck="false"
                                        :class="fieldClass + ' font-mono'" />
                                    <span
                                        class="text-red-500">
                                    {{ jsonErrors[item.key] || error(item.key) }}
                                </span>
                                </label>
                            </div>
                        </details>
                        <div
                            v-if="Object.keys(form.errors).length"
                            class="rounded border border-rose-300 p-3 text-sm text-rose-600">
                            {{ t('errorsFormDesc') }}
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <a :href="route('admin.sliders.index')"
                               class="flex items-center btn px-3 py-1 rounded shadow-md
                                     bg-sky-600 text-white text-sm font-semibold
                                     transition-colors duration-300 ease-in-out
                                     hover:bg-sky-700 focus:bg-sky-700 focus:outline-none">
                                ← {{ t('back') }}
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex items-center gap-2 btn px-3 py-1 bg-teal-500 shadow-md
                                       text-white text-sm font-semibold transition-colors
                                       duration-300 ease-in-out hover:bg-teal-600 focus:bg-teal-600
                                       focus:outline-none ms-4 mb-0 rounded">
                                <svg
                                    class="w-3 h-3 fill-current text-slate-100"
                                    viewBox="0 0 16 16">
                                    <path d="M14.3 2.3L5 11.6 1.7 8.3
                                               c-.4-.4-1-.4-1.4 0
                                               -.4.4-.4 1 0 1.4l4 4
                                               c.2.2.4.3.7.3
                                               .3 0 .5-.1.7-.3l10-10
                                               c.4-.4.4-1 0-1.4
                                               -.4-.4-1-.4-1.4 0z"></path>
                                </svg>
            <span>
              {{ form.processing ? t('saving') : (editing ? t('saveChanges') : t('createSlider')) }}
            </span>
                            </button>
                        </div>
                    </section>
                </form>

            </div>
        </div>
    </AdminLayout>
</template>
