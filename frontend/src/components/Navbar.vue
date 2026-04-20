<template>
  <nav class="navbar glass px-4 py-3">
    <div class="navbar-shell">
      <div class="navbar-branding">
        <button class="btn btn-link text-dark nav-icon-button" @click="$emit('toggle-sidebar')">
        <i class="bi bi-list fs-4"></i>
      </button>
        <div class="navbar-copy">
          <span class="surface-tag mb-2 navbar-tag">Music Foundation</span>
          <h5 class="mb-0 fw-semibold">Welcome back, {{ userStore.user.name }}!</h5>
        </div>
      </div>

      <div class="navbar-actions">
        <div class="position-relative nav-icon-button notification-button">
          <i class="bi bi-bell fs-5 text-dark"></i>
          <span
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
          >
            3
          </span>
        </div>

        <div class="dropdown">
          <div
            class="d-flex align-items-center gap-2 cursor-pointer profile-trigger"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <img
              :src="userStore.user.avatar"
              class="rounded-circle avatar-image media-frame"
              width="48"
              height="48"
              alt="Avatar"
            />
            <div class="text-start profile-copy">
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
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useUserStore } from "@/stores/userStore";

defineProps<{
  isSidebarOpen: boolean;
}>();

defineEmits<{
  (event: "toggle-sidebar"): void;
}>();

const userStore = useUserStore();
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
  padding-inline: 1.5rem;
}

.navbar-shell {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  width: 100%;
}

.navbar-branding {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  min-width: 0;
}

.navbar-copy {
  min-width: 0;
}

.navbar-copy h5 {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.navbar-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 1rem;
  flex-shrink: 0;
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

.profile-trigger {
  padding: 0.3rem;
  border-radius: 999px;
  min-width: 0;
}

.profile-copy {
  line-height: 1.2;
}

@media (max-width: 768px) {
  .navbar {
    left: 0;
    padding-inline: 1rem;
  }

  .navbar-shell {
    gap: 0.75rem;
  }

  .navbar-copy h5 {
    font-size: 1.15rem;
    white-space: normal;
    line-height: 1.1;
  }

  .navbar-tag,
  .profile-copy {
    display: none;
  }

  .navbar-actions {
    gap: 0.6rem;
  }

  .nav-icon-button {
    width: 42px;
    height: 42px;
  }

  .profile-trigger {
    padding: 0;
  }
}

@media (max-width: 560px) {
  .navbar {
    padding-block: 0.85rem;
  }

  .navbar-shell {
    align-items: flex-start;
  }

  .navbar-copy h5 {
    font-size: 1rem;
    max-width: 10rem;
  }

  .notification-button {
    display: none;
  }
}
</style>
