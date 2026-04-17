<script setup>
import { useI18n } from 'vue-i18n'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputDecimalExt from '@/Components/Admin/Input/InputDecimalExt.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    rating_avg: {
        type: [Number, String, null],
        default: null,
    },
    rating_count: {
        type: [Number, String, null],
        default: null,
    },
    popularity: {
        type: [Number, String, null],
        default: null,
    },
    views: {
        type: [Number, String, null],
        default: null,
    },
    likes: {
        type: [Number, String, null],
        default: null,
    },
    errors: {
        type: Object,
        default: () => ({
            rating_avg: '',
            rating_count: '',
            popularity: '',
            views: '',
            likes: '',
        }),
    },
})

const emit = defineEmits([
    'update:rating_avg',
    'update:rating_count',
    'update:popularity',
    'update:views',
    'update:likes',
])

const updateRatingAvg = (val) => emit('update:rating_avg', val)
const updateRatingCount = (val) => emit('update:rating_count', val)
const updatePopularity = (val) => emit('update:popularity', val)
const updateViews = (val) => emit('update:views', val)
const updateLikes = (val) => emit('update:likes', val)
</script>

<template>
    <div class="mb-3 flex flex-col items-start
                border-t border-b border-dashed border-slate-500 py-3">

        <div class="w-full mb-2 text-sm font-semibold text-gray-900 dark:text-gray-200">
            {{ t('metricsLesson') }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 w-full">

            <!-- popularity -->
            <div class="flex flex-col items-start">
                <LabelInput for="popularity">
                    {{ t('popularity') }}
                </LabelInput>
                <InputNumber
                    id="popularity"
                    type="number"
                    min="0"
                    :model-value="popularity"
                    class="w-full"
                    @update:model-value="updatePopularity"
                />
                <InputError class="mt-1" :message="errors.popularity" />
            </div>

            <!-- rating_count -->
            <div class="flex flex-col items-start">
                <LabelInput for="rating_count">
                    {{ t('ratingCount') }}
                </LabelInput>
                <InputNumber
                    id="rating_count"
                    type="number"
                    min="0"
                    :model-value="rating_count"
                    class="w-full"
                    @update:model-value="updateRatingCount"
                />
                <InputError class="mt-1" :message="errors.rating_count" />
            </div>

            <!-- rating_avg -->
            <div class="flex flex-col items-start">
                <LabelInput for="rating_avg">
                    {{ t('ratingAvg') }}
                </LabelInput>
                <InputDecimalExt
                    id="rating_avg"
                    :model-value="rating_avg"
                    :min="0"
                    :max="5"
                    :step="0.01"
                    :fraction-digits="2"
                    class="w-full"
                    @update:model-value="updateRatingAvg"
                />
                <InputError class="mt-1" :message="errors.rating_avg" />
            </div>

            <!-- views -->
            <div class="flex flex-col items-start">
                <LabelInput for="views">
                    {{ t('views') }}
                </LabelInput>
                <InputNumber
                    id="views"
                    type="number"
                    min="0"
                    :model-value="views"
                    class="w-full"
                    @update:model-value="updateViews"
                />
                <InputError class="mt-1" :message="errors.views" />
            </div>

            <!-- likes -->
            <div class="flex flex-col items-start">
                <LabelInput for="likes">
                    {{ t('likes') }}
                </LabelInput>
                <InputNumber
                    id="likes"
                    type="number"
                    min="0"
                    :model-value="likes"
                    class="w-full"
                    @update:model-value="updateLikes"
                />
                <InputError class="mt-1" :message="errors.likes" />
            </div>
        </div>
    </div>
</template>
