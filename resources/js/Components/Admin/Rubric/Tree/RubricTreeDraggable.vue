<script setup>
import { defineOptions, defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'
import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

defineOptions({ name: 'RubricTreeDraggable' })

/** Входящие пропсы */
const props = defineProps({
    rubric: Object,
    level: Number,
    selectedRubrics: Array,
    isAdmin: Boolean,
})

/** обработчики */
const emits = defineEmits([
    'toggle-activity',
    'delete',
    'clone',
    'toggle-select',
    'request-drag-end',
    'approve',
])

/** состояние аккордеона */
const isExpanded = ref(true)

watch(() => props.rubric, () => {}, { deep: true })
watch(() => props.selectedRubrics, () => {}, { deep: true })

/** draggable */
const handleInnerDragEnd = (event) => {
    emits('request-drag-end', event)
}

/** свёртывание/развёртывание аккордеона */
const toggleExpand = () => {
    isExpanded.value = !isExpanded.value
}

/** Главное изображение (минимальный order) */
const getPrimaryImage = (rubric) => {
    if (rubric?.images && rubric.images.length) {
        return [...rubric.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }
    return null
}

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d)) return ''
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

/** title изображения автора (имя, email) */
const ownerTitle = (rubric) => {
    const o = rubric?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** бейджи модерации */
const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300'
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300'
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300'
    }
}

</script>

