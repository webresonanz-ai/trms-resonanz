<template>
  <div id="app">
    <Sidebar :is-open="isSidebarOpen" @close="isSidebarOpen = false" />
    <Navbar :is-sidebar-open="isSidebarOpen" @toggle-sidebar="toggleSidebar" />
    <router-view />
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import Sidebar from "./components/Sidebar.vue";
import Navbar from "./components/Navbar.vue";

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<style>
#app {
  min-height: 100vh;
}
</style>
