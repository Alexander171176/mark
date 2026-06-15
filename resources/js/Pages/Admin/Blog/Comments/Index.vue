<script setup>
/**
 * Комментарии — Index
 * - frontend/server/auto режимы обработки
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/Blog/Comment/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/Comment/Sort/SortSelect.vue'
import CommentTable from '@/Components/Admin/Blog/Comment/Table/CommentTable.vue'
import CommentCardGrid from '@/Components/Admin/Blog/Comment/View/CommentCardGrid.vue'
import CommentDetailsModal from '@/Components/Admin/Blog/Comment/Modal/CommentDetailsModal.vue'

const { t, locale } = useI18n()
const toast = useToast()

const props = defineProps({
    comments: { type: [Array, Object], default: () => [] },
    commentsCount: { type: Number, default: 0 },

    adminCommentsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminCommentsPerPage: { type: Number, default: 10 },
    adminCommentsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    isAdmin: { type: Boolean, default: false },

    error: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

const isAdmin = computed(() => !!props.isAdmin)

const viewMode = ref(localStorage.getItem('admin_view_mode_comments') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_comments', val)
})

const commentsList = computed(() => {
    if (Array.isArray(props.comments)) {
        return props.comments
    }

    if (Array.isArray(props.comments?.data)) {
        return props.comments.data
    }

    return []
})

const localComments = ref([])

watch(
    commentsList,
    (newVal) => {
        localComments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const patchLocal = (id, patch) => {
    const idx = localComments.value.findIndex((comment) => comment.id === id)

    if (idx !== -1) {
        localComments.value[idx] = {
            ...localComments.value[idx],
            ...patch,
        }
    }
}

const itemsPerPage = ref(Number(props.adminCommentsPerPage || 10))

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountComments'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminCommentsDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortComments'),
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
                toast.error(errors?.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const shortType = (fullType) => {
    return fullType ? fullType.split('\\').pop() : ''
}

const sortComments = (comments) => {
    const list = (comments || []).slice()

    if (sortParam.value === 'activity') return list.filter((comment) => !!comment.activity)
    if (sortParam.value === 'inactive') return list.filter((comment) => !comment.activity)

    if (sortParam.value === 'moderationPending') return list.filter((comment) => moderationNum(comment?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((comment) => moderationNum(comment?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((comment) => moderationNum(comment?.moderation_status) === 2)

    const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),

        userNameAsc: (a, b) => normalize(a?.user?.name).localeCompare(normalize(b?.user?.name), locale.value),
        userNameDesc: (a, b) => normalize(b?.user?.name).localeCompare(normalize(a?.user?.name), locale.value),

        userEmailAsc: (a, b) => normalize(a?.user?.email).localeCompare(normalize(b?.user?.email), locale.value),
        userEmailDesc: (a, b) => normalize(b?.user?.email).localeCompare(normalize(a?.user?.email), locale.value),

        contentAsc: (a, b) => normalize(a?.content).localeCompare(normalize(b?.content), locale.value),
        contentDesc: (a, b) => normalize(b?.content).localeCompare(normalize(a?.content), locale.value),

        typeAsc: (a, b) => normalize(shortType(a?.commentable_type)).localeCompare(normalize(shortType(b?.commentable_type)), locale.value),
        typeDesc: (a, b) => normalize(shortType(b?.commentable_type)).localeCompare(normalize(shortType(a?.commentable_type)), locale.value),

        commentableTitleAsc: (a, b) => normalize(a?.commentable_title).localeCompare(normalize(b?.commentable_title), locale.value),
        commentableTitleDesc: (a, b) => normalize(b?.commentable_title).localeCompare(normalize(a?.commentable_title), locale.value),

        repliesAsc: (a, b) => (a.replies_count ?? 0) - (b.replies_count ?? 0),
        repliesDesc: (a, b) => (b.replies_count ?? 0) - (a.replies_count ?? 0),

        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),

        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),

        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),

        moderationStatusAsc: (a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredComments = computed(() => {
    let filtered = localComments.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortComments(filtered)
    }

    filtered = filtered.filter((comment) => {
        const values = [
            comment?.id,
            comment?.content,
            comment?.moderation_note,
            comment?.commentable_type,
            shortType(comment?.commentable_type),
            comment?.commentable_title,
            comment?.user?.name,
            comment?.user?.email,
            comment?.moderator?.name,
            comment?.moderator?.email,
            comment?.parent?.content,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortComments(filtered)
})

const paginatedComments = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredComments.value.slice(start, start + perPage)
})

const displayedComments = computed(() => {
    return props.useServerProcessing
        ? commentsList.value
        : paginatedComments.value
})

const selectedComments = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedComments.value.map((comment) => comment.id)

    if (checked) {
        selectedComments.value = [...new Set([...selectedComments.value, ...ids])]
    } else {
        selectedComments.value = selectedComments.value.filter((id) => !ids.includes(id))
    }
}

const toggleSelectComment = (commentId) => {
    const index = selectedComments.value.indexOf(commentId)

    if (index > -1) {
        selectedComments.value.splice(index, 1)
    } else {
        selectedComments.value.push(commentId)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedComments.value.length) {
        toast.warning('Выберите комментарии для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedComments.value]

    router.put(
        route('admin.actions.comments.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localComments.value = localComments.value.map((comment) => {
                    return idsToUpdate.includes(comment.id)
                        ? { ...comment, activity: newActivity }
                        : comment
                })

                selectedComments.value = []
                toast.success('Активность комментариев массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Не удалось массово обновить активность комментариев'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = async () => {
    if (!selectedComments.value.length) {
        toast.warning('Выберите хотя бы один комментарий для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные комментарии?')) return

    const ids = [...selectedComments.value]

    try {
        const res = await axios.delete(route('admin.actions.comments.bulkDestroy'), {
            data: { ids },
        })

        if (res?.data?.success) {
            localComments.value = localComments.value.filter((comment) => !ids.includes(comment.id))
            selectedComments.value = []
            toast.success(res.data.message || 'Массовое удаление комментариев успешно завершено.')
            return
        }

        toast.error(res?.data?.message || 'Произошла ошибка при удалении комментариев.')
    } catch (error) {
        console.error('Ошибка массового удаления:', error)
        toast.error('Произошла ошибка при удалении комментариев.')
    }
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

const showCommentDetailsModal = ref(false)
const commentDetails = ref(null)

const viewCommentDetails = (comment) => {
    commentDetails.value = comment
    showCommentDetailsModal.value = true
}

const closeCommentDetailsModal = () => {
    showCommentDetailsModal.value = false
    commentDetails.value = null
}

const showConfirmDeleteModal = ref(false)
const commentToDeleteId = ref(null)

const confirmDelete = (id) => {
    commentToDeleteId.value = id
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    commentToDeleteId.value = null
}

const deleteComment = () => {
    if (commentToDeleteId.value === null) return

    const idToDelete = commentToDeleteId.value

    router.delete(route('admin.comments.destroy', { comment: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Комментарий "ID: ${idToDelete}" удален.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors?.general || errors?.[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Комментарий: ID: ${idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const toggleActivity = (comment) => {
    const newActivity = !comment.activity

    axios.put(route('admin.actions.comments.updateActivity', { comment: comment.id }), {
        activity: newActivity,
    })
        .then((response) => {
            patchLocal(comment.id, { activity: response.data.activity })
            toast.success(response.data.message)
        })
        .catch((error) => {
            toast.error('Ошибка при изменении активности комментария.')
            console.error(error)
        })
}

const approveComment = (comment, status = 1, note = '') => {
    if (!isAdmin.value) {
        toast.error('Модерация доступна только администратору.')
        return
    }

    if (!comment?.id) return

    axios.put(
        route('admin.actions.comments.approve', { comment: comment.id }),
        {
            moderation_status: status,
            moderation_note: note,
        }
    )
        .then((response) => {
            const resource = response.data?.comment
            const data = resource?.data ? resource.data : resource

            if (data) {
                patchLocal(comment.id, {
                    moderation_status: data.moderation_status,
                    moderation_note: data.moderation_note,
                    moderated_by: data.moderated_by,
                    moderated_at: data.moderated_at,
                })
            } else {
                patchLocal(comment.id, {
                    moderation_status: status,
                    moderation_note: note,
                })
            }

            toast.success(response.data.message || 'Статус модерации обновлён')
        })
        .catch((error) => {
            if (error?.response?.status === 403) {
                toast.error('Доступ запрещён: только администратор может модерировать комментарии.')
                return
            }

            toast.error('Ошибка при обновлении модерации комментария.')
            console.error(error)
        })
}
</script>

<template>
    <AdminLayout :title="t('comments')">
        <template #header>
            <TitlePage>
                {{ t('comments') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3">
                    <ProcessingModeSwitcher
                        setting-key="adminCommentsProcessingMode"
                        :mode="adminCommentsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="commentsCount"
                    />
                </div>

                <SearchInput
                    v-if="commentsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="commentsCount && useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div
                    v-if="commentsCount"
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
                        update-route="admin.settings.updateAdminCountComments"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="commentsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ commentsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="commentsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="commentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredComments.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="comments"
                    />
                </div>

                <CommentTable
                    v-if="viewMode === 'table'"
                    :isAdmin="isAdmin"
                    :comments="displayedComments"
                    :selected-comments="selectedComments"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectComment"
                    @toggle-all="toggleAll"
                    @view-details="viewCommentDetails"
                    @approve-comment="approveComment"
                />

                <CommentCardGrid
                    v-else
                    :isAdmin="isAdmin"
                    :comments="displayedComments"
                    :selected-comments="selectedComments"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectComment"
                    @toggle-all="toggleAll"
                    @view-details="viewCommentDetails"
                    @approve-comment="approveComment"
                />

                <CommentDetailsModal
                    :show="showCommentDetailsModal"
                    :comment="commentDetails"
                    @close="closeCommentDetailsModal"
                />

                <div
                    v-if="commentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredComments.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="comments"
                    />
                </div>

                <div
                    v-if="props.error"
                    class="mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"
                >
                    {{ props.error }}
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteComment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
