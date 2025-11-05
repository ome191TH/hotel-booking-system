<?php
session_start();
// Include database connection
include '../config/db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    // Check for overlapping bookings
    $query = "SELECT * FROM bookings WHERE room_id = ? AND (checkin_date < ? AND checkout_date > ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $room_id, $checkout_date, $checkin_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $success = false;
    $message = "";

    if ($result->num_rows > 0) {
        $message = "ห้องพักไม่ว่างในช่วงวันที่เลือก กรุณาลองใหม่";
    } else {
        // Insert booking into database
        $insert_query = "INSERT INTO bookings (user_id, room_id, name, phone, checkin_date, checkout_date) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iissss", $user_id, $room_id, $name, $phone, $checkin_date, $checkout_date);

        if ($insert_stmt->execute()) {
            // ลดจำนวนห้องว่าง
            $update_query = "UPDATE rooms SET available_rooms = available_rooms - 1 WHERE id = ?";
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
                            <h3 class="text-success mb-3"><?php echo $message; ?></h3>
                            <p class="text-muted">คุณสามารถตรวจสอบการจองได้ที่เมนู "การจองของฉัน"</p>
                            <div class="mt-4">
                                <a href="my_bookings.php" class="btn btn-success btn-lg">ดูการจองของฉัน</a>
                                <a href="index.php" class="btn btn-outline-secondary">กลับหน้าแรก</a>
                            </div>
                        <?php else: ?>
                            <div class="text-danger mb-3" style="font-size: 80px;">❌</div>
                            <h3 class="text-danger mb-3"><?php echo $message; ?></h3>
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