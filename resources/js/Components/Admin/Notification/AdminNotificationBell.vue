<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'

/**
 * Состояние панели уведомлений.
 */
const isOpen = ref(false)
const loading = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
const notificationBell = ref(null)

/**
 * Блокировка повторного перехода
 * по одному уведомлению.
 */
const openingNotificationId = ref(null)

/**
 * Есть ли непрочитанные уведомления.
 */
const hasUnread = computed(() => unreadCount.value > 0)

/**
 * Текст счётчика.
 *
 * Большие значения сокращаем,
 * чтобы badge не растягивал футер.
 */
const unreadCountLabel = computed(() => {
    if (unreadCount.value > 99) {
        return '99+'
    }

    return String(unreadCount.value)
})

/**
 * Получить количество собственных
 * непрочитанных уведомлений.
 */
const loadUnreadCount = async () => {
    try {
        const response = await axios.get(
            '/admin/notifications/unread-count'
        )

        unreadCount.value = Number(
            response.data?.count ?? 0
        )
    } catch (error) {
        console.error(
            'Ошибка получения количества непрочитанных уведомлений:',
            error
        )
    }
}

/**
 * Получить последние собственные уведомления.
 */
const loadRecentNotifications = async () => {
    loading.value = true

    try {
        const response = await axios.get(
            '/admin/notifications/recent',
            {
                params: {
                    limit: 5
                }
            }
        )

        notifications.value = Array.isArray(response.data?.data)
            ? response.data.data
            : []
    } catch (error) {
        console.error(
            'Ошибка получения последних уведомлений:',
            error
        )

        notifications.value = []
    } finally {
        loading.value = false
    }
}

/**
 * Обновить данные колокольчика.
 */
const refreshNotifications = async () => {
    await Promise.all([
        loadUnreadCount(),
        loadRecentNotifications()
    ])
}

/**
 * Открыть или закрыть панель уведомлений.
 */
const toggleNotifications = async () => {
    isOpen.value = !isOpen.value

    if (isOpen.value) {
        await refreshNotifications()
    }
}

/**
 * Закрыть панель уведомлений.
 */
const closeNotifications = () => {
    isOpen.value = false
}

/**
 * Закрыть панель при клике
 * за пределами компонента.
 */
const handleClickOutside = (event) => {
    if (
        notificationBell.value
        && !notificationBell.value.contains(event.target)
    ) {
        closeNotifications()
    }
}

/**
 * Закрыть панель по Escape.
 */
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        closeNotifications()
    }
}

/**
 * Отметить собственное уведомление
 * как прочитанное.
 */
const markAsRead = async (notification) => {
    if (
        !notification?.id
        || notification.is_read
        || !notification.is_own
    ) {
        return
    }

    await axios.patch(
        `/admin/notifications/${notification.id}/read`
    )

    notification.is_read = true
    notification.read_at = new Date().toISOString()

    unreadCount.value = Math.max(
        0,
        unreadCount.value - 1
    )
}

/**
 * Открыть уведомление.
 *
 * Собственное непрочитанное уведомление
 * сначала отмечается как прочитанное.
 */
const openNotification = async (notification) => {
    if (!notification) {
        return
    }

    if (openingNotificationId.value !== null) {
        return
    }

    openingNotificationId.value = notification.id

    try {
        if (
            notification.is_own
            && !notification.is_read
        ) {
            await markAsRead(notification)
        }

        closeNotifications()

        if (notification.url) {
            Inertia.visit(notification.url)
        }
    } catch (error) {
        console.error(
            'Ошибка открытия уведомления:',
            error
        )
    } finally {
        openingNotificationId.value = null
    }
}

/**
 * Перейти в центр уведомлений.
 */
const openNotificationCenter = () => {
    closeNotifications()

    Inertia.visit('/admin/notifications')
}

/**
 * Получить классы индикатора
 * уровня уведомления.
 */
const levelClasses = (level) => {
    switch (level) {
        case 'success':
            return 'bg-green-500'

        case 'warning':
            return 'bg-amber-500'

        case 'error':
            return 'bg-red-500'

        case 'info':
        default:
            return 'bg-blue-500'
    }
}

/**
 * Форматирование даты уведомления.
 */
const formatDate = (value) => {
    if (!value) {
        return ''
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return new Intl.DateTimeFormat(
        document.documentElement.lang || 'ru',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }
    ).format(date)
}

/**
 * Первичная загрузка счётчика.
 *
 * Сам список загружается только
 * при открытии панели.
 */
onMounted(() => {
    loadUnreadCount()

    document.addEventListener(
        'click',
        handleClickOutside
    )

    document.addEventListener(
        'keydown',
        handleKeydown
    )
})

onBeforeUnmount(() => {
    document.removeEventListener(
        'click',
        handleClickOutside
    )

    document.removeEventListener(
        'keydown',
        handleKeydown
    )
})
</script>

