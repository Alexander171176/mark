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

        <div v-if="localImages.length" class="mt-2">
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
                                       bg-blue-600 hover:bg-blue-700 text-white
                                       flex items-center justify-center gap-1"
                                @click.prevent="selectReplaceFile(index)"
                            >
                                <svg
                                    class="w-3 h-3 fill-current"
                                    viewBox="0 0 512 512">
                                    <path d="M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z" />
                                </svg>
                                <span>
                                {{ t('replace') }}
                                </span>
                            </button>

                            <button
                                type="button"
                                class="w-full text-xs font-semibold px-2 py-1 rounded-sm
                                       bg-rose-500 hover:bg-rose-700 text-white
                                       flex items-center justify-center gap-1"
                                @click.prevent="removeImage(index)"
                            >
                                <svg
                                    class="w-3 h-3 fill-current"
                                    viewBox="0 0 448 512">
                                    <path d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z" />
                                </svg>
                                <span>
                                    {{ t('delete') }}
                                </span>
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
