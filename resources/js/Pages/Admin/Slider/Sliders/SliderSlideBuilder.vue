<script setup>
/** Вложенный редактор слайдов. Не выполняет запросов: единый submit — в SliderForm. */
import { computed, onBeforeUnmount, ref, watch } from 'vue'

const props = defineProps({
    modelValue: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue'])
const items = computed({ get: () => props.modelValue, set: value => emit('update:modelValue', value) })
const locales = computed(() => (props.availableLocales.length ? props.availableLocales : [props.currentLocale]).map(x => typeof x === 'string' ? x : (x.code ?? x.locale)).filter(Boolean))
const locale = ref(props.currentLocale)
const selected = ref(0)
const device = ref('desktop')
const urls = new Map()
const error = path => props.errors[path] || ''
const cls = 'w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-white'
const unwrap = value => Array.isArray(value) ? value : (value?.data ?? [])
const translations = (source, fields) => {
    const result = {}
    const raw = source?.data ?? source ?? []
    if (Array.isArray(raw)) {
        for (const row of raw) {
            if (row?.locale) result[row.locale] = Object.fromEntries(fields.map(k => [k, row[k] ?? '']))
        }
    } else if (raw && typeof raw === 'object') {
        for (const [key, row] of Object.entries(raw)) {
            if (row && typeof row === 'object') result[key] = Object.fromEntries(fields.map(k => [k, row[k] ?? '']))
        }
    }
    for (const code of locales.value) result[code] ??= Object.fromEntries(fields.map(k => [k, '']))
    return result
}
const slideFields = ['label', 'title', 'accent', 'description']
const actionFields = ['label', 'title', 'aria_label']
const advantageFields = ['title', 'text']
const makeSlide = source => ({
    ...(source?.id ? { id: source.id } : {}),
    activity: source?.activity ?? true,
    is_main: source?.is_main ?? false,
    status: source?.status ?? 'draft',
    sort: source?.sort ?? 0,
    published_at: source?.published_at ?? null,
    show_from_at: source?.show_from_at ?? null,
    show_to_at: source?.show_to_at ?? null,
    background_color: source?.background_color ?? '#0f172a',
    text_color: source?.text_color ?? '#ffffff',
    accent_color: source?.accent_color ?? '#93c5fd',
    overlay_color: source?.overlay_color ?? '#000000',
    overlay_opacity: source?.overlay_opacity ?? 0,
    image_position: source?.image_position ?? 'object-center',
    content_position: source?.content_position ?? 'left',
    animation_type: source?.animation_type ?? 'fade-up',
    animation_duration: source?.animation_duration ?? 600,
    animation_delay: source?.animation_delay ?? 0,
    animation_stagger: source?.animation_stagger ?? 0,
    animation_once: source?.animation_once ?? false,
    animation_settings: source?.animation_settings ?? {},
    settings: source?.settings ?? {},
    translations: translations(source?.translations, slideFields),
    images: unwrap(source?.images).map(img => ({
        id: img.id,
        alt: img.alt ?? '',
        caption: img.caption ?? '',
        desktop_url: img.desktop_url ?? img.webp_url ?? img.image_url ?? '',
        mobile_url: img.mobile_url ?? ''
    })),
    actions: unwrap(source?.actions).map(action => ({
        ...(action.id ? { id: action.id } : {}),
        activity: action.activity ?? true,
        is_primary: action.is_primary ?? false,
        action_type: action.action_type ?? 'url',
        action_value: action.action_value ?? '',
        target: action.target ?? '_self',
        style: action.style ?? 'primary',
        icon: action.icon ?? '',
        icon_position: action.icon_position ?? 'left',
        route_params: action.route_params ?? {},
        settings: action.settings ?? {},
        translations: translations(action.translations, actionFields),
    })),
    advantages: unwrap(source?.advantages).map(adv => ({
        ...(adv.id ? { id: adv.id } : {}),
        activity: adv.activity ?? true,
        icon_type: adv.icon_type ?? 'lucide',
        icon: adv.icon ?? '',
        icon_color: adv.icon_color ?? '',
        style: adv.style ?? 'default',
        settings: adv.settings ?? {},
        translations: translations(adv.translations, advantageFields),
    })),
})
// Нормализация выполняется только при первом открытии родительской формы.
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
    items.value = [...items.value, makeSlide()];
    selected.value = items.value.length - 1
}
const remove = index => {
    if (!window.confirm('Удалить слайд из конструктора? Изменения применятся после сохранения.')) return
    const removed = items.value[index]
    for (const img of removed.images) release(img)
    items.value = items.value.filter((_, i) => i !== index)
    selected.value = Math.min(selected.value, Math.max(0, items.value.length - 1))
}
const release = image => {
    if (image?.preview_url && urls.has(image.preview_url)) {
        URL.revokeObjectURL(image.preview_url);
        urls.delete(image.preview_url)
    }
}
const upload = event => {
    const file = event.target.files?.[0]
    if (!file || !active.value) return
    const preview_url = URL.createObjectURL(file)
    urls.set(preview_url, true)
    active.value.images.push({ file, alt: '', caption: '', preview_url })
    event.target.value = ''
}
const removeImage = index => {
    release(active.value.images[index]);
    active.value.images.splice(index, 1)
}
const addAction = () => active.value.actions.push({
    activity: true,
    is_primary: false,
    action_type: 'url',
    action_value: '',
    target: '_self',
    style: 'primary',
    icon: '',
    icon_position: 'left',
    route_params: {},
    settings: {},
    translations: translations(null, actionFields)
})
const addAdvantage = () => active.value.advantages.push({
    activity: true,
    icon_type: 'lucide',
    icon: '',
    icon_color: '',
    style: 'default',
    settings: {},
    translations: translations(null, advantageFields)
})
const imageUrl = img => img.preview_url || (device.value === 'mobile' ? (img.mobile_url || img.desktop_url) : img.desktop_url) || ''
watch(() => items.value.length, length => {
    if (selected.value >= length) selected.value = Math.max(0, length - 1)
})
onBeforeUnmount(() => {
    for (const url of urls.keys()) URL.revokeObjectURL(url);
    urls.clear()
})
// Отделяем отображаемые поля (URL/preview) от API-полей и нормализуем порядок.
const serialize = () => items.value.map((slide, index) => ({
    ...Object.fromEntries(Object.entries(slide).filter(([k]) => !['images', 'actions', 'advantages'].includes(k))),
    sort: index,
    images: slide.images.map(img => img.file ? {
        file: img.file,
        alt: img.alt ?? '',
        caption: img.caption ?? ''
    } : { id: img.id }),
    actions: slide.actions.map((action, i) => ({ ...action, sort: i })),
    advantages: slide.advantages.map((advantage, i) => ({ ...advantage, sort: i })),
}))
defineExpose({ serialize, normalize })
</script>

<template>
    <section class="rounded border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div><h2 class="text-lg font-bold">Конструктор слайдов</h2>
                <p class="text-xs text-slate-500">Слайды, изображения и вложенные элементы сохраняются вместе со
                    слайдером.</p></div>
            <button type="button" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white" @click="add">+
                Добавить слайд
            </button>
        </div>
        <p v-if="error('slides')" class="mb-3 text-sm text-red-600">{{ error('slides') }}</p>
        <div v-if="items.length" class="grid gap-5 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside class="space-y-2">
                <div v-for="(slide, index) in items" :key="slide.id ?? `new-${index}`"
                     class="flex items-center gap-1 rounded border p-1 dark:border-slate-600"
                     :class="selected === index ? 'border-blue-500 bg-blue-50 dark:bg-slate-700' : ''">
                    <button type="button" class="min-w-0 flex-1 truncate px-2 py-2 text-left text-sm"
                            @click="selected = index">{{ index + 1 }}.
                        {{ slide.translations?.[locale]?.title || 'Без названия' }}
                    </button>
                    <button type="button" :disabled="index === 0" title="Вверх" class="px-1 disabled:opacity-30"
                            @click="move(index, -1)">↑
                    </button>
                    <button type="button" :disabled="index === items.length - 1" title="Вниз"
                            class="px-1 disabled:opacity-30" @click="move(index, 1)">↓
                    </button>
                    <button type="button" title="Удалить" class="px-1 text-red-600" @click="remove(index)">×</button>
                </div>
            </aside>
            <div v-if="active" class="min-w-0 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3"><h3 class="font-semibold">Слайд
                    {{ selected + 1 }} <span v-if="active.id" class="text-xs text-slate-500">#{{ active.id }}</span>
                </h3>
                    <div class="flex gap-2">
                        <button v-for="code in locales" :key="code" type="button"
                                class="rounded border px-3 py-1 text-sm"
                                :class="locale === code ? 'border-blue-500 text-blue-600' : 'dark:border-slate-600'"
                                @click="locale = code">{{ code.toUpperCase() }}
                        </button>
                    </div>
                </div>
                <div class="grid gap-3 md:grid-cols-3"><label class="text-sm">Статус<select v-model="active.status"
                                                                                            :class="cls">
                    <option value="draft">Черновик</option>
                    <option value="published">Опубликован</option>
                    <option value="archived">Архив</option>
                </select></label><label class="flex items-center gap-2 text-sm"><input v-model="active.activity"
                                                                                       type="checkbox" />
                    Активен</label><label class="flex items-center gap-2 text-sm"><input v-model="active.is_main"
                                                                                         type="checkbox" /> Главный
                    слайд</label></div>
                <div v-if="translation" class="grid gap-3 md:grid-cols-2">
                    <label class="text-sm">Метка<input v-model="translation.label" :class="cls" /><span
                        class="text-red-600">{{ error(`slides.${selected}.translations.${locale}.label`)
                        }}</span></label>
                    <label class="text-sm">Заголовок<input v-model="translation.title" :class="cls" /><span
                        class="text-red-600">{{ error(`slides.${selected}.translations.${locale}.title`)
                        }}</span></label>
                    <label class="text-sm">Акцент<input v-model="translation.accent" :class="cls" /></label>
                    <label class="text-sm">Описание<textarea v-model="translation.description" :class="cls" rows="2" /></label>
                </div>
                <div class="grid gap-3 md:grid-cols-3"><label
                    v-for="field in [{key:'published_at',label:'Публикация'}, {key:'show_from_at',label:'Показывать с'}, {key:'show_to_at',label:'Показывать до'}]"
                    :key="field.key" class="text-sm">{{ field.label }}<input v-model="active[field.key]"
                                                                             type="datetime-local"
                                                                             :class="cls" /></label></div>
                <div class="grid gap-3 md:grid-cols-3"><label
                    v-for="field in [{key:'background_color',label:'Фон'}, {key:'text_color',label:'Текст'}, {key:'accent_color',label:'Акцент'}, {key:'overlay_color',label:'Затемнение'}]"
                    :key="field.key" class="text-sm">{{ field.label }}<input v-model="active[field.key]" type="text"
                                                                             :class="cls"
                                                                             placeholder="#ffffff" /></label><label
                    class="text-sm">Прозрачность наложения, %<input v-model.number="active.overlay_opacity"
                                                                    type="number" min="0" max="100"
                                                                    :class="cls" /></label><label class="text-sm">Расположение
                    текста<select v-model="active.content_position" :class="cls">
                        <option value="left">Слева</option>
                        <option value="center">По центру</option>
                        <option value="right">Справа</option>
                    </select></label><label class="text-sm">Позиция изображения<select v-model="active.image_position"
                                                                                       :class="cls">
                    <option
                        v-for="pos in ['object-center','object-top','object-bottom','object-left','object-right','object-left-top','object-left-bottom','object-right-top','object-right-bottom']"
                        :key="pos" :value="pos">{{ pos }}
                    </option>
                </select></label><label class="text-sm">Анимация<select v-model="active.animation_type" :class="cls">
                    <option
                        v-for="a in ['none','fade','fade-up','fade-down','fade-left','fade-right','zoom-in','zoom-out','slide-up','slide-down','slide-left','slide-right']"
                        :key="a" :value="a">{{ a }}
                    </option>
                </select></label><label class="text-sm">Длительность, мс<input
                    v-model.number="active.animation_duration" type="number" min="0" :class="cls" /></label><label
                    class="text-sm">Задержка, мс<input v-model.number="active.animation_delay" type="number" min="0"
                                                       :class="cls" /></label><label class="text-sm">Интервал, мс<input
                    v-model.number="active.animation_stagger" type="number" min="0" :class="cls" /></label><label
                    class="flex items-center gap-2 text-sm"><input v-model="active.animation_once" type="checkbox" />
                    Анимация один раз</label></div>
                <div class="space-y-3 rounded border p-3 dark:border-slate-600">
                    <div class="flex flex-wrap items-center justify-between gap-2"><h4 class="font-semibold">Изображения
                        Desktop</h4><label class="cursor-pointer rounded bg-slate-700 px-3 py-2 text-xs text-white">+
                        Загрузить Desktop<input class="hidden" type="file"
                                                accept="image/jpeg,image/png,image/webp,image/avif" @change="upload" /></label>
                    </div>
                    <p class="text-xs text-slate-500">Mobile создаётся автоматически на сервере. При новом файле до
                        сохранения показывается Desktop.</p>
                    <div v-for="(img, index) in active.images" :key="img.id ?? img.preview_url"
                         class="flex items-center gap-3 rounded border p-2 dark:border-slate-600"><img
                        v-if="imageUrl(img)" :src="imageUrl(img)" class="h-16 w-24 rounded object-cover" alt="" /><span
                        class="min-w-0 flex-1 truncate text-xs">{{ img.file?.name || `Изображение #${img.id}` }}</span>
                        <button type="button" class="text-sm text-red-600" @click="removeImage(index)">Удалить</button>
                        <span class="text-xs text-red-600">{{ error(`slides.${selected}.images.${index}.file`) }}</span>
                    </div>
                </div>
                <div class="space-y-3 rounded border p-3 dark:border-slate-600">
                    <div class="flex justify-between"><h4 class="font-semibold">Кнопки / действия</h4>
                        <button type="button" class="text-sm text-blue-600" @click="addAction">+ Кнопка</button>
                    </div>
                    <div v-for="(action, i) in active.actions" :key="action.id ?? `action-${i}`"
                         class="grid gap-2 rounded border p-3 md:grid-cols-2 dark:border-slate-600"><label
                        class="text-sm">Подпись ({{ locale }})<input v-model="action.translations[locale].label"
                                                                     :class="cls" /></label><label
                        class="text-sm">Тип<select v-model="action.action_type" :class="cls">
                        <option v-for="kind in ['url','route','form','anchor']" :key="kind" :value="kind">{{ kind }}
                        </option>
                    </select></label><label class="text-sm">Значение / адрес<input v-model="action.action_value"
                                                                                   :class="cls" /><span
                        class="text-red-600">{{ error(`slides.${selected}.actions.${i}.action_value`)
                        }}</span></label><label class="text-sm">Стиль<select v-model="action.style" :class="cls">
                        <option
                            v-for="style in ['primary','secondary','outline','ghost','link','danger','success','warning']"
                            :key="style" :value="style">{{ style }}
                        </option>
                    </select></label><label class="text-sm">Иконка<input v-model="action.icon"
                                                                         :class="cls" /></label><label class="text-sm">Открыть<select
                        v-model="action.target" :class="cls">
                        <option value="_self">В этой вкладке</option>
                        <option value="_blank">В новой вкладке</option>
                    </select></label><label class="flex items-center gap-2 text-sm"><input v-model="action.activity"
                                                                                           type="checkbox" />
                        Активна</label><label class="flex items-center gap-2 text-sm"><input v-model="action.is_primary"
                                                                                             type="checkbox" /> Основная</label>
                        <button type="button" class="justify-self-start text-sm text-red-600"
                                @click="active.actions.splice(i, 1)">Удалить кнопку
                        </button>
                    </div>
                </div>
                <div class="space-y-3 rounded border p-3 dark:border-slate-600">
                    <div class="flex justify-between"><h4 class="font-semibold">Преимущества</h4>
                        <button type="button" class="text-sm text-blue-600" @click="addAdvantage">+ Преимущество
                        </button>
                    </div>
                    <div v-for="(adv, i) in active.advantages" :key="adv.id ?? `adv-${i}`"
                         class="grid gap-2 rounded border p-3 md:grid-cols-2 dark:border-slate-600"><label
                        class="text-sm">Заголовок ({{ locale }})<input v-model="adv.translations[locale].title"
                                                                       :class="cls" /></label><label class="text-sm">Текст<textarea
                        v-model="adv.translations[locale].text" :class="cls" rows="2" /></label><label class="text-sm">Тип
                        иконки<select v-model="adv.icon_type" :class="cls">
                            <option v-for="kind in ['lucide','heroicon','class','none']" :key="kind" :value="kind">
                                {{ kind }}
                            </option>
                        </select></label><label class="text-sm">Иконка<input v-model="adv.icon"
                                                                             :class="cls" /></label><label
                        class="text-sm">Стиль<select v-model="adv.style" :class="cls">
                        <option v-for="style in ['default','card','compact','inline','minimal','outlined']" :key="style"
                                :value="style">{{ style }}
                        </option>
                    </select></label><label class="flex items-center gap-2 text-sm"><input v-model="adv.activity"
                                                                                           type="checkbox" />
                        Активно</label>
                        <button type="button" class="justify-self-start text-sm text-red-600"
                                @click="active.advantages.splice(i, 1)">Удалить преимущество
                        </button>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between"><h4 class="font-semibold">Предпросмотр</h4>
                        <div class="flex gap-2">
                            <button v-for="d in ['desktop','mobile']" :key="d" type="button"
                                    class="rounded border px-2 py-1 text-xs"
                                    :class="device === d ? 'border-blue-500 text-blue-600' : ''" @click="device = d">
                                {{ d }}
                            </button>
                        </div>
                    </div>
                    <div class="relative mx-auto min-h-64 overflow-hidden rounded"
                         :class="device === 'mobile' ? 'max-w-[360px]' : 'w-full'"
                         :style="{ backgroundColor: active.background_color, color: active.text_color }"><img
                        v-if="active.images[0] && imageUrl(active.images[0])" :src="imageUrl(active.images[0])"
                        class="absolute inset-0 h-full w-full object-cover" :class="active.image_position" alt="" />
                        <div class="absolute inset-0"
                             :style="{ backgroundColor: active.overlay_color, opacity: Number(active.overlay_opacity || 0) / 100 }"></div>
                        <div class="relative flex min-h-64 flex-col justify-center p-6"
                             :class="active.content_position === 'center' ? 'items-center text-center' : active.content_position === 'right' ? 'items-end text-right' : 'items-start'">
                            <span class="text-sm">{{ translation?.label }}</span>
                            <h3 class="text-2xl font-bold">{{ translation?.title }}</h3>
                            <p class="text-lg" :style="{ color: active.accent_color }">{{ translation?.accent }}</p>
                            <p class="mt-2 text-sm">{{ translation?.description }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        <p v-else class="rounded border border-dashed p-6 text-center text-sm text-slate-500 dark:border-slate-600">
            Слайдов пока нет. Нажмите «Добавить слайд».</p>
    </section>
</template>
