<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Guest Management</p>
        <h1 class="fw-bold">Register Guest</h1>
      </div>
    </div>

    <div class="row g-4">
      <!-- Registration Form -->
      <div class="col-lg-7">
        <div class="card">
          <div class="card-body p-4">
            <h5 class="card-title fw-semibold mb-4">Guest Details</h5>
            
            <form @submit.prevent="handleRegister" v-if="!registeredGuest">
              <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  placeholder="Enter guest name"
                  required
                />
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Company</label>
                  <input
                    v-model="form.company"
                    type="text"
                    class="form-control"
                    placeholder="Company name"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Position</label>
                  <input
                    v-model="form.position"
                    type="text"
                    class="form-control"
                    placeholder="Job position"
                  />
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label">Notes</label>
                <textarea
                  v-model="form.notes"
                  class="form-control"
                  rows="3"
                  placeholder="Additional notes..."
                ></textarea>
              </div>

              <div v-if="error" class="alert alert-danger">{{ error }}</div>

              <button type="submit" class="btn btn-gold" :disabled="loading">
                <span v-if="loading">Registering...</span>
                <span v-else>Register Guest</span>
              </button>
            </form>

            <!-- Success State -->
            <div v-else class="text-center py-4">
              <div class="success-icon mb-3">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h4 class="fw-semibold text-success mb-2">Guest Registered Successfully!</h4>
              <p class="text-muted mb-4">The guest has been registered and QR code generated.</p>
              
              <button @click="resetForm" class="btn btn-outline-secondary">
                Register Another Guest
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- QR Code Display -->
      <div class="col-lg-5">
        <div class="card h-100">
          <div class="card-body p-4 d-flex flex-column">
            <h5 class="card-title fw-semibold mb-4">QR Code</h5>
            
            <div v-if="registeredGuest" class="text-center flex-grow-1">
              <div class="qr-display mb-4">
                <div class="qr-code">
                  <div class="qr-placeholder">
                    <i class="bi bi-qr-code"></i>
                  </div>
                </div>
              </div>
              
              <div class="guest-info text-start mb-4">
                <div class="info-item">
                  <span class="info-label">Guest Name</span>
                  <span class="info-value">{{ registeredGuest.name }}</span>
                </div>
                <div class="info-item" v-if="registeredGuest.company">
                  <span class="info-label">Company</span>
                  <span class="info-value">{{ registeredGuest.company }}</span>
                </div>
                <div class="info-item" v-if="registeredGuest.position">
                  <span class="info-label">Position</span>
                  <span class="info-value">{{ registeredGuest.position }}</span>
                </div>
              </div>

              <div class="qr-code-text">
                <label class="form-label">QR Code</label>
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control text-center"
                    :value="registeredGuest.qr_code"
                    readonly
                  />
                  <button class="btn btn-outline-secondary" @click="copyQrCode" type="button">
                    <i :class="copied ? 'bi bi-check' : 'bi bi-clipboard'"></i>
                  </button>
                </div>
              </div>
            </div>

            <div v-else class="text-center text-muted flex-grow-1 d-flex align-items-center justify-content-center">
              <div>
                <i class="bi bi-qr-code fs-1 opacity-25"></i>
                <p class="mb-0 mt-2">QR code will appear here</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Guests Table -->
    <div class="card mt-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="card-title fw-semibold mb-0">Registered Guests</h5>
          <span class="text-muted">{{ guests.length }} guests</span>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover" v-if="guests.length > 0">
            <thead>
              <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Position</th>
                <th>QR Code</th>
                <th>Registered Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="guest in guests" :key="guest.id">
                <td class="fw-semibold">{{ guest.name }}</td>
                <td>{{ guest.company || '-' }}</td>
                <td>{{ guest.position || '-' }}</td>
                <td>
                  <code class="qr-badge">{{ guest.qr_code }}</code>
                </td>
                <td>{{ formatDate(guest.registration_date) }}</td>
                <td>
                  <button 
                    class="btn btn-sm btn-outline-secondary me-1" 
                    @click="viewGuest(guest)"
                    title="View"
                  >
                    <i class="bi bi-eye"></i>
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-danger" 
                    @click="deleteGuest(guest.id)"
                    title="Delete"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-else class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 opacity-25"></i>
            <p class="mb-0 mt-2">No guests registered yet</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000/api";

