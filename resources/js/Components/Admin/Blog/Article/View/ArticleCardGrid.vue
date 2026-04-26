<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'

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

const articleShort = (article) => articleTranslation(article)?.short || ''

const articleLocale = (article) => articleTranslation(article)?.locale || ''

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
        || '/storage/blog_article_images/default-image.png'
}

const imageAlt = (article) => {
    const image = getPrimaryImage(article)

    return image?.alt || t('defaultImageAlt')
}

const imageTitle = (article) => {
    const image = getPrimaryImage(article)

    return image?.caption || t('postImage')
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

const ownerName = (article) => article?.owner?.name || t('noData')
const ownerEmail = (article) => article?.owner?.email || ''

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

        <div v-if="localArticles.length" class="p-3">
            <draggable
                tag="div"
                v-model="localArticles"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: article }">
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
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${articleLocale(article)}] : [${article.sort}] ${formatDate(article.published_at)}`"
                                >
                                    ID: {{ article.id }}
                                </div>

                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="article.activity
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ articleLocale(article).toUpperCase() }}
                                </div>
                            </div>

                            <input
                                type="checkbox"
                                :checked="selectedArticles.includes(article.id)"
                                @change="$emit('toggle-select', article.id)"
                            />
                        </header>

                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <img
                                :src="imageUrl(article)"
                                :alt="imageAlt(article)"
                                :title="imageTitle(article)"
                                class="h-32 w-full object-cover"
                            />
                        </div>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div class="flex flex-col items-center justify-center text-center">
                                <img
                                    :src="ownerAvatar(article)"
                                    :title="ownerTitle(article)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('author')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(article)"
                                >
                                    {{ ownerName(article) }}
                                </div>

                                <div
                                    v-if="ownerEmail(article)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(article)"
                                >
                                    {{ ownerEmail(article) }}
                                </div>
                            </div>

                            <div
                                v-if="article.show_from_at"
                                class="flex flex-col items-center justify-center
                                       text-center text-[10px] text-slate-500 dark:text-slate-300"
                            >
                                {{ t('show') }}: {{ article.show_from_at }} / {{ article.show_to_at }}
                            </div>

                            <div
                                v-else
                                class="flex flex-col items-center justify-center
                                       text-center text-[10px] text-slate-500 dark:text-slate-300"
                            >
                                {{ formatDate(article.published_at) }}
                            </div>

                            <a
                                :href="`/blog/articles/${encodeURIComponent(article.url)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-xs font-semibold
                                       text-sky-700 dark:text-sky-200 hover:underline
                                       hover:text-amber-700 dark:hover:text-amber-200
                                       line-clamp-2 text-center"
                            >
                                {{ truncateText(articleTitle(article), 90) }}
                            </a>

                            <div
                                class="flex items-center justify-between text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <span
                                    v-if="(article.views ?? 0) > 0"
                                    class="flex flex-row items-center"
                                    :title="t('views')"
                                >
                                    👁 <span class="ml-1">{{ article.views }}</span>
                                </span>

                                <span
                                    v-if="(article.likes_count ?? 0) > 0"
                                    :title="t('likes')"
                                >
                                    ❤ {{ article.likes_count }}
                                </span>
                            </div>

                            <div
                                class="font-semibold text-[12px] text-center
                                       text-teal-700 dark:text-teal-200"
                            >
                                {{ truncateText(articleShort(article), 120) }}
                            </div>

                            <div class="flex flex-col justify-center">
                                <div
                                    class="text-center text-[11px] font-medium
                                           text-slate-600 dark:text-slate-300 mb-0.5"
                                >
                                    {{ t('rubrics') }}:
                                </div>

                                <div
                                    v-if="article.rubrics && article.rubrics.length"
                                    class="flex justify-center flex-wrap gap-1"
                                >
                                    <span
                                        v-for="rubric in article.rubrics"
                                        :key="rubric.id"
                                        :title="rubricTitle(rubric)"
                                        class="py-0.5 px-1.5 badge
                                               bg-indigo-500 dark:bg-indigo-200
                                               rounded-sm text-[10px]
                                               text-slate-100 dark:text-slate-900"
                                    >
                                        {{ rubric.id }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
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
                        </div>

                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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

                            <div class="flex items-center space-x-1">
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
