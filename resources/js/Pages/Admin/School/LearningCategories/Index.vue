<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Категории обучения — дерево + карточки (паттерн рубрик)
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import BulkActionSelect from '@/Components/Admin/LearningCategory/Select/BulkActionSelect.vue'
import LearningCategoryTreeItem from '@/Components/Admin/LearningCategory/Tree/LearningCategoryTreeItem.vue'
import LearningCategoryCardGrid from '@/Components/Admin/LearningCategory/View/LearningCategoryCardGrid.vue'
import SortSelect from '@/Components/Admin/LearningCategory/Sort/SortSelect.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'

const { t, locale } = useI18n()
const toast = useToast()

const props = defineProps({
    learningCategoriesTree: { type: Array, default: () => [] },
    learningCategories: { type: Array, default: () => [] },
    learningCategoriesCount: { type: Number, default: 0 },

    adminCountLearningCategories: { type: Number, default: 15 },
    adminSortLearningCategories: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => [] },

    errors: { type: Object, default: () => ({}) },
})

/** Вид (table / cards) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'cards')
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Пагинация */
const itemsPerPage = ref(props.adminCountLearningCategories || 15)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountLearningCategories'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Сортировка */
const sortParam = ref(props.adminSortLearningCategories || 'idDesc')
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortLearningCategories'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Локальная копия дерева */
const localLearningCategoriesTree = ref([])
watch(
    () => props.learningCategoriesTree,
    (newVal) => {
        localLearningCategoriesTree.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Локальная копия плоского списка */
const localLearningCategoriesFlat = ref([])
watch(
    () => props.learningCategories,
    (newVal) => {
        localLearningCategoriesFlat.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const learningCategoryToDeleteId = ref(null)
const learningCategoryToDeleteName = ref('')

const confirmDelete = (learningCategory) => {
    learningCategoryToDeleteId.value = learningCategory.id
    learningCategoryToDeleteName.value = learningCategory.name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    learningCategoryToDeleteId.value = null
    learningCategoryToDeleteName.value = ''
}

const deleteLearningCategory = () => {
    if (learningCategoryToDeleteId.value === null) return

    const idToDelete = learningCategoryToDeleteId.value
    const nameToDelete = learningCategoryToDeleteName.value

    router.delete(route('admin.learningCategories.destroy', { learningCategory: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Категория "${nameToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Категория: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Активность */
const toggleActivity = (learningCategory) => {
    const newActivity = !learningCategory.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.learningCategories.updateActivity', { learningCategory: learningCategory.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                const patchTree = (nodes) => {
                    for (const node of nodes) {
                        if (node.id === learningCategory.id) {
                            node.activity = newActivity
                            return true
                        }
                        if (node.children?.length && patchTree(node.children)) return true
                    }
                    return false
                }
                patchTree(localLearningCategoriesTree.value)

                const idx = localLearningCategoriesFlat.value.findIndex(item => item.id === learningCategory.id)
                if (idx !== -1) {
                    localLearningCategoriesFlat.value[idx].activity = newActivity
                }

                toast.success(`Категория "${learningCategory.name}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${learningCategory.name}".`)
            },
        }
    )
}

/** Поиск / пагинация (карточки) */
const searchQuery = ref('')
const currentPage = ref(1)

const normalize = (v) => (v ?? '').toString().trim().toLowerCase()

/** Сортировка карточек */
const sortLearningCategories = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'locale') {
        return list.sort((a, b) => {
            if ((a.locale || '') < (b.locale || '')) return 1
            if ((a.locale || '') > (b.locale || '')) return -1
            return 0
        })
    }

    if (sortParam.value === 'views') {
        return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    }

    if (sortParam.value === 'courses_count') {
        return list.sort((a, b) => (b.courses_count ?? 0) - (a.courses_count ?? 0))
    }

    if (sortParam.value === 'children_count') {
        return list.sort((a, b) => (b.children_count ?? 0) - (a.children_count ?? 0))
    }

    if (sortParam.value === 'nameAsc') {
        return list.sort((a, b) => normalize(a?.name).localeCompare(normalize(b?.name), locale.value))
    }

    if (sortParam.value === 'nameDesc') {
        return list.sort((a, b) => normalize(b?.name).localeCompare(normalize(a?.name), locale.value))
    }

    return list.sort((a, b) => {
        const key = sortParam.value
        const av = a?.[key]
        const bv = b?.[key]
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтр поиска */
const filteredLearningCategories = computed(() => {
    let filtered = localLearningCategoriesFlat.value || []
    const q = normalize(searchQuery.value)

    if (!q) return sortLearningCategories(filtered)

    filtered = filtered.filter((item) => {
        const name = normalize(item?.name)
        const slug = normalize(item?.slug)
        const short = normalize(item?.short)
        const parentName = normalize(item?.parent?.name)

        return (
            name.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            parentName.includes(q)
        )
    })

    return sortLearningCategories(filtered)
})

/** Пагинация */
const paginatedLearningCategories = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredLearningCategories.value.slice(start, start + per)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Drag & Drop дерева */
const handleDragEnd = () => {
    let changes = []

    const updateSortAndCollectChanges = (nodes, parentId) => {
        nodes.forEach((node, index) => {
            let changed = false

            if (node.sort !== index) {
                node.sort = index
                changed = true
            }

            if (node.parent_id !== parentId) {
                node.parent_id = parentId
                changed = true
            }

            if (changed) {
                changes.push({
                    id: node.id,
                    sort: node.sort,
                    parent_id: parentId,
                })
            }

            if (node.children && node.children.length) {
                updateSortAndCollectChanges(node.children, node.id)
            }
        })
    }

    updateSortAndCollectChanges(localLearningCategoriesTree.value, null)

    const uniqueChanges = changes.reduce((acc, current) => {
        const x = acc.find(item => item.id === current.id)
        if (!x) return acc.concat([current])
        Object.assign(x, current)
        return acc
    }, [])

    if (!uniqueChanges.length) return

    router.put(
        route('admin.actions.learningCategories.updateSortBulk'),
        { items: uniqueChanges, locale: props.currentLocale },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия категорий успешно обновлена'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || 'Ошибка обновления иерархии категорий')
                router.reload({
                    only: ['learningCategoriesTree', 'learningCategories'],
                    preserveScroll: true
                })
            },
        }
    )
}

/** Массовые действия */
const selectedLearningCategories = ref([])

const getAllIds = (nodes) => {
    let ids = []
    nodes.forEach(node => {
        ids.push(node.id)
        if (node.children?.length) ids = ids.concat(getAllIds(node.children))
    })
    return ids
}

const toggleAll = (event) => {
    const checked = event.target?.checked

    const allIds = viewMode.value === 'table'
        ? getAllIds(localLearningCategoriesTree.value)
        : (filteredLearningCategories.value || []).map(item => item.id)

    if (checked === true) {
        selectedLearningCategories.value = allIds
    } else if (checked === false) {
        selectedLearningCategories.value = []
    }
}

const toggleAllCards = ({ ids, checked }) => {
    if (checked === true) {
        selectedLearningCategories.value = ids
    } else if (checked === false) {
        selectedLearningCategories.value = []
    }
}

const toggleSelectLearningCategory = (learningCategoryId) => {
    const index = selectedLearningCategories.value.indexOf(learningCategoryId)
    if (index > -1) selectedLearningCategories.value.splice(index, 1)
    else selectedLearningCategories.value.push(learningCategoryId)
}

const updateActivityByIds = (nodes, ids, activity) => {
    nodes.forEach(node => {
        if (ids.includes(node.id)) node.activity = activity
        if (node.children?.length) updateActivityByIds(node.children, ids, activity)
    })
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedLearningCategories.value.length) {
        toast.warning('Выберите категории для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedLearningCategories.value]

    router.put(route('admin.actions.learningCategories.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            updateActivityByIds(localLearningCategoriesTree.value, idsToUpdate, newActivity)

            localLearningCategoriesFlat.value = localLearningCategoriesFlat.value.map(item => {
                if (idsToUpdate.includes(item.id)) return { ...item, activity: newActivity }
                return item
            })

            selectedLearningCategories.value = []
            toast.success('Статус активности массово обновлён')
        },
        onError: (errors) => {
            const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
            toast.error(msg)
        },
    })
}

const bulkDelete = () => {
    if (!selectedLearningCategories.value.length) {
        toast.warning('Выберите хотя бы одну категорию для удаления.')
        return
    }

    toast.warning('Массовое удаление для категорий пока не реализовано.')
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') toggleAll({ target: { checked: true } })
    else if (action === 'deselectAll') toggleAll({ target: { checked: false } })
    else if (action === 'activate') bulkToggleActivity(true)
    else if (action === 'deactivate') bulkToggleActivity(false)
    else if (action === 'delete') bulkDelete()

    event.target.value = ''
}

/** Таб локалей */
const localeLink = (loc) => route('admin.learningCategories.index', { locale: loc })
</script>

<template>
    <AdminLayout :title="t('learningCategories')">
        <template #header>
            <TitlePage>{{ t('learningCategories') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.learningCategories.create', { locale: currentLocale })">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addLearningCategory') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="learningCategoriesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            {{ t('localization') }}:
                        </span>

                        <template v-for="loc in availableLocales" :key="loc">
                            <Link
                                :href="localeLink(loc)"
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-sm',
                                    currentLocale === loc
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ loc.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3" v-if="learningCategoriesCount">
                        <CountTable>{{ learningCategoriesCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="learningCategoriesCount && viewMode !== 'table'"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Дерево -->
                <div
                    v-if="viewMode === 'table'"
                    class="bg-gray-300 dark:bg-gray-700 border border-gray-400 relative"
                >
                    <div
                        class="flex items-center justify-between mt-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
                        <div class="flex items-center">
                            <CountTable v-if="learningCategoriesCount">
                                {{ learningCategoriesCount }}
                            </CountTable>
                        </div>

                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="select-all-header"
                                @change="toggleAll"
                                class="form-checkbox rounded-sm text-indigo-500 ml-2"
                                :title="t('selectAll')"
                            >
                        </div>
                    </div>

                    <div class="bg-gray-300 dark:bg-gray-900 border border-gray-400 relative">
                        <draggable
                            v-model="localLearningCategoriesTree"
                            tag="div"
                            item-key="id"
                            handle=".handle"
                            group="learningCategories"
                            @end="handleDragEnd"
                            class="category-tree-root"
                            :data-parent-id="null"
                        >
                            <template #item="{ element: learningCategory }">
                                <LearningCategoryTreeItem
                                    :learningCategories="learningCategory"
                                    :level="0"
                                    :selected-learning-categories="selectedLearningCategories"
                                    @toggle-activity="toggleActivity"
                                    @delete="confirmDelete"
                                    @toggle-select="toggleSelectLearningCategory"
                                    @request-drag-end="handleDragEnd"
                                />
                            </template>

                            <template #header v-if="localLearningCategoriesTree.length === 0 && learningCategoriesCount > 0">
                                <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                                    {{ t('loading') }}...
                                </div>
                            </template>

                            <template #footer v-if="localLearningCategoriesTree.length === 0 && learningCategoriesCount === 0">
                                <div class="p-4 text-center text-slate-900 dark:text-slate-100">
                                    {{ t('noData') }}
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>

                <!-- Карточки -->
                <LearningCategoryCardGrid
                    v-else
                    :learning-categories="paginatedLearningCategories"
                    :selected-learning-categories="selectedLearningCategories"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectLearningCategory"
                    @toggle-all="toggleAllCards"
                />

                <!-- Панель карточек -->
                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="learningCategoriesCount && viewMode !== 'table'"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLearningCategories.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteLearningCategory"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>

<style scoped>
.category-tree-root {
    padding: 5px;
}
</style>
