<script setup>
import {defineOptions, defineProps, defineEmits, ref, watch} from 'vue';
import draggable from 'vuedraggable';
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue';
import IconEdit from "@/Components/Admin/Buttons/IconEdit.vue";
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();

defineOptions({ name: 'LearningCategoryTreeItem' });
const props = defineProps({
    learningCategories: Object,
    level: Number,
    selectedLearningCategories: Array,
});

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'request-drag-end'
]);

const isExpanded = ref(true);

// Логируем входящие props
// console.log('[LearningCategoryTreeItem] learningCategories:', props.learningCategories);
// console.log('[LearningCategoryTreeItem] level:', props.level);
// console.log('[LearningCategoryTreeItem] selectedCategories:', props.selectedLearningCategories);

// Слежение за изменениями props.category
watch(() => props.learningCategories, (newVal, oldVal) => {
    // console.log('[LearningCategoryTreeItem] props.learningCategories изменился:', newVal);
}, {deep: true});

// Слежение за selectedLearningCategories
watch(() => props.selectedLearningCategories, (newVal, oldVal) => {
    // console.log('[CategoryTreeItem] selectedLearningCategories изменился:', newVal);
}, {deep: true});

const handleInnerDragEnd = (event) => {
    // console.log('[LearningCategoryTreeItem] handleInnerDragEnd event:', event);
    emits('request-drag-end', event);
};

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
    // console.log('[LearningCategoryTreeItem] isExpanded:', isExpanded.value);
};

// Функция для выбора изображения с наименьшим значением order
const getPrimaryImage = (learningCategories) => {
    if (learningCategories.images && learningCategories.images.length) {
        // Создаем копию массива и сортируем по возрастанию order
        return [...learningCategories.images].sort((a, b) => a.order - b.order)[0];
    }
    return null;
};
</script>

<template>
    <div>
        <!-- Элемент категории -->
        <div class="category-item mb-1" :style="{ marginLeft: level * 20 + 'px' }">

            <div class="flex items-center justify-between py-1 px-2
                        border border-gray-400 rounded-sm
                        bg-white dark:bg-slate-600
                        hover:bg-slate-50 dark:hover:bg-slate-700
                        transition duration-150 ease-in-out">

                <div class="flex items-center space-x-2 flex-grow min-w-0">

                    <span class="handle cursor-move mr-1 flex-shrink-0"
                          :title="t('dragDrop')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                             class="w-4 h-4">
                            <path class="fill-current text-sky-500 dark:text-sky-200"
                                  d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 96-96 0 0-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6l0-32 96 0 0 96-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-96 96 0 0 32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6l0 32-96 0 0-96 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"/>
                        </svg>
                    </span>

                    <button v-if="learningCategories.children
                    && learningCategories.children.length"
                            :title="isExpanded ? t('collapse') : t('expand')"
                            @click="toggleExpand"
                            class="flex-shrink-0 text-slate-900 hover:text-red-500
                                   dark:text-slate-100 dark:hover:text-red-200">

                        <svg class="w-5 h-5 transform transition-transform duration-150"
                             :class="{ 'rotate-90': isExpanded }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>

                    </button>

                    <span v-else class="w-4 h-4 inline-block flex-shrink-0"></span>

                    <span class="w-8 font-semibold text-sm
                                text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0">
                        {{ learningCategories.id }}
                    </span>

                    <a
                        :href="`/school/tracks/${encodeURIComponent(learningCategories.slug)}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm font-semibold
                               text-sky-700 dark:text-sky-200
                               hover:text-amber-700 dark:hover:text-amber-200
                               hover:underline line-clamp-2 text-center"
                    >
                        {{ learningCategories.name }}
                    </a>

                    <!-- Кол-во курсов -->
                    <span
                        class="shrink-0 px-1.5 py-0.5 rounded-sm
                               border border-slate-300 dark:border-slate-600
                               bg-slate-100 dark:bg-slate-700
                               flex items-center justify-center gap-1"
                        :title="`${t('courses')}: ${learningCategories.courses_count ?? 0}`"
                    >
                        <svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                        </svg>
                        <span class="text-[10px] text-slate-700 dark:text-slate-200">
                            {{ learningCategories.courses_count ?? 0 }}
                        </span>
                    </span>

                </div>

                <!-- Правая часть -->
                <div class="flex items-center space-x-1 flex-shrink-0 ml-4">
                    <!-- Количество просмотров -->
                    <div class="flex items-center gap-1">
                        <!-- Views -->
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                            <path class="fill-current text-blue-600 dark:text-blue-300"
                                  d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                        </svg>
                        <div class="w-7 font-semibold text-[10px]
                                    text-slate-700 dark:text-slate-200"
                             :title="t('views')">
                            {{ learningCategories.views ?? 0 }}
                        </div>
                    </div>

                    <!-- Локаль -->
                    <div class="text-xs ml-1 px-1.5 py-0.5 rounded-sm
                               border border-gray-400 flex-shrink-0"
                          :class="learningCategories.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'">
                        {{ learningCategories.locale.toUpperCase() }}
                    </div>

                    <!-- Изображение -->
                    <div class="flex justify-center">
                        <template
                            v-if="learningCategories.images
                            && learningCategories.images.length">
                            <img
                                :src="getPrimaryImage(learningCategories).webp_url
                                || getPrimaryImage(learningCategories).url"
                                :alt="getPrimaryImage(learningCategories).alt
                                || t('defaultImageAlt')"
                                :title="getPrimaryImage(learningCategories).caption
                                || t('postImage')"
                                class="h-6 w-8 object-cover rounded-sm
                                       border border-slate-400 dark:border-slate-200 p-0.5"
                            >
                        </template>
                        <template v-else>
                            <img
                                src="/storage/learning_category_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="h-6 w-8 object-cover rounded-sm
                                       border border-slate-400 dark:border-slate-200 p-0.5"
                            >
                        </template>
                    </div>

                    <ActivityToggle
                        :isActive="learningCategories.activity"
                        @toggle-activity="$emit('toggle-activity', learningCategories)"
                        :title="learningCategories.activity ? t('enabled') : t('disabled')"/>
                    <IconEdit :href="route('admin.learningCategories.edit',
                    { learningCategory: learningCategories.id })"/>
                    <DeleteIconButton @click.stop="$emit('delete', learningCategories)"/>
                    <div class="pl-1.5">
                    <input type="checkbox"
                           :checked="selectedLearningCategories.includes(learningCategories.id)"
                           @change="$emit('toggle-select', learningCategories.id)"
                           class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"/>
                    </div>
                </div>
            </div>
        </div>

        <!-- Дочерние элементы -->
        <div
            v-show="isExpanded && learningCategories.children
            && learningCategories.children.length"
            class="children-container mt-1">

            <draggable v-model="learningCategories.children"
                       tag="div"
                       item-key="id"
                       handle=".handle"
                       group="learningCategories"
                       @end="handleInnerDragEnd"
                       class="category-tree-children"
                       :data-parent-id="learningCategories.id">

                <template #item="{ element: childLearningCategory }">

                    <LearningCategoryTreeItem :learningCategories="childLearningCategory"
                                              :level="level + 1"
                                              :selected-learning-categories="selectedLearningCategories"
                                              @toggle-activity="(p) => $emit('toggle-activity', p)"
                                              @delete="(p) => $emit('delete', p)"
                                              @toggle-select="(id) => $emit('toggle-select', id)"
                                              @request-drag-end="handleInnerDragEnd"/>

                </template>

            </draggable>
        </div>
    </div>
</template>
