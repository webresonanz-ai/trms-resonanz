<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Insights</p>
        <h1 class="fw-bold">Revenue Analytics</h1>
      </div>
      <span class="surface-tag">Updated monthly</span>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <RevenueChart />
      </div>
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Revenue by Artist</h5>
            <div v-for="artist in musicStore.artists" :key="artist.id" class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="fw-semibold">{{ artist.name }}</span>
                <span class="gold-text">${{ (artist.revenue / 1000000).toFixed(1) }}M</span>
              </div>
              <div class="progress">
                <div
                  class="progress-bar"
                  :style="{ width: getPercentage(artist.revenue) + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useMusicStore } from "@/stores/musicStore";
import RevenueChart from "@/components/Charts/RevenueChart.vue";

const musicStore = useMusicStore();

const getPercentage = (revenue: number) => {
  const maxRevenue = Math.max(...musicStore.artists.map((a) => a.revenue));
  return (revenue / maxRevenue) * 100;
};
</script>

