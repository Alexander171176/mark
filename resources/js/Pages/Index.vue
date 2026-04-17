<script setup>
import { defineProps, defineAsyncComponent, shallowRef, watch, onMounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Maintenance from './Maintenance.vue'

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    template: String,
    laravelVersion: String,
    phpVersion: String,
})

const page = usePage()

// /admin/* или /{locale}/admin/*
const isAdminPath = computed(() => {
    const segments = window.location.pathname.split('/').filter(Boolean)
    return segments[0] === 'admin' || segments[1] === 'admin'
})

// Функция для динамического импорта компонентов
const importTemplates = () => {
    const context = import.meta.glob('/resources/js/Pages/Templates/*/Index.vue')
    const components = {}

    for (const path in context) {
        const templateName = path.split('/')[5]
        components[templateName] = defineAsyncComponent(context[path])
    }

    return components
}

const components = importTemplates()

const currentComponent = shallowRef(null)

// downtimeSite берём из publicSettings (который читается из snapshot/public.php)
// В админке эту логику НЕ применяем (всегда false)
const downtimeSite = computed(() => {
    if (isAdminPath.value) return false

    const v = page.props?.publicSettings?.downtimeSite

    // на всякий случай поддержим разные типы
    if (typeof v === 'boolean') return v
    if (typeof v === 'string') return v === 'true' || v === '1'
    if (typeof v === 'number') return v === 1

    return false
})

const updateComponent = () => {
    if (downtimeSite.value) {
        currentComponent.value = Maintenance
        return
    }

    const template = props.template || 'Default'
    currentComponent.value = components[template] || components['Default']
}

onMounted(() => {
    updateComponent()
})

// если publicSettings меняется (например, при Inertia.visit), компонент пересчитается
watch([downtimeSite, () => props.template], () => {
    updateComponent()
})
</script>
