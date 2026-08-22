<script setup>
import {
    computed,
    ref,
    onMounted,
} from 'vue'

import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

defineOptions({
    name: 'TrackTreeItem',
})

const { t } = useI18n()

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },

    /**
     * 0 = первый уровень
     * 1 = второй уровень
     * 2 = третий уровень
     */
    depth: {
        type: Number,
        default: 0,
    },
})

const STORAGE_KEY =
    'trackAccordionOpen'

/**
 * Максимальная глубина
 * публичного дерева — 3 уровня.
 *
 * depth:
 * 0 → уровень 1
 * 1 → уровень 2
 * 2 → уровень 3
 */
const MAX_DEPTH = 2

/**
 * Название из единого
 * Public Resource-контракта.
 */
const trackName = computed(() => {
    return props.item?.translation?.name || ''
})

/**
 * Slug трека.
 */
const trackSlug = computed(() => {
    return props.item?.slug || ''
})

/**
 * URL публичной страницы трека.
 */
const trackUrl = computed(() => {
    return trackSlug.value
        ? route(
            'public.schoolTracks.show',
            {
                slug: trackSlug.value,
            }
        )
        : '#'
})

/**
 * Дочерние элементы.
 *
 * На третьем уровне
 * дальнейшее раскрытие запрещаем.
 */
const children = computed(() => {
    if (props.depth >= MAX_DEPTH) {
        return []
    }

    return Array.isArray(
        props.item?.children
    )
        ? props.item.children
        : []
})

/**
 * Есть ли доступные дочерние элементы.
 */
const hasChildren = computed(() => {
    return children.value.length > 0
})

/**
 * Состояние accordion.
 */
const isOpen = ref(false)

/**
 * Загрузить состояние accordion
 * из localStorage.
 */
const loadState = () => {
    try {
        const raw =
            localStorage.getItem(
                STORAGE_KEY
            )

        const openIds =
            raw
                ? JSON.parse(raw)
                : []

        if (Array.isArray(openIds)) {
            isOpen.value =
                openIds.includes(
                    props.item.id
                )
        }
    } catch {
        isOpen.value = false
    }
}

/**
 * Сохранить состояние accordion.
 */
const saveState = (open) => {
    try {
        const raw =
            localStorage.getItem(
                STORAGE_KEY
            )

        let openIds =
            raw
                ? JSON.parse(raw)
                : []

        if (!Array.isArray(openIds)) {
            openIds = []
        }

        if (open) {
            if (
                !openIds.includes(
                    props.item.id
                )
            ) {
                openIds.push(
                    props.item.id
                )
            }
        } else {
            openIds =
                openIds.filter(
                    (id) =>
                        id !== props.item.id
                )
        }

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(openIds)
        )
    } catch {
        //
    }
}

/**
 * Открыть / закрыть ветку.
 */
const toggleAccordion = () => {
    if (!hasChildren.value) {
        return
    }

    isOpen.value =
        !isOpen.value

    saveState(
        isOpen.value
    )
}

onMounted(() => {
    if (hasChildren.value) {
        loadState()
    }
})
</script>

<template>
    <div class="w-full">
        <!-- Строка трека -->
        <div
            class="my-0.5 flex items-center
                   justify-between gap-0.5
                   rounded-sm transition
                   hover:bg-slate-200
                   dark:hover:bg-slate-800"
            :style="{
                paddingLeft: `${depth * 16}px`,
            }"
        >
            <div
                class="flex min-w-0 flex-1
                       items-center gap-2
                       py-1 pr-2"
            >
                <!-- Есть дочерние элементы -->
                <button
                    v-if="hasChildren"
                    type="button"
                    class="flex min-w-0 flex-1
                           items-center justify-start
                           gap-1 text-left"
                    @click="toggleAccordion"
                >
                    <svg
                        class="h-3.5 w-3.5 shrink-0
                               text-gray-500
                               transition-transform
                               duration-200
                               dark:text-gray-400"
                        :class="{
                            'rotate-90': isOpen,
                        }"
                        viewBox="0 0 320 512"
                        fill="currentColor"
                    >
                        <path
                            d="M96 96l128 160L96 416z"
                        />
                    </svg>

                    <span
                        class="truncate text-xs
                               font-semibold
                               text-gray-700
                               dark:text-gray-300"
                    >
                        {{ trackName }}
                    </span>
                </button>

                <!-- Конечный элемент -->
                <Link
                    v-else
                    :href="trackUrl"
                    class="min-w-0 flex-1"
                >
                    <span
                        class="truncate text-xs
                               font-semibold
                               text-gray-700
                               dark:text-gray-300"
                    >
                        {{ trackName }}
                    </span>
                </Link>
            </div>

            <!-- Ссылка на страницу родительского трека -->
            <Link
                v-if="hasChildren"
                :href="trackUrl"
                class="mr-2 shrink-0
                       rounded-sm px-2 py-1
                       text-[10px] font-semibold
                       text-indigo-700
                       hover:bg-indigo-500
                       hover:text-slate-100
                       dark:text-indigo-300
                       dark:hover:bg-indigo-500"
                :title="t('openLink')"
            >
                →
            </Link>
        </div>

        <!-- Дочерние элементы -->
        <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[1000px]"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-[1000px]"
            leave-to-class="opacity-0 max-h-0"
        >
            <div
                v-if="hasChildren && isOpen"
                class="overflow-hidden"
            >
                <TrackTreeItem
                    v-for="child in children"
                    :key="child.id"
                    :item="child"
                    :depth="depth + 1"
                />
            </div>
        </transition>
    </div>
</template>
