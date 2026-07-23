<script setup>
import { Link } from '@inertiajs/vue3'
import { defineProps, ref } from 'vue'

defineProps({
    href: {
        type: String,
        required: true
    }
})

const isPressed = ref(false)
</script>

<template>
    <Link :href="href"
          class="flex items-center btn px-2 py-0.5
                 text-sm font-semibold
                 text-slate-700 dark:text-slate-300
                 hover:text-blue-800 focus:text-blue-800
                 dark:hover:text-blue-600 dark:focus:text-blue-600
                 transition-colors duration-300 ease-in-out">
        <span>
          <svg class="w-4 h-4 fill-current opacity-80 shrink-0" viewBox="0 0 16 16">
              <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
          </svg>
        </span>
    </Link>
</template>
