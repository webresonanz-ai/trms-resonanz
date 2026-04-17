<template>
  <div id="app">
    <Sidebar />
    <Navbar />
    <router-view />
  </div>
</template>

<script setup lang="ts">
import Sidebar from "./components/Sidebar.vue";
import Navbar from "./components/Navbar.vue";
</script>

<style>
#app {
  min-height: 100vh;
}
</style>
