<script setup>
import { useDark } from '@vueuse/core'

const props = defineProps({
    id: { type: String, default: 'theme-toggle' }, // чтобы можно было переиспользовать
    label: { type: String, default: 'Toggle dark mode' },
})

const isDark = useDark({ selector: 'html' })
</script>

<template>
    <label :for="props.id" class="vulk-theme-toggle">
        <input
            :id="props.id"
            type="checkbox"
            v-model="isDark"
            class="sr-only"
        />
        <div class="no-margin">
            <div class="track">
                <div class="track-inner"></div>
                <div class="track-knob">
                    <!-- Солнце -->
                    <svg viewBox="0 0 24 24" width="1.2em" height="1.2em"
                         class="inactive iconify">
                        <g fill="none"
                           stroke="currentColor"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           stroke-width="2">
                            <circle cx="12" cy="12" r="5"></circle>
                            <path
                                d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>
                        </g>
                    </svg>
                    <!-- Луна -->
                    <svg viewBox="0 0 24 24" width="1.2em" height="1.2em"
                         class="active iconify">
                        <path fill="none"
                              stroke="currentColor"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 12.79A9 9 0 1 1 11.21 3A7 7 0 0 0 21 12.79"></path>
                    </svg>
                </div>
            </div>
        </div>
        <span class="sr-only">{{ label }}</span>
    </label>
</template>

<style scoped>
/* базовая утилита */
.sr-only {
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1;
    opacity: 0;
}

/* визуальный стиль под vulk */
.vulk-theme-toggle {
    position: relative;
    cursor: pointer;
}

/* дорожка */
.vulk-theme-toggle .track {
    position: relative;
}

/* внутренняя заливка (можно подложить цвет темы) */
.vulk-theme-toggle .track-inner {
    height: 11px;
    width: 42px;
    background: var(--custom-toggle-track-bg);
    border-radius: 100px;
}

/* бегунок */
.vulk-theme-toggle .track-knob {
    position: absolute;
    top: -8px;
    left: -2px;
    height: 26px;
    width: 26px;
    border-radius: 50%;
    border: 1px solid var(--border);
    box-shadow: var(--light-box-shadow);
    background: var(--white);
    display: flex;
    justify-content: center;
    align-items: center;
    transform: rotate(0);
    transition: all 0.3s;
}

/* иконки: по умолчанию видим солнце, луну скрываем */
.vulk-theme-toggle .track-knob .inactive { display: block; }
.vulk-theme-toggle .track-knob .active { display: none; }

/* Состояние: когда чекбокс включён (тёмная тема) — двигаем бегунок вправо и меняем иконки */
.vulk-theme-toggle input:checked + .no-margin .track .track-knob {
    margin-left: 22px;
    transform: rotate(360deg);
    background: var(--primary);
    border-color: var(--primary);
    box-shadow: var(--primary-box-shadow);
}

.vulk-theme-toggle input:checked + .no-margin .track .track-knob .inactive { display: none; }
.vulk-theme-toggle input:checked + .no-margin .track .track-knob .active { display: block; }
</style>
