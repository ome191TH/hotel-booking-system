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
    room_type ENUM('standard', 'superior', 'deluxe') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    details TEXT,
    image VARCHAR(255),
    single_bed_rooms INT NOT NULL DEFAULT 0,
    twin_bed_rooms INT NOT NULL DEFAULT 0
);

-- Create the bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT NOT NULL,
    bed_type ENUM('single', 'twin') NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Insert default admin account (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('Admin', 'admin@hotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert rooms (ช้องนาง เรสซิเดนซ์ - 3 ประเภท)
INSERT INTO rooms (name, room_type, price, details, image, single_bed_rooms, twin_bed_rooms) VALUES
-- ห้องมาตรฐาน (Standard) - เดี่ยว 1 ห้อง + แฝด 1 ห้อง
('ห้องมาตรฐาน', 'standard', 500.00, 'ห้องพักมาตรฐาน สะอาด สะดวกสบาย พร้อมสิ่งอำนวยความสะดวกครบครัน Wi-Fi ฟรี', 'standard.jpg', 1, 1),

-- ห้องซูพีเรีย (Superior) - เดี่ยว 1 ห้อง + แฝด 1 ห้อง
('ห้องซูพีเรีย', 'superior', 700.00, 'ห้องพักซูพีเรีย พื้นที่กว้างขวาง ตกแต่งสวยงาม วิวสวย พร้อมสิ่งอำนวยความสะดวกระดับพรีเมียม', 'superior.jpg', 1, 1),

-- ห้องดีลักซ์ (Deluxe) - เดี่ยว 1 ห้อง + แฝด 1 ห้อง
('ห้องดีลักซ์', 'deluxe', 900.00, 'ห้องพักดีลักซ์ หรูหรา พื้นที่กว้างที่สุด ตกแต่งพิเศษ วิวทะเลสวยงาม พร้อมบริการระดับพรีเมียม', 'deluxe.jpg', 1, 1);