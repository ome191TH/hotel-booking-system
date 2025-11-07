-- ระบบสั่งอาหาร Hotel Booking System
-- สร้างวันที่: 2025-11-07

-- ตาราง 1: เมนูอาหาร
CREATE TABLE IF NOT EXISTS `food_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'ชื่อเมนู',
  `category` enum('rice','noodle','special') NOT NULL COMMENT 'หมวดหมู่: ข้าว, เส้น, พิเศษ',
  `base_price` decimal(10,2) NOT NULL COMMENT 'ราคาพื้นฐาน',
  `has_protein_option` tinyint(1) DEFAULT 1 COMMENT 'มีตัวเลือกโปรตีนหรือไม่',
  `protein_extra_price` decimal(10,2) DEFAULT 5.00 COMMENT 'ราคาเพิ่มสำหรับทะเล',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายเมนู',
  `image` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์รูปภาพ',
  `is_available` tinyint(1) DEFAULT 1 COMMENT 'มีจำหน่ายหรือไม่',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง 2: ออเดอร์อาหาร
CREATE TABLE IF NOT EXISTS `food_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID ผู้ใช้ (ถ้ามี)',
  `booking_id` int(11) DEFAULT NULL COMMENT 'ID การจอง (ถ้าสั่งพร้อมจองห้อง)',
  `customer_name` varchar(100) NOT NULL COMMENT 'ชื่อผู้สั่ง',
  `customer_phone` varchar(20) NOT NULL COMMENT 'เบอร์โทรศัพท์',
  `room_number` varchar(20) DEFAULT NULL COMMENT 'เลขห้องพัก',
  `delivery_address` text DEFAULT NULL COMMENT 'ที่อยู่จัดส่ง (ถ้าไม่ใช่ห้องพัก)',
  `total_price` decimal(10,2) NOT NULL COMMENT 'ราคารวมทั้งหมด',
  `special_note` text DEFAULT NULL COMMENT 'หมายเหตุพิเศษ',
  `status` enum('pending','preparing','ready','delivered','cancelled') DEFAULT 'pending' COMMENT 'สถานะ: รอดำเนินการ, กำลังทำ, พร้อมแล้ว, จัดส่งแล้ว, ยกเลิก',
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาสั่ง',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง 3: รายการอาหารในออเดอร์
CREATE TABLE IF NOT EXISTS `food_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT 'ID ออเดอร์',
  `menu_id` int(11) NOT NULL COMMENT 'ID เมนูอาหาร',
  `menu_name` varchar(100) NOT NULL COMMENT 'ชื่อเมนู (บันทึกไว้กรณีเมนูถูกลบ)',
  `protein_type` enum('pork','chicken','seafood','none') DEFAULT 'pork' COMMENT 'ประเภทโปรตีน: หมู, ไก่, ทะเล, ไม่มี',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'จำนวน',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'ราคาต่อหน่วย',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'ราคารวมรายการนี้',
  `note` varchar(255) DEFAULT NULL COMMENT 'หมายเหตุรายการ',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `food_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `food_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- เพิ่ม Foreign Key
ALTER TABLE `food_orders`
  ADD CONSTRAINT `food_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `food_orders_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL;

-- ใส่ข้อมูลเมนูอาหาร

-- เมนูข้าว
INSERT INTO `food_menu` (`name`, `category`, `base_price`, `has_protein_option`, `protein_extra_price`, `description`) VALUES
('ผัดกะเพรา', 'rice', 40.00, 1, 5.00, 'ข้าวผัดกะเพราหอมกรุ่น รสจัดจ้าน'),
('ผัดเครื่องแกง', 'rice', 40.00, 1, 5.00, 'ผัดเครื่องแกงเผ็ดหอม อร่อยถูกปาก'),
('ผัดพริกหยวก', 'rice', 40.00, 1, 5.00, 'ผัดพริกหยวกเผ็ดร้อน รสชาติเข้มข้น'),
('ข้าวผัด', 'rice', 40.00, 1, 5.00, 'ข้าวผัดหอมกรุ่น ผัดสดใหม่ทุกจาน'),
('ไข่เจียวหมูสับ', 'rice', 30.00, 0, 0.00, 'ไข่เจียวหมูสับฟู นุ่ม อร่อย'),
('ข้าวหมูทอดกระเทียม', 'rice', 40.00, 0, 0.00, 'หมูทอดกระเทียมกรอบนอกนุ่มใน'),
('ผัดผักรวม', 'rice', 40.00, 1, 5.00, 'ผัดผักรวมมิตรสดใหม่ มีประโยชน์'),
('ผัดผักบุ้ง', 'rice', 40.00, 1, 5.00, 'ผักบุ้งไฟแดง กรอบอร่อย');

-- เมนูเส้น
INSERT INTO `food_menu` (`name`, `category`, `base_price`, `has_protein_option`, `protein_extra_price`, `description`) VALUES
('ราดหน้า', 'noodle', 40.00, 1, 5.00, 'ราดหน้าน้ำข้นเข้มข้น อร่อยหอมหวาน'),
('ผัดซีอิ๊ว', 'noodle', 40.00, 1, 5.00, 'ผัดซีอิ๊วหอมกรุ่น รสชาติกลมกล่อม'),
('สุกี้', 'noodle', 40.00, 1, 5.00, 'สุกี้น้ำร้อนๆ รสชาติจัดจ้าน'),
('สุกี้แห้ง', 'noodle', 40.00, 1, 5.00, 'สุกี้แห้งรสเด็ด น้ำจิ้มรสเผ็ด'),
('มาม่าผัดเครื่อง', 'noodle', 40.00, 1, 5.00, 'มาม่าผัดเครื่องแกง รสจัดจ้าน'),
('ผัดมาม่า', 'noodle', 40.00, 1, 5.00, 'ผัดมาม่าธรรมดา อร่อยง่ายๆ');

-- เมนูพิเศษ
INSERT INTO `food_menu` (`name`, `category`, `base_price`, `has_protein_option`, `protein_extra_price`, `description`) VALUES
('ไข่ดาว', 'special', 5.00, 0, 0.00, 'ไข่ดาวไข่สด ทอดสุกกำลังดี'),
('ไข่เจียว', 'special', 10.00, 0, 0.00, 'ไข่เจียวฟูนุ่ม หอมกรุ่น'),
('พิเศษ (เพิ่มปริมาณ)', 'special', 10.00, 0, 0.00, 'เพิ่มปริมาณหรือเครื่องเพิ่มเติม'),
('ข้าวเปล่า', 'special', 10.00, 0, 0.00, 'ข้าวสวยร้อนๆ หอมนุ่ม');

-- สร้าง View สำหรับรายงาน
CREATE OR REPLACE VIEW `food_orders_summary` AS
SELECT 
    fo.id,
    fo.customer_name,
    fo.customer_phone,
    fo.room_number,
    fo.total_price,
    fo.status,
    fo.order_date,
    COUNT(foi.id) as total_items,
    u.username,
    b.checkin_date
FROM food_orders fo
LEFT JOIN users u ON fo.user_id = u.id
LEFT JOIN bookings b ON fo.booking_id = b.id
LEFT JOIN food_order_items foi ON fo.id = foi.order_id
GROUP BY fo.id
ORDER BY fo.order_date DESC;
