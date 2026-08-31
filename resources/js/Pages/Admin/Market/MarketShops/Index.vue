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
import { computed, defineProps, ref, watch } from 'vue'
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

/** Перевод магазина */
const getShopTranslation = (shop) => shop?.translation || {}

/** Название магазина для отображения */
const getShopTitle = (shop) => getShopTranslation(shop)?.title || `ID: ${shop?.id}`

/** Поля перевода магазина для поиска/сортировки */
const getShopTranslationTitle = (shop) => getShopTranslation(shop)?.title || ''
const getShopSubtitle = (shop) => getShopTranslation(shop)?.subtitle || ''
const getShopShort = (shop) => getShopTranslation(shop)?.short || ''
const getShopDescription = (shop) => getShopTranslation(shop)?.description || ''

/** Перевод компании */
const getCompanyTranslation = (shop) => shop?.company?.translation || {}

/** Название компании для отображения */
const getCompanyTitle = (shop) => {
    return getCompanyTranslation(shop)?.title
        || shop?.company?.legal_name
        || `Company ID: ${shop?.market_company_id}`
}

/** Название компании текущей локали для поиска */
const getCompanyTranslationTitle = (shop) => getCompanyTranslation(shop)?.title || ''

/** Нормализация строки */
const normalize = (value) => String(value ?? '').trim().toLowerCase()

/** Нормализация числа */
const numberValue = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Нормализация статуса модерации */
const moderationNum = (value) => numberValue(value)

/** Числовое значение даты */
const dateValue = (value) => {
    if (!value) return 0

    const timestamp = new Date(value).getTime()
    return Number.isNaN(timestamp) ? 0 : timestamp
}

/** Стабильный tie-break по ID DESC */
const compareIdDesc = (a, b) => numberValue(b?.id) - numberValue(a?.id)

/** Сравнение чисел с ID DESC при равенстве */
const compareNumber = (aValue, bValue, direction, a, b) => {
    const result = (numberValue(aValue) - numberValue(bValue)) * direction
    return result !== 0 ? result : compareIdDesc(a, b)
}

/** Сравнение строк с ID DESC при равенстве */
const compareText = (aValue, bValue, direction, a, b) => {
    const result = normalize(aValue).localeCompare(
        normalize(bValue),
        locale.value || undefined
    ) * direction

    return result !== 0 ? result : compareIdDesc(a, b)
}

/** Сравнение дат с ID DESC при равенстве */
const compareDate = (aValue, bValue, direction, a, b) => {
    const result = (dateValue(aValue) - dateValue(bValue)) * direction
    return result !== 0 ? result : compareIdDesc(a, b)
}

