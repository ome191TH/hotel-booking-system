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
    
    // ดึงข้อมูล room_id และ bed_type ก่อนลบเพื่อเพิ่มจำนวนห้องกลับ
    $get_booking = $conn->prepare("SELECT room_id, bed_type FROM bookings WHERE id = ?");
    $get_booking->bind_param("i", $id);
    $get_booking->execute();
    $result = $get_booking->get_result();
    
    if ($booking_data = $result->fetch_assoc()) {
        $room_id = $booking_data['room_id'];
        $bed_type = $booking_data['bed_type'];
        
        // ลบการจอง
        $delete_stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        // เพิ่มจำนวนห้องกลับตามประเภทเตียง
        if ($bed_type == 'single') {
            $update_stmt = $conn->prepare("UPDATE rooms SET single_bed_rooms = single_bed_rooms + 1 WHERE id = ?");
        } else {
            $update_stmt = $conn->prepare("UPDATE rooms SET twin_bed_rooms = twin_bed_rooms + 1 WHERE id = ?");
        }
        $update_stmt->bind_param("i", $room_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $get_booking->close();
    
    header("Location: admin.php");
    exit();
}

$query = "SELECT b.id, r.name AS room_name, b.bed_type, u.username, b.name AS guest_name, b.phone, b.email, b.checkin_date, b.checkout_date, b.special_requests, b.created_at 
          FROM bookings b 
          JOIN rooms r ON b.room_id = r.id 
          LEFT JOIN users u ON b.user_id = u.id
          ORDER BY b.created_at DESC";
$result = $conn->query($query);

// นับสถิติ
$total_bookings = $result->num_rows;
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()['count'];
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
                <span class="text-white mr-3">ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="index.php" class="btn btn-outline-light mr-2">หน้าแรก</a>
                <a href="logout.php" class="btn btn-outline-danger">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- สถิติ -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">📋 การจองทั้งหมด</h5>
                        <h2><?php echo $total_bookings; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">👥 สมาชิกทั้งหมด</h5>
                        <h2><?php echo $total_users; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">🏨 ห้องพักทั้งหมด</h5>
                        <h2><?php echo $total_rooms; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-center mb-4">รายการจองทั้งหมด</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>ห้องพัก</th>
                        <th>ประเภทเตียง</th>
                        <th>ผู้จอง (User)</th>
                        <th>ชื่อผู้เข้าพัก</th>
                        <th>เบอร์โทร</th>
                        <th>อีเมล</th>
                        <th>เช็คอิน</th>
                        <th>เช็คเอาท์</th>
                        <th>คำขอพิเศษ</th>
                        <th>วันที่จอง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['bed_type'] == 'single' ? 'primary' : 'info'; ?>">
                                        <?php echo $row['bed_type'] == 'single' ? '🛏️ เตียงเดี่ยว' : '🛏️🛏️ เตียงแฝด'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['username'] ?? 'Guest'); ?></td>
                                <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['checkin_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['checkout_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['special_requests'] ?: '-'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center">ยังไม่มีการจอง</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>