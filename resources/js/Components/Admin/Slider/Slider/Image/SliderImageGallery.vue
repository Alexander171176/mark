<script setup>
/** Универсальная мини-галерея изображений с точками навигации. */
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

/** Локализация и уведомления */
const { t } = useI18n()

const props = defineProps({
    images: { type: Array, default: () => [] },
    alt: { type: String, default: 'Изображение' },
    heightClass: { type: String, default: 'h-40' },
})

const current = ref(0)
const failed = ref({})
const normalized = computed(() => props.images
    .map((image, index) => {
        if (typeof image === 'string') return { id: index, url: image, alt: props.alt }
        return {
            id: image?.id ?? index,
            url: image?.url || image?.desktop_url || image?.image_url || image?.thumb_url || '',
            alt: image?.alt || props.alt,
            caption: image?.caption || '',
        }
    })
    .filter(image => image.url))

watch(() => props.images, () => {
    current.value = 0
    failed.value = {}
})

const select = index => { current.value = index }
const active = computed(() => normalized.value[current.value] || null)
const onError = () => {
    if (active.value) failed.value = { ...failed.value, [current.value]: true }
}
</script>

<template>
    <div class="w-full min-w-0">
        <div :class="heightClass"
             class="relative flex w-full items-center justify-center overflow-hidden
                    rounded bg-slate-100 dark:bg-slate-900">
            <img
                v-if="active && !failed[current]"
                :key="`${current}-${active.url}`"
                :src="active.url"
                :alt="active.alt"
                :title="active.caption || active.alt"
                class="h-full w-full object-cover"
                loading="lazy"
                @error="onError"
            />
            <span
                v-else
                  class="text-center text-xs text-slate-500 dark:text-slate-400">
                {{ t('noImage') }}
            </span>
        </div>
        <div
            v-if="normalized.length > 1"
            class="mt-1 flex flex-wrap items-center justify-center gap-1"
            role="group"
            :aria-label="t('addImage')">
            <button
                v-for="(image, index) in normalized"
                :key="`${image.id}-${index}`"
                type="button"
                :aria-label="`${t('image')} ${index + 1} - ${normalized.length}`"
                :aria-pressed="current === index"
                :title="image.caption || `${t('image')} ${index + 1}`"
                class="h-2 w-2 rounded-full border border-slate-500
                       transition-transform focus-visible:outline
                       focus-visible:outline-2 focus-visible:outline-blue-500"
                :class="current === index ? 'scale-125 bg-blue-600'
                : 'bg-slate-300 hover:bg-slate-500 dark:bg-slate-600'"
                @click.stop.prevent="select(index)"
            />
        </div>
    </div>
</template>
