# Hotel Booking System (ระบบจองห้องพักโรงแรม)

## 📖 ภาพรวมโครงงาน
ระบบจองห้องพักโรงแรมออนไลน์ พัฒนาด้วย PHP และ MySQL เหมาะสำหรับโครงงานระดับ ปวส.

## ✨ ฟีเจอร์หลัก
1. ✅ แสดงรายการห้องพักทั้งหมดพร้อมรายละเอียด
2. ✅ ระบบสมาชิก (สมัครสมาชิก/เข้าสู่ระบบ)
3. ✅ จองห้องพักพร้อมตรวจสอบความว่าง
4. ✅ ป้องกันการจองซ้ำ (Overlapping Bookings)
5. ✅ อัพเดทจำนวนห้องว่างอัตโนมัติ
6. ✅ ดูประวัติการจองของตัวเอง
7. ✅ Admin Panel สำหรับจัดการการจอง
8. ✅ Responsive Design (Bootstrap 4)

## 🛠️ เทคโนโลยีที่ใช้
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, Bootstrap 4
- **Server:** Apache (XAMPP)

## 📂 โครงสร้างโครงงาน
```
hotel-booking-system/
├── assets/
│   ├── css/
│   │   └── style.css          # ไฟล์ CSS หลัก
│   ├── images/                # โฟลเดอร์รูปภาพห้องพัก
│   └── js/
│       └── script.js          # JavaScript
├── config/
│   ├── db.php                 # การเชื่อมต่อฐานข้อมูล
│   └── create_admin.php       # สร้าง admin (ลบหลังใช้งาน!)
├── public/
│   ├── index.php              # หน้าแรก
│   ├── login.php              # เข้าสู่ระบบ
│   ├── register.php           # สมัครสมาชิก
│   ├── room_detail.php        # รายละเอียดห้องพัก
│   ├── booking_form.php       # ฟอร์มจองห้อง
│   ├── booking_save.php       # บันทึกการจอง
│   ├── my_bookings.php        # การจองของฉัน
│   ├── admin.php              # หน้าผู้ดูแลระบบ
│   └── logout.php             # ออกจากระบบ
├── sql/
│   └── hotel_db.sql           # ไฟล์ SQL สำหรับสร้างฐานข้อมูล
└── README.md
```

## 🚀 วิธีติดตั้ง

### ขั้นตอนที่ 1: ติดตั้ง XAMPP
1. ดาวน์โหลด XAMPP จาก https://www.apachefriends.org/
2. ติดตั้งและเปิด Apache + MySQL

### ขั้นตอนที่ 2: วางไฟล์โปรเจกต์
1. คัดลอกโฟลเดอร์ `hotel-booking-system` ไปที่ `C:\xampp\htdocs\`
2. เส้นทางที่ถูกต้อง: `C:\xampp\htdocs\hotel-booking-system\`

### ขั้นตอนที่ 3: สร้างฐานข้อมูล
1. เปิด phpMyAdmin: http://localhost/phpmyadmin
2. สร้างฐานข้อมูลใหม่ชื่อ `hotel_db`
3. Import ไฟล์ `sql/hotel_db.sql`

### ขั้นตอนที่ 4: ตั้งค่าการเชื่อมต่อ
1. เปิดไฟล์ `config/db.php`
2. ตรวจสอบค่าต่อไปนี้:
```php
$host = 'localhost';
$db_name = 'hotel_db';
$username = 'root';
$password = '';        // ใส่รหัสผ่าน MySQL (ถ้ามี)
```

### ขั้นตอนที่ 5: สร้างบัญชี Admin
1. เปิดเบราว์เซอร์ไปที่: http://localhost/hotel-booking-system/config/create_admin.php
2. ระบบจะสร้างบัญชี admin อัตโนมัติ
   - **Email:** admin@hotel.com
   - **Password:** admin123
3. **⚠️ สำคัญ:** ลบไฟล์ `create_admin.php` ทันทีหลังใช้งาน!

### ขั้นตอนที่ 6: เริ่มใช้งาน
1. เปิดเบราว์เซอร์: http://localhost/hotel-booking-system/public/index.php
2. ลองสมัครสมาชิกและจองห้องพัก
3. Login เป็น admin เพื่อจัดการระบบ

## 👤 บัญชีทดสอบ

### Admin
- **Email:** admin@hotel.com
- **Password:** admin123

### User (สร้างเองผ่านหน้าสมัครสมาชิก)

## 🗄️ โครงสร้างฐานข้อมูล

### ตาราง `users`
```sql
id, username, email, password, role, created_at
```

### ตาราง `rooms`
```sql
id, name, price, details, image, available_rooms
```

### ตาราง `bookings`
```sql
id, user_id, room_id, name, phone, checkin_date, checkout_date, created_at
```

## 📸 การเพิ่มรูปภาพห้องพัก
1. วางรูปภาพในโฟลเดอร์ `assets/images/`
2. ตั้งชื่อไฟล์ให้ตรงกับชื่อในฐานข้อมูล (เช่น `standard.jpg`)
3. รูปที่ไม่มีจะแสดง placeholder อัตโนมัติ

## 🔐 ความปลอดภัย
- ✅ Password เข้ารหัสด้วย `password_hash()`
- ✅ Prepared Statement ป้องกัน SQL Injection
- ✅ Session Management
- ✅ Input Validation
- ✅ XSS Protection ด้วย `htmlspecialchars()`

## 📝 การใช้งาน

### สำหรับผู้ใช้ทั่วไป:
1. สมัครสมาชิก / เข้าสู่ระบบ
2. เลือกห้องพักที่ต้องการ
3. กรอกข้อมูลในฟอร์มจอง
4. ตรวจสอบการจองในเมนู "การจองของฉัน"

### สำหรับ Admin:
1. Login ด้วยบัญชี admin
2. ดูรายการจองทั้งหมดใน Admin Panel
3. สามารถลบการจองได้ (ห้องว่างจะเพิ่มกลับอัตโนมัติ)

## 🐛 แก้ไขปัญหาที่พบบ่อย

### ปัญหา: ไม่สามารถเชื่อมต่อฐานข้อมูล
- ตรวจสอบว่า MySQL ทำงานอยู่
- ตรวจสอบ username/password ใน `config/db.php`

### ปัญหา: หน้าเว็บแสดง error PHP
- เปิด error reporting ใน `php.ini`
- ตรวจสอบ path ของไฟล์

### ปัญหา: รูปภาพไม่แสดง
- ตรวจสอบชื่อไฟล์รูปใน database
- วางรูปในโฟลเดอร์ `assets/images/`

## 📊 Features ที่ทำงาน

| ฟีเจอร์ | สถานะ |
|--------|-------|
| แสดงรายการห้องพัก | ✅ |
| ระบบสมาชิก | ✅ |
| จองห้องพัก | ✅ |
| ตรวจสอบความว่าง | ✅ |
| อัพเดทห้องว่างอัตโนมัติ | ✅ |
| ดูประวัติการจอง | ✅ |
| Admin Panel | ✅ |
| Responsive Design | ✅ |

## 📞 การสนับสนุน
หากพบปัญหาหรือข้อสงสัย สามารถติดต่อได้ที่:
- อาจารย์ที่ปรึกษาโครงงาน

## 📄 License
โครงงานนี้พัฒนาเพื่อการศึกษา (ระดับ ปวส.)

---
**พัฒนาโดย:** [ชื่อของคุณ]  
**สถาบัน:** [ชื่อสถาบัน]  
**ปีการศึกษา:** 2568