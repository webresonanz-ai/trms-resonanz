import { defineStore } from "pinia";
import { ref } from "vue";
import userAvatar from "@/assets/images/user-avatar.svg";

export const useUserStore = defineStore("user", () => {
  const user = ref({
    name: "Sarah Johnson",
    email: "sarah@musicfoundation.com",
    avatar: userAvatar,
    role: "Admin",
  });

  const isLoggedIn = ref(true);

  function logout() {
    isLoggedIn.value = false;
  }

  function login(_email: string, _password: string) {
    // Simulate login
    isLoggedIn.value = true;
  }

  return {
    user,
    isLoggedIn,
    logout,
    login,
  };
});
