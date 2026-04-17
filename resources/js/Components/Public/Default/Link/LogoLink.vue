<script setup>
import { Link } from '@inertiajs/vue3'

defineOptions({ inheritAttrs: false })

const props = defineProps({
    href: { type: String, default: route?.('home') ?? '/' },
    title: { type: String, default: '' },
    ariaCurrent: { type: String, default: null },

    imgSrcLight: {
        type: String,
        default: new URL('./../../../../../images/fort-awesome.svg', import.meta.url).href,
    },
    imgSrcDark: {
        type: String,
        default: new URL('./../../../../../images/fort-awesome.svg', import.meta.url).href,
    },
    imgAlt: { type: String, default: 'Logo' },

    sizeClass: { type: String, default: 'h-4 w-auto' },

    // 🔥 Текст рядом с логотипом
    brandText: { type: String, default: '' }, // например: 'PulsarCMS'
    showBrandText: { type: Boolean, default: true },

    linkClass: {
        type: [String, Array, Object],
        default:
            'inline-flex items-center gap-2 rounded-lg px-3 py-1 ' +
            'text-gray-900 dark:text-white ' +
            'hover:opacity-80 transition-opacity ' +
            'focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-700',
    },

    logoWrapperClass: { type: [String, Array, Object], default: 'flex items-center gap-2' },
    imgClass: { type: [String, Array, Object], default: 'block object-contain' },

    // Tailwind классы текста (можешь менять централизованно)
    brandTextClass: {
        type: [String, Array, Object],
        default:
            'hidden sm:inline-flex items-center ' +
            'text-md font-semibold tracking-wide ' +
            'text-blue-600 dark:text-blue-400',
    },
})
</script>

<template>
    <Link
        v-bind="$attrs"
        :href="href"
        :title="title"
        :aria-current="ariaCurrent"
        :class="linkClass"
    >
        <div :class="logoWrapperClass">
            <!-- light -->
            <img
                :src="imgSrcLight"
                :alt="imgAlt"
                loading="lazy"
                :class="[imgClass, sizeClass, 'dark:hidden']"
            />
            <!-- dark -->
            <img
                :src="imgSrcDark"
                :alt="imgAlt"
                loading="lazy"
                :class="[imgClass, sizeClass, 'hidden dark:block']"
            />
        </div>

        <!-- ✅ Текст: скрыт на mobile, виден с sm -->
        <span v-if="showBrandText && (brandText || $slots.default)" :class="brandTextClass">
      <template v-if="brandText">{{ brandText }}</template>
      <slot v-else />
    </span>
    </Link>
</template>
