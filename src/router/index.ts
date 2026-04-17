import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Artists from "../views/Artists.vue";
import Albums from "../views/Albums.vue";
import Revenue from "../views/Revenue.vue";
import Settings from "../views/Settings.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "dashboard",
      component: Dashboard,
    },
    {
      path: "/artists",
      name: "artists",
      component: Artists,
    },
    {
      path: "/albums",
      name: "albums",
      component: Albums,
    },
    {
      path: "/revenue",
      name: "revenue",
      component: Revenue,
    },
    {
      path: "/settings",
      name: "settings",
      component: Settings,
    },
  ],
});

export default router;
