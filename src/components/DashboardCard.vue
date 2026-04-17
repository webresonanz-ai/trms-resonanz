<template>
  <div class="card card-hover h-100 overflow-hidden">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <span class="surface-tag mb-3">{{ title }}</span>
          <h2 class="mb-0 fw-bold">{{ value }}</h2>
        </div>
        <div class="icon-wrapper media-frame" :style="{ background: gradient }">
          <i :class="icon" class="fs-4"></i>
        </div>
      </div>
      <div class="mt-3">
        <span class="metric-positive fw-semibold">
          <i class="bi bi-arrow-up-short"></i> +{{ growth }}%
        </span>
        <span class="text-muted ms-2">vs last month</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string;
  value: string | number;
  icon: string;
  gradient: string;
  growth: number;
}>();
</script>

<style scoped>
.icon-wrapper {
  width: 58px;
  height: 58px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gray-700);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
}
</style>