interface Guest {
  id: number;
  name: string;
  company: string | null;
  position: string | null;
  notes: string | null;
  qr_code: string;
  registration_date: string;
}

const router = useRouter();

const form = ref({
  name: "",
  company: "",
  position: "",
  notes: "",
});

const loading = ref(false);
const error = ref("");
const registeredGuest = ref<Guest | null>(null);
const guests = ref<Guest[]>([]);
const copied = ref(false);

onMounted(() => {
  fetchGuests();
});

async function fetchGuests() {
  try {
    const response = await fetch(`${API_BASE}/guests`);
    const result = await response.json();
    if (result.success) {
      guests.value = result.data;
    }
  } catch (e) {
    console.error("Failed to fetch guests:", e);
  }
}

async function handleRegister() {
  error.value = "";
  loading.value = true;

  try {
    const response = await fetch(`${API_BASE}/guests`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form.value),
    });
    const result = await response.json();
    
    if (result.success) {
      registeredGuest.value = result.data.guest;
      fetchGuests();
    } else {
      error.value = result.error || "Registration failed";
    }
  } catch (e) {
    error.value = "Registration failed";
  } finally {
    loading.value = false;
  }
}

function resetForm() {
  form.value = {
    name: "",
    company: "",
    position: "",
    notes: "",
  };
  registeredGuest.value = null;
}

async function copyQrCode() {
  if (registeredGuest.value) {
    await navigator.clipboard.writeText(registeredGuest.value.qr_code);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  }
}

function formatDate(dateString: string) {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function viewGuest(guest: Guest) {
  registeredGuest.value = guest;
}

async function deleteGuest(id: number) {
  if (!confirm("Are you sure you want to delete this guest?")) return;
  
  try {
    const response = await fetch(`${API_BASE}/guests/${id}`, {
      method: "DELETE",
    });
    const result = await response.json();
    if (result.success) {
      fetchGuests();
      if (registeredGuest.value?.id === id) {
        registeredGuest.value = null;
      }
    }
  } catch (e) {
    console.error("Failed to delete guest:", e);
  }
}
</script>

<style scoped>
.form-control {
  padding: 0.75rem 1rem;
  border: 1px solid rgba(217, 198, 154, 0.45);
  border-radius: 10px;
}

.form-control:focus {
  border-color: #d6b25b;
  box-shadow: 0 0 0 3px rgba(214, 178, 91, 0.15);
}

.success-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  display: grid;
  place-items: center;
  border-radius: 50%;
  background: linear-gradient(180deg, rgba(34, 197, 94, 0.15), rgba(34, 197, 94, 0.05));
}

.success-icon i {
  font-size: 2.5rem;
  color: #22c55e;
}

.qr-display {
  padding: 1.5rem;
  background: linear-gradient(180deg, rgba(255, 249, 236, 0.9), rgba(239, 209, 139, 0.3));
  border-radius: 16px;
  border: 1px solid rgba(217, 198, 154, 0.45);
}

.qr-code {
  width: 160px;
  height: 160px;
  margin: 0 auto;
}

.qr-placeholder {
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
  background: white;
  border-radius: 12px;
}

.qr-placeholder i {
  font-size: 4rem;
  color: #1a1a1a;
}

.guest-info {
  padding: 1rem;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 10px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(217, 198, 154, 0.25);
}

.info-item:last-child {
  border-bottom: none;
}

.info-label {
  color: #6b7280;
  font-size: 0.875rem;
}

.info-value {
  font-weight: 500;
}

.qr-badge {
  background: rgba(217, 198, 154, 0.25);
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.75rem;
}
</style>