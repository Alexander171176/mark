<script setup>
/** Общая форма создания и редактирования настроек слайдера. */
import { computed, ref } from 'vue'
import SliderSlideBuilder from './SliderSlideBuilder.vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

const props = defineProps({
    slider: { type: Object, default: null },
    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})
const { t } = useI18n()
const toast = useToast()
const record = computed(() => props.slider?.data ?? props.slider ?? {})
const editing = computed(() => Boolean(record.value.id))
const makeTranslation = () => ({ title: '', subtitle: '', description: '', meta_title: '', meta_description: '' })
const normalizeTranslations = (items) => {
    const out = {}
    const rows = Array.isArray(items) ? items : (items?.data ?? [])
    for (const item of rows) {
        if (!item?.locale) continue
        out[item.locale] = Object.fromEntries(Object.keys(makeTranslation()).map(key => [key, item[key] ?? '']))
    }
    if (!Object.keys(out).length) out[props.currentLocale || 'ru'] = makeTranslation()
    return out
}
const activeLocale = ref(props.currentLocale || 'ru')
const initial = record.value
const builder = ref(null)
const initialSlides = Array.isArray(initial.slides) ? initial.slides : (initial.slides?.data ?? [])
const normalizeSlides = () => builder.value?.normalize(initialSlides) ?? []
const form = useForm({
    code: initial.code ?? '',
    type: initial.type ?? 'hero',
    status: initial.status ?? 'draft',
    sort: Number(initial.sort ?? 0),
    activity: Boolean(initial.activity ?? false),
    published_at: initial.published_at ?? '',
    show_from_at: initial.show_from_at ?? '',
    show_to_at: initial.show_to_at ?? '',
    autoplay_delay: initial.autoplay_delay ?? null,
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
    slides: [],
})
const slidesInitialized = ref(false)
const initializeSlides = () => {
    if (!slidesInitialized.value && builder.value) {
        form.slides = normalizeSlides()
        slidesInitialized.value = true
    }
}
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) form.translations[activeLocale.value] = makeTranslation()
    return form.translations[activeLocale.value]
})
const jsonFields = ['effect_options', 'breakpoints', 'settings']
const jsonText = ref(Object.fromEntries(jsonFields.map(k => [k, JSON.stringify(form[k] ?? {}, null, 2)])))
const jsonErrors = ref({})
const submit = () => {
    jsonErrors.value = {}
    for (const key of jsonFields) {
        try {
            const value = JSON.parse(jsonText.value[key] || '{}')
            if (!value || Array.isArray(value) || typeof value !== 'object') throw new Error('Ожидается объект JSON')
            form[key] = value
        } catch (e) {
            jsonErrors.value[key] = `Неверный JSON: ${e.message}`
        }
    }
    if (Object.keys(jsonErrors.value).length) return
    initializeSlides()
    // Данные изображений с File отправляем multipart; для PUT используем POST + _method.
    form.slides = builder.value?.serialize() ?? form.slides
    form.transform(data => ({ ...data, ...(editing.value ? { _method: 'put' } : {}) }))
    const options = {
        forceFormData: true,
        preserveScroll: true,
        onError: errors => toast.error(Object.values(errors)[0] || 'Проверьте заполнение полей.'),
    }
    if (editing.value) {
        form.post(route('admin.sliders.update', { slider: record.value.id }), options)
    } else {
        form.post(route('admin.sliders.store'), options)
    }
}
const fieldClass = 'w-full rounded border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-800 dark:text-slate-100'
const error = key => form.errors[key] || ''
</script>

