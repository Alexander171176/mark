<script setup>
/** Редактор вложенного дерева. Запросы выполняет только SliderForm.vue. */
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
    storageKey: { type: String, default: 'pulsarcms.slider.builder.create' }
})

const emit = defineEmits(['update:modelValue'])

const items = computed({
    get: () => props.modelValue,
    set: value => emit('update:modelValue', value)
})

const locales = computed(() => {
    const codes = props.availableLocales.map(
        item => typeof item === 'string' ? item : item?.code ?? item?.locale).filter(Boolean)
    return [
        ...new Set([...(codes.length ? codes : [props.currentLocale]), props.currentLocale])
    ]
})
const locale = ref(props.currentLocale)
const selected = ref(0)
const device = ref('desktop')
const urls = new Set()

// Пользовательские настройки раскрытия секций (отдельно для каждого слайдера).
const defaultSections = {
    properties: true,
    translations: true,
    publication: false,
    appearance: false,
    images: true,
    actions: false,
    advantages: false,
    preview: true
}

const readSections = () => {
    try {
        const saved = JSON.parse(localStorage.getItem(props.storageKey) || '{}')
        return {
            ...defaultSections,
            ...(saved && typeof saved === 'object' && !Array.isArray(saved) ? saved : {})
        }
    } catch {
        return { ...defaultSections }
    }
}

const sections = ref(readSections())

const toggleSection = (key, event) => {
    sections.value[key] = event.target.open
    try {
        localStorage.setItem(props.storageKey, JSON.stringify(sections.value))
    } catch {
        // При блокировке localStorage секции всё равно работают.
    }
}

watch(() => props.storageKey, () => {
    sections.value = readSections()
})

const error = path => props.errors[path] || ''

const cls = 'w-full rounded border border-slate-400 dark:border-slate-500 ' +
    'bg-white dark:bg-slate-900 px-3 py-1 text-sm text-slate-800 dark:text-slate-100'

const unwrap = value =>
    Array.isArray(value) ? value : (Array.isArray(value?.data) ? value.data : [])

const plainObject = value =>
    value && typeof value === 'object' && !Array.isArray(value) ? value : {}

const dateTime = value => value ? String(value).slice(0, 16) : ''

const translate = (source, fields) => {
    const result = {}
    const raw = source?.data ?? source ?? []
    const rows =
        Array.isArray(raw) ? raw.map(row => [row?.locale, row]) : Object.entries(plainObject(raw))
    for (const [code, row] of rows) {
        if (!code || !row || typeof row !== 'object') continue
        result[code] = Object.fromEntries(fields.map(field => [field, row[field] ?? '']))
    }
    for (const code of locales.value) result[code] ??= Object.fromEntries(fields.map(field => [field, '']))
    return result
}
const slideFields = ['label', 'title', 'accent', 'description']
const actionFields = ['label', 'title', 'aria_label']
const advantageFields = ['title', 'text']

let nextKey = 0

const uiKey = () => `local-${++nextKey}`

