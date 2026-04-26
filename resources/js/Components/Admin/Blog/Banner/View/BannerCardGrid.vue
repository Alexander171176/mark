<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'
import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'

const { t } = useI18n()

const props = defineProps({
    banners: { type: Array, default: () => [] },
    selectedBanners: { type: Array, default: () => [] },
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

const localBanners = ref([])

watch(
    () => props.banners,
    (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localBanners.value.map((banner) => banner.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localBanners.value.map((banner) => banner.id),
        checked: event.target.checked,
    })
}

const allSelected = () => {
    return localBanners.value.length
        && localBanners.value.every((banner) => props.selectedBanners.includes(banner.id))
}

const bannerTranslation = (banner) => banner?.translation || {}

const bannerTitle = (banner) => bannerTranslation(banner)?.title || `ID: ${banner?.id}`

const bannerShort = (banner) => bannerTranslation(banner)?.short || ''

const bannerLink = (banner) => bannerTranslation(banner)?.link || ''

const bannerLocale = (banner) => bannerTranslation(banner)?.locale || ''

const truncateText = (text, maxLength = 90) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

const getPrimaryImage = (banner) => {
    if (banner.images && banner.images.length) {
        return [...banner.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }

    return null
}

const imageUrl = (banner) => {
    const image = getPrimaryImage(banner)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || banner?.cover_webp_url
        || banner?.cover_thumb_url
        || banner?.cover_image_url
        || '/storage/banner_images/default-image.png'
}

const imageAlt = (banner) => {
    return getPrimaryImage(banner)?.alt || t('defaultImageAlt')
}

const imageTitle = (banner) => {
    return getPrimaryImage(banner)?.caption || t('postImage')
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

const ownerName = (banner) => banner?.owner?.name || t('noData')

const ownerEmail = (banner) => banner?.owner?.email || ''

const ownerTitle = (banner) => {
    const owner = banner?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (banner) => {
    return banner?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (s === 2) {
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
                {{ t('selected') }}: {{ selectedBanners.length }}
            </div>

            <label
                v-if="localBanners.length"
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

        <div v-if="localBanners.length" class="p-3">
            <draggable
                v-model="localBanners"
                tag="div"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
                item-key="id"
                @end="handleDragEnd"
                handle=".handle"
            >
                <template #item="{ element: banner }">
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
                                    class="handle cursor-move text-slate-500 dark:text-slate-300
                                           hover:text-slate-800 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${bannerLocale(banner)}] : [${banner.sort}]`"
                                >
                                    ID: {{ banner.id }}
                                </div>

                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="banner.activity
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ bannerLocale(banner).toUpperCase() }}
                                </div>
                            </div>

                            <input
                                type="checkbox"
                                :checked="selectedBanners.includes(banner.id)"
                                @change="$emit('toggle-select', banner.id)"
                            />
                        </header>

                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <img
                                :src="imageUrl(banner)"
                                :alt="imageAlt(banner)"
                                :title="imageTitle(banner)"
                                class="h-32 w-full object-cover"
                            />
                        </div>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div class="text-center">
                                <div
                                    class="text-[14px] font-semibold
                                           text-sky-700 dark:text-sky-200
                                           line-clamp-2"
                                    :title="bannerTitle(banner)"
                                >
                                    {{ bannerTitle(banner) }}
                                </div>
                            </div>

                            <div
                                v-if="bannerShort(banner)"
                                class="text-center text-xs font-semibold
                                       text-slate-600 dark:text-slate-300 line-clamp-2"
                                :title="bannerShort(banner)"
                            >
                                {{ truncateText(bannerShort(banner)) }}
                            </div>

                            <div class="text-center min-h-[1rem]">
                                <a
                                    v-if="bannerLink(banner)"
                                    :href="bannerLink(banner)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-semibold text-xs
                                           text-violet-600 dark:text-violet-200
                                           hover:text-violet-800 dark:hover:text-violet-50
                                           hover:underline break-all"
                                >
                                    {{ bannerLink(banner) }}
                                </a>

                                <span v-else class="text-xs text-slate-400">
                                    —
                                </span>
                            </div>

                            <div class="flex flex-col items-center justify-center text-center">
                                <img
                                    :src="ownerAvatar(banner)"
                                    :title="ownerTitle(banner)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('author')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(banner)"
                                >
                                    {{ ownerName(banner) }}
                                </div>

                                <div
                                    v-if="ownerEmail(banner)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(banner)"
                                >
                                    {{ ownerEmail(banner) }}
                                </div>
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(banner.moderation_status).class"
                                    :title="banner.moderation_note && banner.moderated_at
                                        ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(banner.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="banner?.moderation_status ?? 0"
                                    :initialNote="banner?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) => $emit('approve', banner, status, note)"
                                />
                            </div>
                        </div>

                        <div
                            class="px-3 py-2 border-t border-dashed
                                   border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center justify-between space-x-2">
                                <div class="flex items-center space-x-1">
                                    <LeftToggle
                                        :isActive="banner.left"
                                        @toggle-left="$emit('toggle-left', banner)"
                                        :title="banner.left ? t('enabled') : t('disabled')"
                                    />

                                    <MainToggle
                                        :isActive="banner.main"
                                        @toggle-main="$emit('toggle-main', banner)"
                                        :title="banner.main ? t('enabled') : t('disabled')"
                                    />

                                    <RightToggle
                                        :isActive="banner.right"
                                        @toggle-right="$emit('toggle-right', banner)"
                                        :title="banner.right ? t('enabled') : t('disabled')"
                                    />
                                </div>

                                <div class="flex items-center space-x-1">
                                    <ActivityToggle
                                        :isActive="banner.activity"
                                        @toggle-activity="$emit('toggle-activity', banner)"
                                        :title="banner.activity ? t('enabled') : t('disabled')"
                                    />

                                    <IconEdit
                                        :href="route('admin.blogBanners.edit', { blogBanner: banner.id })"
                                    />

                                    <DeleteIconButton @delete="$emit('delete', banner)" />
                                </div>
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
