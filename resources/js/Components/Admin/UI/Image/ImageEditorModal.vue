<script setup>
import { computed, ref, watch, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import {
    Cropper,
    CircleStencil,
    RectangleStencil,
} from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

import SecondaryButton from '@/Components/Admin/UI/Buttons/SecondaryButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import WarningButton from '@/Components/Admin/UI/Buttons/WarningButton.vue'
import GreenButton from '@/Components/Admin/UI/Buttons/GreenButton.vue'

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
                class="w-full max-w-3xl max-h-[95vh] flex flex-col rounded-sm
                       bg-white dark:bg-slate-800
                       border border-slate-300 dark:border-slate-600
                       shadow-xl overflow-hidden"
            >
                <div
                    class="shrink-0 flex items-center justify-between px-4 py-1
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
                        class="text-slate-500 hover:text-red-500"
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
                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        <SecondaryButton
                            type="button"
                            class="py-2.5 rounded-sm"
                            :title="t('rotateImageLeft')"
                            :disabled="!canRotateImage"
                            @click.prevent="rotateImageLeft"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                     viewBox="0 0 448 512">
                                    <path
                                        d="M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z"
                                    />
                                </svg>
                            </template>
                        </SecondaryButton>
                        <GreenButton
                            type="button"
                            class="py-2.5 rounded-sm"
                            :title="t('rotateCropFrame')"
                            :disabled="!canRotateCropFrame"
                            @click.prevent="rotateCropFrame"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                     viewBox="0 0 576 512">
                                    <path
                                        d="M561.938 158.06L417.94 14.092C387.926-15.922 336 5.097 336 48.032v57.198c-42.45 1.88-84.03 6.55-120.76 17.99-35.17 10.95-63.07 27.58-82.91 49.42C108.22 199.2 96 232.6 96 271.94c0 61.697 33.178 112.455 84.87 144.76 37.546 23.508 85.248-12.651 71.02-55.74-15.515-47.119-17.156-70.923 84.11-78.76V336c0 42.993 51.968 63.913 81.94 33.94l143.998-144c18.75-18.74 18.75-49.14 0-67.88zM384 336V232.16C255.309 234.082 166.492 255.35 206.31 376 176.79 357.55 144 324.08 144 271.94c0-109.334 129.14-118.947 240-119.85V48l144 144-144 144zm24.74 84.493a82.658 82.658 0 0 0 20.974-9.303c7.976-4.952 18.286.826 18.286 10.214V464c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h132c6.627 0 12 5.373 12 12v4.486c0 4.917-2.987 9.369-7.569 11.152-13.702 5.331-26.396 11.537-38.05 18.585a12.138 12.138 0 0 1-6.28 1.777H54a6 6 0 0 0-6 6v340a6 6 0 0 0 6 6h340a6 6 0 0 0 6-6v-25.966c0-5.37 3.579-10.059 8.74-11.541z"
                                    />
                                </svg>
                            </template>
                        </GreenButton>
                        <SecondaryButton
                            type="button"
                            class="py-2.5 rounded-sm"
                            :title="t('rotateImageRight')"
                            :disabled="!canRotateImage"
                            @click.prevent="rotateImageRight"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                     viewBox="0 0 448 512">
                                    <path
                                        d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"
                                    />
                                </svg>
                            </template>
                        </SecondaryButton>
                    </div>
                </div>
                <div
                    class="shrink-0 flex items-center justify-center gap-3 px-3 sm:px-4 py-3
                           border-t border-slate-300 dark:border-slate-600"
                >
                    <WarningButton
                        type="button"
                        class="text-sm py-0.5 rounded-sm"
                        @click.prevent="closeModal"
                    >
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                 viewBox="0 0 512 512">
                                <path
                                    d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z"
                                />
                            </svg>
                        </template>
                        <span>
                            {{ t('cancel') }}
                        </span>
                    </WarningButton>
                    <PrimaryButton
                        type="button"
                        class="text-sm py-0.5 rounded-sm"
                        @click.prevent="saveImage"
                    >
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                 viewBox="0 0 448 512">
                                <path
                                    d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM272 80v80H144V80h128zm122 352H54a6 6 0 0 1-6-6V86a6 6 0 0 1 6-6h42v104c0 13.255 10.745 24 24 24h176c13.255 0 24-10.745 24-24V83.882l78.243 78.243a6 6 0 0 1 1.757 4.243V426a6 6 0 0 1-6 6zM224 232c-48.523 0-88 39.477-88 88s39.477 88 88 88 88-39.477 88-88-39.477-88-88-88zm0 128c-22.056 0-40-17.944-40-40s17.944-40 40-40 40 17.944 40 40-17.944 40-40 40z"
                                />
                            </svg>
                        </template>
                        <span>
                            {{ t('save') }}
                        </span>
                    </PrimaryButton>
                </div>
                <div class="mb-3 mx-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div
                        class="p-2 rounded-sm border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-700
                                   text-xs sm:text-sm text-slate-700 dark:text-slate-100"
                    >
                        <div class="font-semibold">
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
                        <div class="font-semibold">
                            {{ t('rotation') }}
                        </div>
                        <div>
                            {{ imageRotation }}°
                        </div>
                    </div>
                    <div
                        class="p-2 rounded-sm border border-slate-300 dark:border-slate-600
                                   bg-slate-50 dark:bg-slate-700
                                   text-xs sm:text-sm text-slate-700 dark:text-slate-100"
                    >
                        <div class="font-semibold">
                            {{ t('typeSize') }}
                        </div>
                        <div>
                            {{ rotatedTargetWidth }}×{{ rotatedTargetHeight }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
