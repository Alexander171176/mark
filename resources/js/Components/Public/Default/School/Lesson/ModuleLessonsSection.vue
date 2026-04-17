<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import ModuleLessonGrid from '@/Components/Public/Default/School/Lesson/ModuleLessonGrid.vue'
import ModuleLessonRows from '@/Components/Public/Default/School/Lesson/ModuleLessonRows.vue'
import ViewModeToggle from '@/Components/Public/Default/Buttons/ViewModeToggle.vue'

const { t } = useI18n()

const props = defineProps({
    lessons: { type: [Array, Object], default: () => [] },
    cols: { type: Number, default: 2 },
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const lessonsList = computed(() => normalizeList(props.lessons))

const VIEW_KEY = 'public_module_lessons_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

const lessonsCount = computed(() => lessonsList.value.length)
</script>

<template>
    <section v-if="lessonsCount > 0" class="mt-8">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

            <div class="my-3 text-start center text-xs text-slate-500 dark:text-slate-400">
                {{ t('lessons') }}:
                <span class="font-bold">{{ lessonsCount }}</span>
            </div>

            <div class="flex items-center gap-2">
                <svg
                    class="h-6 w-6 text-slate-600 dark:text-slate-300"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path d="M4 4h16v14H4zm2 2v10h12V6zm-2 14h16v2H4z"/>
                </svg>

                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    {{ t('lessons') }}
                </h2>
            </div>

            <ViewModeToggle
                v-model="viewMode"
                grid-value="grid"
                row-value="row"
                size="sm"
            />
        </div>

        <ModuleLessonGrid
            v-if="viewMode === 'grid'"
            :lessons="lessonsList"
            :cols="cols"
        />

        <ModuleLessonRows
            v-else
            :lessons="lessonsList"
        />
    </section>
</template>
