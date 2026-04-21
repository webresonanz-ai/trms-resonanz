<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Guest Attendance</p>
        <h1 class="fw-bold">Check In by QR Code</h1>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-7">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
              <div>
                <h5 class="card-title fw-semibold mb-1">Scanner</h5>
                <p class="scanner-subtitle mb-0">
                  Point the camera at a guest QR code or type the code manually.
                </p>
              </div>

              <div class="d-flex flex-wrap gap-2">
                <button
                  type="button"
                  class="btn btn-gold"
                  :disabled="startingCamera || !cameraSupported"
                  @click="startScanner"
                >
                  <span v-if="startingCamera">Starting...</span>
                  <span v-else>{{ scannerActive ? "Restart Camera" : "Start Camera" }}</span>
                </button>
                <button
                  v-if="scannerActive"
                  type="button"
                  class="btn btn-outline-secondary"
                  @click="stopScanner"
                >
                  Stop
                </button>
              </div>
            </div>

            <div class="scanner-panel mb-4">
              <div
                id="qr-reader"
                class="qr-reader-container"
                :class="{ 'is-hidden': !scannerActive }"
              ></div>

              <div v-if="errorMessage" class="alert alert-danger m-3">{{ errorMessage }}</div>

              <div v-if="!scannerActive" class="scanner-empty">
                <div class="scanner-empty-icon">
                  <i class="bi bi-qr-code-scan"></i>
                </div>
                <h6 class="fw-semibold mb-2">Camera scanner is idle</h6>
                <p class="text-muted mb-0">
                  {{ cameraSupportMessage }}
                </p>
              </div>

              <div v-if="scannerActive" class="scanner-frame" aria-hidden="true"></div>
            </div>

            <div v-if="message" class="alert" :class="messageClass">{{ message }}</div>

            <form class="row g-3 align-items-end" @submit.prevent="submitManualCode">
              <div class="col-md-8">
                <label class="form-label">Manual QR Code</label>
                <input
                  v-model="manualCode"
                  type="text"
                  class="form-control"
                  placeholder="Example: REG-12-1713600000-ABCD"
                />
              </div>
              <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100" :disabled="submitting">
                  <span v-if="submitting">Saving...</span>
                  <span v-else>Check In</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-xl-5">
        <div class="card h-100">
          <div class="card-body p-4">
            <h5 class="card-title fw-semibold mb-4">Latest Result</h5>

            <div v-if="lastCheckIn" class="result-card">
              <div class="result-badge">
                <i class="bi bi-check-circle-fill"></i>
                Checked In
              </div>

              <div class="result-row">
                <span class="result-label">Name</span>
                <strong>{{ lastCheckIn.guest.name }}</strong>
              </div>
              <div class="result-row" v-if="lastCheckIn.guest.company">
                <span class="result-label">Company</span>
                <strong>{{ lastCheckIn.guest.company }}</strong>
              </div>
              <div class="result-row" v-if="lastCheckIn.guest.position">
                <span class="result-label">Position</span>
                <strong>{{ lastCheckIn.guest.position }}</strong>
              </div>
              <div class="result-row">
                <span class="result-label">QR Code</span>
                <code>{{ lastCheckIn.guest.qr_code }}</code>
              </div>
              <div class="result-row">
                <span class="result-label">Checked In At</span>
                <strong>{{ formatDate(lastCheckIn.check_in.checked_in_at) }}</strong>
              </div>
            </div>

            <div v-else class="result-empty">
              <i class="bi bi-person-check"></i>
              <p class="mb-0">No guest checked in yet during this session.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="card-title fw-semibold mb-1">Recent Check-Ins</h5>
            <p class="text-muted mb-0">Latest guests successfully saved to the database.</p>
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm" @click="fetchRecentCheckIns">
            Refresh
          </button>
        </div>

        <div class="table-responsive">
          <table v-if="recentCheckIns.length > 0" class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Position</th>
                <th>Source</th>
                <th>Checked In At</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in recentCheckIns" :key="item.id">
                <td class="fw-semibold">{{ item.name }}</td>
                <td>{{ item.company || "-" }}</td>
                <td>{{ item.position || "-" }}</td>
                <td>
                  <span class="source-pill">{{ item.scan_source }}</span>
                </td>
                <td>{{ formatDate(item.checked_in_at) }}</td>
              </tr>
            </tbody>
          </table>

          <div v-else class="result-empty py-5">
            <i class="bi bi-clipboard-data"></i>
            <p class="mb-0">No check-ins have been saved yet.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useUserStore } from "@/stores/userStore";
