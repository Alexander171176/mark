<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    modules: {
        type: Array,
        default: () => []
    },

    selectedModules: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

const localModules = ref([])

watch(
    () => props.modules,
    (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const moduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived'
}

const moduleAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate'
}

const getModuleStatusLabel = (status) => {
    return t(moduleStatusLabelKeyMap[status] || status || 'no')
}

const getModuleAvailabilityLabel = (availability) => {
    return t(moduleAvailabilityLabelKeyMap[availability] || availability || 'no')
}

const handleDragEnd = () => {
    const newOrderIds = localModules.value.map(module => module.id)
    emit('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localModules.value.map(module => module.id)

    emit('toggle-all', { ids, checked })
}

const getPrimaryImage = (module) => {
    if (module.images && module.images.length) {
        return [...module.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }

    return null
}

const getCourseTitle = (module) => {
    return module?.course?.title || `ID: ${module?.school_course_id || '-'}`
}

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
                {{ t('selected') }}: {{ selectedModules.length }}
            </div>

            <label
                v-if="localModules.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localModules.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0"
                                 viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('module') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('status') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('duration')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path
                                    class="fill-current
                                           text-violet-700 dark:text-violet-300"
                                    d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('lessonsCount')">
                            <svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24">
                                <path class="fill-current text-cyan-600 dark:text-cyan-30"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                      d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('rating')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('likes')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localModules"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: module }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[sort: ${module.sort}] ${formatDate(module.published_at)}`"
                                >
                                    {{ module.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center"
                                     :title="module.title || ''">
                                    <template v-if="module.images && module.images.length">
                                        <img
                                            :src="getPrimaryImage(module)?.webp_url || getPrimaryImage(module)?.url"
                                            :alt="getPrimaryImage(module)?.alt || t('defaultImageAlt')"
                                            :title="getPrimaryImage(module)?.caption || t('currentImage')"
                                            class="h-8 w-12 object-cover rounded-sm"
                                        />
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/school_module_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-8 w-12 object-cover rounded-sm"
                                        />
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="min-w-0">
                                    <a
                                        :href="`/school/modules/${encodeURIComponent(module.slug)}`"
                                        class="text-xs text-sky-600 dark:text-sky-200
                                               hover:underline hover:text-sky-600
                                               dark:hover:text-sky-200"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :title="module.subtitle || module.short"
                                    >
                                        {{ module.title || `ID: ${module.id}` }}
                                    </a>
                                    <div
                                        class="text-xs text-slate-700 dark:text-slate-200"
                                        :title="`ID: ${module.school_course_id}`"
                                    >
                                        {{ t('course') }}: {{ getCourseTitle(module) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-fuchsia-800 dark:text-fuchsia-400"
                                    :title="`${t('status')}: ${getModuleStatusLabel(module.status)}`"
                                >
                                    {{ getModuleStatusLabel(module.status) }}
                                </div>
                                <div
                                    class="text-center text-xs text-teal-600 dark:text-teal-300"
                                    :title="`${t('availability')}: ${getModuleAvailabilityLabel(module.availability)}`"
                                >
                                    {{ getModuleAvailabilityLabel(module.availability) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-violet-700 dark:text-violet-300"
                                    :title="`${t('duration')}: ${module.duration ?? 0}`"
                                >
                                    {{ module.duration ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-sky-700 dark:text-sky-300"
                                    :title="`${t('lessons')}: ${module.lessons_count ?? 0}`"
                                >
                                    {{ module.lessons_count ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-blue-700 dark:text-blue-300"
                                    :title="`${t('views')}: ${module.views ?? 0}`"
                                >
                                    {{ module.views ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <div
                                        class="text-center text-xs text-rose-500 dark:text-rose-300"
                                        :title="`${t('ratingCount')}: ${module.rating_count ?? 0}`"
                                    >
                                        {{ module.rating_avg ?? 0 }}
                                    </div>
                                    <div class="text-center text-[10px]
                                                text-slate-500 dark:text-slate-300">
                                        {{ module.rating_count ?? 0 }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs"
                                    :title="`${t('likes')}: ${module.likes ?? 0}`"
                                >
                                    {{ module.likes ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex flex-row items-center justify-end gap-1">
                                    <ActivityToggle
                                        :isActive="module.activity"
                                        :title="module.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', module)"
                                    />

                                    <IconEdit
                                        :href="route('admin.schoolModules.edit', { schoolModule: module.id })"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', module)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedModules.includes(module.id)"
                                        @change="emit('toggle-select', module.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
