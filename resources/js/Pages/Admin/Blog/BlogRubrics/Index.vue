<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Рубрики блога — Index
 * Новая мультиязычная архитектура:
 * - blog_rubrics
 * - blog_rubric_translations
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
import BulkActionSelect from '@/Components/Admin/Blog/Rubric/Select/BulkActionSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import RubricTreeDraggable from '@/Components/Admin/Blog/Rubric/Tree/RubricTreeDraggable.vue'
import RubricCardGrid from '@/Components/Admin/Blog/Rubric/View/RubricCardGrid.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SortSelect from '@/Components/Admin/Blog/Rubric/Sort/SortSelect.vue'

const { t, locale } = useI18n()
const toast = useToast()

/** Данные текущей страницы Inertia */
const page = usePage()

/** Props приходят из BlogRubricController@index */
const props = defineProps({
    rubricsTree: { type: Array, default: () => [] },
    rubrics: { type: Array, default: () => [] },
    rubricsCount: { type: Number, default: 0 },

    adminCountRubrics: { type: Number, default: 15 },
    adminSortRubrics: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },

    search: { type: String, default: '' },
    sortParam: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) }
})

/** Проверяем, является ли текущий пользователь администратором */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/**
 * Получение текущего перевода рубрики.
 * Данные приходят из BlogRubricResource в поле rubric.translation
 */
const getRubricTranslation = (rubric) => {
    return rubric?.translation || {}
}

/** Название рубрики в текущей локали */
const getRubricTitle = (rubric) => {
    return getRubricTranslation(rubric)?.title || `ID: ${rubric?.id}`
}

/** Краткое описание рубрики в текущей локали */
const getRubricShort = (rubric) => {
    return getRubricTranslation(rubric)?.short || ''
}

/** Локаль текущего перевода рубрики */
const getRubricLocale = (rubric) => {
    return getRubricTranslation(rubric)?.locale || props.currentLocale || ''
}

/** Нормализация строки для поиска/сортировки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное приведение статуса модерации к числу */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/**
 * Режим отображения:
 * - cards = карточки
 * - table = дерево с drag-and-drop
 * Сохраняем выбор в localStorage.
 */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode', value)
})

/**
 * Количество элементов на странице.
 * Значение сохраняется в настройках админки.
 */
const itemsPerPage = ref(props.adminCountRubrics || 15)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountRubrics'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.')
        }
    )
})

/**
 * Параметр сортировки.
 * Значение также сохраняется в настройках админки.
 */
const sortParam = ref(props.sortParam || props.adminSortRubrics || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortRubrics'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.')
        }
    )
})

/**
 * Локальные копии данных нужны, чтобы:
 * - менять активность без полной перезагрузки;
 * - обновлять drag-and-drop дерево;
 * - работать с карточками и массовыми действиями.
 */
const localRubricsTree = ref([])
const localRubricsFlat = ref([])

