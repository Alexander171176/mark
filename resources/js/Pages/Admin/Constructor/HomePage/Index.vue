<script setup>
import { reactive, onMounted, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import Draggable from 'vuedraggable'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

import SectionHero from '@/Partials/Default/SectionHero.vue'
import SectionWave from '@/Partials/Default/SectionWave.vue'
import SectionFeature from '@/Partials/Default/SectionFeature.vue'
import SectionDeveloper from '@/Partials/Default/SectionDeveloper.vue'
import SectionQuickstart from '@/Partials/Default/SectionQuickstart.vue'
import SectionDemo from '@/Partials/Default/SectionDemo.vue'
import SectionQuality from '@/Partials/Default/SectionQuality.vue'
import SectionComponent from '@/Partials/Default/SectionComponent.vue'
import SectionReason from '@/Partials/Default/SectionReason.vue'

import '@/../css/vulk/admin.css'

const { t } = useI18n()
const toast = useToast()

const pageTitle = computed(() => `${t('edit')}: ${t('home')}`)

const props = defineProps({
    heroSectionId: { type: Number, required: true },
    blocks: { type: Array, required: true } // [{type,id,sort}]
})

/* ---------- maps ---------- */
const componentMap = {
    wave: SectionWave,
    feature: SectionFeature,
    developer: SectionDeveloper,
    quickstart: SectionQuickstart,
    demo: SectionDemo,
    quality: SectionQuality,
    component: SectionComponent,
    reason: SectionReason
}
const editRouteMap = {
    wave: 'admin.home-page.wave.sections.edit',
    feature: 'admin.home-page.feature.sections.edit',
    developer: 'admin.home-page.developer.sections.edit',
    quickstart: 'admin.home-page.quickstart.sections.edit',
    demo: 'admin.home-page.demo.sections.edit',
    quality: 'admin.home-page.quality.sections.edit',
    component: 'admin.home-page.component.sections.edit',
    reason: 'admin.home-page.reason.sections.edit'
}
const displayName = (type) =>
    ({
        wave: 'Wave',
        feature: 'Feature',
        developer: 'Developer',
        quickstart: 'Quickstart',
        demo: 'Demo',
        quality: 'Quality',
        component: 'Component',
        reason: 'Reason'
    }[type] || type)

/* ---------- state ---------- */
const state = reactive({
    items: [...props.blocks].sort((a, b) => a.sort - b.sort),
    saving: false,
    // key = `${type}:${id}` -> true/false
    collapsed: {}
})
const keyOf = (b) => `${b.type}:${b.id}`

/* ---------- localStorage (collapsed) ---------- */
const storageKey = `hp:index:collapsed:${props.heroSectionId}`

const loadCollapsed = () => {
    if (typeof window === 'undefined') return
    try {
        const raw = window.localStorage.getItem(storageKey)
        if (!raw) return
        const saved = JSON.parse(raw)
        if (!saved || typeof saved !== 'object') return

        // фильтруем только валидные ключи из текущего набора блоков
        const valid = new Set(state.items.map(keyOf))
        Object.entries(saved).forEach(([k, v]) => {
            if (valid.has(k)) state.collapsed[k] = !!v
        })
    } catch {}
}

const saveCollapsed = () => {
    if (typeof window === 'undefined') return
    try {
        window.localStorage.setItem(storageKey, JSON.stringify(state.collapsed))
    } catch {}
}

onMounted(loadCollapsed)
watch(() => state.collapsed, saveCollapsed, { deep: true })

// если сервер вернет новый порядок/набор блоков (реактивная подмена props.blocks)
watch(
    () => props.blocks,
    (list) => {
        state.items = [...list].sort((a, b) => a.sort - b.sort)
        // подчистим невалидные ключи
        const valid = new Set(state.items.map(keyOf))
        Object.keys(state.collapsed).forEach((k) => {
            if (!valid.has(k)) delete state.collapsed[k]
        })
    },
    { deep: true }
)

/* ---------- ui actions ---------- */
const toggleCollapse = (b) => {
    const k = keyOf(b)
    state.collapsed[k] = !state.collapsed[k]
}

const saveOrder = () => {
    const payload = state.items.map((b, i) => ({
        id: b.id,
        type: b.type,
        sort: (i + 1) * 10,
    }))
    const prev = [...state.items]
    state.saving = true

    router.put(
        route('admin.home-page.sort.update'),
        { blocks: payload },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // успешный ответ (в т.ч. с флешем от бэка)
                toast.success('Порядок секций сохранён')
            },
            onError: (errors) => {
                // 422/валидация/прочие ошибки
                state.items = prev
                toast.error(errors?.general || errors?.blocks || 'Не удалось сохранить порядок секций')
            },
            onFinish: () => {
                state.saving = false
                // зафиксируем локально новые sort-значения
                state.items = state.items.map((b, i) => ({ ...b, sort: (i + 1) * 10 }))
            },
        }
    )
}

