<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список магазинов MarketShop
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
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'

import BulkActionSelect from '@/Components/Admin/Market/MarketShop/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketShop/Sort/SortSelect.vue'
import ShopTable from '@/Components/Admin/Market/MarketShop/Table/ShopTable.vue'
import ShopCardGrid from '@/Components/Admin/Market/MarketShop/View/ShopCardGrid.vue'

/** Локализация и сервисы */
const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/** Входные параметры страницы */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminMarketShopsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    adminMarketShopsPerPage: { type: Number, default: 6 },
    adminMarketShopsDefaultSort: { type: String, default: 'idDesc' },

    shops: { type: [Array, Object], default: () => [] },
    shopsCount: { type: Number, default: 0 },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/** Проверка роли администратора */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []
    return roles.some((role) => role?.name === 'admin')
})

/** Получение перевода магазина */
const getShopTranslation = (shop) => shop?.translation || {}

/** Получение названия магазина */
const getShopTitle = (shop) => getShopTranslation(shop)?.title || `ID: ${shop?.id}`

/** Получение краткого описания магазина */
const getShopShort = (shop) => getShopTranslation(shop)?.short || ''

/** Получение полного описания магазина */
const getShopDescription = (shop) => getShopTranslation(shop)?.description || ''

/** Получение названия компании */
const getCompanyTitle = (shop) => {
    return shop?.company?.translation?.title
        || shop?.company?.legal_name
        || `Company ID: ${shop?.market_company_id}`
}

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Нормализация статуса модерации */
const moderationNum = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Режим отображения списка */
const viewMode = ref(localStorage.getItem('admin_view_mode_market_shops') || 'cards')

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_market_shops', value)
})

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminMarketShopsPerPage || 6)

/** Обновление количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountShops'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} магазинов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва магазинов.'),
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(props.sortParam || props.adminMarketShopsDefaultSort || 'idDesc')

/** Обновление сортировки */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortShops'),
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

                toast.info('Сортировка магазинов успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки магазинов.')
            },
        }
    )
})

/** Локальный список магазинов */
const localShops = ref([])

/** Нормализация списка магазинов */
const shopsList = computed(() => {
    if (Array.isArray(props.shops)) {
        return props.shops
    }

    if (Array.isArray(props.shops?.data)) {
        return props.shops.data
    }

    if (Array.isArray(props.shops?.data?.data)) {
        return props.shops.data.data
    }

    if (Array.isArray(props.shops?.resource)) {
        return props.shops.resource
    }

    return []
})

/** Синхронизация локального списка */
watch(
    shopsList,
    (newVal) => {
        localShops.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Модальное окно удаления */
const showConfirmDeleteModal = ref(false)
const shopToDeleteId = ref(null)
const shopToDeleteTitle = ref('')

/** Подготовка удаления магазина */
const confirmDelete = (shopOrId, title = null) => {
    if (typeof shopOrId === 'object') {
        shopToDeleteId.value = shopOrId.id
        shopToDeleteTitle.value = title || getShopTitle(shopOrId)
    } else {
        shopToDeleteId.value = shopOrId
        shopToDeleteTitle.value = title || `ID: ${shopOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие модального окна */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    shopToDeleteId.value = null
    shopToDeleteTitle.value = ''
}

/** Удаление магазина */
const deleteShop = () => {
    if (shopToDeleteId.value === null) return

    const idToDelete = shopToDeleteId.value
    const titleToDelete = shopToDeleteTitle.value

    router.delete(route('admin.marketShops.destroy', { marketShop: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Магазин "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Магазин: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/** Локальное обновление магазина */
const patchLocalShop = (shopId, callback) => {
    const index = localShops.value.findIndex((shop) => shop.id === shopId)

    if (index !== -1) {
        callback(localShops.value[index])
    }
}

/** Переключение активности */
const toggleActivity = (shop) => {
    const newActivity = !shop.activity
    const title = getShopTitle(shop)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketShops.updateActivity', { marketShop: shop.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalShop(shop.id, (node) => {
                    node.activity = newActivity
                })

                toast.success(`Магазин "${title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`)
            },
        }
    )
}

/** Переключение позиции left */
const toggleLeft = (shop) => {
    const newLeft = !shop.left
    const title = getShopTitle(shop)

    router.put(
        route('admin.actions.marketShops.updateLeft', { marketShop: shop.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalShop(shop.id, (node) => {
                    node.left = newLeft
                })

                toast.success(`Позиция left для магазина "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`)
            },
        }
    )
}

