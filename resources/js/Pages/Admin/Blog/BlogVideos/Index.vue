<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Видео блога — Index
 * Новая мультиязычная архитектура:
 * - blog_videos
 * - blog_video_translations
 */
import { defineProps, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'

import BulkActionSelect from '@/Components/Admin/Blog/Video/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/Video/Sort/SortSelect.vue'
import VideoTable from '@/Components/Admin/Blog/Video/Table/VideoTable.vue'
import VideoCardGrid from '@/Components/Admin/Blog/Video/View/VideoCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Props приходят из BlogVideoController@index */
const props = defineProps({
    videos: { type: Array, default: () => [] },
    videosCount: { type: Number, default: 0 },

    adminCountVideos: { type: Number, default: 15 },
    adminSortVideos: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },

    search: { type: String, default: '' },
    sortParam: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

/** Проверяем, является ли текущий пользователь администратором */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Текущий перевод видео */
const getVideoTranslation = (video) => video?.translation || {}

/** Заголовок видео */
const getVideoTitle = (video) => {
    return getVideoTranslation(video)?.title || `ID: ${video?.id}`
}

/** Краткое описание видео */
const getVideoShort = (video) => {
    return getVideoTranslation(video)?.short || ''
}

/** Описание видео */
const getVideoDescription = (video) => {
    return getVideoTranslation(video)?.description || ''
}

/** Локаль текущего перевода */
const getVideoLocale = (video) => {
    return getVideoTranslation(video)?.locale || props.currentLocale || ''
}

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное число статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения */
const viewMode = ref(localStorage.getItem('admin_view_mode_videos') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_videos', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminCountVideos || 15)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountVideos'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSortVideos || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortVideos'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
        }
    )
})

/** Локальная копия видео */
const localVideos = ref([])

watch(
    () => props.videos,
    (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const videoToDeleteId = ref(null)
const videoToDeleteTitle = ref('')

/** Открыть модалку удаления */
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

/** Закрыть модалку */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    videoToDeleteId.value = null
    videoToDeleteTitle.value = ''
}

/** Удалить видео */
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
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Видео: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Локальный patch */
const patchLocalVideo = (videoId, callback) => {
    const index = localVideos.value.findIndex((video) => video.id === videoId)

    if (index !== -1) {
        callback(localVideos.value[index])
    }
}

/** Переключение активности */
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

/** Переключение left */
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

/** Переключение main */
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

/** Переключение right */
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

/** Поиск */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Локальная сортировка */
const sortVideos = (videos) => {
    const list = (videos || []).slice()

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

    if (sortParam.value === 'publishedAtDesc') {
        return list.sort((a, b) => new Date(b.published_at || 0) - new Date(a.published_at || 0))
    }

    if (sortParam.value === 'publishedAtAsc') {
        return list.sort((a, b) => new Date(a.published_at || 0) - new Date(b.published_at || 0))
    }

    if (sortParam.value === 'titleAsc') {
        return list.sort((a, b) => normalize(getVideoTitle(a)).localeCompare(normalize(getVideoTitle(b)), locale.value))
    }

    if (sortParam.value === 'titleDesc') {
        return list.sort((a, b) => normalize(getVideoTitle(b)).localeCompare(normalize(getVideoTitle(a)), locale.value))
    }

    if (sortParam.value === 'activity') {
        return list.filter((video) => !!video.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((video) => !video.activity)
    }

    if (sortParam.value === 'private') {
        return list.filter((video) => !!video.is_private)
    }

    if (sortParam.value === 'public') {
        return list.filter((video) => !video.is_private)
    }

    if (sortParam.value === 'left') {
        return list.filter((video) => !!video.left)
    }

    if (sortParam.value === 'noLeft') {
        return list.filter((video) => !video.left)
    }

    if (sortParam.value === 'main') {
        return list.filter((video) => !!video.main)
    }

    if (sortParam.value === 'noMain') {
        return list.filter((video) => !video.main)
    }

    if (sortParam.value === 'right') {
        return list.filter((video) => !!video.right)
    }

    if (sortParam.value === 'noRight') {
        return list.filter((video) => !video.right)
    }

    if (sortParam.value === 'locale') {
        return list.sort((a, b) => getVideoLocale(a).localeCompare(getVideoLocale(b), locale.value))
    }

    if (sortParam.value === 'views' || sortParam.value === 'viewsDesc') {
        return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    }

    if (sortParam.value === 'viewsAsc') {
        return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0))
    }

    if (sortParam.value === 'likes' || sortParam.value === 'likesDesc') {
        return list.sort((a, b) => (b.likes_count ?? 0) - (a.likes_count ?? 0))
    }

    if (sortParam.value === 'likesAsc') {
        return list.sort((a, b) => (a.likes_count ?? 0) - (b.likes_count ?? 0))
    }

    if (sortParam.value === 'durationDesc') {
        return list.sort((a, b) => (b.duration ?? 0) - (a.duration ?? 0))
    }

    if (sortParam.value === 'durationAsc') {
        return list.sort((a, b) => (a.duration ?? 0) - (b.duration ?? 0))
    }

    if (sortParam.value === 'moderation_pending') {
        return list.filter((video) => moderationNum(video?.moderation_status) === 0)
    }

    if (sortParam.value === 'moderation_approved') {
        return list.filter((video) => moderationNum(video?.moderation_status) === 1)
    }

    if (sortParam.value === 'moderation_rejected') {
        return list.filter((video) => moderationNum(video?.moderation_status) === 2)
    }

    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }

    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    return list
}