const makeAction = source => ({
    _key: uiKey(), ...(source?.id ? { id: source.id } : {}),
    activity: source?.activity ?? true, is_primary: source?.is_primary ?? false,
    action_type: source?.action_type ?? 'url', action_value: source?.action_value ?? '',
    target: source?.target ?? '_self', style: source?.style ?? 'primary',
    icon: source?.icon ?? '', icon_position: source?.icon_position ?? 'left',
    css_class: source?.css_class ?? '', route_params: plainObject(source?.route_params),
    settings: plainObject(source?.settings),
    translations: translate(source?.translations, actionFields)
})
const makeAdvantage = source => ({
    _key: uiKey(), ...(source?.id ? { id: source.id } : {}),
    activity: source?.activity ?? true, icon_type: source?.icon_type ?? 'none',
    icon: source?.icon ?? '', icon_color: source?.icon_color ?? '',
    style: source?.style ?? 'default', settings: plainObject(source?.settings),
    translations: translate(source?.translations, advantageFields)
})
const makeImage = image => ({
    _key: uiKey(), id: image.id,
    alt: image.alt ?? '', caption: image.caption ?? '',
    desktop_url: image.desktop_url ?? image.webp_url ?? image.image_url ?? image.url ?? '',
    mobile_url: image.mobile_url ?? ''
})
const makeSlide = source => ({
    _key: uiKey(), ...(source?.id ? { id: source.id } : {}),
    activity: source?.activity ?? true, is_main: source?.is_main ?? false,
    status: source?.status ?? 'draft', sort: source?.sort ?? 0,
    published_at: dateTime(source?.published_at), show_from_at: dateTime(source?.show_from_at),
    show_to_at: dateTime(source?.show_to_at),
    background_color: source?.background_color ?? '#0f172a',
    text_color: source?.text_color ?? '#ffffff', accent_color: source?.accent_color ?? '#93c5fd',
    overlay_color: source?.overlay_color ?? '#000000', overlay_opacity: source?.overlay_opacity ?? 0,
    image_position: source?.image_position ?? 'object-center',
    content_position: source?.content_position ?? 'left',
    animation_type: source?.animation_type ?? 'fade-up',
    animation_duration: source?.animation_duration ?? 600,
    animation_delay: source?.animation_delay ?? 0,
    animation_stagger: source?.animation_stagger ?? 0,
    animation_once: source?.animation_once ?? false,
    animation_settings: plainObject(source?.animation_settings),
    settings: plainObject(source?.settings),
    translations: translate(source?.translations, slideFields),
    images: unwrap(source?.images).map(makeImage),
    actions: unwrap(source?.actions).map(makeAction),
    advantages: unwrap(source?.advantages).map(makeAdvantage)
})
const normalize = raw => unwrap(raw).map(makeSlide)
const active = computed(() => items.value[selected.value] ?? null)
const translation = computed(() => active.value?.translations?.[locale.value] ?? null)

const move = (index, offset) => {
    const target = index + offset
    if (target < 0 || target >= items.value.length) return
    const copy = [...items.value]
    ;[copy[index], copy[target]] = [copy[target], copy[index]]
    items.value = copy
    selected.value = target
}

const add = () => {
    items.value = [...items.value, makeSlide()]
    selected.value = items.value.length - 1
}

const release = image => {
    if (image?.preview_url && urls.has(image.preview_url)) {
        URL.revokeObjectURL(image.preview_url)
        urls.delete(image.preview_url)
    }
}

const remove = index => {
    if (!window.confirm('Удалить слайд? Изменения применятся после сохранения.')) return
    for (const image of items.value[index].images) release(image)
    items.value = items.value.filter((_, i) => i !== index)
    selected.value = Math.min(selected.value, Math.max(0, items.value.length - 1))
}

const upload = event => {
    if (!active.value) return
    const files = Array.from(event.target.files ?? [])
    for (const file of files) {
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/avif'].includes(file.type)
            || file.size > 10 * 1024 * 1024) continue
        if (active.value.images.length >= 30) break
        const preview_url = URL.createObjectURL(file)
        urls.add(preview_url)
        active.value.images.push({ _key: uiKey(), file, alt: '', caption: '', preview_url })
    }
    event.target.value = ''
}

const removeImage = index => {
    release(active.value.images[index])
    active.value.images.splice(index, 1)
}

const addAction = () => active.value?.actions.push(makeAction())

const addAdvantage = () => active.value?.advantages.push(makeAdvantage())

const moveNested = (key, index, offset) => {
    const collection = active.value?.[key]
    if (!collection || index + offset < 0 || index + offset >= collection.length) return
        ;
    [collection[index], collection[index + offset]] = [collection[index + offset], collection[index]]
}
// Миниатюра навигации: сначала новый файл, затем сохранённое Desktop-изображение.
const slideThumbnail = slide => {
    const image = slide.images?.[0]
    return image?.preview_url || image?.desktop_url || image?.mobile_url || ''
}

const imageUrl = image => image.preview_url
    || (device.value === 'mobile' ? (image.mobile_url
        || image.desktop_url) : image.desktop_url) || ''

const clean = object =>
    Object.fromEntries(Object.entries(object).filter(([key]) => !key.startsWith('_')))

const serialize = () => items.value.map((slide, index) => ({
    ...clean(slide), sort: index,
    published_at: slide.published_at || null,
    show_from_at: slide.show_from_at || null, show_to_at: slide.show_to_at || null,
    images: slide.images.map(image => image.file
        ? { file: image.file, alt: image.alt ?? '', caption: image.caption ?? '' }
        : { id: image.id }),
    actions: slide.actions.map((action, i) => ({ ...clean(action), sort: i })),
    advantages: slide.advantages.map((advantage, i) => ({ ...clean(advantage), sort: i }))
}))

