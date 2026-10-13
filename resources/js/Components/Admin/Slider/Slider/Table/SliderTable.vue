<script setup>
/**
 * @version PulsarCMS 1.0
 *
 * Таблица слайдеров.
 *
 * - Drag & Drop;
 * - выбор отдельных и всех слайдеров;
 * - превью первого слайда;
 * - информация о владельце;
 * - параметры Swiper;
 * - статус публикации;
 * - модерация;
 * - изменение активности;
 * - редактирование и удаление.
 */

import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

/* ======================== Props ======================== */

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },

    selectedSliders: {
        type: Array,
        default: () => [],
    },

    allSelected: {
        type: Boolean,
        default: false,
    },

    isAdmin: {
        type: Boolean,
        default: false,
    },

    // Передаются из Index.vue; существующую логику Drag & Drop не изменяем.
    canReorder: {
        type: Boolean,
        default: false,
    },

    busy: {
        type: Boolean,
        default: false,
    },
})

/* ======================== Emits ======================== */

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

/* ======================== Local sliders ======================== */

/** Локальная копия списка для vuedraggable. */
const localSliders = ref([])

watch(
    () => props.sliders,
    newValue => {
        localSliders.value = [...(newValue || [])]
    },
    {
        immediate: true,
    }
)

/** Передача нового порядка слайдеров родительскому компоненту. */
const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localSliders.value.map(slider => slider.id)
    )
}

/* ======================== Selection ======================== */

/** Все слайдеры текущей таблицы выбраны. */
const allSlidersSelected = computed(() => {
    if (!localSliders.value.length) {
        return false
    }

    return localSliders.value.every(slider => {
        return props.selectedSliders.some(id => Number(id) === Number(slider.id))
    })
})

/** Выбор всех отображаемых слайдеров. */
const toggleAll = event => {
    emit('toggle-all', {
        ids: localSliders.value.map(slider => slider.id),
        checked: Boolean(event?.target?.checked),
    })
}

/* ======================== Translations ======================== */

/** Текущий перевод слайдера. */
const sliderTranslation = slider => {
    return slider?.translation || {}
}

/** Название слайдера. */
const sliderTitle = slider => {
    return sliderTranslation(slider)?.title || `ID: ${slider?.id}`
}

/** Подзаголовок слайдера. */
const sliderSubtitle = slider => {
    return sliderTranslation(slider)?.subtitle || ''
}

/** Описание слайдера. */
const sliderDescription = slider => {
    return sliderTranslation(slider)?.description || ''
}

/* ======================== Images ======================== */

/** Изображение по умолчанию. */
const defaultImage = '/storage/slider/slider_slide_images/default-image.png'

/** Список изображений первого слайда. */
const getPrimaryImage = slider => {
    if (slider?.preview_image) {
        return slider.preview_image
    }

    if (!Array.isArray(slider?.slides) || !slider.slides.length) {
        return null
    }

    const firstSlide = [...slider.slides].sort((left, right) => {
        return Number(left?.sort ?? 0) - Number(right?.sort ?? 0)
    })[0]

    const images = [
        ...(firstSlide?.desktop_images || []),
        ...(firstSlide?.images || []),
        ...(firstSlide?.tablet_images || []),
        ...(firstSlide?.mobile_images || []),
    ]

    return [...images].sort((left, right) => {
        return Number(left?.order ?? 0) - Number(right?.order ?? 0)
    })[0] || null
}

/** URL изображения слайдера. */
const imageUrl = slider => {
    const image = getPrimaryImage(slider)

    if (typeof image === 'string') {
        return image || defaultImage
    }

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || defaultImage
}

/** Alt изображения. */
const imageAlt = slider => {
    const image = getPrimaryImage(slider)

    return image?.alt || sliderTitle(slider)
}

/** Title изображения. */
const imageTitle = slider => {
    const image = getPrimaryImage(slider)

    return image?.caption || sliderTitle(slider)
}

/** Подстановка заглушки при ошибке загрузки. */
const handleImageError = event => {
    const image = event?.target

    if (!image) {
        return
    }

    if (!image.src.endsWith(defaultImage)) {
        image.src = defaultImage
    }
}

/* ======================== Owner ======================== */

