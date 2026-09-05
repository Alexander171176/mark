<script setup>
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

defineProps({
    permissions: { type: Array, default: () => [] },
})

const emit = defineEmits(['delete'])
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="permissions.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead class="text-sm font-semibold uppercase
                               bg-slate-200 dark:bg-cyan-900
                               border border-solid
                               border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">{{ t('id') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('name') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">Guard</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('roles') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="text-sm font-semibold
                           border-b-2
                           hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="text-center">{{ permission.id }}</div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="text-left text-teal-600 dark:text-violet-200">
                            {{ permission.name }}
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="text-left text-amber-600 dark:text-amber-200">
                            {{ permission.guard_name || '—' }}
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1">
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="role in (permission.roles || [])"
                                :key="role.id"
                                class="px-1 py-0 bg-slate-100 dark:bg-slate-800
                                       border border-dashed border-gray-400
                                       text-orange-500 dark:text-orange-200 text-xs"
                            >
                                {{ role.name }}
                            </span>

                            <span
                                v-if="!permission.roles || permission.roles.length === 0"
                                class="text-xs italic text-gray-500"
                            >
                                —
                            </span>
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
                            <IconEdit :href="route('admin.permissions.edit', permission.id)" />
                            <DeleteIconButton @click="emit('delete', permission.id, permission.name)" />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
