<script setup>
import {ref, watch, onMounted, onUnmounted, computed} from 'vue';
import { useI18n } from 'vue-i18n';
import Draggable from 'vuedraggable';
import DraggableSidebarLink from '@/Components/Admin/Links/DraggableSidebarLink.vue';
import {usePage} from "@inertiajs/vue3";

const { siteSettings } = usePage().props;
const props = defineProps({
    expanded: Boolean
});

// Реф для хранения состояния темного режима (true, если активен)
const isDarkMode = ref(false);
let observer;

// Функция для проверки наличия класса "dark" на <html>
const checkDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
};

// При монтировании компонента запускаем первоначальную проверку и устанавливаем MutationObserver
onMounted(() => {
    checkDarkMode();
    observer = new MutationObserver(checkDarkMode);
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// При размонтировании отключаем наблюдатель
onUnmounted(() => {
    if (observer) observer.disconnect();
});

const colorTextActive = computed(() => {
    return isDarkMode.value
        ? (siteSettings.AdminSidebarDarkActiveText || 'text-yellow-200')
        : (siteSettings.AdminSidebarLightActiveText || 'text-yellow-200');
});

const emit = defineEmits(['update:pageLinks']);
const { t } = useI18n();

const pageLinks = ref(JSON.parse(localStorage.getItem('pageLinks')) || [
    'admin',
]);

const handleDragEnd = () => {
    localStorage.setItem('pageLinks', JSON.stringify(pageLinks.value));
    emit('update:pageLinks', pageLinks.value);
};

watch(pageLinks, (newVal) => {
    localStorage.setItem('pageLinks', JSON.stringify(newVal));
    emit('update:pageLinks', newVal);
});

onMounted(() => {
    pageLinks.value = JSON.parse(localStorage.getItem('pageLinks')) || pageLinks.value;
});
</script>

<template>
    <Draggable v-model="pageLinks" @end="handleDragEnd" itemKey="id" group="links"
               tag="ul" class="pb-2">
        <template #item="{ element }">
            <DraggableSidebarLink :id="element" :expanded="expanded" />
        </template>
    </Draggable>
</template>