import { Html5Qrcode } from "html5-qrcode";

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000/api";
const userStore = useUserStore();

interface CheckInResponse {
  guest: {
    id: number;
    name: string;
    company: string | null;
    position: string | null;
    notes: string | null;
    qr_code: string;
  };
  check_in: {
    checked_in_at: string;
    scan_source: string;
  };
}

interface RecentCheckIn {
  id: number;
  guest_id: number;
  name: string;
  company: string | null;
  position: string | null;
  qr_code: string;
  checked_in_at: string;
  scan_source: string;
}

const html5QrCode = ref<Html5Qrcode | null>(null);
const scannerActive = ref(false);
const startingCamera = ref(false);
const errorMessage = ref("");
const submitting = ref(false);
const message = ref("");
const messageType = ref<"success" | "danger" | "warning">("success");
const manualCode = ref("");
const lastCheckIn = ref<CheckInResponse | null>(null);
const recentCheckIns = ref<RecentCheckIn[]>([]);
const lastProcessedCode = ref("");

const cameraSupported =
  typeof navigator !== "undefined" &&
  "mediaDevices" in navigator &&
  typeof navigator.mediaDevices?.getUserMedia === "function";

const cameraSupportMessage = computed(() =>
  cameraSupported
    ? "Press Start Camera and scan the guest badge."
    : "This browser does not support live QR scanning here yet, so use the manual QR field instead.",
);

const messageClass = computed(() => `alert-${messageType.value}`);

onMounted(() => {
  fetchRecentCheckIns();
});

onBeforeUnmount(() => {
  stopScanner();
});

async function fetchRecentCheckIns() {
  try {
    const response = await fetch(`${API_BASE}/guests/check-ins?limit=15`, {
      headers: userStore.getAuthHeaders(),
    });
    const result = await response.json();
    if (result.success) {
      recentCheckIns.value = result.data;
    }
  } catch (error) {
    console.error("Failed to fetch recent check-ins:", error);
  }
}

async function startScanner() {
  if (!cameraSupported) {
    showMessage("Live camera scanning is not supported in this browser.", "warning");
    return;
  }

  try {
    errorMessage.value = "";
    scannerActive.value = true;
    startingCamera.value = true;

    await new Promise((resolve) => setTimeout(resolve, 100));

    html5QrCode.value = new Html5Qrcode("qr-reader");

    const config = {
      fps: 10,
      qrbox: { width: 250, height: 250 },
      aspectRatio: 1.0,
    };

    await html5QrCode.value.start(
      { facingMode: "user" },
      config,
      onScanSuccess,
      onScanFailure,
    );
  } catch (error) {
    console.error("Error starting scanner:", error);
    errorMessage.value = "Unable to access camera. Please check permissions or enter code manually.";
    showMessage(errorMessage.value, "warning");
    scannerActive.value = false;
  } finally {
    startingCamera.value = false;
  }
}

function onScanSuccess(decodedText: string) {
  if (decodedText && decodedText !== lastProcessedCode.value && !submitting.value) {
    lastProcessedCode.value = decodedText;
    manualCode.value = decodedText;
    submitCheckIn(decodedText, "camera");
  }
}

function onScanFailure(_error: string) {}

async function stopScanner() {
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.stop();
      html5QrCode.value.clear();
    } catch (error) {
      console.error("Error stopping scanner:", error);
    }
    html5QrCode.value = null;
  }

  scannerActive.value = false;
}

async function submitManualCode() {
  const code = manualCode.value.trim();
  if (!code) {
    showMessage("Please enter a QR code first.", "warning");
    return;
  }

  await submitCheckIn(code, "manual");
}

