<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
})
</script>

<template>
    <Link
        :href="href"
        class="p-4 group block bg-white rounded-md shadow-sm hover:shadow-md transition
               border border-gray-200 hover:border-indigo-400
               dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-400"
    >
        <div class="flex items-center justify-between">
            <h2
                class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600
                       dark:text-gray-100 dark:group-hover:text-indigo-400"
            >
                {{ title }}
            </h2>

            <!-- slot для иконки -->
            <slot name="icon" />
        </div>

        <p class="mt-2 font-semibold text-[10px] text-gray-600 dark:text-gray-200">
            {{ description }}
        </p>
    </Link>
</template>
