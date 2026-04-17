<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр
 * Видео — список + локали + модерация (как Banner Index)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import SortSelect from '@/Components/Admin/Video/Sort/SortSelect.vue'
import VideoTable from '@/Components/Admin/Video/Table/VideoTable.vue'
import VideoCardGrid from '@/Components/Admin/Video/View/VideoCardGrid.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import BulkActionSelect from '@/Components/Admin/Video/Select/BulkActionSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Роль пользователя */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some(r => r?.name === 'admin')
})

/** Props */
const props = defineProps({
    videos: Array,
    videosCount: Number,

    adminCountVideos: Number,
    adminSortVideos: String,

    currentLocale: String,
    availableLocales: Array,

    errors: Object,
})

/** Вид: table/cards */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Локальная копия списка видео (для оптимистичных обновлений) */
const localVideos = ref([])
watch(
    () => props.videos,
    (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Пагинация */
const itemsPerPage = ref(props.adminCountVideos)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountVideos'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметры сортировки */
const sortParam = ref(props.adminSortVideos)
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortVideos'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const videoToDeleteId = ref(null)
const videoToDeleteTitle = ref('')

const confirmDelete = (id, title) => {
    videoToDeleteId.value = id
    videoToDeleteTitle.value = title
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

    router.delete(route('admin.videos.destroy', { video: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Видео "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Видео: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            videoToDeleteId.value = null
            videoToDeleteTitle.value = ''
        }
    })
}

/** Оптимистичный patch локального массива */
const patchLocal = (id, patch) => {
    const idx = localVideos.value.findIndex(v => v.id === id)
    if (idx !== -1) localVideos.value[idx] = { ...localVideos.value[idx], ...patch }
}

/** toggle left */
const toggleLeft = (video) => {
    const newLeft = !video.left
    const actionText = newLeft ? 'активировано в левой колонке' : 'деактивировано в левой колонке'

    router.put(route('admin.actions.videos.updateLeft', { video: video.id }), { left: newLeft }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(video.id, { left: newLeft })
            toast.success(`Видео "${video.title}" ${actionText}.`)
        },
        onError: (errors) => toast.error(errors.left || errors.general || `Ошибка изменения статуса для "${video.title}".`),
    })
}

/** toggle main */
const toggleMain = (video) => {
    const newMain = !video.main
    const actionText = newMain ? 'активировано в главном' : 'деактивировано в главном'

    router.put(route('admin.actions.videos.updateMain', { video: video.id }), { main: newMain }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(video.id, { main: newMain })
            toast.success(`Видео "${video.title}" ${actionText}.`)
        },
        onError: (errors) => toast.error(errors.main || errors.general || `Ошибка изменения статуса для "${video.title}".`),
    })
}

/** toggle right */
const toggleRight = (video) => {
    const newRight = !video.right
    const actionText = newRight ? 'активировано в правой колонке' : 'деактивировано в правой колонке'

    router.put(route('admin.actions.videos.updateRight', { video: video.id }), { right: newRight }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(video.id, { right: newRight })
            toast.success(`Видео "${video.title}" ${actionText}.`)
        },
        onError: (errors) => toast.error(errors.right || errors.general || `Ошибка изменения статуса для "${video.title}".`),
    })
}

/** toggle activity */
const toggleActivity = (video) => {
    const newActivity = !video.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.videos.updateActivity', { video: video.id }), { activity: newActivity }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocal(video.id, { activity: newActivity })
            toast.success(`Видео "${video.title}" ${actionText}.`)
        },
        onError: (errors) => toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${video.title}".`),
    })
}

/** Модерация видео (approve/reject) */
const approveVideo = (video, status = 1, note = '') => {
    if (!video?.id) return

    router.put(
        route('admin.actions.videos.approve', { video: video.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocal(video.id, {
                    moderation_status: status,
                    is_approved: status === 1,
                    moderation_note: note,
                })
                toast.success(status === 1 ? 'Видео одобрено' : 'Видео отклонено')
            },
            onError: () => toast.error('Ошибка модерации видео'),
        }
    )
}

/** Поиск/пагинация */
const currentPage = ref(1)
const searchQuery = ref('')

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const normalize = (v) => (v ?? '').toString().trim().toLowerCase()
const moderationNum = (v) => {
    const n = Number(v)
    return Number.isFinite(n) ? n : 0
}

