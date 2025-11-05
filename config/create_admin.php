<?php
// ⚠️ ลบไฟล์นี้ทันทีหลังใช้งาน!

include 'db.php';

// ตรวจสอบว่ามี admin อยู่แล้วหรือไม่
$check = $conn->query("SELECT * FROM users WHERE email = 'admin@hotel.com'");

if ($check->num_rows > 0) {
    echo "<h2 style='color:orange;'>⚠️ Admin มีอยู่แล้ว</h2>";
    echo "<p>Email: admin@hotel.com</p>";
    echo "<p><strong style='color:red;'>กรุณาลบไฟล์ create_admin.php ทันที!</strong></p>";
    echo "<a href='../public/login.php'>ไปหน้า Login</a>";
} else {
    $username = 'Admin';
    $email = 'admin@hotel.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<h2 style='color:green;'>✅ สร้าง Admin สำเร็จ!</h2>";
        echo "<p><strong>Email:</strong> admin@hotel.com</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<hr>";
        echo "<p style='color:red; font-size:20px;'><strong>⚠️ ลบไฟล์ create_admin.php ทันที!</strong></p>";
        echo "<a href='../public/login.php'>ไปหน้า Login</a>";
    } else {
        echo "<h2 style='color:red;'>❌ เกิดข้อผิดพลาด</h2>";
        echo "<p>" . $conn->error . "</p>";
    }
}

$conn->close();
?>