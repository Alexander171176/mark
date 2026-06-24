<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import ImageEditorModal from '@/Components/Admin/UI/Image/ImageEditorModal.vue'

const { t } = useI18n()

defineProps({
    preset: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['update:images'])

const fileInput = ref(null)
const previewImages = ref([])

const editorVisible = ref(false)
const editingFile = ref(null)

const selectFiles = (event) => {
    const files = Array.from(event.target.files || [])

    if (!files.length) return

    editingFile.value = files[0]
    editorVisible.value = true

    if (fileInput.value) {
        fileInput.value.value = null
    }
}

const handleEditorSave = (payload) => {
    const file = payload.file
    const url = URL.createObjectURL(file)

    previewImages.value.push({
        file,
        url,
        order: previewImages.value.length + 1,
        alt: '',
        caption: '',
    })

    editorVisible.value = false
    editingFile.value = null

    updateImages()
}

const updateImages = () => {
    emit(
        'update:images',
        previewImages.value.map((image) => ({
            file: image.file,
            order: image.order,
            alt: image.alt,
            caption: image.caption,
        }))
    )
}

const removeImage = (index) => {
    const image = previewImages.value[index]

    if (image?.url) {
        URL.revokeObjectURL(image.url)
    }

    previewImages.value.splice(index, 1)

    previewImages.value.forEach((image, index) => {
        image.order = index + 1
    })

    updateImages()
}

const closeEditor = () => {
    editorVisible.value = false
    editingFile.value = null
}
</script>

<template>
    <div class="multi-image-preset-upload">
        <LabelInput :value="t('uploadNewImages')" />

        <input
            ref="fileInput"
            type="file"
            accept="image/png,image/jpeg,image/webp"
            class="block w-full text-md text-gray-700 dark:text-gray-100
                   file:mr-4 file:py-0.5 file:px-2 file:border-0
                   file:text-sm file:font-semibold
                   file:bg-violet-600 file:text-white
                   hover:file:bg-violet-700"
            @change="selectFiles"
        />

        <div
            v-if="previewImages.length"
            class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
        >
            <div
                v-for="(image, index) in previewImages"
                :key="index"
                class="relative border border-slate-500 rounded-sm py-0.5 px-2"
            >
                <img
                    :src="image.url"
                    :alt="t('view')"
                    class="h-40 w-full object-cover"
                />

                <input
                    v-model="image.order"
                    @input="updateImages"
                    :placeholder="t('sort')"
                    class="w-full my-2 py-0.5 px-2 text-sm font-semibold
                           border border-slate-500 rounded
                           dark:bg-cyan-800 dark:text-slate-100"
                />

                <input
                    v-model="image.alt"
                    @input="updateImages"
                    :placeholder="t('seoAltImage')"
                    class="w-full my-2 py-0.5 px-2 text-sm font-semibold
                           border border-slate-500 rounded
                           dark:bg-cyan-800 dark:text-slate-100"
                />

                <input
                    v-model="image.caption"
                    @input="updateImages"
                    :placeholder="t('seoTitleImage')"
                    class="w-full mb-2 py-0.5 px-2 text-sm font-semibold
                           border border-slate-500 rounded
                           dark:bg-cyan-800 dark:text-slate-100"
                />

                <button
                    type="button"
                    @click.prevent="removeImage(index)"
                    class="absolute top-2 right-2 bg-rose-500
                           hover:bg-rose-700 text-white rounded-sm p-1"
                >
                    ✕
                </button>
            </div>
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