watch(
    () => props.rubricsTree,
    (newVal) => {
        localRubricsTree.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

watch(
    () => props.rubrics,
    (newVal) => {
        localRubricsFlat.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/**
 * Модальное подтверждение удаления одной рубрики
 */
const showConfirmDeleteModal = ref(false)
const rubricToDeleteId = ref(null)
const rubricToDeleteTitle = ref('')

/** Открываем модалку удаления и запоминаем рубрику */
const confirmDelete = (rubricOrId, title = null) => {
    if (typeof rubricOrId === 'object') {
        rubricToDeleteId.value = rubricOrId.id
        rubricToDeleteTitle.value = title || getRubricTitle(rubricOrId)
    } else {
        rubricToDeleteId.value = rubricOrId
        rubricToDeleteTitle.value = title || `ID: ${rubricOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрываем модалку удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    rubricToDeleteId.value = null
    rubricToDeleteTitle.value = ''
}

/** Удаление одной рубрики через destroy route */
const deleteRubric = () => {
    if (rubricToDeleteId.value === null) return

    const idToDelete = rubricToDeleteId.value
    const titleToDelete = rubricToDeleteTitle.value

    router.delete(route('admin.blogRubrics.destroy', { blogRubric: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Рубрика "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Рубрика: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal()
    })
}

/**
 * Обновление рубрики внутри дерева без полной перезагрузки страницы
 */
const patchRubricInTree = (nodes, rubricId, callback) => {
    for (const node of nodes) {
        if (node.id === rubricId) {
            callback(node)
            return true
        }

        if (node.children?.length && patchRubricInTree(node.children, rubricId, callback)) {
            return true
        }
    }

    return false
}

/** Обновление рубрики в плоском списке карточек */
const patchRubricInFlat = (rubricId, callback) => {
    const index = localRubricsFlat.value.findIndex((rubric) => rubric.id === rubricId)

    if (index !== -1) {
        callback(localRubricsFlat.value[index])
    }
}

/** Переключение активности одной рубрики */
const toggleActivity = (rubric) => {
    const newActivity = !rubric.activity
    const title = getRubricTitle(rubric)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogRubrics.updateActivity', { blogRubric: rubric.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchRubricInTree(localRubricsTree.value, rubric.id, (node) => {
                    node.activity = newActivity
                })

                patchRubricInFlat(rubric.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Рубрика "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            }
        }
    )
}

/**
 * Клонирование рубрики вместе с переводами и изображениями
 */
const cloneRubric = (rubric) => {
    const rubricId = rubric?.id
    const rubricTitle = getRubricTitle(rubric)

    if (!rubricId) {
        toast.error('Не удалось определить рубрику для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать рубрику "${rubricTitle}"?`)) return

    router.post(
        route('admin.actions.blogRubrics.clone', { blogRubric: rubricId }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => toast.success(`Рубрика "${rubricTitle}" успешно клонирована.`),
            onError: (errors) => {
                const errorKey = Object.keys(errors)[0]
                toast.error(errors[errorKey] || `Ошибка клонирования рубрики "${rubricTitle}".`)
            }
        }
    )
}

/**
 * Локальный поиск по уже загруженному списку рубрик
 */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/**
 * Локальная сортировка списка карточек.
 * Backend уже отдаёт отсортированный список, но здесь нужна быстрая
 * клиентская пересортировка при смене select.
 */
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

    if (sortParam.value === 'idAsc') {
        return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    }

    if (sortParam.value === 'idDesc') {
        return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
    }

    if (sortParam.value === 'sortAsc') {
        return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    }

    if (sortParam.value === 'sortDesc') {
        return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    }

    if (sortParam.value === 'activity') {
        return list.filter((rubric) => !!rubric.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((rubric) => !rubric.activity)
    }

    if (sortParam.value === 'locale') {
        return list.sort((a, b) => getRubricLocale(a).localeCompare(getRubricLocale(b), locale.value))
    }

    if (sortParam.value === 'viewsDesc') {
        return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    }

    if (sortParam.value === 'viewsAsc') {
        return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0))
    }

    if (sortParam.value === 'titleAsc') {
        return list.sort((a, b) => normalize(getRubricTitle(a)).localeCompare(normalize(getRubricTitle(b)), locale.value))
    }

    if (sortParam.value === 'titleDesc') {
        return list.sort((a, b) => normalize(getRubricTitle(b)).localeCompare(normalize(getRubricTitle(a)), locale.value))
    }

    if (sortParam.value === 'moderation_pending') {
        return list.filter((rubric) => moderationNum(rubric?.moderation_status) === 0)
    }

    if (sortParam.value === 'moderation_approved') {
        return list.filter((rubric) => moderationNum(rubric?.moderation_status) === 1)
    }

    if (sortParam.value === 'moderation_rejected') {
        return list.filter((rubric) => moderationNum(rubric?.moderation_status) === 2)
    }

    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }

    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    return list
}

/**
 * Фильтрация рубрик по поисковой строке + применение сортировки
 */
const filteredRubrics = computed(() => {
    let filtered = localRubricsFlat.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortRubrics(filtered)
    }

    filtered = filtered.filter((rubric) => {
        const title = normalize(getRubricTitle(rubric))
        const short = normalize(getRubricShort(rubric))
        const ownerName = normalize(rubric?.owner?.name)
        const ownerEmail = normalize(rubric?.owner?.email)

        return title.includes(query)
            || short.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortRubrics(filtered)
})

/**
 * Ручная клиентская пагинация для карточек
 */
const paginatedRubrics = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredRubrics.value.slice(start, start + perPage)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/**
 * После drag-and-drop собираем только изменённые элементы:
 * id, sort, parent_id.
 * Затем отправляем их на updateSortBulk.
 */
const handleDragEnd = () => {
    const changes = []

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
                    parent_id: parentId
                })
            }

            if (node.children?.length) {
                updateSortAndCollectChanges(node.children, node.id)
            }
        })
    }

    updateSortAndCollectChanges(localRubricsTree.value, null)

    const uniqueChanges = changes.reduce((acc, current) => {
        const existing = acc.find((item) => item.id === current.id)

        if (!existing) {
            return acc.concat([current])
        }

        Object.assign(existing, current)
        return acc
    }, [])

    if (!uniqueChanges.length) return

    router.put(
        route('admin.actions.blogRubrics.updateSortBulk'),
        { items: uniqueChanges },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия рубрик успешно обновлена'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || 'Ошибка обновления иерархии рубрик')
                router.reload({
                    only: ['rubricsTree', 'rubrics'],
                    preserveScroll: true
                })
            }
        }
    )
}

/**
 * Выбранные рубрики для массовых действий
 */
const selectedRubrics = ref([])

/** Получаем ID всех рубрик из дерева */
const getAllIds = (nodes) => {
    let ids = []

    nodes.forEach((node) => {
        ids.push(node.id)

        if (node.children?.length) {
            ids = ids.concat(getAllIds(node.children))
        }
    })

    return ids
}

