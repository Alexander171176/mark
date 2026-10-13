<script setup>
/**
 * @version PulsarCMS 1.0
 *
 * Карточное представление слайдеров.
 *
 * Функциональность:
 * - Drag & Drop;
 * - выбор одной или всех карточек;
 * - изображение первого слайда;
 * - раскрытие информации о владельце;
 * - параметры Swiper;
 * - статус публикации и модерации;
 * - изменение модерации;
 * - переключение активности;
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

/* =========================================================
 * PROPS
 * ========================================================= */

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },

    selectedSliders: {
        type: Array,
        default: () => [],
    },

    isAdmin: {
        type: Boolean,
        default: false,
    },

    busy: {
        type: Boolean,
        default: false,
    },

    // Передаётся Index.vue; механику Drag & Drop не изменяем.
    canReorder: {
        type: Boolean,
        default: false,
    },

    allSelected: {
        type: Boolean,
        default: false,
    },
})

/* =========================================================
 * EMITS
 * ========================================================= */

const emit = defineEmits([
    'toggle-select',
    'toggle-all',
    'toggle-activity',
    'delete',
    'update-sort-order',
    'approve',
])

/* =========================================================
 * LOCAL STATE
 * ========================================================= */

/** Локальный список для перетаскивания */
const localSliders = ref([])

/** Открытые информационные блоки владельцев */
const openedOwnerBlocks = ref([])

/** ID изображений, которые не удалось загрузить */
const failedImages = ref([])

/** Синхронизация с родительским списком */
watch(
    () => props.sliders,
    newValue => {
        localSliders.value = [...(newValue || [])]
    },
    {
        immediate: true,
    }
)

/* =========================================================
 * DRAG & DROP
 * ========================================================= */

/** Передача нового порядка в Index.vue */
const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localSliders.value.map(slider => slider.id)
    )
}

/* =========================================================
 * SELECTION
 * ========================================================= */

/** Проверка выделения всех карточек */
const allSelected = computed(() => {
    if (!localSliders.value.length) {
        return false
    }

    return localSliders.value.every(slider => {
        return props.selectedSliders.some(id => Number(id) === Number(slider.id))
    })
})

/** Выделение всех отображаемых карточек */
const toggleAll = event => {
    emit('toggle-all', {
        ids: localSliders.value.map(slider => slider.id),
        checked: Boolean(event?.target?.checked),
    })
}

/* =========================================================
 * TRANSLATIONS
 * ========================================================= */

/** Перевод слайдера */
const sliderTranslation = slider => {
    return slider?.translation || {}
}

/** Название слайдера */
const sliderTitle = slider => {
    return sliderTranslation(slider)?.title
        || `ID: ${slider?.id}`
}

/** Подзаголовок */
const sliderSubtitle = slider => {
    return sliderTranslation(slider)?.subtitle || ''
}

/** Описание */
const sliderDescription = slider => {
    return sliderTranslation(slider)?.description || ''
}

/** Сокращение длинного текста */
const truncateText = (text, maxLength = 80) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

/* =========================================================
 * OWNER
 * ========================================================= */

/** Имя владельца */
const ownerName = slider => {
    return slider?.owner?.name || t('noData')
}

/** Email владельца */
const ownerEmail = slider => {
    return slider?.owner?.email || ''
}

