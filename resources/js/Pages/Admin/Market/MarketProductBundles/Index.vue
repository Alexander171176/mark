<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список комплектов товаров маркетплейса.
 *
 * Возможности:
 * - frontend | server | auto;
 * - локальный и серверный поиск;
 * - локальная и серверная пагинация;
 * - сортировка;
 * - таблица и карточки;
 * - массовые действия;
 * - модерация;
 * - activity / left / main / right;
 * - drag-and-drop сортировка.
 */

import { computed, defineProps, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

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

import BulkActionSelect from '@/Components/Admin/Market/MarketProductBundle/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketProductBundle/Sort/SortSelect.vue'

import BundleTable from '@/Components/Admin/Market/MarketProductBundle/Table/BundleTable.vue'
import BundleCardGrid from '@/Components/Admin/Market/MarketProductBundle/View/BundleCardGrid.vue'

/* ======================== Services ======================== */

const { t, locale } = useI18n()
const toast = useToast()
const page = usePage()

/* ======================== Props ======================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    adminMarketProductBundlesProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    adminMarketProductBundlesPerPage: {
        type: Number,
        default: 6,
    },

    adminMarketProductBundlesDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    bundles: {
        type: [Array, Object],
        default: () => [],
    },

    bundlesCount: {
        type: Number,
        default: 0,
    },

    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ======================== Current user ======================== */

/** Текущий пользователь является администратором */
const isAdmin = computed(() => {
    const roles = page.props?.auth?.user?.roles || []

    return roles.some((role) => role?.name === 'admin')
})

/* ======================== Bundle helpers ======================== */

/** Текущий перевод комплекта товаров */
const getBundleTranslation = (bundle) => {
    return bundle?.translation
        || bundle?.translations?.[0]
        || {}
}

/** Название комплекта товаров */
const getBundleTitle = (bundle) => {
    return getBundleTranslation(bundle)?.title
        || `ID: ${bundle?.id}`
}

/** Подзаголовок комплекта товаров */
const getBundleSubtitle = (bundle) => {
    return getBundleTranslation(bundle)?.subtitle || ''
}

/** Краткое описание комплекта товаров */
const getBundleShort = (bundle) => {
    return getBundleTranslation(bundle)?.short || ''
}

/** Полное описание комплекта товаров */
const getBundleDescription = (bundle) => {
    return getBundleTranslation(bundle)?.description || ''
}

/** Название компании */
const getCompanyTitle = (bundle) => {
    return bundle?.company?.title ||
        bundle?.company?.translation?.title || bundle?.company?.legal_name || ''
}

/** Название магазина */
const getShopTitle = (bundle) => {
    return bundle?.shop?.title || bundle?.shop?.translation?.title || ''
}

/** Имя владельца */
const getOwnerName = (bundle) => {
    return bundle?.owner?.name || ''
}

/** Email владельца */
const getOwnerEmail = (bundle) => {
    return bundle?.owner?.email || ''
}

/** Код валюты */
const getCurrencyCode = (bundle) => {
    return bundle?.currency?.code || ''
}

/* ======================== Normalization ======================== */

/** Нормализация строки */
const normalize = (value) => {
    return (value ?? '')
        .toString()
        .trim()
        .toLowerCase()
}

/** Безопасное число */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/** Безопасная дата */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

/** Статус модерации в виде числа */
const moderationNum = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/* ======================== View mode ======================== */

/** Режим отображения */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_product_bundles')
    || 'cards'
)

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_market_product_bundles',
        value
    )
})

/* ======================== Settings ======================== */

/** Количество элементов на странице */
const itemsPerPage = ref(
    props.adminMarketProductBundlesPerPage || 6
)

/** Сохранение количества элементов */
watch(itemsPerPage, (newValue, oldValue) => {
    if (newValue === oldValue) {
        return
    }

    router.put(
        route('admin.settings.updateAdminCountMarketProductBundles'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newValue} комплектов товаров на странице.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления количества комплектов товаров.'
                )
            },
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(
    props.sortParam
    || props.adminMarketProductBundlesDefaultSort
    || 'idDesc'
)

/** Сохранение сортировки */
watch(sortParam, (newValue, oldValue) => {
    if (newValue === oldValue) {
        return
    }

    router.put(
        route('admin.settings.updateAdminSortMarketProductBundles'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(
                                new URLSearchParams(
                                    window.location.search
                                )
                            ),

                            sort: newValue || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info(
                    'Сортировка комплектов товаров успешно изменена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления сортировки комплектов товаров.'
                )
            },
        }
    )
})

/* ======================== Bundle collection ======================== */