/** Сортировка + фильтры (включая модерацию + owner + позиции) */
const sortVideos = (videos) => {
    const list = (videos || []).slice()

    // --- owner сортировки ---
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

    // id
    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

    // flags/filters
    if (sortParam.value === 'activity') return list.filter(v => !!v.activity)
    if (sortParam.value === 'inactive') return list.filter(v => !v.activity)

    // private/public filters
    if (sortParam.value === 'private') return list.filter(v => !!v.is_private)
    if (sortParam.value === 'public')  return list.filter(v => !v.is_private)

    if (sortParam.value === 'left') return list.filter(v => !!v.left)
    if (sortParam.value === 'noLeft') return list.filter(v => !v.left)

    if (sortParam.value === 'main') return list.filter(v => !!v.main)
    if (sortParam.value === 'noMain') return list.filter(v => !v.main)

    if (sortParam.value === 'right') return list.filter(v => !!v.right)
    if (sortParam.value === 'noRight') return list.filter(v => !v.right)

    // locale
    if (sortParam.value === 'locale') {
        return list.sort((a, b) => {
            if ((a.locale || '') < (b.locale || '')) return 1
            if ((a.locale || '') > (b.locale || '')) return -1
            return 0
        })
    }

    // --- moderation filters ---
    if (sortParam.value === 'moderation_pending') return list.filter(v => moderationNum(v?.moderation_status) === 0)
    if (sortParam.value === 'moderation_approved') return list.filter(v => moderationNum(v?.moderation_status) === 1)
    if (sortParam.value === 'moderation_rejected') return list.filter(v => moderationNum(v?.moderation_status) === 2)

    // --- moderation sorting ---
    if (sortParam.value === 'moderation_statusAsc') {
        return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    }
    if (sortParam.value === 'moderation_statusDesc') {
        return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))
    }

    // просмотры/лайки (если есть в ресурсах)
    if (sortParam.value === 'views' || sortParam.value === 'likes') {
        return list.sort((a, b) => (b?.[sortParam.value] ?? 0) - (a?.[sortParam.value] ?? 0))
    }

    // fallback
    return list.sort((a, b) => {
        const key = sortParam.value
        const av = a?.[key]
        const bv = b?.[key]
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтр поиска: title + owner.name + owner.email + description + link */
const filteredVideos = computed(() => {
    let filtered = localVideos.value || []
    const q = normalize(searchQuery.value)

    if (q) {
        filtered = filtered.filter(v => {
            const title = normalize(v?.title)
            const desc = normalize(v?.description)
            const link = normalize(v?.link)

            const ownerName = normalize(v?.owner?.name)
            const ownerEmail = normalize(v?.owner?.email)

            return (
                title.includes(q) ||
                desc.includes(q) ||
                link.includes(q) ||
                ownerName.includes(q) ||
                ownerEmail.includes(q)
            )
        })
    }

    return sortVideos(filtered)
})

/** Пагинация */
const paginatedVideos = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per
    return filteredVideos.value.slice(start, start + per)
})

/** Drag & Drop sort (bulk) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * Number(itemsPerPage.value || 10)

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.videos.updateSortBulk'),
        { items: sortData, locale: props.currentLocale },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок видео успешно обновлен.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки:', errors)
                toast.error(errors.message || errors.general || 'Не удалось обновить порядок видео.')
                router.reload({ only: ['videos'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedVideos = ref([])

/** Выбрать все */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedVideos.value = [...new Set([...selectedVideos.value, ...ids])]
    } else {
        selectedVideos.value = selectedVideos.value.filter(id => !ids.includes(id))
    }
}

/** Выбор одного */
const toggleSelectVideo = (videoId) => {
    const index = selectedVideos.value.indexOf(videoId)
    if (index > -1) selectedVideos.value.splice(index, 1)
    else selectedVideos.value.push(videoId)
}

