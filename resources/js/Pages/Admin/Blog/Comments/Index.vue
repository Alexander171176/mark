<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр
 * Комментарии — список + модерация + bulk actions (как у тегов)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import BulkActionSelect from '@/Components/Admin/Comment/Select/BulkActionSelect.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import SortSelect from '@/Components/Admin/Comment/Sort/SortSelect.vue'
import CommentTable from '@/Components/Admin/Comment/Table/CommentTable.vue'
import CommentCardGrid from '@/Components/Admin/Comment/View/CommentCardGrid.vue'
import CommentDetailsModal from '@/Components/Admin/Comment/Modal/CommentDetailsModal.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

const { t, locale } = useI18n()
const toast = useToast()

/** Props — как у тегов: массив + счетчики + настройки */
const props = defineProps({
    comments: Array,
    commentsCount: Number,

    adminCountComments: Number,
    adminSortComments: String,

    isAdmin: Boolean,

    error: String,
})

/** isAdmin берем из props (бэк — источник правды) */
const isAdmin = computed(() => !!props.isAdmin)

/** Вид: table/cards */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Локальная копия списка комментариев (для оптимистичных обновлений) */
const localComments = ref([])
watch(
    () => props.comments,
    (newVal) => {
        localComments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Оптимистичный патч */
const patchLocal = (id, patch) => {
    const idx = localComments.value.findIndex(c => c.id === id)
    if (idx !== -1) localComments.value[idx] = { ...localComments.value[idx], ...patch }
}

/** Пагинация */
const itemsPerPage = ref(Number(props.adminCountComments || 10))
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountComments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметры сортировки */
const sortParam = ref(props.adminSortComments || 'idDesc')
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortComments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления сортировки.'),
    })
})

/** Поиск/пагинация */
const currentPage = ref(1)
const searchQuery = ref('')

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** helpers */
const normalize = (v) => (v ?? '').toString().trim().toLowerCase()
const moderationNum = (v) => {
    const n = Number(v)
    return Number.isFinite(n) ? n : 0
}
const shortType = (fullType) => (fullType ? fullType.split('\\').pop() : '')

/** Сортировка + фильтры (локально, как у тегов) */
const sortComments = (comments) => {
    const list = (comments || []).slice()

    // id
    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

    // flags/filters
    if (sortParam.value === 'activity') return list.filter(c => !!c.activity)
    if (sortParam.value === 'inactive') return list.filter(c => !c.activity)

    // moderation filters
    if (sortParam.value === 'moderation_pending') return list.filter(c => moderationNum(c?.moderation_status) === 0)
    if (sortParam.value === 'moderation_approved') return list.filter(c => moderationNum(c?.moderation_status) === 1)
    if (sortParam.value === 'moderation_rejected') return list.filter(c => moderationNum(c?.moderation_status) === 2)

    // moderation sorting
    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }
    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    // type
    if (sortParam.value === 'type') {
        return list.sort((a, b) => normalize(shortType(a?.commentable_type)).localeCompare(normalize(shortType(b?.commentable_type)), locale.value))
    }

    // user
    if (sortParam.value === 'user') {
        return list.sort((a, b) => normalize(a?.user?.name).localeCompare(normalize(b?.user?.name), locale.value))
    }

    return list
}

/** Фильтр поиска: content + user.name + type + commentable_title */
const filteredComments = computed(() => {
    let filtered = localComments.value || []
    const q = normalize(searchQuery.value)

    if (q) {
        filtered = filtered.filter(c => {
            const content = normalize(c?.content)
            const userName = normalize(c?.user?.name)
            const type = normalize(shortType(c?.commentable_type))
            const title = normalize(c?.commentable_title)
            return content.includes(q) || userName.includes(q) || type.includes(q) || title.includes(q)
        })
    }

    return sortComments(filtered)
})

/** Пагинация */
const paginatedComments = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredComments.value.slice(start, start + per)
})

/** Массовые действия */
const selectedComments = ref([])