/** Выбрать/снять все рубрики в текущем режиме отображения */
const toggleAll = (event) => {
    const checked = event.target?.checked
    const allNodeIds = viewMode.value === 'table'
        ? getAllIds(localRubricsTree.value)
        : paginatedRubrics.value.map((rubric) => rubric.id)

    selectedRubrics.value = checked ? allNodeIds : []
}

/** Выбрать или снять одну рубрику */
const toggleSelectRubric = (rubricId) => {
    const index = selectedRubrics.value.indexOf(rubricId)

    if (index > -1) {
        selectedRubrics.value.splice(index, 1)
    } else {
        selectedRubrics.value.push(rubricId)
    }
}

/** Локально обновляем activity у выбранных рубрик в дереве */
const updateActivityByIds = (nodes, ids, activity) => {
    nodes.forEach((node) => {
        if (ids.includes(node.id)) {
            node.activity = activity
        }

        if (node.children?.length) {
            updateActivityByIds(node.children, ids, activity)
        }
    })
}

/** Массовое включение/выключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedRubrics.value.length) {
        toast.warning('Выберите рубрики для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedRubrics.value]

    router.put(
        route('admin.actions.blogRubrics.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                updateActivityByIds(localRubricsTree.value, idsToUpdate, newActivity)

                localRubricsFlat.value = localRubricsFlat.value.map((rubric) => {
                    if (idsToUpdate.includes(rubric.id)) {
                        return { ...rubric, activity: newActivity }
                    }

                    return rubric
                })

                selectedRubrics.value = []
                toast.success('Статус активности массово обновлён')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            }
        }
    )
}

/** Массовое удаление выбранных рубрик */
const bulkDelete = () => {
    if (!selectedRubrics.value.length) {
        toast.warning('Выберите хотя бы одну рубрику для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные рубрики?')) return

    router.delete(route('admin.actions.blogRubrics.bulkDestroy'), {
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
        }
    })
}

/** Обработка select массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ target: { checked: true } })
    } else if (action === 'deselectAll') {
        toggleAll({ target: { checked: false } })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/**
 * Ссылка переключения локали списка рубрик
 */
const localeLink = (loc) => {
    return route('admin.blogRubrics.index', { locale: loc })
}

/**
 * Одобрение / отклонение рубрики администратором
 */
const approveRubric = (rubric, status = 1, note = '') => {
    if (!rubric?.id) return

    router.put(
        route('admin.actions.blogRubrics.approve', { blogRubric: rubric.id }),
        {
            moderation_status: status,
            moderation_note: note
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchRubricInTree(localRubricsTree.value, rubric.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                patchRubricInFlat(rubric.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Рубрика одобрена' : 'Рубрика отклонена')
            },
            onError: () => toast.error('Ошибка модерации рубрики')
        }
    )
}

/**
 * Обновление сортировки в режиме карточек
 */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
        parent_id: null
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.blogRubrics.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка рубрик обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки карточек:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            }
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
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.blogRubrics.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addRubric') }}
                    </DefaultButton>

                </div>

                <SearchInput v-if="rubricsCount" v-model="searchQuery" />

                <div v-if="rubricsCount && viewMode !== 'table'"
                     class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div v-if="rubricsCount"
                     class="flex items-center justify-between mb-3">
                    <CountTable>{{ rubricsCount }}</CountTable>
                    <BulkActionSelect
                        v-if="rubricsCount"
                        @change="handleBulkAction"
                    />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Дерево -->
                <div v-if="viewMode === 'table'"
                     class="border border-gray-400 bg-white dark:bg-slate-800">
                    <div
                        v-if="rubricsCount"
                        class="flex justify-between items-center px-3 py-2
                               border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    >
                        <div class="text-xs text-slate-600 dark:text-slate-200">
                            {{ t('selected') }}: {{ selectedRubrics.length }}
                        </div>

                        <label
                            class="flex items-center text-xs
                                   text-slate-600 dark:text-slate-200 cursor-pointer"
                        >
                            <span>{{ t('selectAll') }}</span>
                            <input
                                type="checkbox"
                                @change="toggleAll"
                                class="form-checkbox rounded-sm text-indigo-500 ml-2"
                                :title="t('selectAll')"
                            />
                        </label>
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
                                :is-admin="isAdmin"
                                @toggle-activity="toggleActivity"
                                @delete="confirmDelete"
                                @clone="cloneRubric"
                                @toggle-select="toggleSelectRubric"
                                @request-drag-end="handleDragEnd"
                                @approve="approveRubric"
                            />
                        </template>

                        <template #header v-if="localRubricsTree.length === 0 && rubricsCount > 0">
                            <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                                {{ t('loading') }}
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
                    :isAdmin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneRubric"
                    @toggle-select="toggleSelectRubric"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                    @approve="approveRubric"
                />

                <div v-if="rubricsCount && viewMode !== 'table'"
                     class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredRubrics.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
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