/** Массовое переключение активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите видео для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    router.put(
        route('admin.actions.videos.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localVideos.value = localVideos.value.map(v => {
                    if (idsToUpdate.includes(v.id)) return { ...v, activity: newActivity }
                    return v
                })
                selectedVideos.value = []
                toast.success('Активность видео массово обновлена')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность видео'
                toast.error(msg)
            },
        }
    )
}

/** Массовое переключение в левую колонку */
const bulkToggleLeft = (newLeft) => {
    if (!selectedVideos.value.length) {
        toast.warning(`Выберите видео для ${newLeft ? 'активации в левой колонке' : 'деактивации в левой колонке'}.`)
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    axios.put(route('admin.actions.videos.bulkUpdateLeft'), {
        ids: idsToUpdate,
        left: newLeft,
    }).then(() => {
        localVideos.value = localVideos.value.map(v => {
            if (idsToUpdate.includes(v.id)) return { ...v, left: newLeft }
            return v
        })
        selectedVideos.value = []
        toast.success('Статус в левой колонке массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в левой колонке')
    })
}

/** Массовое переключение по центру */
const bulkToggleMain = (newMain) => {
    if (!selectedVideos.value.length) {
        toast.warning(`Выберите видео для ${newMain ? 'активации в главном' : 'деактивации в главном'}.`)
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    axios.put(route('admin.actions.videos.bulkUpdateMain'), {
        ids: idsToUpdate,
        main: newMain,
    }).then(() => {
        localVideos.value = localVideos.value.map(v => {
            if (idsToUpdate.includes(v.id)) return { ...v, main: newMain }
            return v
        })
        selectedVideos.value = []
        toast.success('Статус в главном массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в главном')
    })
}

/** Массовое переключение в правую колонку */
const bulkToggleRight = (newRight) => {
    if (!selectedVideos.value.length) {
        toast.warning(`Выберите видео для ${newRight ? 'активации в правой колонке' : 'деактивации в правой колонке'}.`)
        return
    }

    const idsToUpdate = [...selectedVideos.value]

    axios.put(route('admin.actions.videos.bulkUpdateRight'), {
        ids: idsToUpdate,
        right: newRight,
    }).then(() => {
        localVideos.value = localVideos.value.map(v => {
            if (idsToUpdate.includes(v.id)) return { ...v, right: newRight }
            return v
        })
        selectedVideos.value = []
        toast.success('Статус в правой колонке массово обновлен')
    }).catch(() => {
        toast.error('Не удалось обновить статус в правой колонке')
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedVideos.value.length) {
        toast.warning('Выберите хотя бы одно видео для удаления.')
        return
    }
    if (!confirm('Вы уверены, что хотите удалить выбранные видео?')) return

    router.delete(route('admin.actions.videos.bulkDestroy'), {
        data: { ids: selectedVideos.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedVideos.value = []
            toast.success('Массовое удаление видео успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении видео.')
        },
    })
}

/** Массовая модерация (для admin) */
const bulkApprove = (status = 1) => {
    if (!isAdmin.value) return
    if (!selectedVideos.value.length) {
        toast.warning('Выберите видео для модерации')
        return
    }

    // Без отдельного endpoint bulkApprove — не выполняем запросом.
    toast.info('Массовая модерация: добавь endpoint bulkApprove на бэкенде (если нужно).')
}

/** обработчики массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedVideos.value = paginatedVideos.value.map(v => v.id)
    } else if (action === 'deselectAll') {
        selectedVideos.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'left') {
        bulkToggleLeft(true)
    } else if (action === 'noLeft') {
        bulkToggleLeft(false)
    } else if (action === 'main') {
        bulkToggleMain(true)
    } else if (action === 'noMain') {
        bulkToggleMain(false)
    } else if (action === 'right') {
        bulkToggleRight(true)
    } else if (action === 'noRight') {
        bulkToggleRight(false)
    } else if (action === 'delete') {
        bulkDelete()
    } else if (action === 'moderation_approve') {
        bulkApprove(1)
    } else if (action === 'moderation_reject') {
        bulkApprove(2)
    }

    event.target.value = ''
}

/** Таб локалей */
const localeLink = (loc) => route('admin.videos.index', { locale: loc })

</script>

<template>
    <AdminLayout :title="t('videos')">
        <template #header>
            <TitlePage>
                {{ t('videos') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700
                        border border-blue-400 dark:border-blue-200
                        overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                        bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.videos.create', { locale: currentLocale })">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addVideo') }}
                    </DefaultButton>

                    <BulkActionSelect v-if="videosCount" @change="handleBulkAction" />
                </div>

                <!-- Локали + счётчик + переключатель вида -->
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
                                        : 'bg-slate-100 dark:bg-slate-900 ' +
                                          'text-slate-700 dark:text-slate-200 ' +
                                          'hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ loc.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3" v-if="videosCount">
                        <CountTable>{{ videosCount }}</CountTable>
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput v-if="videosCount" v-model="searchQuery"
                             :placeholder="t('searchByName')" />

                <!-- Таблица / Карточки -->
                <VideoTable
                    v-if="viewMode === 'table'"
                    :videos="paginatedVideos"
                    :selected-videos="selectedVideos"
                    :isAdmin="isAdmin"
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
                    :isAdmin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @toggle-select="toggleSelectVideo"
                    @toggle-all="toggleAll"
                    @update-sort-order="handleSortOrderUpdate"
                    @approve="approveVideo"
                />

                <div class="flex justify-between items-center flex-col md:flex-row my-1"
                     v-if="videosCount">
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event" />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredVideos.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val" />
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
