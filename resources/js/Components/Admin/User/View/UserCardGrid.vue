<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits(['delete'])

const getInitials = (name) => {
    if (!name) return '?'
    const parts = name.trim().split(/\s+/)
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
    return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase()
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative">

        <!-- Верхняя панель: просто инфо -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('users') }}: {{ users.length }}
            </div>
        </div>

        <!-- Сетка карточек -->
        <div v-if="users.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="user in users"
                    :key="user.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Верхняя панель карточки: ID + email -->
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex flex-row items-center space-x-2">
                            <span
                                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400 bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                            >
                                ID: {{ user.id }}
                            </span>
                            <span
                                class="text-[10px] text-blue-800 dark:text-blue-200
                                       break-all"
                                :title="user.email"
                            >
                                {{ user.email }}
                            </span>
                        </div>
                    </header>

                    <!-- «Аватар» с инициалами -->
                    <div class="relative w-full h-24 flex items-center justify-center
                                bg-slate-200 dark:bg-slate-900">
                        <div
                            class="h-16 w-16 rounded-full flex items-center justify-center
                                   bg-gradient-to-br from-sky-500 to-indigo-500
                                   text-white text-xl font-bold shadow-md"
                        >
                            {{ getInitials(user.name) }}
                        </div>
                    </div>

                    <!-- Контент -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <!-- Имя -->
                        <div class="text-center">
                            <div
                                class="text-[13px] font-semibold
                                       text-teal-700 dark:text-teal-200
                                       line-clamp-2"
                                :title="user.name"
                            >
                                {{ user.name }}
                            </div>
                        </div>

                        <!-- Роли -->
                        <div class="text-center">
                            <div
                                class="flex flex-wrap justify-center gap-1"
                            >
                                <span
                                    v-for="role in user.roles"
                                    :key="role.id"
                                    class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800
                                           border border-dashed border-gray-400
                                           text-orange-500 dark:text-orange-200
                                           text-[10px] rounded-sm"
                                >
                                    {{ role.name }}
                                </span>
                                <span
                                    v-if="!user.roles || user.roles.length === 0"
                                    class="text-[10px] italic text-gray-500 dark:text-gray-400"
                                >
                                    —
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Низ карточки: действия -->
                    <footer
                        class="px-3 py-2 border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <IconEdit :href="route('admin.users.edit', user.id)" />
                            <DeleteIconButton @delete="$emit('delete', user.id)" />
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
