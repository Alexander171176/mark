<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список товаров маркетплейса.
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

import BulkActionSelect from '@/Components/Admin/Market/MarketProduct/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/Market/MarketProduct/Sort/SortSelect.vue'

import ProductTable from '@/Components/Admin/Market/MarketProduct/Table/ProductTable.vue'
import ProductCardGrid from '@/Components/Admin/Market/MarketProduct/View/ProductCardGrid.vue'

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

    adminMarketProductsProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    adminMarketProductsPerPage: {
        type: Number,
        default: 6,
    },

    adminMarketProductsDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    products: {
        type: [Array, Object],
        default: () => [],
    },

    productsCount: {
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

/* ======================== Product helpers ======================== */

/** Текущий перевод товара */
const getProductTranslation = (product) => product?.translation || {}

/** Название товара */
const getProductTitle = (product) => {
    return getProductTranslation(product)?.title
        || `ID: ${product?.id}`
}

/** Название товара без UI fallback — для поиска/сортировки */
const getProductSortTitle = (product) => getProductTranslation(product)?.title || ''

/** Подзаголовок товара */
const getProductSubtitle = (product) => {
    return getProductTranslation(product)?.subtitle || ''
}

/** Краткое описание товара */
const getProductShort = (product) => {
    return getProductTranslation(product)?.short || ''
}

/** Полное описание товара */
const getProductDescription = (product) => {
    return getProductTranslation(product)?.description || ''
}

/** Название компании */
const getCompanyTitle = (product) => product?.company?.translation?.title || ''

/** Название магазина */
const getShopTitle = (product) => product?.shop?.translation?.title || ''

/** Название бренда */
const getBrandTitle = (product) => product?.brand?.translation?.title || ''

/** Имя владельца */
const getOwnerName = (product) => {
    return product?.owner?.name || ''
}

/** Email владельца */
const getOwnerEmail = (product) => {
    return product?.owner?.email || ''
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

/** Стабильная сортировка по ID DESC */
const byIdDesc = (a, b) => safeNumber(b?.id) - safeNumber(a?.id)

/* ======================== View mode ======================== */

/** Режим отображения */
const viewMode = ref(
    localStorage.getItem('admin_view_mode_market_products')
    || 'cards'
)

/** Сохранение режима отображения */
watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_market_products',
        value
    )
})

/* ======================== Settings ======================== */

/** Количество элементов на странице */
const itemsPerPage = ref(
    props.adminMarketProductsPerPage || 6
)

/** Сохранение количества элементов */
watch(itemsPerPage, (newValue, oldValue) => {
    if (newValue === oldValue) {
        return
    }

    router.put(
        route('admin.settings.updateAdminCountMarketProducts'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newValue} товаров на странице.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления количества товаров.'
                )
            },
        }
    )
})

/** Текущая сортировка */
const sortParam = ref(
    props.sortParam
    || props.adminMarketProductsDefaultSort
    || 'idDesc'
)

/** Сохранение сортировки */
watch(sortParam, (newValue, oldValue) => {
    if (newValue === oldValue) {
        return
    }

    router.put(
        route('admin.settings.updateAdminSortMarketProducts'),
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
                    'Сортировка товаров успешно изменена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления сортировки товаров.'
                )
            },
        }
    )
})

/* ======================== Product collection ======================== */

/** Нормализованный список товаров */
const productsList = computed(() => {
    if (Array.isArray(props.products)) {
        return props.products
    }

    if (Array.isArray(props.products?.data)) {
        return props.products.data
    }

    if (Array.isArray(props.products?.data?.data)) {
        return props.products.data.data
    }

    if (Array.isArray(props.products?.resource)) {
        return props.products.resource
    }

    return []
})

/** Локальная копия товаров */
const localProducts = ref([])

