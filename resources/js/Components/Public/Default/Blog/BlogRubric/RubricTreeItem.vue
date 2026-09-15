<script setup>
import {
    computed,
    ref,
    onMounted,
} from 'vue'

import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

defineOptions({
    name: 'RubricTreeItem',
})

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },

    depth: {
        type: Number,
        default: 0,
    },
})

const { t } = useI18n()

const STORAGE_KEY = 'rubricAccordionOpen'

/**
 * Есть ли дочерние рубрики.
 */
const hasChildren = computed(() =>
    Array.isArray(props.item?.children)
    && props.item.children.length > 0
)

/**
 * Показывать иконку только в том случае,
 * если значение действительно содержит SVG.
 */
const hasSvgIcon = computed(() => {
    const icon = props.item?.icon

    if (!icon) {
        return false
    }

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(icon)
    )
})

/**
 * URL публичной страницы рубрики.
 */
const rubricUrl = computed(() =>
    route(
        'public.blogRubrics.show',
        {
            url: props.item?.url,
        }
    )
)

/**
 * Состояние аккордеона.
 */
const isOpen = ref(false)

/**
 * Получить сохранённые ID
 * открытых рубрик.
 */
const getStoredOpenIds = () => {
    try {
        const raw =
            localStorage.getItem(STORAGE_KEY)

        if (!raw) {
            return []
        }

        const ids = JSON.parse(raw)

        return Array.isArray(ids)
            ? ids
            : []
    } catch {
        return []
    }
}

/**
 * Загрузить состояние
 * текущей рубрики.
 */
const loadState = () => {
    const openIds =
        getStoredOpenIds()

    isOpen.value =
        openIds.includes(props.item.id)
}

/**
 * Сохранить состояние
 * текущей рубрики.
 */
const saveState = (open) => {
    try {
        let openIds =
            getStoredOpenIds()

        if (open) {
            if (!openIds.includes(props.item.id)) {
                openIds.push(props.item.id)
            }
        } else {
            openIds = openIds.filter(
                id => id !== props.item.id
            )
        }

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(openIds)
        )
    } catch {
        // localStorage может быть недоступен.
    }
}

/**
 * Открыть / закрыть
 * дочерние рубрики.
 */
const toggleAccordion = () => {
    if (!hasChildren.value) {
        return
    }

    isOpen.value =
        !isOpen.value

    saveState(isOpen.value)
}

onMounted(() => {
    if (hasChildren.value) {
        loadState()
    }
})
</script>

<template>
    <div class="w-full">
        <!-- Строка рубрики -->
        <div
            class="flex items-center justify-between gap-0.5
                   my-0.5 rounded-sm transition
                   hover:bg-slate-200 dark:hover:bg-slate-800"
            :style="{
                paddingLeft: `${depth * 16}px`,
            }"
        >
            <!-- Левая часть -->
            <div
                class="flex min-w-0 flex-1
                       items-center gap-2 pr-2 py-1"
            >
                <!--
                    Если есть дочерние рубрики,
                    заголовок управляет аккордеоном.
                -->
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
                               text-gray-500 dark:text-gray-400
                               transition-transform duration-200"
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
                        class="truncate text-xs font-semibold
                               text-gray-700 dark:text-gray-300"
                    >
                        {{ item.title }}
                    </span>
                </button>

                <!--
                    Если дочерних рубрик нет,
                    заголовок является ссылкой.
                -->
                <Link
                    v-else
                    :href="rubricUrl"
                    class="min-w-0 flex-1"
                >
                    <span
                        class="truncate text-xs font-semibold
                               text-gray-700 dark:text-gray-300"
                    >
                        {{ item.title }}
                    </span>
                </Link>

                <!-- SVG-иконка рубрики -->
                <span
                    v-if="hasSvgIcon"
                    class="flex h-4 w-4
                           items-center justify-center shrink-0"
                    v-html="item.icon"
                />
            </div>

            <!--
                Отдельная ссылка на родительскую рубрику,
                когда заголовок управляет аккордеоном.
            -->
            <Link
                v-if="hasChildren"
                :href="rubricUrl"
                class="mr-2 shrink-0 rounded-sm px-2 py-1
                       text-[10px] font-semibold
                       text-indigo-700 dark:text-indigo-300
                       hover:text-slate-100
                       hover:bg-indigo-500
                       dark:hover:bg-indigo-500"
                :title="t('openLink')"
            >
                →
            </Link>
        </div>

        <!-- Дочерние рубрики -->
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
                <RubricTreeItem
                    v-for="child in item.children"
                    :key="child.id"
                    :item="child"
                    :depth="depth + 1"
                />
            </div>
        </transition>
    </div>
</template>
