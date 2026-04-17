<template>
  <nav class="navbar glass px-4 py-3">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-link text-dark nav-icon-button" @click="toggleSidebar">
        <i class="bi bi-list fs-4"></i>
      </button>
      <div>
        <span class="surface-tag mb-2">Music Foundation</span>
        <h5 class="mb-0 fw-semibold">Welcome back, {{ userStore.user.name }}!</h5>
      </div>
    </div>

    <div class="d-flex align-items-center gap-4">
      <div class="position-relative nav-icon-button">
        <i class="bi bi-bell fs-5 text-dark"></i>
        <span
          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
        >
          3
        </span>
      </div>

      <div class="dropdown">
        <div class="d-flex align-items-center gap-2 cursor-pointer" data-bs-toggle="dropdown">
          <img
            :src="userStore.user.avatar"
            class="rounded-circle avatar-image media-frame"
            width="48"
            height="48"
            alt="Avatar"
          />
          <div class="text-start">
            <div class="fw-semibold">{{ userStore.user.name }}</div>
            <small class="text-muted">{{ userStore.user.role }}</small>
          </div>
        </div>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="#">Account Settings</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useUserStore } from "@/stores/userStore";

const userStore = useUserStore();

const toggleSidebar = () => {
  // Sidebar toggle logic
  const sidebar = document.querySelector(".sidebar");
  sidebar?.classList.toggle("collapsed");
};
</script>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  right: 0;
  left: var(--sidebar-width);
  z-index: 999;
  min-height: var(--navbar-height);
  box-shadow: 0 12px 34px rgba(166, 130, 41, 0.08);
  border-bottom: 1px solid rgba(217, 198, 154, 0.45);
}

.cursor-pointer {
  cursor: pointer;
}

.nav-icon-button {
  width: 44px;
  height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 14px;
  background: rgba(255, 249, 236, 0.85);
  border: 1px solid rgba(217, 198, 154, 0.45);
}

@media (max-width: 768px) {
  .navbar {
    left: 0;
  }
}
</style>
