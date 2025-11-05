-- คำสั่ง SQL สำหรับอัพเดทฐานข้อมูลเป็นระบบใหม่
-- รันคำสั่งนี้ใน phpMyAdmin

USE hotel_db;

-- 1. ลบข้อมูลการจองเดิม (ถ้ามี)
DELETE FROM bookings;

-- 2. ลบห้องเก่าทั้งหมด
DELETE FROM rooms;

-- 3. ลบคอลัมน์เก่า available_rooms (ถ้ามี)
ALTER TABLE rooms DROP COLUMN IF EXISTS available_rooms;

-- 4. เพิ่มคอลัมน์ room_type ในตาราง rooms
ALTER TABLE rooms 
ADD COLUMN room_type ENUM('standard', 'superior', 'deluxe') NOT NULL DEFAULT 'standard' AFTER name;

-- 5. เพิ่มคอลัมน์สำหรับเก็บจำนวนห้องแยกตามประเภทเตียง
ALTER TABLE rooms 
ADD COLUMN single_bed_rooms INT NOT NULL DEFAULT 0 AFTER image,
ADD COLUMN twin_bed_rooms INT NOT NULL DEFAULT 0 AFTER single_bed_rooms;

-- 6. เพิ่มคอลัมน์ bed_type ในตาราง bookings
ALTER TABLE bookings 
ADD COLUMN bed_type ENUM('single', 'twin') NOT NULL DEFAULT 'single' AFTER room_id;

-- 7. ใส่ข้อมูลห้องพักใหม่ (3 ประเภท)
INSERT INTO rooms (name, room_type, price, details, image, single_bed_rooms, twin_bed_rooms) VALUES
-- ห้องมาตรฐาน (Standard) - เดี่ยว 1 ห้อง + แฝด 1 ห้อง
('ห้องมาตรฐาน', 'standard', 500.00, 'ห้องพักมาตรฐาน สะอาด สะดวกสบาย พร้อมสิ่งอำนวยความสะดวกครบครัน Wi-Fi ฟรี', 'standard.jpg', 1, 1),

-- ห้องซูพีเรีย (Superior) - เดี่ยว 1 ห้อง + แฝด 1 ห้อง
('ห้องซูพีเรีย', 'superior', 700.00, 'ห้องพักซูพีเรีย พื้นที่กว้างขวาง ตกแต่งสวยงาม วิวสวย พร้อมสิ่งอำนวยความสะดวกระดับพรีเมียม', 'superior.jpg', 1, 1),

-- ห้องดีลักซ์ (Deluxe) - เดี่ยว 1 ห้อง + แฝด 1 ห้อง
('ห้องดีลักซ์', 'deluxe', 900.00, 'ห้องพักดีลักซ์ หรูหรา พื้นที่กว้างที่สุด ตกแต่งพิเศษ วิวทะเลสวยงาม พร้อมบริการระดับพรีเมียม', 'deluxe.jpg', 1, 1);

-- เสร็จสิ้น! ตอนนี้มีห้องพัก 3 ประเภท แต่ละประเภทมี: เตียงเดี่ยว 1 ห้อง + เตียงแฝด 1 ห้อง
