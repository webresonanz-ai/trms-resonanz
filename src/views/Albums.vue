<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Catalog</p>
        <h1 class="fw-bold">Albums</h1>
      </div>
      <span class="surface-tag">{{ musicStore.albums.length }} releases</span>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-4" v-for="album in musicStore.albums" :key="album.id">
        <div class="card card-hover h-100 overflow-hidden">
          <img :src="album.cover" class="card-img-top cover-image" :alt="album.title" />
          <div class="card-body">
            <h5 class="card-title fw-bold">{{ album.title }}</h5>
            <p class="card-text text-muted">{{ album.artist }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted">Released</small>
                <div class="fw-semibold">{{ formatDate(album.releaseDate) }}</div>
              </div>
              <div class="text-end">
                <small class="text-muted">Streams</small>
                <div class="fw-semibold">{{ album.streams.toLocaleString() }}</div>
              </div>
            </div>
            <hr />
            <div class="d-flex justify-content-between">
              <span>Revenue</span>
              <span class="fw-bold gold-text">${{ (album.revenue / 1000000).toFixed(1) }}M</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useMusicStore } from "@/stores/musicStore";

const musicStore = useMusicStore();

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};
</script>

<style scoped>
.card-img-top {
  height: 220px;
  background: linear-gradient(180deg, rgba(255, 249, 236, 0.85), rgba(239, 209, 139, 0.7));
}
</style>