async function submitCheckIn(qrCode: string, scanSource: "camera" | "manual") {
  submitting.value = true;

  const now = new Date();
  const jakartaOffset = 7 * 60;
  const localTime = new Date(now.getTime() + (jakartaOffset + now.getTimezoneOffset()) * 60 * 1000);
  const year = localTime.getFullYear();
  const month = String(localTime.getMonth() + 1).padStart(2, "0");
  const day = String(localTime.getDate()).padStart(2, "0");
  const hours = String(localTime.getHours()).padStart(2, "0");
  const minutes = String(localTime.getMinutes()).padStart(2, "0");
  const seconds = String(localTime.getSeconds()).padStart(2, "0");
  const checkedInAt = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

  try {
    const response = await fetch(`${API_BASE}/guests/check-in`, {
      method: "POST",
      headers: userStore.getAuthHeaders(),
      body: JSON.stringify({
        qr_code: qrCode,
        scan_source: scanSource,
        checked_in_at: checkedInAt,
      }),
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      showMessage(result.error || "Check-in failed.", "danger");
      return;
    }

    lastCheckIn.value = result.data;
    manualCode.value = "";
    showMessage(`${result.data.guest.name} checked in successfully.`, "success");
    fetchRecentCheckIns();
  } catch (error) {
    console.error("Failed to save check-in:", error);
    showMessage("Failed to save check-in.", "danger");
  } finally {
    submitting.value = false;
  }
}

function showMessage(text: string, type: "success" | "danger" | "warning") {
  message.value = text;
  messageType.value = type;
}

function formatDate(dateString: string) {
  const date = new Date(dateString);
  return date.toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
</script>

<style scoped>
.scanner-subtitle {
  color: #6b7280;
}

.scanner-panel {
  position: relative;
  min-height: 360px;
  border-radius: 24px;
  overflow: hidden;
  background:
    radial-gradient(circle at top, rgba(214, 178, 91, 0.2), transparent 45%),
    linear-gradient(180deg, #17120a, #342915);
  border: 1px solid rgba(217, 198, 154, 0.35);
}

.scanner-video {
  width: 100%;
  height: 360px;
  object-fit: cover;
}

.qr-reader-container {
  width: 100%;
  min-height: 360px;
  transform: scaleX(-1);
}

.qr-reader-container video {
  width: 100% !important;
  height: 360px !important;
  object-fit: cover;
  border-radius: 24px;
}

.qr-reader-container.is-hidden {
  display: none;
}

.qr-reader-container #qr-shaded-region {
  border-width: 0 !important;
}

.qr-reader-container button {
  display: none !important;
}

.scanner-empty {
  min-height: 360px;
  display: grid;
  place-items: center;
  text-align: center;
  padding: 2rem;
  color: #fff7e6;
}

.scanner-empty-icon {
  width: 82px;
  height: 82px;
  margin: 0 auto 1rem;
  border-radius: 26px;
  display: grid;
  place-items: center;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
}

.scanner-empty-icon i {
  font-size: 2.2rem;
}

.scanner-frame {
  position: absolute;
  inset: 50% auto auto 50%;
  width: min(72vw, 260px);
  height: min(72vw, 260px);
  transform: translate(-50%, -50%);
  border: 3px solid rgba(255, 244, 213, 0.95);
  border-radius: 28px;
  box-shadow:
    0 0 0 999px rgba(0, 0, 0, 0.22),
    0 0 24px rgba(255, 228, 161, 0.35);
}

.form-control {
  padding: 0.85rem 1rem;
  border: 1px solid rgba(217, 198, 154, 0.45);
  border-radius: 12px;
}

.form-control:focus {
  border-color: #d6b25b;
  box-shadow: 0 0 0 3px rgba(214, 178, 91, 0.15);
}

.result-card {
  padding: 1.25rem;
  border-radius: 20px;
  background: linear-gradient(180deg, rgba(248, 251, 243, 0.95), rgba(235, 247, 228, 0.82));
  border: 1px solid rgba(145, 191, 120, 0.25);
}

.result-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.85rem;
  border-radius: 999px;
  margin-bottom: 1rem;
  color: #166534;
  background: rgba(34, 197, 94, 0.12);
}

.result-row {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.8rem 0;
  border-bottom: 1px solid rgba(148, 163, 184, 0.16);
}

.result-row:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}

.result-label {
  color: #6b7280;
}

.result-empty {
  min-height: 220px;
  display: grid;
  place-items: center;
  text-align: center;
  color: #6b7280;
}

.result-empty i {
  font-size: 2.5rem;
  margin-bottom: 0.75rem;
  color: #d6b25b;
}

.source-pill {
  display: inline-flex;
  align-items: center;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: rgba(217, 198, 154, 0.18);
  color: #7c5d14;
  text-transform: capitalize;
}

@media (max-width: 768px) {
  .scanner-panel,
  .scanner-empty,
  .scanner-video {
    min-height: 300px;
    height: 300px;
  }

  .result-row {
    flex-direction: column;
    gap: 0.35rem;
  }
}
</style>
