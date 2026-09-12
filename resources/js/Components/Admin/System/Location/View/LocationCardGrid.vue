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
        default: () => []
    },

    selectedLocations: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits([
    'toggle-activity',
    'make-default',
    'update-sort',
    'update-sort-order',
    'delete',
    'toggle-select',
    'toggle-all'
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
        deep: true
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

const locationTitleFrom = (location) => {
    return locationTranslation(location)?.title_from || ''
}

const locationShort = (location) => {
    return locationTranslation(location)?.short || ''
}


const parentTitle = (location) => {
    return location?.parent?.translation?.title
        || (location?.parent_id ? `ID: ${location.parent_id}` : '—')
}

/* ==========================================================
 * HELPERS
 * ========================================================== */

const truncateText = (text, maxLength = 100) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

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
        return ''
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
            'dark:bg-emerald-900/40 dark:text-emerald-200'
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
        checked: event.target.checked
    })
}
</script>

<template>
    <div class="relative rounded-sm border border-slate-400 dark:border-slate-500
                bg-white dark:bg-slate-700 shadow-lg">

        <!-- Верхняя панель -->
        <div
            class="flex items-center justify-between
                    border-b border-slate-400 dark:border-slate-500 px-3 py-2"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}:
                {{ selectedLocations.length }}
            </div>

            <label
                v-if="localLocations.length"
                class="flex cursor-pointer items-center text-xs
                       text-slate-600 dark:text-slate-200"
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

        <!-- Сетка карточек -->
        <div
            v-if="localLocations.length"
            class="p-3"
        >
            <draggable
                tag="div"
                v-model="localLocations"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2
                       lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: location }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80
                               shadow-sm hover:shadow-md
                               transition-shadow duration-150"
                    >
                        <!-- Верх карточки -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- Drag handle -->
                                <button
                                    type="button"
                                    class="handle text-slate-400
                                           hover:text-slate-700
                                           dark:hover:text-slate-100
                                           cursor-move"
                                    :title="`${t('sort')}: ${location.sort ?? 0}`"
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

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold
                                           px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200
                                           dark:bg-slate-700 text-slate-800
                                           dark:text-blue-100"
                                    :title="`${t('sort')}: ${location.sort ?? 0}`"
                                >
                                    ID: {{ location.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div
                                    class="rounded-sm border px-2 py-0.5
                                           text-[10px] font-semibold"
                                    :class="typeBadgeClass(location.type)"
                                >
                                    {{ locationTypeTitle(location.type) }}
                                </div>
                                <input
                                    type="checkbox"
                                    :checked="selectedLocations.includes(location.id)"
                                    @change="emit('toggle-select', location.id)"
                                />
                            </div>
                        </header>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-3 space-y-3">

                            <!-- Название -->
                            <div class="text-center">
                                <div
                                    class="text-base font-semibold
                                           text-sky-700 dark:text-sky-200"
                                    :title="locationTitle(location)"
                                >
                                    {{ locationTitle(location) }}
                                </div>

                                <div
                                    v-if="location.slug"
                                    class="mt-1 text-[11px]
                                           text-violet-700 dark:text-violet-200"
                                >
                                    /{{ location.slug }}
                                </div>
                            </div>

                            <!-- Формы названия -->
                            <div
                                v-if="
                                    locationTitleIn(location)
                                    || locationTitleFrom(location)
                                "
                                class="text-center space-y-1"
                            >
                                <div
                                    v-if="locationTitleIn(location)"
                                    class="text-[11px] text-slate-600
                                           dark:text-slate-300"
                                >
                                    {{ t('in') }}:
                                    {{ locationTitleIn(location) }}
                                </div>

                                <div
                                    v-if="locationTitleFrom(location)"
                                    class="text-[11px] text-slate-600
                                           dark:text-slate-300"
                                >
                                    {{ t('from') }}:
                                    {{ locationTitleFrom(location) }}
                                </div>
                            </div>

                            <!-- Short -->
                            <div
                                v-if="locationShort(location)"
                                class="font-semibold text-[12px] text-center
                                       text-teal-700 dark:text-teal-200"
                                :title="locationShort(location)"
                            >
                                {{ truncateText(locationShort(location)) }}
                            </div>

                            <!-- Информация -->
                            <div
                                class="mt-auto text-[11px] text-slate-600
                                       dark:text-slate-300
                                       border-t border-dashed
                                       border-slate-300 dark:border-slate-600
                                       pt-2 space-y-1"
                            >
                                <!-- Parent -->
                                <div class="flex justify-between gap-2">
                                    <span>{{ t('parent') }}:</span>

                                    <span
                                        class="font-semibold text-right"
                                        :title="
                                            location.parent_id
                                                ? `ID: ${location.parent_id}`
                                                : null
                                        "
                                    >
                                        {{ parentTitle(location) }}
                                    </span>
                                </div>

                                <!-- Code -->
                                <div class="flex justify-between gap-2">
                                    <span>Code:</span>
                                    <span class="font-semibold text-amber-600 dark:text-amber-200">
                                        {{ location.code || '—' }}
                                    </span>
                                </div>

                                <!-- Coordinates -->
                                <div
                                    v-if="coordinates(location)"
                                    class="flex justify-between gap-2"
                                >
                                    <span>{{ t('coordinates') }}:</span>

                                    <span
                                        class="font-semibold text-right
                                               text-blue-600 dark:text-blue-200">
                                        {{ coordinates(location) }}
                                    </span>
                                </div>

                                <!-- Timezone -->
                                <div
                                    v-if="location.timezone"
                                    class="flex justify-between gap-2"
                                >
                                    <span>Timezone:</span>

                                    <span class="font-semibold text-right">
                                        {{ location.timezone }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <footer
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
