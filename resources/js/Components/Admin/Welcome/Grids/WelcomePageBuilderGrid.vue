<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import WelcomeCard from '@/Components/Admin/Welcome/WelcomeCard.vue'

const props = defineProps({
    t: { type: Function, required: true },
})

const page = usePage()

const description = computed(() =>
    page.props.laravelLang?.admin?.welcome?.pageBuilder?.home?.description ?? ''
)
</script>


<template>
    <div
        class="p-4 bg-gray-200 dark:bg-slate-700 bg-opacity-95 dark:bg-opacity-95
               grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5
               border-dashed border-y border-gray-400"
    >
        <WelcomeCard
            :href="route('admin.home-page.index')"
            :title="t('pageBuilder')"
            :description="description"
        >

        <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M17,23H4c-1.654,0-3-1.346-3-3V7c0-1.654,1.346-3,3-3h5c.552,0,1,.448,1,1s-.448,1-1,1H4c-.551,0-1,.449-1,1v13c0,.551,.449,1,1,1h13c.551,0,1-.449,1-1v-5c0-.552,.448-1,1-1s1,.448,1,1v5c0,1.654-1.346,3-3,3Z"></path>
                    <path d="M20.414,8l1.585-1.585c.378-.377,.587-.879,.587-1.414,0-.535-.208-1.037-.586-1.416l-1.585-1.585c-.377-.378-.879-.587-1.414-.587h0c-.534,0-1.037,.208-1.415,.586l-1.586,1.586,4.414,4.414Z"></path>
                    <path d="M14.586,5l-6.293,6.293c-.096,.096-.171,.21-.222,.335l-2,5c-.148,.372-.061,.796,.222,1.079,.191,.191,.447,.293,.707,.293,.125,0,.251-.023,.372-.071l5-2c.125-.05,.24-.126,.335-.222l6.293-6.293-4.414-4.414Z"></path>
                </svg>
            </template>
        </WelcomeCard>
    </div>
</template>
