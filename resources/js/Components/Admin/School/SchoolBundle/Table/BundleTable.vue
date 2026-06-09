<script setup>
import { computed, defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    bundles: { type: Array, default: () => [] },
    selectedBundles: { type: Array, default: () => [] },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

const localBundles = ref([])

watch(
    () => props.bundles,
    (newVal) => {
        localBundles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const selectedCount = computed(() => props.selectedBundles.length)

const handleDragEnd = () => {
    emit('update-sort-order', localBundles.value.map(bundle => bundle.id))
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localBundles.value.map(bundle => bundle.id),
        checked: event.target.checked,
    })
}

const getPrimaryImage = (bundle) => {
    if (bundle.primary_image) {
        return bundle.primary_image
    }

    if (Array.isArray(bundle.images) && bundle.images.length) {
        return [...bundle.images].sort((a, b) => {
            return (a.order ?? 0) - (b.order ?? 0)
        })[0]
    }

    return null
}

const imageUrl = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/school_bundle_images/default-image.png'
}

const imageAlt = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.alt || bundle.title || t('defaultImageAlt')
}

const imageTitle = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.caption || bundle.title || t('defaultImageTitle')
}

const formatDate = (dateStr) => {
    if (!dateStr) return '—'

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const getCoursesCount = (bundle) => {
    if (typeof bundle.courses_count === 'number') {
        return bundle.courses_count
    }

    if (Array.isArray(bundle.courses)) {
        return bundle.courses.length
    }

    return 0
}

const getCourses = (bundle) => {
    return Array.isArray(bundle.courses) ? bundle.courses : []
}

const getCourseTitles = (bundle) => {
    return getCourses(bundle)
        .map(course => course?.title || course?.slug)
        .filter(Boolean)
}

const getCourseTitlesTooltip = (bundle) => {
    const titles = getCourseTitles(bundle)

    return titles.length ? titles.join('\n') : ''
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-200 dark:border-slate-600"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedCount }}
            </div>

            <label
                v-if="localBundles.length"
                class="flex items-center text-xs
                       text-slate-600 dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="
                        localBundles.length &&
                        localBundles.every(bundle => selectedBundles.includes(bundle.id))
                    "
                    @change="toggleAll"
                />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localBundles.length"
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
                            viewBox="0 0 24 24"
                        >
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
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('bundle') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('courses') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                      d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
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
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('prices') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                :checked="
                                    localBundles.length &&
                                    localBundles.every(bundle => selectedBundles.includes(bundle.id))
                                "
                                @change="toggleAll"
                            />
                        </div>
                    </th>
                </tr>
                </thead>
                <draggable
                    tag="tbody"
                    v-model="localBundles"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: bundle }">
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
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div
                                    class="text-center text-xs text-slate-800 dark:text-blue-200"
                                    :title="`[${bundle.sort}] ${formatDate(bundle.published_at)}`"
                                >
                                    {{ bundle.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(bundle)"
                                        :alt="imageAlt(bundle)"
                                        :title="imageTitle(bundle)"
                                        class="h-6 w-8 object-cover rounded-xs"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <a
                                    :href="`/school-bundles/${encodeURIComponent(bundle.slug)}`"
                                    class="text-xs text-sky-600 dark:text-sky-200 hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :title="bundle.subtitle || bundle.title"
                                >
                                    {{ bundle.title || `ID: ${bundle.id}` }}
                                </a>
                                <div
                                    v-if="bundle.subtitle"
                                    class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"
                                    :title="bundle.subtitle"
                                >
                                    {{ bundle.subtitle }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="text-center text-xs text-slate-700 dark:text-slate-300">
                                        {{ getCoursesCount(bundle) }}
                                    </div>
                                    <div
                                        v-if="getCourseTitles(bundle).length"
                                        class="text-[11px] leading-snug text-left
                                               text-teal-700 dark:text-teal-300"
                                        :title="getCourseTitlesTooltip(bundle)"
                                    >
                                        <ul class="space-y-0.5">
                                            <li
                                                v-for="(title, index) in getCourseTitles(bundle)"
                                                :key="index"
                                                class="relative pl-2"
                                            >
                                                <span class="absolute left-0 text-teal-500">•</span>
                                                {{ title }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div
                                        v-else
                                        class="text-[11px] leading-4 text-slate-400 text-center"
                                        :title="t('noData')"
                                    >
                                        —
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs text-blue-700 dark:text-blue-300">
                                    {{ bundle.views ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-slate-700 dark:text-slate-300 text-center text-xs">
                                    {{ bundle.likes ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-emerald-700 dark:text-emerald-300 text-center text-xs">
                                    {{ bundle.prices_count ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="bundle.activity"
                                        :title="bundle.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', bundle)"
                                    />
                                    <IconEdit
                                        :href="route('admin.schoolBundles.edit', {
                                            schoolBundle: bundle.id,
                                        })"
                                    />
                                    <DeleteIconButton
                                        :title="t('delete')"
                                        @delete="emit('delete', bundle)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedBundles.includes(bundle.id)"
                                        @change="emit('toggle-select', bundle.id)"
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