/** Фильтрация видео */
const filteredVideos = computed(() => {
    let filtered = localVideos.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortVideos(filtered)
    }

    filtered = filtered.filter((video) => {
        const title = normalize(getVideoTitle(video))
        const short = normalize(getVideoShort(video))
        const description = normalize(getVideoDescription(video))
        const url = normalize(video?.url)
        const externalVideoId = normalize(video?.external_video_id)
        const sourceType = normalize(video?.source_type)
        const ownerName = normalize(video?.owner?.name)
        const ownerEmail = normalize(video?.owner?.email)

        return title.includes(query)
            || short.includes(query)
            || description.includes(query)
            || url.includes(query)
            || externalVideoId.includes(query)
            || sourceType.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortVideos(filtered)
})

/** Пагинация */
const paginatedVideos = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredVideos.value.slice(start, start + perPage)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные видео */
const selectedVideos = ref([])

/** Выбрать/снять все видео */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? paginatedVideos.value.map((video) => video.id)

    if (checked) {
        selectedVideos.value = [...new Set([...selectedVideos.value, ...ids])]
    } else {
        selectedVideos.value = selectedVideos.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать или снять одно видео */
const toggleSelectVideo = (videoId) => {
    const index = selectedVideos.value.indexOf(videoId)

    if (index > -1) {
        selectedVideos.value.splice(index, 1)
    } else {
        selectedVideos.value.push(videoId)
    }
}

/** Массовое включение/выключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите видео для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    router.put(
        route('admin.actions.blogVideos.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localVideos.value = localVideos.value.map((video) => {
                    if (idsToUpdate.includes(video.id)) {
                        return { ...video, activity: newActivity }
                    }

                    return video
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

/** Массовое изменение boolean-поля */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите видео для массового действия')
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    router.put(
        route(routeName),
        {
            ids: idsToUpdate,
            [field]: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localVideos.value = localVideos.value.map((video) => {
                    if (idsToUpdate.includes(video.id)) {
                        return { ...video, [field]: newValue }
                    }

                    return video
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

/** Массовое удаление */
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
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении видео.')
        },
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

/** Одобрение / отклонение видео */
const approveVideo = (video, status = 1, note = '') => {
    if (!video?.id) return

    router.put(
        route('admin.actions.blogVideos.approve', { blogVideo: video.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
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

/** Обновление сортировки */
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.blogVideos.create')">
                        {{ t('addVideo') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="videosCount"
                    v-model="searchQuery"
                />

                <div
                    v-if="videosCount"
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

                <div
                    v-if="videosCount"
                    class="flex items-center justify-between mb-3"
                >
                    <CountTable>{{ videosCount }}</CountTable>

                    <BulkActionSelect
                        v-if="videosCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <VideoTable
                    v-if="viewMode === 'table'"
                    :videos="paginatedVideos"
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
                    :videos="paginatedVideos"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredVideos.length"
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
            :onConfirm="deleteVideo"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