/** Подсказка владельца слайдера. */
const ownerTitle = slider => {
    const owner = slider?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ` — ${owner.email}` : ''}`.trim()
}

/** Аватар владельца. */
const ownerAvatar = slider => {
    return slider?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/* ======================== Status ======================== */

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

/** Название статуса публикации. */
const getStatusLabel = status => {
    return t(statusLabelKeyMap[status] || status || 'no')
}

/** Оформление статуса публикации. */
const statusBadge = status => {
    if (status === 'published') {
        return {
            text: getStatusLabel(status),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
        }
    }

    if (status === 'archived') {
        return {
            text: getStatusLabel(status),
            class:
                'bg-slate-200 text-slate-700 border-slate-400 ' +
                'dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600',
        }
    }

    return {
        text: getStatusLabel(status),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
    }
}

/** Оформление статуса модерации. */
const moderationBadge = status => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
    }
}

/* ======================== Formatting ======================== */

/** Форматирование даты. */
const formatDate = dateString => {
    if (!dateString) {
        return ''
    }

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString(locale.value || 'ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

/** Ограничение длины текста. */
const truncateText = (text, maxLength = 50) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

/* ======================== Slider settings ======================== */

/** Количество слайдов. */
const slidesCount = slider => {
    return Number(
        slider?.slides_count ?? slider?.slides?.length ?? 0
    )
}

/** Задержка автопрокрутки. */
const autoplayLabel = slider => {
    const delay = Number(slider?.autoplay_delay ?? 0)

    return delay > 0
        ? `${delay} мс`
        : 'Выкл.'
}

/** Скорость анимации. */
const speedLabel = slider => {
    return slider?.speed !== null && slider?.speed !== undefined
        ? `${slider.speed} мс`
        : '—'
}

/** Количество видимых слайдов. */
const slidesPerViewLabel = slider => {
    const value = slider?.slides_per_view

    return value !== null && value !== undefined
        ? String(value)
        : '—'
}

/** Подсказка публикации. */
const publicationTitle = slider => {
    const values = [`${t('sort')}: ${slider?.sort ?? 0}`]

    if (slider?.published_at) {
        values.push(`${t('publishedAt')}: ${formatDate(slider.published_at)}`)
    }

    if (slider?.show_from_at) {
        values.push(`${t('showFromAt')}: ${formatDate(slider.show_from_at)}`)
    }

    if (slider?.show_to_at) {
        values.push(`${t('showToAt')}: ${formatDate(slider.show_to_at)}`)
    }

    return values.join('\n')
}

/** Подсказка настроек Swiper. */
const swiperSettingsTitle = slider => {
    return [
        `${t('effect')}: ${slider?.effect || 'slide'}`,
        `${t('speed')}: ${speedLabel(slider)}`,
        `${t('autoScroll')}: ${autoplayLabel(slider)}`,
        `${t('slidesScreen')}: ${slidesPerViewLabel(slider)}`,
        `${t('cycle')}: ${slider?.loop ? t('yes') : t('no')}`,
        `${t('navigation')}: ${slider?.show_navigation ? t('yes') : t('no')}`,
        `${t('pagination')}: ${slider?.show_pagination ? t('yes') : t('no')}`,
        `${t('keyboard')}: ${slider?.keyboard ? t('yes') : t('no')}`,
    ].join('\n')
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-200
               bg-white shadow-lg dark:border-slate-600
               dark:bg-slate-700"
    >
        <!-- =====================================================
             SELECTION PANEL
             ===================================================== -->

        <div
            class="flex items-center justify-between border-b
                   border-slate-400 px-3 py-2 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedSliders.length }}
            </div>

            <label
                v-if="localSliders.length"
                class="flex cursor-pointer items-center text-xs
                       text-slate-600 dark:text-slate-200"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="Boolean(allSlidersSelected || allSelected)"
                    @change="toggleAll"
                />
            </label>
        </div>

        <!-- =====================================================
             TABLE
             ===================================================== -->

        <div class="overflow-x-auto">
            <table
                v-if="localSliders.length"
                class="table-auto w-full text-slate-700
                       dark:text-slate-100"
            >
                <!-- TABLE HEADER -->

                <thead
                    class="border border-solid border-gray-300
                           bg-slate-200 text-xs uppercase
                           dark:border-gray-700 dark:bg-cyan-900"
                >
                <tr>
                    <!-- Drag -->
                    <th class="w-px px-1 py-3">
                        <svg
                            class="h-4 w-4 fill-current text-slate-800
                                       dark:text-slate-200"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>

                    <!-- ID -->
                    <th class="w-px whitespace-nowrap px-1 py-3">
                        <div class="text-center font-semibold">{{ t('id') }}</div>
                    </th>

                    <!-- Owner -->
                    <th class="w-px whitespace-nowrap px-1 py-3">
                        <div class="flex justify-center" :title="t('owner')">
                            <svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z" />
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z" />
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z" />
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z" />
                                <path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z" />
                            </svg>
                        </div>
                    </th>

                    <!-- Image -->
                    <th class="w-px px-1 py-3">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                            </svg>
                        </div>
                    </th>

                    <!-- Slider -->
                    <th class="min-w-64 px-1 py-3 text-left">
                        <div class="text-left font-semibold">{{ t('slider') }}</div>
                    </th>

                    <!-- Type -->
                    <th class="whitespace-nowrap px-1 py-3">
                        <div class="text-center font-semibold">{{ t('type') }}</div>
                    </th>

                    <!-- Show -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-left font-semibold">{{ t('show') }}</div>
                    </th>

                    <!-- Actions -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-right font-semibold">{{ t('actions') }}</div>
                    </th>

                    <!-- Checkbox -->
                    <th class="w-px whitespace-nowrap px-1 py-1 text-center">
                        <input
                            type="checkbox"
                            :checked="Boolean(allSlidersSelected || allSelected)"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <!-- =================================================
                     TABLE BODY / DRAG & DROP
                     ================================================= -->

                <draggable
                    v-model="localSliders"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: slider }">
                        <tr
                            class="border-b-2 text-xs font-semibold
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <!-- Drag -->
                            <td
                                class="handle w-px cursor-move px-1 py-1
                                       text-center"
                            >
                                <svg
                                    class="h-4 w-4 text-gray-500
                                           dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="w-px whitespace-nowrap px-1 py-1">
                                <div
                                    class="text-center text-blue-600
                                           dark:text-blue-200"
                                    :title="publicationTitle(slider)"
                                >
                                    {{ slider.id }}
                                </div>
                            </td>

                            <!-- Owner -->
                            <td class="w-px px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(slider)"
                                        :title="ownerTitle(slider)"
                                        :alt="t('owner')"
                                        class="h-7 w-7 rounded-full border
                                               border-slate-300 object-cover
                                               dark:border-slate-600"
                                    />
                                </div>
                            </td>

                            <!-- Image -->
                            <td class="w-16 px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(slider)"
                                        :alt="imageAlt(slider)"
                                        :title="imageTitle(slider)"
                                        class="h-10 w-14 rounded-sm border
                                               border-slate-300 object-cover
                                               dark:border-slate-600"
                                        loading="lazy"
                                        @error="handleImageError"
                                    />
                                </div>
                                <div
                                    v-if="slidesCount(slider)"
                                    class="text-center text-sm font-bold
                                           text-cyan-700 dark:text-cyan-300"
                                >
                                    {{ slidesCount(slider) }}
                                </div>
                            </td>

                            <!-- Slider information -->
                            <td class="px-2 py-1">
                                <div class="text-left">
                                    <!-- Title -->
                                    <a
                                        :href="route('admin.sliders.show', {
                                            slider: slider.id,
                                        })"
                                        class="text-sm text-sky-700
                                               hover:text-amber-700
                                               hover:underline
                                               dark:text-sky-300
                                               dark:hover:text-amber-300"
                                        :title="sliderDescription(slider)
                                            || sliderSubtitle(slider)
                                            || sliderTitle(slider)"
                                    >
                                        {{ truncateText(sliderTitle(slider), 80) }}
                                    </a>

                                    <!-- Subtitle -->
                                    <div
                                        v-if="sliderSubtitle(slider)"
                                        class="text-[10px] text-slate-700
                                               dark:text-slate-300"
                                    >
                                        {{ truncateText(sliderSubtitle(slider), 75) }}
                                    </div>

                                    <!-- Code -->
                                    <div
                                        class="mt-0.5 text-[10px] italic
                                               text-slate-500 dark:text-slate-400"
                                        :title="slider.code"
                                    >
                                        {{ truncateText(slider.code, 70) }}
                                    </div>

                                    <!-- Swiper options -->
                                    <div
                                        class="mt-1 flex flex-wrap items-center
                                               gap-1 text-[9px]"
                                        :title="swiperSettingsTitle(slider)"
                                    >
                                        <span
                                            class="rounded-sm border border-slate-400
                                                   bg-indigo-100 px-1 py-0.5
                                                   text-indigo-700
                                                   dark:bg-indigo-900/40
                                                   dark:text-indigo-300"
                                        >
                                            {{ t('cycle') }}:
                                            {{ slider.loop ? t('yes') : t('no') }}
                                        </span>

                                        <span
                                            class="rounded-sm border border-slate-400
                                                   bg-cyan-100 px-1 py-0.5
                                                   text-cyan-700
                                                   dark:bg-cyan-900/40
                                                   dark:text-cyan-300"
                                        >
                                            {{ t('navigation') }}:
                                            {{ slider.show_navigation ? t('yes') : t('no') }}
                                        </span>

                                        <span
                                            class="rounded-sm border border-slate-400
                                                   bg-violet-100 px-1 py-0.5
                                                   text-violet-700
                                                   dark:bg-violet-900/40
                                                   dark:text-violet-300"
                                        >
                                            {{ t('pagination') }}:
                                            {{ slider.show_pagination ? t('yes') : t('no') }}
                                        </span>

                                        <span
                                            class="rounded-sm border border-slate-400
                                                   bg-teal-100 px-1 py-0.5
                                                   text-teal-700
                                                   dark:bg-teal-900/40
                                                   dark:text-teal-300"
                                        >
                                            {{ t('keyboard') }}:
                                            {{ slider.keyboard ? t('yes') : t('no') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="flex flex-col items-center justify-center gap-0.5
                                       whitespace-nowrap px-2 py-1">
                                <div
                                    class="text-center text-sm
                                           text-blue-700 dark:text-blue-300"
                                >
                                    {{ slider.type || '—' }}
                                </div>
                                <div
                                    class="text-center text-sm
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ slider.effect || 'slide' }}
                                </div>
                                <div
                                    class="text-center text-[10px]
                                           text-violet-700 dark:text-violet-300"
                                >
                                    {{ autoplayLabel(slider) }}
                                </div>
                                <div
                                    class="text-center text-xs
                                           text-teal-700 dark:text-teal-300"
                                >
                                    {{ speedLabel(slider) }}
                                </div>
                                <div
                                    class="text-center text-xs
                                           text-cyan-700 dark:text-cyan-300"
                                >
                                    {{ slidesPerViewLabel(slider) }}
                                </div>
                            </td>

                            <!-- =================================================
                                 STATUS / MODERATION
                                 ================================================= -->

                            <td class="whitespace-nowrap px-2 py-1">
                                <div
                                    class="flex flex-col items-start
                                           justify-center gap-1"
                                >
                                    <!-- Publication status -->

                                    <span
                                        class="rounded-sm border px-2 py-1
                                               text-[10px] font-semibold"
                                        :class="statusBadge(slider.status).class"
                                        :title="publicationTitle(slider)"
                                    >
                                        {{ statusBadge(slider.status).text }}
                                    </span>

                                    <!-- Moderation -->

                                    <div
                                        class="flex items-center justify-center gap-1"
                                    >
                                        <span
                                            class="rounded-sm border px-2 py-1
                                                   text-[9px] font-semibold"
                                            :class="moderationBadge(
                                                slider.moderation_status
                                            ).class"
                                        >
                                            {{ moderationBadge(slider.moderation_status).text }}
                                        </span>

                                        <ModerationButton
                                            :isAdmin="isAdmin"
                                            :status="slider.moderation_status ?? 0"
                                            mode="toggle"
                                            :disabled="busy"
                                            :initialNote="slider.moderation_note || ''"
                                            @submit="({ status, note }) => emit('approve', slider, status, note)"
                                        />
                                    </div>
                                </div>
                            </td>

                            <!-- =================================================
                                 ACTIONS
                                 ================================================= -->

                            <td class="whitespace-nowrap px-2 py-1">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <ActivityToggle
                                        :isActive="slider.activity"
                                        :title="slider.activity
                                            ? t('enabled')
                                            : t('disabled')"
                                        @toggle-activity="emit(
                                            'toggle-activity',
                                            slider
                                        )"
                                    />

                                    <IconEdit
                                        :href="route('admin.sliders.edit', {
                                            slider: slider.id,
                                        })"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', slider)"
                                    />
                                </div>
                            </td>

                            <!-- Checkbox -->

                            <td class="w-px whitespace-nowrap px-1 py-1">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedSliders.some(id => Number(id) === Number(slider.id))"
                                        @change="emit('toggle-select', slider.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <!-- EMPTY STATE -->

            <div
                v-else
                class="p-5 text-center text-slate-700
                       dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
