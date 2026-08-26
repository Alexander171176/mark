<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    bundles: {
        type: Array,
        default: () => [],
    },

    selectedBundles: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/* ==========================================================
 * LOCAL DATA
 * ========================================================== */

const localBundles = ref([])

watch(
    () => props.bundles,
    (newValue) => {
        localBundles.value = JSON.parse(JSON.stringify(newValue || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * DRAG / SELECTION
 * ========================================================== */

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localBundles.value.map(bundle => bundle.id)
    )
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localBundles.value.map(bundle => bundle.id),
        checked: event.target.checked,
    })
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

const getBundleTitle = (bundle) => {
    return bundle?.translation?.title
        || `ID: ${bundle?.id ?? '—'}`
}

const getBundleSubtitle = (bundle) => {
    return bundle?.translation?.subtitle || ''
}

const getCourseTitle = (course) => {
    return course?.translation?.title
        || course?.slug
        || ''
}

const getCourses = (bundle) => {
    return Array.isArray(bundle?.courses)
        ? bundle.courses
        : []
}

const getCoursesCount = (bundle) => {
    if (typeof bundle?.courses_count === 'number') {
        return bundle.courses_count
    }

    return getCourses(bundle).length
}

const getCourseTitles = (bundle) => {
    return getCourses(bundle)
        .map(getCourseTitle)
        .filter(Boolean)
}

const getCourseTitlesTooltip = (bundle) => {
    return getCourseTitles(bundle).join('\n')
}

/* ==========================================================
 * IMAGE
 * ========================================================== */

const getPrimaryImage = (bundle) => {
    if (bundle?.primary_image) {
        return bundle.primary_image
    }

    if (Array.isArray(bundle?.images) && bundle.images.length) {
        return bundle.images[0]
    }

    return null
}

const imageUrl = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/school/school_bundle_images/default-image.png'
}

const imageAlt = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.alt
        || getBundleTitle(bundle)
        || t('defaultImageAlt')
}

const imageTitle = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.caption
        || getBundleTitle(bundle)
        || t('defaultImageTitle')
}

/* ==========================================================
 * DATE
 * ========================================================== */

const formatDate = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
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
                {{ t('selected') }}: {{ selectedBundles.length }}
            </div>

            <label
                v-if="localBundles.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="localBundles.length &&
                        localBundles.every(bundle => selectedBundles.includes(bundle.id))"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div v-if="localBundles.length" class="p-3">
            <draggable
                tag="div"
                v-model="localBundles"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: bundle }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Header -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5
                                           rounded-sm border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${bundle.sort}] ${formatDate(bundle.published_at)}`"
                                >
                                    ID: {{ bundle.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-sky-100
                                           dark:bg-sky-900/50
                                           text-sky-700 dark:text-sky-300"
                                    :title="t('courses')"
                                >
                                    {{ t('courses') }}: {{ getCoursesCount(bundle) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedBundles.includes(bundle.id)"
                                    @change="emit('toggle-select', bundle.id)"
                                />
                            </div>
                        </header>

                        <!-- Image -->
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <img
                                :src="imageUrl(bundle)"
                                :alt="imageAlt(bundle)"
                                :title="imageTitle(bundle)"
                                class="w-full h-full object-cover"
                            />
                        </div>

                        <!-- Body -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <a
                                :href="`/school-bundles/${encodeURIComponent(bundle.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700
                                       dark:text-sky-300 hover:underline
                                       line-clamp-2 text-center"
                                :title="getBundleSubtitle(bundle) || getBundleTitle(bundle)"
                            >
                                {{ getBundleTitle(bundle) }}
                            </a>

                            <div
                                v-if="getBundleSubtitle(bundle)"
                                class="text-[11px] text-slate-600 dark:text-slate-200
                                       text-center font-semibold line-clamp-2"
                                :title="getBundleSubtitle(bundle)"
                            >
                                {{ getBundleSubtitle(bundle) }}
                            </div>

                            <!-- Courses -->
                            <div
                                v-if="getCourseTitles(bundle).length"
                                class="py-1 font-semibold text-[11px] text-left
                                       text-slate-900 dark:text-slate-100
                                       border border-dashed border-slate-400"
                                :title="getCourseTitlesTooltip(bundle)"
                            >
                                <div
                                    class="mb-1 text-[13px] text-center font-semibold
                                           text-teal-700 dark:text-teal-300"
                                >
                                    {{ t('courses') }}:
                                </div>

                                <ul class="space-y-0.5 max-h-20 overflow-auto pr-1">
                                    <li
                                        v-for="(title, index) in getCourseTitles(bundle)"
                                        :key="index"
                                        class="leading-snug pl-2 relative"
                                    >
                                        <span class="absolute left-0 text-red-400">•</span>
                                        {{ title }}
                                    </li>
                                </ul>
                            </div>

                            <div
                                v-else
                                class="mt-1 text-[11px] text-center text-slate-400"
                                :title="t('noData')"
                            >
                                {{ t('courses') }}: —
                            </div>

                            <!-- Stats -->
                            <div
                                class="flex flex-wrap justify-center gap-3 mt-4
                                       text-[11px] text-slate-900 dark:text-slate-200"
                            >
                                <div>
                                    <span class="font-semibold text-gray-500 dark:text-gray-400">
                                        {{ t('views') }}:
                                    </span>

                                    <span class="font-semibold text-blue-700 dark:text-blue-300">
                                        {{ bundle.views ?? 0 }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-500 dark:text-gray-400">
                                        {{ t('likes') }}:
                                    </span>

                                    <span class="font-semibold text-red-700 dark:text-red-300">
                                        {{ bundle.likes ?? 0 }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold text-gray-500 dark:text-gray-400">
                                        {{ t('prices') }}:
                                    </span>

                                    <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                                        {{ bundle.prices_count ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <footer
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
                        </footer>
                    </article>
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
