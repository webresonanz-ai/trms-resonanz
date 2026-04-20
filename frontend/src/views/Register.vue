<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <div class="auth-header">
          <h1 class="fw-bold">Create Account</h1>
          <p class="text-muted">Join Music Foundation</p>
        </div>

        <form @submit.prevent="handleRegister" class="auth-form">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input
              v-model="name"
              type="text"
              class="form-control"
              placeholder="Enter your name"
              required
            />
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input
              v-model="email"
              type="email"
              class="form-control"
              placeholder="Enter your email"
              required
            />
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input
              v-model="password"
              type="password"
              class="form-control"
              placeholder="Create a password"
              required
              minlength="6"
            />
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input
              v-model="confirmPassword"
              type="password"
              class="form-control"
              placeholder="Confirm your password"
              required
            />
          </div>

          <div v-if="error" class="alert alert-danger">{{ error }}</div>

          <button type="submit" class="btn btn-gold w-100" :disabled="loading">
            <span v-if="loading">Creating account...</span>
            <span v-else>Create Account</span>
          </button>
        </form>

        <div class="auth-footer">
          <p class="text-muted">
            Already have an account?
            <router-link to="/login" class="text-gold">Sign in</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "@/stores/userStore";

const router = useRouter();
const userStore = useUserStore();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const loading = ref(false);
const error = ref("");

async function handleRegister() {
  error.value = "";

  if (password.value !== confirmPassword.value) {
    error.value = "Passwords do not match";
    return;
  }

  loading.value = true;

  const success = await userStore.register(name.value, email.value, password.value);

  loading.value = false;

  if (success) {
    router.push("/login");
  } else {
    error.value = userStore.error || "Registration failed";
  }
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #fff8e7 0%, #efd28e 100%);
}

.auth-container {
  width: 100%;
  max-width: 420px;
  padding: 2rem;
}

.auth-card {
  background: white;
  border-radius: 16px;
  padding: 2.5rem;
  box-shadow: 0 12px 34px rgba(166, 130, 41, 0.15);
}

.auth-header {
  text-align: center;
  margin-bottom: 2rem;
}

.auth-header h1 {
  color: #1a1a1a;
  margin-bottom: 0.5rem;
}

.auth-form .form-control {
  padding: 0.75rem 1rem;
  border: 1px solid rgba(217, 198, 154, 0.45);
  border-radius: 10px;
}

.auth-form .form-control:focus {
  border-color: #d6b25b;
  box-shadow: 0 0 0 3px rgba(214, 178, 91, 0.15);
}

.auth-footer {
  text-align: center;
  margin-top: 1.5rem;
}

.text-gold {
  color: #d6b25b;
  text-decoration: none;
  font-weight: 500;
}

.text-gold:hover {
  color: #b8963c;
}
</style>