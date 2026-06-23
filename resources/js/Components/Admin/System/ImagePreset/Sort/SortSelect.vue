<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    sortParam: String,
})

const emits = defineEmits(['update:sortParam'])

const updateSort = (event) => {
    emits('update:sortParam', event.target.value)
}
</script>

<template>
    <div class="flex justify-center items-center h-fit mt-2 mb-2">
        <label
            for="sortParam"
            class="hidden lg:block sm:mr-2 tracking-wider
                   text-sm font-semibold text-slate-600 dark:text-slate-100"
        >
            {{ t('sort') }}
        </label>

        <select
            id="sortParam"
            :value="sortParam"
            @change="updateSort"
            class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200
                   text-gray-600 dark:text-gray-900
                   border border-slate-400 dark:border-slate-600
                   rounded-sm shadow-sm"
        >
            <option value="idDesc">ID ↓</option>
            <option value="idAsc">ID ↑</option>
            <option disabled>────────────────────</option>

            <option value="sortAsc">{{ t('sortNumber') }} 0→9</option>
            <option value="sortDesc">{{ t('sortNumber') }} 9→0</option>
            <option disabled>────────────────────</option>

            <option value="keyAsc">{{ t('key') }} A→Z</option>
            <option value="keyDesc">{{ t('key') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="widthAsc">{{ t('width') }} 0→9</option>
            <option value="widthDesc">{{ t('width') }} 9→0</option>

            <option value="heightAsc">{{ t('height') }} 0→9</option>
            <option value="heightDesc">{{ t('height') }} 9→0</option>
            <option disabled>────────────────────</option>

            <option value="sizeAsc">{{ t('fileSize') }} 0→9</option>
            <option value="sizeDesc">{{ t('fileSize') }} 9→0</option>
            <option disabled>────────────────────</option>

            <option value="shapeAsc">{{ t('shape') }} A→Z</option>
            <option value="shapeDesc">{{ t('shape') }} Z→A</option>
            <option disabled>────────────────────</option>

            <option value="allowRotate">
                {{ t('allowRotateShort') }} {{ t('image') }} ON
            </option>
            <option value="noAllowRotate">
                {{ t('allowRotateShort') }} {{ t('image') }} OFF
            </option>

            <option value="allowCropRotate">
                {{ t('allowRotateShort') }} {{ t('photoFrames') }} ON
            </option>
            <option value="noCropRotate">
                {{ t('allowRotateShort') }} {{ t('photoFrames') }} OFF
            </option>
            <option disabled>────────────────────</option>

            <option value="keepOriginal">
                {{ t('keepOriginal') }} - {{ t('yes') }}
            </option>
            <option value="noKeepOriginal">
                {{ t('keepOriginal') }} - {{ t('no') }}
            </option>
            <option disabled>────────────────────</option>

            <option value="allowRotate">
                {{ t('allowRotate') }} - {{ t('yes') }}
            </option>
            <option value="noAllowRotate">
                {{ t('allowRotate') }} - {{ t('no') }}
            </option>

            <option value="keepOriginal">
                {{ t('keepOriginal') }} - {{ t('yes') }}
            </option>
            <option value="noKeepOriginal">
                {{ t('keepOriginal') }} - {{ t('no') }}
            </option>
            <option disabled>────────────────────</option>

            <option value="createdAtDesc">{{ t('createdAt') }} ↓</option>
            <option value="createdAtAsc">{{ t('createdAt') }} ↑</option>

            <option value="updatedAtDesc">{{ t('updatedAt') }} ↓</option>
            <option value="updatedAtAsc">{{ t('updatedAt') }} ↑</option>
            <option disabled>────────────────────</option>
        </select>
    </div>
</template>
