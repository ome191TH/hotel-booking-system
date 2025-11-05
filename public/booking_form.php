<?php
session_start();

// ตรวจสอบว่า login หรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include '../config/db.php';

// รับค่า room_id จาก URL (ถ้ามี)
$selected_room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$selected_room = null;

// ถ้ามีการเลือกห้องมาจาก room_detail
if ($selected_room_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ? AND available_rooms > 0");
    $stmt->bind_param("i", $selected_room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $selected_room = $result->fetch_assoc();
    }
    $stmt->close();
}

// Fetch available rooms for the dropdown
$query = "SELECT * FROM rooms WHERE available_rooms > 0";
$result = mysqli_query($conn, $query);
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพัก - Hotel Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🏨 Hotel Booking</a>
            <div class="ml-auto">
                <span class="text-white mr-3">สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="index.php" class="btn btn-outline-light btn-sm mr-2">หน้าแรก</a>
                <a href="my_bookings.php" class="btn btn-outline-light btn-sm mr-2">การจองของฉัน</a>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="btn btn-outline-warning btn-sm mr-2">Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📝 ฟอร์มจองห้องพัก</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($selected_room): ?>
                            <div class="alert alert-info">
                                <strong>ห้องที่เลือก:</strong> <?php echo htmlspecialchars($selected_room['name']); ?> - 
                                <strong>฿<?php echo number_format($selected_room['price'], 2); ?></strong> / คืน
                            </div>
                        <?php endif; ?>

                        <form action="booking_save.php" method="POST" id="bookingForm">
                            <div class="form-group">
                                <label for="room">เลือกห้องพัก <span class="text-danger">*</span></label>
                                <select class="form-control" id="room" name="room_id" required>
                                    <option value="">-- กรุณาเลือกห้องพัก --</option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?php echo $room['id']; ?>" 
                                                <?php echo ($selected_room_id == $room['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($room['name']); ?> - 
                                            ฿<?php echo number_format($room['price'], 2); ?> 
                                            (ว่าง: <?php echo $room['available_rooms']; ?> ห้อง)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">ชื่อผู้เข้าพัก <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="กรอกชื่อ-นามสกุล" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="0812345678" pattern="[0-9]{10}" required>
                                <small class="form-text text-muted">กรอกตัวเลข 10 หลัก</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="checkin_date">วันที่เช็คอิน <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="checkin_date" name="checkin_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="checkout_date">วันที่เช็คเอาท์ <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="checkout_date" name="checkout_date" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-lg">✅ ยืนยันการจอง</button>
                                <a href="index.php" class="btn btn-secondary btn-block">ยกเลิก</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ป้องกันเลือกวันย้อนหลัง
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin_date').min = today;
        document.getElementById('checkout_date').min = today;

        // เช็คอินต้องก่อนเช็คเอาท์
        document.getElementById('checkin_date').addEventListener('change', function() {
            const checkin = new Date(this.value);
            const nextDay = new Date(checkin);
            nextDay.setDate(nextDay.getDate() + 1);
            document.getElementById('checkout_date').min = nextDay.toISOString().split('T')[0];
        });
    </script>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>