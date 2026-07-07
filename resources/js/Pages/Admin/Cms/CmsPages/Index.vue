<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список CMS страниц
 * - режимы обработки: frontend | server | auto
 * - дерево страниц с drag&drop
 * - плоский список карточек
 * - переключатели: activity, in_menu, in_footer, show_content, show_seo
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/Cms/CmsPage/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Cms/CmsPage/Sort/SortSelect.vue'
import PageCardGrid from '@/Components/Admin/Cms/CmsPage/View/PageCardGrid.vue'
import PageTreeDraggable from '@/Components/Admin/Cms/CmsPage/Tree/PageTreeDraggable.vue'

/** Сервисы страницы */
const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminCmsPagesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    pagesTree: { type: Array, default: () => [] },
    pages: { type: [Array, Object], default: () => [] },
    pagesCount: { type: Number, default: 0 },

    adminCmsPagesPerPage: { type: Number, default: 6 },
    adminCmsPagesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка роли администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

const viewMode = ref(
    localStorage.getItem('admin_view_mode_cms_pages') || 'table'
)

/** Режим отображения: дерево или карточки */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_cms_pages', value)
})

/** Сохранение режима отображения */
const pagesList = computed(() => {
    if (Array.isArray(props.pages)) {
        return props.pages
    }

    if (Array.isArray(props.pages?.data)) {
        return props.pages.data
    }

    if (Array.isArray(props.pages?.data?.data)) {
        return props.pages.data.data
    }

    return []
})

/** Нормализация списка страниц из props */
const localPagesTree = ref([])

/** Локальные копии дерева и плоского списка */
const localPagesFlat = ref([])

/** Обновление локального дерева */
watch(
    () => props.pagesTree,
    (value) => {
        localPagesTree.value = JSON.parse(JSON.stringify(value || []))
    },
    { immediate: true, deep: true }
)

/** Обновление локального плоского списка */
watch(
    pagesList,
    (value) => {
        localPagesFlat.value = JSON.parse(JSON.stringify(value || []))
    },
    { immediate: true, deep: true }
)

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminCmsPagesPerPage || 6)

/** Сохранение количества элементов на странице */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountCmsPages'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} CMS страниц на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления количества CMS страниц.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam || props.adminCmsPagesDefaultSort || 'idDesc'
)

/** Сохранение выбранной сортировки */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortCmsPages'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing && viewMode.value !== 'table') {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(new URLSearchParams(window.location.search)),
                            sort: newVal || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка CMS страниц успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки CMS страниц.')
            },
        }
    )
})

/** Поисковая строка */
const searchQuery = ref(props.search || '')

/** Текущая страница локальной пагинации */
const currentPage = ref(1)

/** Нормализация значения для поиска/сортировки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное преобразование в число */
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/** Получение текущего перевода страницы */
const getPageTranslation = (cmsPage) => {
    return cmsPage?.translation || cmsPage?.translations?.[0] || {}
}

/** Получение названия страницы */
const getPageTitle = (cmsPage) => {
    return cmsPage?.title
        || getPageTranslation(cmsPage)?.title
        || `ID: ${cmsPage?.id}`
}

/** Получение краткого описания страницы */
const getPageShort = (cmsPage) => {
    return cmsPage?.short
        || getPageTranslation(cmsPage)?.short
        || ''
}

/** Получение полного контента страницы */
const getPageDescription = (cmsPage) => {
    return cmsPage?.description
        || getPageTranslation(cmsPage)?.description
        || ''
}

/** Получение названия родительской страницы */
const getParentTitle = (cmsPage) => {
    return cmsPage?.parent?.title
        || cmsPage?.parent?.translation?.title
        || cmsPage?.parent?.translations?.[0]?.title
        || ''
}

/** Получение имени владельца */
const getOwnerName = (cmsPage) => cmsPage?.owner?.name || ''

/** Получение email владельца */
const getOwnerEmail = (cmsPage) => cmsPage?.owner?.email || ''

