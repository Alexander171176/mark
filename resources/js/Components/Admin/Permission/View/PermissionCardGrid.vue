<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    permissions: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits(['delete'])
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative">

        <!-- Верхняя панель: инфо -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('permissions') }}: {{ permissions.length }}
            </div>
        </div>

        <!-- Сетка карточек -->
        <div v-if="permissions.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Верхняя панель карточки: ID + имя -->
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="w-full flex flex-row items-center justify-start gap-2">
                            <span
                                class="w-fit text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400 bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100 whitespace-nowrap"
                            >
                                ID: {{ permission.id }}
                            </span>
                            <span
                                class="w-full text-[14px] font-semibold text-center
                                       text-teal-700 dark:text-teal-200 line-clamp-2"
                                :title="permission.name"
                            >
                                {{ permission.name }}
                            </span>
                        </div>
                    </header>

                    <!-- Доп. инфо (опционально guard_name) -->
                    <div class="flex-1 px-3 py-2">
                        <div
                            v-if="permission.guard_name"
                            class="text-[14px] text-gray-600 dark:text-gray-200 text-center mb-1"
                        >
                            guard:
                        <span class="text-amber-600 dark:text-amber-200 font-semibold">
                            {{ permission.guard_name }}
                        </span>
                        </div>
                    </div>

                    <!-- Низ карточки: действия -->
                    <footer
                        class="px-3 py-2 border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <IconEdit :href="route('admin.permissions.edit', permission.id)" />
                            <DeleteIconButton @delete="$emit('delete', permission.id)" />
                        </div>
                    </footer>
                </article>
            </div>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
