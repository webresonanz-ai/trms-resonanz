<template>
  <div class="page-shell fade-in-up">
    <div class="section-heading">
      <div>
        <p>Preferences</p>
        <h1 class="fw-bold">Settings</h1>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-4">
        <h5 class="fw-semibold mb-4">General Settings</h5>

        <div class="mb-3">
          <label class="form-label fw-semibold">Foundation Name</label>
          <input type="text" class="form-control" placeholder="Music Foundation" />
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Email Notifications</label>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="emailSwitch" />
            <label class="form-check-label" for="emailSwitch">Receive email notifications</label>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Default Currency</label>
          <select class="form-select">
            <option>USD ($)</option>
            <option>EUR (EUR)</option>
            <option>GBP (GBP)</option>
          </select>
        </div>

        <button class="btn btn-gold">Save Changes</button>
      </div>
    </div>
  </div>
</template>
