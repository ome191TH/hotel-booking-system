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
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ? AND (single_bed_rooms > 0 OR twin_bed_rooms > 0)");
    $stmt->bind_param("i", $selected_room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $selected_room = $result->fetch_assoc();
    }
    $stmt->close();
}

// Fetch available rooms for the dropdown
$query = "SELECT * FROM rooms WHERE single_bed_rooms > 0 OR twin_bed_rooms > 0";
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
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">จองห้องพัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php">การจองของฉัน</a>
                    </li>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <span class="nav-link text-white">สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm ml-2" href="logout.php">ออกจากระบบ</a>
                    </li>
                </ul>
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
                                <label for="room">เลือกประเภทห้องพัก <span class="text-danger">*</span></label>
                                <select class="form-control" id="room" name="room_id" required>
                                    <option value="">-- กรุณาเลือกประเภทห้องพัก --</option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?php echo $room['id']; ?>" 
                                                data-price="<?php echo $room['price']; ?>"
                                                data-single="<?php echo $room['single_bed_rooms']; ?>"
                                                data-twin="<?php echo $room['twin_bed_rooms']; ?>"
                                                <?php echo ($selected_room_id == $room['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($room['name']); ?> - 
                                            ฿<?php echo number_format($room['price'], 2); ?> / คืน
                                            (เดี่ยว: <?php echo $room['single_bed_rooms']; ?>, แฝด: <?php echo $room['twin_bed_rooms']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="bed_type">เลือกประเภทเตียง <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="bed_single" name="bed_type" value="single" class="custom-control-input" required>
                                            <label class="custom-control-label" for="bed_single">
                                                <strong>🛏️ เตียงเดี่ยว</strong><br>
                                                <small class="text-muted" id="single_available">1 เตียง (Single Bed)</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="bed_twin" name="bed_type" value="twin" class="custom-control-input" required>
                                            <label class="custom-control-label" for="bed_twin">
                                                <strong>🛏️🛏️ เตียงแฝด</strong><br>
                                                <small class="text-muted" id="twin_available">2 เตียง (Twin Beds)</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted" id="bed_info">กรุณาเลือกประเภทห้องก่อน</small>
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
                            
                            <div class="form-group">
                                <label for="email">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="example@email.com" required>
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
                                <label for="special_requests">คำขอพิเศษ / บริการเพิ่มเติม</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" 
                                          rows="3" placeholder="ระบุคำขอพิเศษ เช่น เตียงเสริม, อาหารเช้า, ไม่สูบบุหรี่ (ถ้ามี)"></textarea>
                                <small class="form-text text-muted">ไม่บังคับ - สามารถเว้นว่างได้</small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-lg">✅ ยืนยันการจอง</button>
                                <a href="rooms.php" class="btn btn-secondary btn-block">ยกเลิก</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ป้องกันเลือกวันย้อนหลัง (ใช้ local timezone)
        const today = new Date();
        today.setHours(0, 0, 0, 0); // ตั้งเวลาเป็น 00:00:00
        const todayStr = today.toISOString().split('T')[0];
        
        document.getElementById('checkin_date').min = todayStr;
        document.getElementById('checkout_date').min = todayStr;

        // เช็คอินต้องก่อนเช็คเอาท์
        document.getElementById('checkin_date').addEventListener('change', function() {
            const checkin = new Date(this.value + 'T00:00:00');
            const nextDay = new Date(checkin);
            nextDay.setDate(nextDay.getDate() + 1);
            document.getElementById('checkout_date').min = nextDay.toISOString().split('T')[0];
        });

        // อัพเดทจำนวนห้องว่างตามประเภทที่เลือก
        document.getElementById('room').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const singleRooms = parseInt(selectedOption.getAttribute('data-single')) || 0;
            const twinRooms = parseInt(selectedOption.getAttribute('data-twin')) || 0;
            
            // อัพเดทข้อมูลห้องว่าง
            const singleRadio = document.getElementById('bed_single');
            const twinRadio = document.getElementById('bed_twin');
            const bedInfo = document.getElementById('bed_info');
            
            if (singleRooms > 0) {
                singleRadio.disabled = false;
                document.getElementById('single_available').textContent = `ว่าง ${singleRooms} ห้อง`;
                document.getElementById('single_available').classList.remove('text-danger');
                document.getElementById('single_available').classList.add('text-success');
            } else {
                singleRadio.disabled = true;
                singleRadio.checked = false;
                document.getElementById('single_available').textContent = 'เต็ม';
                document.getElementById('single_available').classList.remove('text-success');
                document.getElementById('single_available').classList.add('text-danger');
            }
            
            if (twinRooms > 0) {
                twinRadio.disabled = false;
                document.getElementById('twin_available').textContent = `ว่าง ${twinRooms} ห้อง`;
                document.getElementById('twin_available').classList.remove('text-danger');
                document.getElementById('twin_available').classList.add('text-success');
            } else {
                twinRadio.disabled = true;
                twinRadio.checked = false;
                document.getElementById('twin_available').textContent = 'เต็ม';
                document.getElementById('twin_available').classList.remove('text-success');
                document.getElementById('twin_available').classList.add('text-danger');
            }
            
            bedInfo.textContent = `เตียงเดี่ยว: ${singleRooms} ห้อง, เตียงแฝด: ${twinRooms} ห้อง`;
        });

        // เรียกใช้ครั้งแรกถ้ามีการเลือกห้องไว้แล้ว
        if (document.getElementById('room').value) {
            document.getElementById('room').dispatchEvent(new Event('change'));
        }
    </script>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>