<script setup>
/**
 * Баннеры блога — Index
 * Новая мультиязычная архитектура:
 * - blog_banners
 * - blog_banner_translations
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

import BulkActionSelect from '@/Components/Admin/Blog/BlogBanner/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/BlogBanner/Sort/SortSelect.vue'
import BannerTable from '@/Components/Admin/Blog/BlogBanner/Table/BannerTable.vue'
import BannerCardGrid from '@/Components/Admin/Blog/BlogBanner/View/BannerCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

const props = defineProps({
    banners: { type: [Array, Object], default: () => [] },
    bannersCount: { type: Number, default: 0 },

    adminBlogBannersProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminBlogBannersPerPage: { type: Number, default: 20 },
    adminBlogBannersDefaultSort: { type: String, default: 'idDesc' },

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

const getBannerTranslation = (banner) => banner?.translation || {}
const getBannerTitle = (banner) => getBannerTranslation(banner)?.title || `ID: ${banner?.id}`
const getBannerShort = (banner) => getBannerTranslation(banner)?.short || ''
const getBannerLink = (banner) => getBannerTranslation(banner)?.link || ''

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const viewMode = ref(localStorage.getItem('admin_view_mode_banners') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_banners', value)
})

const itemsPerPage = ref(props.adminBlogBannersPerPage || 20)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountBlogBanners'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminBlogBannersDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortBlogBanners'),
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

const bannersList = computed(() => {
    if (Array.isArray(props.banners)) return props.banners
    if (Array.isArray(props.banners?.data)) return props.banners.data
    return []
})

const localBanners = ref([])

watch(
    bannersList,
    (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const showConfirmDeleteModal = ref(false)
const bannerToDeleteId = ref(null)
const bannerToDeleteTitle = ref('')

const confirmDelete = (bannerOrId, title = null) => {
    if (typeof bannerOrId === 'object') {
        bannerToDeleteId.value = bannerOrId.id
        bannerToDeleteTitle.value = title || getBannerTitle(bannerOrId)
    } else {
        bannerToDeleteId.value = bannerOrId
        bannerToDeleteTitle.value = title || `ID: ${bannerOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    bannerToDeleteId.value = null
    bannerToDeleteTitle.value = ''
}

const deleteBanner = () => {
    if (bannerToDeleteId.value === null) return

    const idToDelete = bannerToDeleteId.value
    const titleToDelete = bannerToDeleteTitle.value

    router.delete(route('admin.blogBanners.destroy', { blogBanner: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Баннер "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Баннер: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const patchLocalBanner = (bannerId, callback) => {
    const index = localBanners.value.findIndex((banner) => banner.id === bannerId)

    if (index !== -1) {
        callback(localBanners.value[index])
    }
}

const toggleActivity = (banner) => {
    const newActivity = !banner.activity
    const title = getBannerTitle(banner)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.blogBanners.updateActivity', { blogBanner: banner.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalBanner(banner.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Баннер "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

const toggleLeft = (banner) => {
    const newLeft = !banner.left
    const title = getBannerTitle(banner)

    router.put(
        route('admin.actions.blogBanners.updateLeft', { blogBanner: banner.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalBanner(banner.id, (node) => {
                    node.left = newLeft
                })

                toast.success(`Позиция left для баннера "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`)
            },
        }
    )
}

const toggleMain = (banner) => {
    const newMain = !banner.main
    const title = getBannerTitle(banner)

    router.put(
        route('admin.actions.blogBanners.updateMain', { blogBanner: banner.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalBanner(banner.id, (node) => {
                    node.main = newMain
                })

                toast.success(`Позиция main для баннера "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`)
            },
        }
    )
}

const toggleRight = (banner) => {
    const newRight = !banner.right
    const title = getBannerTitle(banner)

    router.put(
        route('admin.actions.blogBanners.updateRight', { blogBanner: banner.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalBanner(banner.id, (node) => {
                    node.right = newRight
                })

                toast.success(`Позиция right для баннера "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`)
            },
        }
    )
}

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

const sortBanners = (banners) => {
    const list = (banners || []).slice()

    if (sortParam.value === 'activity') return list.filter((banner) => !!banner.activity)
    if (sortParam.value === 'inactive') return list.filter((banner) => !banner.activity)

    if (sortParam.value === 'left') return list.filter((banner) => !!banner.left)
    if (sortParam.value === 'noLeft') return list.filter((banner) => !banner.left)

    if (sortParam.value === 'main') return list.filter((banner) => !!banner.main)
    if (sortParam.value === 'noMain') return list.filter((banner) => !banner.main)

    if (sortParam.value === 'right') return list.filter((banner) => !!banner.right)
    if (sortParam.value === 'noRight') return list.filter((banner) => !banner.right)

    if (sortParam.value === 'moderationPending') return list.filter((banner) => moderationNum(banner?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((banner) => moderationNum(banner?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((banner) => moderationNum(banner?.moderation_status) === 2)

    const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),

        sortAsc: (a, b) => (a.sort ?? 0) - (b.sort ?? 0),
        sortDesc: (a, b) => (b.sort ?? 0) - (a.sort ?? 0),

        ownerNameAsc: (a, b) => normalize(a?.owner?.name).localeCompare(normalize(b?.owner?.name), locale.value),
        ownerNameDesc: (a, b) => normalize(b?.owner?.name).localeCompare(normalize(a?.owner?.name), locale.value),
        ownerEmailAsc: (a, b) => normalize(a?.owner?.email).localeCompare(normalize(b?.owner?.email), locale.value),
        ownerEmailDesc: (a, b) => normalize(b?.owner?.email).localeCompare(normalize(a?.owner?.email), locale.value),

        titleAsc: (a, b) => normalize(getBannerTitle(a)).localeCompare(normalize(getBannerTitle(b)), locale.value),
        titleDesc: (a, b) => normalize(getBannerTitle(b)).localeCompare(normalize(getBannerTitle(a)), locale.value),

        imagesAsc: (a, b) => (a.images_count ?? 0) - (b.images_count ?? 0),
        imagesDesc: (a, b) => (b.images_count ?? 0) - (a.images_count ?? 0),

        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),

        leftAsc: (a, b) => Number(a.left) - Number(b.left),
        leftDesc: (a, b) => Number(b.left) - Number(a.left),

        mainAsc: (a, b) => Number(a.main) - Number(b.main),
        mainDesc: (a, b) => Number(b.main) - Number(a.main),

        rightAsc: (a, b) => Number(a.right) - Number(b.right),
        rightDesc: (a, b) => Number(b.right) - Number(a.right),

        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),

        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),

        moderationStatusAsc: (a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredBanners = computed(() => {
    let filtered = localBanners.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortBanners(filtered)
    }

    filtered = filtered.filter((banner) => {
        const values = [
            banner?.id,
            banner?.comment,
            banner?.moderation_note,
            getBannerTitle(banner),
            getBannerShort(banner),
            getBannerLink(banner),
            banner?.owner?.name,
            banner?.owner?.email,
            banner?.moderator?.name,
            banner?.moderator?.email,
        ]

        return values.some((value) => normalize(value).includes(query))
    })

    return sortBanners(filtered)
})

const paginatedBanners = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredBanners.value.slice(start, start + perPage)
})

const displayedBanners = computed(() => {
    return props.useServerProcessing
        ? bannersList.value
        : paginatedBanners.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const selectedBanners = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedBanners.value.map((banner) => banner.id)

    if (checked) {
        selectedBanners.value = [...new Set([...selectedBanners.value, ...ids])]
    } else {
        selectedBanners.value = selectedBanners.value.filter((id) => !ids.includes(id))
    }
}

const toggleSelectBanner = (bannerId) => {
    const index = selectedBanners.value.indexOf(bannerId)

    if (index > -1) {
        selectedBanners.value.splice(index, 1)
    } else {
        selectedBanners.value.push(bannerId)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedBanners.value.length) {
        toast.warning('Выберите баннеры для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    router.put(
        route('admin.actions.blogBanners.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localBanners.value = localBanners.value.map((banner) => {
                    return idsToUpdate.includes(banner.id)
                        ? { ...banner, activity: newActivity }
                        : banner
                })

                selectedBanners.value = []
                toast.success('Активность баннеров массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            },
        }
    )
}

const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedBanners.value.length) {
        toast.warning('Выберите баннеры для массового действия')
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localBanners.value = localBanners.value.map((banner) => {
                    return idsToUpdate.includes(banner.id)
                        ? { ...banner, [field]: newValue }
                        : banner
                })

                selectedBanners.value = []
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
    if (!selectedBanners.value.length) {
        toast.warning('Выберите хотя бы один баннер для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные баннеры?')) return

    router.delete(route('admin.actions.blogBanners.bulkDestroy'), {
        data: { ids: selectedBanners.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedBanners.value = []
            toast.success('Массовое удаление баннеров успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении баннеров.')
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
        bulkToggleFlag('left', true, 'admin.actions.blogBanners.bulkUpdateLeft', 'Баннеры добавлены в левую колонку')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.blogBanners.bulkUpdateLeft', 'Баннеры убраны из левой колонки')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.blogBanners.bulkUpdateMain', 'Баннеры добавлены в главный блок')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.blogBanners.bulkUpdateMain', 'Баннеры убраны из главного блока')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.blogBanners.bulkUpdateRight', 'Баннеры добавлены в правую колонку')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.blogBanners.bulkUpdateRight', 'Баннеры убраны из правой колонки')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

const approveBanner = (banner, status = 1, note = '') => {
    if (!banner?.id) return

    router.put(
        route('admin.actions.blogBanners.approve', { blogBanner: banner.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalBanner(banner.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Баннер одобрен' : 'Баннер отклонён')
            },
            onError: () => toast.error('Ошибка модерации баннера'),
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
        route('admin.actions.blogBanners.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка баннеров обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки баннеров:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('banners')">
        <template #header>
            <TitlePage>{{ t('banners') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.blogBanners.create')">
                        {{ t('addBanner') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminBlogBannersProcessingMode"
                        :mode="adminBlogBannersProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="bannersCount"
                    />
                </div>

                <SearchInput v-if="bannersCount && !useServerProcessing" v-model="searchQuery" />
                <ServerSearchInput v-if="bannersCount && useServerProcessing" v-model="searchQuery" />

                <div v-if="bannersCount" class="flex justify-between items-center flex-col md:flex-row my-3">
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountBlogBanners"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div v-if="bannersCount" class="flex flex-col lg:flex-row items-center justify-between gap-3">
                    <CountTable>{{ bannersCount }}</CountTable>

                    <BulkActionSelect v-if="bannersCount" @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div v-if="bannersCount" class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBanners.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="banners"
                    />
                </div>

                <BannerTable
                    v-if="viewMode === 'table'"
                    :banners="displayedBanners"
                    :selected-banners="selectedBanners"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBanner"
                    @toggle-all="toggleAll"
                    @approve="approveBanner"
                />

                <BannerCardGrid
                    v-else
                    :banners="displayedBanners"
                    :selected-banners="selectedBanners"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBanner"
                    @toggle-all="toggleAll"
                    @approve="approveBanner"
                />

                <div v-if="bannersCount" class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBanners.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="banners"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteBanner"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
