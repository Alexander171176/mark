<script setup>
import { computed, ref, watch, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import {
    Cropper,
    CircleStencil,
    RectangleStencil,
} from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

const { t } = useI18n()

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },

    file: {
        type: [File, null],
        default: null,
    },

    preset: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'close',
    'save',
])

const cropper = ref(null)
const imageSrc = ref(null)

const imageRotation = ref(0)
const cropRotation = ref(0)

const shape = computed(() => {
    return props.preset?.shape || 'rectangle'
})

const isCircle = computed(() => {
    return shape.value === 'circle'
})

const isRectangle = computed(() => {
    return shape.value === 'rectangle'
})

const isFixedShape = computed(() => {
    return ['square', 'circle'].includes(shape.value)
})

const targetWidth = computed(() => {
    return Number(props.preset?.width || 1)
})

const targetHeight = computed(() => {
    return Number(props.preset?.height || 1)
})

const rotatedTargetWidth = computed(() => {
    if (!isRectangle.value) {
        return targetWidth.value
    }

    return cropRotation.value === 90
        ? targetHeight.value
        : targetWidth.value
})

const rotatedTargetHeight = computed(() => {
    if (!isRectangle.value) {
        return targetHeight.value
    }

    return cropRotation.value === 90
        ? targetWidth.value
        : targetHeight.value
})

const aspectRatio = computed(() => {
    if (isFixedShape.value) {
        return 1
    }

    return rotatedTargetWidth.value / rotatedTargetHeight.value
})

const stencilComponent = computed(() => {
    return isCircle.value
        ? CircleStencil
        : RectangleStencil
})

const stencilProps = computed(() => {
    return {
        aspectRatio: aspectRatio.value,
        movable: true,
        resizable: !isFixedShape.value,
        scalable: true,
    }
})

const canRotateImage = computed(() => {
    return Boolean(props.preset?.image_rotation_enabled)
})

const canRotateCropFrame = computed(() => {
    return Boolean(props.preset?.crop_rotation_enabled) && isRectangle.value
})

const cropperKey = computed(() => {
    return [
        shape.value,
        cropRotation.value,
        rotatedTargetWidth.value,
        rotatedTargetHeight.value,
    ].join('-')
})

const resultFileName = computed(() => {
    const key = props.preset?.key || 'image'

    return `${key}.webp`
})

const normalizeAngle = (value) => {
    return ((value % 360) + 360) % 360
}

const resetState = () => {
    imageRotation.value = 0
    cropRotation.value = 0
}

const revokeImageUrl = () => {
    if (imageSrc.value) {
        URL.revokeObjectURL(imageSrc.value)
        imageSrc.value = null
    }
}

watch(
    () => props.file,
    (file) => {
        resetState()
        revokeImageUrl()

        if (!file) {
            return
        }

        imageSrc.value = URL.createObjectURL(file)
    },
    { immediate: true }
)

onBeforeUnmount(() => {
    revokeImageUrl()
})

const rotateImageLeft = () => {
    if (!canRotateImage.value) {
        return
    }

    imageRotation.value = normalizeAngle(imageRotation.value - 90)
    cropper.value?.rotate?.(-90)
}

const rotateImageRight = () => {
    if (!canRotateImage.value) {
        return
    }

    imageRotation.value = normalizeAngle(imageRotation.value + 90)
    cropper.value?.rotate?.(90)
}

const rotateCropFrame = () => {
    if (!canRotateCropFrame.value) {
        return
    }

    cropRotation.value = cropRotation.value === 0 ? 90 : 0
}

const saveImage = () => {
    const result = cropper.value?.getResult?.()

    if (!result?.canvas) {
        return
    }

    const canvas = document.createElement('canvas')
    canvas.width = rotatedTargetWidth.value
    canvas.height = rotatedTargetHeight.value

    const context = canvas.getContext('2d')

    context.drawImage(
        result.canvas,
        0,
        0,
        canvas.width,
        canvas.height
    )

    canvas.toBlob(
        (blob) => {
            if (!blob) {
                return
            }

            const file = new File(
                [blob],
                resultFileName.value,
                {
                    type: 'image/webp',
                }
            )

            emit('save', {
                blob,
                file,

                coordinates: result.coordinates,

                preset_key: props.preset.key,
                shape: shape.value,

                width: rotatedTargetWidth.value,
                height: rotatedTargetHeight.value,

                image_rotation: imageRotation.value,
                crop_rotation: cropRotation.value,
            })
        },
        'image/webp',
        0.85
    )
}

