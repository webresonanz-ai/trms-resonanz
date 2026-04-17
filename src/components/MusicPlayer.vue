<template>
  <div class="music-player soft-panel mt-4">
    <div class="d-flex align-items-center justify-content-between p-3">
      <div class="d-flex align-items-center gap-3">
        <img
          :src="nowPlayingCover"
          width="56"
          height="56"
          class="rounded-4 media-frame cover-image"
          alt="Now Playing"
        />
        <div>
          <div class="fw-semibold">Blinding Lights</div>
          <small class="text-muted">The Weeknd</small>
        </div>
      </div>

      <div class="controls d-flex align-items-center gap-3">
        <button class="btn btn-link control-btn"><i class="bi bi-skip-backward-fill"></i></button>
        <button class="btn rounded-circle play-btn">
          <i class="bi bi-play-fill"></i>
        </button>
        <button class="btn btn-link control-btn"><i class="bi bi-skip-forward-fill"></i></button>
      </div>

      <div class="volume-control d-flex align-items-center gap-2">
        <i class="bi bi-volume-up-fill text-muted"></i>
        <div class="progress flex-grow-1" style="width: 100px">
          <div class="progress-bar" style="width: 65%"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import nowPlayingCover from "@/assets/images/now-playing.svg";
</script>

<style scoped>
.music-player {
  border-radius: 24px;
}

.play-btn {
  width: 48px;
  height: 48px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #eed291 0%, var(--gold-primary) 100%);
  border: none;
  color: var(--gray-700);
  box-shadow: 0 12px 24px rgba(212, 177, 90, 0.24);
}

.play-btn:hover {
  transform: scale(1.1);
  transition: transform 0.3s ease;
}

.control-btn {
  color: var(--gray-700);
}

@media (max-width: 768px) {
  .music-player .d-flex {
    flex-wrap: wrap;
  }
}
</style>