/** Подсказка владельца */
const ownerTitle = slider => {
    const owner = slider?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ` — ${owner.email}` : ''}`.trim()
}

/** Аватар владельца */
const ownerAvatar = slider => {
    return slider?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/** Проверка раскрытого блока владельца */
const isOwnerBlockOpen = sliderId => {
    return openedOwnerBlocks.value.includes(sliderId)
}

/** Раскрытие информации о владельце */
const toggleOwnerBlock = sliderId => {
    if (isOwnerBlockOpen(sliderId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
            id => id !== sliderId
        )

        return
    }

    openedOwnerBlocks.value.push(sliderId)
}

/* =========================================================
 * IMAGES
 * ========================================================= */

/**
 * Выбор изображения слайдера.
 *
 * Поддерживается готовое preview_image из ресурса,
 * а также изображения первого слайда, если они
 * передаются в составе slides.
 */
const getPrimaryImage = slider => {
    if (slider?.preview_image) {
        return slider.preview_image
    }

    const slides = Array.isArray(slider?.slides)
        ? slider.slides
        : []

    const firstSlide = [...slides].sort((left, right) => {
        return Number(left?.sort ?? 0) - Number(right?.sort ?? 0)
    })[0]

    if (!firstSlide) {
        return null
    }

    const images = [
        ...(firstSlide.desktop_images || []),
        ...(firstSlide.images || []),
        ...(firstSlide.tablet_images || []),
        ...(firstSlide.mobile_images || []),
    ]

    return images[0] || null
}

/** Изображение по умолчанию */
const defaultImage = '/storage/slider/slider_slide_images/default-image.png'

/** URL изображения слайдера */
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

/** Alt изображения */
const imageAlt = slider => {
    const image = getPrimaryImage(slider)

    return image?.alt || sliderTitle(slider)
}

/** Title изображения */
const imageTitle = slider => {
    const image = getPrimaryImage(slider)

    return image?.caption || sliderTitle(slider)
}

/** Обработка ошибки загрузки */
const handleImageError = (sliderId, event) => {
    if (event?.target?.src?.endsWith('/default-image.png')) {
        return
    }

    if (!failedImages.value.includes(sliderId)) {
        failedImages.value.push(sliderId)
    }
}

/* =========================================================
 * STATUS
 * ========================================================= */

/** Ключи локализации статусов */
const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

/** Название статуса публикации */
const getStatusLabel = status => {
    return t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )
}

/** Оформление статуса модерации */
const moderationBadge = status => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/* =========================================================
 * FORMATTING
 * ========================================================= */

/** Форматирование даты */
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

/** Форматирование логического значения */
const booleanLabel = value => {
    return value ? t('yes') : t('no')
}

/* =========================================================
 * SLIDER SETTINGS
 * ========================================================= */

/** Количество слайдов */
const slidesCount = slider => {
    return Number(slider?.slides_count ?? slider?.slides?.length ?? 0)
}

/** Автопрокрутка */
const autoplayLabel = slider => {
    const delay = Number(slider?.autoplay_delay ?? 0)

    return delay > 0
        ? `${delay} мс`
        : 'Выкл.'
}

/** Количество видимых слайдов */
const slidesPerViewLabel = slider => {
    const value = slider?.slides_per_view

    return value === null || value === undefined
        ? '—'
        : String(value)
}

/** Эффект Swiper */
const effectLabel = slider => {
    return slider?.effect || 'slide'
}

/** Скорость анимации */
const speedLabel = slider => {
    return slider?.speed !== null
    && slider?.speed !== undefined
        ? `${slider.speed} мс`
        : '—'
}

/* =========================================================
 * ACTIONS
 * ========================================================= */

/** Изменение активности */
const toggleActivity = slider => {
    emit('toggle-activity', slider)
}

/** Удаление */
const deleteSlider = slider => {
    emit('delete', slider)
}

/** Изменение модерации */
const approveSlider = (slider, status, note = '') => {
    if (props.busy || !props.isAdmin) return

    emit('approve', slider, status, note)
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-400
               bg-white shadow-lg dark:border-slate-500
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
                    :checked="allSelected"
                    @change="toggleAll"
                />
            </label>
        </div>

        <!-- =====================================================
             SLIDER CARDS
             ===================================================== -->

        <div
            v-if="localSliders.length"
            class="p-3"
        >
            <draggable
                v-model="localSliders"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2
                       lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: slider }">
                    <article
                        class="relative flex h-full flex-col rounded-md
                               border border-slate-400 bg-slate-50/70
                               shadow-sm transition-shadow duration-150
                               hover:shadow-md dark:border-slate-500
                               dark:bg-slate-800/80"
                    >
                        <!-- =================================================
                             CARD HEADER
                             ================================================= -->

                        <header
                            class="flex items-center justify-between
                                   border-b border-dashed border-slate-400
                                   px-2 py-1 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- Drag handle -->
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <!-- ID -->
                                <div
                                    class="rounded-sm border border-gray-400
                                           bg-slate-200 px-1.5 py-0.5
                                           text-[10px] font-semibold
                                           text-slate-800 dark:bg-slate-700
                                           dark:text-blue-100"
                                    :title="`${t('sort')}: [${slider.sort}] / ${formatDate(slider.published_at)}`"
                                >
                                    ID: {{ slider.id }}
                                </div>

                                <!-- Owner toggle -->

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(slider.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(slider.id)"
                                >
                                    <svg
                                        class="h-4 w-4 transition-transform duration-200"
                                        :class="{
                                            'rotate-180': isOwnerBlockOpen(slider.id),
                                        }"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Moderation badge -->
                                <span
                                    class="rounded-sm border px-2 py-0.5
                                           text-[10px] font-semibold"
                                    :class="moderationBadge(
                                        slider.moderation_status
                                    ).class"
                                    :title="moderationBadge(slider.moderation_status).text"
                                >
                                    {{ moderationBadge(slider.moderation_status).text }}
                                </span>

                                <!-- Checkbox -->
                                <input
                                    type="checkbox"
                                    :checked="selectedSliders.some(id => Number(id) === Number(slider.id))"
                                    @change="emit('toggle-select', slider.id)"
                                />
                            </div>
                        </header>

                        <!-- =================================================
                             CARD CONTENT
                             ================================================= -->

                        <div class="flex flex-1 flex-col">

                            <!-- OWNER -->
                            <div
                                v-show="isOwnerBlockOpen(slider.id)"
                                class="flex flex-col items-center justify-center
                                       text-center"
                            >
                                <img
                                    :src="ownerAvatar(slider)"
                                    :title="ownerTitle(slider)"
                                    :alt="t('owner')"
                                    class="h-12 w-12 rounded-full border
                                           border-slate-300 object-cover
                                           dark:border-slate-600"
                                />

                                <div
                                    class="mt-1 line-clamp-1 text-[11px]
                                           font-semibold leading-tight
                                           text-slate-700 dark:text-slate-100"
                                    :title="ownerName(slider)"
                                >
                                    {{ ownerName(slider) }}
                                </div>

                                <div
                                    v-if="ownerEmail(slider)"
                                    class="line-clamp-1 text-[10px] leading-tight
                                           text-slate-500 dark:text-slate-300"
                                    :title="ownerEmail(slider)"
                                >
                                    {{ ownerEmail(slider) }}
                                </div>

                                <div
                                    class="mt-1 text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    <template v-if="slider.show_from_at || slider.show_to_at">
                                        {{ t('show') }}:
                                        {{ formatDate(slider.show_from_at) || '—' }}
                                        /
                                        {{ formatDate(slider.show_to_at) || '—' }}
                                    </template>

                                    <template v-else>
                                        {{ formatDate(slider.published_at) || '—' }}
                                    </template>
                                </div>
                            </div>

                            <!-- =================================================
                                 IMAGE
                                 ================================================= -->

                            <div class="relative w-full overflow-hidden
                                        bg-slate-200 dark:bg-slate-900">
                                <img
                                    :src="failedImages.includes(slider.id)
                                        ? defaultImage
                                        : imageUrl(slider)"
                                    :alt="imageAlt(slider)"
                                    :title="imageTitle(slider)"
                                    class="h-auto w-full object-cover"
                                    loading="lazy"
                                    @error="handleImageError(slider.id, $event)"
                                />

                                <!-- Количество слайдов -->
                                <div
                                    class="absolute bottom-2 right-2 rounded-sm
                                           bg-slate-900/70 px-1.5 py-0.5
                                           text-[9px] font-semibold text-white"
                                >
                                    {{ slidesCount(slider) }}
                                </div>
                            </div>

                            <!-- =================================================
                                 INFORMATION
                                 ================================================= -->

                            <div class="flex flex-1 flex-col space-y-2 py-2">

                                <!-- CODE -->
                                <div
                                    class="flex flex-wrap items-center
                                           justify-center gap-1 px-2 text-[9px]"
                                >
                                    <span
                                        class="rounded-sm border border-slate-400
                                               bg-slate-200 px-2 py-0.5
                                               text-slate-700 dark:bg-slate-700
                                               dark:text-slate-300"
                                        :title="slider.code"
                                    >
                                        {{ truncateText(slider.code, 50) }}
                                    </span>
                                </div>

                                <!-- TITLE -->
                                <a
                                    :href="route('admin.sliders.show', {
                                        slider: slider.id,
                                    })"
                                    class="flex items-center justify-center
                                           px-3 text-center text-sm font-semibold
                                           text-sky-700 hover:text-amber-700
                                           hover:underline dark:text-sky-300
                                           dark:hover:text-amber-300"
                                    :title="sliderTitle(slider)"
                                >
                                    {{ truncateText(sliderTitle(slider), 90) }}
                                </a>

                                <!-- SUBTITLE -->
                                <div
                                    v-if="sliderSubtitle(slider)"
                                    class="px-3 text-center text-[11px]
                                           font-semibold text-teal-700
                                           dark:text-teal-300"
                                    :title="sliderSubtitle(slider)"
                                >
                                    {{ truncateText(sliderSubtitle(slider), 120) }}
                                </div>

                                <!-- DESCRIPTION -->
                                <div
                                    v-if="sliderDescription(slider)"
                                    class="line-clamp-3 px-3 text-center
                                           text-[11px] text-slate-600
                                           dark:text-slate-300"
                                >
                                    {{ truncateText(sliderDescription(slider), 180) }}
                                </div>

                                <!-- =================================================
                                     SWIPER PARAMETERS
                                     ================================================= -->

                                <div
                                    class="grid grid-cols-2 gap-1 px-2
                                           text-center text-[10px] font-semibold"
                                >
                                    <!-- Effect -->
                                    <div
                                        class="rounded-sm border border-gray-400
                                               bg-indigo-100 px-1 py-1
                                               text-indigo-700
                                               dark:bg-indigo-900/40
                                               dark:text-indigo-300"
                                    >
                                        <div>{{ t('effect') }}</div>
                                        <div>{{ effectLabel(slider) }}</div>
                                    </div>

                                    <!-- Autoplay -->
                                    <div
                                        class="rounded-sm border border-gray-400
                                               bg-cyan-100 px-1 py-1
                                               text-cyan-700
                                               dark:bg-cyan-900/40
                                               dark:text-cyan-300"
                                    >
                                        <div>{{ t('autoScroll') }}</div>
                                        <div>{{ autoplayLabel(slider) }}</div>
                                    </div>

                                    <!-- Speed -->
                                    <div
                                        class="rounded-sm border border-gray-400
                                               bg-teal-100 px-1 py-1
                                               text-teal-700
                                               dark:bg-teal-900/40
                                               dark:text-teal-300"
                                    >
                                        <div>{{ t('speed') }}</div>
                                        <div>{{ speedLabel(slider) }}</div>
                                    </div>

                                    <!-- Slides per view -->
                                    <div
                                        class="rounded-sm border border-gray-400
                                               bg-violet-100 px-1 py-1
                                               text-violet-700
                                               dark:bg-violet-900/40
                                               dark:text-violet-300"
                                    >
                                        <div>{{ t('slidesScreen') }}</div>
                                        <div>{{ slidesPerViewLabel(slider) }}</div>
                                    </div>
                                </div>

                                <!-- =================================================
                                     OPTIONS
                                     ================================================= -->

                                <div
                                    class="grid grid-cols-2 gap-1 px-2
                                           text-center text-[10px]"
                                >
                                    <div>
                                        <div class="font-semibold text-sky-600 dark:text-sky-300">
                                            {{ t('cycle') }}
                                        </div>

                                        <span
                                            :class="slider.loop
                                                ? 'text-emerald-600 dark:text-emerald-300'
                                                : 'text-rose-600 dark:text-rose-300'"
                                        >
                                            {{ booleanLabel(slider.loop) }}
                                        </span>
                                    </div>

                                    <div>
                                        <div class="font-semibold text-sky-600 dark:text-sky-300">
                                            {{ t('keyboard') }}
                                        </div>

                                        <span
                                            :class="slider.keyboard
                                                ? 'text-emerald-600 dark:text-emerald-300'
                                                : 'text-rose-600 dark:text-rose-300'"
                                        >
                                            {{ booleanLabel(slider.keyboard) }}
                                        </span>
                                    </div>

                                    <div>
                                        <div class="font-semibold text-sky-600 dark:text-sky-300">
                                            {{ t('navigation') }}
                                        </div>

                                        <span
                                            :class="slider.show_navigation
                                                ? 'text-emerald-600 dark:text-emerald-300'
                                                : 'text-rose-600 dark:text-rose-300'"
                                        >
                                            {{ booleanLabel(slider.show_navigation) }}
                                        </span>
                                    </div>

                                    <div>
                                        <div class="font-semibold text-sky-600 dark:text-sky-300">
                                            {{ t('pagination') }}
                                        </div>

                                        <span
                                            :class="slider.show_pagination
                                                ? 'text-emerald-600 dark:text-emerald-300'
                                                : 'text-rose-600 dark:text-rose-300'"
                                        >
                                            {{ booleanLabel(slider.show_pagination) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- PUBLICATION DATE -->
                                <div
                                    v-if="slider.published_at"
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-400"
                                >
                                    {{ t('publishedAt') }}:
                                    {{ formatDate(slider.published_at) }}
                                </div>

                                <!-- STATUS -->
                                <div
                                    class="text-center text-[11px] font-semibold
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ t('status') }}:
                                    {{ getStatusLabel(slider.status) }}
                                </div>

                                <!-- MODERATION -->
                                <div
                                    class="flex items-center justify-center space-x-1"
                                >
                                    <span
                                        class="rounded-sm border px-2 py-1
                                               text-[10px] font-semibold"
                                        :class="moderationBadge(
                                            slider.moderation_status
                                        ).class"
                                        :title="moderationBadge(slider.moderation_status).text"
                                    >
                                        {{ moderationBadge(slider.moderation_status).text }}
                                    </span>

                                    <ModerationButton
                                        :isAdmin="isAdmin"
                                        :status="slider.moderation_status ?? 0"
                                        mode="toggle"
                                        :disabled="busy"
                                        :initialNote="slider.moderation_note || ''"
                                        @submit="({ status, note }) => approveSlider(slider, status, note)"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- =================================================
                             CARD ACTIONS
                             ================================================= -->

                        <footer
                            class="flex items-center justify-center border-t
                                   border-dashed border-slate-400 px-3 py-2
                                   dark:border-slate-500"
                        >
                            <div
                                class="flex flex-wrap items-center
                                       justify-center gap-2"
                            >
                                <ActivityToggle
                                    :isActive="slider.activity"
                                    :title="slider.activity
                                        ? t('enabled')
                                        : t('disabled')"
                                    @toggle-activity="toggleActivity(slider)"
                                />

                                <IconEdit
                                    :href="route('admin.sliders.edit', {
                                        slider: slider.id,
                                    })"
                                />

                                <DeleteIconButton
                                    @delete="deleteSlider(slider)"
                                />
                            </div>
                        </footer>
                    </article>
                </template>
            </draggable>
        </div>

        <!-- =====================================================
             EMPTY STATE
             ===================================================== -->

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
