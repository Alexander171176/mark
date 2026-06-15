<script setup>
/**
 * Теги блога — Index
 * - frontend/server/auto режимы обработки
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

import BulkActionSelect from '@/Components/Admin/Blog/BlogTag/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/BlogTag/Sort/SortSelect.vue'
import TagTable from '@/Components/Admin/Blog/BlogTag/Table/TagTable.vue'
import TagCardGrid from '@/Components/Admin/Blog/BlogTag/View/TagCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

const props = defineProps({
    tags: { type: [Array, Object], default: () => [] },
    tagsCount: { type: Number, default: 0 },

    adminBlogTagsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminBlogTagsPerPage: { type: Number, default: 20 },
    adminBlogTagsDefaultSort: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

const getTagTranslation = (tag) => tag?.translation || {}
const getTagName = (tag) => getTagTranslation(tag)?.name || `ID: ${tag?.id}`
const getTagShort = (tag) => getTagTranslation(tag)?.short || ''
const getTagDescription = (tag) => getTagTranslation(tag)?.description || ''

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const viewMode = ref(localStorage.getItem('admin_view_mode_tags') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_tags', value)
})

const itemsPerPage = ref(props.adminBlogTagsPerPage || 20)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountTags'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminBlogTagsDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortTags'),
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

                toast.info('Сортировка успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

const tagsList = computed(() => {
    if (Array.isArray(props.tags)) return props.tags
    if (Array.isArray(props.tags?.data)) return props.tags.data
    return []
})

const localTags = ref([])

watch(
    tagsList,
    (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const showConfirmDeleteModal = ref(false)
const tagToDeleteId = ref(null)
const tagToDeleteName = ref('')

const confirmDelete = (tagOrId, name = null) => {
    if (typeof tagOrId === 'object') {
        tagToDeleteId.value = tagOrId.id
        tagToDeleteName.value = name || getTagName(tagOrId)
    } else {
        tagToDeleteId.value = tagOrId
        tagToDeleteName.value = name || `ID: ${tagOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    tagToDeleteId.value = null
    tagToDeleteName.value = ''
}

const deleteTag = () => {
    if (tagToDeleteId.value === null) return

    const idToDelete = tagToDeleteId.value
    const nameToDelete = tagToDeleteName.value

    router.delete(route('admin.blogTags.destroy', { blogTag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Тег "${nameToDelete || 'ID: ' + idToDelete}" удалён.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Тег: ${nameToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const patchLocalTag = (tagId, callback) => {
    const index = localTags.value.findIndex((tag) => tag.id === tagId)

    if (index !== -1) {
        callback(localTags.value[index])
    }
}

const toggleActivity = (tag) => {
    const newActivity = !tag.activity
    const name = getTagName(tag)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogTags.updateActivity', { blogTag: tag.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalTag(tag.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Тег "${name}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${name}".`)
            },
        }
    )
}

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

const sortTags = (tags) => {
    const list = (tags || []).slice()

    if (sortParam.value === 'activity') return list.filter((tag) => !!tag.activity)
    if (sortParam.value === 'inactive') return list.filter((tag) => !tag.activity)

    if (sortParam.value === 'moderationPending') return list.filter((tag) => moderationNum(tag?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((tag) => moderationNum(tag?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((tag) => moderationNum(tag?.moderation_status) === 2)

    const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),

        sortAsc: (a, b) => (a.sort ?? 0) - (b.sort ?? 0),
        sortDesc: (a, b) => (b.sort ?? 0) - (a.sort ?? 0),

        nameAsc: (a, b) => normalize(getTagName(a)).localeCompare(normalize(getTagName(b)), locale.value),
        nameDesc: (a, b) => normalize(getTagName(b)).localeCompare(normalize(getTagName(a)), locale.value),

        slugAsc: (a, b) => normalize(a?.slug).localeCompare(normalize(b?.slug), locale.value),
        slugDesc: (a, b) => normalize(b?.slug).localeCompare(normalize(a?.slug), locale.value),

        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),

        viewsAsc: (a, b) => (a.views ?? 0) - (b.views ?? 0),
        viewsDesc: (a, b) => (b.views ?? 0) - (a.views ?? 0),

        articlesAsc: (a, b) => (a.articles_count ?? 0) - (b.articles_count ?? 0),
        articlesDesc: (a, b) => (b.articles_count ?? 0) - (a.articles_count ?? 0),

        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),

        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),

        moderationStatusAsc: (a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status),

        ownerNameAsc: (a, b) => normalize(a?.owner?.name).localeCompare(normalize(b?.owner?.name), locale.value),
        ownerNameDesc: (a, b) => normalize(b?.owner?.name).localeCompare(normalize(a?.owner?.name), locale.value),

        ownerEmailAsc: (a, b) => normalize(a?.owner?.email).localeCompare(normalize(b?.owner?.email), locale.value),
        ownerEmailDesc: (a, b) => normalize(b?.owner?.email).localeCompare(normalize(a?.owner?.email), locale.value),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredTags = computed(() => {
    let filtered = localTags.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortTags(filtered)
    }

    filtered = filtered.filter((tag) => {
        const values = [
            tag?.id,
            tag?.slug,
            tag?.icon,
            tag?.views,
            tag?.moderation_note,
            getTagName(tag),
            getTagShort(tag),
            getTagDescription(tag),
            tag?.owner?.name,
            tag?.owner?.email,
            tag?.moderator?.name,
            tag?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortTags(filtered)
})

const paginatedTags = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredTags.value.slice(start, start + perPage)
})

const displayedTags = computed(() => {
    return props.useServerProcessing
        ? tagsList.value
        : paginatedTags.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const selectedTags = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedTags.value.map((tag) => tag.id)

    if (checked) {
        selectedTags.value = [...new Set([...selectedTags.value, ...ids])]
    } else {
        selectedTags.value = selectedTags.value.filter((id) => !ids.includes(id))
    }
}

const toggleSelectTag = (tagId) => {
    const index = selectedTags.value.indexOf(tagId)

    if (index > -1) {
        selectedTags.value.splice(index, 1)
    } else {
        selectedTags.value.push(tagId)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите теги для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedTags.value]

    router.put(
        route('admin.actions.blogTags.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localTags.value = localTags.value.map((tag) => {
                    if (idsToUpdate.includes(tag.id)) {
                        return { ...tag, activity: newActivity }
                    }

                    return tag
                })

                selectedTags.value = []
                toast.success('Активность тегов массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = () => {
    if (!selectedTags.value.length) {
        toast.warning('Выберите хотя бы один тег для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные теги?')) return

    router.delete(route('admin.actions.blogTags.bulkDestroy'), {
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

const approveTag = (tag, status = 1, note = '') => {
    if (!tag?.id) return

    router.put(
        route('admin.actions.blogTags.approve', { blogTag: tag.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalTag(tag.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Тег одобрен' : 'Тег отклонён')
            },
            onError: () => toast.error('Ошибка модерации тега'),
        }
    )
}

const handleSortOrderUpdate = (newOrderIds) => {
    const items = newOrderIds.map((id, index) => ({
        id,
        sort: index,
    }))

    if (!items.length) return

    router.put(
        route('admin.actions.blogTags.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка тегов обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки тегов:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('tags')">
        <template #header>
            <TitlePage>{{ t('tags') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.blogTags.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addTag') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminBlogTagsProcessingMode"
                        :mode="adminBlogTagsProcessingMode"
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
                        update-route="admin.settings.updateAdminCountTags"
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
