<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import LeftToggle from '@/Components/Admin/UI/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/UI/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/UI/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    brands: { type: Array, default: () => [] },
    selectedBrands: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

const emits = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

/** Локальный список брендов */
const localBrands = ref([])

/** Открытые блоки владельцев */
const openedOwnerBlocks = ref([])

watch(
    () => props.brands,
    newVal => {
        localBrands.value = JSON.parse(
            JSON.stringify(newVal || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/** Завершение drag&drop */
const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localBrands.value.map(
            brand => brand.id
        )
    )
}

/** Массовое выделение */
const toggleAll = event => {
    emits('toggle-all', {
        ids: localBrands.value.map(
            brand => brand.id
        ),
        checked: Boolean(
            event?.target?.checked
        ),
    })
}

/** Все бренды выбраны */
const allSelected = () => {
    return Boolean(
        localBrands.value.length
        && localBrands.value.every(
            brand =>
                props.selectedBrands.includes(
                    brand.id
                )
        )
    )
}

/** Текущий перевод */
const brandTranslation = brand =>
    brand?.translation || {}

/** Название */
const brandTitle = brand =>
    brandTranslation(brand)?.title
    || `ID: ${brand?.id}`

/** Краткое описание */
const brandShort = brand =>
    brandTranslation(brand)?.short || ''

/** Названия статусов */
const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

/** Текст статуса */
const getStatusLabel = status =>
    t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )

/** Имя владельца */
const ownerName = brand =>
    brand?.owner?.name
    || t('noData')

/** Email владельца */
const ownerEmail = brand =>
    brand?.owner?.email || ''

/** Полная подпись владельца */
const ownerTitle = brand => {
    const owner = brand?.owner

    if (!owner) {
        return t('noData')
    }

    return [
        owner.name || '',
        owner.email || '',
    ]
        .filter(Boolean)
        .join(' — ')
}

/** Аватар владельца */
const ownerAvatar = brand =>
    brand?.owner?.profile_photo_url
    || '/storage/profile-photos/default-image.png'

/** URL логотипа */
const logoUrl = brand => {
    const logo = brand?.logo

    if (!logo) {
        return '/storage/market/market_brands/logos/default-image-light.png'
    }

    const value = String(logo)

    if (
        value.startsWith('http://')
        || value.startsWith('https://')
        || value.startsWith('/storage/')
    ) {
        return value
    }

    return `/storage/${value}`
}

/** Основное изображение */
const getPrimaryImage = brand => {
    const images = Array.isArray(
        brand?.images
    )
        ? brand.images
        : []

    if (!images.length) {
        return null
    }

    return [...images].sort(
        (a, b) =>
            Number(a?.order ?? 0)
            - Number(b?.order ?? 0)
    )[0]
}

/** URL основного изображения */
const imageUrl = brand => {
    const image = getPrimaryImage(brand)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/market/market_brand_images/default-image.png'
}

/** ALT изображения */
const imageAlt = brand => {
    const image = getPrimaryImage(brand)

    return image?.alt
        || brandTitle(brand)
}

/** TITLE изображения */
const imageTitle = brand => {
    const image = getPrimaryImage(brand)

    return image?.caption
        || brandTitle(brand)
}

/** Публичный URL бренда */
const brandPublicUrl = brand => {
    if (!brand?.url) {
        return '#'
    }

    return `/market/brands/${encodeURIComponent(
        String(brand.url)
    )}`
}

/** Форматирование даты */
const formatDate = dateStr => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return ''
    }

    return date.toLocaleDateString(
        locale.value || undefined,
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    )
}

/** Обрезка текста */
const truncateText = (
    text,
    maxLength = 80
) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? value
        .slice(0, maxLength)
        .trimEnd() + '…'
        : value
}

