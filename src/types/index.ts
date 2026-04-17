export interface Artist {
  id: number;
  name: string;
  image: string;
  monthlyListeners: number;
  totalStreams: number;
  albums: number;
  genre: string;
  revenue: number;
}

export interface Album {
  id: number;
  title: string;
  artist: string;
  artistId: number;
  cover: string;
  releaseDate: string;
  streams: number;
  revenue: number;
}

export interface Stream {
  id: number;
  trackName: string;
  artist: string;
  date: string;
  count: number;
  revenue: number;
}

export interface DashboardStats {
  totalStreams: number;
  totalRevenue: number;
  totalArtists: number;
  activeListeners: number;
  growth: number;
}
