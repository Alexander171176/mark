<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    latitude: {
        type: [String, Number, null],
        default: '',
    },
    longitude: {
        type: [String, Number, null],
        default: '',
    },
    latitudeError: {
        type: String,
        default: '',
    },
    longitudeError: {
        type: String,
        default: '',
    },
    wrapperClass: {
        type: String,
        default: 'lg:col-span-3 flex justify-center',
    },
    innerClass: {
        type: String,
        default: 'w-full max-w-2xl',
    },
})

const emit = defineEmits([
    'update:latitude',
    'update:longitude',
])

const latitudeValue = computed({
    get: () => props.latitude ?? '',
    set: (value) => emit('update:latitude', value),
})

const longitudeValue = computed({
    get: () => props.longitude ?? '',
    set: (value) => emit('update:longitude', value),
})
</script>

<template>
    <div :class="wrapperClass">
        <div :class="innerClass">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <LabelInput
                        for="latitude"
                        :value="t('latitude')"
                    />

                    <input
                        id="latitude"
                        type="number"
                        step="0.0000001"
                        min="-90"
                        max="90"
                        v-model="latitudeValue"
                        class="w-full px-2 py-0.5
                               border border-slate-400
                               dark:border-slate-600
                               rounded-sm shadow-sm
                               bg-white dark:bg-cyan-800
                               dark:text-slate-100 text-gray-600"
                    />

                    <InputError
                        class="mt-2"
                        :message="latitudeError"
                    />
                </div>

                <div class="flex flex-col">
                    <LabelInput
                        for="longitude"
                        :value="t('longitude')"
                    />

                    <input
                        id="longitude"
                        type="number"
                        step="0.0000001"
                        min="-180"
                        max="180"
                        v-model="longitudeValue"
                        class="w-full px-2 py-0.5
                               border border-slate-400
                               dark:border-slate-600
                               rounded-sm shadow-sm
                               bg-white dark:bg-cyan-800
                               dark:text-slate-100 text-gray-600"
                    />

                    <InputError
                        class="mt-2"
                        :message="longitudeError"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