watch(() => items.value.length, length => {
    if (selected.value >= length) selected.value = Math.max(0, length - 1)
})

watch(locales, codes => {
    if (!codes.includes(locale.value)) locale.value = codes[0] || 'ru'
})

onBeforeUnmount(() => {
    for (const url of urls) URL.revokeObjectURL(url)
    urls.clear()
})

defineExpose({ normalize, serialize })
</script>

<template>
    <section
        class="rounded border border-slate-300 bg-white p-4 shadow-sm
               dark:border-slate-600 dark:bg-slate-800">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">{{ t('slideBuilder') }}</h2>
                <p class="text-xs text-slate-500">
                    {{ t('slidesDescription') }}
                </p>
            </div>
            <button
                type="button"
                class="flex items-center gap-1 btn px-3 py-1 bg-blue-700 rounded shadow-md
                       text-white text-sm font-semibold transition-colors duration-300
                       ease-in-out hover:text-slate-100 focus:text-white hover:bg-blue-600
                       focus:bg-blue-600 focus:outline-none"
                @click="add">
                + {{ t('addSlide') }}
            </button>
        </div>
        <p
            v-if="error('slides')"
            class="mb-3 text-sm text-red-600">
            {{ error('slides') }}
        </p>

        <div v-if="items.length" class="space-y-5">

            <!-- Горизонтальные карточки: миниатюра, подпись и управление -->
            <nav
                class="flex w-full max-w-full gap-3 overflow-x-auto pb-3"
                :aria-label="t('slides')">
                <div
                    v-for="(slide, index) in items"
                    :key="slide.id ?? slide._key"
                    class="flex w-44 shrink-0 flex-col rounded-lg border p-2 transition-colors
                           dark:border-slate-600"
                    :class="selected === index
                        ? 'border-blue-500 bg-blue-50 dark:bg-slate-700'
                        : 'border-slate-500 bg-white dark:bg-slate-800'">
                    <button
                        type="button"
                        class="flex w-full flex-col gap-2 rounded text-left"
                        :aria-current="selected === index ? 'true' : undefined"
                        @click="selected = index">
                        <img
                            v-if="slideThumbnail(slide)"
                            :src="slideThumbnail(slide)"
                            alt=""
                            class="h-24 w-full rounded object-cover" />
                        <span
                            v-else
                            class="flex h-24 w-full items-center justify-center rounded
                                   bg-slate-100 text-2xl text-slate-400 dark:bg-slate-700"
                            aria-hidden="true">
                            ▧
                        </span>
                        <span class="flex w-full items-center gap-2 text-sm">
                            <span class="shrink-0 font-semibold text-blue-600 dark:text-blue-300">
                                {{ index + 1 }}.
                            </span>
                            <span
                                class="min-w-0 flex-1 font-medium text-[9px]
                                       text-slate-800 dark:text-slate-100"
                                  :title="slide.translations?.[locale]?.title || t('untitled')">
                                {{ slide.translations?.[locale]?.title || t('untitled') }}
                            </span>
                        </span>
                    </button>
                    <div class="mt-2 flex items-center justify-center gap-1
                                border-t border-slate-400 pt-2
                                dark:border-slate-500">
                        <button
                            type="button"
                            :title="t('delete')"
                            :aria-label="t('delete')"
                            class="border border-slate-300 rounded px-2 py-1
                                   text-sm text-red-600 hover:bg-red-50
                                   dark:hover:bg-slate-600"
                            @click="remove(index)">×</button>
                        <button
                            type="button"
                            :disabled="index === 0"
                            :title="t('moveLeft')"
                            :aria-label="t('moveLeft')"
                            class="border border-slate-300 rounded px-2 py-1
                                   text-sm hover:bg-slate-200 disabled:opacity-30
                                   dark:hover:bg-slate-600"
                            @click="move(index, -1)">←</button>
                        <button
                            type="button"
                            :disabled="index === items.length - 1"
                            :title="t('moveRight')"
                            :aria-label="t('moveRight')"
                            class="border border-slate-300 rounded px-2 py-1
                                   text-sm hover:bg-slate-200 disabled:opacity-30
                                   dark:hover:bg-slate-600"
                            @click="move(index, 1)">→</button>
                    </div>
                </div>
            </nav>
            <div
                v-if="active"
                class="min-w-0 space-y-5">
                <div
                    class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="font-semibold">
                        {{ t('slide') }}
                        {{ selected + 1 }}
                        <span
                            v-if="active.id"
                            class="text-xs text-slate-500">
                            #{{ active.id }}
                        </span>
                    </h3>
                    <div class="flex gap-2">
                        <button
                            v-for="code in locales"
                            :key="code"
                            type="button"
                            class="rounded border px-3 py-1 text-sm"
                            :class="locale === code
                            ? 'border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-400'
                            : 'border-slate-500 dark:border-slate-400 text-slate-600 dark:text-slate-400'"
                            @click="locale = code">
                            {{ code.toUpperCase() }}
                        </button>
                    </div>
                </div>
                <details
                    :open="sections.preview"
                    @toggle="toggleSection('preview', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800"
                >
                    <summary class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('preview') }}
                    </summary>
                    <div class="mt-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-center">
                                <div class="flex gap-2">
                                    <button
                                        v-for="d in ['desktop','mobile']"
                                        :key="d"
                                        type="button"
                                        class="rounded border border-slate-400 px-2 py-1
                                               text-xs font-semibold"