/** Сортировка чисел по возрастанию */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел по убыванию */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка дат по возрастанию */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка дат по убыванию */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Локальная сортировка страниц */
const sortPages = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'inMenu') return list.filter(item => !!item.in_menu)
    if (sortParam.value === 'notInMenu') return list.filter(item => !item.in_menu)

    if (sortParam.value === 'inFooter') return list.filter(item => !!item.in_footer)
    if (sortParam.value === 'notInFooter') return list.filter(item => !item.in_footer)

    if (sortParam.value === 'showContent') return list.filter(item => !!item.show_content)
    if (sortParam.value === 'notShowContent') return list.filter(item => !item.show_content)

    if (sortParam.value === 'showSeo') return list.filter(item => !!item.show_seo)
    if (sortParam.value === 'notShowSeo') return list.filter(item => !item.show_seo)

    if (sortParam.value === 'statusDraft') return list.filter(item => item?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter(item => item?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter(item => item?.status === 'archived')

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        levelAsc: byNumberAsc('level'),
        levelDesc: byNumberDesc('level'),

        parentAsc: byNumberAsc('parent_id'),
        parentDesc: byNumberDesc('parent_id'),

        urlAsc: (a, b) =>
            normalize(a?.url).localeCompare(normalize(b?.url), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        urlDesc: (a, b) =>
            normalize(b?.url).localeCompare(normalize(a?.url), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        titleAsc: (a, b) =>
            normalize(getPageTitle(a)).localeCompare(normalize(getPageTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getPageTitle(b)).localeCompare(normalize(getPageTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        childrenAsc: byNumberAsc('children_count'),
        childrenDesc: byNumberDesc('children_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        inMenuAsc: byNumberAsc('in_menu'),
        inMenuDesc: byNumberDesc('in_menu'),

        inFooterAsc: byNumberAsc('in_footer'),
        inFooterDesc: byNumberDesc('in_footer'),

        showContentAsc: byNumberAsc('show_content'),
        showContentDesc: byNumberDesc('show_content'),

        showSeoAsc: byNumberAsc('show_seo'),
        showSeoDesc: byNumberDesc('show_seo'),

        ownerNameAsc: (a, b) =>
            normalize(getOwnerName(a)).localeCompare(normalize(getOwnerName(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerNameDesc: (a, b) =>
            normalize(getOwnerName(b)).localeCompare(normalize(getOwnerName(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        ownerEmailAsc: (a, b) =>
            normalize(getOwnerEmail(a)).localeCompare(normalize(getOwnerEmail(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        ownerEmailDesc: (a, b) =>
            normalize(getOwnerEmail(b)).localeCompare(normalize(getOwnerEmail(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: (a, b) =>
            normalize(a?.status).localeCompare(normalize(b?.status), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        statusDesc: (a, b) =>
            normalize(b?.status).localeCompare(normalize(a?.status), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),
        dateAsc: byDateAsc('created_at'),
        dateDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/** Локальный поиск страниц */
const filteredPages = computed(() => {
    let filtered = localPagesFlat.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortPages(filtered)
    }

    filtered = filtered.filter((cmsPage) => {
        const values = [
            cmsPage?.id,
            cmsPage?.url,
            cmsPage?.icon,
            cmsPage?.views,
            cmsPage?.status,
            getPageTitle(cmsPage),
            getPageShort(cmsPage),
            getPageDescription(cmsPage),
            getParentTitle(cmsPage),
            getOwnerName(cmsPage),
            getOwnerEmail(cmsPage),
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortPages(filtered)
})

/** Локальная пагинация страниц */
const paginatedPages = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredPages.value.slice(start, start + perPage)
})

/** Итоговый список для карточек */
const displayedPages = computed(() => {
    return props.useServerProcessing
        ? pagesList.value
        : paginatedPages.value
})

/** Сброс страницы при изменении пагинации или поиска */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const pageToDeleteId = ref(null)
const pageToDeleteTitle = ref('')

/** Подготовка подтверждения удаления */
const confirmDelete = (pageOrId, title = null) => {
    if (typeof pageOrId === 'object') {
        pageToDeleteId.value = pageOrId.id
        pageToDeleteTitle.value = title || getPageTitle(pageOrId)
    } else {
        pageToDeleteId.value = pageOrId
        pageToDeleteTitle.value = title || `ID: ${pageOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна удаления */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    pageToDeleteId.value = null
    pageToDeleteTitle.value = ''
}

/** Удаление одной страницы */
const deletePage = () => {
    if (pageToDeleteId.value === null) return

    const idToDelete = pageToDeleteId.value
    const titleToDelete = pageToDeleteTitle.value

    router.delete(route('admin.cmsPages.destroy', { cmsPage: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`CMS страница "${titleToDelete || 'ID: ' + idToDelete}" удалена.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Страница: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Обновление страницы в дереве */
const patchPageInTree = (nodes, pageId, callback) => {
    for (const node of nodes) {
        if (node.id === pageId) {
            callback(node)
            return true
        }

        if (node.children?.length && patchPageInTree(node.children, pageId, callback)) {
            return true
        }
    }

    return false
}

/** Обновление страницы в плоском списке */
const patchPageInFlat = (pageId, callback) => {
    const index = localPagesFlat.value.findIndex((cmsPage) => cmsPage.id === pageId)

    if (index !== -1) {
        callback(localPagesFlat.value[index])
    }
}

/** Переключение активности страницы */
const toggleActivity = (cmsPage) => {
    const newActivity = !cmsPage.activity
    const title = getPageTitle(cmsPage)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.cmsPages.updateActivity', { cmsPage: cmsPage.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchPageInTree(localPagesTree.value, cmsPage.id, node => {
                    node.activity = newActivity
                })

                patchPageInFlat(cmsPage.id, node => {
                    node.activity = newActivity
                })

                cmsPage.activity = newActivity
                toast.success(`CMS страница "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Переключение флагов страницы */
const toggleBooleanFlag = (cmsPage, field, routeName, payloadKey, enabledText, disabledText) => {
    const newValue = !cmsPage[field]
    const title = getPageTitle(cmsPage)

    router.put(
        route(routeName, { cmsPage: cmsPage.id }),
        { [payloadKey]: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchPageInTree(localPagesTree.value, cmsPage.id, node => {
                    node[field] = newValue
                })

                patchPageInFlat(cmsPage.id, node => {
                    node[field] = newValue
                })

                cmsPage[field] = newValue

                toast.success(
                    newValue
                        ? `CMS страница "${title}" ${enabledText}.`
                        : `CMS страница "${title}" ${disabledText}.`
                )
            },
            onError: (errors) => {
                toast.error(
                    errors[payloadKey]
                    || errors.general
                    || `Ошибка изменения настройки для "${title}".`
                )
            },
        }
    )
}

/** Переключение страницы активности в главном меню */
const toggleInMenu = (cmsPage) => {
    toggleBooleanFlag(
        cmsPage,
        'in_menu',
        'admin.actions.cmsPages.updateInMenu',
        'in_menu',
        'добавлена в меню',
        'скрыта из меню'
    )
}

/** Переключение страницы активности в меню подвала */
const toggleInFooter = (cmsPage) => {
    toggleBooleanFlag(
        cmsPage,
        'in_footer',
        'admin.actions.cmsPages.updateInFooter',
        'in_footer',
        'добавлена в футер',
        'скрыта из футера'
    )
}

/** Переключение показа контента страницы */
const toggleShowContent = (cmsPage) => {
    toggleBooleanFlag(
        cmsPage,
        'show_content',
        'admin.actions.cmsPages.updateShowContent',
        'show_content',
        'будет показывать свой HTML-контент',
        'не будет показывать свой HTML-контент'
    )
}

/** Переключение показа SEO страницы */
const toggleShowSeo = (cmsPage) => {
    toggleBooleanFlag(
        cmsPage,
        'show_seo',
        'admin.actions.cmsPages.updateShowSeo',
        'show_seo',
        'будет использовать своё SEO',
        'не будет использовать своё SEO'
    )
}

/** Обработка изменения дерева через drag&drop */
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
                    parent_id: parentId,
                })
            }

            if (node.children?.length) {
                updateSortAndCollectChanges(node.children, node.id)
            }
        })
    }

    updateSortAndCollectChanges(localPagesTree.value, null)

    if (!changes.length) return

    router.put(
        route('admin.actions.cmsPages.updateSortBulk'),
        { items: changes },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Иерархия CMS страниц успешно обновлена.'),
            onError: (errors) => {
                toast.error(errors.message || 'Ошибка обновления иерархии CMS страниц.')

                router.reload({
                    only: ['pagesTree', 'pages'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/** Выбранные страницы */
const selectedPages = ref([])

/** Получение всех ID из дерева */
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

/** Выбор/снятие выбора всех страниц */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false

    const ids = viewMode.value === 'table'
        ? getAllIds(localPagesTree.value)
        : displayedPages.value.map(cmsPage => cmsPage.id)

    selectedPages.value = checked ? ids : []
}

/** Выбор/снятие выбора всех карточек */
const toggleAllCards = ({ ids, checked }) => {
    selectedPages.value = checked ? [...ids] : []
}

/** Выбор одной страницы */
const toggleSelectPage = (pageId) => {
    const index = selectedPages.value.indexOf(pageId)

    if (index > -1) {
        selectedPages.value.splice(index, 1)
    } else {
        selectedPages.value.push(pageId)
    }
}

/** Обновление активности выбранных категорий в дереве */
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

/** Массовое переключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedPages.value.length) {
        toast.warning('Выберите CMS страницы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedPages.value]

    router.put(
        route('admin.actions.cmsPages.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                updateActivityByIds(localPagesTree.value, idsToUpdate, newActivity)

                localPagesFlat.value = localPagesFlat.value.map(item => {
                    return idsToUpdate.includes(item.id)
                        ? { ...item, activity: newActivity }
                        : item
                })

                selectedPages.value = []
                toast.success('Активность выбранных CMS страниц обновлена.')
            },
            onError: (errors) => {
                toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
            },
        }
    )
}

/** Массовое удаление страниц */
const bulkDelete = () => {
    if (!selectedPages.value.length) {
        toast.warning('Выберите хотя бы одну CMS страницу для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные CMS страницы?')) return

    router.delete(route('admin.actions.cmsPages.bulkDestroy'), {
        data: { ids: selectedPages.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedPages.value = []
            toast.success('Выбранные CMS страницы успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка при массовом удалении CMS страниц.')
        },
    })
}

/** Обработка выбранного массового действия */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ checked: true })
    } else if (action === 'deselectAll') {
        toggleAll({ checked: false })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('pages')">
        <template #header>
            <TitlePage>{{ t('pages') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.cmsPages.create')">
                        {{ t('addPage') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminCmsPagesProcessingMode"
                        :mode="adminCmsPagesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="pagesCount"
                    />
                </div>

                <SearchInput
                    v-if="pagesCount && !useServerProcessing && viewMode !== 'table'"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="pagesCount && useServerProcessing && viewMode !== 'table'"
                    v-model="searchQuery"
                />

                <div
                    v-if="pagesCount && viewMode !== 'table'"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing && viewMode !== 'table'"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-if="useServerProcessing && viewMode !== 'table'"
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountCmsPages"
                    />

                    <SortSelect
                        v-if="viewMode !== 'table'"
                        :sortParam="sortParam"
                        @update:sortParam="(value) => (sortParam = value)"
                    />
                </div>

                <div
                    v-if="pagesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                >
                    <CountTable>{{ pagesCount }}</CountTable>

                    <BulkActionSelect
                        :disabled="!selectedPages.length"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="pagesCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPages.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="pages"
                    />
                </div>

                <div
                    v-if="viewMode === 'table'"
                    class="mt-2 border border-gray-400 bg-white dark:bg-slate-800"
                >
                    <div
                        v-if="pagesCount"
                        class="flex justify-between items-center px-3 py-2
                               border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    >
                        <div class="text-xs text-slate-600 dark:text-slate-200">
                            {{ t('selected') }}: {{ selectedPages.length }}
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
                        v-model="localPagesTree"
                        item-key="id"
                        group="cms-pages"
                        handle=".drag-handle"
                        @end="handleDragEnd"
                        class="cms-page-tree-root p-1"
                    >
                        <template #item="{ element }">
                            <PageTreeDraggable
                                :page="element"
                                :selected-pages="selectedPages"
                                :is-admin="isAdmin"
                                @toggle-activity="toggleActivity"
                                @delete="confirmDelete"
                                @toggle-select="toggleSelectPage"
                                @request-drag-end="handleDragEnd"
                                @toggle-menu="toggleInMenu"
                                @toggle-footer="toggleInFooter"
                                @toggle-content="toggleShowContent"
                                @toggle-seo="toggleShowSeo"
                            />
                        </template>

                        <template #footer>
                            <div
                                v-if="!localPagesTree.length"
                                class="p-4 text-center text-slate-900 dark:text-slate-100"
                            >
                                {{ t('noData') }}
                            </div>
                        </template>
                    </draggable>
                </div>

                <PageCardGrid
                    v-else
                    :pages="displayedPages"
                    :selected-pages="selectedPages"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectPage"
                    @toggle-all="toggleAllCards"
                    @toggle-menu="toggleInMenu"
                    @toggle-footer="toggleInFooter"
                    @toggle-content="toggleShowContent"
                    @toggle-seo="toggleShowSeo"
                />

                <div
                    v-if="pagesCount && viewMode !== 'table'"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredPages.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="pages"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deletePage"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>

<style scoped>
.cms-page-tree-root {
    padding: 5px;
}
</style>
