<template>
  <div class="card h-100">
    <div class="card-body p-4">
      <h5 class="card-title fw-semibold mb-4">Revenue Overview</h5>
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import Chart from "chart.js/auto";

const chartCanvas = ref<HTMLCanvasElement>();

onMounted(() => {
  if (chartCanvas.value) {
    new Chart(chartCanvas.value, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [
          {
            label: "Revenue ($)",
            data: [125000, 145000, 162000, 189000, 215000, 245000],
            borderColor: "#cfa24a",
            backgroundColor: "rgba(212, 177, 90, 0.16)",
            tension: 0.4,
            fill: true,
            pointBackgroundColor: "#fff8ea",
            pointBorderColor: "#b98b25",
            pointBorderWidth: 2,
            pointRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#7b715c",
            },
          },
          y: {
            grid: {
              color: "rgba(217, 198, 154, 0.35)",
            },
            ticks: {
              color: "#7b715c",
            },
          },
        },
      },
    });
  }
});
</script>

<style scoped>
canvas {
  max-height: 300px;
}
</style>