/** Синхронизация props с локальной копией */
watch(
    productsList,
    (newValue) => {
        localProducts.value = JSON.parse(
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

const productToDeleteId = ref(null)
const productToDeleteTitle = ref('')

/** Открытие подтверждения удаления */
const confirmDelete = (productOrId, title = null) => {
    if (typeof productOrId === 'object') {
        productToDeleteId.value = productOrId?.id
        productToDeleteTitle.value = title
            || getProductTitle(productOrId)
    } else {
        productToDeleteId.value = productOrId
        productToDeleteTitle.value = title
            || `ID: ${productOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрытие подтверждения */
const closeModal = () => {
    showConfirmDeleteModal.value = false

    productToDeleteId.value = null
    productToDeleteTitle.value = ''
}

/** Удаление одного товара */
const deleteProduct = () => {
    if (productToDeleteId.value === null) {
        return
    }

    const id = productToDeleteId.value
    const title = productToDeleteTitle.value

    router.delete(
        route(
            'admin.marketProducts.destroy',
            {
                marketProduct: id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Товар "${title || `ID: ${id}`}" удалён.`
                )
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                const message = errors?.general
                    || errors?.[firstKey]
                    || 'Ошибка при удалении товара.'

                toast.error(
                    `${message} (Товар: ${title || `ID: ${id}`})`
                )
            },

            onFinish: closeModal,
        }
    )
}

/* ======================== Local patch ======================== */

/** Локальное обновление товара */
const patchLocalProduct = (productId, callback) => {
    const index = localProducts.value.findIndex(
        (product) => Number(product.id) === Number(productId)
    )

    if (index === -1) {
        return
    }

    callback(localProducts.value[index])
}

/* ======================== Single actions ======================== */

/** Переключение активности */
const toggleActivity = (product) => {
    if (!product?.id) {
        return
    }

    const activity = !product.activity
    const title = getProductTitle(product)

    router.put(
        route(
            'admin.actions.marketProducts.updateActivity',
            {
                marketProduct: product.id,
            }
        ),
        {
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalProduct(product.id, (item) => {
                    item.activity = activity
                    item.is_active = activity
                })

                toast.success(
                    activity
                        ? `Товар "${title}" активирован.`
                        : `Товар "${title}" деактивирован.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности товара "${title}".`
                )
            },
        }
    )
}

/** Переключение left */
const toggleLeft = (product) => {
    if (!product?.id) {
        return
    }

    const left = !product.left
    const title = getProductTitle(product)

    router.put(
        route(
            'admin.actions.marketProducts.updateLeft',
            {
                marketProduct: product.id,
            }
        ),
        {
            left,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalProduct(product.id, (item) => {
                    item.left = left
                })

                toast.success(
                    `Позиция left товара "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.left
                    || errors?.general
                    || `Ошибка изменения left товара "${title}".`
                )
            },
        }
    )
}

/** Переключение main */
const toggleMain = (product) => {
    if (!product?.id) {
        return
    }

    const main = !product.main
    const title = getProductTitle(product)

    router.put(
        route(
            'admin.actions.marketProducts.updateMain',
            {
                marketProduct: product.id,
            }
        ),
        {
            main,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalProduct(product.id, (item) => {
                    item.main = main
                })

                toast.success(
                    `Позиция main товара "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.main
                    || errors?.general
                    || `Ошибка изменения main товара "${title}".`
                )
            },
        }
    )
}