const onDragEnd = () => saveOrder()

const toggleSectionActivity = (b) => {
    const newVal = !b.activity

    router.put(
        route('admin.home-page.activity.update'),
        { id: b.id, type: b.type, activity: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // оптимистично обновим локально
                b.activity = newVal
                const name = displayName(b.type)
                toast.success(`${name}: ${newVal ? t('enabled') : t('disabled')}`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity || errors?.general || 'Не удалось обновить активность секции'
                )
            }
        }
    )
}

</script>

<template>
    <AdminLayout :title="pageTitle">
        <template #header>
            <TitlePage>{{ pageTitle }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-2 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- HERO фиксирован сверху -->
                <div class="relative">
                    <!-- оверлей с кнопкой -->
                    <div class="absolute top-2 right-2 z-50 pointer-events-none">
                        <IconEdit
                            :href="route('admin.home-page.hero.sections.edit',
                            { section: props.heroSectionId })"
                            class="pointer-events-auto w-fit"
                        />
                    </div>

                    <!-- контент секции -->
                    <SectionHero />
                </div>

                <!-- DnD для остальных -->
                <Draggable
                    v-model="state.items"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="opacity-60"
                    drag-class="ring-2"
                    @end="onDragEnd"
                >
                    <template #item="{ element: b, index }">
                        <div class="relative bg-slate-50 dark:bg-cyan-950
                                    hover:bg-slate-100 dark:hover:bg-cyan-800">
                            <div
                                class="flex items-center justify-between p-1
                                       border border-slate-900/10 dark:border-white/10
                                       bg-slate-400/10"
                            >
                                <div class="flex flex-row items-center gap-1">

                                    <!-- перетаскивание -->
                                    <button
                                        type="button"
                                        class="drag-handle inline-flex items-center justify-center
                                           w-6 h-6 text-slate-900 dark:text-slate-100
                                           cursor-move active:cursor-grabbing active:scale-95"
                                        :title="t('dragDrop')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-4 h-4 fill-current
                                                    text-slate-800 dark:text-slate-200"
                                             height="24" width="24" viewBox="0 0 24 24">
                                            <path
                                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path>
                                            <path
                                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path>
                                        </svg>
                                    </button>

                                    <!-- имя секции -->
                                    <div class="font-bold text-slate-900 dark:text-slate-200">
                                        {{ displayName(b.type) }}
<!--                                        <span class="opacity-60 text-xs">-->
<!--                                            (ID: {{ b.id }})-->
<!--                                        </span>-->
<!--                                        <span class="opacity-60 text-xs">-->
<!--                                            #{{index + 1}}-->
<!--                                        </span>-->
                                    </div>

                                </div>

                                <div class="flex gap-1">

                                    <!-- toggle активности -->
                                    <ActivityToggle
                                        :isActive="b.activity"
                                        :title="b.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="() => toggleSectionActivity(b)"
                                    />

                                    <!-- кнопка редактирования -->
                                    <IconEdit
                                        :href="route(editRouteMap[b.type], { section: b.id })" />

                                    <!-- кнопка свёртывания -->
                                    <div class="inline-flex items-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center
                                                   w-6 h-6 text-slate-900 dark:text-slate-100
                                                   active:scale-95"
                                            @click="toggleCollapse(b)"
                                            :title="state.collapsed[keyOf(b)] ? 'Развернуть' : 'Свернуть'"
                                            :aria-expanded="(!state.collapsed[keyOf(b)]).toString()"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                class="w-5 h-5 transition-transform duration-200"
                                                :class="state.collapsed[keyOf(b)] ? '-rotate-90' : 'rotate-0'"
                                            >
                                                <!-- одна форма «вниз» (chevron-down) -->
                                                <path
                                                    fill="currentColor"
                                                    d="M7.41,8.58L12,13.17l4.59-4.59L18,10l-6,6l-6-6L7.41,8.58z"
                                                />
                                            </svg>
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <div
                                v-show="!state.collapsed[keyOf(b)]"
                                class="border border-slate-900/10 dark:border-white/10 border-t-0
                                       rounded-b-lg p-2 bg-white/50 dark:bg-slate-900/35"
                            >
                                <component :is="componentMap[b.type]" />
                            </div>
                        </div>
                    </template>
                </Draggable>
            </div>
        </div>
    </AdminLayout>
</template>
