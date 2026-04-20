import { defineStore } from "pinia";
import { ref } from "vue";

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000/api";

export const useUserStore = defineStore("user", () => {
  const user = ref<{
    id: number;
    name: string;
    email: string;
    avatar: string;
    role: string;
  }>({
    id: 0,
    name: "Sarah Johnson",
    email: "sarah@musicfoundation.com",
    avatar: "",
    role: "Admin",
  });

  const isLoggedIn = ref(false);
  const loading = ref(false);
  const error = ref<string | null>(null);
   const token = ref<string | null>(localStorage.getItem("token"));

  // Initialize: check token validity on app load
  async function initAuth() {
    const storedToken = localStorage.getItem("token");
    if (storedToken) {
      token.value = storedToken;
      try {
        const response = await fetch(`${API_BASE}/profile`, {
          headers: getAuthHeaders(),
        });
        if (response.ok) {
          const result = await response.json();
          if (result.success && result.data) {
            user.value = {
              id: result.data.id,
              name: result.data.name,
              email: result.data.email,
              avatar: result.data.avatar || "",
              role: result.data.role || "user",
            };
            isLoggedIn.value = true;
            return;
          }
        }
      } catch (e) {
        console.error("Auth init failed:", e);
      }
      // Token invalid — clear it
      token.value = null;
      localStorage.removeItem("token");
      isLoggedIn.value = false;
    }
  }

  async function fetchProfile() {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/profile`, {
        headers: getAuthHeaders(),
      });
      if (response.status === 401) {
        isLoggedIn.value = false;
        token.value = null;
        localStorage.removeItem("token");
        loading.value = false;
        return;
      }
      const result = await response.json();
      if (result.success && result.data) {
        user.value = {
          id: result.data.id,
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
      if (result.success && result.data?.token) {
        token.value = result.data.token;
        localStorage.setItem("token", result.data.token);
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
        if (result.data?.token) {
          token.value = result.data.token;
          localStorage.setItem("token", result.data.token);
          await fetchProfile();
        }
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
      id: 0,
      name: "",
      email: "",
      avatar: "",
      role: "user",
    };
    isLoggedIn.value = false;
    token.value = null;
    localStorage.removeItem("token");
  }

  function getAuthHeaders(): HeadersInit {
    return token.value
      ? {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token.value}`,
        }
      : {
          "Content-Type": "application/json",
        };
  }

  return {
    user,
    isLoggedIn,
    loading,
    error,
    token,
    initAuth,
    fetchProfile,
    login,
    register,
    logout,
    getAuthHeaders,
  };
});
