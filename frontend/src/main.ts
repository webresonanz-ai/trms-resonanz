import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router";
import { useUserStore } from "@/stores/userStore";

// Import Bootstrap CSS and JS
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "./assets/main.css";

const app = createApp(App);

app.use(createPinia());
app.use(router);

// Initialize authentication state (check stored token)
const userStore = useUserStore();
userStore.initAuth();

app.mount("#app");
