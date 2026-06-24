<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import ImageEditorModal from '@/Components/Admin/UI/Image/ImageEditorModal.vue'

const { t } = useI18n()

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
    preset: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'update:images',
    'delete-image',
    'delete:image',
])

const localImages = ref([])

const fileInput = ref(null)
const replaceIndex = ref(null)

const editorVisible = ref(false)
const editingFile = ref(null)

watch(
    () => props.images,
    (images) => {
        localImages.value = images
            .map((image) => ({
                id: image.id,
                order: image.order || 0,
                url: image.url,
                alt: image.alt || '',
                caption: image.caption || '',
                file: image.file || null,
            }))
            .sort((a, b) => a.order - b.order)
    },
    { immediate: true }
)

const updateImages = () => {
    emit('update:images', localImages.value)
}

const updateOrder = () => {
    localImages.value.forEach((image, index) => {
        image.order = index + 1
    })

    updateImages()
}

const removeImage = (index) => {
    const removedImage = localImages.value[index]

    if (removedImage?.id) {
        emit('delete-image', removedImage.id)
        emit('delete:image', removedImage.id)
    }

    localImages.value.splice(index, 1)
    updateOrder()
}

const selectReplaceFile = (index) => {
    replaceIndex.value = index
    fileInput.value?.click()
}

const handleReplaceFile = (event) => {
    const file = event.target.files?.[0]

    if (!file) return

    editingFile.value = file
    editorVisible.value = true

    if (fileInput.value) {
        fileInput.value.value = null
    }
}

const handleEditorSave = (payload) => {
    if (replaceIndex.value === null) return

    const image = localImages.value[replaceIndex.value]

    if (!image) return

    if (image.url?.startsWith('blob:')) {
        URL.revokeObjectURL(image.url)
    }

    image.file = payload.file
    image.url = URL.createObjectURL(payload.file)

    editorVisible.value = false
    editingFile.value = null
    replaceIndex.value = null

    updateImages()
}

const closeEditor = () => {
    editorVisible.value = false
    editingFile.value = null
    replaceIndex.value = null
}
</script>

<template>
    <div class="multi-image-preset-edit">
        <LabelInput :value="t('editImages')" />

        <input
            ref="fileInput"
            type="file"
            accept="image/png,image/jpeg,image/webp"
            class="hidden"
            @change="handleReplaceFile"
        />

        <div v-if="localImages.length" class="mt-4">
            <draggable
                v-model="localImages"
                group="images"
                item-key="id"
                @end="updateOrder"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
            >
                <template #item="{ element, index }">
                    <div class="relative border border-slate-500 rounded-sm py-0.5 px-2">
                        <img
                            :src="element.url"
                            :alt="t('view')"
                            class="h-40 w-full object-cover"
                        />

                        <input
                            v-model="element.order"
                            @input="updateImages"
                            :placeholder="t('sort')"
                            class="w-full my-2 py-0.5 px-2 text-sm font-semibold
                                   border border-slate-500 rounded
                                   dark:bg-cyan-800 dark:text-slate-100"
                        />

                        <input
                            v-model="element.alt"
                            @input="updateImages"
                            :placeholder="t('seoAltImage')"
                            class="w-full my-2 py-0.5 px-2 text-sm font-semibold
                                   border border-slate-500 rounded
                                   dark:bg-cyan-800 dark:text-slate-100"
                        />

                        <input
                            v-model="element.caption"
                            @input="updateImages"
                            :placeholder="t('seoTitleImage')"
                            class="w-full mb-2 py-0.5 px-2 text-sm font-semibold
                                   border border-slate-500 rounded
                                   dark:bg-cyan-800 dark:text-slate-100"
                        />

                        <div class="flex justify-between gap-2 mb-2">
                            <button
                                type="button"
                                class="w-full text-xs font-semibold px-2 py-1 rounded-sm
                                       bg-blue-600 hover:bg-blue-700 text-white"
                                @click.prevent="selectReplaceFile(index)"
                            >
                                {{ t('replace') }}
                            </button>

                            <button
                                type="button"
                                class="w-full text-xs font-semibold px-2 py-1 rounded-sm
                                       bg-rose-500 hover:bg-rose-700 text-white"
                                @click.prevent="removeImage(index)"
                            >
                                {{ t('delete') }}
                            </button>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-else>
            <p class="text-sm text-left text-slate-800 dark:text-slate-100">
                {{ t('noData') }}
            </p>
        </div>

        <ImageEditorModal
            :show="editorVisible"
            :file="editingFile"
            :preset="preset"
            @save="handleEditorSave"
            @close="closeEditor"
        />
    </div>
</template>
