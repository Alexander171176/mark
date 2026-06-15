<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: String,
})

const emits = defineEmits([
    'update:sortParam',
])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit my-2">
        <label
            for="sortParam"
            class="
                hidden lg:block sm:mr-2
                tracking-wider text-sm
                font-semibold
                text-slate-600
                dark:text-slate-100
            "
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="
                w-52 px-3 py-0.5
                form-select
                bg-white dark:bg-gray-200
                text-gray-600 dark:text-gray-900
                border border-slate-400
                dark:border-slate-600
                rounded-sm shadow-sm
            "
        >

            <!-- ID -->
            <option value="idDesc">
                {{ t('idDesc') }}
            </option>

            <option value="idAsc">
                {{ t('idAsc') }}
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- NAME -->
            <option value="nameAsc">
                {{ t('name') }} ↑
            </option>

            <option value="nameDesc">
                {{ t('name') }} ↓
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- GUARD -->
            <option value="guardNameAsc">
                {{ t('guard') }} ↑
            </option>

            <option value="guardNameDesc">
                {{ t('guard') }} ↓
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- ROLES -->
            <option value="rolesAsc">
                {{ t('roles') }} ↑
            </option>

            <option value="rolesDesc">
                {{ t('roles') }} ↓
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- CREATED -->
            <option value="createdAtAsc">
                {{ t('createdAt') }} ↑
            </option>

            <option value="createdAtDesc">
                {{ t('createdAt') }} ↓
            </option>

            <option disabled>
                ─────────────────
            </option>

            <!-- UPDATED -->
            <option value="updatedAtAsc">
                {{ t('updatedAt') }} ↑
            </option>

            <option value="updatedAtDesc">
                {{ t('updatedAt') }} ↓
            </option>

        </select>
    </div>
</template>
