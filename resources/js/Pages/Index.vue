<script setup>
import {
    defineProps,
    defineAsyncComponent,
    shallowRef,
    watch,
    onMounted,
} from 'vue'

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    template: String,
    laravelVersion: String,
    phpVersion: String,
})

// Функция для динамического импорта компонентов
const importTemplates = () => {
    const context = import.meta.glob(
        '/resources/js/Pages/Templates/*/Index.vue'
    )

    const components = {}

    for (const path in context) {
        const templateName = path.split('/')[5]

        components[templateName] =
            defineAsyncComponent(
                context[path]
            )
    }

    return components
}

const components = importTemplates()

const currentComponent = shallowRef(null)

const updateComponent = () => {
    const template =
        props.template
        || 'Default'

    currentComponent.value =
        components[template]
        || components['Default']
}

onMounted(() => {
    updateComponent()
})

watch(
    () => props.template,
    () => {
        updateComponent()
    }
)
</script>
