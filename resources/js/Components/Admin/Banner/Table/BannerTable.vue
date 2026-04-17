<script setup>
import { defineProps, defineEmits, watch, ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'
import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'

const { t } = useI18n();

/** Входящие пропсы */
const props = defineProps({
    banners: Array,
    selectedBanners: Array,
    isAdmin: { type: Boolean, default: false },
});

/** обработчики */
const emits = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
]);

/** Локальная копия для vuedraggable */
const localBanners = ref([]);

watch(() => props.banners, (newVal) => {
    localBanners.value = JSON.parse(JSON.stringify(newVal || []));
}, { immediate: true, deep: true });

/** draggable end */
const handleDragEnd = () => {
    // Отправляем НОВЫЙ ПОРЯДОК ID из локального массива
    const newOrderIds = localBanners.value.map(banner => banner.id);
    emits('update-sort-order', newOrderIds); // Отправляем массив ID
};

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked;
    const ids = localBanners.value.map(r => r.id);
    emits('toggle-all', { ids, checked });
};

/** Основное изображение (минимальный order) */
const getPrimaryImage = (banner) => {
    if (banner.images && banner.images.length) {
        // Создаем копию массива и сортируем по возрастанию order
        return [...banner.images].sort((a, b) => a.order - b.order)[0];
    }
    return null;
};

const selectedCount = computed(() => props.selectedBanners.length)

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d)) return ''
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

/** имя и email автора */
const ownerName = (banner) => banner?.owner?.name || t('noData')
const ownerEmail = (banner) => banner?.owner?.email || ''

/** title автора */
const ownerTitle = (banner) => {
    const o = banner?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** путь изображения автора */
const ownerAvatar = (banner) => {
    return banner?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

/** бейджи модерации */
const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300'
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300'
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300'
    }
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

        <!-- Верхняя панель: выбранные + чекбокс "выбрать все" -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedBanners.length }}
            </div>

            <label
                v-if="localBanners.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table v-if="banners.length > 0"
                   class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                            border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-1 py-1 w-px">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
                            <path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path>
                            <path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path>
                        </svg>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('owner')">
                            <svg  class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path>
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path>
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path>
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path>
                                <path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap w-px">
                        <div class="flex justify-center" :title="t('localization')">
                            <svg class="w-8 h-8 fill-current shrink-0" viewBox="0 0 640 512">
                                <path d="M0 128C0 92.7 28.7 64 64 64l192 0 48 0 16 0 256 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64l-256 0-16 0-48 0L64 448c-35.3 0-64-28.7-64-64L0 128zm320 0l0 256 256 0 0-256-256 0zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1 73.6 0 8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276l-38 0 19-42.8zM448 164c11 0 20 9 20 20l0 4 44 0 16 0c11 0 20 9 20 20s-9 20-20 20l-2 0-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45L448 228l-72 0c-11 0-20-9-20-20s9-20 20-20l52 0 0-4c0-11 9-20 20-20z"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('title') }}</div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('url') }}</div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('show') }}</div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('status') }}</div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('actions') }}</div>
                    </th>
                    <th class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll"/>
                        </div>
                    </th>
                </tr>
                </thead>
                <draggable
                    tag="tbody"
                    v-model="localBanners"
                    @end="handleDragEnd"
                    itemKey="id"
                    handle=".handle"
                >
                    <template #item="{ element: banner }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">
                            <td class="px-1 py-1 text-center cursor-move handle w-px">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap w-px">
                                <div class="text-center text-blue-600 dark:text-blue-200">
                                    {{ banner.id }}
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(banner)"
                                        :title="ownerTitle(banner)"
                                        class="h-6 w-6 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                        :alt="t('author')"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap w-px">
                                <div
                                    class="text-xs px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="banner.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ banner.locale }}
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10">
                                <div class="flex justify-center">
                                    <template v-if="banner.images && banner.images.length">
                                        <img
                                            :src="getPrimaryImage(banner).webp_url || getPrimaryImage(banner).url"
                                            :alt="getPrimaryImage(banner).alt || t('defaultImageAlt')"
                                            :title="getPrimaryImage(banner).caption || t('postImage')"
                                            class="h-6 w-6 object-cover rounded-sm"
                                        >
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/banner_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-8 w-8 object-cover rounded-sm"
                                        >
                                    </template>
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                                <div class="text-left text-sky-700 dark:text-sky-200">
                                    {{ banner.title }}
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                                <div class="text-left">
                                    <a :href="banner.link"
                                       class="text-violet-600 dark:text-violet-200 hover:underline
                                              hover:text-violet-800 dark:hover:text-violet-50"
                                       target="_blank" rel="noopener noreferrer">
                                        {{ banner.link }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <LeftToggle
                                        :isActive="banner.left"
                                        @toggle-left="$emit('toggle-left', banner)"
                                        :title="banner.left ? t('enabled') : t('disabled')"/>
                                    <MainToggle
                                        :isActive="banner.main"
                                        @toggle-main="$emit('toggle-main', banner)"
                                        :title="banner.main ? t('enabled') : t('disabled')"/>
                                    <RightToggle
                                        :isActive="banner.right"
                                        @toggle-right="$emit('toggle-right', banner)"
                                        :title="banner.right ? t('enabled') : t('disabled')"/>
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                        <span
                                            class="text-[10px] px-1 py-1 rounded-sm
                                                   border font-semibold"
                                            :class="moderationBadge(banner.moderation_status).class"
                                            :title="banner.moderation_note && banner.moderated_at
                                ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]`
                                : null"
                                        >
                                         {{ moderationBadge(banner.moderation_status).text }}
                                        </span>
                                    <!-- Модерация (только админ) -->
                                    <ModerationButton
                                        :isAdmin="isAdmin"
                                        :status="banner?.moderation_status ?? 0"
                                        :initialNote="banner?.moderation_note || ''"
                                        mode="toggle"
                                        @submit="({ status, note }) =>
                                            $emit('approve', banner, status, note)"
                                    />
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <ActivityToggle :isActive="banner.activity"
                                                    @toggle-activity="$emit('toggle-activity', banner)"
                                                    :title="banner.activity ? t('enabled') : t('disabled')"/>
                                    <IconEdit :href="route('admin.banners.edit', banner.id)" />
                                    <DeleteIconButton @delete="$emit('delete', banner.id)"/>
                                </div>
                            </td>
                            <td class="px-1 py-1 first:pl-10 last:pr-10 whitespace-nowrap">
                                <div class="text-center">
                                    <input type="checkbox"
                                           :checked="selectedBanners.includes(banner.id)"
                                           @change="$emit('toggle-select', banner.id)"/>
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>
            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
