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
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'

import BulkActionSelect from '@/Components/Admin/Blog/Banner/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Blog/Banner/Sort/SortSelect.vue'
import BannerTable from '@/Components/Admin/Blog/Banner/Table/BannerTable.vue'
import BannerCardGrid from '@/Components/Admin/Blog/Banner/View/BannerCardGrid.vue'

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/**
 * Props от контроллера
 */
const props = defineProps({
    banners: { type: Array, default: () => [] },
    bannersCount: { type: Number, default: 0 },

    adminCountBanners: { type: Number, default: 15 },
    adminSortBanners: { type: String, default: 'idDesc' },

    currentLocale: { type: String, default: 'ru' },
    availableLocales: { type: Array, default: () => ['ru', 'en', 'kk'] },

    search: { type: String, default: '' },
    sortParam: { type: String, default: '' },
    errors: { type: Object, default: () => ({}) },
})

/**
 * Проверка роли администратора
 */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/**
 * Получение текущего перевода баннера
 */
const getBannerTranslation = (banner) => banner?.translation || {}

/**
 * Получение названия баннера
 */
const getBannerTitle = (banner) => {
    return getBannerTranslation(banner)?.title || `ID: ${banner?.id}`
}

/**
 * Короткое описание
 */
const getBannerShort = (banner) => {
    return getBannerTranslation(banner)?.short || ''
}

/**
 * Ссылка баннера
 */
const getBannerLink = (banner) => {
    return getBannerTranslation(banner)?.link || ''
}

/**
 * Локаль перевода
 */
const getBannerLocale = (banner) => {
    return getBannerTranslation(banner)?.locale || props.currentLocale || ''
}

/**
 * Нормализация строки для поиска/сортировки
 */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/**
 * Приведение статуса модерации к числу
 */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/**
 * Режим отображения (таблица/карточки)
 */
const viewMode = ref(localStorage.getItem('admin_view_mode_banners') || 'cards')

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_banners', value)
})

/**
 * Количество элементов на странице
 */
const itemsPerPage = ref(props.adminCountBanners || 15)

/**
 * Обновление количества элементов (сохранение в настройках)
 */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountBanners'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/**
 * Текущий параметр сортировки
 */
const sortParam = ref(props.sortParam || props.adminSortBanners || 'idDesc')

/**
 * Обновление сортировки (сохранение в настройках)
 */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortBanners'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
        }
    )
})

/**
 * Локальный список баннеров (для реактивных изменений)
 */
const localBanners = ref([])

