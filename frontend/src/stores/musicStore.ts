import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { Artist, Album, Stream, DashboardStats } from "@/types";

const API_BASE = import.meta.env.VITE_API_BASE || "/api";

export const useMusicStore = defineStore("music", () => {
  const artists = ref<Artist[]>([]);
  const albums = ref<Album[]>([]);
  const recentStreams = ref<Stream[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const dashboardStats = computed<DashboardStats>(() => ({
    totalStreams: artists.value.reduce((sum, artist) => sum + artist.totalStreams, 0),
    totalRevenue: artists.value.reduce((sum, artist) => sum + artist.revenue, 0),
    totalArtists: artists.value.length,
    activeListeners: artists.value.reduce((sum, artist) => sum + artist.monthlyListeners, 0),
    growth: 23.5,
  }));

  async function fetchArtists() {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/artists`);
      const result = await response.json();
      if (result.success) {
        artists.value = result.data.map((a: any) => ({
          id: a.id,
          name: a.name,
          image: a.image || "",
          monthlyListeners: parseInt(a.monthly_listeners) || 0,
          totalStreams: parseInt(a.total_streams) || 0,
          albums: parseInt(a.albums_count) || 0,
          genre: a.genre || "",
          revenue: parseFloat(a.revenue) || 0,
        }));
      }
    } catch (e) {
      error.value = "Failed to fetch artists";
    } finally {
      loading.value = false;
    }
  }

  async function fetchAlbums() {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/albums`);
      const result = await response.json();
      if (result.success) {
        albums.value = result.data.map((a: any) => ({
          id: a.id,
          title: a.title,
          artist: a.artist,
          artistId: a.artist_id,
          cover: a.cover || "",
          releaseDate: a.release_date || "",
          streams: parseInt(a.streams) || 0,
          revenue: parseFloat(a.revenue) || 0,
        }));
      }
    } catch (e) {
      error.value = "Failed to fetch albums";
    } finally {
      loading.value = false;
    }
  }

  async function fetchRecentStreams() {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/streams/recent?limit=10`);
      const result = await response.json();
      if (result.success) {
        recentStreams.value = result.data.map((s: any) => ({
          id: s.id,
          trackName: s.track_name,
          artist: s.artist,
          date: s.stream_date,
          count: parseInt(s.stream_count) || 0,
          revenue: parseFloat(s.revenue) || 0,
        }));
      }
    } catch (e) {
      error.value = "Failed to fetch streams";
    } finally {
      loading.value = false;
    }
  }

  async function fetchDashboardStats() {
    try {
      const response = await fetch(`${API_BASE}/dashboard/stats`);
      const result = await response.json();
      return result.success ? result.data : null;
    } catch (e) {
      error.value = "Failed to fetch dashboard stats";
      return null;
    }
  }

  async function fetchAll() {
    await Promise.all([fetchArtists(), fetchAlbums(), fetchRecentStreams()]);
  }

  async function addArtist(artist: Artist) {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(`${API_BASE}/artists`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(artist),
      });
      const result = await response.json();
      if (result.success) {
        artists.value.push(artist);
        return true;
      }
      return false;
    } catch (e) {
      error.value = "Failed to add artist";
      return false;
    } finally {
      loading.value = false;
    }
  }

  function getArtistById(id: number) {
    return artists.value.find((artist) => artist.id === id);
  }

  function getAlbumsByArtist(artistId: number) {
    return albums.value.filter((album) => album.artistId === artistId);
  }

  return {
    artists,
    albums,
    recentStreams,
    loading,
    error,
    dashboardStats,
    fetchArtists,
    fetchAlbums,
    fetchRecentStreams,
    fetchDashboardStats,
    fetchAll,
    addArtist,
    getArtistById,
    getAlbumsByArtist,
  };
});
