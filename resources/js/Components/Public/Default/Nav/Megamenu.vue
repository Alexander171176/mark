<script setup>
import { computed } from 'vue'

const props = defineProps({
    /* уникальное имя для этой мегаменю */
    name: { type: String, required: true },
    /* v-model для активного меню */
    modelValue: { type: [String, null], default: null },
    /* тег триггера (по умолчанию <a>) */
    as: { type: String, default: 'a' }
})

const emit = defineEmits(['update:modelValue'])

const isActive = computed(() => props.modelValue === props.name)

function openMenu()  { emit('update:modelValue', props.name) }
function closeMenu() { emit('update:modelValue', null) }
function toggleMenu(e) {
    e?.preventDefault?.()
    isActive.value ? closeMenu() : openMenu()
}
</script>

<template>
    <div class="navbar-item has-mega"
         @mouseenter="openMenu"
         @mouseleave="closeMenu"
         @focusin="openMenu"
         @focusout="closeMenu">

        <!-- Триггер -->
        <component :is="as"
                   role="button"
                   class="navbar-item"
                   tabindex="0"
                   @click="toggleMenu">
            <!-- слот label для текста/иконки триггера -->
            <slot name="label" />
        </component>

        <!-- Панель -->
        <div class="megamenu" :class="{ 'is-active': isActive }">
            <div data-v-101d024b="" class="megamenu-inner">
                <button class="close-button" @click="closeMenu" aria-label="Close megamenu">
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                         role="img" width="1em" height="1em" viewBox="0 0 24 24"
                         data-icon="feather:arrow-left" class="iconify iconify--feather">
                        <path fill="none"
                              stroke="currentColor"
                              stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 12H5m7 7l-7-7l7-7"></path>
                    </svg>
                </button>

                <!-- сюда идёт уникальный контент -->
                <slot />
            </div>
        </div>
    </div>
</template>
