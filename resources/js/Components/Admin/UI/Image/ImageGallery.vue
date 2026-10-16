<script setup>
/** Универсальная галерея для товаров и других сущностей с массивом images. */
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    images: { type: Array, default: () => [] },
    alt: { type: String, default: 'Изображение' },
    fallback: { type: String, default: '/storage/default-image.png' },
    heightClass: { type: String, default: 'h-40' },
    fit: { type: String, default: 'cover' },
})

const current = ref(0)
const failed = ref({})
const normalized = computed(() => [...props.images]
    .sort((a, b) =>
        Number(a?.order ?? a?.pivot?.order ?? 0) -
        Number(b?.order ?? b?.pivot?.order ?? 0))
    .map((image, index) => typeof image === 'string'
        ? { id: index, url: image, alt: props.alt, caption: '' }
        : {
            id: image?.id ?? index,
            url: image?.webp_url || image?.thumb_url || image?.image_url || image?.url || '',
            alt: image?.alt || props.alt,
            caption: image?.caption || '',
        })
    .filter(image => image.url))

watch(() => props.images, () => { current.value = 0; failed.value = {} })
const active = computed(() => normalized.value[current.value] || null)
const activeUrl = computed(() =>
    active.value && !failed.value[current.value] ? active.value.url : props.fallback)
const onError = () => {
    if (active.value && !failed.value[current.value]) {
        failed.value = { ...failed.value, [current.value]: true }
    }
}
</script>

<template>
    <div class="w-full min-w-0" @pointerdown.stop>
        <div class="relative flex w-full items-center justify-center overflow-hidden rounded
                    bg-slate-100 dark:bg-slate-900"
             :class="heightClass">
            <img
                v-if="activeUrl"
                :key="`${current}-${activeUrl}`"
                :src="activeUrl"
                :alt="active?.alt || alt"
                :title="active?.caption || active?.alt || alt"
                class="h-full w-full"
                :class="fit === 'contain' ? 'object-contain' : 'object-cover'"
                loading="lazy"
                @error="onError"
            />
            <span v-else class="text-center text-xs text-slate-500">
                {{ t('noImage') }}
            </span>
        </div>
        <div
            v-if="normalized.length > 1"
            class="mt-1 flex flex-wrap items-center justify-center gap-1 py-1"
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
                       focus-visible:outline focus-visible:outline-2
                       focus-visible:outline-blue-500"
                :class="current === index ? 'scale-125 bg-blue-600'
                : 'bg-slate-300 hover:bg-slate-500 dark:bg-slate-600'"
                @click.stop.prevent="current = index"
                @mousedown.stop
            />
        </div>
    </div>
</template>
