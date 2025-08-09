-- Create database if not exists
CREATE DATABASE IF NOT EXISTS lapangan_service;
USE lapangan_service;

-- Grant privileges
GRANT ALL PRIVILEGES ON lapangan_service.* TO 'lapangan_user'@'%';
FLUSH PRIVILEGES;

-- Basic table structure (you can modify this based on your needs)
CREATE TABLE IF NOT EXISTS lapangan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    description TEXT,
    price_per_hour DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'maintenance', 'unavailable') DEFAULT 'available',
    facilities JSON,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO lapangan (name, type, description, price_per_hour, status, facilities) VALUES
('Lapangan Futsal A', 'Futsal', 'Lapangan futsal standar dengan rumput sintetis', 100000.00, 'available', '["Shower", "Parking", "Lighting"]'),
('Lapangan Badminton 1', 'Badminton', 'Lapangan badminton indoor dengan AC', 50000.00, 'available', '["AC", "Shower", "Parking"]'),
('Lapangan Basket Outdoor', 'Basketball', 'Lapangan basket outdoor standar', 75000.00, 'available', '["Lighting", "Parking"]');