/** Переключение позиции main */
const toggleMain = (shop) => {
    const newMain = !shop.main
    const title = getShopTitle(shop)

    router.put(
        route('admin.actions.marketShops.updateMain', { marketShop: shop.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalShop(shop.id, (node) => {
                    node.main = newMain
                })

                toast.success(`Позиция main для магазина "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`)
            },
        }
    )
}

/** Переключение позиции right */
const toggleRight = (shop) => {
    const newRight = !shop.right
    const title = getShopTitle(shop)

    router.put(
        route('admin.actions.marketShops.updateRight', { marketShop: shop.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalShop(shop.id, (node) => {
                    node.right = newRight
                })

                toast.success(`Позиция right для магазина "${title}" обновлена.`)
            },
            onError: (errors) => {
                toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`)
            },
        }
    )
}

/** Поиск и пагинация */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Сортировка магазинов */
const sortShops = (shops) => {
    const list = (shops || []).slice()

    if (sortParam.value === 'activityAsc') {
        return list.sort((a, b) => Number(a.activity) - Number(b.activity))
    }
    if (sortParam.value === 'activityDesc') {
        return list.sort((a, b) => Number(b.activity) - Number(a.activity))
    }

    if (sortParam.value === 'leftAsc') {
        return list.sort((a, b) => Number(a.left) - Number(b.left))
    }
    if (sortParam.value === 'leftDesc') {
        return list.sort((a, b) => Number(b.left) - Number(a.left))
    }

    if (sortParam.value === 'mainAsc') {
        return list.sort((a, b) => Number(a.main) - Number(b.main))
    }
    if (sortParam.value === 'mainDesc') {
        return list.sort((a, b) => Number(b.main) - Number(a.main))
    }

    if (sortParam.value === 'rightAsc') {
        return list.sort((a, b) => Number(a.right) - Number(b.right))
    }
    if (sortParam.value === 'rightDesc') {
        return list.sort((a, b) => Number(b.right) - Number(a.right))
    }

    if (sortParam.value === 'ownerNameAsc') return list.sort((a, b) => normalize(a?.owner?.name).localeCompare(normalize(b?.owner?.name), locale.value))
    if (sortParam.value === 'ownerNameDesc') return list.sort((a, b) => normalize(b?.owner?.name).localeCompare(normalize(a?.owner?.name), locale.value))
    if (sortParam.value === 'ownerEmailAsc') return list.sort((a, b) => normalize(a?.owner?.email).localeCompare(normalize(b?.owner?.email), locale.value))
    if (sortParam.value === 'ownerEmailDesc') return list.sort((a, b) => normalize(b?.owner?.email).localeCompare(normalize(a?.owner?.email), locale.value))

    if (sortParam.value === 'companyLegalNameAsc') return list.sort((a, b) => normalize(a?.company?.legal_name).localeCompare(normalize(b?.company?.legal_name), locale.value))
    if (sortParam.value === 'companyLegalNameDesc') return list.sort((a, b) => normalize(b?.company?.legal_name).localeCompare(normalize(a?.company?.legal_name), locale.value))

    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
    if (sortParam.value === 'sortAsc') return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    if (sortParam.value === 'sortDesc') return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))

    if (sortParam.value === 'titleAsc') return list.sort((a, b) => normalize(getShopTitle(a)).localeCompare(normalize(getShopTitle(b)), locale.value))
    if (sortParam.value === 'titleDesc') return list.sort((a, b) => normalize(getShopTitle(b)).localeCompare(normalize(getShopTitle(a)), locale.value))

    if (sortParam.value === 'urlAsc') return list.sort((a, b) => normalize(a?.url).localeCompare(normalize(b?.url), locale.value))
    if (sortParam.value === 'urlDesc') return list.sort((a, b) => normalize(b?.url).localeCompare(normalize(a?.url), locale.value))

    if (sortParam.value === 'emailAsc') return list.sort((a, b) => normalize(a?.email).localeCompare(normalize(b?.email), locale.value))
    if (sortParam.value === 'emailDesc') return list.sort((a, b) => normalize(b?.email).localeCompare(normalize(a?.email), locale.value))

    if (sortParam.value === 'phoneAsc') return list.sort((a, b) => normalize(a?.phone).localeCompare(normalize(b?.phone), locale.value))
    if (sortParam.value === 'phoneDesc') return list.sort((a, b) => normalize(b?.phone).localeCompare(normalize(a?.phone), locale.value))

    if (sortParam.value === 'statusAsc') return list.sort((a, b) => normalize(a?.status).localeCompare(normalize(b?.status), locale.value))
    if (sortParam.value === 'statusDesc') return list.sort((a, b) => normalize(b?.status).localeCompare(normalize(a?.status), locale.value))
    if (sortParam.value === 'statusDraft') return list.filter((shop) => shop?.status === 'draft')
    if (sortParam.value === 'statusPublished') return list.filter((shop) => shop?.status === 'published')
    if (sortParam.value === 'statusArchived') return list.filter((shop) => shop?.status === 'archived')

    if (sortParam.value === 'activity') return list.filter((shop) => !!shop.activity)
    if (sortParam.value === 'inactive') return list.filter((shop) => !shop.activity)

    if (sortParam.value === 'left') return list.filter((shop) => !!shop.left)
    if (sortParam.value === 'noLeft') return list.filter((shop) => !shop.left)
    if (sortParam.value === 'main') return list.filter((shop) => !!shop.main)
    if (sortParam.value === 'noMain') return list.filter((shop) => !shop.main)
    if (sortParam.value === 'right') return list.filter((shop) => !!shop.right)
    if (sortParam.value === 'noRight') return list.filter((shop) => !shop.right)

    if (sortParam.value === 'publishedAtDesc') return list.sort((a, b) => new Date(b.published_at || 0) - new Date(a.published_at || 0))
    if (sortParam.value === 'publishedAtAsc') return list.sort((a, b) => new Date(a.published_at || 0) - new Date(b.published_at || 0))

    if (sortParam.value === 'showFromAtDesc') return list.sort((a, b) => new Date(b.show_from_at || 0) - new Date(a.show_from_at || 0))
    if (sortParam.value === 'showFromAtAsc') return list.sort((a, b) => new Date(a.show_from_at || 0) - new Date(b.show_from_at || 0))

    if (sortParam.value === 'showToAtDesc') return list.sort((a, b) => new Date(b.show_to_at || 0) - new Date(a.show_to_at || 0))
    if (sortParam.value === 'showToAtAsc') return list.sort((a, b) => new Date(a.show_to_at || 0) - new Date(b.show_to_at || 0))

    if (sortParam.value === 'createdAtDesc' || sortParam.value === 'dateDesc') return list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
    if (sortParam.value === 'createdAtAsc' || sortParam.value === 'dateAsc') return list.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0))

    if (sortParam.value === 'updatedAtDesc') return list.sort((a, b) => new Date(b.updated_at || 0) - new Date(a.updated_at || 0))
    if (sortParam.value === 'updatedAtAsc') return list.sort((a, b) => new Date(a.updated_at || 0) - new Date(b.updated_at || 0))

    if (sortParam.value === 'views' || sortParam.value === 'viewsDesc') return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    if (sortParam.value === 'viewsAsc') return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0))

    if (sortParam.value === 'imagesDesc') return list.sort((a, b) => (b.images_count ?? 0) - (a.images_count ?? 0))
    if (sortParam.value === 'imagesAsc') return list.sort((a, b) => (a.images_count ?? 0) - (b.images_count ?? 0))

    if (sortParam.value === 'moderationPending') return list.filter((shop) => moderationNum(shop?.moderation_status) === 0)
    if (sortParam.value === 'moderationApproved') return list.filter((shop) => moderationNum(shop?.moderation_status) === 1)
    if (sortParam.value === 'moderationRejected') return list.filter((shop) => moderationNum(shop?.moderation_status) === 2)

    if (sortParam.value === 'moderationStatusAsc') return list.sort((a, b) => moderationNum(a?.moderation_status) - moderationNum(b?.moderation_status))
    if (sortParam.value === 'moderationStatusDesc') return list.sort((a, b) => moderationNum(b?.moderation_status) - moderationNum(a?.moderation_status))

    return list
}

/** Фильтрация магазинов */
const filteredShops = computed(() => {
    let filtered = localShops.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortShops(filtered)
    }

    filtered = filtered.filter((shop) => {
        const title = normalize(getShopTitle(shop))
        const short = normalize(getShopShort(shop))
        const description = normalize(getShopDescription(shop))
        const url = normalize(shop?.url)
        const email = normalize(shop?.email)
        const phone = normalize(shop?.phone)
        const companyTitle = normalize(getCompanyTitle(shop))
        const companyLegalName = normalize(shop?.company?.legal_name)
        const ownerName = normalize(shop?.owner?.name)
        const ownerEmail = normalize(shop?.owner?.email)

        return title.includes(query)
            || short.includes(query)
            || description.includes(query)
            || url.includes(query)
            || email.includes(query)
            || phone.includes(query)
            || companyTitle.includes(query)
            || companyLegalName.includes(query)
            || ownerName.includes(query)
            || ownerEmail.includes(query)
    })

    return sortShops(filtered)
})

/** Пагинация магазинов */
const paginatedShops = computed(() => {
    const perPage = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * perPage

    return filteredShops.value.slice(start, start + perPage)
})

/** Список для отображения */
const displayedShops = computed(() => {
    return props.useServerProcessing
        ? shopsList.value
        : paginatedShops.value
})

/** Сброс страницы при изменениях */
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/** Выбранные магазины */
const selectedShops = ref([])

/** Массовое выделение */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedShops.value.map((shop) => shop.id)

    if (checked) {
        selectedShops.value = [...new Set([...selectedShops.value, ...ids])]
    } else {
        selectedShops.value = selectedShops.value.filter((id) => !ids.includes(id))
    }
}

/** Переключение выбора магазина */
const toggleSelectShop = (shopId) => {
    const index = selectedShops.value.indexOf(shopId)

    if (index > -1) {
        selectedShops.value.splice(index, 1)
    } else {
        selectedShops.value.push(shopId)
    }
}

/** Массовое обновление активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedShops.value.length) {
        toast.warning('Выберите магазины для активации/деактивации')
        return
    }

    const idsToUpdate = [...selectedShops.value]

    router.put(
        route('admin.actions.marketShops.bulkUpdateActivity'),
        { ids: idsToUpdate, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localShops.value = localShops.value.map((shop) => {
                    if (idsToUpdate.includes(shop.id)) {
                        return { ...shop, activity: newActivity }
                    }

                    return shop
                })

                selectedShops.value = []
                toast.success('Активность магазинов массово обновлена')
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности'
                toast.error(msg)
            },
        }
    )
}

/** Массовое обновление позиции */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedShops.value.length) {
        toast.warning('Выберите магазины для массового действия')
        return
    }

    const idsToUpdate = [...selectedShops.value]

    router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                localShops.value = localShops.value.map((shop) => {
                    if (idsToUpdate.includes(shop.id)) {
                        return { ...shop, [field]: newValue }
                    }

                    return shop
                })

                selectedShops.value = []
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
    if (!selectedShops.value.length) {
        toast.warning('Выберите хотя бы один магазин для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные магазины?')) return

    router.delete(route('admin.actions.marketShops.bulkDestroy'), {
        data: { ids: selectedShops.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedShops.value = []
            toast.success('Массовое удаление магазинов успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении магазинов.')
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
    } else if (action === 'left') {
        bulkToggleFlag('left', true, 'admin.actions.marketShops.bulkUpdateLeft', 'Магазины добавлены в левую колонку')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.marketShops.bulkUpdateLeft', 'Магазины убраны из левой колонки')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.marketShops.bulkUpdateMain', 'Магазины добавлены в главный блок')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.marketShops.bulkUpdateMain', 'Магазины убраны из главного блока')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.marketShops.bulkUpdateRight', 'Магазины добавлены в правую колонку')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.marketShops.bulkUpdateRight', 'Магазины убраны из правой колонки')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Модерация магазина */
const approveShop = (shop, status = 1, note = '') => {
    if (!shop?.id) return

    router.put(
        route('admin.actions.marketShops.approve', { marketShop: shop.id }),
        { moderation_status: status, moderation_note: note },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                patchLocalShop(shop.id, (node) => {
                    node.moderation_status = status
                    node.is_approved = status === 1
                    node.moderation_note = note
                })

                toast.success(status === 1 ? 'Магазин одобрен' : 'Магазин отклонён')
            },
            onError: () => toast.error('Ошибка модерации магазина'),
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
        route('admin.actions.marketShops.updateSortBulk'),
        { items },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Сортировка магазинов обновлена'),
            onError: (errors) => {
                console.error('Ошибка сортировки магазинов:', errors)
                toast.error(errors.message || 'Ошибка обновления сортировки')
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('marketShops')">
        <template #header>
            <TitlePage>{{ t('marketShops') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.marketShops.create')">
                        {{ t('addMarketShop') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketShopsProcessingMode"
                        :mode="adminMarketShopsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="shopsCount"
                    />
                </div>

                <SearchInput
                    v-if="shopsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="shopsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="shopsCount"
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
                        update-route="admin.settings.updateAdminCountMarketShops"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>

                <div
                    v-if="shopsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ shopsCount }}</CountTable>

                    <BulkActionSelect
                        v-if="shopsCount"
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="shopsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredShops.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="shops"
                    />
                </div>

                <ShopTable
                    v-if="viewMode === 'table'"
                    :shops="displayedShops"
                    :selected-shops="selectedShops"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectShop"
                    @toggle-all="toggleAll"
                    @approve="approveShop"
                />

                <ShopCardGrid
                    v-else
                    :shops="displayedShops"
                    :selected-shops="selectedShops"
                    :is-admin="isAdmin"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectShop"
                    @toggle-all="toggleAll"
                    @approve="approveShop"
                />

                <div
                    v-if="shopsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredShops.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="shops"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteShop"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
