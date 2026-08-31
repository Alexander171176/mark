<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
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
    shops: { type: Array, default: () => [] },
    selectedShops: { type: Array, default: () => [] },
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

const localShops = ref([])
const openedOwnerBlocks = ref([])

/** Синхронизация списка магазинов */
watch(
    () => props.shops,
    (newVal) => {
        localShops.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Завершение drag&drop */
const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localShops.value.map((shop) => shop.id)
    )
}

/** Выделение / снятие выделения всех карточек */
const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localShops.value.map((shop) => shop.id),
        checked: Boolean(event?.target?.checked),
    })
}

/** Все отображаемые магазины выбраны */
const allSelected = () => {
    return localShops.value.length > 0
        && localShops.value.every((shop) => props.selectedShops.includes(shop.id))
}

/** Перевод магазина */
const shopTranslation = (shop) => shop?.translation || {}

/** Название магазина */
const shopTitle = (shop) => {
    return shopTranslation(shop)?.title || `ID: ${shop?.id}`
}

/** Краткое описание */
const shopShort = (shop) => {
    return shopTranslation(shop)?.short || ''
}

/** Перевод компании */
const companyTranslation = (shop) => shop?.company?.translation || {}

/** Название компании */
const companyTitle = (shop) => {
    return companyTranslation(shop)?.title
        || shop?.company?.legal_name
        || `Company ID: ${shop?.market_company_id}`
}

/** Локализованный статус */
const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const getStatusLabel = (status) => {
    return t(statusLabelKeyMap[status] || status || 'no')
}

/** Имя владельца */
const ownerName = (shop) => {
    return shop?.owner?.name || t('noData')
}

/** Email владельца */
const ownerEmail = (shop) => {
    return shop?.owner?.email || ''
}

