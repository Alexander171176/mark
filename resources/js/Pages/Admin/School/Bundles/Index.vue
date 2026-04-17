<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Список бандлов (паттерн как у курсов)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// 🔹 новые компоненты бандлов
import BulkActionSelect from '@/Components/Admin/Bundle/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Bundle/Sort/SortSelect.vue'
import BundleTable from '@/Components/Admin/Bundle/Table/BundleTable.vue'
import BundleCardGrid from '@/Components/Admin/Bundle/View/BundleCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    bundles: Array,
    bundlesCount: Number,
    adminCountBundles: Number,
    adminSortBundles: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountBundles || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountBundles'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortBundles || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortBundles'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const bundleToDeleteId = ref(null)
const bundleToDeleteTitle = ref('')

const confirmDelete = (id, title = '') => {
    bundleToDeleteId.value = id
    bundleToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    bundleToDeleteId.value = null
    bundleToDeleteTitle.value = ''
}

const deleteBundle = () => {
    if (bundleToDeleteId.value === null) return
    const idToDelete = bundleToDeleteId.value
    const titleToDelete = bundleToDeleteTitle.value

    router.delete(route('admin.bundles.destroy', { bundle: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Бандл "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Бандл: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            bundleToDeleteId.value = null
            bundleToDeleteTitle.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива бандлов */
const sortBundles = (bundles) => {
    const value = sortParam.value
    const list = bundles.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)

    // Дата публикации — от новых к старым, null в конец
    if (value === 'published_at') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0
            return bTime - aTime
        })
    }

    // Фильтры по активности
    if (value === 'activity') return bundles.filter(b => b.activity)
    if (value === 'inactive') return bundles.filter(b => !b.activity)

    // Числовые поля — сортировка по убыванию
    if (['views', 'likes', 'sort'].includes(value)) {
        return list.sort((a, b) => (b[value] ?? 0) - (a[value] ?? 0))
    }

    // title, slug, locale и прочее — обычная сортировка по возрастанию
    return list.sort((a, b) => {
        const av = a[value] ?? ''
        const bv = b[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка (поиск по title/slug/subtitle) */
const filteredBundles = computed(() => {
    let filtered = props.bundles || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(b =>
            (b.title || '').toLowerCase().includes(q) ||
            (b.slug || '').toLowerCase().includes(q) ||
            (b.subtitle || '').toLowerCase().includes(q)
        )
    }

    return sortBundles(filtered)
})

/** Пагинация */
const paginatedBundles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredBundles.value.slice(start, start + itemsPerPage.value)
})

const totalPages = computed(() =>
    Math.ceil((filteredBundles.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.bundles.updateSortBulk'),
        { bundles: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок бандлов успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки бандлов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.bundles ||
                    'Не удалось обновить порядок бандлов.'
                )
                router.reload({ only: ['bundles'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedBundles = ref([])

const toggleAll = ({ ids, checked }) => {
    selectedBundles.value = checked ? [...ids] : []
}

const toggleSelectBundle = (id) => {
    const idx = selectedBundles.value.indexOf(id)
    if (idx > -1) selectedBundles.value.splice(idx, 1)
    else selectedBundles.value.push(id)
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedBundles.value.length) {
        toast.warning('Выберите бандлы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.bundles.bulkUpdateActivity'),
        { ids: selectedBundles.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность бандлов массово обновлена')
                const updatedIds = [...selectedBundles.value]
                selectedBundles.value = []

                paginatedBundles.value.forEach(b => {
                    if (updatedIds.includes(b.id)) b.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность бандлов'
                toast.error(msg)
            },
        }
    )
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedBundles.value = paginatedBundles.value.map(b => b.id)
    } else if (action === 'deselectAll') {
        selectedBundles.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/** Переключение активности одного бандла */
const toggleActivity = (bundle) => {
    const newActivity = !bundle.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.bundles.updateActivity', { bundle: bundle.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                bundle.activity = newActivity
                toast.success(`Бандл "${bundle.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для бандла "${bundle.title}".`
                )
            },
        }
    )
}

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.bundles.index', { locale })
</script>

<template>
    <AdminLayout :title="t('bundles')">
        <template #header>
            <TitlePage>{{ t('bundles') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить бандл -->
                    <DefaultButton :href="route('admin.bundles.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addBundle') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="bundlesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <!-- Локали + счётчик -->
                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            {{ t('localization') }}:
                        </span>

                        <template v-for="locale in availableLocales" :key="locale">
                            <Link
                                :href="localeLink(locale)"
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-sm',
                                    currentLocale === locale
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ locale.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3">
                        <CountTable v-if="bundlesCount">
                            {{ bundlesCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="bundlesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица -->
                <BundleTable
                    v-if="viewMode === 'table'"
                    :bundles="paginatedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <BundleCardGrid
                    v-else
                    :bundles="paginatedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="bundlesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => (sortParam = val)"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteBundle"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