<template>
    <AdminLayout :title="editing ? 'Редактирование слайдера' : 'Создание слайдера'">
        <template #header>
            <TitlePage>{{ editing ? 'Редактирование слайдера' : 'Создание слайдера' }}</TitlePage>
        </template>
        <div class="mx-auto max-w-7xl p-3 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <a :href="route('admin.sliders.index')"
                   class="rounded bg-slate-600 px-4 py-2 text-sm font-semibold text-white">← {{ t('back') }}</a>
                <span v-if="editing" class="text-sm text-slate-500">ID: {{ record.id }} · {{ record.code }}</span>
            </div>
            <form class="space-y-5" @submit.prevent="submit">
                <section
                    class="rounded border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                    <h2 class="mb-4 font-bold text-lg">Основные параметры</h2>
                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="space-y-1 text-sm">Код слайдера *<input v-model="form.code" :class="fieldClass"
                                                                              maxlength="150" required
                                                                              placeholder="home-hero" /><span
                            class="text-red-500">{{ error('code') }}</span></label>
                        <label class="space-y-1 text-sm">Тип *<select v-model="form.type" :class="fieldClass">
                            <option value="hero">Hero</option>
                            <option value="banner">Banner</option>
                            <option value="carousel">Carousel</option>
                        </select><span class="text-red-500">{{ error('type') }}</span></label>
                        <label class="space-y-1 text-sm">Публикация *<select v-model="form.status" :class="fieldClass">
                            <option value="draft">Черновик</option>
                            <option value="published">Опубликован</option>
                            <option value="archived">Архив</option>
                        </select></label>
                        <label class="space-y-1 text-sm">Сортировка<input v-model.number="form.sort" type="number"
                                                                          min="0" :class="fieldClass" /></label>
                        <label class="flex items-center gap-2 text-sm"><input v-model="form.activity" type="checkbox" />
                            Активен</label>
                    </div>
                </section>
                <section
                    class="rounded border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                    <h2 class="mb-4 font-bold text-lg">Переводы слайдера</h2>
                    <TranslationTabs v-model="activeLocale" :translations="form.translations"
                                     :available-locales="availableLocales" :make-translation="makeTranslation"
                                     @update:translations="form.translations = $event" />
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="space-y-1 text-sm">Название ({{ activeLocale }})<input
                            v-model="currentTranslation.title" :class="fieldClass" maxlength="255" /><span
                            class="text-red-500">{{ error(`translations.${activeLocale}.title`) }}</span></label>
                        <label class="space-y-1 text-sm">Подзаголовок<input v-model="currentTranslation.subtitle"
                                                                            :class="fieldClass"
                                                                            maxlength="255" /></label>
                        <label class="space-y-1 text-sm md:col-span-2">Описание<textarea
                            v-model="currentTranslation.description" :class="fieldClass" rows="3" /></label>
                        <label class="space-y-1 text-sm">Meta title<input v-model="currentTranslation.meta_title"
                                                                          :class="fieldClass" maxlength="255" /></label>
                        <label class="space-y-1 text-sm">Meta description<textarea
                            v-model="currentTranslation.meta_description" :class="fieldClass" rows="2" /></label>
                    </div>
                    <p v-if="error('translations')" class="mt-2 text-sm text-red-500">{{ error('translations') }}</p>
                </section>
                <section
                    class="rounded border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                    <h2 class="mb-4 font-bold text-lg">Время публикации</h2>
                    <div class="grid gap-4 md:grid-cols-3">
                        <label
                            v-for="item in [{ key: 'published_at', label: 'Дата публикации' }, { key: 'show_from_at', label: 'Показывать с' }, { key: 'show_to_at', label: 'Показывать до' }]"
                            :key="item.key" class="space-y-1 text-sm">{{ item.label }}<input v-model="form[item.key]"
                                                                                             type="datetime-local"
                                                                                             :class="fieldClass" /><span
                            class="text-red-500">{{ error(item.key) }}</span></label>
                    </div>
                </section>
                <section
                    class="rounded border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                    <h2 class="mb-4 font-bold text-lg">Настройки Swiper</h2>
                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="space-y-1 text-sm">Эффект<select v-model="form.effect" :class="fieldClass">
                            <option v-for="effect in ['slide','fade','cube','coverflow','flip','creative']"
                                    :key="effect" :value="effect">{{ effect }}
                            </option>
                        </select></label>
                        <label class="space-y-1 text-sm">Скорость анимации (мс)<input v-model.number="form.speed"
                                                                                      type="number" min="0"
                                                                                      :class="fieldClass" /></label>
                        <label class="space-y-1 text-sm">Автопрокрутка (мс; пусто — выключена)<input
                            v-model="form.autoplay_delay" type="number" min="1" :class="fieldClass" /></label>
                        <label class="space-y-1 text-sm">Слайдов на экране<input v-model.number="form.slides_per_view"
                                                                                 type="number" min="0.01" max="999.99"
                                                                                 step="0.01"
                                                                                 :class="fieldClass" /></label>
                        <label class="space-y-1 text-sm">Расстояние между слайдами (px)<input
                            v-model.number="form.space_between" type="number" min="0" :class="fieldClass" /></label>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <label
                            v-for="item in [{key:'loop',label:'Зациклить'}, {key:'keyboard',label:'Управление клавиатурой'}, {key:'allow_touch_move',label:'Перетаскивание мышью / касанием'}, {key:'grab_cursor',label:'Курсор захвата'}, {key:'auto_height',label:'Автоматическая высота'}, {key:'pause_on_hover',label:'Пауза при наведении'}, {key:'autoplay_disable_on_interaction',label:'Отключать автопрокрутку при взаимодействии'}, {key:'show_navigation',label:'Показывать стрелки'}, {key:'show_pagination',label:'Показывать пагинацию'}]"
                            :key="item.key" class="flex items-center gap-2 text-sm"><input v-model="form[item.key]"
                                                                                           type="checkbox" />{{ item.label
                            }}</label>
                    </div>
                </section>
                <section
                    class="rounded border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                    <h2 class="mb-2 font-bold text-lg">Расширенные параметры</h2>
                    <p class="mb-4 text-xs text-slate-500">Объекты JSON. Значения проверяются перед сохранением.</p>
                    <div class="grid gap-4 lg:grid-cols-3">
                        <label
                            v-for="item in [{key:'effect_options',label:'Параметры эффекта'}, {key:'breakpoints',label:'Адаптивные точки'}, {key:'settings',label:'Дополнительные настройки'}]"
                            :key="item.key" class="space-y-1 text-sm">{{ item.label }}<textarea
                            v-model="jsonText[item.key]" rows="7" spellcheck="false"
                            :class="fieldClass + ' font-mono'" /><span
                            class="text-red-500">{{ jsonErrors[item.key] || error(item.key) }}</span></label>
                    </div>
                </section>
                <SliderSlideBuilder
                    ref="builder"
                    v-model="form.slides"
                    :errors="form.errors"
                    :current-locale="currentLocale"
                    :available-locales="availableLocales"
                    @vue:mounted="initializeSlides"
                />
                <div v-if="Object.keys(form.errors).length"
                     class="rounded border border-rose-300 p-3 text-sm text-rose-600">Есть ошибки заполнения. Проверьте
                    поля формы.
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                            class="rounded bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                        {{ form.processing ? 'Сохранение...' : (editing ? 'Сохранить изменения' : 'Создать слайдер') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
