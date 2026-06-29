<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список тегов MarketTag
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { defineProps, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'

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

import BulkActionSelect from '@/Components/Admin/Market/MarketTag/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketTag/Sort/SortSelect.vue'
import TagTable from '@/Components/Admin/Market/MarketTag/Table/TagTable.vue'
import TagCardGrid from '@/Components/Admin/Market/MarketTag/View/TagCardGrid.vue'

/** Локализация и сервисы */
const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminMarketTagsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketTagsPerPage: { type: Number, default: 10 },
    adminMarketTagsDefaultSort: { type: String, default: 'idDesc' },

    tags: { type: [Array, Object], default: () => [] },
    tagsCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка роли администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Получение перевода тега */
const getTagTranslation = (tag) => tag?.translation || tag?.translations?.[0] || {}

/** Получение названия тега */
const getTagTitle = (tag) => getTagTranslation(tag)?.title || `ID: ${tag?.id}`

/** Получение краткого описания тега */
const getTagShort = (tag) => getTagTranslation(tag)?.short || ''

/** Получение полного описания тега */
const getTagDescription = (tag) => getTagTranslation(tag)?.description || ''

/** Получение имени владельца */
const getOwnerName = (tag) => tag?.owner?.name || ''

/** Получение email владельца */
const getOwnerEmail = (tag) => tag?.owner?.email || ''

/** Нормализация строки */
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

/** Нормализация статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения списка */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_tags') || 'cards'
)

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_tags', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketTagsPerPage || 10)

/** Обновление количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountMarketTags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} тегов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва тегов.'),
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(
    props.sortParam || props.adminMarketTagsDefaultSort || 'idDesc'
)

/** Обновление сортировки */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketTags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
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

                toast.info('Сортировка тегов успешно изменена.')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки тегов.')
            },
        }
    )
})

/** Локальный список тегов */
const localTags = ref([])

/** Нормализация списка тегов */
const tagsList = computed(() => {
    if (Array.isArray(props.tags)) {
        return props.tags
    }

    if (Array.isArray(props.tags?.data)) {
        return props.tags.data
    }

    if (Array.isArray(props.tags?.data?.data)) {
        return props.tags.data.data
    }

    if (Array.isArray(props.tags?.resource)) {
        return props.tags.resource
    }

    return []
})

