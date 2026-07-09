<script setup>
import { ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

const { t } = useI18n()

const props = defineProps({
    settingKey: { type: String, required: true },
    mode: { type: String, default: 'server' },
    useServerProcessing: { type: Boolean, default: false },
    total: { type: Number, default: 0 },
    storageKey: { type: String, default: 'public_admin_panel_collapsed' },
})

const getStoredBoolean = (key, defaultValue = false) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

const collapsed = ref(getStoredBoolean(props.storageKey, false))

watch(collapsed, (value) => {
    localStorage.setItem(props.storageKey, String(value))
})

const togglePanel = () => {
    collapsed.value = !collapsed.value
}
</script>

<template>
    <div class="fixed bottom-0 left-0 right-0 z-[9999]">
        <button
            type="button"
            class="absolute left-1/2 -translate-x-1/2
                   flex h-4 w-10 items-center justify-center
                   rounded-t-full border border-b-0
                   border-slate-400/60
                   bg-slate-300/95 dark:bg-slate-700/95
                   text-slate-700 dark:text-slate-300
                   shadow-md backdrop-blur-md
                   hover:text-indigo-600 dark:hover:text-indigo-300"
            :class="collapsed ? 'bottom-0' : 'bottom-12 sm:bottom-10'"
            :title="collapsed ? t('show') : t('hide')"
            @click="togglePanel"
        >
            <svg
                class="h-3 w-3 transition-transform duration-300"
                :class="collapsed ? 'rotate-180' : ''"
                fill="currentColor"
                viewBox="0 0 320 512"
            >
                <path
                    d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19.1 320 32 320h256c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"
                />
            </svg>
        </button>

        <div
            class="min-h-10 sm:min-h-8
                   flex flex-col sm:flex-row
                   items-stretch sm:items-center
                   justify-between gap-2
                   px-2 sm:px-3 py-2 sm:py-0
                   border-t border-slate-400/40
                   bg-slate-300/90 dark:bg-slate-700/90
                   backdrop-blur-md text-[11px]
                   transition-transform duration-300"
            :class="collapsed ? 'translate-y-full' : 'translate-y-0'"
        >
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <Link
                    :href="route('admin.index')"
                    class="bg-gray-200 dark:bg-gray-800 rounded-sm px-2 py-0.5
                           border-2 border-slate-500 hover:border-indigo-500"
                >
                    <span class="text-slate-700 dark:text-slate-300
                                 hover:text-indigo-700 hover:dark:text-indigo-300">
                        {{ t('adminPanel') }}
                    </span>
                </Link>

                <slot name="left" />
            </div>

            <div class="flex items-center justify-center sm:justify-end gap-2 overflow-x-auto">
                <slot name="right-before" />

                <ProcessingModeSwitcher
                    :setting-key="settingKey"
                    :mode="mode"
                    :use-server-processing="useServerProcessing"
                    :total="total"
                />

                <slot name="right-after" />
            </div>
        </div>
    </div>
</template>
