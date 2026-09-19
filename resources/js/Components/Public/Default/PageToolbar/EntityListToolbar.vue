<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import ViewModeToggle
    from '@/Components/Public/Default/Buttons/ViewModeToggle.vue'

const { t } = useI18n()

const props = defineProps({
    found: {
        type: Number,
        default: 0,
    },

    viewMode: {
        type: String,
        default: 'grid',
    },

    sortValue: {
        type: String,
        default: '',
    },

    sortOptions: {
        type: Array,
        default: () => [],
    },

    foundLabel: {
        type: String,
        default: '',
    },
})

const emit = defineEmits([
    'update:viewMode',
    'update:sortValue',
])

/**
 * Локальное значение сортировки.
 */
const sortLocal = ref(
    String(props.sortValue ?? '')
)

/**
 * Синхронизация сортировки
 * после изменения props.
 */
watch(
    () => props.sortValue,
    (value) => {
        sortLocal.value = String(value ?? '')
    }
)

/**
 * Изменение режима отображения.
 */
const setView = (mode) => {
    emit('update:viewMode', mode)
}

/**
 * Изменение сортировки.
 */
const onSortChange = () => {
    emit(
        'update:sortValue',
        sortLocal.value
    )
}
</script>

<template>
    <div
        class="my-4 p-2 rounded-md shadow-sm
               bg-white dark:bg-gray-900
               border border-gray-300 dark:border-gray-600"
    >
        <div
            class="px-2 py-2
                   flex flex-col gap-3
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >
            <!-- Количество найденных -->
            <div
                class="text-start text-xs
                       text-slate-500 dark:text-slate-400"
            >
                {{ t('found') }} {{ foundLabel }}:
                <span class="font-bold">
                    {{ found }}
                </span>
            </div>

            <!-- Управление списком -->
            <div
                class="flex items-center
                       justify-end gap-2"
            >
                <!-- Сортировка -->
                <div
                    class="flex flex-row
                           items-center gap-2"
                >
                    <label
                        class="hidden xl:block
                               text-xs font-semibold
                               text-slate-500
                               dark:text-slate-400"
                    >
                        {{ t('sort') }}
                    </label>

                    <select
                        v-model="sortLocal"
                        class="rounded-sm
                               pl-2 pr-6 py-1.5
                               text-xs font-semibold
                               border border-gray-400
                               dark:border-gray-500
                               bg-white dark:bg-gray-900
                               outline-none
                               text-slate-700
                               dark:text-slate-300
                               focus:border-indigo-400
                               focus:ring-2
                               focus:ring-indigo-200
                               dark:focus:border-indigo-500
                               dark:focus:ring-indigo-900/40"
                        @change="onSortChange"
                    >
                        <option
                            v-for="option in sortOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <!-- Grid / Rows -->
                <ViewModeToggle
                    :model-value="viewMode"
                    @update:modelValue="setView"
                />
            </div>
        </div>
    </div>
</template>