watch(
    () => props.banners,
    (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/**
 * ===== УДАЛЕНИЕ =====
 */
const showConfirmDeleteModal = ref(false)
const bannerToDeleteId = ref(null)
const bannerToDeleteTitle = ref('')

/**
 * Открыть модалку удаления
 */
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

/**
 * Закрыть модалку
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    bannerToDeleteId.value = null
    bannerToDeleteTitle.value = ''
}

/**
 * Удаление баннера
 */
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
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Баннер: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/**
 * Локальное обновление баннера
 */
const patchLocalBanner = (bannerId, callback) => {
    const index = localBanners.value.findIndex((banner) => banner.id === bannerId)

    if (index !== -1) {
        callback(localBanners.value[index])
    }
}

/**
 * ===== TOGGLES =====
 */

/**
 * Переключение активности
 */
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

/**
 * Переключение left
 */
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

/**
 * Переключение main
 */
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

/**
 * Переключение right
 */
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

/**
 * ===== ПОИСК И СОРТИРОВКА =====
 */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/**
 * Сортировка баннеров по выбранному параметру
 */
const sortBanners = (banners) => {
    const list = (banners || []).slice()

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

    if (sortParam.value === 'titleAsc') {
        return list.sort((a, b) => normalize(getBannerTitle(a)).localeCompare(normalize(getBannerTitle(b)), locale.value))
    }

    if (sortParam.value === 'titleDesc') {
        return list.sort((a, b) => normalize(getBannerTitle(b)).localeCompare(normalize(getBannerTitle(a)), locale.value))
    }

    if (sortParam.value === 'locale') {
        return list.sort((a, b) => getBannerLocale(a).localeCompare(getBannerLocale(b), locale.value))
    }

    if (sortParam.value === 'activity') {
        return list.filter((banner) => !!banner.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter((banner) => !banner.activity)
    }

    if (sortParam.value === 'left') {
        return list.filter((banner) => !!banner.left)
    }

    if (sortParam.value === 'noLeft') {
        return list.filter((banner) => !banner.left)
    }

    if (sortParam.value === 'main') {
        return list.filter((banner) => !!banner.main)
    }

    if (sortParam.value === 'noMain') {
        return list.filter((banner) => !banner.main)
    }

    if (sortParam.value === 'right') {
        return list.filter((banner) => !!banner.right)
    }

    if (sortParam.value === 'noRight') {
        return list.filter((banner) => !banner.right)
    }

    if (sortParam.value === 'imagesDesc') {
        return list.sort((a, b) => (b.images_count ?? 0) - (a.images_count ?? 0))
    }

    if (sortParam.value === 'imagesAsc') {
        return list.sort((a, b) => (a.images_count ?? 0) - (b.images_count ?? 0))
    }

    if (sortParam.value === 'moderation_pending') {
        return list.filter((banner) => moderationNum(banner?.moderation_status) === 0)
    }

    if (sortParam.value === 'moderation_approved') {
        return list.filter((banner) => moderationNum(banner?.moderation_status) === 1)
    }

    if (sortParam.value === 'moderation_rejected') {
        return list.filter((banner) => moderationNum(banner?.moderation_status) === 2)
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
 * Фильтрация + сортировка
 */
const filteredBanners = computed(() => {
    let filtered = localBanners.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortBanners(filtered)
    }

    filtered = filtered.filter((banner) => {
        const title = normalize(getBannerTitle(banner))
        const short = normalize(getBannerShort(banner))
        const link = normalize(getBannerLink(banner))
        const comment = normalize(banner?.comment)
        const ownerName = normalize(banner?.owner?.name)
        const ownerEmail = normalize(banner?.owner?.email)

        return title.includes(query)
            || short.includes(query)
            || link.includes(query)
            || comment.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortBanners(filtered)
})

/**
 * Пагинация
 */
const paginatedBanners = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredBanners.value.slice(start, start + perPage)
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/**
 * ===== BULK =====
 */
const selectedBanners = ref([])

/**
 * Выбрать / снять выбор всех
 */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? paginatedBanners.value.map((banner) => banner.id)

    if (checked) {
        selectedBanners.value = [...new Set([...selectedBanners.value, ...ids])]
    } else {
        selectedBanners.value = selectedBanners.value.filter((id) => !ids.includes(id))
    }
}

/**
 * Выбор одного баннера
 */
const toggleSelectBanner = (bannerId) => {
    const index = selectedBanners.value.indexOf(bannerId)

    if (index > -1) {
        selectedBanners.value.splice(index, 1)
    } else {
        selectedBanners.value.push(bannerId)
    }
}

/**
 * Массовое переключение активности
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedBanners.value.length) {
        toast.warning('Выберите баннеры для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedBanners.value]

    router.put(
        route('admin.actions.blogBanners.bulkUpdateActivity'),
        {
            ids: idsToUpdate,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localBanners.value = localBanners.value.map((banner) => {
                    if (idsToUpdate.includes(banner.id)) {
                        return { ...banner, activity: newActivity }
                    }

                    return banner
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

/**
 * Выбор инпутов для маасовых действий
 */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedBanners.value.length) {
        toast.warning('Выберите баннеры для массового действия')
        return
    }

    const idsToUpdate = [...selectedBanners.value]

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
                localBanners.value = localBanners.value.map((banner) => {
                    if (idsToUpdate.includes(banner.id)) {
                        return { ...banner, [field]: newValue }
                    }

                    return banner
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

/**
 * Массовое удаление
 */
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
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении баннеров.')
        },
    })
}

/**
 * Обработчик bulk действий
 */
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

/**
 * Модерация баннера
 */
const approveBanner = (banner, status = 1, note = '') => {
    if (!banner?.id) return

    router.put(
        route('admin.actions.blogBanners.approve', { blogBanner: banner.id }),
        {
            moderation_status: status,
            moderation_note: note,
        },
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

/**
 * Сохранение сортировки drag&drop
 */
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
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.blogBanners.create')">
                        {{ t('addBanner') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="bannersCount"
                    v-model="searchQuery"
                />

                <div
                    v-if="bannersCount"
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
                    v-if="bannersCount"
                    class="flex items-center justify-between mb-3"
                >
                    <CountTable>{{ bannersCount }}</CountTable>

                    <BulkActionSelect
                        v-if="bannersCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <BannerTable
                    v-if="viewMode === 'table'"
                    :banners="paginatedBanners"
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
                    :banners="paginatedBanners"
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

                <div
                    v-if="bannersCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBanners.length"
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
            :onConfirm="deleteBanner"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
