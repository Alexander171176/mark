<script setup>
import { ref, defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import Header from '@/Partials/User/Header.vue';
import Sidebar from '@/Partials/User/Sidebar.vue';
import Footer from '@/Partials/User/Footer.vue';
import WidgetPanel from '@/Partials/User/WidgetPanel.vue';
import ScrollButtons from '@/Components/Admin/UI/Scroll/ScrollButtons.vue';

// Импорт изображения
import authImage from '../../images/auth-image.jpg';

defineProps({
    title: String,
});


const sidebarOpen = ref(false);
const showingNavigationDropdown = ref(false);

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
                <div class="max-w-7xl mx-auto py-2 px-1 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-grow bg-center"
                  :style="{ backgroundImage: `url(${authImage})`, backgroundAttachment: 'fixed' }">
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
