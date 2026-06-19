<script setup>
import { ref } from 'vue'

const isPressed = ref(false)
</script>

<template>
    <button
        type="button"
        class="flex items-center
               btn px-2 py-0.5
               bg-yellow-200 rounded-sm shadow-md
               text-slate-600 text-sm font-semibold
               transition-colors duration-300 ease-in-out
               hover:text-slate-700 focus:text-slate-700
               hover:bg-yellow-300 focus:bg-yellow-300 focus:outline-none"
        @mousedown="isPressed = true"
        @mouseup="isPressed = false"
        :class="{ 'ring-2 ring-yellow-300 ring-offset-2 ring-offset-white': isPressed }"
    >
        <span>
            <slot name="icon">
                <svg
                    class="w-4 h-4 fill-current text-gray-500 shrink-0"
                    viewBox="0 0 512 512">
                    <path d="M256 8C119.034 8 8 119.033 8 256s111.034 248 248 248 248-111.034 248-248S392.967 8 256 8zm130.108 117.892c65.448 65.448 70 165.481 20.677 235.637L150.47 105.216c70.204-49.356 170.226-44.735 235.638 20.676zM125.892 386.108c-65.448-65.448-70-165.481-20.677-235.637L361.53 406.784c-70.203 49.356-170.226 44.736-235.638-20.676z" />
                </svg>
            </slot>
        </span>

        <span class="hidden xs:block ml-1">
            <slot />
        </span>
    </button>
</template>
