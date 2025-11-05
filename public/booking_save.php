<?php
session_start();
// Include database connection
include '../config/db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $bed_type = $_POST['bed_type'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $special_requests = isset($_POST['special_requests']) ? $_POST['special_requests'] : '';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    $success = false;
    $message = "";

    // ตรวจสอบวันที่ (Double validation: Client + Server)
    date_default_timezone_set('Asia/Bangkok'); // ตั้งเวลาไทย
    $today = date('Y-m-d');
    
    // เปรียบเทียบแบบ date only (ไม่สน timestamp)
    if ($checkin_date < $today) {
        $message = "ไม่สามารถจองย้อนหลังได้ กรุณาเลือกวันที่ตั้งแต่วันนี้เป็นต้นไป";
    } elseif ($checkout_date <= $checkin_date) {
        $message = "วันเช็คเอาท์ต้องมากกว่าวันเช็คอิน กรุณาตรวจสอบอีกครั้ง";
    } else {
        // ตรวจสอบจำนวนห้องว่างของประเภทเตียงที่เลือก
        $check_room = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
        $check_room->bind_param("i", $room_id);
        $check_room->execute();
        $room_result = $check_room->get_result();
        
        if ($room_result->num_rows == 0) {
            $message = "ไม่พบห้องพักที่เลือก กรุณาลองใหม่";
        } else {
            $room_data = $room_result->fetch_assoc();
            $available_count = ($bed_type == 'single') ? $room_data['single_bed_rooms'] : $room_data['twin_bed_rooms'];
            
            if ($available_count <= 0) {
                $bed_name = ($bed_type == 'single' ? 'เตียงเดี่ยว' : 'เตียงแฝด');
                $message = "ห้องพัก " . $bed_name . " เต็มแล้ว กรุณาเลือกประเภทอื่น";
            } else {
                // Check for overlapping bookings with same room and bed type
                $query = "SELECT * FROM bookings WHERE room_id = ? AND bed_type = ? AND (checkin_date < ? AND checkout_date > ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("isss", $room_id, $bed_type, $checkout_date, $checkin_date);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $bed_name = ($bed_type == 'single' ? 'เตียงเดี่ยว' : 'เตียงแฝด');
                    $message = "ห้องพัก " . $bed_name . " ไม่ว่างในช่วงวันที่เลือก กรุณาลองใหม่";
                } else {
                    // Insert booking into database
                    $insert_query = "INSERT INTO bookings (user_id, room_id, bed_type, name, phone, email, checkin_date, checkout_date, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_query);
                    $insert_stmt->bind_param("iisssssss", $user_id, $room_id, $bed_type, $name, $phone, $email, $checkin_date, $checkout_date, $special_requests);

                    if ($insert_stmt->execute()) {
                        // ลดจำนวนห้องว่างตามประเภทเตียง
                        if ($bed_type == 'single') {
                            $update_query = "UPDATE rooms SET single_bed_rooms = single_bed_rooms - 1 WHERE id = ?";
                        } else {
                            $update_query = "UPDATE rooms SET twin_bed_rooms = twin_bed_rooms - 1 WHERE id = ?";
                        }
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->bind_param("i", $room_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        $success = true;
                        $message = "การจองห้องพักสำเร็จ!";
                    } else {
                        $message = "เกิดข้อผิดพลาดในการจอง กรุณาลองใหม่";
                    }
                    $insert_stmt->close();
                }
                $stmt->close();
            }
        }
        $check_room->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'จองสำเร็จ' : 'เกิดข้อผิดพลาด'; ?> - Hotel Booking</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center p-5">
                        <?php if ($success): ?>
                            <div class="text-success mb-3" style="font-size: 80px;">✅</div>
                            <h3 class="text-success mb-3"><?php echo htmlspecialchars($message); ?></h3>
                            <p class="text-muted">คุณสามารถตรวจสอบการจองได้ที่เมนู "การจองของฉัน"</p>
                            <div class="mt-4">
                                <a href="my_bookings.php" class="btn btn-success btn-lg">ดูการจองของฉัน</a>
                                <a href="index.php" class="btn btn-outline-secondary">กลับหน้าแรก</a>
                            </div>
                        <?php else: ?>
                            <div class="text-danger mb-3" style="font-size: 80px;">❌</div>
                            <h3 class="text-danger mb-3"><?php echo htmlspecialchars($message); ?></h3>
                            <p class="text-muted">กรุณาตรวจสอบข้อมูลและลองอีกครั้ง</p>
                            <div class="mt-4">
                                <a href="booking_form.php" class="btn btn-primary btn-lg">ลองอีกครั้ง</a>
                                <a href="index.php" class="btn btn-outline-secondary">กลับหน้าแรก</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>