/** Нормализованный список комплектов товаров */
const bundlesList = computed(() => {
    if (Array.isArray(props.bundles)) {
        return props.bundles
    }

    if (Array.isArray(props.bundles?.data)) {
        return props.bundles.data
    }

    if (Array.isArray(props.bundles?.data?.data)) {
        return props.bundles.data.data
    }

    if (Array.isArray(props.bundles?.resource)) {
        return props.bundles.resource
    }

    return []
})

/** Локальная копия комплектов товаров */
const localBundles = ref([])

/** Синхронизация props с локальной копией */
watch(
    bundlesList,
    (newValue) => {
        localBundles.value = JSON.parse(
            JSON.stringify(newValue || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ======================== Delete modal ======================== */

const showConfirmDeleteModal = ref(false)

const bundleToDeleteId = ref(null)
const bundleToDeleteTitle = ref('')

/** Открытие подтверждения удаления */
const confirmDelete = (productOrId, title = null) => {
    if (typeof productOrId === 'object') {
        bundleToDeleteId.value = productOrId?.id
        bundleToDeleteTitle.value = title
            || getBundleTitle(productOrId)
    } else {
        bundleToDeleteId.value = productOrId
        bundleToDeleteTitle.value = title
            || `ID: ${productOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие подтверждения */
const closeModal = () => {
    showConfirmDeleteModal.value = false

    bundleToDeleteId.value = null
    bundleToDeleteTitle.value = ''
}

/** Удаление одного комплекта товаров */
const deleteBundle = () => {
    if (bundleToDeleteId.value === null) {
        return
    }

    const id = bundleToDeleteId.value
    const title = bundleToDeleteTitle.value

    router.delete(
        route(
            'admin.marketProductBundles.destroy',
            {
                marketProductBundle: id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Комплект товаров "${title || `ID: ${id}`}" удалён.`
                )
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                const message = errors?.general
                    || errors?.[firstKey]
                    || 'Ошибка при удалении комплекта товаров.'

                toast.error(
                    `${message} (Комплект товаров: ${title || `ID: ${id}`})`
                )
            },

            onFinish: closeModal,
        }
    )
}

/* ======================== Local patch ======================== */

/** Локальное обновление комплекта товаров */
const patchLocalBundle = (productId, callback) => {
    const index = localBundles.value.findIndex(
        (bundle) => Number(bundle.id) === Number(productId)
    )

    if (index === -1) {
        return
    }

    callback(localBundles.value[index])
}

/* ======================== Single actions ======================== */

/** Переключение активности */
const toggleActivity = (bundle) => {
    if (!bundle?.id) {
        return
    }

    const activity = !bundle.activity
    const title = getBundleTitle(bundle)

    router.put(
        route(
            'admin.actions.marketProductBundles.updateActivity',
            {
                marketProductBundle: bundle.id,
            }
        ),
        {
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBundle(bundle.id, (item) => {
                    item.activity = activity
                    item.is_active = activity
                })

                toast.success(
                    activity
                        ? `Комплект товаров "${title}" активирован.`
                        : `Комплект товаров "${title}" деактивирован.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности комплекта товаров "${title}".`
                )
            },
        }
    )
}

/** Переключение left */
const toggleLeft = (bundle) => {
    if (!bundle?.id) {
        return
    }

    const left = !bundle.left
    const title = getBundleTitle(bundle)

    router.put(
        route(
            'admin.actions.marketProductBundles.updateLeft',
            {
                marketProductBundle: bundle.id,
            }
        ),
        {
            left,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBundle(bundle.id, (item) => {
                    item.left = left
                })

                toast.success(
                    `Позиция left комплекта товаров "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.left
                    || errors?.general
                    || `Ошибка изменения left комплекта товаров "${title}".`
                )
            },
        }
    )
}

/** Переключение main */
const toggleMain = (bundle) => {
    if (!bundle?.id) {
        return
    }

    const main = !bundle.main
    const title = getBundleTitle(bundle)

    router.put(
        route(
            'admin.actions.marketProductBundles.updateMain',
            {
                marketProductBundle: bundle.id,
            }
        ),
        {
            main,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBundle(bundle.id, (item) => {
                    item.main = main
                })

                toast.success(
                    `Позиция main комплекта товаров "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.main
                    || errors?.general
                    || `Ошибка изменения main комплекта товаров "${title}".`
                )
            },
        }
    )
}