/** Фильтр с серверным порядком ID DESC */
const filterBy = (list, callback) => {
    return list.filter(callback).sort(compareIdDesc)
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
        route('admin.settings.updateAdminCountMarketShops'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} магазинов на странице.`),
            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления кол-ва магазинов.')
            },
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(props.sortParam || props.adminMarketShopsDefaultSort || 'idDesc')

/** Обновление сортировки */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortMarketShops'),
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
            const errorMsg = errors.general
                || errors[errorKey]
                || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Магазин: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: closeModal,
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
                toast.error(
                    errors.activity
                    || errors.general
                    || `Ошибка изменения активности для "${title}".`
                )
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
                toast.error(
                    errors.left
                    || errors.general
                    || `Ошибка изменения left для "${title}".`
                )
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
                toast.error(
                    errors.main
                    || errors.general
                    || `Ошибка изменения main для "${title}".`
                )
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
                toast.error(
                    errors.right
                    || errors.general
                    || `Ошибка изменения right для "${title}".`
                )
            },
        }
    )
}

/** Поиск и пагинация */
const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/**
 * Локальная сортировка магазинов.
 *
 * Повторяет MarketShop::scopeSortByParam().
 * Для всех сортировок, кроме чистого ID,
 * при равенстве используется ID DESC.
 */
const sortShops = (shops) => {
    const list = [...(shops || [])]
    const sort = sortParam.value

    if (sort === 'idAsc') {
        return list.sort((a, b) => numberValue(a?.id) - numberValue(b?.id))
    }

    if (sort === 'idDesc') {
        return list.sort(compareIdDesc)
    }

    if (sort === 'sortAsc') {
        return list.sort((a, b) => compareNumber(a?.sort, b?.sort, 1, a, b))
    }

    if (sort === 'sortDesc') {
        return list.sort((a, b) => compareNumber(a?.sort, b?.sort, -1, a, b))
    }

    if (sort === 'titleAsc') {
        return list.sort((a, b) => compareText(
            getShopTranslationTitle(a),
            getShopTranslationTitle(b),
            1,
            a,
            b
        ))
    }

    if (sort === 'titleDesc') {
        return list.sort((a, b) => compareText(
            getShopTranslationTitle(a),
            getShopTranslationTitle(b),
            -1,
            a,
            b
        ))
    }

    if (sort === 'urlAsc') {
        return list.sort((a, b) => compareText(a?.url, b?.url, 1, a, b))
    }

    if (sort === 'urlDesc') {
        return list.sort((a, b) => compareText(a?.url, b?.url, -1, a, b))
    }

    if (sort === 'emailAsc') {
        return list.sort((a, b) => compareText(a?.email, b?.email, 1, a, b))
    }

    if (sort === 'emailDesc') {
        return list.sort((a, b) => compareText(a?.email, b?.email, -1, a, b))
    }

    if (sort === 'phoneAsc') {
        return list.sort((a, b) => compareText(a?.phone, b?.phone, 1, a, b))
    }

    if (sort === 'phoneDesc') {
        return list.sort((a, b) => compareText(a?.phone, b?.phone, -1, a, b))
    }

    if (sort === 'statusAsc') {
        return list.sort((a, b) => compareText(a?.status, b?.status, 1, a, b))
    }

    if (sort === 'statusDesc') {
        return list.sort((a, b) => compareText(a?.status, b?.status, -1, a, b))
    }

    if (sort === 'statusDraft') {
        return filterBy(list, (shop) => shop?.status === 'draft')
    }

    if (sort === 'statusPublished') {
        return filterBy(list, (shop) => shop?.status === 'published')
    }

    if (sort === 'statusArchived') {
        return filterBy(list, (shop) => shop?.status === 'archived')
    }

    if (sort === 'publishedAtAsc') {
        return list.sort((a, b) => compareDate(
            a?.published_at,
            b?.published_at,
            1,
            a,
            b
        ))
    }

    if (sort === 'publishedAtDesc') {
        return list.sort((a, b) => compareDate(
            a?.published_at,
            b?.published_at,
            -1,
            a,
            b
        ))
    }

    if (sort === 'showFromAtAsc') {
        return list.sort((a, b) => compareDate(
            a?.show_from_at,
            b?.show_from_at,
            1,
            a,
            b
        ))
    }

    if (sort === 'showFromAtDesc') {
        return list.sort((a, b) => compareDate(
            a?.show_from_at,
            b?.show_from_at,
            -1,
            a,
            b
        ))
    }

    if (sort === 'showToAtAsc') {
        return list.sort((a, b) => compareDate(
            a?.show_to_at,
            b?.show_to_at,
            1,
            a,
            b
        ))
    }

    if (sort === 'showToAtDesc') {
        return list.sort((a, b) => compareDate(
            a?.show_to_at,
            b?.show_to_at,
            -1,
            a,
            b
        ))
    }

    if (sort === 'createdAtAsc' || sort === 'dateAsc') {
        return list.sort((a, b) => compareDate(
            a?.created_at,
            b?.created_at,
            1,
            a,
            b
        ))
    }

    if (sort === 'createdAtDesc' || sort === 'dateDesc') {
        return list.sort((a, b) => compareDate(
            a?.created_at,
            b?.created_at,
            -1,
            a,
            b
        ))
    }

    if (sort === 'updatedAtAsc') {
        return list.sort((a, b) => compareDate(
            a?.updated_at,
            b?.updated_at,
            1,
            a,
            b
        ))
    }

    if (sort === 'updatedAtDesc') {
        return list.sort((a, b) => compareDate(
            a?.updated_at,
            b?.updated_at,
            -1,
            a,
            b
        ))
    }

    if (sort === 'viewsAsc') {
        return list.sort((a, b) => compareNumber(a?.views, b?.views, 1, a, b))
    }

    if (sort === 'viewsDesc' || sort === 'views') {
        return list.sort((a, b) => compareNumber(a?.views, b?.views, -1, a, b))
    }

    if (sort === 'imagesAsc') {
        return list.sort((a, b) => compareNumber(
            a?.images_count,
            b?.images_count,
            1,
            a,
            b
        ))
    }

    if (sort === 'imagesDesc') {
        return list.sort((a, b) => compareNumber(
            a?.images_count,
            b?.images_count,
            -1,
            a,
            b
        ))
    }

    if (sort === 'activityAsc') {
        return list.sort((a, b) => compareNumber(a?.activity, b?.activity, 1, a, b))
    }

    if (sort === 'activityDesc') {
        return list.sort((a, b) => compareNumber(a?.activity, b?.activity, -1, a, b))
    }

    if (sort === 'activity') {
        return filterBy(list, (shop) => Boolean(shop?.activity))
    }

    if (sort === 'inactive') {
        return filterBy(list, (shop) => !shop?.activity)
    }

    if (sort === 'leftAsc') {
        return list.sort((a, b) => compareNumber(a?.left, b?.left, 1, a, b))
    }

    if (sort === 'leftDesc') {
        return list.sort((a, b) => compareNumber(a?.left, b?.left, -1, a, b))
    }

    if (sort === 'left') {
        return filterBy(list, (shop) => Boolean(shop?.left))
    }

    if (sort === 'noLeft') {
        return filterBy(list, (shop) => !shop?.left)
    }

    if (sort === 'mainAsc') {
        return list.sort((a, b) => compareNumber(a?.main, b?.main, 1, a, b))
    }

    if (sort === 'mainDesc') {
        return list.sort((a, b) => compareNumber(a?.main, b?.main, -1, a, b))
    }

    if (sort === 'main') {
        return filterBy(list, (shop) => Boolean(shop?.main))
    }

    if (sort === 'noMain') {
        return filterBy(list, (shop) => !shop?.main)
    }

    if (sort === 'rightAsc') {
        return list.sort((a, b) => compareNumber(a?.right, b?.right, 1, a, b))
    }

    if (sort === 'rightDesc') {
        return list.sort((a, b) => compareNumber(a?.right, b?.right, -1, a, b))
    }

    if (sort === 'right') {
        return filterBy(list, (shop) => Boolean(shop?.right))
    }

    if (sort === 'noRight') {
        return filterBy(list, (shop) => !shop?.right)
    }

    if (sort === 'moderationStatusAsc') {
        return list.sort((a, b) => compareNumber(
            moderationNum(a?.moderation_status),
            moderationNum(b?.moderation_status),
            1,
            a,
            b
        ))
    }

    if (sort === 'moderationStatusDesc') {
        return list.sort((a, b) => compareNumber(
            moderationNum(a?.moderation_status),
            moderationNum(b?.moderation_status),
            -1,
            a,
            b
        ))
    }

    if (sort === 'moderationPending') {
        return filterBy(
            list,
            (shop) => moderationNum(shop?.moderation_status) === 0
        )
    }

    if (sort === 'moderationApproved') {
        return filterBy(
            list,
            (shop) => moderationNum(shop?.moderation_status) === 1
        )
    }

    if (sort === 'moderationRejected') {
        return filterBy(
            list,
            (shop) => moderationNum(shop?.moderation_status) === 2
        )
    }

    if (sort === 'ownerNameAsc') {
        return list.sort((a, b) => compareText(
            a?.owner?.name,
            b?.owner?.name,
            1,
            a,
            b
        ))
    }

    if (sort === 'ownerNameDesc') {
        return list.sort((a, b) => compareText(
            a?.owner?.name,
            b?.owner?.name,
            -1,
            a,
            b
        ))
    }

    if (sort === 'ownerEmailAsc') {
        return list.sort((a, b) => compareText(
            a?.owner?.email,
            b?.owner?.email,
            1,
            a,
            b
        ))
    }

    if (sort === 'ownerEmailDesc') {
        return list.sort((a, b) => compareText(
            a?.owner?.email,
            b?.owner?.email,
            -1,
            a,
            b
        ))
    }

    if (sort === 'companyLegalNameAsc') {
        return list.sort((a, b) => compareText(
            a?.company?.legal_name,
            b?.company?.legal_name,
            1,
            a,
            b
        ))
    }

    if (sort === 'companyLegalNameDesc') {
        return list.sort((a, b) => compareText(
            a?.company?.legal_name,
            b?.company?.legal_name,
            -1,
            a,
            b
        ))
    }

    /**
     * Неизвестный параметр:
     * исходный frontend-набор уже приходит ordered()
     * из Controller: sort ASC, id DESC.
     */
    return list
}

/**
 * Локальная фильтрация.
 *
 * Повторяет MarketShop::scopeSearch():
 * - url/email/phone/status/moderation_note магазина;
 * - title/subtitle/short/description перевода;
 * - name/email владельца;
 * - url/legal_name/email/phone компании;
 * - title перевода компании.
 */
const filteredShops = computed(() => {
    let filtered = localShops.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortShops(filtered)
    }

    filtered = filtered.filter((shop) => {
        const translation = getShopTranslation(shop)
        const company = shop?.company || {}
        const companyTranslationTitle = getCompanyTranslationTitle(shop)

        const searchableValues = [
            shop?.url,
            shop?.email,
            shop?.phone,
            shop?.status,
            shop?.moderation_note,

            translation?.title,
            translation?.subtitle,
            translation?.short,
            translation?.description,

            shop?.owner?.name,
            shop?.owner?.email,

            company?.url,
            company?.legal_name,
            company?.email,
            company?.phone,

            companyTranslationTitle,
        ]

        return searchableValues.some((value) => normalize(value).includes(query))
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

/** Сброс страницы при изменении пагинации, поиска или сортировки */
watch([itemsPerPage, searchQuery, sortParam], () => {
    currentPage.value = 1
})

/** Выбранные магазины */
const selectedShops = ref([])

/** Массовое выделение */
const toggleAll = (payload) => {
    const checked = Boolean(
        payload?.checked
        ?? payload?.target?.checked
        ?? false
    )

    const ids = payload?.ids ?? displayedShops.value.map((shop) => shop.id)

    if (checked) {
        selectedShops.value = [
            ...new Set([...selectedShops.value, ...ids]),
        ]
    } else {
        selectedShops.value = selectedShops.value.filter(
            (id) => !ids.includes(id)
        )
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
    const activity = Boolean(newActivity)

    router.put(
        route('admin.actions.marketShops.bulkUpdateActivity'),
        { ids: idsToUpdate, activity },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localShops.value = localShops.value.map((shop) => {
                    if (idsToUpdate.includes(shop.id)) {
                        return { ...shop, activity }
                    }

                    return shop
                })

                selectedShops.value = []
                toast.success('Активность магазинов массово обновлена')
            },

            onError: (errors) => {
                const msg = errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности'

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
    const value = Boolean(newValue)

    router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: value },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localShops.value = localShops.value.map((shop) => {
                    if (idsToUpdate.includes(shop.id)) {
                        return { ...shop, [field]: value }
                    }

                    return shop
                })

                selectedShops.value = []
                toast.success(successMessage)
            },

            onError: (errors) => {
                const msg = errors?.ids
                    || errors?.[field]
                    || errors?.general
                    || 'Ошибка массового обновления'

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
            toast.error(
                errors[errorKey]
                || 'Произошла ошибка при удалении магазинов.'
            )
        },
    })
}

/** Обработка массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ checked: true })
    } else if (action === 'deselectAll') {
        toggleAll({ checked: false })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'left') {
        bulkToggleFlag(
            'left',
            true,
            'admin.actions.marketShops.bulkUpdateLeft',
            'Магазины добавлены в левую колонку'
        )
    } else if (action === 'noLeft') {
        bulkToggleFlag(
            'left',
            false,
            'admin.actions.marketShops.bulkUpdateLeft',
            'Магазины убраны из левой колонки'
        )
    } else if (action === 'main') {
        bulkToggleFlag(
            'main',
            true,
            'admin.actions.marketShops.bulkUpdateMain',
            'Магазины добавлены в главный блок'
        )
    } else if (action === 'noMain') {
        bulkToggleFlag(
            'main',
            false,
            'admin.actions.marketShops.bulkUpdateMain',
            'Магазины убраны из главного блока'
        )
    } else if (action === 'right') {
        bulkToggleFlag(
            'right',
            true,
            'admin.actions.marketShops.bulkUpdateRight',
            'Магазины добавлены в правую колонку'
        )
    } else if (action === 'noRight') {
        bulkToggleFlag(
            'right',
            false,
            'admin.actions.marketShops.bulkUpdateRight',
            'Магазины убраны из правой колонки'
        )
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Модерация магазина */
const approveShop = (shop, status = 1, note = '') => {
    if (!shop?.id) return

    const moderationStatus = moderationNum(status)

    router.put(
        route('admin.actions.marketShops.approve', { marketShop: shop.id }),
        {
            moderation_status: moderationStatus,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalShop(shop.id, (node) => {
                    node.moderation_status = moderationStatus
                    node.is_pending = moderationStatus === 0
                    node.is_approved = moderationStatus === 1
                    node.is_rejected = moderationStatus === 2
                    node.moderation_note = note
                })

                toast.success(
                    moderationStatus === 1
                        ? 'Магазин одобрен'
                        : 'Магазин отклонён'
                )
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

            onSuccess: () => {
                items.forEach((item) => {
                    patchLocalShop(item.id, (shop) => {
                        shop.sort = item.sort
                    })
                })

                toast.success('Сортировка магазинов обновлена')
            },

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
                        @update:sortParam="(value) => (sortParam = value)"
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
