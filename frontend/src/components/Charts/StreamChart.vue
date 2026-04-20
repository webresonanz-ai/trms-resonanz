<template>
  <div class="card h-100">
    <div class="card-body p-4">
      <h5 class="card-title fw-semibold mb-4">Top Streaming Tracks</h5>
      <div v-for="track in topTracks" :key="track.name" class="mb-3">
        <div class="d-flex justify-content-between mb-1">
          <span class="fw-semibold">{{ track.name }}</span>
          <span>{{ track.streams.toLocaleString() }}</span>
        </div>
        <div class="progress">
          <div class="progress-bar bg-gradient" :style="{ width: track.percentage + '%' }"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const topTracks = [
  { name: "Blinding Lights", streams: 1250000, percentage: 100 },
  { name: "Anti-Hero", streams: 980000, percentage: 78 },
  { name: "Rich Flex", streams: 876000, percentage: 70 },
  { name: "Save Your Tears", streams: 745000, percentage: 60 },
  { name: "Lavender Haze", streams: 623000, percentage: 50 },
];
</script>

<style scoped>
.progress {
  height: 8px;
  border-radius: 10px;
}
</style>
