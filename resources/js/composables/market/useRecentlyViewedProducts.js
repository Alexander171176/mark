import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

/**
 * Ключ гостевой истории в localStorage.
 */
const STORAGE_KEY = 'market_recently_viewed_products'

/**
 * Максимальное количество ID,
 * которое храним у гостя.
 */
const STORAGE_LIMIT = 50

/**
 * Работа с недавно просмотренными товарами.
 *
 * Гость:
 * - история ID хранится в localStorage;
 * - актуальные карточки получаем через backend.
 *
 * Авторизованный пользователь:
 * - история хранится в БД;
 * - frontend получает её через backend;
 * - гостевая история может быть перенесена в БД.
 */
export const useRecentlyViewedProducts = () => {
    const page = usePage()

    /** Полученные карточки товаров */
    const products = ref([])

    /** Состояние загрузки */
    const loading = ref(false)

    /** Ошибка */
    const error = ref(null)

    /**
     * Авторизован ли пользователь.
     *
     * Поддерживаем несколько возможных
     * вариантов структуры Inertia props.
     */
    const isAuthenticated = () => {
        return Boolean(
            page.props?.auth?.user
            || page.props?.user
        )
    }

    /* ===================== LOCAL STORAGE ===================== */

    /**
     * Получить историю ID гостя.
     */
    const getGuestIds = () => {
        if (typeof window === 'undefined') {
            return []
        }

        try {
            const stored = window.localStorage.getItem(STORAGE_KEY)

            if (!stored) {
                return []
            }

            const parsed = JSON.parse(stored)

            if (!Array.isArray(parsed)) {
                return []
            }

            return parsed
                .map((id) => Number(id))
                .filter((id) => Number.isInteger(id) && id > 0)
                .filter((id, index, array) => {
                    return array.indexOf(id) === index
                })
                .slice(0, STORAGE_LIMIT)
        } catch {
            return []
        }
    }

    /**
     * Сохранить ID гостевой истории.
     */
    const saveGuestIds = (ids) => {
        if (typeof window === 'undefined') {
            return
        }

        const normalized = ids
            .map((id) => Number(id))
            .filter((id) => Number.isInteger(id) && id > 0)
            .filter((id, index, array) => {
                return array.indexOf(id) === index
            })
            .slice(0, STORAGE_LIMIT)

        window.localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(normalized)
        )
    }

    /**
     * Запомнить просмотр товара гостем.
     *
     * Текущий товар переносится
     * в начало истории.
     */
    const rememberGuestProduct = (productId) => {
        if (isAuthenticated()) {
            return
        }

        const id = Number(productId)

        if (!Number.isInteger(id) || id <= 0) {
            return
        }

        const ids = getGuestIds()
            .filter((storedId) => storedId !== id)

        ids.unshift(id)

        saveGuestIds(ids)
    }

    /**
     * Очистить localStorage гостя.
     */
    const clearGuestHistory = () => {
        if (typeof window === 'undefined') {
            return
        }

        window.localStorage.removeItem(STORAGE_KEY)
    }

    /* ===================== HELPERS ===================== */

    /**
     * Исключить конкретный товар
     * из отображаемой истории.
     *
     * Используется прежде всего на Show.vue,
     * чтобы текущий товар не показывался сам у себя.
     */
    const excludeProduct = (
        items,
        excludeProductId = null
    ) => {
        const list = Array.isArray(items)
            ? items
            : []

        const excludedId = Number(excludeProductId)

        if (
            !Number.isInteger(excludedId)
            || excludedId <= 0
        ) {
            return list
        }

        return list.filter(
            (product) => Number(product?.id) !== excludedId
        )
    }

    /* ===================== LOAD ===================== */

    /**
     * Загрузить недавно просмотренные товары.
     *
     * Для гостя отправляем ID из localStorage.
     *
     * Для авторизованного пользователя backend
     * самостоятельно получает историю из БД.
     *
     * excludeProductId нужен прежде всего
     * для Show.vue.
     */
    const load = async (excludeProductId = null) => {
        loading.value = true
        error.value = null

        try {
            let ids = []

            /**
             * Только гостю нужно передавать
             * содержимое localStorage.
             */
            if (!isAuthenticated()) {
                ids = getGuestIds()

                if (excludeProductId) {
                    const excludedId = Number(
                        excludeProductId
                    )

                    ids = ids.filter(
                        (id) => id !== excludedId
                    )
                }
            }

            const response = await axios.post(
                route('public.marketProducts.recentlyViewed'),
                {
                    ids,
                }
            )

            const responseProducts = Array.isArray(
                response.data?.products
            )
                ? response.data.products
                : []

            products.value = excludeProduct(
                responseProducts,
                excludeProductId
            )

            return products.value
        } catch (exception) {
            console.error(
                'Ошибка загрузки недавно просмотренных товаров:',
                exception
            )

            error.value = exception
            products.value = []

            return []
        } finally {
            loading.value = false
        }
    }

    /* ===================== MERGE ===================== */

    /**
     * Перенести гостевую историю в БД
     * после авторизации.
     *
     * После успешного merge localStorage очищается.
     *
     * excludeProductId позволяет на Show.vue
     * исключить текущий товар из carousel
     * после получения объединённой истории.
     */
    const mergeGuestHistory = async (
        excludeProductId = null
    ) => {
        if (!isAuthenticated()) {
            return []
        }

        const ids = getGuestIds()

        /**
         * Если гостевой истории нет,
         * оставляем уже полученные от Inertia данные.
         */
        if (!ids.length) {
            products.value = excludeProduct(
                products.value,
                excludeProductId
            )

            return products.value
        }

        loading.value = true
        error.value = null

        try {
            const response = await axios.post(
                route(
                    'public.marketProducts.recentlyViewed.merge'
                ),
                {
                    ids,
                }
            )

            const responseProducts = Array.isArray(
                response.data?.products
            )
                ? response.data.products
                : []

            /**
             * На странице конкретного товара
             * убираем текущий товар из carousel.
             *
             * При этом из БД он НЕ удаляется —
             * это только фильтрация отображения.
             */
            products.value = excludeProduct(
                responseProducts,
                excludeProductId
            )

            /**
             * Очищаем localStorage только после
             * успешной серверной синхронизации.
             */
            clearGuestHistory()

            return products.value
        } catch (exception) {
            console.error(
                'Ошибка объединения истории просмотренных товаров:',
                exception
            )

            error.value = exception

            return products.value
        } finally {
            loading.value = false
        }
    }

    /* ===================== CLEAR ===================== */

    /**
     * Полностью очистить историю.
     *
     * Гость:
     * - очищается localStorage.
     *
     * Авторизованный:
     * - очищается история в БД.
     */
    const clear = async () => {
        error.value = null

        /**
         * Гость.
         */
        if (!isAuthenticated()) {
            clearGuestHistory()
            products.value = []

            return true
        }

        /**
         * Авторизованный пользователь.
         */
        loading.value = true

        try {
            await axios.delete(
                route(
                    'public.marketProducts.recentlyViewed.clear'
                )
            )

            products.value = []

            /**
             * Очищаем и гостевой storage,
             * если после авторизации там
             * по какой-либо причине что-то осталось.
             */
            clearGuestHistory()

            return true
        } catch (exception) {
            console.error(
                'Ошибка очистки просмотренных товаров:',
                exception
            )

            error.value = exception

            return false
        } finally {
            loading.value = false
        }
    }

    /* ===================== INITIAL DATA ===================== */

    /**
     * Положить в composable данные,
     * уже полученные от Laravel через Inertia.
     *
     * Благодаря этому авторизованному пользователю
     * не требуется сразу выполнять повторный API-запрос.
     */
    const setProducts = (items) => {
        products.value = Array.isArray(items)
            ? items
            : []
    }

    return {
        /** State */
        products,
        loading,
        error,

        /** Auth */
        isAuthenticated,

        /** Guest storage */
        getGuestIds,
        rememberGuestProduct,
        clearGuestHistory,

        /** API */
        load,
        mergeGuestHistory,
        clear,

        /** Initial data */
        setProducts,
    }
}
