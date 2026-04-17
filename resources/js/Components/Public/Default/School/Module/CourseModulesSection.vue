<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import CourseModuleGrid from '@/Components/Public/Default/School/Module/CourseModuleGrid.vue'
import CourseModuleRows from '@/Components/Public/Default/School/Module/CourseModuleRows.vue'
import ViewModeToggle from '@/Components/Public/Default/Buttons/ViewModeToggle.vue'

const { t } = useI18n()

const props = defineProps({
    modules: { type: [Array, Object], default: () => [] },
    cols: { type: Number, default: 2 },
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const modulesList = computed(() => normalizeList(props.modules))

const VIEW_KEY = 'public_course_modules_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

const modulesCount = computed(() => modulesList.value.length)
</script>

<template>
    <section v-if="modulesCount > 0" class="mt-8">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

            <div class="my-3 text-start center text-xs text-slate-500 dark:text-slate-400">
                {{ t('modules') }}:
                <span class="font-bold">{{ modulesCount }}</span>
            </div>

            <div class="flex items-center gap-2">
                <svg class="shrink-0 h-4 w-4"
                     viewBox="0 0 24 24">
                    <rect class="fill-current text-slate-400"
                          x="1" y="1" width="10" height="10" rx="2"></rect>
                    <path class="fill-current text-slate-600"
                          d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                    <rect class="fill-current text-slate-400"
                          x="13" y="13" width="10" height="10" rx="2"></rect>
                    <rect class="fill-current text-slate-400"
                          x="1" y="13" width="10" height="10" rx="2"></rect>
                </svg>

                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    {{ t('modules') }}
                </h2>
            </div>

            <ViewModeToggle v-model="viewMode" />
        </div>

        <CourseModuleGrid
            v-if="viewMode === 'grid'"
            :modules="modulesList"
            :cols="cols"
        />

        <CourseModuleRows
            v-else
            :modules="modulesList"
        />
    </section>
</template>
