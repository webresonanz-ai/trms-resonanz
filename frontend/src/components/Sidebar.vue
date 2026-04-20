<template>
  <div>
    <button v-if="isOpen" class="sidebar-backdrop" type="button" @click="$emit('close')"></button>

    <aside class="sidebar glass" :class="{ 'sidebar-open': isOpen }">
      <div class="sidebar-header text-center py-4">
        <div class="logo">
          <div class="logo-mark mx-auto mb-3">
            <i class="bi bi-music-note-beamed fs-3 gradient-text"></i>
          </div>
          <h3 class="mt-2 fw-bold">Music<span class="gradient-text">Foundation</span></h3>
          <p class="text-muted mb-0">A polished dashboard for artist growth and revenue tracking.</p>
        </div>
      </div>

      <nav class="nav flex-column mt-4">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="nav-link d-flex align-items-center gap-3 py-3 px-4"
          active-class="active"
          @click="$emit('close')"
        >
          <i :class="item.icon" class="fs-5"></i>
          <span class="fw-medium">{{ item.name }}</span>
        </router-link>
      </nav>
    </aside>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  isOpen: boolean;
}>();

defineEmits<{
  (event: "close"): void;
}>();

const menuItems = [
  { name: "Dashboard", path: "/", icon: "bi bi-grid-3x3-gap-fill" },
  { name: "Artists", path: "/artists", icon: "bi bi-people-fill" },
  { name: "Albums", path: "/albums", icon: "bi bi-collection-fill" },
  { name: "Revenue", path: "/revenue", icon: "bi bi-graph-up" },
  { name: "Guest Registration", path: "/guest-registration", icon: "bi bi-person-badge" },
  { name: "Guest Check In", path: "/guest-check-in", icon: "bi bi-qr-code-scan" },
  { name: "Settings", path: "/settings", icon: "bi bi-gear-fill" },
];
</script>

<style scoped>
.sidebar {
  width: var(--sidebar-width);
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  padding: 1rem 0.75rem;
  box-shadow: 18px 0 38px rgba(166, 130, 41, 0.08);
  z-index: 1000;
  border-right: 1px solid rgba(217, 198, 154, 0.45);
  transition: transform 0.28s ease, box-shadow 0.28s ease;
}

.nav-link {
  color: var(--gray-500);
  transition: all 0.3s ease;
  border-radius: 18px;
  margin: 0 12px;
}

.nav-link:hover {
  background: var(--gold-lighter);
  color: var(--gold-dark);
  transform: translateX(3px);
}

.nav-link.active {
  box-shadow: 0 12px 24px rgba(212, 175, 55, 0.2);
}

.logo {
  cursor: pointer;
}

.logo-mark {
  width: 72px;
  height: 72px;
  display: grid;
  place-items: center;
  border-radius: 24px;
  background: linear-gradient(180deg, rgba(255, 249, 236, 0.9), rgba(239, 209, 139, 0.7));
  border: 1px solid rgba(217, 198, 154, 0.55);
}

.sidebar-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(60, 49, 33, 0.2);
  border: 0;
  z-index: 998;
}

@media (max-width: 768px) {
  .sidebar {
    transform: translateX(calc(-100% - 1rem));
  }

  .sidebar.sidebar-open {
    transform: translateX(0);
    box-shadow: 22px 0 48px rgba(60, 49, 33, 0.18);
  }
}
</style>