<template>
    <div>
        <!-- Элемент рубрики -->
        <div class="category-item mb-1" :style="{ marginLeft: level * 20 + 'px' }">
            <div class="flex items-center justify-between py-1 px-2
                        border border-gray-400 rounded-sm
                        bg-white dark:bg-slate-600
                        hover:bg-slate-50 dark:hover:bg-slate-700
                        transition duration-150 ease-in-out">

                <div class="flex items-center space-x-2 flex-grow min-w-0">

                  <span class="handle cursor-move mr-1 flex-shrink-0" :title="t('dragDrop')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4">
                      <path class="fill-current text-sky-500 dark:text-sky-200"
                            d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 96-96 0 0-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6l0-32 96 0 0 96-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-96 96 0 0 32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6l0 32-96 0 0-96 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"/>
                    </svg>
                  </span>

                    <button
                        v-if="rubric.children && rubric.children.length"
                        :title="isExpanded ? t('collapse') : t('expand')"
                        @click="toggleExpand"
                        class="flex-shrink-0 text-slate-900 hover:text-red-500
                               dark:text-slate-100 dark:hover:text-red-200"
                    >
                        <svg class="w-5 h-5 transform transition-transform duration-150"
                             :class="{ 'rotate-90': isExpanded }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <span v-else class="w-4 h-4 inline-block flex-shrink-0"></span>
                    <div class="w-8 font-semibold text-sm
                                text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0">
                        {{ rubric.id }}
                    </div>

                    <img
                        v-if="rubric?.owner?.profile_photo_url"
                        :src="rubric.owner.profile_photo_url"
                        :title="ownerTitle(rubric)"
                        class="h-6 w-6 rounded-full object-cover
                               border border-slate-300 dark:border-slate-600"
                        :alt="t('author')"
                    />

                    <div
                        v-if="rubric.icon"
                        class="pl-3 w-6 h-6 text-slate-700 dark:text-slate-100
                               flex items-center justify-center"
                        v-html="rubric.icon"
                    />
                    <svg
                        v-else
                        class="w-4 h-4 text-slate-500 dark:text-slate-300"
                        fill="currentColor"
                        viewBox="0 0 16 16"
                    >
                        <path
                            d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 014 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"
                        />
                    </svg>

                    <a
                        :href="`/blog/rubrics/${encodeURIComponent(rubric.url)}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm font-semibold
                               text-sky-700 dark:text-sky-200
                               hover:text-amber-700 dark:hover:text-amber-200
                               hover:underline line-clamp-2 text-center"
                        :title="rubric.title"
                    >
                        {{ rubric.title }}
                    </a>
                    <!-- Кол-во статей -->
                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm
                               border border-slate-300 dark:border-slate-600
                               bg-slate-100 dark:bg-slate-700
                               text-slate-700 dark:text-slate-200"
                        :title="`${t('articles')}: ${rubric.articles_count ?? 0}`"
                    >
                        {{ rubric.articles_count ?? 0 }}
                    </span>
                </div>

                <!-- Правая часть -->
                <div class="flex items-center space-x-1 flex-shrink-0 ml-4">
                    <!-- Количество просмотров -->
                    <div class="flex items-center gap-1">
                        <!-- Views -->
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                            <path class="fill-current text-blue-600 dark:text-blue-300"
                                  d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                        </svg>
                        <div class="w-7 font-semibold text-[10px]
                                    text-slate-700 dark:text-slate-200"
                              :title="t('views')">
                            {{ rubric.views ?? 0 }}
                        </div>
                    </div>
                    <!-- Локаль -->
                    <div
                        class="text-xs ml-1 px-1.5 py-0.5 rounded-sm
                               border border-gray-400 flex-shrink-0"
                        :class="rubric.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                    >
                        {{ (rubric.locale || '').toUpperCase() }}
                    </div>
                    <!-- Изображение статьи -->
                    <div class="flex justify-center">
                        <template v-if="rubric.images && rubric.images.length">
                            <img
                                :src="getPrimaryImage(rubric)?.webp_url || getPrimaryImage(rubric)?.url"
                                :alt="getPrimaryImage(rubric)?.alt || t('defaultImageAlt')"
                                :title="getPrimaryImage(rubric)?.caption || t('postImage')"
                                class="h-6 w-8 object-cover rounded-sm
                                       border border-slate-400 dark:border-slate-200 p-0.5"
                            >
                        </template>
                        <template v-else>
                            <img
                                src="/storage/rubric_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="h-6 w-8 object-cover rounded-sm
                                       border border-slate-400 dark:border-slate-200 p-0.5"
                            >
                        </template>
                    </div>
                    <!-- Статус Модерации -->
                    <div class="flex justify-center">
                          <span
                              class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                              :class="moderationBadge(rubric.moderation_status).class"
                              :title="rubric.moderation_note && rubric.moderated_at
                                ? `${rubric.moderation_note} [${formatDate(rubric.moderated_at)}]`
                                : null"
                          >
                            {{ moderationBadge(rubric.moderation_status).text }}
                          </span>
                    </div>
                    <!-- Модерация (только админ) -->
                    <ModerationButton
                        :isAdmin="isAdmin"
                        :status="rubric?.moderation_status ?? 0"
                        :initialNote="rubric?.moderation_note || ''"
                        mode="toggle"
                        @submit="({ status, note }) => $emit('approve', rubric, status, note)"
                    />
                    <ActivityToggle
                        :isActive="rubric.activity"
                        @toggle-activity="$emit('toggle-activity', rubric)"
                        :title="rubric.activity ? t('enabled') : t('disabled')"
                    />
                    <CloneIconButton @clone="$emit('clone', rubric)" />
                    <IconEdit :href="route('admin.rubrics.edit', { rubric: rubric.id })" />
                    <DeleteIconButton @click.stop="$emit('delete', rubric)" />
                    <div class="pl-1.5">
                        <input
                            type="checkbox"
                            :checked="selectedRubrics.includes(rubric.id)"
                            @change="$emit('toggle-select', rubric.id)"
                            class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Дочерние элементы -->
        <div
            v-show="isExpanded && rubric.children && rubric.children.length"
            class="children-container mt-1"
        >
            <draggable
                v-model="rubric.children"
                tag="div"
                item-key="id"
                handle=".handle"
                group="rubrics"
                @end="handleInnerDragEnd"
                class="category-tree-children"
                :data-parent-id="rubric.id"
            >
                <template #item="{ element: childRubric }">
                    <RubricTreeDraggable
                        :rubric="childRubric"
                        :level="level + 1"
                        :selected-rubrics="selectedRubrics"
                        :is-admin="isAdmin"
                        @toggle-activity="(p) => $emit('toggle-activity', p)"
                        @delete="(p) => $emit('delete', p)"
                        @clone="(p) => $emit('clone', p)"
                        @toggle-select="(id) => $emit('toggle-select', id)"
                        @request-drag-end="handleInnerDragEnd"
                        @approve="(rub, status, note) => $emit('approve', rub, status, note)"
                    />
                </template>
            </draggable>
        </div>
    </div>
</template>
