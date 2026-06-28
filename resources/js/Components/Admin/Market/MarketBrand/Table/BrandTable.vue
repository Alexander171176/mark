<script setup>
import { defineProps, defineEmits, watch, ref } from 'vue'
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

const localBrands = ref([])

watch(
    () => props.brands,
    (newVal) => {
        localBrands.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localBrands.value.map(brand => brand.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localBrands.value.map(brand => brand.id),
        checked: event.target.checked,
    })
}

const allSelected = () => {
    return localBrands.value.length
        && localBrands.value.every(brand => props.selectedBrands.includes(brand.id))
}

const brandTranslation = (brand) => brand?.translation || {}

const brandTitle = (brand) => brandTranslation(brand)?.title || `ID: ${brand?.id}`

const brandShort = (brand) => brandTranslation(brand)?.short || ''

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}
const getStatusLabel = (status) => t(statusLabelKeyMap[status] || status || 'no')

const ownerTitle = (brand) => {
    const owner = brand?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (brand) => {
    return brand?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

const logoUrl = (brand) => {
    if (!brand?.logo) {
        return '/storage/market/market_brands/logos/default-image-light.png'
    }

    return brand.logo.startsWith('/storage/')
        ? brand.logo
        : `/storage/${brand.logo}`
}

const getPrimaryImage = (brand) => {
    if (brand.images && brand.images.length) {
        return [...brand.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }

    return null
}

const imageUrl = (brand) => {
    const image = getPrimaryImage(brand)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/market/market_brand_images/default-image.png'
}

const imageAlt = (brand) => {
    const image = getPrimaryImage(brand)

    return image?.alt || brandTitle(brand)
}

const imageTitle = (brand) => {
    const image = getPrimaryImage(brand)

    return image?.caption || brandTitle(brand)
}

const brandPublicUrl = (brand) => {
    return `/market/brands/${encodeURIComponent(brand.url)}`
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

const truncateText = (text, maxLength = 50) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
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

        <div class="overflow-x-auto">
            <table
                v-if="localBrands.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-1 py-3 w-px">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="flex justify-center" :title="t('owner')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z" />
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z" />
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z" />
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z" />
                                <path
                                    d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3">
                        <div class="flex justify-center" :title="t('logo')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path d="M274.835 12.646l25.516 62.393c4.213 10.301 16.671 14.349 26.134 8.492l57.316-35.479c15.49-9.588 34.808 4.447 30.475 22.142l-16.03 65.475c-2.647 10.81 5.053 21.408 16.152 22.231l67.224 4.987c18.167 1.348 25.546 24.057 11.641 35.826L441.81 242.26c-8.495 7.19-8.495 20.289 0 27.479l51.454 43.548c13.906 11.769 6.527 34.478-11.641 35.826l-67.224 4.987c-11.099.823-18.799 11.421-16.152 22.231l16.03 65.475c4.332 17.695-14.986 31.73-30.475 22.142l-57.316-35.479c-9.463-5.858-21.922-1.81-26.134 8.492l-25.516 62.393c-6.896 16.862-30.774 16.862-37.67 0l-25.516-62.393c-4.213-10.301-16.671-14.349-26.134-8.492l-57.317 35.479c-15.49 9.588-34.808-4.447-30.475-22.142l16.03-65.475c2.647-10.81-5.053-21.408-16.152-22.231l-67.224-4.987c-18.167-1.348-25.546-24.057-11.641-35.826L70.19 269.74c8.495-7.19 8.495-20.289 0-27.479l-51.454-43.548c-13.906-11.769-6.527-34.478 11.641-35.826l67.224-4.987c11.099-.823 18.799-11.421 16.152-22.231l-16.03-65.475c-4.332-17.695 14.986-31.73 30.475-22.142l57.317 35.479c9.463 5.858 21.921 1.81 26.134-8.492l25.516-62.393c6.896-16.861 30.774-16.861 37.67 0zM392 256c0-74.991-61.01-136-136-136-74.991 0-136 61.009-136 136s61.009 136 136 136c74.99 0 136-61.009 136-136zm-32 0c0 57.346-46.654 104-104 104s-104-46.654-104-104 46.654-104 104-104 104 46.654 104 104z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('brand') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('site') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('show') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('status') }}</div>
                    </th>
                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                    <th class="px-1 py-1 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            :checked="allSelected()"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localBrands"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: brand }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-1 py-1 text-center cursor-move handle w-px">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                                    :title="`[${brand.sort}] / ${formatDate(brand.published_at)}`"
                                >
                                    {{ brand.id }}
                                </div>
                            </td>
                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(brand)"
                                        :title="ownerTitle(brand)"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                        :alt="t('owner')"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 w-12">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(brand)"
                                        :alt="imageAlt(brand)"
                                        :title="imageTitle(brand)"
                                        class="h-8 w-12 object-cover rounded-sm
                                               border border-slate-300 dark:border-slate-600"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 w-12">
                                <div class="flex justify-center">
                                    <img
                                        :src="logoUrl(brand)"
                                        :alt="brandTitle(brand)"
                                        :title="brandTitle(brand)"
                                        class="h-8 w-12 object-cover rounded-sm
                                               border border-slate-300 dark:border-slate-600"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1">
                                <div class="text-left">
                                    <a
                                        :href="brandPublicUrl(brand)"
                                        class="text-blue-700 dark:text-blue-300
                                               text-sm hover:underline
                                               hover:text-amber-700 dark:hover:text-amber-200"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :title="brand.show_from_at
                                    ? `${t('show')}: ${brand.show_from_at} / ${brand.show_to_at}`
                                    : `${formatDate(brand.published_at)}`"
                                    >
                                        {{ truncateText(brandTitle(brand), 70) }}
                                    </a>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-300">
                                        {{ truncateText(brand.url, 70) }}
                                    </div>
                                    <div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ getStatusLabel(brand.status) }}
                                    </div>
                                    <div
                                        v-if="brandShort(brand)"
                                        class="text-[10px] text-slate-500 dark:text-slate-300"
                                    >
                                        {{ truncateText(brandShort(brand), 80) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-left text-xs">
                                    <a
                                        v-if="brand.website"
                                        :href="brand.website"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-amber-700 dark:text-amber-300 hover:underline"
                                    >
                                        {{ truncateText(brand.website, 45) }}
                                    </a>
                                    <span
                                        v-else
                                        class="text-slate-400 dark:text-slate-300"
                                    >
                                        —
                                    </span>
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center text-blue-600 dark:text-blue-300">
                                    {{ brand.views }}
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <LeftToggle
                                        :isActive="brand.left"
                                        @toggle-left="$emit('toggle-left', brand)"
                                        :title="brand.left ? t('enabled') : t('disabled')"
                                    />

                                    <MainToggle
                                        :isActive="brand.main"
                                        @toggle-main="$emit('toggle-main', brand)"
                                        :title="brand.main ? t('enabled') : t('disabled')"
                                    />

                                    <RightToggle
                                        :isActive="brand.right"
                                        @toggle-right="$emit('toggle-right', brand)"
                                        :title="brand.right ? t('enabled') : t('disabled')"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
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
                                        @submit="({ status, note }) => $emit('approve', brand, status, note)"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-1">
                                    <ActivityToggle
                                        :isActive="brand.activity"
                                        @toggle-activity="$emit('toggle-activity', brand)"
                                        :title="brand.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit
                                        :href="route('admin.marketBrands.edit',
                                        { marketBrand: brand.id })"
                                    />

                                    <DeleteIconButton @delete="$emit('delete', brand)" />
                                </div>
                            </td>
                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedBrands.includes(brand.id)"
                                        @change="$emit('toggle-select', brand.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
