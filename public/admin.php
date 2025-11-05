<?php
session_start();

// ตรวจสอบว่า login และเป็น admin หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

// Delete booking
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // ดึงข้อมูล room_id ก่อนลบเพื่อเพิ่มจำนวนห้องว่างกลับ
    $get_room = $conn->query("SELECT room_id FROM bookings WHERE id = $id");
    if ($room_data = $get_room->fetch_assoc()) {
        $room_id = $room_data['room_id'];
        
        // ลบการจอง
        $conn->query("DELETE FROM bookings WHERE id = $id");
        
        // เพิ่มจำนวนห้องว่างกลับ
        $conn->query("UPDATE rooms SET available_rooms = available_rooms + 1 WHERE id = $room_id");
    }
    
    header("Location: admin.php");
    exit();
}

$query = "SELECT b.id, r.name AS room_name, u.username, b.name AS guest_name, b.phone, b.checkin_date, b.checkout_date, b.created_at 
          FROM bookings b 
          JOIN rooms r ON b.room_id = r.id 
          LEFT JOIN users u ON b.user_id = u.id
          ORDER BY b.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Bookings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">🏨 Admin Panel</a>
            <div class="ml-auto">
                <span class="text-white mr-3">ยินดีต้อนรับ, <?php echo $_SESSION['username']; ?></span>
                <a href="index.php" class="btn btn-outline-light mr-2">หน้าแรก</a>
                <a href="logout.php" class="btn btn-outline-danger">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">รายการจองทั้งหมด</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>ห้องพัก</th>
                        <th>ผู้จอง (User)</th>
                        <th>ชื่อผู้เข้าพัก</th>
                        <th>เบอร์โทร</th>
                        <th>เช็คอิน</th>
                        <th>เช็คเอาท์</th>
                        <th>วันที่จอง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['room_name']; ?></td>
                                <td><?php echo $row['username'] ?? 'Guest'; ?></td>
                                <td><?php echo $row['guest_name']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['checkin_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['checkout_date'])); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">ยังไม่มีการจอง</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>