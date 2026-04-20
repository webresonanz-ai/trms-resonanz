<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Overview</p>
        <h1 class="fw-bold">Dashboard</h1>
      </div>
      <button class="btn btn-gold">Export Report</button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-6 col-lg-3" v-for="stat in stats" :key="stat.title">
        <DashboardCard
          :title="stat.title"
          :value="stat.value"
          :icon="stat.icon"
          :gradient="stat.gradient"
          :growth="stat.growth"
        />
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <RevenueChart />
      </div>
      <div class="col-lg-4">
        <StreamChart />
      </div>
    </div>

    <!-- Recent Streams Table -->
    <div class="card">
      <div class="card-body p-4">
        <h5 class="card-title fw-semibold mb-4">Recent Streams Activity</h5>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Track Name</th>
                <th>Artist</th>
                <th>Date</th>
                <th>Streams</th>
                <th>Revenue</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stream in musicStore.recentStreams" :key="stream.id">
                <td class="fw-semibold">{{ stream.trackName }}</td>
                <td>{{ stream.artist }}</td>
                <td>{{ stream.date }}</td>
                <td>{{ stream.count.toLocaleString() }}</td>
                <td class="gold-text fw-semibold">${{ stream.revenue.toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <MusicPlayer />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from "vue";
import { useMusicStore } from "@/stores/musicStore";
import DashboardCard from "@/components/DashboardCard.vue";
import RevenueChart from "@/components/Charts/RevenueChart.vue";
import StreamChart from "@/components/Charts/StreamChart.vue";
import MusicPlayer from "@/components/MusicPlayer.vue";

const musicStore = useMusicStore();

onMounted(() => {
  musicStore.fetchAll();
});

const stats = computed(() => [
  {
    title: "Total Streams",
    value: musicStore.dashboardStats.totalStreams.toLocaleString(),
    icon: "bi bi-play-circle-fill",
    gradient: "linear-gradient(135deg, #fff8e7 0%, #efd28e 55%, #d6b25b 100%)",
    growth: 15.3,
  },
  {
    title: "Total Revenue",
    value: `$${(musicStore.dashboardStats.totalRevenue / 1000000).toFixed(1)}M`,
    icon: "bi bi-cash-stack",
    gradient: "linear-gradient(135deg, #fffdf7 0%, #f3dfa9 50%, #d8b25c 100%)",
    growth: 23.5,
  },
  {
    title: "Active Artists",
    value: musicStore.dashboardStats.totalArtists,
    icon: "bi bi-people-fill",
    gradient: "linear-gradient(135deg, #fff8eb 0%, #edd08a 50%, #cfa24a 100%)",
    growth: 8.2,
  },
  {
    title: "Monthly Listeners",
    value: (musicStore.dashboardStats.activeListeners / 1000000).toFixed(1) + "M",
    icon: "bi bi-headphones",
    gradient: "linear-gradient(135deg, #fffef9 0%, #f4e2b2 55%, #d2aa53 100%)",
    growth: 12.7,
  },
]);
</script>

