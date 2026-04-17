<script setup>
import { computed, ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },

    /**
     * bottom-end -> выпадает вниз вправо
     * top-end    -> выпадает вверх вправо
     */
    placement: {
        type: String,
        default: 'bottom-end',
    }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const dropdownRef = ref(null)
const tooltipSide = ref('left')

/**
 * Внутренний словарь названий языков.
 * Потом сможете дополнять вручную.
 */
const localeTitles = {
    ru: 'Русский',
    en: 'English',
    kk: 'Қазақ',
}

/**
 * Получить красивое название локали.
 */
const getLocaleTitle = (code) => {
    const normalized = String(code || '').toLowerCase()

    return localeTitles[normalized] || normalized.toUpperCase()
}

const localesList = computed(() => {
    const serverLocales = page.props.availableLocales

    if (Array.isArray(serverLocales) && serverLocales.length) {
        return serverLocales.map((code) => {
            const c = String(code).toLowerCase()

            return {
                code: c,
                flag: `/storage/flags/${c}.svg`,
                title: getLocaleTitle(c),
            }
        })
    }

    return []
})

const selectedLocale = computed(() =>
    localesList.value.find(
        (loc) => loc.code === String(props.modelValue || '').toLowerCase()
    ) || null
)

const dropdownClass = computed(() => {
    if (props.placement === 'top-end') {
        return 'absolute right-0 bottom-full mb-2 origin-bottom-right'
    }

    return 'absolute right-0 top-full mt-2 origin-top-right'
})

const tooltipItemClass = computed(() => {
    if (tooltipSide.value === 'right') {
        return 'left-full ml-2'
    }

    return 'right-full mr-2'
})

/**
 * Определить, куда показывать tooltip:
 * справа или слева.
 */
const updateTooltipSide = async () => {
    await nextTick()

    if (!dropdownRef.value) return

    const rect = dropdownRef.value.getBoundingClientRect()
    const viewportWidth = window.innerWidth
    const tooltipApproxWidth = 96

    const freeRight = viewportWidth - rect.right
    const freeLeft = rect.left

    if (freeRight >= tooltipApproxWidth) {
        tooltipSide.value = 'right'
        return
    }

    if (freeLeft >= tooltipApproxWidth) {
        tooltipSide.value = 'left'
        return
    }

    tooltipSide.value = 'left'
}

const toggleDropdown = async () => {
    isOpen.value = !isOpen.value

    if (isOpen.value) {
        await updateTooltipSide()
    }
}

const closeDropdown = () => {
    isOpen.value = false
}

const selectLocale = (code) => {
    emit('update:modelValue', code)
    closeDropdown()
}

const handleClickOutside = (event) => {
    if (!dropdownRef.value) return

    if (!dropdownRef.value.contains(event.target)) {
        closeDropdown()
    }
}

const handleEscape = (event) => {
    if (event.key === 'Escape') {
        closeDropdown()
    }
}

const handleResize = async () => {
    if (isOpen.value) {
        await updateTooltipSide()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleEscape)
    window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleEscape)
    window.removeEventListener('resize', handleResize)
})
</script>

<template>
    <div ref="dropdownRef" class="relative">
        <!-- Trigger -->
        <div class="group relative">
            <button
                type="button"
                @click="toggleDropdown"
                class="flex items-center justify-center
                       w-10 h-8 rounded-md
                       hover:bg-slate-200 dark:hover:bg-slate-700
                       transition"
                :aria-expanded="isOpen"
                :aria-label="selectedLocale ? selectedLocale.title : t('selectLocale')"
            >
                <img
                    v-if="selectedLocale"
                    :src="selectedLocale.flag"
                    :alt="selectedLocale.code"
                    class="w-8 aspect-[4/3] object-cover rounded-sm"
                />
            </button>

            <!-- Tooltip trigger -->
            <div
                v-if="selectedLocale"
                class="pointer-events-none absolute left-1/2 top-full z-[60] mt-2
                       -translate-x-1/2 whitespace-nowrap rounded-md
                       bg-slate-900 px-2 py-1 text-[11px] font-semibold text-white
                       opacity-0 shadow-md transition duration-200
                       group-hover:opacity-100
                       dark:bg-slate-100 dark:text-slate-900"
            >
                {{ selectedLocale.title }}
            </div>
        </div>

        <!-- Dropdown -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
            <div
                v-if="isOpen"
                :class="dropdownClass"
                class="z-50 rounded-md border border-slate-200 bg-white shadow-lg
                       dark:border-slate-700 dark:bg-slate-900
                       flex flex-col gap-1"
            >
                <div
                    v-for="loc in localesList"
                    :key="loc.code"
                    class="group/item relative"
                >
                    <button
                        type="button"
                        @click="selectLocale(loc.code)"
                        class="flex items-center justify-center
                               w-10 h-8 rounded-md
                               hover:bg-slate-100 dark:hover:bg-slate-800
                               transition"
                        :class="String(loc.code).toLowerCase() === String(modelValue || '').toLowerCase()
                            ? 'ring-2 ring-blue-500'
                            : ''"
                        :aria-label="loc.title"
                    >
                        <img
                            :src="loc.flag"
                            :alt="loc.code"
                            class="w-8 aspect-[4/3] object-cover rounded-sm"
                        />
                    </button>

                    <!-- Tooltip item -->
                    <div
                        :class="tooltipItemClass"
                        class="pointer-events-none absolute top-1/2 z-[60]
                               -translate-y-1/2 whitespace-nowrap rounded-md
                               bg-slate-900 px-2 py-1 text-[11px] font-semibold text-white
                               opacity-0 shadow-md transition duration-200
                               group-hover/item:opacity-100
                               dark:bg-slate-100 dark:text-slate-900"
                    >
                        {{ loc.title }}
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
