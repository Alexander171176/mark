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
    articles: { type: Array, default: () => [] },
    selectedArticles: { type: Array, default: () => [] },
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

const localArticles = ref([])

watch(
    () => props.articles,
    (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localArticles.value.map(article => article.id))
}

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localArticles.value.map(article => article.id),
        checked: event.target.checked,
    })
}

const allSelected = () => {
    return localArticles.value.length
        && localArticles.value.every(article => props.selectedArticles.includes(article.id))
}

const articleTranslation = (article) => article?.translation || {}

const articleTitle = (article) => articleTranslation(article)?.title || `ID: ${article?.id}`

const rubricTitle = (rubric) => {
    return rubric?.title || rubric?.translation?.title || `ID: ${rubric?.id}`
}

const getPrimaryImage = (article) => {
    if (article.images && article.images.length) {
        return [...article.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }

    return null
}

const imageUrl = (article) => {
    const image = getPrimaryImage(article)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/blog/blog_article_images/default-image.png'
}

const imageAlt = (article) => {
    const image = getPrimaryImage(article)

    return image?.alt || t('defaultImageAlt')
}

const imageTitle = (article) => {
    const image = getPrimaryImage(article)

    return image?.caption || t('postImage')
}

const ownerTitle = (article) => {
    const owner = article?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (article) => {
    return article?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 ' +
                'border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 ' +
                'border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 ' +
            'border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
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

const truncateText = (text, maxLength = 30) => {
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
                {{ t('selected') }}: {{ selectedArticles.length }}
            </div>

            <label
                v-if="localArticles.length"
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
                v-if="localArticles.length"
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
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path>
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path>
                        </svg>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px">
                        <div class="flex justify-center" :title="t('owner')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path>
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path>
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path>
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path>
                                <path
                                    d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('title') }}</div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('rubrics')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                <circle class="fill-current text-cyan-600" cx="16" cy="8" r="8"></circle>
                                <circle class="fill-current text-cyan-400" cx="8" cy="16" r="8"></circle>
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('likes')">
                            <svg class="h-7 w-7 fill-current" viewBox="0 0 32 32">
                                <path
                                    class="fill-current text-red-600 dark:text-red-300"
                                    d="M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('show') }}</div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('status') }}</div>
                    </th>
                    <th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
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
                    v-model="localArticles"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: article }">
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
                                <div class="text-center text-blue-600 dark:text-blue-200"
                                :title="`[${article.sort}] / ${formatDate(article.published_at)}`">
                                    {{ article.id }}
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(article)"
                                        :title="ownerTitle(article)"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                        :alt="t('author')"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(article)"
                                        :alt="imageAlt(article)"
                                        :title="imageTitle(article)"
                                        class="h-6 w-8 object-cover rounded-sm"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="text-left">
                                    <a
                                        :href="`/blog/articles/${encodeURIComponent(article.url)}`"
                                        class="text-sky-700 dark:text-sky-200
                                               text-xs hover:underline
                                               hover:text-amber-700 dark:hover:text-amber-200"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :title="article.show_from_at
                                            ? `${t('show')}: ${article.show_from_at} / ${article.show_to_at}`
                                            : `${formatDate(article.published_at)}`"
                                    >
                                        {{ truncateText(articleTitle(article), 90) }}
                                    </a>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-left">
                                    <span
                                        v-for="rubric in article.rubrics"
                                        :key="rubric.id"
                                        :title="rubricTitle(rubric)"
                                        class="py-0.5 px-1.5 mr-0.5
                                               badge bg-indigo-500 dark:bg-indigo-200
                                               rounded-sm text-xs
                                               text-slate-100 dark:text-slate-900"
                                    >
                                        {{ rubric.id }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">{{ article.views }}</div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">{{ article.likes_count }}</div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <LeftToggle
                                        :isActive="article.left"
                                        @toggle-left="$emit('toggle-left', article)"
                                        :title="article.left ? t('enabled') : t('disabled')"
                                    />

                                    <MainToggle
                                        :isActive="article.main"
                                        @toggle-main="$emit('toggle-main', article)"
                                        :title="article.main ? t('enabled') : t('disabled')"
                                    />

                                    <RightToggle
                                        :isActive="article.right"
                                        @toggle-right="$emit('toggle-right', article)"
                                        :title="article.right ? t('enabled') : t('disabled')"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <span
                                        class="text-[10px] px-2 py-1 rounded-sm
                                               border font-semibold"
                                        :class="moderationBadge(article.moderation_status).class"
                                        :title="article.moderation_note && article.moderated_at
                                            ? `${article.moderation_note} [${formatDate(article.moderated_at)}]`
                                            : null"
                                    >
                                        {{ moderationBadge(article.moderation_status).text }}
                                    </span>

                                    <ModerationButton
                                        :isAdmin="isAdmin"
                                        :status="article?.moderation_status ?? 0"
                                        :initialNote="article?.moderation_note || ''"
                                        mode="toggle"
                                        @submit="({ status, note }) => $emit('approve', article, status, note)"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-1">
                                    <ActivityToggle
                                        :isActive="article.activity"
                                        @toggle-activity="$emit('toggle-activity', article)"
                                        :title="article.activity ? t('enabled') : t('disabled')"
                                    />

                                    <IconEdit
                                        :href="route('admin.blogArticles.edit', { blogArticle: article.id })"
                                    />

                                    <DeleteIconButton @delete="$emit('delete', article)" />
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedArticles.includes(article.id)"
                                        @change="$emit('toggle-select', article.id)"
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