const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedComments.value = [...new Set([...selectedComments.value, ...ids])]
    } else {
        selectedComments.value = selectedComments.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectComment = (commentId) => {
    const index = selectedComments.value.indexOf(commentId)
    if (index > -1) selectedComments.value.splice(index, 1)
    else selectedComments.value.push(commentId)
}

/** bulk toggle activity (redirect метод в контроллере) */
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
                localComments.value = localComments.value.map(c => {
                    if (idsToUpdate.includes(c.id)) return { ...c, activity: newActivity }
                    return c
                })
                selectedComments.value = []
                toast.success('Активность комментариев массово обновлена')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность комментариев'
                toast.error(msg)
            },
        }
    )
}

/** bulk delete — ВАЖНО: bulkDestroy у тебя JSON, поэтому делаем axios */
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
            localComments.value = localComments.value.filter(c => !ids.includes(c.id))
            selectedComments.value = []
            toast.success(res.data.message || 'Массовое удаление комментариев успешно завершено.')
            // если ты реально возвращаешь reload:true — можно не делать reload, мы уже обновили локально
            return
        }

        toast.error(res?.data?.message || 'Произошла ошибка при удалении комментариев.')

    } catch (e) {
        console.error('Ошибка массового удаления:', e)
        toast.error('Произошла ошибка при удалении комментариев.')
    }
}

/** bulk actions select */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedComments.value = paginatedComments.value.map(c => c.id)
    } else if (action === 'deselectAll') {
        selectedComments.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Модальное окно просмотра */
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

/** Модалка удаления (single) */
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

/** delete single (redirect) */
const deleteComment = () => {
    if (commentToDeleteId.value === null) return

    const idToDelete = commentToDeleteId.value

    router.delete(route('admin.comments.destroy', { comment: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Комментарий "ID: ${idToDelete}" удален.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors?.general || errors?.[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Комментарий: ID: ${idToDelete})`)
        },
        onFinish: () => {
            commentToDeleteId.value = null
        }
    })
}

/** toggle activity (single) — JSON */
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

/** approve / reject (single) — JSON (как теги по контракту) */
const approveComment = (comment, status = 1, note = '') => {
    if (!isAdmin.value) {
        toast.error('Модерация доступна только администратору.')
        return
    }
    if (!comment?.id) return

    axios.put(
        route('admin.actions.comments.approve', { comment: comment.id }),
        { moderation_status: status, moderation_note: note }
    )
        .then((response) => {
            const resource = response.data?.comment

            // у нас comment приходит CommentResource (один в один как теги)
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
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                  overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                  bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-end sm:items-center mb-2">
                    <BulkActionSelect v-if="commentsCount" @change="handleBulkAction" />
                </div>

                <SearchInput v-if="commentsCount" v-model="searchQuery" :placeholder="t('search')" />

                <!-- Count + переключатель вида -->
                <div v-if="commentsCount" class="flex items-center justify-between my-2">
                    <div class="flex items-center gap-3">
                        <CountTable>{{ commentsCount }}</CountTable>
                    </div>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Таблица -->
                <CommentTable
                    v-if="viewMode === 'table'"
                    :isAdmin="isAdmin"
                    :comments="paginatedComments"
                    :selected-comments="selectedComments"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectComment"
                    @toggle-all="toggleAll"
                    @view-details="viewCommentDetails"
                    @approve-comment="approveComment"
                />

                <!-- Карточки -->
                <CommentCardGrid
                    v-else
                    :isAdmin="isAdmin"
                    :comments="paginatedComments"
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

                <div class="flex justify-between items-center flex-col md:flex-row my-1" v-if="commentsCount">
                    <ItemsPerPageSelect :items-per-page="itemsPerPage" @update:itemsPerPage="itemsPerPage = $event" />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredComments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect :sortParam="sortParam" @update:sortParam="val => sortParam = val" />
                </div>

                <div v-if="props.error" class="mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300">
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
