<script setup>
import { computed } from 'vue'

import TrackTreeItem from '@/Components/Public/Default/School/SchoolTrack/TrackTreeItem.vue'

const props = defineProps({
    tracks: {
        type: Array,
        default: () => [],
    },
})

/**
 * Публичное дерево треков.
 *
 * Backend уже формирует
 * иерархическую структуру.
 */
const trackList = computed(() => {
    return Array.isArray(props.tracks)
        ? props.tracks
        : []
})
</script>

<template>
    <!-- Дерево направлений обучения -->
    <div
        v-if="trackList.length"
        class="w-full space-y-1"
    >
        <TrackTreeItem
            v-for="track in trackList"
            :key="track.id"
            :item="track"
            :depth="0"
        />
    </div>
</template>
