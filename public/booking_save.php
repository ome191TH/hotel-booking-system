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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Result</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> alert-custom text-center">
                    <h4><?php echo $message; ?></h4>
                    <hr>
                    <a href="index.php" class="btn <?php echo $success ? 'btn-success' : 'btn-danger'; ?>">กลับหน้าแรก</a>
                    <?php if (!$success): ?>
                        <a href="javascript:history.back()" class="btn btn-secondary">ลองใหม่</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>