<script setup>
import MarketCategoryTreeItem from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryTreeItem.vue'

defineProps({
    categories: { type: Array, default: () => [] },
})
</script>

<template>
    <!-- Аккордеон категорий маркетплейса -->
    <div v-if="categories.length" class="w-full space-y-1">
        <MarketCategoryTreeItem
            v-for="category in categories"
            :key="category.id"
            :item="category"
            :depth="0"
        />
    </div>
</template>