:class="device === d ? 'border-blue-500 dark:border-blue-400 text-blue-500 dark:text-blue-400' : ''"
                                        @click="device = d">
                                        {{ d }}
                                    </button>
                                </div>
                            </div>
                            <div class="relative mx-auto min-h-64 overflow-hidden rounded"
                                 :class="device === 'mobile' ? 'max-w-[360px]' : 'w-full'"
                                 :style="{
                                     backgroundColor: active.background_color,
                                     color: active.text_color
                                 }">
                                <img
                                    v-if="active.images[0] && imageUrl(active.images[0])"
                                    :src="imageUrl(active.images[0])"
                                    class="absolute inset-0 h-full w-full object-cover"
                                    :class="active.image_position"
                                    alt="" />
                                <div class="absolute inset-0"
                                     :style="{
                                        backgroundColor: active.overlay_color,
                                        opacity: Number(active.overlay_opacity || 0) / 100
                                     }">
                                </div>
                                <div
                                    class="relative flex min-h-64 flex-col justify-center p-6"
                                    :class="active.content_position === 'center'
                                    ? 'items-center text-center'
                                    : active.content_position === 'right'
                                    ? 'items-end text-right' : 'items-start'"
                                >
                                    <span class="text-sm">
                                        {{ translation?.label }}
                                    </span>
                                    <h3 class="text-2xl font-bold">
                                        {{ translation?.title }}
                                    </h3>
                                    <p
                                        class="text-lg"
                                        :style="{ color: active.accent_color }">
                                        {{ translation?.accent }}
                                    </p>
                                    <p class="mt-2 text-sm">{{ translation?.description }}</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    v-for="action in active.actions.filter(item => item.activity)"
                                    :key="action.id ?? action._key"
                                    class="rounded border px-3 py-2 text-xs font-semibold"
                                    :class="action.style === 'primary'
                                    ? 'border-blue-600 bg-blue-600 text-white'
                                    : 'border-current'"
                                >
                                    {{ action.translations?.[locale]?.label || 'Кнопка' }}
                                </span>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <div
                                            v-for="adv in active.advantages.filter(item => item.activity)"
                                            :key="adv.id ?? adv._key"
                                            class="rounded border border-current/30
                                                   px-3 py-2 text-xs"
                                        >
                                            <span
                                                v-if="adv.icon_type !== 'none' && adv.icon"
                                                class="mr-1">
                                                {{ adv.icon }}
                                            </span>
                                            <strong>
                                                {{ adv.translations?.[locale]?.title }}
                                            </strong>
                                            <p>{{ adv.translations?.[locale]?.text }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.images"
                    @toggle="toggleSection('images', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800">
                    <summary
                        class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('images') }} Desktop / Mobile
                    </summary>
                    <div class="mt-4">
                        <div class="space-y-3">
                            <p class="text-xs text-slate-500">
                                {{ t('maximumImagesDesc') }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ t('imageDownloadDesc') }}
                            </p>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <label
                                    class="flex items-center gap-1 btn px-3 py-1 bg-cyan-600
                                           rounded shadow-md text-white text-sm font-semibold
                                           transition-colors duration-300 ease-in-out
                                           hover:text-slate-100 focus:text-white
                                           hover:bg-cyan-700 focus:bg-cyan-700 focus:outline-none">
                                    + {{ t('downloadImage') }} Desktop
                                    <input
                                        class="hidden"
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp,image/avif"
                                        multiple @change="upload" />
                                </label>
                            </div>
                            <div
                                v-for="(img, index) in active.images"
                                :key="img.id ?? img._key"
                                class="p-2 flex items-center gap-3 rounded border
                                       border-dashed border-slate-400 dark:border-slate-600">
                                <img
                                    v-if="imageUrl(img)"
                                    :src="imageUrl(img)"
                                    class="h-16 w-24 rounded object-cover"
                                    alt="" />
                                <span class="min-w-0 flex-1 truncate text-xs">
                                {{ img.file?.name || `${t('image')} #${img.id}` }}
                            </span>
                                <div
                                    v-if="img.file"
                                    class="grid min-w-0 flex-1 gap-1">
                                    <input
                                        v-model="img.alt"
                                        :class="cls"
                                        maxlength="255"
                                        placeholder="Alt" />
                                    <input
                                        v-model="img.caption"
                                        :class="cls"
                                        maxlength="255"
                                        :placeholder="t('caption')" />
                                </div>
                                <button
                                    type="button"
                                    class="flex items-center gap-1 btn px-2 py-0.5 bg-yellow-200
                                           rounded-sm shadow-md text-slate-600 text-sm font-semibold
                                           transition-colors duration-300 ease-in-out
                                           hover:text-slate-700 focus:text-slate-700
                                           hover:bg-yellow-300 focus:bg-yellow-300
                                           focus:outline-none"
                                    @click="removeImage(index)">
                                    {{ t('delete') }}
                                    <svg
                                        class="w-3 h-3 fill-current text-red-400
                                               dark:text-red-600 shrink-0"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" ></path>
                                    </svg>
                                </button>
                                <span
                                    class="text-xs text-red-600">
                                {{ error(`slides.${selected}.images.${index}.file`) }}
                            </span>
                            </div>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.properties"
                    @toggle="toggleSection('properties', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800"
                >
                    <summary
                        class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('keyParameters') }}
                    </summary>
                    <div class="mt-4">
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    v-model="active.activity"
                                    type="checkbox" />
                                {{ t('actively') }}
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    v-model="active.is_main"
                                    type="checkbox" />
                                {{ t('mainSlide') }}
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('status') }}
                                </span>
                                <select
                                    v-model="active.status"
                                    :class="cls">
                                    <option value="draft">{{ t('statusDraft') }}</option>
                                    <option value="published">{{ t('statusPublished') }}</option>
                                    <option value="archived">{{ t('statusArchived') }}</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.publication"
                    @toggle="toggleSection('publication', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800"
                >
                    <summary class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('datetime') }}
                    </summary>
                    <div class="mt-4">
                        <div class="grid gap-3 md:grid-cols-3">
                            <label
                                v-for="field in [
                                    {
                                        key:'published_at',
                                        label: t('publishedAt')
                                    },
                                    {
                                        key:'show_from_at',
                                        label: t('show')+' '+t('from')
                                    },
                                    {
                                        key:'show_to_at',
                                        label: t('show')+' '+t('to')
                                    }
                                ]"
                                :key="field.key"
                                class="text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ field.label }}
                                    </span>
                                <input
                                    v-model="active[field.key]"
                                    type="datetime-local"
                                    :class="cls" />
                            </label>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.translations"
                    @toggle="toggleSection('translations', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800"
                >
                    <summary class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('translations') }}
                    </summary>
                    <div class="mt-4">
                        <div v-if="translation" class="grid gap-3 md:grid-cols-2">
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('tag') }}
                                    <span class="uppercase">
                                        [{{ locale }}]
                                    </span>
                                </span>
                                <input v-model="translation.label" :class="cls" />
                                <span
                                    class="text-red-600">
                                    {{ error(`slides.${selected}.translations.${locale}.label`) }}
                                </span>
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('heading') }}
                                    <span class="uppercase">
                                        [{{ locale }}]
                                    </span>
                                </span>
                                <input v-model="translation.title" :class="cls" />
                                <span class="text-red-600">
                                    {{ error(`slides.${selected}.translations.${locale}.title`) }}
                                </span>
                            </label>
                        </div>
                        <div v-if="translation" class="mt-2">
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('accent') }}
                                    <span class="uppercase">
                                        [{{ locale }}]
                                    </span>
                                </span>
                                <input v-model="translation.accent" :class="cls" />
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('description') }}
                                    <span class="uppercase">
                                        [{{ locale }}]
                                    </span>
                                </span>
                                <textarea
                                    v-model="translation.description"
                                    :class="cls" rows="2" />
                            </label>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.appearance"
                    @toggle="toggleSection('appearance', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800">
                    <summary
                        class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('designAndAnimation') }}
                    </summary>
                    <div class="mt-4">
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="active.animation_once" type="checkbox" />
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('animationOnce') }}
                                </span>
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('animation') }}
                                </span>
                                <select v-model="active.animation_type" :class="cls">
                                    <option
                                        v-for="a in [
                                            'none',
                                            'fade',
                                            'fade-up',
                                            'fade-down',
                                            'fade-left',
                                            'fade-right',
                                            'zoom-in',
                                            'zoom-out',
                                            'slide-up',
                                            'slide-down',
                                            'slide-left',
                                            'slide-right'
                                        ]"
                                        :key="a"
                                        :value="a">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ a }}
                                    </span>
                                    </option>
                                </select>
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('durationMs') }}
                                </span>
                                <input
                                    v-model.number="active.animation_duration"
                                    type="number"
                                    min="0"
                                    :class="cls" />
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('intervalMs') }}
                                </span>
                                <input
                                    v-model.number="active.animation_stagger"
                                    type="number"
                                    min="0"
                                    :class="cls" />
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('latencyMs') }}
                                </span>
                                <input
                                    v-model.number="active.animation_delay"
                                    type="number"
                                    min="0"
                                    :class="cls" />
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('imagePosition') }}
                                </span>
                                <select
                                    v-model="active.image_position"
                                    :class="cls">
                                    <option
                                        v-for="pos in [
                                            'object-center',
                                            'object-top',
                                            'object-bottom',
                                            'object-left',
                                            'object-right',
                                            'object-left-top',
                                            'object-left-bottom',
                                            'object-right-top',
                                            'object-right-bottom'
                                        ]"
                                        :key="pos"
                                        :value="pos">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ pos }}
                                    </span>
                                    </option>
                                </select>
                            </label>
                            <label v-for="field in [
                                {
                                    key:'background_color',
                                    label: t('backgroundShort')
                                },
                                {
                                    key:'accent_color',
                                    label: t('accent')
                                },
                                {
                                    key:'overlay_color',
                                    label: t('blackout')
                                },
                                {
                                    key:'text_color',
                                    label: t('text')
                                }
                            ]"
                           :key="field.key"
                           class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                   {{ field.label }}
                                </span>
                            <input
                                v-model="active[field.key]"
                                type="text"
                                :class="cls"
                                placeholder="#ffffff" />
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('textPosition') }}
                                </span>
                                <select v-model="active.content_position" :class="cls">
                                    <option value="left">{{ t('isLeft') }}</option>
                                    <option value="center">{{ t('centered') }}</option>
                                    <option value="right">{{ t('isRight') }}</option>
                                </select>
                            </label>
                            <label class="space-y-1 text-sm">
                                <span class="font-semibold text-blue-700 dark:text-blue-300">
                                    {{ t('overlayTransparency') }}, %
                                </span>
                                <input
                                    v-model.number="active.overlay_opacity"
                                    type="number" min="0" max="100"
                                    :class="cls" />
                            </label>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.actions"
                    @toggle="toggleSection('actions', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800"
                >
                    <summary
                        class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('buttonsAndActions') }}
                    </summary>
                    <div class="mt-2">
                        <div class="my-2 flex justify-end">
                            <button
                                type="button"
                                class="flex items-center gap-1 btn px-3 py-1 bg-indigo-600
                                           rounded shadow-md text-white text-sm font-semibold
                                           transition-colors duration-300 ease-in-out
                                           hover:text-slate-100 focus:text-white
                                           hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none"
                                @click="addAction">
                                + {{ t('button') }}
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="(action, i) in active.actions"
                                :key="action.id ?? action._key"
                                class="grid gap-2 md:grid-cols-2 p-2 rounded border-dashed
                                       border-2 border-slate-400 dark:border-slate-500">
                                <label class="flex items-center gap-2 text-sm">
                                    <input
                                        v-model="action.activity"
                                        type="checkbox" />
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('actively') }}
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input
                                        v-model="action.is_primary"
                                        type="checkbox" />
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('basics') }}
                                    </span>
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('type') }}
                                    </span>
                                    <select v-model="action.action_type" :class="cls">
                                        <option
                                            v-for="kind in [
                                                'url',
                                                'route',
                                                'form',
                                                'anchor'
                                            ]"
                                            :key="kind"
                                            :value="kind">
                                            {{ kind }}
                                        </option>
                                    </select>
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('value') }} / URL
                                    </span>
                                    <input v-model="action.action_value" :class="cls" />
                                    <span class="text-red-600">
                                        {{ error(`slides.${selected}.actions.${i}.action_value`) }}
                                    </span>
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('open') }}
                                    </span>
                                    <select v-model="action.target" :class="cls">
                                        <option value="_self">{{ t('inThisTab') }}</option>
                                        <option value="_blank">{{ t('InNewTab') }}</option>
                                    </select>
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('caption') }}
                                        </span>
                                        <span class="uppercase">
                                            [{{ locale }}]
                                        </span>
                                    </span>
                                    <input
                                        v-model="action.translations[locale].label"
                                        :class="cls" />
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('heading') }}
                                        <span class="uppercase">
                                            [{{ locale }}]
                                        </span>
                                    </span>
                                    <input
                                        v-model="action.translations[locale].title" :class="cls" />
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        Aria-label
                                        <span class="uppercase">
                                            [{{ locale }}]
                                        </span>
                                    </span>
                                    <input
                                        v-model="action.translations[locale].aria_label"
                                        :class="cls" />
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('class') }}
                                    </span>
                                    <input v-model="action.css_class" :class="cls" />
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('style') }}
                                    </span>
                                    <select v-model="action.style" :class="cls">
                                        <option
                                            v-for="style in [
                                                'primary',
                                                'secondary',
                                                'outline',
                                                'ghost',
                                                'link',
                                                'danger',
                                                'success',
                                                'warning'
                                            ]"
                                            :key="style"
                                            :value="style">
                                            {{ style }}
                                        </option>
                                    </select>
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('icon') }}
                                    </span>
                                    <input v-model="action.icon" :class="cls" />
                                </label>
                                <label class="space-y-1 text-sm">
                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ t('iconPosition') }}
                                    </span>
                                    <select v-model="action.icon_position" :class="cls">
                                        <option value="left">{{ t('isLeft') }}</option>
                                        <option value="right">{{ t('isRight') }}</option>
                                    </select>
                                </label>
                                <div class="py-2 flex items-center justify-start">
                                    <button
                                        type="button"
                                        class="flex items-center gap-1 btn px-2 py-0.5 bg-rose-400
                                           rounded-sm shadow-md text-slate-50 text-sm font-semibold
                                           transition-colors duration-300 ease-in-out
                                           hover:text-white focus:text-slate-700
                                           hover:bg-rose-500 focus:bg-rose-500
                                           focus:outline-none w-fit"
                                        @click="active.actions.splice(i, 1)">
                                        {{ t('deleteButton') }}
                                        <svg class="w-3 h-3 fill-current text-white shrink-0"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" ></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="py-2 flex items-center justify-end gap-3">
                                    <button
                                        type="button"
                                        class="px-2.5 py-0.5 rounded-sm bg-slate-100 dark:bg-slate-900
                                               hover:bg-slate-50 dark:hover:bg-slate-950
                                               border border-slate-400 dark:border-slate-500"
                                        :disabled="i === 0"
                                        @click="moveNested('actions', i, -1)">
                                        ↑
                                    </button>
                                    <button
                                        type="button"
                                        class="px-2.5 py-0.5 rounded-sm bg-slate-100 dark:bg-slate-900
                                               hover:bg-slate-50 dark:hover:bg-slate-950
                                               border border-slate-400 dark:border-slate-500"
                                        :disabled="i === active.actions.length - 1"
                                        @click="moveNested('actions', i, 1)">
                                        ↓
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
                <details
                    :open="sections.advantages"
                    @toggle="toggleSection('advantages', $event)"
                    class="rounded border border-slate-400 bg-white p-3 shadow-sm
                           dark:border-slate-600 dark:bg-slate-800"
                >
                    <summary class="cursor-pointer select-none font-semibold">
                        {{ t('slide') }}: {{ t('triggers') }}
                    </summary>
                    <div class="mt-2">
                        <div class="my-2 flex justify-end">
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    class="flex items-center gap-1 btn px-3 py-1 bg-fuchsia-600
                                           rounded shadow-md text-white text-sm font-semibold
                                           transition-colors duration-300 ease-in-out
                                           hover:text-slate-100 focus:text-white
                                           hover:bg-fuchsia-700 focus:bg-fuchsia-700
                                           focus:outline-none"
                                    @click="addAdvantage">
                                    + {{ t('add') }} {{ t('trigger') }}
                                </button>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="(adv, i) in active.advantages"
                                :key="adv.id ?? adv._key"
                                class="p-2 rounded border-dashed border-2
                                       border-slate-400 dark:border-slate-500">
                                <div class="grid gap-2 md:grid-cols-2">
                                    <label class="flex items-center gap-2 text-sm">
                                        <input
                                            v-model="adv.activity"
                                            type="checkbox" />
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('actively') }}
                                        </span>
                                    </label>
                                    <label class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('style') }}
                                        </span>
                                        <select v-model="adv.style" :class="cls">
                                            <option
                                                v-for="style in [
                                                'default',
                                                'card',
                                                'compact',
                                                'inline',
                                                'minimal',
                                                'outlined'
                                            ]"
                                                :key="style"
                                                :value="style">
                                                {{ style }}
                                            </option>
                                        </select>
                                    </label>
                                </div>
                                <div>
                                    <label class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('heading') }}
                                            <span class="uppercase">
                                                [{{ locale }}]
                                            </span>
                                        </span>
                                        <input
                                            v-model="adv.translations[locale].title"
                                            :class="cls" />
                                    </label>
                                    <label class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('text') }}
                                            <span class="uppercase">
                                                [{{ locale }}]
                                            </span>
                                        </span>
                                        <textarea
                                            v-model="adv.translations[locale].text"
                                            :class="cls" rows="2" />
                                    </label>
                                </div>
                                <div class="grid gap-2 md:grid-cols-2">
                                    <label class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('iconType') }}
                                        </span>
                                        <select v-model="adv.icon_type" :class="cls">
                                            <option
                                                v-for="kind in ['lucide','heroicon','class','none']"
                                                :key="kind"
                                                :value="kind">
                                                {{ kind }}
                                            </option>
                                        </select>
                                    </label>
                                    <label class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('icon') }}
                                        </span>
                                        <input
                                            v-model="adv.icon"
                                            :class="cls" />
                                    </label>
                                    <label class="space-y-1 text-sm">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300">
                                            {{ t('iconColor') }}
                                        </span>
                                        <input
                                            v-model="adv.icon_color"
                                            :class="cls"
                                            placeholder="#ffffff" />
                                    </label>
                                </div>
                                <div class="grid gap-2 md:grid-cols-2 p-2">
                                    <div class="py-2 flex items-center justify-start">
                                        <button
                                            type="button"
                                            class="flex items-center gap-1 btn px-2 py-0.5
                                                   bg-red-400 rounded-sm shadow-md text-slate-50
                                                   text-sm font-semibold transition-colors
                                                   duration-300 ease-in-out hover:text-white
                                                   focus:text-slate-700 hover:bg-red-500
                                                   focus:bg-red-500 focus:outline-none w-fit"
                                            @click="active.advantages.splice(i, 1)">
                                            {{ t('delete') }} {{ t('trigger') }}
                                            <svg
                                                class="w-3 h-3 fill-current text-white shrink-0"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z "></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="py-2 flex items-center justify-end gap-3">
                                        <button
                                            type="button"
                                            class="px-2.5 py-0.5 rounded-sm bg-slate-100
                                                   dark:bg-slate-900 hover:bg-slate-50
                                                   dark:hover:bg-slate-950 border border-slate-400
                                                   dark:border-slate-500"
                                            :disabled="i === 0"
                                            @click="moveNested('advantages', i, -1)">
                                            ↑
                                        </button>
                                        <button
                                            type="button"
                                            class="px-2.5 py-0.5 rounded-sm bg-slate-100
                                                   dark:bg-slate-900 hover:bg-slate-50
                                                   dark:hover:bg-slate-950 border border-slate-400
                                                   dark:border-slate-500"
                                            :disabled="i === active.advantages.length - 1"
                                            @click="moveNested('advantages', i, 1)">
                                            ↓
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </div>
        <p
            v-else
            class="rounded border border-dashed p-6 text-center
                   text-sm text-slate-500 dark:border-slate-600">
            {{ t('noData') }}
        </p>
    </section>
</template>
