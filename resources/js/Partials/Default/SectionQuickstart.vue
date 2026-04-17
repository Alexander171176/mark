<script setup>
import SectionHeader from '@/Components/Public/Default/Shared/SectionHeader.vue'
import CtaRow from '@/Components/Public/Default/SectionQuickstart/CtaRow.vue'
import VideoPlayer from '@/Components/Public/Default/SectionQuickstart/VideoPlayer.vue'
import WaveSeparator from '@/Components/Public/Default/Shared/WaveSeparator.vue'
</script>

<template>
    <div class="dark:bg-[hsl(240_33%_9%)]">
        <div class="section-content">
            <div class="container">

                <SectionHeader
                    subtitle="Quickstart Tutorial"
                    title="Get Started with Vulk"
                />

                <CtaRow />

                <VideoPlayer
                    src="/assets/video/meeting.mp4"
                    poster="/assets/screenshots/vulk-yt-cover.png"
                />
            </div>
        </div>
        <WaveSeparator />
    </div>
</template>

<style scoped></style>