/** Переключение right */
const toggleRight = (bundle) => {
    if (!bundle?.id) {
        return
    }

    const right = !bundle.right
    const title = getBundleTitle(bundle)

    router.put(
        route(
            'admin.actions.marketProductBundles.updateRight',
            {
                marketProductBundle: bundle.id,
            }
        ),
        {
            right,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBundle(bundle.id, (item) => {
                    item.right = right
                })

                toast.success(
                    `Позиция right комплекта товаров "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.right
                    || errors?.general
                    || `Ошибка изменения right комплекта товаров "${title}".`
                )
            },
        }
    )
}

/** Модерация комплекта товаров */
const approveBundle = (
    bundle,
    status = 1,
    note = ''
) => {
    if (!bundle?.id || !isAdmin.value) {
        return
    }

    router.put(
        route(
            'admin.actions.marketProductBundles.approve',
            {
                marketProductBundle: bundle.id,
            }
        ),
        {
            moderation_status: status,
            moderation_note: note,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalBundle(bundle.id, (item) => {
                    item.moderation_status = status
                    item.is_approved = status === 1
                    item.moderation_note = note
                })

                toast.success(
                    status === 1
                        ? 'Комплект товаров одобрен.'
                        : 'Комплект товаров отклонён.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.moderation_status
                    || errors?.general
                    || 'Ошибка модерации комплекта товаров.'
                )
            },
        }
    )
}

/* ======================== Frontend filtering ======================== */

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/** Проверка совпадения комплекта с поиском */
const bundleMatchesSearch = (bundle, query) => {
    if (!query) {
        return true
    }

    const items = Array.isArray(bundle?.items)
        ? bundle.items
        : []

    const searchValues = [
        bundle?.id,
        bundle?.url,
        bundle?.sku,
        bundle?.vendor_code,
        bundle?.barcode,

        bundle?.price,
        bundle?.old_price,
        bundle?.purchase_price,
        bundle?.wholesale_price,
        bundle?.wholesale_min_quantity,
        bundle?.calculated_price,
        bundle?.effective_price,
        bundle?.available_quantity,

        bundle?.status,
        bundle?.moderation_note,

        getBundleTitle(bundle),
        getBundleSubtitle(bundle),
        getBundleShort(bundle),
        getBundleDescription(bundle),

        getCompanyTitle(bundle),
        bundle?.company?.legal_name,
        bundle?.company?.bin_iin,

        getShopTitle(bundle),
        bundle?.shop?.email,
        bundle?.shop?.phone,

        getOwnerName(bundle),
        getOwnerEmail(bundle),
        getCurrencyCode(bundle),

        ...items.flatMap((item) => [
            item?.display_title,
            item?.product?.translation?.title,
            item?.product?.title,
            item?.variant?.translation?.title,
            item?.variant?.title,
            item?.quantity,
            item?.unit_price,
            item?.discount_type,
            item?.discount_value,
            item?.total_price,
        ]),
    ]

    return searchValues.some((value) => {
        return normalize(value).includes(query)
    })
}

/** Отфильтрованные комплекты товаров */
const filteredBundles = computed(() => {
    if (props.useServerProcessing) {
        return localBundles.value
    }

    const query = normalize(searchQuery.value)

    return localBundles.value.filter(
        (bundle) => bundleMatchesSearch(bundle, query)
    )
})

/* ======================== Frontend sorting ======================== */

/** Сортировка строки */
const compareText = (left, right) => {
    return normalize(left).localeCompare(
        normalize(right),
        locale.value
    )
}

/**
 * Дополнительная сортировка по ID.
 *
 * Используется при одинаковых значениях основного поля,
 * чтобы порядок списка всегда оставался предсказуемым.
 */
const compareByIdDesc = (left, right) => {
    return safeNumber(right?.id) - safeNumber(left?.id)
}

/** Сортировка числового поля по возрастанию. */
const byNumberAsc = (field) => {
    return (left, right) => {
        return safeNumber(left?.[field])
            - safeNumber(right?.[field])
            || compareByIdDesc(left, right)
    }
}

/** Сортировка числового поля по убыванию. */
const byNumberDesc = (field) => {
    return (left, right) => {
        return safeNumber(right?.[field])
            - safeNumber(left?.[field])
            || compareByIdDesc(left, right)
    }
}

/** Сортировка строкового поля по возрастанию. */
const byStringAsc = (field) => {
    return (left, right) => {
        return compareText(
            left?.[field],
            right?.[field]
        ) || compareByIdDesc(left, right)
    }
}

/** Сортировка строкового поля по убыванию. */
const byStringDesc = (field) => {
    return (left, right) => {
        return compareText(
            right?.[field],
            left?.[field]
        ) || compareByIdDesc(left, right)
    }
}

/** Сортировка поля даты по возрастанию. */
const byDateAsc = (field) => {
    return (left, right) => {
        return safeDate(left?.[field])
            - safeDate(right?.[field])
            || compareByIdDesc(left, right)
    }
}

/** Сортировка поля даты по убыванию. */
const byDateDesc = (field) => {
    return (left, right) => {
        return safeDate(right?.[field])
            - safeDate(left?.[field])
            || compareByIdDesc(left, right)
    }
}

/** Сортировка и фильтрация комплектов товаров */
const sortedBundles = computed(() => {
    const list = [...filteredBundles.value]

    /*
     * В режиме серверной обработки контроллер уже выполнил
     * фильтрацию и сортировку. Повторная сортировка не нужна.
     */
    if (props.useServerProcessing) {
        return list
    }

    const sort = sortParam.value

    /* ======================== Filters ======================== */

    /** Только активные комплекты */
    if (sort === 'activity') {
        return list.filter(
            (bundle) => bundle.activity
        )
    }

    /** Только неактивные комплекты */
    if (sort === 'inactive') {
        return list.filter(
            (bundle) => !bundle.activity
        )
    }

    /** Только комплекты с позициями */
    if (sort === 'hasItems') {
        return list.filter(
            (bundle) =>
                safeNumber(bundle.items_count) > 0
        )
    }

    /** Только пустые комплекты */
    if (sort === 'withoutItems') {
        return list.filter(
            (bundle) =>
                safeNumber(bundle.items_count) === 0
        )
    }

    /** Только комплекты с автоматическим расчётом цены */
    if (sort === 'calculatedPrice') {
        return list.filter(
            (bundle) => bundle.calculate_price
        )
    }

    /** Только комплекты с ручной ценой */
    if (sort === 'manualPrice') {
        return list.filter(
            (bundle) => !bundle.calculate_price
        )
    }

    /** Только комплекты в левой рекламной позиции */
    if (sort === 'left') {
        return list.filter(
            (bundle) => bundle.left
        )
    }

    /** Только комплекты вне левой рекламной позиции */
    if (sort === 'noLeft') {
        return list.filter(
            (bundle) => !bundle.left
        )
    }

    /** Только комплекты в главной рекламной позиции */
    if (sort === 'main') {
        return list.filter(
            (bundle) => bundle.main
        )
    }

    /** Только комплекты вне главной рекламной позиции */
    if (sort === 'noMain') {
        return list.filter(
            (bundle) => !bundle.main
        )
    }

    /** Только комплекты в правой рекламной позиции */
    if (sort === 'right') {
        return list.filter(
            (bundle) => bundle.right
        )
    }

    /** Только комплекты вне правой рекламной позиции */
    if (sort === 'noRight') {
        return list.filter(
            (bundle) => !bundle.right
        )
    }

    /** Только новинки */
    if (sort === 'new') {
        return list.filter(
            (bundle) => bundle.is_new
        )
    }

    /** Только комплекты без признака новинки */
    if (sort === 'notNew') {
        return list.filter(
            (bundle) => !bundle.is_new
        )
    }

    /** Только хиты продаж */
    if (sort === 'hit') {
        return list.filter(
            (bundle) => bundle.is_hit
        )
    }

    /** Только комплекты без признака хита */
    if (sort === 'notHit') {
        return list.filter(
            (bundle) => !bundle.is_hit
        )
    }

    /** Только комплекты распродажи */
    if (sort === 'sale') {
        return list.filter(
            (bundle) => bundle.is_sale
        )
    }

    /** Только комплекты без признака распродажи */
    if (sort === 'notSale') {
        return list.filter(
            (bundle) => !bundle.is_sale
        )
    }

    /** Только черновики */
    if (sort === 'statusDraft') {
        return list.filter(
            (bundle) => bundle.status === 'draft'
        )
    }

    /** Только опубликованные комплекты */
    if (sort === 'statusPublished') {
        return list.filter(
            (bundle) => bundle.status === 'published'
        )
    }

    /** Только архивные комплекты */
    if (sort === 'statusArchived') {
        return list.filter(
            (bundle) => bundle.status === 'archived'
        )
    }

    /** Только комплекты на модерации */
    if (sort === 'moderationPending') {
        return list.filter(
            (bundle) =>
                moderationNum(
                    bundle.moderation_status
                ) === 0
        )
    }

    /** Только одобренные комплекты */
    if (sort === 'moderationApproved') {
        return list.filter(
            (bundle) =>
                moderationNum(
                    bundle.moderation_status
                ) === 1
        )
    }

    /** Только отклонённые комплекты */
    if (sort === 'moderationRejected') {
        return list.filter(
            (bundle) =>
                moderationNum(
                    bundle.moderation_status
                ) === 2
        )
    }

    /* ======================== Sorting map ======================== */

    const sortMap = {
        /** ID */
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        /** Ручная сортировка */
        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        /** Название комплекта */
        titleAsc: (left, right) => {
            return compareText(
                getBundleTitle(left),
                getBundleTitle(right)
            ) || compareByIdDesc(left, right)
        },

        titleDesc: (left, right) => {
            return compareText(
                getBundleTitle(right),
                getBundleTitle(left)
            ) || compareByIdDesc(left, right)
        },

        /** Основные коды */
        urlAsc: byStringAsc('url'),
        urlDesc: byStringDesc('url'),

        skuAsc: byStringAsc('sku'),
        skuDesc: byStringDesc('sku'),

        vendorCodeAsc:
            byStringAsc('vendor_code'),

        vendorCodeDesc:
            byStringDesc('vendor_code'),

        barcodeAsc:
            byStringAsc('barcode'),

        barcodeDesc:
            byStringDesc('barcode'),

        /** Компания */
        companyAsc: (left, right) => {
            return compareText(
                getCompanyTitle(left),
                getCompanyTitle(right)
            ) || compareByIdDesc(left, right)
        },

        companyDesc: (left, right) => {
            return compareText(
                getCompanyTitle(right),
                getCompanyTitle(left)
            ) || compareByIdDesc(left, right)
        },

        /** Магазин */
        shopAsc: (left, right) => {
            return compareText(
                getShopTitle(left),
                getShopTitle(right)
            ) || compareByIdDesc(left, right)
        },

        shopDesc: (left, right) => {
            return compareText(
                getShopTitle(right),
                getShopTitle(left)
            ) || compareByIdDesc(left, right)
        },

        /** Имя владельца */
        ownerNameAsc: (left, right) => {
            return compareText(
                getOwnerName(left),
                getOwnerName(right)
            ) || compareByIdDesc(left, right)
        },

        ownerNameDesc: (left, right) => {
            return compareText(
                getOwnerName(right),
                getOwnerName(left)
            ) || compareByIdDesc(left, right)
        },

        /** Email владельца */
        ownerEmailAsc: (left, right) => {
            return compareText(
                getOwnerEmail(left),
                getOwnerEmail(right)
            ) || compareByIdDesc(left, right)
        },

        ownerEmailDesc: (left, right) => {
            return compareText(
                getOwnerEmail(right),
                getOwnerEmail(left)
            ) || compareByIdDesc(left, right)
        },

        /** Режим формирования цены */
        calculatePriceAsc:
            byNumberAsc('calculate_price'),

        calculatePriceDesc:
            byNumberDesc('calculate_price'),

        /** Основная цена */
        priceAsc:
            byNumberAsc('price'),

        priceDesc:
            byNumberDesc('price'),

        /** Старая цена */
        oldPriceAsc:
            byNumberAsc('old_price'),

        oldPriceDesc:
            byNumberDesc('old_price'),

        /** Закупочная цена */
        purchasePriceAsc:
            byNumberAsc('purchase_price'),

        purchasePriceDesc:
            byNumberDesc('purchase_price'),

        /** Оптовая цена */
        wholesalePriceAsc:
            byNumberAsc('wholesale_price'),

        wholesalePriceDesc:
            byNumberDesc('wholesale_price'),

        /** Минимальное оптовое количество */
        wholesaleMinQuantityAsc:
            byNumberAsc('wholesale_min_quantity'),

        wholesaleMinQuantityDesc:
            byNumberDesc('wholesale_min_quantity'),

        /** Количество позиций */
        itemsAsc:
            byNumberAsc('items_count'),

        itemsDesc:
            byNumberDesc('items_count'),

        /** Количество изображений */
        imagesAsc:
            byNumberAsc('images_count'),

        imagesDesc:
            byNumberDesc('images_count'),

        /** Просмотры */
        viewsAsc:
            byNumberAsc('views'),

        viewsDesc:
            byNumberDesc('views'),

        /** Лайки */
        likesAsc:
            byNumberAsc('likes_count'),

        likesDesc:
            byNumberDesc('likes_count'),

        /** Средний рейтинг */
        ratingAsc:
            byNumberAsc('rating_avg'),

        ratingDesc:
            byNumberDesc('rating_avg'),

        /** Количество оценок */
        ratingCountAsc:
            byNumberAsc('rating_count'),

        ratingCountDesc:
            byNumberDesc('rating_count'),

        /** Активность */
        activityAsc:
            byNumberAsc('activity'),

        activityDesc:
            byNumberDesc('activity'),

        /** Новинка */
        newAsc:
            byNumberAsc('is_new'),

        newDesc:
            byNumberDesc('is_new'),

        /** Хит продаж */
        hitAsc:
            byNumberAsc('is_hit'),

        hitDesc:
            byNumberDesc('is_hit'),

        /** Распродажа */
        saleAsc:
            byNumberAsc('is_sale'),

        saleDesc:
            byNumberDesc('is_sale'),

        /** Левая рекламная позиция */
        leftAsc:
            byNumberAsc('left'),

        leftDesc:
            byNumberDesc('left'),

        /** Главная рекламная позиция */
        mainAsc:
            byNumberAsc('main'),

        mainDesc:
            byNumberDesc('main'),

        /** Правая рекламная позиция */
        rightAsc:
            byNumberAsc('right'),

        rightDesc:
            byNumberDesc('right'),

        /** Статус публикации */
        statusAsc:
            byStringAsc('status'),

        statusDesc:
            byStringDesc('status'),

        /** Статус модерации */
        moderationStatusAsc:
            byNumberAsc('moderation_status'),

        moderationStatusDesc:
            byNumberDesc('moderation_status'),

        /** Дата публикации */
        publishedAtAsc:
            byDateAsc('published_at'),

        publishedAtDesc:
            byDateDesc('published_at'),

        /** Начало показа */
        showFromAtAsc:
            byDateAsc('show_from_at'),

        showFromAtDesc:
            byDateDesc('show_from_at'),

        /** Окончание показа */
        showToAtAsc:
            byDateAsc('show_to_at'),

        showToAtDesc:
            byDateDesc('show_to_at'),

        /** Дата создания */
        createdAtAsc:
            byDateAsc('created_at'),

        createdAtDesc:
            byDateDesc('created_at'),

        /** Совместимость со старым параметром date */
        dateAsc:
            byDateAsc('created_at'),

        dateDesc:
            byDateDesc('created_at'),

        /** Дата обновления */
        updatedAtAsc:
            byDateAsc('updated_at'),

        updatedAtDesc:
            byDateDesc('updated_at'),
    }

    const comparator = sortMap[sort]

    /*
     * Если параметр отсутствует в карте,
     * используем стандартный порядок модели:
     * sort ASC, затем ID DESC.
     */
    if (typeof comparator !== 'function') {
        return list.sort((left, right) => {
            return (
                safeNumber(left?.sort)
                - safeNumber(right?.sort)
            ) || compareByIdDesc(left, right)
        })
    }

    return list.sort(comparator)
})

/* ======================== Frontend pagination ======================== */

/** Локально отображаемые комплекты товаров */
const frontendPaginatedBundles = computed(() => {
    const start = (
        currentPage.value - 1
    ) * itemsPerPage.value

    return sortedBundles.value.slice(
        start,
        start + itemsPerPage.value
    )
})

/** Сброс страницы при фильтрации */
watch(
    [
        searchQuery,
        sortParam,
        itemsPerPage,
        viewMode,
    ],
    () => {
        currentPage.value = 1
    }
)

/* ======================== Displayed collection ======================== */

/** Итоговый список */
const displayedBundles = computed(() => {
    return props.useServerProcessing
        ? localBundles.value
        : frontendPaginatedBundles.value
})

/* ======================== Selection ======================== */

/** Выбранные комплекты товаров */
const selectedBundles = ref([])

/** Выбран ли комплект товаров */
const isSelected = (productId) => {
    return selectedBundles.value.includes(productId)
}

/** Выбор одного комплекта товаров */
const toggleSelectBundle = (bundleOrId) => {
    const id = typeof bundleOrId === 'object'
        ? bundleOrId?.id
        : bundleOrId

    if (!id) {
        return
    }

    if (isSelected(id)) {
        selectedBundles.value = selectedBundles.value.filter(
            (selectedId) => selectedId !== id
        )

        return
    }

    selectedBundles.value.push(id)
}

/** Выбрать или снять все видимые комплекты товаров */
const toggleAll = (eventOrValue) => {
    const checked = typeof eventOrValue === 'boolean'
        ? eventOrValue
        : Boolean(eventOrValue?.target?.checked ?? eventOrValue?.checked)

    if (!checked) {
        selectedBundles.value = []
        return
    }

    selectedBundles.value = displayedBundles.value
        .map((bundle) => bundle?.id)
        .filter(Boolean)
}

/** Все видимые комплекты товаров выбраны */
const allDisplayedSelected = computed(() => {
    const ids = displayedBundles.value
        .map((bundle) => bundle?.id)
        .filter(Boolean)

    return ids.length > 0
        && ids.every((id) => selectedBundles.value.includes(id))
})

/* ======================== Bulk actions ======================== */

/** Массовое изменение активности */
const bulkToggleActivity = (activity) => {
    if (!selectedBundles.value.length) {
        toast.warning(
            'Выберите хотя бы один комплект товаров.'
        )

        return
    }

    const ids = [...selectedBundles.value]

    router.put(
        route(
            'admin.actions.marketProductBundles.bulkUpdateActivity'
        ),
        {
            ids,
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localBundles.value.forEach((bundle) => {
                    if (ids.includes(bundle.id)) {
                        bundle.activity = activity
                        bundle.is_active = activity
                    }
                })

                selectedBundles.value = []

                toast.success(
                    'Активность выбранных комплектов товаров обновлена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового изменения активности.'
                )
            },
        }
    )
}

/** Массовое обновление boolean-флага комплектов товаров. */
const bulkToggleFlag = (field, value, routeName, message) => {
    if (!selectedBundles.value.length) {
        toast.warning('Выберите комплекты товаров для массового действия.')
        return
    }

    const ids = [...selectedBundles.value]

    router.put(route(routeName), {
        ids,
        [field]: value,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            ids.forEach((id) => {
                patchLocalBundle(id, (bundle) => {
                    bundle[field] = value
                })
            })

            selectedBundles.value = []
            toast.success(message)
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления комплектов товаров.')
        },
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedBundles.value.length) {
        toast.warning(
            'Выберите хотя бы один комплект товаров для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные комплекты товаров?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.marketProductBundles.bulkDestroy'
        ),
        {
            data: {
                ids: selectedBundles.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedBundles.value = []

                toast.success(
                    'Выбранные комплекты товаров успешно удалены.'
                )
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Ошибка массового удаления комплектов товаров.'
                )
            },
        }
    )
}

/** Обработка массового действия */
const handleBulkAction = (event) => {
    const action = event?.target?.value

    if (action === 'selectAll') {
        toggleAll(true)
    } else if (action === 'deselectAll') {
        toggleAll(false)
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'isNewOn') {
        bulkToggleFlag('is_new', true,
            'admin.actions.marketProductBundles.bulkUpdateIsNew',
            'Комплекты товаров добавлены в новинки.')
    } else if (action === 'isNewOff') {
        bulkToggleFlag('is_new', false,
            'admin.actions.marketProductBundles.bulkUpdateIsNew',
            'Комплекты товаров убраны из новинок.')
    } else if (action === 'isHitOn') {
        bulkToggleFlag('is_hit', true,
            'admin.actions.marketProductBundles.bulkUpdateIsHit',
            'Комплекты товаров добавлены в рекомендуемые.')
    } else if (action === 'isHitOff') {
        bulkToggleFlag('is_hit', false,
            'admin.actions.marketProductBundles.bulkUpdateIsHit',
            'Комплекты товаров убраны из рекомендуемых.')
    } else if (action === 'isSaleOn') {
        bulkToggleFlag('is_sale', true,
            'admin.actions.marketProductBundles.bulkUpdateIsSale',
            'Комплекты товаров добавлены в распродажу.')
    } else if (action === 'isSaleOff') {
        bulkToggleFlag('is_sale', false,
            'admin.actions.marketProductBundles.bulkUpdateIsSale',
            'Комплекты товаров убраны из распродажи.')
    } else if (action === 'left') {
        bulkToggleFlag(
            'left',
            true,
            'admin.actions.marketProductBundles.bulkUpdateLeft',
            'Комплекты товаров добавлены в левую колонку.'
        )
    } else if (action === 'noLeft') {
        bulkToggleFlag(
            'left',
            false,
            'admin.actions.marketProductBundles.bulkUpdateLeft',
            'Комплекты товаров убраны из левой колонки.'
        )
    } else if (action === 'main') {
        bulkToggleFlag(
            'main',
            true,
            'admin.actions.marketProductBundles.bulkUpdateMain',
            'Комплекты товаров добавлены в главный блок.'
        )
    } else if (action === 'noMain') {
        bulkToggleFlag(
            'main',
            false,
            'admin.actions.marketProductBundles.bulkUpdateMain',
            'Комплекты товаров убраны из главного блока.'
        )
    } else if (action === 'right') {
        bulkToggleFlag(
            'right',
            true,
            'admin.actions.marketProductBundles.bulkUpdateRight',
            'Комплекты товаров добавлены в правую колонку.'
        )
    } else if (action === 'noRight') {
        bulkToggleFlag(
            'right',
            false,
            'admin.actions.marketProductBundles.bulkUpdateRight',
            'Комплекты товаров убраны из правой колонки.'
        )
    } else if (action === 'delete') {
        bulkDelete()
    }

    if (event?.target) {
        event.target.value = ''
    }
}

/* ======================== Drag-and-drop sorting ======================== */

/** Массовое обновление sort */
const handleSortOrderUpdate = (newOrderIds) => {
    const items = (newOrderIds || []).map(
        (id, index) => ({
            id,
            sort: index,
        })
    )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.marketProductBundles.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                items.forEach((item) => {
                    patchLocalBundle(item.id, (bundle) => {
                        bundle.sort = item.sort
                    })
                })

                toast.success(
                    'Сортировка комплектов товаров обновлена.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка сортировки комплектов товаров:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.items
                    || 'Ошибка обновления сортировки комплектов товаров.'
                )
            },
        }
    )
}

/* ======================== Флаги: новинки, хиты, распродажа ======================== */

/** Универсальное переключение маркетингового флага комплекта товаров. */
const toggleBundleFlag = (bundle, field, routeName, enabledMessage, disabledMessage) => {
    if (!bundle?.id) {
        return
    }

    const newValue = !bundle[field]
    const title = getBundleTitle(bundle)

    router.put(route(routeName, { marketProductBundle: bundle.id }), {
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocalBundle(bundle.id, (item) => {
                item[field] = newValue
            })

            bundle[field] = newValue
            toast.success(newValue ? `${enabledMessage} "${title}".` : `${disabledMessage} "${title}".`)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || `Ошибка изменения флага комплекта товаров "${title}".`)
        },
    })
}

/** Переключение флага "Новинка". */
const toggleIsNew = (bundle) => {
    toggleBundleFlag(
        bundle,
        'is_new',
        'admin.actions.marketProductBundles.updateIsNew',
        'Комплект товаров добавлен в новинки',
        'Комплект товаров убран из новинок'
    )
}

/** Переключение флага "Рекомендуемый / хит". */
const toggleIsHit = (bundle) => {
    toggleBundleFlag(
        bundle,
        'is_hit',
        'admin.actions.marketProductBundles.updateIsHit',
        'Комплект товаров добавлен в рекомендуемые',
        'Комплект товаров убран из рекомендуемых'
    )
}

/** Переключение флага "Распродажа". */
const toggleIsSale = (bundle) => {
    toggleBundleFlag(
        bundle,
        'is_sale',
        'admin.actions.marketProductBundles.updateIsSale',
        'Комплект товаров добавлен в распродажу',
        'Комплект товаров убран из распродажи'
    )
}
</script>

<template>
    <AdminLayout :title="t('marketProductBundles')">
        <template #header>
            <TitlePage>
                {{ t('marketProductBundles') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4
                       bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden
                       shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель -->
                <div
                    class="flex flex-col gap-3 mb-3
                           sm:flex-row sm:items-center sm:justify-between"
                >
                    <DefaultButton
                        :href="route('admin.marketProductBundles.create')"
                    >
                        {{ t('addMarketProductBundle') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketProductBundlesProcessingMode"
                        :mode="adminMarketProductBundlesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="bundlesCount"
                    />
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="bundlesCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="bundlesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Пагинация и сортировка -->
                <div
                    v-if="bundlesCount"
                    class="flex flex-col md:flex-row
                           items-center justify-between
                           gap-3 my-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountMarketProductBundles"
                    />

                    <SortSelect :sortParam="sortParam" @update:sortParam="sortParam = $event" />
                </div>

                <!-- Счётчик, массовые действия, вид -->
                <div
                    v-if="bundlesCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between
                           gap-3"
                >
                    <CountTable>
                        {{ bundlesCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Верхняя пагинация -->
                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="bundles"
                    />
                </div>

                <!-- Таблица -->
                <BundleTable
                    v-if="viewMode === 'table'"
                    :bundles="displayedBundles"
                    :selected-bundles="selectedBundles"
                    :all-selected="allDisplayedSelected"
                    :is-admin="isAdmin"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-is-new="toggleIsNew"
                    @toggle-is-hit="toggleIsHit"
                    @toggle-is-sale="toggleIsSale"
                    @approve="approveBundle"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <!-- Карточки -->
                <BundleCardGrid
                    v-else
                    :bundles="displayedBundles"
                    :selected-bundles="selectedBundles"
                    :is-admin="isAdmin"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-is-new="toggleIsNew"
                    @toggle-is-hit="toggleIsHit"
                    @toggle-is-sale="toggleIsSale"
                    @approve="approveBundle"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <!-- Пустой список -->
                <div
                    v-if="!bundlesCount"
                    class="py-12 text-center
                           text-slate-500 dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Ничего не найдено -->
                <div
                    v-else-if="
                        !useServerProcessing
                        && !displayedBundles.length
                    "
                    class="py-10 text-center
                           text-slate-500 dark:text-slate-300"
                >
                    По вашему запросу комплекты товаров не найдены.
                </div>

                <!-- Нижняя пагинация -->
                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-4"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="bundles"
                    />
                </div>
            </div>
        </div>

        <!-- Подтверждение удаления -->
        <DangerModal
            :show="showConfirmDeleteModal"
            :on-cancel="closeModal"
            :on-confirm="deleteBundle"
            :cancel-text="t('cancel')"
            :confirm-text="t('yesDelete')"
            @close="closeModal"
        >
            <template #default>
                <p class="text-sm text-slate-700 dark:text-slate-200">
                    Удалить комплект товаров
                    <strong>
                        {{ bundleToDeleteTitle }}
                    </strong>
                    ?
                </p>
            </template>
        </DangerModal>
    </AdminLayout>
</template>
