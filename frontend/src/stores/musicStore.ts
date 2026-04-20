import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { Artist, Album, Stream, DashboardStats } from "@/types";
import artistTheWeeknd from "@/assets/images/artist-the-weeknd.svg";
import artistTaylorSwift from "@/assets/images/artist-taylor-swift.svg";
import artistDrake from "@/assets/images/artist-drake.svg";
import albumAfterHours from "@/assets/images/album-after-hours.svg";
import albumMidnights from "@/assets/images/album-midnights.svg";
import albumCertifiedLoverBoy from "@/assets/images/album-certified-lover-boy.svg";

export const useMusicStore = defineStore("music", () => {
  // State
  const artists = ref<Artist[]>([
    {
      id: 1,
      name: "The Weeknd",
      image: artistTheWeeknd,
      monthlyListeners: 85000000,
      totalStreams: 150000000,
      albums: 6,
      genre: "R&B/Pop",
      revenue: 12500000,
    },
    {
      id: 2,
      name: "Taylor Swift",
      image: artistTaylorSwift,
      monthlyListeners: 92000000,
      totalStreams: 200000000,
      albums: 10,
      genre: "Pop",
      revenue: 18500000,
    },
    {
      id: 3,
      name: "Drake",
      image: artistDrake,
      monthlyListeners: 78000000,
      totalStreams: 175000000,
      albums: 8,
      genre: "Hip-Hop",
      revenue: 15200000,
    },
  ]);

  const albums = ref<Album[]>([
    {
      id: 1,
      title: "After Hours",
      artist: "The Weeknd",
      artistId: 1,
      cover: albumAfterHours,
      releaseDate: "2020-03-20",
      streams: 45000000,
      revenue: 4500000,
    },
    {
      id: 2,
      title: "Midnights",
      artist: "Taylor Swift",
      artistId: 2,
      cover: albumMidnights,
      releaseDate: "2022-10-21",
      streams: 68000000,
      revenue: 6800000,
    },
    {
      id: 3,
      title: "Certified Lover Boy",
      artist: "Drake",
      artistId: 3,
      cover: albumCertifiedLoverBoy,
      releaseDate: "2021-09-03",
      streams: 52000000,
      revenue: 5200000,
    },
  ]);

  const recentStreams = ref<Stream[]>([
    {
      id: 1,
      trackName: "Blinding Lights",
      artist: "The Weeknd",
      date: "2024-01-15",
      count: 1250000,
      revenue: 12500,
    },
    {
      id: 2,
      trackName: "Anti-Hero",
      artist: "Taylor Swift",
      date: "2024-01-15",
      count: 980000,
      revenue: 9800,
    },
    {
      id: 3,
      trackName: "Rich Flex",
      artist: "Drake",
      date: "2024-01-14",
      count: 876000,
      revenue: 8760,
    },
  ]);

  // Getters
  const dashboardStats = computed<DashboardStats>(() => ({
    totalStreams: artists.value.reduce((sum, artist) => sum + artist.totalStreams, 0),
    totalRevenue: artists.value.reduce((sum, artist) => sum + artist.revenue, 0),
    totalArtists: artists.value.length,
    activeListeners: artists.value.reduce((sum, artist) => sum + artist.monthlyListeners, 0),
    growth: 23.5,
  }));

  // Actions
  function addArtist(artist: Artist) {
    artists.value.push(artist);
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
    dashboardStats,
    addArtist,
    getArtistById,
    getAlbumsByArtist,
  };
});
