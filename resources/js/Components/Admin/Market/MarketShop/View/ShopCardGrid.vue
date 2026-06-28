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

const { t } = useI18n()

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

watch(
    () => props.shops,
    (newVal) => {
        localShops.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localShops.value.map(shop => shop.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localShops.value.map(shop => shop.id),
        checked: event.target.checked,
    })
}

const allSelected = () => {
    return localShops.value.length
        && localShops.value.every(shop => props.selectedShops.includes(shop.id))
}

const shopTranslation = (shop) => shop?.translation || {}
const shopTitle = (shop) => shopTranslation(shop)?.title || `ID: ${shop?.id}`
const shopShort = (shop) => shopTranslation(shop)?.short || ''

const companyTranslation = (shop) => shop?.company?.translation || {}

const companyTitle = (shop) => {
    return companyTranslation(shop)?.title
        || shop?.company?.legal_name
        || `Company ID: ${shop?.market_company_id}`
}

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}
const getStatusLabel = (status) => t(statusLabelKeyMap[status] || status || 'no')

const ownerName = (shop) => shop?.owner?.name || t('noData')
const ownerEmail = (shop) => shop?.owner?.email || ''

const ownerTitle = (shop) => {
    const owner = shop?.owner
    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (shop) => {
    return shop?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

const logoUrl = (shop) => {
    if (!shop?.logo) {
        return '/storage/market/market_shops/logos/default-image-light.png'
    }

    return shop.logo.startsWith('/storage/')
        ? shop.logo
        : `/storage/${shop.logo}`
}

const getPrimaryImage = (shop) => {
    if (shop.images && shop.images.length) {
        return [...shop.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }

    return null
}

const imageUrl = (shop) => {
    const image = getPrimaryImage(shop)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/market/market_shop_images/default-image.png'
}

const imageAlt = (shop) => {
    const image = getPrimaryImage(shop)

    return image?.alt || shopTitle(shop)
}

const imageTitle = (shop) => {
    const image = getPrimaryImage(shop)

    return image?.caption || shopTitle(shop)
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (isNaN(date)) return ''

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const truncateText = (text, maxLength = 80) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

const isOwnerBlockOpen = (shopId) => {
    return openedOwnerBlocks.value.includes(shopId)
}

const toggleOwnerBlock = (shopId) => {
    if (isOwnerBlockOpen(shopId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(id => id !== shopId)
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
                tag="div"
                v-model="localShops"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
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
                                    class="handle cursor-move text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
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
                                :title="isOwnerBlockOpen(shop.id) ? t('hideOwner') : t('showOwner')"
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
                                        ? `${shop.moderation_note} [${formatDate(shop.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(shop.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedShops.includes(shop.id)"
                                    @change="$emit('toggle-select', shop.id)"
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
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('owner')"
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
                                    v-if="shop.show_from_at"
                                    class="flex flex-col items-center justify-center
                                           text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('show') }}: {{ shop.show_from_at }} / {{ shop.show_to_at }}
                                </div>

                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center
                                           text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDate(shop.published_at) }}
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
                                :href="`/market/shops/${encodeURIComponent(shop.url)}`"
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
                                    <span>{{ t('contacts') }}:</span>
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
                                <div class="font-semibold text-center
                                            text-fuchsia-700 dark:text-fuchsia-300">
                                    <span>{{ t('status') }}: </span>
                                    {{ getStatusLabel(shop.status) }}
                                </div>
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(shop.moderation_status).class"
                                    :title="shop.moderation_note && shop.moderated_at
                                        ? `${shop.moderation_note} [${formatDate(shop.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(shop.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="shop?.moderation_status ?? 0"
                                    :initialNote="shop?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) => $emit('approve', shop, status, note)"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="shop.left"
                                    @toggle-left="$emit('toggle-left', shop)"
                                    :title="shop.left ? t('enabled') : t('disabled')"
                                />

                                <MainToggle
                                    :isActive="shop.main"
                                    @toggle-main="$emit('toggle-main', shop)"
                                    :title="shop.main ? t('enabled') : t('disabled')"
                                />

                                <RightToggle
                                    :isActive="shop.right"
                                    @toggle-right="$emit('toggle-right', shop)"
                                    :title="shop.right ? t('enabled') : t('disabled')"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="shop.activity"
                                    @toggle-activity="$emit('toggle-activity', shop)"
                                    :title="shop.activity ? t('enabled') : t('disabled')"
                                />

                                <IconEdit
                                    :href="route('admin.marketShops.edit', { marketShop: shop.id })"
                                />

                                <DeleteIconButton @delete="$emit('delete', shop)" />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
