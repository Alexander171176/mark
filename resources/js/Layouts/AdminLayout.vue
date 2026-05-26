<script setup>
import {ref, defineProps, watch} from 'vue';
import { Head } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification'; // Импортируем useToast
import Header from '@/Partials/Admin/Header.vue';
import Sidebar from '@/Partials/Admin/Sidebar.vue';
import Footer from '@/Partials/Admin/Footer.vue';
import WidgetPanel from '@/Partials/Admin/WidgetPanel.vue';
import ScrollButtons from '@/Components/Admin/UI/Scroll/ScrollButtons.vue';
import {Container as page} from "vue-smooth-dnd";

defineProps({
    title: String,
});

const sidebarOpen = ref(false);
const showingNavigationDropdown = ref(false);

const toast = useToast(); // Получаем экземпляр toast

// Наблюдаем за изменением flash-сообщений в props
watch(() => page.props.flash, (flashMessages) => {
    if (flashMessages) {
        if (flashMessages.success) {
            toast.success(flashMessages.success);
        }
        if (flashMessages.error) {
            toast.error(flashMessages.error);
        }
        if (flashMessages.warning) {
            toast.warning(flashMessages.warning);
        }
        if (flashMessages.info) {
            toast.info(flashMessages.info);
        }
        // Можно добавить обработку других типов сообщений
    }
}, {
    deep: true, // Наблюдаем за изменениями внутри объекта flash
    // immediate: true // Раскомментируйте, если нужно проверить flash при первой загрузке лэйаута
});
</script>

<template>
    <Head :title="title" />
    <div class="flex flex-row h-screen overflow-hidden">
        <!-- Sidebar -->
        <Sidebar :sidebar-open="sidebarOpen" @close-sidebar="sidebarOpen = false" />

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            <!-- Header -->
            <Header :showing-navigation-dropdown="showingNavigationDropdown"
                    @toggle-navigation-dropdown="showingNavigationDropdown = !showingNavigationDropdown" />

            <!-- TitlePage Page Heading -->
            <header v-if="$slots.header" class="dark:bg-slate-700 bg-slate-50 shadow">
                <div class="max-w-7xl mx-auto py-2 px-1 sm:px-6 lg:px-8
                            border-b border-slate-300 dark:border-slate-700">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-grow bg-center border-l border-r border-slate-400">
                <slot />
            </main>

            <!-- Footer -->
            <Footer />

            <ScrollButtons />

        </div>

        <!-- Widget Panel -->
        <WidgetPanel />

    </div>
</template>

<style scoped>
main {
    width: 100%;
    height: auto;
    background-size: contain; /* изменяет размер изображения, чтобы оно полностью отображалось */
    background-repeat: no-repeat; /* предотвращает повторение изображения */
    background-position: center;
    background-attachment: fixed; /* делает фон фиксированным при прокрутке */
}
</style>
