<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Roster</p>
        <h1 class="fw-bold">Artists</h1>
      </div>
      <button class="btn btn-gold">
        <i class="bi bi-plus-lg me-2"></i>Add Artist
      </button>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-4" v-for="artist in musicStore.artists" :key="artist.id">
        <div class="card card-hover h-100">
          <div class="card-body p-4 text-center">
            <img
              :src="artist.image"
              class="rounded-circle mb-3 avatar-image media-frame"
              width="112"
              height="112"
              :alt="artist.name"
            />
            <h5 class="fw-bold mb-2">{{ artist.name }}</h5>
            <p class="text-muted mb-3">{{ artist.genre }}</p>

            <div class="row g-2 text-center">
              <div class="col-6">
                <div class="fw-bold">{{ artist.monthlyListeners.toLocaleString() }}</div>
                <small class="text-muted">Monthly Listeners</small>
              </div>
              <div class="col-6">
                <div class="fw-bold">{{ artist.albums }}</div>
                <small class="text-muted">Albums</small>
              </div>
            </div>

            <div class="mt-3">
              <div class="fw-bold gold-text">${{ (artist.revenue / 1000000).toFixed(1) }}M</div>
              <small class="text-muted">Total Revenue</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useMusicStore } from "@/stores/musicStore";

const musicStore = useMusicStore();

onMounted(() => {
  musicStore.fetchArtists();
});
</script>

