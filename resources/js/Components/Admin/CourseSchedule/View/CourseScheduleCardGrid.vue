<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    schedules: {
        type: Array,
        default: () => []
    },
    selectedSchedules: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone'
])

// 🔹 Мапы "значение из БД" → ключ перевода (flat i18n)
const scheduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

// 🔹 Хелперы
const getScheduleStatusLabel = (status) => {
    if (!status) return '—'

    const key = scheduleStatusLabelKeyMap[status]

    // Если есть ключ — переводим
    if (key) return t(key)

    // Нет ключа — не вызываем t(), просто возвращаем текст
    return status
}

const getScheduleOnlineLabel = (isOnline) => {
    const normalized =
        isOnline === true ||
        isOnline === 1 ||
        isOnline === '1' ||
        isOnline === 'true'

    return t(normalized ? 'online' : 'offline')
}

/** Локальная копия для vuedraggable */
const localSchedules = ref([])

watch(
    () => props.schedules,
    (newVal) => {
        localSchedules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Drag end: отдаём массив ID вверх */
const handleDragEnd = () => {
    const newOrderIds = localSchedules.value.map(schedule => schedule.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localSchedules.value.map(l => l.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение */
const getPrimaryImage = (schedule) => {
    if (schedule.images && schedule.images.length) {
        return [...schedule.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

// Формат даты (как в таблице)
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
                {{ t('selected') }}: {{ selectedSchedules.length }}
            </div>

            <label
                v-if="localSchedules.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localSchedules.length" class="p-3">
            <draggable
                tag="div"
                v-model="localSchedules"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: schedule }">
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

                                <!-- ID / locale / sort -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${schedule.locale}] : [${schedule.sort}]`">
                                    ID: {{ schedule.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Онлайн / офлайн -->
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-emerald-100 dark:bg-emerald-900/50
                                           text-emerald-700 dark:text-emerald-200"
                                    :title="t('online')">
                                    {{ getScheduleOnlineLabel(schedule.is_online) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedSchedules.includes(schedule.id)"
                                    @change="$emit('toggle-select', schedule.id)"
                                />
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full h-auto bg-slate-200 dark:bg-slate-900">
                            <template v-if="schedule.images?.length">
                                <img
                                    :src="getPrimaryImage(schedule).webp_url || getPrimaryImage(schedule).url"
                                    :alt="getPrimaryImage(schedule).alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(schedule).caption || t('scheduleImage')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/course_schedule_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1 text-[11px]">

                            <!-- Заголовок потока -->
                            <a
                                :href="`/schedules/${encodeURIComponent(schedule.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="schedule.subtitle || schedule.title"
                            >
                                {{ schedule.title }}
                            </a>

                            <!-- Курс -->
                            <div
                                class="text-[11px] text-teal-700 dark:text-teal-200 text-center">
                                {{ schedule.course?.title || '—' }}
                            </div>

                            <!-- Инструктор -->
                            <div class="text-center mt-1">
                                <template v-if="schedule.instructor?.user">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                                        {{ schedule.instructor.user.name || schedule.instructor.user.email }}
                                        <span v-if="schedule.instructor.title">:</span>
                                    </span>
                                    <br v-if="schedule.instructor.title">
                                    <span
                                        v-if="schedule.instructor.title"
                                        class="text-blue-700 dark:text-blue-300">
                                        {{ schedule.instructor.title }}
                                    </span>
                                </template>
                                <template v-else>
                                    <span class="text-slate-400 dark:text-slate-500">—</span>
                                </template>
                            </div>

                            <!-- Даты расписания -->
                            <div
                                class="px-1 text-left text-[9px] font-semibold
                                       border-dashed border border-gray-400">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-300">
                                        {{ t('scheduleStartsAt') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                        {{ schedule.starts_at ? formatDate(schedule.starts_at) : '—' }}
                                    </span>
                                </div>
                                <div v-if="schedule.ends_at">
                                    <span class="text-gray-600 dark:text-gray-300">
                                        {{ t('scheduleEndsAt') }}:
                                    </span>
                                    <span class="text-rose-600 dark:text-rose-300">
                                        {{ formatDate(schedule.ends_at) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Даты набора -->
                            <div
                                class="mt-1 text-center text-[9px] font-semibold">
                                <div class="flex flex-col justify-start">
                                    <span class="text-slate-800 dark:text-slate-100">
                                        {{ t('scheduleEnrollStartsAt') }}:
                                    </span>
                                    <span class="text-blue-700 dark:text-blue-300">
                                        {{ schedule.enroll_starts_at ? formatDate(schedule.enroll_starts_at) : '—' }}
                                    </span>
                                </div>
                                <div v-if="schedule.enroll_ends_at"
                                     class="flex flex-col justify-start">
                                    <span class="text-slate-800 dark:text-slate-100">
                                        {{ t('scheduleEnrollEndsAt') }}:
                                    </span>
                                    <span class="text-rose-600 dark:text-rose-300">
                                        {{ formatDate(schedule.enroll_ends_at) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Статус + вместимость + просмотры -->
                            <div
                                class="flex flex-wrap justify-center gap-2 mt-2
                                       text-[10px] font-semibold">

                                <!-- Просмотры -->
                                <span
                                    v-if="schedule.views"
                                    class="px-2 py-0.5 rounded-sm
                                           bg-blue-100 dark:bg-blue-900
                                           border border-gray-400
                                           text-blue-700 dark:text-blue-200"
                                    :title="t('views')"
                                >
                                    {{ t('views') }}: {{ schedule.views }}
                                </span>

                                <!-- Вместимость -->
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-amber-100 dark:bg-amber-900
                                           border border-gray-400
                                           text-amber-700 dark:text-amber-200"
                                    :title="t('capacity')"
                                >
                                    {{ t('capacity') }}: {{ schedule.capacity ?? '—' }}
                                </span>

                                <!-- Статус -->
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-sky-100 dark:bg-sky-900
                                           border border-gray-400
                                           text-sky-700 dark:text-sky-200"
                                    :title="t('status')"
                                >
                                    {{ getScheduleStatusLabel(schedule.status) }}
                                </span>

                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="schedule.activity"
                                    @toggle-activity="$emit('toggle-activity', schedule)"
                                    :title="schedule.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', schedule)" />
                                <IconEdit :href="route('admin.courseSchedules.edit', schedule.id)" />
                                <DeleteIconButton
                                    @delete="$emit('delete', schedule.id, schedule.title)"
                                />
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
