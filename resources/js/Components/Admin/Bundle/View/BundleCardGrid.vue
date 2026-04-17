<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    bundles: {
        type: Array,
        default: () => []
    },
    selectedBundles: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

/** Локальная копия для vuedraggable */
const localBundles = ref([])

watch(
    () => props.bundles,
    (newVal) => {
        localBundles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localBundles.value.map(b => b.id)
    emits('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localBundles.value.map(b => b.id)
    emits('toggle-all', { ids, checked })
}

const getPrimaryImage = (bundle) => {
    if (bundle.images && bundle.images.length) {
        return [...bundle.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d)) return ''
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getCoursesCount = (bundle) => {
    if (typeof bundle.courses_count === 'number') return bundle.courses_count
    if (Array.isArray(bundle.courses)) return bundle.courses.length
    return 0
}

/** Курсы бандла */
const getCourses = (bundle) => {
    if (Array.isArray(bundle.courses)) return bundle.courses
    if (Array.isArray(bundle.bundle_courses)) return bundle.bundle_courses
    return []
}

/** Названия курсов */
const getCourseTitles = (bundle) => {
    return getCourses(bundle)
        .map(c => c?.title)
        .filter(Boolean)
}

/** Tooltip со всеми курсами */
const getCourseTitlesTooltip = (bundle) => {
    return getCourseTitles(bundle).join('\n')
}

/** Превью: первые N + "+ ещё" */
const getCourseTitlesPreview = (bundle, limit = 3) => {
    const titles = getCourseTitles(bundle)
    if (!titles.length) return ''
    if (titles.length <= limit) return titles.join(', ')
    return `${titles.slice(0, limit).join(', ')} +${titles.length - limit}`
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative">

        <!-- верх: выбранные + selectAll -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedBundles.length }}
            </div>

            <label
                v-if="localBundles.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localBundles.length" class="p-3">
            <draggable
                tag="div"
                v-model="localBundles"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: bundle }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150">

                        <!-- Верх карточки -->
                        <div class="flex items-center justify-between px-2 py-1
                                    border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                :title="`[${bundle.locale}] : [${bundle.sort}] ${formatDate(bundle.published_at)}`">
                                    ID: {{ bundle.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-green-100 dark:bg-green-900/50
                                           text-green-700 dark:text-green-300"
                                    :title="t('courses')">
                                    {{ t('courses') }}: {{ getCoursesCount(bundle) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedBundles.includes(bundle.id)"
                                    @change="$emit('toggle-select', bundle.id)"
                                />
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <template v-if="bundle.images?.length">
                                <img
                            :src="getPrimaryImage(bundle)?.webp_url ||
                            getPrimaryImage(bundle)?.url"
                            :alt="getPrimaryImage(bundle)?.alt ||
                            t('defaultImageAlt')"
                            :title="getPrimaryImage(bundle)?.caption ||
                            t('defaultImageTitle')"
                            class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/bundle_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <a
                                :href="`/bundles/${encodeURIComponent(bundle.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-300
                                       hover:underline line-clamp-2 text-center"
                                :title="bundle.subtitle || bundle.title"
                            >
                                {{ bundle.title }}
                            </a>

                            <div
                                v-if="bundle.subtitle"
                                class="text-[11px] text-slate-600 dark:text-slate-200 text-center
                                       font-semibold line-clamp-2"
                                :title="bundle.subtitle"
                            >
                                {{ bundle.subtitle }}
                            </div>

                            <!-- Названия курсов в бандле -->
                            <div
                                v-if="getCourseTitles(bundle).length"
                                class="text-[11px] text-center
                                       text-slate-800 dark:text-slate-200"
                                :title="getCourseTitlesTooltip(bundle)"
                            >
                                <!-- Названия курсов в бандле -->
                                <div
                                    v-if="getCourseTitles(bundle).length"
                                    class="py-1 font-semibold text-[11px] text-left
                                           text-slate-900 dark:text-slate-100
                                           border border-dashed border-slate-400"
                                    :title="getCourseTitlesTooltip(bundle)"
                                >
                                    <div class="mb-1 text-[13px] text-center font-semibold
                                                text-teal-700 dark:text-teal-300">
                                        {{ t('courses') }}:
                                    </div>

                                    <ul class="space-y-0.5 max-h-20 overflow-auto pr-1">
                                        <li
                                            v-for="(title, i) in getCourseTitles(bundle)"
                                            :key="i"
                                            class="leading-snug pl-2 relative"
                                        >
                                            <span class="absolute left-0 text-red-400">•</span>
                                            {{ title }}
                                        </li>
                                    </ul>
                                </div>

                                <div
                                    v-else
                                    class="text-[11px] text-center text-slate-400"
                                >
                                    {{ t('courses') }}: —
                                </div>

                            </div>

                            <div
                                v-else
                                class="mt-1 text-[11px] text-center text-slate-400"
                                :title="t('noData')"
                            >
                                {{ t('courses') }}: —
                            </div>

                            <div class="flex flex-wrap justify-center gap-3 mt-4
                                        text-[11px] text-slate-900 dark:text-slate-200">
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
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="bundle.activity"
                                    @toggle-activity="$emit('toggle-activity', bundle)"
                                    :title="bundle.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.bundles.edit', bundle.id)" />
                                <DeleteIconButton @delete="$emit('delete', bundle.id, bundle.title)" />
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
