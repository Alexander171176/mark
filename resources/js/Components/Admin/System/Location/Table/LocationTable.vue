<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import DefaultLocationToggle from '@/Components/Admin/UI/Buttons/DefaultLocationToggle.vue'

const { t } = useI18n()

const props = defineProps({
    locations: {
        type: Array,
        default: () => [],
    },

    selectedLocations: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'toggle-activity',
    'make-default',
    'update-sort',
    'update-sort-order',
    'delete',
    'toggle-select',
    'toggle-all',
])

/* ==========================================================
 * LOCAL LOCATIONS / DRAG'N'DROP
 * ========================================================== */

const localLocations = ref([])

watch(
    () => props.locations,
    (newVal) => {
        localLocations.value = JSON.parse(JSON.stringify(newVal || []))
    },
    {
        immediate: true,
        deep: true,
    }
)

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localLocations.value.map((location) => location.id)
    )
}

/* ==========================================================
 * RESOURCE
 * ========================================================== */

const locationTranslation = (location) => location?.translation || {}

const locationTitle = (location) => {
    return locationTranslation(location)?.title || `ID: ${location?.id}`
}

const locationTitleIn = (location) => {
    return locationTranslation(location)?.title_in || ''
}

const parentTitle = (location) => {
    return location?.parent?.translation?.title
        || (location?.parent_id ? `ID: ${location.parent_id}` : '—')
}

/* ==========================================================
 * HELPERS
 * ========================================================== */

const locationTypeTitle = (type) => {
    const types = {
        country: 'country',
        region: 'region',
        city: 'city',
        district: 'district',
    }

    return types[type] ? t(types[type]) : type || '—'
}

const coordinates = (location) => {
    if (
        location?.latitude === null
        || location?.latitude === undefined
        || location?.longitude === null
        || location?.longitude === undefined
    ) {
        return '—'
    }

    return `${location.latitude} / ${location.longitude}`
}

const typeBadgeClass = (type) => {
    const classes = {
        country:
            'bg-indigo-100 text-indigo-700 border-indigo-300 ' +
            'dark:bg-indigo-900/40 dark:text-indigo-200',

        region:
            'bg-purple-100 text-purple-700 border-purple-300 ' +
            'dark:bg-purple-900/40 dark:text-purple-200',

        city:
            'bg-sky-100 text-sky-700 border-sky-300 ' +
            'dark:bg-sky-900/40 dark:text-sky-200',

        district:
            'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
            'dark:bg-emerald-900/40 dark:text-emerald-200',
    }

    return classes[type]
        || 'bg-gray-100 text-gray-700 border-gray-300 ' +
        'dark:bg-gray-800 dark:text-gray-200'
}

/* ==========================================================
 * ВЫБОР
 * ========================================================== */

const allSelected = () => {
    return !!localLocations.value.length
        && localLocations.value.every((location) =>
            props.selectedLocations.includes(location.id)
        )
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localLocations.value.map((location) => location.id),
        checked: event.target.checked,
    })
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <!-- Верхняя панель -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}:
                {{ selectedLocations.length }}
            </div>

            <label
                v-if="localLocations.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="allSelected()"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localLocations.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300
                           dark:border-gray-700"
                >
                <tr>
                    <!-- Drag -->
                    <th class="px-2 py-3 whitespace-nowrap w-px">
                        <div class="flex justify-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 fill-current
                                           text-slate-800 dark:text-slate-200"
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
                        </div>
                    </th>

                    <!-- ID -->
                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Название -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('title') }}
                        </div>
                    </th>

                    <!-- Slug -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('slug') }}
                        </div>
                    </th>

                    <!-- Тип -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('type') }}
                        </div>
                    </th>

                    <!-- Родитель -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('parent') }}
                        </div>
                    </th>

                    <!-- Code -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('code') }}
                        </div>
                    </th>

                    <!-- Координаты -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('coordinates') }}
                        </div>
                    </th>

                    <!-- Timezone -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            Timezone
                        </div>
                    </th>

                    <!-- Actions -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <!-- Select -->
                    <th class="px-2 py-3 whitespace-nowrap w-px">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                :checked="allSelected()"
                                @change="toggleAll"
                            />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localLocations"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: location }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <!-- Drag -->
                            <td
                                class="px-2 py-1 whitespace-nowrap
                                       text-center cursor-move handle"
                            >
                                <div
                                    class="flex justify-center"
                                    :title="`${t('sort')}: ${location.sort}`"
                                >
                                    <svg
                                        class="w-4 h-4 fill-current
                                               text-gray-500 dark:text-gray-300"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle cx="9" cy="5" r="1.5" />
                                        <circle cx="15" cy="5" r="1.5" />
                                        <circle cx="9" cy="12" r="1.5" />
                                        <circle cx="15" cy="12" r="1.5" />
                                        <circle cx="9" cy="19" r="1.5" />
                                        <circle cx="15" cy="19" r="1.5" />
                                    </svg>
                                </div>
                            </td>

                            <!-- ID -->
                            <td class="px-2 py-3 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600
                                           dark:text-blue-200"
                                    :title="`${t('sort')}: ${location.sort}`"
                                >
                                    {{ location.id }}
                                </div>
                            </td>

                            <!-- Название -->
                            <td class="px-2 py-3">
                                <div
                                    class="text-left text-sky-700
                                           dark:text-sky-200"
                                    :title="locationTitleIn(location)"
                                >
                                    {{ locationTitle(location) }}
                                </div>
                            </td>

                            <!-- Slug -->
                            <td class="px-2 py-3">
                                <div
                                    class="text-xs text-violet-700
                                           dark:text-violet-200"
                                >
                                    {{ location.slug }}
                                </div>
                            </td>

                            <!-- Тип -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <span
                                        class="text-[10px] px-2 py-1
                                               rounded-sm border font-semibold"
                                        :class="typeBadgeClass(location.type)"
                                    >
                                        {{ locationTypeTitle(location.type) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Родитель -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs"
                                    :title="
                                        location.parent_id
                                            ? `ID: ${location.parent_id}`
                                            : null
                                    "
                                >
                                    {{ parentTitle(location) }}
                                </div>
                            </td>

                            <!-- Code -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-amber-600 dark:text-amber-200">
                                    {{ location.code || '—' }}
                                </div>
                            </td>

                            <!-- Координаты -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs font-semibold
                                            text-blue-600 dark:text-blue-200">
                                    {{ coordinates(location) }}
                                </div>
                            </td>

                            <!-- Timezone -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs font-semibold">
                                    {{ location.timezone || '—' }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-1">
                                    <DefaultLocationToggle
                                        :is-default="location.is_default"
                                        :title="
                                            location.is_default
                                                ? 'Локация по умолчанию'
                                                : 'Сделать локацией по умолчанию'
                                        "
                                        @make-default="
                                            emit('make-default', location)
                                        "
                                    />

                                    <ActivityToggle
                                        :isActive="location.activity"
                                        :title="
                                            location.activity
                                                ? t('enabled')
                                                : t('disabled')
                                        "
                                        @toggle-activity="
                                            emit('toggle-activity', location)
                                        "
                                    />

                                    <IconEdit
                                        :href="
                                            route('admin.locations.edit', {
                                                location: location.id,
                                            })
                                        "
                                    />

                                    <DeleteIconButton
                                        @click="emit('delete', location)"
                                    />
                                </div>
                            </td>

                            <!-- Select -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedLocations.includes(
                                                location.id
                                            )
                                        "
                                        @change="
                                            emit(
                                                'toggle-select',
                                                location.id
                                            )
                                        "
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
