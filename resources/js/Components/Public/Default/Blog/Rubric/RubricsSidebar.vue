<script setup>
import RubricTreeItem from '@/Components/Public/Default/Blog/Rubric/RubricTreeItem.vue'

defineProps({
    rubrics: { type: Array, default: () => [] },
})
</script>

<template>
    <!-- Блок меню аккордеон рубрик -->
    <div v-if="rubrics.length" class="w-full space-y-1">
        <RubricTreeItem
            v-for="rubric in rubrics"
            :key="rubric.id"
            :item="rubric"
            :depth="0"
        />
    </div>
</template>