/** Badge модерации */
const moderationBadge = status => {
    const value = Number(
        status ?? 0
    )

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 '
                + 'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 '
                + 'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 '
            + 'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/** Открыт ли блок владельца */
const isOwnerBlockOpen = brandId =>
    openedOwnerBlocks.value.includes(
        brandId
    )

/** Переключение блока владельца */
const toggleOwnerBlock = brandId => {
    if (
        isOwnerBlockOpen(
            brandId
        )
    ) {
        openedOwnerBlocks.value =
            openedOwnerBlocks.value.filter(
                id => id !== brandId
            )

        return
    }

    openedOwnerBlocks.value.push(
        brandId
    )
}

/** Выбор бренда */
const toggleSelect = brandId => {
    emits(
        'toggle-select',
        brandId
    )
}

/** Left */
const emitToggleLeft = brand => {
    emits(
        'toggle-left',
        brand
    )
}

/** Main */
const emitToggleMain = brand => {
    emits(
        'toggle-main',
        brand
    )
}

/** Right */
const emitToggleRight = brand => {
    emits(
        'toggle-right',
        brand
    )
}

/** Activity */
const emitToggleActivity = brand => {
    emits(
        'toggle-activity',
        brand
    )
}

/** Delete */
const emitDelete = brand => {
    emits(
        'delete',
        brand
    )
}

/** Moderation */
const emitApprove = (
    brand,
    status,
    note
) => {
    emits(
        'approve',
        brand,
        status,
        note
    )
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedBrands.length }}
            </div>

            <label
                v-if="localBrands.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="allSelected()"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div
            v-if="localBrands.length"
            class="p-3"
        >
            <draggable
                tag="div"
                v-model="localBrands"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: brand }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${brand.sort}] / ${formatDate(brand.published_at)}`"
                                >
                                    ID: {{ brand.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(brand.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(brand.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                isOwnerBlockOpen(brand.id),
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
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm
                                           border font-semibold"
                                    :class="moderationBadge(brand.moderation_status).class"
                                    :title="brand.moderation_note && brand.moderated_at
                                        ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(brand.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedBrands.includes(brand.id)"
                                    @change="toggleSelect(brand.id)"
                                />
                            </div>
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <div
                                v-show="isOwnerBlockOpen(brand.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(brand)"
                                    :title="ownerTitle(brand)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('owner')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(brand)"
                                >
                                    {{ ownerName(brand) }}
                                </div>

                                <div
                                    v-if="ownerEmail(brand)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(brand)"
                                >
                                    {{ ownerEmail(brand) }}
                                </div>

                                <div
                                    v-if="brand.show_from_at"
                                    class="flex flex-col items-center justify-center
                                           text-center text-[10px] text-slate-500
                                           dark:text-slate-300"
                                >
                                    {{ t('show') }}:
                                    {{ brand.show_from_at }}
                                    /
                                    {{ brand.show_to_at }}
                                </div>

                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center
                                           text-center text-[10px] text-slate-500
                                           dark:text-slate-300"
                                >
                                    {{ formatDate(brand.published_at) }}
                                </div>
                            </div>

                            <div
                                class="relative w-full bg-slate-200 dark:bg-slate-900"
                            >
                                <img
                                    :src="imageUrl(brand)"
                                    :alt="imageAlt(brand)"
                                    :title="imageTitle(brand)"
                                    class="h-32 w-full object-cover"
                                />
                            </div>

                            <div class="flex justify-center">
                                <img
                                    :src="logoUrl(brand)"
                                    :alt="brandTitle(brand)"
                                    :title="brandTitle(brand)"
                                    class="h-12 w-20 object-cover rounded-sm
                                           border border-slate-300 dark:border-slate-600"
                                />
                            </div>

                            <a
                                :href="brandPublicUrl(brand)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold hover:underline
                                       text-blue-700 dark:text-blue-300
                                       hover:text-amber-700 dark:hover:text-amber-200
                                       line-clamp-2 text-center"
                            >
                                {{ truncateText(brandTitle(brand), 90) }}
                            </a>

                            <div
                                class="text-center text-[11px] text-slate-500
                                       dark:text-slate-300"
                            >
                                {{ truncateText(brand.url, 90) }}
                            </div>

                            <a
                                v-if="brand.website"
                                :href="brand.website"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-center text-[11px] text-amber-700
                                       dark:text-amber-300 hover:underline line-clamp-1"
                                :title="brand.website"
                            >
                                {{ truncateText(brand.website, 90) }}
                            </a>

                            <div
                                v-if="brandShort(brand)"
                                class="font-semibold text-[12px] text-center
                                       text-teal-700 dark:text-teal-300"
                            >
                                {{ truncateText(brandShort(brand), 120) }}
                            </div>

                            <div
                                class="flex items-center justify-center gap-3 text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <div
                                    class="flex items-center justify-center space-x-1"
                                    :title="t('views')"
                                >
                                    <svg
                                        class="w-4 h-4 fill-current shrink-0"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-300"
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                        />
                                    </svg>

                                    <span
                                        class="text-[12px] text-slate-700 dark:text-slate-200"
                                    >
                                        {{ brand.views ?? 0 }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-center space-x-1"
                                    :title="t('images')"
                                >
                                    <span class="text-[11px]">
                                        {{ t('images') }}:
                                    </span>

                                    <span
                                        class="text-[12px] text-slate-700 dark:text-slate-200"
                                    >
                                        {{ brand.images_count ?? 0 }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 gap-0.5 text-[11px]
                                       text-slate-600 dark:text-slate-300"
                            >
                                <div
                                    class="font-semibold text-center
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    <span>
                                        {{ t('status') }}:
                                    </span>

                                    {{ getStatusLabel(brand.status) }}
                                </div>
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm
                                           border font-semibold"
                                    :class="moderationBadge(brand.moderation_status).class"
                                    :title="brand.moderation_note && brand.moderated_at
                                        ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(brand.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="brand?.moderation_status ?? 0"
                                    :initialNote="brand?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) =>
                                        emitApprove(brand, status, note)"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="Boolean(brand.left)"
                                    :title="brand.left ? t('enabled') : t('disabled')"
                                    @toggle-left="emitToggleLeft(brand)"
                                />

                                <MainToggle
                                    :isActive="Boolean(brand.main)"
                                    :title="brand.main ? t('enabled') : t('disabled')"
                                    @toggle-main="emitToggleMain(brand)"
                                />

                                <RightToggle
                                    :isActive="Boolean(brand.right)"
                                    :title="brand.right ? t('enabled') : t('disabled')"
                                    @toggle-right="emitToggleRight(brand)"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="Boolean(brand.activity)"
                                    :title="brand.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emitToggleActivity(brand)"
                                />

                                <IconEdit
                                    :href="route(
                                        'admin.marketBrands.edit',
                                        { marketBrand: brand.id }
                                    )"
                                />

                                <DeleteIconButton
                                    @delete="emitDelete(brand)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