const closeModal = () => {
    emit('close')
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center
                   bg-black/70 p-2 sm:p-4"
        >
            <div
                class="w-full max-w-5xl max-h-[95vh] flex flex-col rounded-sm
                       bg-white dark:bg-slate-800
                       border border-slate-300 dark:border-slate-600
                       shadow-xl overflow-hidden"
            >
                <div
                    class="shrink-0 flex items-center justify-between px-3 sm:px-4 py-2
                           border-b border-slate-300 dark:border-slate-600"
                >
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-slate-800 dark:text-slate-100">
                            {{ t('imageEditor') }}
                        </h3>

                        <div class="text-xs text-slate-500 dark:text-slate-300">
                            {{ preset.key }} —
                            {{ rotatedTargetWidth }}×{{ rotatedTargetHeight }}
                        </div>
                    </div>

                    <button
                        type="button"
                        class="text-slate-500 hover:text-red-600"
                        @click.prevent="closeModal"
                    >
                        ✕
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-3 sm:p-4">
                    <div
                        v-if="imageSrc"
                        class="h-[45vh] min-h-[260px] max-h-[520px]
                               bg-slate-100 dark:bg-slate-900
                               border border-slate-300 dark:border-slate-600"
                    >
                        <Cropper
                            :key="cropperKey"
                            ref="cropper"
                            class="h-full"
                            :src="imageSrc"
                            :stencil-component="stencilComponent"
                            :stencil-props="stencilProps"
                        />
                    </div>

                    <div
                        v-else
                        class="h-64 flex items-center justify-center
                               text-slate-500 dark:text-slate-300"
                    >
                        {{ t('noImage') }}
                    </div>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div
                            class="p-2 rounded-sm border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-700
                                   text-xs sm:text-sm text-slate-700 dark:text-slate-100"
                        >
                            <div class="font-semibold mb-1">
                                {{ t('shape') }}
                            </div>

                            <div>
                                {{ shape }}
                            </div>
                        </div>

                        <div
                            class="p-2 rounded-sm border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-700
                                   text-xs sm:text-sm text-slate-700 dark:text-slate-100"
                        >
                            <div class="font-semibold mb-1">
                                {{ t('typeSize') }}
                            </div>

                            <div>
                                {{ rotatedTargetWidth }}×{{ rotatedTargetHeight }}
                            </div>
                        </div>

                        <div
                            class="p-2 rounded-sm border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-700
                                   text-xs sm:text-sm text-slate-700 dark:text-slate-100"
                        >
                            <div class="font-semibold mb-1">
                                {{ t('rotation') }}
                            </div>

                            <div>
                                {{ imageRotation }}°
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        <PrimaryButton
                            type="button"
                            class="text-xs py-1"
                            :disabled="!canRotateImage"
                            @click.prevent="rotateImageLeft"
                        >
                            ↺ {{ t('rotateImageLeft') }}
                        </PrimaryButton>

                        <PrimaryButton
                            type="button"
                            class="text-xs py-1"
                            :disabled="!canRotateImage"
                            @click.prevent="rotateImageRight"
                        >
                            ↻ {{ t('rotateImageRight') }}
                        </PrimaryButton>

                        <PrimaryButton
                            type="button"
                            class="text-xs py-1"
                            :disabled="!canRotateCropFrame"
                            @click.prevent="rotateCropFrame"
                        >
                            ⤾ {{ t('rotateCropFrame') }}
                        </PrimaryButton>
                    </div>
                </div>

                <div
                    class="shrink-0 flex items-center justify-center gap-3 px-3 sm:px-4 py-3
                           border-t border-slate-300 dark:border-slate-600"
                >
                    <PrimaryButton
                        type="button"
                        class="text-xs py-1"
                        @click.prevent="closeModal"
                    >
                        {{ t('cancel') }}
                    </PrimaryButton>

                    <PrimaryButton
                        type="button"
                        class="text-xs py-1"
                        @click.prevent="saveImage"
                    >
                        {{ t('save') }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Teleport>
</template>
