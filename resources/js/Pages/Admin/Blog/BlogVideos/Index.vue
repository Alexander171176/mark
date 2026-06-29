<script setup>
/**
 * Видео блога — Index
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

import BulkActionSelect from '@/Components/Admin/Blog/BlogVideo/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/BlogVideo/Sort/SortSelect.vue'
import VideoTable from '@/Components/Admin/Blog/BlogVideo/Table/VideoTable.vue'
import VideoCardGrid from '@/Components/Admin/Blog/BlogVideo/View/VideoCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

const props = defineProps({
    videos: { type: [Array, Object], default: () => [] },
    videosCount: { type: Number, default: 0 },

    adminBlogVideosProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminBlogVideosPerPage: { type: Number, default: 20 },
    adminBlogVideosDefaultSort: { type: String, default: 'idDesc' },

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

const getVideoTranslation = (video) => video?.translation || {}
const getVideoTitle = (video) => getVideoTranslation(video)?.title || `ID: ${video?.id}`
const getVideoShort = (video) => getVideoTranslation(video)?.short || ''
const getVideoDescription = (video) => getVideoTranslation(video)?.description || ''

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const viewMode = ref(localStorage.getItem('admin_view_mode_videos') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_videos', value)
})

const itemsPerPage = ref(props.adminBlogVideosPerPage || 20)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountBlogVideos'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminBlogVideosDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortBlogVideos'),
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

const videosList = computed(() => {
    if (Array.isArray(props.videos)) {
        return props.videos
    }

    if (Array.isArray(props.videos?.data)) {
        return props.videos.data
    }

    return []
})

const localVideos = ref([])

watch(
    videosList,
    (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const showConfirmDeleteModal = ref(false)
const videoToDeleteId = ref(null)
const videoToDeleteTitle = ref('')

const confirmDelete = (videoOrId, title = null) => {
    if (typeof videoOrId === 'object') {
        videoToDeleteId.value = videoOrId.id
        videoToDeleteTitle.value = title || getVideoTitle(videoOrId)
    } else {
        videoToDeleteId.value = videoOrId
        videoToDeleteTitle.value = title || `ID: ${videoOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    videoToDeleteId.value = null
    videoToDeleteTitle.value = ''
}

const deleteVideo = () => {
    if (videoToDeleteId.value === null) return

    const idToDelete = videoToDeleteId.value
    const titleToDelete = videoToDeleteTitle.value

    router.delete(route('admin.blogVideos.destroy', { blogVideo: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Видео "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Видео: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const patchLocalVideo = (videoId, callback) => {
    const index = localVideos.value.findIndex((video) => video.id === videoId)

    if (index !== -1) {
        callback(localVideos.value[index])
    }
}

const toggleActivity = (video) => {
    const newActivity = !video.activity
    const title = getVideoTitle(video)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogVideos.updateActivity', { blogVideo: video.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalVideo(video.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Видео "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

const toggleLeft = (video) => {
    const newLeft = !video.left
    const title = getVideoTitle(video)

    router.put(
        route('admin.actions.blogVideos.updateLeft', { blogVideo: video.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalVideo(video.id, (node) => {
                    node.left = newLeft
                })

                toast.success(`Позиция left для видео "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`)
            },
        }
    )
}

const toggleMain = (video) => {
    const newMain = !video.main
    const title = getVideoTitle(video)

    router.put(
        route('admin.actions.blogVideos.updateMain', { blogVideo: video.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalVideo(video.id, (node) => {
                    node.main = newMain
                })

                toast.success(`Позиция main для видео "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`)
            },
        }
    )
}

const toggleRight = (video) => {
    const newRight = !video.right
    const title = getVideoTitle(video)

    router.put(
        route('admin.actions.blogVideos.updateRight', { blogVideo: video.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalVideo(video.id, (node) => {
                    node.right = newRight
                })

                toast.success(`Позиция right для видео "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`)
            },
        }
    )
}

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

const sortVideos = (videos) => {
    const list = (videos || []).slice()

    if (sortParam.value === 'activity') return list.filter((video) => !!video.activity)
    if (sortParam.value === 'inactive') return list.filter((video) => !video.activity)

    if (sortParam.value === 'public') return list.filter((video) => !video.is_private)
    if (sortParam.value === 'private') return list.filter((video) => !!video.is_private)

    if (sortParam.value === 'left') return list.filter((video) => !!video.left)
    if (sortParam.value === 'noLeft') return list.filter((video) => !video.left)
    if (sortParam.value === 'main') return list.filter((video) => !!video.main)
    if (sortParam.value === 'noMain') return list.filter((video) => !video.main)
    if (sortParam.value === 'right') return list.filter((video) => !!video.right)
    if (sortParam.value === 'noRight') return list.filter((video) => !video.right)

    if (sortParam.value === 'moderationPending') return list.filter((video) => moderationNum(video?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((video) => moderationNum(video?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((video) => moderationNum(video?.moderation_status) === 2)

    const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),

        sortAsc: (a, b) => (a.sort ?? 0) - (b.sort ?? 0),
        sortDesc: (a, b) => (b.sort ?? 0) - (a.sort ?? 0),

        titleAsc: (a, b) => normalize(getVideoTitle(a)).localeCompare(normalize(getVideoTitle(b)), locale.value),
        titleDesc: (a, b) => normalize(getVideoTitle(b)).localeCompare(normalize(getVideoTitle(a)), locale.value),

        urlAsc: (a, b) => normalize(a?.url).localeCompare(normalize(b?.url), locale.value),
        urlDesc: (a, b) => normalize(b?.url).localeCompare(normalize(a?.url), locale.value),

        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),

        privateAsc: (a, b) => Number(a.is_private) - Number(b.is_private),
        privateDesc: (a, b) => Number(b.is_private) - Number(a.is_private),

        viewsAsc: (a, b) => (a.views ?? 0) - (b.views ?? 0),
        viewsDesc: (a, b) => (b.views ?? 0) - (a.views ?? 0),

        likesAsc: (a, b) => (a.likes_count ?? 0) - (b.likes_count ?? 0),
        likesDesc: (a, b) => (b.likes_count ?? 0) - (a.likes_count ?? 0),

        commentsAsc: (a, b) => (a.comments_count ?? 0) - (b.comments_count ?? 0),
        commentsDesc: (a, b) => (b.comments_count ?? 0) - (a.comments_count ?? 0),

        sourceTypeAsc: (a, b) => normalize(a?.source_type).localeCompare(normalize(b?.source_type), locale.value),
        sourceTypeDesc: (a, b) => normalize(b?.source_type).localeCompare(normalize(a?.source_type), locale.value),

        durationAsc: (a, b) => (a.duration ?? 0) - (b.duration ?? 0),
        durationDesc: (a, b) => (b.duration ?? 0) - (a.duration ?? 0),

        leftAsc: (a, b) => Number(a.left) - Number(b.left),
        leftDesc: (a, b) => Number(b.left) - Number(a.left),

        mainAsc: (a, b) => Number(a.main) - Number(b.main),
        mainDesc: (a, b) => Number(b.main) - Number(a.main),

        rightAsc: (a, b) => Number(a.right) - Number(b.right),
        rightDesc: (a, b) => Number(b.right) - Number(a.right),

        imagesAsc: (a, b) => (a.images_count ?? 0) - (b.images_count ?? 0),
        imagesDesc: (a, b) => (b.images_count ?? 0) - (a.images_count ?? 0),

        articlesAsc: (a, b) => (a.articles_count ?? 0) - (b.articles_count ?? 0),
        articlesDesc: (a, b) => (b.articles_count ?? 0) - (a.articles_count ?? 0),

        relatedVideosAsc: (a, b) => (a.related_videos_count ?? 0) - (b.related_videos_count ?? 0),
        relatedVideosDesc: (a, b) => (b.related_videos_count ?? 0) - (a.related_videos_count ?? 0),

        showFromAtAsc: (a, b) => safeDate(a.show_from_at) - safeDate(b.show_from_at),
        showFromAtDesc: (a, b) => safeDate(b.show_from_at) - safeDate(a.show_from_at),

        showToAtAsc: (a, b) => safeDate(a.show_to_at) - safeDate(b.show_to_at),
        showToAtDesc: (a, b) => safeDate(b.show_to_at) - safeDate(a.show_to_at),

        publishedAtAsc: (a, b) => safeDate(a.published_at) - safeDate(b.published_at),
        publishedAtDesc: (a, b) => safeDate(b.published_at) - safeDate(a.published_at),

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

const filteredVideos = computed(() => {
    let filtered = localVideos.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortVideos(filtered)
    }

    filtered = filtered.filter((video) => {
        const values = [
            video?.id,
            video?.url,
            video?.external_video_id,
            video?.source_type,
            video?.embed_code,
            video?.moderation_note,
            getVideoTitle(video),
            getVideoShort(video),
            getVideoDescription(video),
            video?.owner?.name,
            video?.owner?.email,
            video?.moderator?.name,
            video?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortVideos(filtered)
})

const paginatedVideos = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredVideos.value.slice(start, start + perPage)
})

const displayedVideos = computed(() => {
    return props.useServerProcessing
        ? videosList.value
        : paginatedVideos.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const selectedVideos = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedVideos.value.map((video) => video.id)

    if (checked) {
        selectedVideos.value = [...new Set([...selectedVideos.value, ...ids])]
    } else {
        selectedVideos.value = selectedVideos.value.filter((id) => !ids.includes(id))
    }
}

const toggleSelectVideo = (videoId) => {
    const index = selectedVideos.value.indexOf(videoId)

    if (index > -1) {
        selectedVideos.value.splice(index, 1)
    } else {
        selectedVideos.value.push(videoId)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите видео для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    router.put(
        route('admin.actions.blogVideos.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localVideos.value = localVideos.value.map((video) => {
                    return idsToUpdate.includes(video.id)
                        ? { ...video, activity: newActivity }
                        : video
                })

                selectedVideos.value = []
                toast.success('Активность видео массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            },
        }
    )
}

const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите видео для массового действия')
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localVideos.value = localVideos.value.map((video) => {
                    return idsToUpdate.includes(video.id)
                        ? { ...video, [field]: newValue }
                        : video
                })

                selectedVideos.value = []
                toast.success(successMessage)
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = () => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите хотя бы одно видео для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные видео?')) return

    router.delete(route('admin.actions.blogVideos.bulkDestroy'), {
        data: { ids: selectedVideos.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedVideos.value = []
            toast.success('Массовое удаление видео успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении видео.')
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
    } else if (action === 'left') {
        bulkToggleFlag('left', true, 'admin.actions.blogVideos.bulkUpdateLeft', 'Видео добавлены в левую колонку')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.blogVideos.bulkUpdateLeft', 'Видео убраны из левой колонки')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.blogVideos.bulkUpdateMain', 'Видео добавлены в главный блок')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.blogVideos.bulkUpdateMain', 'Видео убраны из главного блока')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.blogVideos.bulkUpdateRight', 'Видео добавлены в правую колонку')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.blogVideos.bulkUpdateRight', 'Видео убраны из правой колонки')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

const approveVideo = (video, status = 1, note = '') => {
    if (!video?.id) return

    router.put(
        route('admin.actions.blogVideos.approve', { blogVideo: video.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalVideo(video.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Видео одобрено' : 'Видео отклонено')
            },
            onError: () => toast.error('Ошибка модерации видео'),
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
        route('admin.actions.blogVideos.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка видео обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки видео:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('videos')">
        <template #header>
            <TitlePage>{{ t('videos') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.blogVideos.create')">
                        {{ t('addVideo') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminBlogVideosProcessingMode"
                        :mode="adminBlogVideosProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="videosCount"
                    />
                </div>

                <SearchInput
                    v-if="videosCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="videosCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="videosCount"
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
                        update-route="admin.settings.updateAdminCountBlogVideos"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="videosCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ videosCount }}</CountTable>

                    <BulkActionSelect
                        v-if="videosCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="videosCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredVideos.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="videos"
                    />
                </div>

                <VideoTable
                    v-if="viewMode === 'table'"
                    :videos="displayedVideos"
                    :selected-videos="selectedVideos"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectVideo"
                    @toggle-all="toggleAll"
                    @approve="approveVideo"
                />

                <VideoCardGrid
                    v-else
                    :videos="displayedVideos"
                    :selected-videos="selectedVideos"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectVideo"
                    @toggle-all="toggleAll"
                    @approve="approveVideo"
                />

                <div
                    v-if="videosCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredVideos.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="videos"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteVideo"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