/** Синхронизация локального списка */
watch(
    tagsList,
    (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модальное окно удаления */
const showConfirmDeleteModal = ref(false)
const tagToDeleteId = ref(null)
const tagToDeleteTitle = ref('')

/** Подготовка удаления тега */
const confirmDelete = (tagOrId, title = null) => {
    if (typeof tagOrId === 'object') {
        tagToDeleteId.value = tagOrId.id
        tagToDeleteTitle.value = title || getTagTitle(tagOrId)
    } else {
        tagToDeleteId.value = tagOrId
        tagToDeleteTitle.value = title || `ID: ${tagOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    tagToDeleteId.value = null
    tagToDeleteTitle.value = ''
}

/** Удаление тега */
const deleteTag = () => {
    if (tagToDeleteId.value === null) return

    const idToDelete = tagToDeleteId.value
    const titleToDelete = tagToDeleteTitle.value

    router.delete(route('admin.marketTags.destroy', { marketTag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Тег "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Тег: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}

/** Локальное обновление тега */
const patchLocalTag = (tagId, callback) => {
    const index = localTags.value.findIndex((tag) => tag.id === tagId)

    if (index !== -1) {
        callback(localTags.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (tag) => {
    const newActivity = !tag.activity
    const title = getTagTitle(tag)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketTags.updateActivity', { marketTag: tag.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalTag(tag.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Тег "${title}" ${actionText}.`)
            },

            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Поиск и пагинация */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Сортировка по числу по возрастанию */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка по числу по убыванию */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка по дате по возрастанию */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка по дате по убыванию */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка тегов */
const sortTags = (tags) => {
    const list = (tags || []).slice()

    if (sortParam.value === 'activity') return list.filter((tag) => !!tag.activity)
    if (sortParam.value === 'inactive') return list.filter((tag) => !tag.activity)

    if (sortParam.value === 'statusDraft') return list.filter((tag) => tag?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter((tag) => tag?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter((tag) => tag?.status === 'archived')

    if (sortParam.value === 'moderationPending') return list.filter((tag) => moderationNum(tag?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((tag) => moderationNum(tag?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((tag) => moderationNum(tag?.moderation_status) === 2)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getTagTitle(a)).localeCompare(normalize(getTagTitle(b)), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getTagTitle(b)).localeCompare(normalize(getTagTitle(a)), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        urlAsc: (a, b) =>
            normalize(a?.url).localeCompare(normalize(b?.url), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        urlDesc: (a, b) =>
            normalize(b?.url).localeCompare(normalize(a?.url), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        colorAsc: (a, b) =>
            normalize(a?.color).localeCompare(normalize(b?.color), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        colorDesc: (a, b) =>
            normalize(b?.color).localeCompare(normalize(a?.color), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: (a, b) =>
            normalize(a?.status).localeCompare(normalize(b?.status), locale.value)
            || safeNumber(a?.id) - safeNumber(b?.id),

        statusDesc: (a, b) =>
            normalize(b?.status).localeCompare(normalize(a?.status), locale.value)
            || safeNumber(b?.id) - safeNumber(a?.id),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        moderationStatusAsc: (a, b) =>
            moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status)
            || safeNumber(a?.id) - safeNumber(b?.id),

        moderationStatusDesc: (a, b) =>
            moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status)
            || safeNumber(b?.id) - safeNumber(a?.id),

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

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        showFromAtAsc: byDateAsc('show_from_at'),
        showFromAtDesc: byDateDesc('show_from_at'),

        showToAtAsc: byDateAsc('show_to_at'),
        showToAtDesc: byDateDesc('show_to_at'),

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

/** Фильтрация тегов */
const filteredTags = computed(() => {
    let filtered = localTags.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortTags(filtered)
    }

    filtered = filtered.filter((tag) => {
        const values = [
            tag?.id,
            tag?.url,
            tag?.icon,
            tag?.color,
            tag?.status,
            tag?.moderation_note,
            tag?.views,
            getTagTitle(tag),
            getTagShort(tag),
            getTagDescription(tag),
            getOwnerName(tag),
            getOwnerEmail(tag),
            tag?.moderator?.name,
            tag?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortTags(filtered)
})

/** Пагинация тегов */
const paginatedTags = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredTags.value.slice(start, start + perPage)
})

/** Список для отображения */
const displayedTags = computed(() => {
    return props.useServerProcessing
        ? tagsList.value
        : paginatedTags.value
})

/** Сброс страницы при изменениях */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные теги */
const selectedTags = ref([])

/** Массовое выделение */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedTags.value.map((tag) => tag.id)

    if (checked) {
        selectedTags.value = [...new Set([...selectedTags.value, ...ids])]
    } else {
        selectedTags.value = selectedTags.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора тега */
const toggleSelectTag = (tagId) => {
    const index = selectedTags.value.indexOf(tagId)

    if (index > -1) {
        selectedTags.value.splice(index, 1)
    } else {
        selectedTags.value.push(tagId)
    }
}

/** Массовое обновление активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите теги для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedTags.value]

    router.put(
        route('admin.actions.marketTags.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localTags.value = localTags.value.map((tag) => {
                    return idsToUpdate.includes(tag.id)
                        ? { ...tag, activity: newActivity }
                        : tag
                })

                selectedTags.value = []
                toast.success('Активность тегов массово обновлена.')
            },

            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите хотя бы один тег для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные теги?')) return

    router.delete(route('admin.actions.marketTags.bulkDestroy'), {
        data: { ids: selectedTags.value },
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            selectedTags.value = []
            toast.success('Массовое удаление тегов успешно завершено.')
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении тегов.')
        },
    })
}

/** Обработка массовых действий */
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

/** Модерация тега */
const approveTag = (tag, status = 1, note = '') => {
    if (!tag?.id) return

    router.put(
        route('admin.actions.marketTags.approve', { marketTag: tag.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalTag(tag.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Тег одобрен.' : 'Тег отклонён.')
            },

            onError: () => toast.error('Ошибка модерации тега.'),
        }
    )
}

/** Обновление порядка сортировки */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.marketTags.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => toast.success('Сортировка тегов обновлена.'),

            onError: (errors) => {
                console.error('Ошибка сортировки тегов:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки.')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketTags')">
        <template #header>
            <TitlePage>{{ t('marketTags') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketTags.create')">
                        {{ t('addMarketTag') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketTagsProcessingMode"
                        :mode="adminMarketTagsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="tagsCount"
                    />
                </div>

                <SearchInput
                    v-if="tagsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="tagsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="tagsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountMarketTags"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="tagsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ tagsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="tagsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="tagsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTags.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="tags"
                    />
                </div>

                <TagTable
                    v-if="viewMode === 'table'"
                    :tags="displayedTags"
                    :selected-tags="selectedTags"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectTag"
                    @toggle-all="toggleAll"
                    @approve="approveTag"
                />

                <TagCardGrid
                    v-else
                    :tags="displayedTags"
                    :selected-tags="selectedTags"
                    :is-admin="isAdmin"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectTag"
                    @toggle-all="toggleAll"
                    @approve="approveTag"
                />

                <div
                    v-if="tagsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredTags.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="tags"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteTag"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