/** Полное описание владельца */
const ownerTitle = (shop) => {
    const owner = shop?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ` — ${owner.email}` : ''}`.trim()
}

/** Аватар владельца */
const ownerAvatar = (shop) => {
    return shop?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/** URL логотипа */
const logoUrl = (shop) => {
    const logo = shop?.logo

    if (!logo) {
        return '/storage/market/market_shops/logos/default-image-light.png'
    }

    return logo.startsWith('/storage/')
        ? logo
        : `/storage/${logo}`
}

/** Главное изображение */
const getPrimaryImage = (shop) => {
    const images = Array.isArray(shop?.images)
        ? shop.images
        : []

    if (!images.length) {
        return null
    }

    return [...images].sort(
        (a, b) => Number(a?.order ?? 0) - Number(b?.order ?? 0)
    )[0]
}

/** URL изображения */
const imageUrl = (shop) => {
    const image = getPrimaryImage(shop)

    return image?.webp_url
        || image?.thumb_url
        || image?.url
        || '/storage/market/market_shop_images/default-image.png'
}

/** Alt изображения */
const imageAlt = (shop) => {
    return getPrimaryImage(shop)?.alt || shopTitle(shop)
}

/** Title изображения */
const imageTitle = (shop) => {
    return getPrimaryImage(shop)?.caption || shopTitle(shop)
}

/** Локаль для Intl */
const dateLocale = () => {
    const currentLocale = locale.value || 'ru'

    const locales = {
        ru: 'ru-RU',
        en: 'en-US',
        kk: 'kk-KZ',
        kz: 'kk-KZ',
    }

    return locales[currentLocale]
        || currentLocale
        || 'ru-RU'
}

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return new Intl.DateTimeFormat(dateLocale(), {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(date)
}

/** Форматирование даты и времени */
const formatDateTime = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return new Intl.DateTimeFormat(dateLocale(), {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** Окно показа */
const showWindow = (shop) => {
    if (!shop?.show_from_at && !shop?.show_to_at) {
        return formatDate(shop?.published_at)
    }

    const from = formatDateTime(shop?.show_from_at)
    const to = formatDateTime(shop?.show_to_at)

    return `${t('show')}: ${from || '—'} / ${to || '—'}`
}

/** Обрезание текста */
const truncateText = (text, maxLength = 80) => {
    const value = String(text ?? '')

    if (!value) {
        return ''
    }

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

/** Бейдж модерации */
const moderationBadge = (status) => {
    const normalizedStatus = Number(status ?? 0)

    if (normalizedStatus === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (normalizedStatus === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/** Проверка открытого блока владельца */
const isOwnerBlockOpen = (shopId) => {
    return openedOwnerBlocks.value.includes(shopId)
}

/** Переключение блока владельца */
const toggleOwnerBlock = (shopId) => {
    if (isOwnerBlockOpen(shopId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
            (id) => id !== shopId
        )

        return
    }

    openedOwnerBlocks.value.push(shopId)
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
                {{ t('selected') }}: {{ selectedShops.length }}
            </div>

            <label
                v-if="localShops.length"
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

        <div v-if="localShops.length" class="p-3">
            <draggable
                v-model="localShops"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: shop }">
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
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${shop.sort}] / ${formatDate(shop.published_at)}`"
                                >
                                    ID: {{ shop.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(shop.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(shop.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOwnerBlockOpen(shop.id) }"
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
                                    class="text-[10px] px-2 py-0.5 rounded-sm border font-semibold"
                                    :class="moderationBadge(shop.moderation_status).class"
                                    :title="shop.moderation_note && shop.moderated_at
                                        ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(shop.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedShops.includes(shop.id)"
                                    @change="emits('toggle-select', shop.id)"
                                />
                            </div>
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <div
                                v-show="isOwnerBlockOpen(shop.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(shop)"
                                    :title="ownerTitle(shop)"
                                    :alt="t('owner')"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(shop)"
                                >
                                    {{ ownerName(shop) }}
                                </div>

                                <div
                                    v-if="ownerEmail(shop)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(shop)"
                                >
                                    {{ ownerEmail(shop) }}
                                </div>

                                <div
                                    class="flex flex-col items-center justify-center
                                           text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ showWindow(shop) }}
                                </div>
                            </div>

                            <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                                <img
                                    :src="imageUrl(shop)"
                                    :alt="imageAlt(shop)"
                                    :title="imageTitle(shop)"
                                    class="h-32 w-full object-cover"
                                />
                            </div>

                            <div class="flex justify-center">
                                <img
                                    :src="logoUrl(shop)"
                                    :alt="shopTitle(shop)"
                                    :title="shopTitle(shop)"
                                    class="h-12 w-20 object-cover rounded-sm
                                           border border-slate-300 dark:border-slate-600"
                                />
                            </div>

                            <a
                                :href="`/market/shops/${encodeURIComponent(shop.url || '')}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold
                                       text-blue-700 dark:text-blue-300 hover:underline
                                       hover:text-amber-700 dark:hover:text-amber-200
                                       line-clamp-2 text-center"
                            >
                                {{ truncateText(shopTitle(shop), 90) }}
                            </a>

                            <div class="text-center text-[11px] text-slate-500 dark:text-slate-300">
                                {{ truncateText(shop.url, 90) }}
                            </div>

                            <div
                                class="text-center text-[11px] font-semibold
                                       text-indigo-700 dark:text-indigo-300"
                            >
                                {{ truncateText(companyTitle(shop), 90) }}
                            </div>

                            <div
                                v-if="shop?.company?.legal_name"
                                class="text-center text-[10px] text-slate-500 dark:text-slate-400"
                            >
                                {{ truncateText(shop.company.legal_name, 100) }}
                            </div>

                            <div
                                v-if="shopShort(shop)"
                                class="font-semibold text-[12px] text-center
                                       text-sky-700 dark:text-sky-300"
                            >
                                {{ truncateText(shopShort(shop), 120) }}
                            </div>

                            <div
                                class="grid grid-cols-1 gap-0.5 text-[11px]
                                       text-slate-600 dark:text-slate-300"
                            >
                                <div class="text-center font-semibold">
                                    <span>{{ t('contacts') }}: </span>

                                    <span class="text-amber-700 dark:text-amber-300">
                                        {{ shop.phone || '—' }}
                                    </span>
                                </div>

                                <div
                                    v-if="shop.email"
                                    class="text-center line-clamp-1"
                                    :title="shop.email"
                                >
                                    {{ shop.email }}
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-center gap-3 text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <div
                                    class="flex items-center justify-center space-x-1"
                                    :title="t('views')"
                                >
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-300"
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                        />
                                    </svg>

                                    <span class="text-[12px] text-slate-700 dark:text-slate-200">
                                        {{ shop.views ?? 0 }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-center space-x-1"
                                    :title="t('images')"
                                >
                                    <span class="text-[11px]">
                                        {{ t('images') }}:
                                    </span>

                                    <span class="text-[12px] text-slate-700 dark:text-slate-200">
                                        {{ shop.images_count ?? 0 }}
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
                                    <span>{{ t('status') }}: </span>
                                    {{ getStatusLabel(shop.status) }}
                                </div>
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(shop.moderation_status).class"
                                    :title="shop.moderation_note && shop.moderated_at
                                        ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(shop.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="shop?.moderation_status ?? 0"
                                    :initialNote="shop?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) => emits('approve', shop, status, note)"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="Boolean(shop.left)"
                                    :title="shop.left ? t('enabled') : t('disabled')"
                                    @toggle-left="emits('toggle-left', shop)"
                                />

                                <MainToggle
                                    :isActive="Boolean(shop.main)"
                                    :title="shop.main ? t('enabled') : t('disabled')"
                                    @toggle-main="emits('toggle-main', shop)"
                                />

                                <RightToggle
                                    :isActive="Boolean(shop.right)"
                                    :title="shop.right ? t('enabled') : t('disabled')"
                                    @toggle-right="emits('toggle-right', shop)"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="Boolean(shop.activity)"
                                    :title="shop.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emits('toggle-activity', shop)"
                                />

                                <IconEdit
                                    :href="route('admin.marketShops.edit', { marketShop: shop.id })"
                                />

                                <DeleteIconButton
                                    @delete="emits('delete', shop)"
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