<template>
    <div
        ref="notificationBell"
        class="relative"
    >
        <!-- Кнопка колокольчика -->
        <button
            type="button"
            title="Уведомления"
            aria-label="Уведомления"
            :aria-expanded="isOpen"
            @click.stop="toggleNotifications"
            class="relative flex items-center justify-center btn px-1 py-0.5
                   text-slate-900 dark:text-slate-100
                   rounded-sm border-2 border-slate-400
                   hover:border-slate-500 dark:hover:border-slate-300"
        >
            <svg
                class="w-4 h-4 shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path
                    d="M18 8a6 6 0 0 0-12 0
                       c0 7-3 7-3 9h18
                       c0-2-3-2-3-9"
                />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>

            <!-- Счётчик непрочитанных -->
            <span
                v-if="hasUnread"
                class="absolute -top-2 -right-2 min-w-[18px] h-[18px]
                       px-1 flex items-center justify-center
                       text-[10px] leading-none font-semibold text-white
                       bg-red-500 border border-white dark:border-slate-800
                       rounded-full"
            >
                {{ unreadCountLabel }}
            </span>
        </button>

        <!-- Выпадающая вверх панель -->
        <div
            v-if="isOpen"
            class="absolute bottom-full right-0 mb-2
                   w-[320px] sm:w-[380px]
                   max-w-[calc(100vw-1.5rem)]
                   overflow-hidden
                   bg-white dark:bg-slate-800
                   border border-slate-200 dark:border-slate-600
                   rounded-md shadow-xl z-50"
            @click.stop
        >
            <!-- Заголовок -->
            <div
                class="flex items-center justify-between gap-3
                       px-3 py-2
                       bg-slate-50 dark:bg-slate-900
                       border-b border-slate-200 dark:border-slate-700"
            >
                <div class="flex items-center gap-2 min-w-0">
                    <span
                        class="font-semibold text-sm
                               text-slate-800 dark:text-slate-100"
                    >
                        Уведомления
                    </span>

                    <span
                        v-if="hasUnread"
                        class="inline-flex items-center justify-center
                               min-w-[20px] h-5 px-1.5
                               text-[10px] font-semibold text-white
                               bg-red-500 rounded-full"
                    >
                        {{ unreadCountLabel }}
                    </span>
                </div>

                <button
                    type="button"
                    title="Закрыть"
                    aria-label="Закрыть"
                    @click="closeNotifications"
                    class="shrink-0 p-1
                           text-slate-400 hover:text-slate-700
                           dark:text-slate-400 dark:hover:text-slate-100"
                >
                    <svg
                        class="w-4 h-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <!-- Загрузка -->
            <div
                v-if="loading"
                class="flex items-center justify-center
                       min-h-[120px] px-4 py-6
                       text-sm text-slate-500 dark:text-slate-400"
            >
                <svg
                    class="animate-spin w-5 h-5 mr-2"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />

                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 0 1 8-8v4
                           a4 4 0 0 0-4 4H4z"
                    />
                </svg>

                Загрузка...
            </div>

            <!-- Список уведомлений -->
            <div
                v-else-if="notifications.length"
                class="max-h-[360px] overflow-y-auto"
            >
                <button
                    v-for="notification in notifications"
                    :key="notification.id"
                    type="button"
                    @click="openNotification(notification)"
                    class="relative w-full text-left
                           px-3 py-2.5
                           border-b border-slate-100
                           dark:border-slate-700
                           hover:bg-slate-50
                           dark:hover:bg-slate-700/60
                           transition-colors"
                    :class="{
                        'bg-blue-50/60 dark:bg-blue-950/20':
                            !notification.is_read
                    }"
                >
                    <div class="flex items-start gap-2.5">
                        <!-- Индикатор уровня -->
                        <span
                            class="mt-1.5 w-2 h-2 shrink-0 rounded-full"
                            :class="levelClasses(notification.level)"
                        />

                        <div class="min-w-0 flex-1">
                            <div
                                class="flex items-start justify-between
                                       gap-2"
                            >
                                <span
                                    class="block text-sm
                                           text-slate-800 dark:text-slate-100
                                           truncate"
                                    :class="{
                                        'font-semibold':
                                            !notification.is_read,
                                        'font-medium':
                                            notification.is_read
                                    }"
                                >
                                    {{ notification.title || 'Уведомление' }}
                                </span>

                                <!-- Непрочитанное -->
                                <span
                                    v-if="!notification.is_read"
                                    class="mt-1 w-2 h-2 shrink-0
                                           bg-blue-500 rounded-full"
                                    title="Непрочитанное"
                                />
                            </div>

                            <p
                                v-if="notification.message"
                                class="mt-0.5 text-xs
                                       text-slate-600 dark:text-slate-300
                                       line-clamp-2"
                            >
                                {{ notification.message }}
                            </p>

                            <div
                                class="mt-1.5 flex items-center
                                       justify-between gap-2"
                            >
                                <span
                                    class="text-[11px]
                                           text-slate-400
                                           dark:text-slate-500"
                                >
                                    {{ formatDate(notification.created_at) }}
                                </span>

                                <span
                                    v-if="notification.category"
                                    class="text-[10px] uppercase
                                           tracking-wide
                                           text-slate-400
                                           dark:text-slate-500"
                                >
                                    {{ notification.category }}
                                </span>
                            </div>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Нет уведомлений -->
            <div
                v-else
                class="flex flex-col items-center justify-center
                       min-h-[140px] px-4 py-6 text-center"
            >
                <svg
                    class="w-8 h-8 mb-2
                           text-slate-300 dark:text-slate-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path
                        d="M18 8a6 6 0 0 0-12 0
                           c0 7-3 7-3 9h18
                           c0-2-3-2-3-9"
                    />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>

                <span
                    class="text-sm text-slate-500
                           dark:text-slate-400"
                >
                    Уведомлений пока нет
                </span>
            </div>

            <!-- Переход в центр уведомлений -->
            <button
                type="button"
                @click="openNotificationCenter"
                class="flex items-center justify-center gap-1.5
                       w-full px-3 py-2
                       text-xs font-medium
                       text-blue-600 hover:text-blue-700
                       dark:text-blue-400 dark:hover:text-blue-300
                       bg-slate-50 hover:bg-slate-100
                       dark:bg-slate-900 dark:hover:bg-slate-700
                       border-t border-slate-200 dark:border-slate-700
                       transition-colors"
            >
                Все уведомления

                <svg
                    class="w-3.5 h-3.5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </button>
        </div>
    </div>
</template>
