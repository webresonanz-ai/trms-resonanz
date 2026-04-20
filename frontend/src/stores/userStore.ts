import { defineStore } from "pinia";
import { ref } from "vue";

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000/api";

export const useUserStore = defineStore("user", () => {
  const user = ref<{
    name: string;
    email: string;
    avatar: string;
    role: string;
  }>({
    name: "Sarah Johnson",
    email: "sarah@musicfoundation.com",
    avatar: "",
    role: "Admin",
  });

  const isLoggedIn = ref(false);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchProfile() {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/profile`);
      if (response.status === 401) {
        isLoggedIn.value = false;
        loading.value = false;
        return;
      }
      const result = await response.json();
      if (result.success && result.data) {
        user.value = {
          name: result.data.name,
          email: result.data.email,
          avatar: result.data.avatar || "",
          role: result.data.role || "user",
        };
        isLoggedIn.value = true;
      }
    } catch (e) {
      error.value = "Failed to fetch profile";
    } finally {
      loading.value = false;
    }
  }

  async function login(email: string, password: string) {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const result = await response.json();
      if (result.success) {
        await fetchProfile();
        return true;
      }
      error.value = result.error || "Login failed";
      return false;
    } catch (e) {
      error.value = "Login failed";
      return false;
    } finally {
      loading.value = false;
    }
  }

  async function register(name: string, email: string, password: string) {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/register`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, email, password }),
      });
      const result = await response.json();
      if (result.success) {
        return true;
      }
      error.value = result.error || "Registration failed";
      return false;
    } catch (e) {
      error.value = "Registration failed";
      return false;
    } finally {
      loading.value = false;
    }
  }

  function logout() {
    user.value = {
      name: "Sarah Johnson",
      email: "sarah@musicfoundation.com",
      avatar: "",
      role: "Admin",
    };
    isLoggedIn.value = false;
  }

  return {
    user,
    isLoggedIn,
    loading,
    error,
    fetchProfile,
    login,
    register,
    logout,
  };
});
