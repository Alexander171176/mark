<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

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

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

// --- Мапы "значение из БД" → "ключ перевода" (как в таблице) ---
const moduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const moduleAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate',
}

// --- Хелперы для лейблов ---
const getModuleStatusLabel = (status) => {
    if (!status) return '—'
    const key = moduleStatusLabelKeyMap[status]
    return key ? t(key) : status
}

const getModuleAvailabilityLabel = (availability) => {
    if (!availability) return '—'
    const key = moduleAvailabilityLabelKeyMap[availability]
    return key ? t(key) : availability
}

// --- Локальная копия для vuedraggable ---
const localModules = ref([])

watch(
    () => props.modules,
    (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localModules.value.map(m => m.id)
    emits('update-sort-order', newOrderIds)
}

// --- Массовый выбор (как в таблице) ---
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localModules.value.map(m => m.id)
    emits('toggle-all', { ids, checked })
}

// --- Основное изображение (минимальный order) ---
const getPrimaryImage = (module) => {
    if (module.images && module.images.length) {
        return [...module.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

// --- Форматирование даты ---
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
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative">

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedModules.length }}
            </div>

            <label class="flex items-center text-xs text-slate-600
                          dark:text-slate-200 cursor-pointer"
                   v-if="localModules.length">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localModules.length" class="p-3">
            <draggable
                tag="div"
                v-model="localModules"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: module }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150">

                        <!-- Верхняя панель карточки -->
                        <div class="flex items-center justify-between px-2 py-1
                                    border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
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

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${module.locale}] : [${module.sort}] ${formatDate(module.published_at)}`">
                                    ID: {{ module.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Availability badge -->
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/50
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                    :title="t('availability')">
                                    {{ getModuleAvailabilityLabel(module.availability) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedModules.includes(module.id)"
                                    @change="$emit('toggle-select', module.id)"
                                />
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <template v-if="module.images?.length">
                                <img
                                    :src="getPrimaryImage(module).webp_url || getPrimaryImage(module).url"
                                    :alt="getPrimaryImage(module).alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(module).caption || t('moduleImage')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/module_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <!-- Заголовок модуля -->
                            <a
                                :href="`/modules/${encodeURIComponent(module.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="module.subtitle || module.title"
                            >
                                {{ module.title }}
                            </a>

                            <!-- Курс -->
                            <div
                                class="text-[11px] font-semibold
                                       text-teal-600 dark:text-teal-200 text-center">
                                <template v-if="module.course?.title">
                                    {{ t('course') }}: {{ module.course.title }}
                                    <span class="text-gray-600 dark:text-gray-200">
                                        [ID: {{ module.course.id }}]
                                    </span>
                                </template>
                                <template v-else>
                                    —
                                </template>
                            </div>

                            <!-- Статусы -->
                            <div class="flex flex-wrap justify-center gap-1 mt-1
                                        text-[10px] font-semibold">
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-sky-100 dark:bg-sky-900
                                           border border-gray-400
                                           text-sky-700 dark:text-sky-200"
                                    :title="t('status')">
                                    {{ getModuleStatusLabel(module.status) }}
                                </span>
                            </div>

                            <!-- Продолжительность / уроки -->
                            <div class="flex flex-col justify-center
                                        text-gray-700 dark:text-gray-400
                                        text-center text-[11px] mt-2">
                                <div>
                                    {{ t('duration') }}: {{ module.duration || '—' }}
                                </div>
                                <div>
                                    {{ t('lessonsCount') }}: {{ module.lessons_count ?? 0 }}
                                </div>
                            </div>

                            <!-- Статистика -->
                            <div class="flex flex-wrap justify-center gap-3 mt-2
                                        text-[11px] text-slate-900 dark:text-slate-200">
                                <span v-if="module.views">
                                    {{ t('views') }}: {{ module.views }}
                                </span>
                                <span v-if="module.likes">
                                    {{ t('likes') }}: {{ module.likes }}
                                </span>
                            </div>

                            <div class="flex flex-col justify-center mt-2 text-center
                                        text-[11px] text-slate-900 dark:text-slate-200">
                                <span v-if="module.rating_count">
                                    {{ t('ratingCount') }}: {{ module.rating_count }}
                                </span>
                                <span v-if="module.rating_avg">
                                    {{ t('ratingAvg') }}: {{ module.rating_avg }}
                                </span>
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="module.activity"
                                    @toggle-activity="$emit('toggle-activity', module)"
                                    :title="module.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.modules.edit', module.id)" />
                                <DeleteIconButton @delete="$emit('delete', module.id)" />
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
