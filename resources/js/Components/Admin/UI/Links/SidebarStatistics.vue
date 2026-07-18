<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import Draggable from 'vuedraggable';
import DraggableSidebarLink from '@/Components/Admin/UI/Links/DraggableSidebarLink.vue';

defineProps({
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

const emit = defineEmits(['update:pageLinks']);

const pageLinks = ref(JSON.parse(localStorage.getItem('pageLinks')) || [
    'charts',
    'analyticsVisitorLogs',
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
