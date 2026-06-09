<script setup>
import { defineOptions, defineProps, defineEmits, ref, watch } from 'vue'
import draggable from 'vuedraggable'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineOptions({ name: 'TrackTreeItem' })

const props = defineProps({
    track: {
        type: Object,
        required: true,
    },
    level: {
        type: Number,
        default: 0,
    },
    selectedTracks: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'request-drag-end',
])

const isExpanded = ref(true)

// Локальная копия дочерних треков
const localChildren = ref([])

watch(
    () => props.track.children,
    (children) => {
        localChildren.value = Array.isArray(children)
            ? [...children]
            : []
    },
    {
        immediate: true,
        deep: true,
    }
)

const handleInnerDragEnd = (event) => {
    emit('request-drag-end', event)
}

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value
}

const getPrimaryImage = (track) => {
    if (track?.images && track.images.length) {
        return [...track.images].sort((a, b) => {
            const aOrder = Number(a?.order ?? a?.pivot?.order ?? 0)
            const bOrder = Number(b?.order ?? b?.pivot?.order ?? 0)

            return aOrder - bOrder
        })[0]
    }

    return null
}

const trackName = (track) => {
    return track?.name || `ID: ${track?.id}`
}
</script>

<template>
    <div>
        <div
            class="category-item mb-1"
            :style="{ marginLeft: level * 20 + 'px' }"
        >
            <div
                class="flex items-center justify-between py-1 px-2
                       border border-gray-400 rounded-sm
                       bg-white dark:bg-slate-600
                       hover:bg-slate-50 dark:hover:bg-slate-700
                       transition duration-150 ease-in-out"
            >
                <div class="flex items-center space-x-2 flex-grow min-w-0">
                    <span
                        class="handle cursor-move mr-1 flex-shrink-0"
                        :title="t('dragDrop')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4">
                            <path
                                class="fill-current text-sky-500 dark:text-sky-200"
                                d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 96-96 0 0-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6l0-32 96 0 0 96-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-96 96 0 0 32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6l0 32-96 0 0-96 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"
                            />
                        </svg>
                    </span>

                    <button
                        v-if="track.children && track.children.length"
                        type="button"
                        :title="isExpanded ? t('collapse') : t('expand')"
                        @click="toggleExpand"
                        class="flex-shrink-0 text-slate-900 hover:text-red-500
                               dark:text-slate-100 dark:hover:text-red-200"
                    >
                        <svg
                            class="w-5 h-5 transform transition-transform duration-150"
                            :class="{ 'rotate-90': isExpanded }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <span v-else class="w-4 h-4 inline-block flex-shrink-0"></span>

                    <span
                        class="w-8 font-semibold text-sm
                               text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0"
                        :title="`ID: ${track.id} / sort: ${track.sort}`">
                        {{ track.id }}
                    </span>

                    <a
                        :href="`/school/tracks/${encodeURIComponent(track.slug)}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm font-semibold
                               text-sky-700 dark:text-sky-200
                               hover:text-amber-700 dark:hover:text-amber-200
                               hover:underline line-clamp-2 text-center"
                    >
                        {{ trackName(track) }}
                    </a>

                    <span
                        v-if="track.courses_count > 0"
                        class="shrink-0 px-1.5 py-0.5 rounded-sm
                               border border-slate-300 dark:border-slate-600
                               bg-slate-100 dark:bg-slate-700
                               flex items-center justify-center gap-1"
                        :title="`${t('courses')}: ${track.courses_count ?? 0}`"
                    >
                        <svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85" fill="currentColor"
                             viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                            />
                        </svg>
                        <span class="text-[10px] text-slate-700 dark:text-slate-200">
                            {{ track.courses_count ?? 0 }}
                        </span>
                    </span>

                    <span
                        v-if="track.children_count > 0"
                        class="shrink-0 px-1.5 py-0.5 rounded-sm
                               border border-slate-300 dark:border-slate-600
                               bg-slate-100 dark:bg-slate-700
                               flex items-center justify-center gap-1"
                        :title="`${t('subheadings')}: ${track.children_count ?? 0}`"
                    >
                        <span class="text-[10px] text-slate-700 dark:text-slate-200">
                            {{ track.children_count ?? 0 }}
                        </span>
                    </span>
                </div>

                <div class="flex items-center space-x-1 flex-shrink-0 ml-4">
                    <div v-if="track.views > 0"
                         class="flex items-center gap-1">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                            <path
                                class="fill-current text-blue-600 dark:text-blue-300"
                                d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                            />
                        </svg>

                        <div
                            class="w-7 font-semibold text-[10px] text-slate-700 dark:text-slate-200"
                            :title="t('views')"
                        >
                            {{ track.views ?? 0 }}
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <template v-if="track.images && track.images.length">
                            <img
                                :src="getPrimaryImage(track)?.webp_url || getPrimaryImage(track)?.url"
                                :alt="getPrimaryImage(track)?.alt || t('defaultImageAlt')"
                                :title="getPrimaryImage(track)?.caption || t('postImage')"
                                class="h-6 w-8 object-cover rounded-sm
                                       border border-slate-400 dark:border-slate-200 p-0.5"
                            >
                        </template>

                        <template v-else>
                            <img
                                src="/storage/school_track_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="h-6 w-8 object-cover rounded-sm
                                       border border-slate-400 dark:border-slate-200 p-0.5"
                            >
                        </template>
                    </div>

                    <ActivityToggle
                        :isActive="track.activity"
                        @toggle-activity="$emit('toggle-activity', track)"
                        :title="track.activity ? t('enabled') : t('disabled')"
                    />

                    <IconEdit
                        :href="route('admin.schoolTracks.edit', {
                            schoolTrack: track.id
                        })"
                    />

                    <DeleteIconButton @click.stop="$emit('delete', track)" />

                    <div class="pl-1.5">
                        <input
                            type="checkbox"
                            :checked="selectedTracks.includes(track.id)"
                            @change="$emit('toggle-select', track.id)"
                            class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div
            v-show="isExpanded && localChildren.length"
            class="children-container mt-1"
        >
            <draggable
                v-model="localChildren"
                tag="div"
                item-key="id"
                handle=".handle"
                group="tracks"
                @end="handleInnerDragEnd"
                class="category-tree-children"
                :data-parent-id="track.id"
            >
                <template #item="{ element: childTrack }">
                    <TrackTreeItem
                        :track="childTrack"
                        :level="level + 1"
                        :selected-tracks="selectedTracks"
                        @toggle-activity="(item) => $emit('toggle-activity', item)"
                        @delete="(item) => $emit('delete', item)"
                        @toggle-select="(id) => $emit('toggle-select', id)"
                        @request-drag-end="handleInnerDragEnd"
                    />
                </template>
            </draggable>
        </div>
    </div>
</template>
