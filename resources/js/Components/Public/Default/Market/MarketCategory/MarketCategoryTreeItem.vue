<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

defineOptions({
    name: 'MarketCategoryTreeItem',
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

/** Состояние дочернего списка */
const isOpen = ref(false)

/** Дочерние категории */
const children = computed(() => {
    return Array.isArray(props.item?.children)
        ? props.item.children
        : []
})

/** Есть дочерние категории */
const hasChildren = computed(() => {
    return children.value.length > 0
})

/** Ссылка на категорию */
const categoryLink = computed(() => {
    return props.item?.url
        ? route('public.marketCategories.show', { url: props.item.url })
        : '#'
})

/** Есть SVG-иконка */
const hasSvgIcon = computed(() => {
    return typeof props.item?.icon === 'string'
        && props.item.icon.trim().startsWith('<svg')
})

/** Отступ по уровню дерева */
const itemPadding = computed(() => {
    return {
        paddingLeft: `${props.depth * 12}px`,
    }
})

/** Открыть / закрыть дочерние категории */
const toggleChildren = () => {
    if (!hasChildren.value) {
        return
    }

    isOpen.value = !isOpen.value
}
</script>

<template>
    <div class="w-full">
        <!-- Категория -->
        <div
            class="group flex items-center gap-1 rounded-sm
                   transition hover:bg-slate-100
                   dark:hover:bg-slate-800"
            :style="itemPadding"
        >
            <!-- Аккордеон -->
            <button
                v-if="hasChildren"
                type="button"
                class="flex h-6 w-6 shrink-0 items-center justify-center
                       text-slate-500 hover:text-indigo-600
                       dark:text-slate-400 dark:hover:text-indigo-400"
                @click="toggleChildren"
            >
                <svg
                    class="h-3 w-3 transition-transform duration-200"
                    :class="{ 'rotate-90': isOpen }"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>

            <!-- Место кнопки для выравнивания -->
            <span
                v-else
                class="block h-6 w-6 shrink-0"
            />

            <!-- Ссылка -->
            <Link
                :href="categoryLink"
                class="flex min-w-0 flex-1 items-center gap-2
                       rounded-sm px-1 py-2"
                :title="item.title"
            >
                <!-- Изображение -->
                <img
                    v-if="item.thumbnail_url"
                    :src="item.thumbnail_url"
                    :alt="item.title"
                    loading="lazy"
                    class="h-6 w-6 shrink-0 rounded-sm object-cover"
                />

                <!-- SVG-иконка -->
                <span
                    v-else-if="hasSvgIcon"
                    class="flex h-5 w-5 shrink-0 items-center justify-center"
                    v-html="item.icon"
                />

                <!-- Стандартная иконка -->
                <svg
                    v-else
                    class="h-4 w-4 shrink-0
                           text-slate-500 dark:text-slate-400"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" />
                </svg>

                <!-- Название -->
                <span
                    class="min-w-0 flex-1 truncate text-sm font-semibold
                           text-slate-700 group-hover:text-indigo-600
                           dark:text-slate-300 dark:group-hover:text-indigo-400"
                >
                    {{ item.title }}
                </span>

                <!-- Количество детей -->
                <span
                    v-if="hasChildren"
                    class="shrink-0 text-[11px] font-semibold
                           text-slate-400 dark:text-slate-500"
                >
                    {{ children.length }}
                </span>
            </Link>
        </div>

        <!-- Дочерние категории -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-if="isOpen && hasChildren"
                class="space-y-1"
            >
                <MarketCategoryTreeItem
                    v-for="child in children"
                    :key="child.id"
                    :item="child"
                    :depth="depth + 1"
                />
            </div>
        </transition>
    </div>
</template>
