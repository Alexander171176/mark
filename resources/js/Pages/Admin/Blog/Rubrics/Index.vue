<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр
 * Рубрики — дерево паттерн
 */
import { defineProps, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link, usePage } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import BulkActionSelect from '@/Components/Admin/Rubric/Select/BulkActionSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import RubricTreeDraggable from '@/Components/Admin/Rubric/Tree/RubricTreeDraggable.vue'
import RubricCardGrid from '@/Components/Admin/Rubric/View/RubricCardGrid.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SortSelect from '@/Components/Admin/Rubric/Sort/SortSelect.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Props */
const props = defineProps({
    rubricsTree: Array, // дерево
    rubrics: Array,     // плоский список (карточки)
    rubricsCount: Number,

    adminCountRubrics: Number,
    adminSortRubrics: String,

    currentLocale: String,
    availableLocales: Array,

    errors: Object,
})

/** Роль пользователя */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some(r => r?.name === 'admin')
})

/** Вид (table / cards) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'cards')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Пагинация */
const itemsPerPage = ref(props.adminCountRubrics)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountRubrics'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** параметры Сортировки */
const sortParam = ref(props.adminSortRubrics)
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortRubrics'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Локальная копия дерева */
const localRubricsTree = ref([])
watch(
    () => props.rubricsTree,
    (newVal) => {
        localRubricsTree.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Локальная копия плоского списка */
const localRubricsFlat = ref([])
watch(
    () => props.rubrics,
    (newVal) => {
        localRubricsFlat.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модалка, Удаление */
const showConfirmDeleteModal = ref(false)
const rubricToDeleteId = ref(null)
const rubricToDeleteTitle = ref('')

const confirmDelete = (rubric) => {
    rubricToDeleteId.value = rubric.id
    rubricToDeleteTitle.value = rubric.title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    rubricToDeleteId.value = null
    rubricToDeleteTitle.value = ''
}

const deleteRubric = () => {
    if (rubricToDeleteId.value === null) return

    const idToDelete = rubricToDeleteId.value
    const titleToDelete = rubricToDeleteTitle.value

    router.delete(route('admin.rubrics.destroy', { rubric: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Рубрика "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Рубрика: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Активность (оптимистично обновляем дерево) */
const toggleActivity = (rubric) => {
    const newActivity = !rubric.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.rubrics.updateActivity', { rubric: rubric.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                const patchTree = (nodes) => {
                    for (const node of nodes) {
                        if (node.id === rubric.id) {
                            node.activity = newActivity
                            return true
                        }
                        if (node.children?.length && patchTree(node.children)) return true
                    }
                    return false
                }
                patchTree(localRubricsTree.value)

                // (опционально) обновим и плоский список для карточек
                const idx = localRubricsFlat.value.findIndex(r => r.id === rubric.id)
                if (idx !== -1) localRubricsFlat.value[idx].activity = newActivity

                toast.success(`Рубрика "${rubric.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${rubric.title}".`)
            },
        }
    )
}

/** Клон */
const cloneRubric = (rubric) => {
    const rubricId = rubric?.id
    const rubricTitle = rubric?.title || `ID: ${rubricId}`

    if (!rubricId) {
        toast.error('Не удалось определить рубрику для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать рубрику "${rubricTitle}"?`)) return

    router.post(route('admin.actions.rubrics.clone', { rubric: rubricId }), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Рубрика "${rubricTitle}" успешно клонирована.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || `Ошибка клонирования рубрики "${rubricTitle}".`)
        },
    })
}

/** Поиск / пагинация (карточки) */
const searchQuery = ref('')
const currentPage = ref(1)

const normalize = (v) => (v ?? '').toString().trim().toLowerCase()

const moderationNum = (v) => {
    const n = Number(v)
    return Number.isFinite(n) ? n : 0
}

/** Сортировка */
const sortRubrics = (rubrics) => {
    const list = (rubrics || []).slice()

    if (sortParam.value === 'ownerNameAsc') {
        return list.sort((a, b) => normalize(a?.owner?.name).localeCompare(normalize(b?.owner?.name), locale.value))
    }
    if (sortParam.value === 'ownerNameDesc') {
        return list.sort((a, b) => normalize(b?.owner?.name).localeCompare(normalize(a?.owner?.name), locale.value))
    }
    if (sortParam.value === 'ownerEmailAsc') {
        return list.sort((a, b) => normalize(a?.owner?.email).localeCompare(normalize(b?.owner?.email), locale.value))
    }
    if (sortParam.value === 'ownerEmailDesc') {
        return list.sort((a, b) => normalize(b?.owner?.email).localeCompare(normalize(a?.owner?.email), locale.value))
    }

    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

    if (sortParam.value === 'activity') return list.filter(r => !!r.activity)
    if (sortParam.value === 'inactive') return list.filter(r => !r.activity)

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

    // --- ФИЛЬТРЫ по статусу модерации ---
    if (sortParam.value === 'moderation_pending') {
        return list.filter(r => moderationNum(r?.moderation_status) === 0)
    }
    if (sortParam.value === 'moderation_approved') {
        return list.filter(r => moderationNum(r?.moderation_status) === 1)
    }
    if (sortParam.value === 'moderation_rejected') {
        return list.filter(r => moderationNum(r?.moderation_status) === 2)
    }

    // --- СОРТИРОВКА по статусу модерации ---
    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) =>
            moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }
    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) =>
            moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
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
const filteredRubrics = computed(() => {
    let filtered = localRubricsFlat.value || []
    const q = normalize(searchQuery.value)
    if (!q) return sortRubrics(filtered)

    filtered = filtered.filter((rubric) => {
        const title = normalize(rubric?.title)
        const ownerName = normalize(rubric?.owner?.name)
        const ownerEmail = normalize(rubric?.owner?.email)
        return title.includes(q) || ownerName.includes(q) || ownerEmail.includes(q)
    })

    return sortRubrics(filtered)
})

/** Пагинация */
const paginatedRubrics = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredRubrics.value.slice(start, start + per)
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

    updateSortAndCollectChanges(localRubricsTree.value, null)

    const uniqueChanges = changes.reduce((acc, current) => {
        const x = acc.find(item => item.id === current.id)
        if (!x) return acc.concat([current])
        Object.assign(x, current)
        return acc
    }, [])

    if (!uniqueChanges.length) return

    router.put(
        route('admin.actions.rubrics.updateSortBulk'),
        { items: uniqueChanges, locale: props.currentLocale },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия рубрик успешно обновлена'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || 'Ошибка обновления иерархии рубрик')
                router.reload({ only: ['rubricsTree', 'rubrics'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedRubrics = ref([])

/** выбор всех в дереве чекбокс */
const getAllIds = (nodes) => {
    let ids = []
    nodes.forEach(node => {
        ids.push(node.id)
        if (node.children?.length) ids = ids.concat(getAllIds(node.children))
    })
    return ids
}

/** выбор всех чекбокс */
const toggleAll = (event) => {
    const checked = event.target?.checked
    const allNodeIds = getAllIds(localRubricsTree.value)

    if (checked === true) selectedRubrics.value = allNodeIds
    else if (checked === false) selectedRubrics.value = []
}

/** выбор рубрики для массовых действий */
const toggleSelectRubric = (rubricId) => {
    const index = selectedRubrics.value.indexOf(rubricId)
    if (index > -1) selectedRubrics.value.splice(index, 1)
    else selectedRubrics.value.push(rubricId)
}

/** активность одного */
const updateActivityByIds = (nodes, ids, activity) => {
    nodes.forEach(node => {
        if (ids.includes(node.id)) node.activity = activity
        if (node.children?.length) updateActivityByIds(node.children, ids, activity)
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedRubrics.value.length) {
        toast.warning('Выберите рубрики для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedRubrics.value]

    router.put(route('admin.actions.rubrics.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            updateActivityByIds(localRubricsTree.value, idsToUpdate, newActivity)

            // обновим плоский список
            localRubricsFlat.value = localRubricsFlat.value.map(r => {
                if (idsToUpdate.includes(r.id)) return { ...r, activity: newActivity }
                return r
            })

            selectedRubrics.value = []
            toast.success('Статус активности массово обновлён')
        },
        onError: (errors) => {
            const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
            toast.error(msg)
        },
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedRubrics.value.length) {
        toast.warning('Выберите хотя бы одну рубрику для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные рубрики?')) return

    router.delete(route('admin.actions.rubrics.bulkDestroy'), {
        data: { ids: selectedRubrics.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedRubrics.value = []
            toast.success('Массовое удаление рубрик успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении рубрик.')
        },
    })
}

/** Обработчик массовых действий */
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
const localeLink = (loc) => route('admin.rubrics.index', { locale: loc })

/**
 * Модерация рубрики (approve/reject)
 * status: 1=approved, 2=rejected (0=pending — если нужно, можно добавить)
 */
const approveRubric = (rubric, status = 1, note = '') => {
    if (!rubric?.id) return

    router.put(
        route('admin.actions.rubrics.approve', { rubric: rubric.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // дерево
                const patchTree = (nodes) => {
                    for (const n of nodes) {
                        if (n.id === rubric.id) {
                            n.moderation_status = status
                            n.is_approved = status === 1
                            n.moderation_note = note
                            return true
                        }
                        if (n.children?.length && patchTree(n.children)) return true
                    }
                    return false
                }
                patchTree(localRubricsTree.value)

                // плоский список
                const idx = localRubricsFlat.value.findIndex(r => r.id === rubric.id)
                if (idx !== -1) {
                    localRubricsFlat.value[idx].moderation_status = status
                    localRubricsFlat.value[idx].is_approved = status === 1
                    localRubricsFlat.value[idx].moderation_note = note
                }

                toast.success(status === 1 ? 'Рубрика одобрена' : 'Рубрика отклонена')
            },
            onError: () => toast.error('Ошибка модерации рубрики'),
        }
    )
}

</script>

<template>
    <AdminLayout :title="t('rubrics')">
        <template #header>
            <TitlePage>{{ t('rubrics') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.rubrics.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                        </template>
                        {{ t('addRubric') }}
                    </DefaultButton>

                    <BulkActionSelect v-if="rubricsCount" @change="handleBulkAction" />
                </div>

                <!-- Локали + счётчик + вид -->
                <div class="flex items-center justify-between mt-5">
                    <div class="flex items-center justify-end space-x-2 px-3 py-1
                                border-x border-t border-gray-400 rounded-t-lg
                                bg-gray-100 dark:bg-gray-900">
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

                    <div class="flex items-center space-x-3" v-if="rubricsCount">
                        <CountTable>{{ rubricsCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="rubricsCount && viewMode !== 'table'"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Дерево -->
                <div v-if="viewMode === 'table'"
                     class="bg-gray-300 dark:bg-gray-700 border border-gray-400 relative">

                    <div class="flex items-center justify-between mt-2 px-3 py-1
                                border-x border-t border-gray-400 rounded-t-lg
                                bg-gray-100 dark:bg-gray-900">
                        <div class="flex items-center">
                            <CountTable v-if="rubricsCount">{{ rubricsCount }}</CountTable>
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

                    <draggable
                        v-model="localRubricsTree"
                        tag="div"
                        item-key="id"
                        handle=".handle"
                        group="rubrics"
                        @end="handleDragEnd"
                        class="category-tree-root p-1"
                        :data-parent-id="null"
                    >
                        <template #item="{ element: rubric }">
                            <RubricTreeDraggable
                                :rubric="rubric"
                                :level="0"
                                :selected-rubrics="selectedRubrics"
                                @toggle-activity="toggleActivity"
                                @delete="confirmDelete"
                                @clone="cloneRubric"
                                @toggle-select="toggleSelectRubric"
                                @request-drag-end="handleDragEnd"
                                :isAdmin="isAdmin"
                                @approve="approveRubric"
                            />
                        </template>

                        <template #header v-if="localRubricsTree.length === 0 && rubricsCount > 0">
                            <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                                {{ t('loading') }}...
                            </div>
                        </template>

                        <template #footer v-if="localRubricsTree.length === 0 && rubricsCount === 0">
                            <div class="p-4 text-center text-slate-900 dark:text-slate-100">
                                {{ t('noData') }}
                            </div>
                        </template>
                    </draggable>
                </div>

                <!-- Карточки -->
                <RubricCardGrid
                    v-else
                    :rubrics="paginatedRubrics"
                    :selectedRubrics="selectedRubrics"
                    @toggle-activity="toggleActivity"
                    @delete="(id, title) => confirmDelete({ id, title })"
                    @clone="cloneRubric"
                    @toggle-select="toggleSelectRubric"
                    :isAdmin="isAdmin"
                    @approve="approveRubric"
                />

                <div class="flex justify-between items-center flex-col md:flex-row my-1"
                     v-if="rubricsCount && viewMode !== 'table'">
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredRubrics.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect :sortParam="sortParam"
                                @update:sortParam="val => sortParam = val" />
                </div>

            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteRubric"
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
