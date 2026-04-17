<script setup>
import TrackTreeItem from '@/Components/Public/Default/School/Track/TrackTreeItem.vue'

defineProps({
    tracks: { type: Array, default: () => [] },
})
</script>

<template>
    <!-- Блок меню аккордеон треков -->
    <div v-if="tracks.length" class="w-full space-y-1">
        <TrackTreeItem
            v-for="track in tracks"
            :key="track.id"
            :item="track"
            :depth="0"
        />
    </div>
</template>
