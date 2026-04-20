-- Create database
CREATE DATABASE IF NOT EXISTS trms;
USE trms;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_price (price)
);

-- Artists table
CREATE TABLE IF NOT EXISTS artists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    image VARCHAR(500),
    monthly_listeners BIGINT DEFAULT 0,
    total_streams BIGINT DEFAULT 0,
    albums_count INT DEFAULT 0,
    genre VARCHAR(100),
    revenue DECIMAL(15, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_genre (genre)
);

-- Albums table
CREATE TABLE IF NOT EXISTS albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    artist VARCHAR(200) NOT NULL,
    artist_id INT NOT NULL,
    cover VARCHAR(500),
    release_date DATE,
    streams BIGINT DEFAULT 0,
    revenue DECIMAL(15, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_artist_id (artist_id),
    INDEX idx_title (title),
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
);

-- Streams table
CREATE TABLE IF NOT EXISTS streams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    track_name VARCHAR(200) NOT NULL,
    artist VARCHAR(200) NOT NULL,
    stream_date DATE NOT NULL,
    stream_count BIGINT DEFAULT 0,
    revenue DECIMAL(15, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_track_name (track_name),
    INDEX idx_stream_date (stream_date)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample products
INSERT INTO products (name, description, price, stock, user_id) VALUES
('Sample Product 1', 'This is a sample product description', 29.99, 100, 1),
('Sample Product 2', 'Another great product', 49.99, 50, 1);

-- Insert sample artists
INSERT INTO artists (name, image, monthly_listeners, total_streams, albums_count, genre, revenue) VALUES
('The Weeknd', '/assets/images/artist-the-weeknd.svg', 85000000, 150000000, 6, 'R&B/Pop', 12500000),
('Taylor Swift', '/assets/images/artist-taylor-swift.svg', 92000000, 200000000, 10, 'Pop', 18500000),
('Drake', '/assets/images/artist-drake.svg', 78000000, 175000000, 8, 'Hip-Hop', 15200000);

-- Insert sample albums
INSERT INTO albums (title, artist, artist_id, cover, release_date, streams, revenue) VALUES
('After Hours', 'The Weeknd', 1, '/assets/images/album-after-hours.svg', '2020-03-20', 45000000, 4500000),
('Midnights', 'Taylor Swift', 2, '/assets/images/album-midnights.svg', '2022-10-21', 68000000, 6800000),
('Certified Lover Boy', 'Drake', 3, '/assets/images/album-certified-lover-boy.svg', '2021-09-03', 52000000, 5200000);

-- Insert sample streams
INSERT INTO streams (track_name, artist, stream_date, stream_count, revenue) VALUES
('Blinding Lights', 'The Weeknd', '2024-01-15', 1250000, 12500),
('Anti-Hero', 'Taylor Swift', '2024-01-15', 980000, 9800),
('Rich Flex', 'Drake', '2024-01-14', 876000, 8760);