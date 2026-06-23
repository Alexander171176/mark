<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список пресетов обработки изображений
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'

import ImagePresetTable from '@/Components/Admin/System/ImagePreset/Table/ImagePresetTable.vue'
import ImagePresetCardGrid from '@/Components/Admin/System/ImagePreset/View/ImagePresetCardGrid.vue'
import SortSelect from '@/Components/Admin/System/ImagePreset/Sort/SortSelect.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    presets: {
        type: [Array, Object],
        default: () => [],
    },
    presetsCount: {
        type: Number,
        default: 0,
    },
    adminImagePresetsPerPage: {
        type: Number,
        default: 6,
    },
    adminImagePresetsDefaultSort: {
        type: String,
        default: 'idDesc',
    },
    error: {
        type: String,
        default: '',
    },
})

/** Нормализация данных из ResourceCollection */
const allPresets = computed(() => {
    if (Array.isArray(props.presets)) {
        return props.presets
    }

    return props.presets?.data || []
})

/** Вид отображения: таблица или карточки */
const viewMode = ref(localStorage.getItem('admin_image_presets_view_mode') || 'table')

watch(viewMode, (value) => {
    localStorage.setItem('admin_image_presets_view_mode', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminImagePresetsPerPage || 6)

watch(
    () => props.adminImagePresetsPerPage,
    (value) => {
        itemsPerPage.value = value || 6
    }
)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountImagePresets'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => {
                toast.error(
                    errors?.value ||
                    'Ошибка обновления количества элементов.'
                )
            },
        }
    )
})

/** Сортировка */
const sortParam = ref(props.adminImagePresetsDefaultSort || 'idDesc')

watch(
    () => props.adminImagePresetsDefaultSort,
    (value) => {
        sortParam.value = value || 'idDesc'
    }
)

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortImagePresets'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => {
                toast.error(
                    errors?.value ||
                    'Ошибка обновления сортировки.'
                )
            },
        }
    )
})

/** Поиск и пагинация */
const searchQuery = ref('')
const currentPage = ref(1)

watch(searchQuery, () => {
    currentPage.value = 1
})

/** Сортировка на фронте */
const sortPresets = (presets) => {
    const value = sortParam.value
    const list = presets.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)

    if (value === 'sortAsc') {
        return list.sort((a, b) => {
            if (a.sort !== b.sort) return a.sort - b.sort
            return a.id - b.id
        })
    }

    if (value === 'sortDesc') {
        return list.sort((a, b) => {
            if (a.sort !== b.sort) return b.sort - a.sort
            return b.id - a.id
        })
    }

    if (value === 'widthAsc') return list.sort((a, b) => a.width - b.width)
    if (value === 'widthDesc') return list.sort((a, b) => b.width - a.width)

    if (value === 'heightAsc') return list.sort((a, b) => a.height - b.height)
    if (value === 'heightDesc') return list.sort((a, b) => b.height - a.height)

    if (value === 'sizeAsc') {
        return list.sort((a, b) => a.max_file_size_kb - b.max_file_size_kb)
    }

    if (value === 'sizeDesc') {
        return list.sort((a, b) => b.max_file_size_kb - a.max_file_size_kb)
    }

    if (value === 'rectangle') return list.filter(p => p.shape === 'rectangle')
    if (value === 'square') return list.filter(p => p.shape === 'square')
    if (value === 'circle') return list.filter(p => p.shape === 'circle')

    if (value === 'shapeAsc') {
        return list.sort((a, b) => (a.shape || '').localeCompare(b.shape || ''))
    }

    if (value === 'shapeDesc') {
        return list.sort((a, b) => (b.shape || '').localeCompare(a.shape || ''))
    }

    if (value === 'allowRotate') {
        return list.filter(p => p.image_rotation_enabled)
    }

    if (value === 'noAllowRotate') {
        return list.filter(p => !p.image_rotation_enabled)
    }

    if (value === 'allowCropRotate') {
        return list.filter(p => p.crop_rotation_enabled)
    }

    if (value === 'noCropRotate') {
        return list.filter(p => !p.crop_rotation_enabled)
    }

    if (value === 'keepOriginal') {
        return list.filter(p => p.keep_original)
    }

    if (value === 'noKeepOriginal') {
        return list.filter(p => !p.keep_original)
    }

    if (value === 'createdAtAsc') {
        return list.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
    }

    if (value === 'createdAtDesc') {
        return list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }

    if (value === 'updatedAtAsc') {
        return list.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at))
    }

    if (value === 'updatedAtDesc') {
        return list.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))
    }

    return list.sort((a, b) => {
        const av = a?.[value] ?? ''
        const bv = b?.[value] ?? ''

        if (av < bv) return -1
        if (av > bv) return 1

        return 0
    })
}

/** Поиск + сортировка */
const filteredPresets = computed(() => {
    let filtered = allPresets.value || []

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()

        filtered = filtered.filter((preset) =>
            (preset.key || '').toLowerCase().includes(query) ||
            (preset.description || '').toLowerCase().includes(query) ||
            (preset.shape || '').toLowerCase().includes(query) ||
            (preset.resolution || '').toLowerCase().includes(query)
        )
    }

    return sortPresets(filtered)
})

/** Текущая страница */
const paginatedPresets = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredPresets.value.slice(
        start,
        start + itemsPerPage.value
    )
})

/** Drag&Drop сортировка */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.imagePresets.updateSortBulk'),
        {
            presets: sortData,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Порядок пресетов успешно обновлён.')
            },
            onError: (errors) => {
                console.error('Ошибка обновления сортировки пресетов:', errors)

                toast.error(
                    errors?.presets ||
                    errors?.general ||
                    'Не удалось обновить порядок пресетов.'
                )

                router.reload({
                    only: ['presets'],
                    preserveScroll: true,
                })
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('imagePresets')">
        <template #header>
            <TitlePage>
                {{ t('imagePresets') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div
                    v-if="error"
                    class="mb-3 p-3 text-sm text-red-700 bg-red-100
                           border border-red-300 rounded-sm"
                >
                    {{ error }}
                </div>

                <SearchInput
                    v-if="presetsCount"
                    v-model="searchQuery"
                    :placeholder="`${t('search')}...`"
                />

                <div
                    v-if="presetsCount"
                    class="flex flex-col lg:flex-row items-center justify-between mb-3 gap-1"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="value => (sortParam = value)"
                    />
                </div>

                <div
                    v-if="presetsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ presetsCount }}
                    </CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="presetsCount"
                    class="flex justify-center items-center flex-col md:flex-row"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPresets.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <ImagePresetTable
                    v-if="viewMode === 'table'"
                    :presets="paginatedPresets"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <ImagePresetCardGrid
                    v-else
                    :presets="paginatedPresets"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <div
                    v-if="presetsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPresets.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
