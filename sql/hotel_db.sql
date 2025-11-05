-- SQL script to create the hotel_db database and the required tables for the hotel booking system

-- Create the database
CREATE DATABASE IF NOT EXISTS hotel_db;

-- Use the created database
USE hotel_db;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    details TEXT,
    image VARCHAR(255),
    available_rooms INT NOT NULL
);

-- Create the bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Insert default admin account (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('Admin', 'admin@hotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample rooms
INSERT INTO rooms (name, price, details, image, available_rooms) VALUES
('Standard Room', 1000.00, 'ห้องพักมาตรฐาน เตียงเดี่ยว ขนาด 20 ตร.ม.', 'standard.jpg', 5),
('Deluxe Room', 1500.00, 'ห้องพักระดับดีลักซ์ เตียงคู่ ขนาด 30 ตร.ม.', 'deluxe.jpg', 3),
('Suite Room', 2500.00, 'ห้องสวีท กว้างขวาง วิวสวย ขนาด 50 ตร.ม.', 'suite.jpg', 2);