/** Переключение right */
const toggleRight = (product) => {
    if (!product?.id) {
        return
    }

    const right = !product.right
    const title = getProductTitle(product)

    router.put(
        route(
            'admin.actions.marketProducts.updateRight',
            {
                marketProduct: product.id,
            }
        ),
        {
            right,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLocalProduct(product.id, (item) => {
                    item.right = right
                })

                toast.success(
                    `Позиция right товара "${title}" обновлена.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.right
                    || errors?.general
                    || `Ошибка изменения right товара "${title}".`
                )
            },
        }
    )
}

/** Модерация товара */
const approveProduct = (
    product,
    status = 1,
    note = ''
) => {
    if (!product?.id || !isAdmin.value) {
        return
    }

    router.put(
        route(
            'admin.actions.marketProducts.approve',
            {
                marketProduct: product.id,
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
                patchLocalProduct(product.id, (item) => {
                    item.moderation_status = status
                    item.is_pending = status === 0
                    item.is_approved = status === 1
                    item.is_rejected = status === 2
                    item.moderation_note = note
                })

                toast.success(
                    status === 1
                        ? 'Товар одобрен.'
                        : 'Товар отклонён.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.moderation_status
                    || errors?.general
                    || 'Ошибка модерации товара.'
                )
            },
        }
    )
}

/* ======================== Frontend filtering ======================== */

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

/**
 * Проверка совпадения товара с поиском.
 * Полностью повторяет MarketProduct::scopeSearch().
 */
const productMatchesSearch = (product, query) => {
    if (!query) return true

    const searchValues = [
        product?.url,
        product?.sku,
        product?.vendor_code,
        product?.barcode,
        product?.status,
        product?.moderation_note,
        getProductTranslation(product)?.title,
        getProductSubtitle(product),
        getProductShort(product),
        getProductDescription(product),
        getCompanyTitle(product),
        getShopTitle(product),
        getBrandTitle(product),
        getOwnerName(product),
        getOwnerEmail(product),
    ]

    return searchValues.some((value) => normalize(value).includes(query))
}

/** Отфильтрованные товары */
const filteredProducts = computed(() => {
    if (props.useServerProcessing) {
        return localProducts.value
    }

    const query = normalize(searchQuery.value)

    return localProducts.value.filter(
        (product) => productMatchesSearch(product, query)
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

/** Сортировка товаров */
/** Сортировка чисел по возрастанию */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка чисел по убыванию */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк по возрастанию */
const byStringAsc = (field) => (a, b) =>
    compareText(a?.[field], b?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк по убыванию */
const byStringDesc = (field) => (a, b) =>
    compareText(b?.[field], a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка дат по возрастанию */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка дат по убыванию */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field]) || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка товаров */
const sortedProducts = computed(() => {
    let list = [...filteredProducts.value]

    /** Фильтры должны повторять Model: после фильтрации всегда ID DESC */
    const filteredByIdDesc = (predicate) => list.filter(predicate).sort(byIdDesc)

    if (sortParam.value === 'activity') return filteredByIdDesc((product) => Boolean(product.activity))
    if (sortParam.value === 'inactive') return filteredByIdDesc((product) => !product.activity)

    if (sortParam.value === 'inStock') return filteredByIdDesc((product) => Boolean(product.in_stock))
    if (sortParam.value === 'outOfStock') return filteredByIdDesc((product) => !product.in_stock)

    if (sortParam.value === 'left') return filteredByIdDesc((product) => Boolean(product.left))
    if (sortParam.value === 'noLeft') return filteredByIdDesc((product) => !product.left)
    if (sortParam.value === 'main') return filteredByIdDesc((product) => Boolean(product.main))
    if (sortParam.value === 'noMain') return filteredByIdDesc((product) => !product.main)
    if (sortParam.value === 'right') return filteredByIdDesc((product) => Boolean(product.right))
    if (sortParam.value === 'noRight') return filteredByIdDesc((product) => !product.right)

    if (sortParam.value === 'new') return filteredByIdDesc((product) => Boolean(product.is_new))
    if (sortParam.value === 'notNew') return filteredByIdDesc((product) => !product.is_new)
    if (sortParam.value === 'hit') return filteredByIdDesc((product) => Boolean(product.is_hit))
    if (sortParam.value === 'notHit') return filteredByIdDesc((product) => !product.is_hit)
    if (sortParam.value === 'sale') return filteredByIdDesc((product) => Boolean(product.is_sale))
    if (sortParam.value === 'notSale') return filteredByIdDesc((product) => !product.is_sale)

    if (sortParam.value === 'hasVariants') {
        return filteredByIdDesc((product) => safeNumber(product.variants_count) > 0)
    }

    if (sortParam.value === 'withoutVariants') {
        return filteredByIdDesc((product) => safeNumber(product.variants_count) === 0)
    }

    if (sortParam.value === 'statusDraft') return filteredByIdDesc((product) => product.status === 'draft')
    if (sortParam.value === 'statusPublished') return filteredByIdDesc((product) => product.status === 'published')
    if (sortParam.value === 'statusArchived') return filteredByIdDesc((product) => product.status === 'archived')

    if (sortParam.value === 'moderationPending') {
        return filteredByIdDesc((product) => moderationNum(product.moderation_status) === 0)
    }

    if (sortParam.value === 'moderationApproved') {
        return filteredByIdDesc((product) => moderationNum(product.moderation_status) === 1)
    }

    if (sortParam.value === 'moderationRejected') {
        return filteredByIdDesc((product) => moderationNum(product.moderation_status) === 2)
    }

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            compareText(getProductSortTitle(a), getProductSortTitle(b)) ||
            safeNumber(b?.id) - safeNumber(a?.id),
        titleDesc: (a, b) =>
            compareText(getProductSortTitle(b), getProductSortTitle(a)) ||
            safeNumber(b?.id) - safeNumber(a?.id),

        urlAsc: byStringAsc('url'),
        urlDesc: byStringDesc('url'),

        skuAsc: byStringAsc('sku'),
        skuDesc: byStringDesc('sku'),

        vendorCodeAsc: byStringAsc('vendor_code'),
        vendorCodeDesc: byStringDesc('vendor_code'),

        barcodeAsc: byStringAsc('barcode'),
        barcodeDesc: byStringDesc('barcode'),

        priceAsc: byNumberAsc('price'),
        priceDesc: byNumberDesc('price'),

        oldPriceAsc: byNumberAsc('old_price'),
        oldPriceDesc: byNumberDesc('old_price'),

        purchasePriceAsc: byNumberAsc('purchase_price'),
        purchasePriceDesc: byNumberDesc('purchase_price'),

        wholesalePriceAsc: byNumberAsc('wholesale_price'),
        wholesalePriceDesc: byNumberDesc('wholesale_price'),

        quantityAsc: byNumberAsc('quantity'),
        quantityDesc: byNumberDesc('quantity'),

        inStockAsc: byNumberAsc('in_stock'),
        inStockDesc: byNumberDesc('in_stock'),

        weightAsc: byNumberAsc('weight'),
        weightDesc: byNumberDesc('weight'),

        lengthAsc: byNumberAsc('length'),
        lengthDesc: byNumberDesc('length'),

        widthAsc: byNumberAsc('width'),
        widthDesc: byNumberDesc('width'),

        heightAsc: byNumberAsc('height'),
        heightDesc: byNumberDesc('height'),

        companyAsc: (a, b) =>
            compareText(getCompanyTitle(a), getCompanyTitle(b)) ||
            safeNumber(b?.id) - safeNumber(a?.id),
        companyDesc: (a, b) =>
            compareText(getCompanyTitle(b), getCompanyTitle(a)) ||
            safeNumber(b?.id) - safeNumber(a?.id),

        shopAsc: (a, b) =>
            compareText(getShopTitle(a), getShopTitle(b)) || safeNumber(b?.id) - safeNumber(a?.id),
        shopDesc: (a, b) =>
            compareText(getShopTitle(b), getShopTitle(a)) || safeNumber(b?.id) - safeNumber(a?.id),

        brandAsc: (a, b) =>
            compareText(getBrandTitle(a), getBrandTitle(b)) || safeNumber(b?.id) - safeNumber(a?.id),
        brandDesc: (a, b) =>
            compareText(getBrandTitle(b), getBrandTitle(a)) || safeNumber(b?.id) - safeNumber(a?.id),

        ownerNameAsc: (a, b) =>
            compareText(getOwnerName(a), getOwnerName(b)) || safeNumber(b?.id) - safeNumber(a?.id),
        ownerNameDesc: (a, b) =>
            compareText(getOwnerName(b), getOwnerName(a)) || safeNumber(b?.id) - safeNumber(a?.id),

        ownerEmailAsc: (a, b) =>
            compareText(getOwnerEmail(a), getOwnerEmail(b)) || safeNumber(b?.id) - safeNumber(a?.id),
        ownerEmailDesc: (a, b) =>
            compareText(getOwnerEmail(b), getOwnerEmail(a)) || safeNumber(b?.id) - safeNumber(a?.id),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        likesAsc: byNumberAsc('likes_count'),
        likesDesc: byNumberDesc('likes_count'),

        ratingAsc: byNumberAsc('rating_avg'),
        ratingDesc: byNumberDesc('rating_avg'),

        ratingCountAsc: byNumberAsc('rating_count'),
        ratingCountDesc: byNumberDesc('rating_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        categoriesAsc: byNumberAsc('categories_count'),
        categoriesDesc: byNumberDesc('categories_count'),

        tagsAsc: byNumberAsc('tags_count'),
        tagsDesc: byNumberDesc('tags_count'),

        attributesAsc: byNumberAsc('attribute_values_count'),
        attributesDesc: byNumberDesc('attribute_values_count'),

        variantsAsc: byNumberAsc('variants_count'),
        variantsDesc: byNumberDesc('variants_count'),

        reviewsAsc: byNumberAsc('reviews_count'),
        reviewsDesc: byNumberDesc('reviews_count'),

        relatedProductsAsc: byNumberAsc('related_products_count'),
        relatedProductsDesc: byNumberDesc('related_products_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        leftAsc: byNumberAsc('left'),
        leftDesc: byNumberDesc('left'),

        mainAsc: byNumberAsc('main'),
        mainDesc: byNumberDesc('main'),

        rightAsc: byNumberAsc('right'),
        rightDesc: byNumberDesc('right'),

        newAsc: (a, b) =>
            Number(Boolean(a?.is_new)) - Number(Boolean(b?.is_new)) ||
            safeNumber(b?.id) - safeNumber(a?.id),
        newDesc: (a, b) =>
            Number(Boolean(b?.is_new)) - Number(Boolean(a?.is_new)) ||
            safeNumber(b?.id) - safeNumber(a?.id),

        hitAsc: (a, b) =>
            Number(Boolean(a?.is_hit)) - Number(Boolean(b?.is_hit)) ||
            safeNumber(b?.id) - safeNumber(a?.id),
        hitDesc: (a, b) =>
            Number(Boolean(b?.is_hit)) - Number(Boolean(a?.is_hit)) ||
            safeNumber(b?.id) - safeNumber(a?.id),

        saleAsc: (a, b) =>
            Number(Boolean(a?.is_sale)) - Number(Boolean(b?.is_sale)) ||
            safeNumber(b?.id) - safeNumber(a?.id),
        saleDesc: (a, b) =>
            Number(Boolean(b?.is_sale)) - Number(Boolean(a?.is_sale)) ||
            safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        moderationStatusAsc: byNumberAsc('moderation_status'),
        moderationStatusDesc: byNumberDesc('moderation_status'),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        showFromAtAsc: byDateAsc('show_from_at'),
        showFromAtDesc: byDateDesc('show_from_at'),

        showToAtAsc: byDateAsc('show_to_at'),
        showToAtDesc: byDateDesc('show_to_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        dateAsc: byDateAsc('created_at'),
        dateDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    const sorter = sortMap[sortParam.value] || sortMap.idDesc

    return list.sort(sorter)
})


/* ======================== Frontend pagination ======================== */

/** Локально отображаемые товары */
const frontendPaginatedProducts = computed(() => {
    const start = (
        currentPage.value - 1
    ) * itemsPerPage.value

    return sortedProducts.value.slice(
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
const displayedProducts = computed(() => {
    return props.useServerProcessing
        ? localProducts.value
        : frontendPaginatedProducts.value
})

/* ======================== Selection ======================== */

/** Выбранные товары */
const selectedProducts = ref([])

/** Выбран ли товар */
const isSelected = (productId) => {
    return selectedProducts.value.includes(productId)
}

/** Выбор одного товара */
const toggleSelectProduct = (productOrId) => {
    const id = typeof productOrId === 'object'
        ? productOrId?.id
        : productOrId

    if (!id) {
        return
    }

    if (isSelected(id)) {
        selectedProducts.value = selectedProducts.value.filter(
            (selectedId) => selectedId !== id
        )

        return
    }

    selectedProducts.value.push(id)
}

/** Выбрать или снять все видимые товары */
const toggleAll = (eventOrValue) => {
    const checked = typeof eventOrValue === 'boolean'
        ? eventOrValue
        : Boolean(eventOrValue?.target?.checked ?? eventOrValue?.checked)

    if (!checked) {
        selectedProducts.value = []
        return
    }

    selectedProducts.value = displayedProducts.value
        .map((product) => product?.id)
        .filter(Boolean)
}

/** Все видимые товары выбраны */
const allDisplayedSelected = computed(() => {
    const ids = displayedProducts.value
        .map((product) => product?.id)
        .filter(Boolean)

    return ids.length > 0
        && ids.every((id) => selectedProducts.value.includes(id))
})

/* ======================== Bulk actions ======================== */

/** Массовое изменение активности */
const bulkToggleActivity = (activity) => {
    if (!selectedProducts.value.length) {
        toast.warning(
            'Выберите хотя бы один товар.'
        )

        return
    }

    const ids = [...selectedProducts.value]

    router.put(
        route(
            'admin.actions.marketProducts.bulkUpdateActivity'
        ),
        {
            ids,
            activity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                localProducts.value.forEach((product) => {
                    if (ids.includes(product.id)) {
                        product.activity = activity
                        product.is_active = activity
                    }
                })

                selectedProducts.value = []

                toast.success(
                    'Активность выбранных товаров обновлена.'
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

/** Массовое обновление boolean-флага товаров. */
const bulkToggleFlag = (field, value, routeName, message) => {
    if (!selectedProducts.value.length) {
        toast.warning('Выберите товары для массового действия.')
        return
    }

    const ids = [...selectedProducts.value]

    router.put(route(routeName), {
        ids,
        [field]: value,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            ids.forEach((id) => {
                patchLocalProduct(id, (product) => {
                    product[field] = value
                })
            })

            selectedProducts.value = []
            toast.success(message)
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления товаров.')
        },
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedProducts.value.length) {
        toast.warning(
            'Выберите хотя бы один товар для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные товары?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.marketProducts.bulkDestroy'
        ),
        {
            data: {
                ids: selectedProducts.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedProducts.value = []

                toast.success(
                    'Выбранные товары успешно удалены.'
                )
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Ошибка массового удаления товаров.'
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
            'admin.actions.marketProducts.bulkUpdateIsNew',
            'Товары добавлены в новинки.')
    } else if (action === 'isNewOff') {
        bulkToggleFlag('is_new', false,
            'admin.actions.marketProducts.bulkUpdateIsNew',
            'Товары убраны из новинок.')
    } else if (action === 'isHitOn') {
        bulkToggleFlag('is_hit', true,
            'admin.actions.marketProducts.bulkUpdateIsHit',
            'Товары добавлены в рекомендуемые.')
    } else if (action === 'isHitOff') {
        bulkToggleFlag('is_hit', false,
            'admin.actions.marketProducts.bulkUpdateIsHit',
            'Товары убраны из рекомендуемых.')
    } else if (action === 'isSaleOn') {
        bulkToggleFlag('is_sale', true,
            'admin.actions.marketProducts.bulkUpdateIsSale',
            'Товары добавлены в распродажу.')
    } else if (action === 'isSaleOff') {
        bulkToggleFlag('is_sale', false,
            'admin.actions.marketProducts.bulkUpdateIsSale',
            'Товары убраны из распродажи.')
    } else if (action === 'left') {
        bulkToggleFlag(
            'left',
            true,
            'admin.actions.marketProducts.bulkUpdateLeft',
            'Товары добавлены в левую колонку.'
        )
    } else if (action === 'noLeft') {
        bulkToggleFlag(
            'left',
            false,
            'admin.actions.marketProducts.bulkUpdateLeft',
            'Товары убраны из левой колонки.'
        )
    } else if (action === 'main') {
        bulkToggleFlag(
            'main',
            true,
            'admin.actions.marketProducts.bulkUpdateMain',
            'Товары добавлены в главный блок.'
        )
    } else if (action === 'noMain') {
        bulkToggleFlag(
            'main',
            false,
            'admin.actions.marketProducts.bulkUpdateMain',
            'Товары убраны из главного блока.'
        )
    } else if (action === 'right') {
        bulkToggleFlag(
            'right',
            true,
            'admin.actions.marketProducts.bulkUpdateRight',
            'Товары добавлены в правую колонку.'
        )
    } else if (action === 'noRight') {
        bulkToggleFlag(
            'right',
            false,
            'admin.actions.marketProducts.bulkUpdateRight',
            'Товары убраны из правой колонки.'
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
            'admin.actions.marketProducts.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                items.forEach((item) => {
                    patchLocalProduct(item.id, (product) => {
                        product.sort = item.sort
                    })
                })

                toast.success(
                    'Сортировка товаров обновлена.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка сортировки товаров:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.items
                    || 'Ошибка обновления сортировки товаров.'
                )
            },
        }
    )
}

/* ======================== Флаги: новинки, хиты, распродажа ======================== */

/** Универсальное переключение маркетингового флага товара. */
const toggleProductFlag = (product, field, routeName, enabledMessage, disabledMessage) => {
    if (!product?.id) {
        return
    }

    const newValue = !product[field]
    const title = getProductTitle(product)

    router.put(route(routeName, { marketProduct: product.id }), {
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchLocalProduct(product.id, (item) => {
                item[field] = newValue
            })

            product[field] = newValue
            toast.success(newValue ? `${enabledMessage} "${title}".` : `${disabledMessage} "${title}".`)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || `Ошибка изменения флага товара "${title}".`)
        },
    })
}

/** Переключение флага "Новинка". */
const toggleIsNew = (product) => {
    toggleProductFlag(
        product,
        'is_new',
        'admin.actions.marketProducts.updateIsNew',
        'Товар добавлен в новинки',
        'Товар убран из новинок'
    )
}

/** Переключение флага "Рекомендуемый / хит". */
const toggleIsHit = (product) => {
    toggleProductFlag(
        product,
        'is_hit',
        'admin.actions.marketProducts.updateIsHit',
        'Товар добавлен в рекомендуемые',
        'Товар убран из рекомендуемых'
    )
}

/** Переключение флага "Распродажа". */
const toggleIsSale = (product) => {
    toggleProductFlag(
        product,
        'is_sale',
        'admin.actions.marketProducts.updateIsSale',
        'Товар добавлен в распродажу',
        'Товар убран из распродажи'
    )
}
</script>

<template>
    <AdminLayout :title="t('marketProducts')">
        <template #header>
            <TitlePage>
                {{ t('marketProducts') }}
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
                        :href="route('admin.marketProducts.create')"
                    >
                        {{ t('addMarketProduct') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminMarketProductsProcessingMode"
                        :mode="adminMarketProductsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="productsCount"
                    />
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="productsCount && !useServerProcessing"
                    v-model="searchQuery"
                />

                <ServerSearchInput
                    v-if="productsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Пагинация и сортировка -->
                <div
                    v-if="productsCount"
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
                        update-route="admin.settings.updateAdminCountMarketProducts"
                    />

                    <SortSelect :sortParam="sortParam" @update:sortParam="sortParam = $event" />
                </div>

                <!-- Счётчик, массовые действия, вид -->
                <div
                    v-if="productsCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between
                           gap-3"
                >
                    <CountTable>
                        {{ productsCount }}
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
                    v-if="productsCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredProducts.length"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="products"
                    />
                </div>

                <!-- Таблица -->
                <ProductTable
                    v-if="viewMode === 'table'"
                    :products="displayedProducts"
                    :selected-products="selectedProducts"
                    :all-selected="allDisplayedSelected"
                    :is-admin="isAdmin"
                    @toggle-select="toggleSelectProduct"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-is-new="toggleIsNew"
                    @toggle-is-hit="toggleIsHit"
                    @toggle-is-sale="toggleIsSale"
                    @approve="approveProduct"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <!-- Карточки -->
                <ProductCardGrid
                    v-else
                    :products="displayedProducts"
                    :selected-products="selectedProducts"
                    :is-admin="isAdmin"
                    @toggle-select="toggleSelectProduct"
                    @toggle-all="toggleAll"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @toggle-is-new="toggleIsNew"
                    @toggle-is-hit="toggleIsHit"
                    @toggle-is-sale="toggleIsSale"
                    @approve="approveProduct"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                />

                <!-- Пустой список -->
                <div
                    v-if="!productsCount"
                    class="py-12 text-center
                           text-slate-500 dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Ничего не найдено -->
                <div
                    v-else-if="
                        !useServerProcessing
                        && !displayedProducts.length
                    "
                    class="py-10 text-center
                           text-slate-500 dark:text-slate-300"
                >
                    {{ t('noData') }}
                </div>

                <!-- Нижняя пагинация -->
                <div
                    v-if="productsCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-4"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredProducts.length"
                        @update:currentPage="
                            currentPage = $event
                        "
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="products"
                    />
                </div>
            </div>
        </div>

        <!-- Подтверждение удаления -->
        <DangerModal
            :show="showConfirmDeleteModal"
            :on-cancel="closeModal"
            :on-confirm="deleteProduct"
            :cancel-text="t('cancel')"
            :confirm-text="t('yesDelete')"
            @close="closeModal"
        >
            <template #default>
                <p class="text-sm text-slate-700 dark:text-slate-200">
                    {{ t('delete') }}
                    <strong>
                        {{ productToDeleteTitle }}
                    </strong>
                    ?
                </p>
            </template>
        </DangerModal>
    </AdminLayout>
</template>
