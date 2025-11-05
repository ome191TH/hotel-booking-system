<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT b.id, r.name AS room_name, r.price, b.checkin_date, b.checkout_date, b.created_at,
          DATEDIFF(b.checkout_date, b.checkin_date) as nights
          FROM bookings b 
          JOIN rooms r ON b.room_id = r.id 
          WHERE b.user_id = ?
          ORDER BY b.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองของฉัน</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🏨 Hotel Booking</a>
            <div class="ml-auto">
                <a href="index.php" class="btn btn-outline-light">หน้าแรก</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">การจองของฉัน</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while($booking = $result->fetch_assoc()): 
                    $total = $booking['nights'] * $booking['price'];
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5><?php echo $booking['room_name']; ?></h5>
                            </div>
                            <div class="card-body">
                                <p><strong>วันเช็คอิน:</strong> <?php echo date('d/m/Y', strtotime($booking['checkin_date'])); ?></p>
                                <p><strong>วันเช็คเอาท์:</strong> <?php echo date('d/m/Y', strtotime($booking['checkout_date'])); ?></p>
                                <p><strong>จำนวนคืน:</strong> <?php echo $booking['nights']; ?> คืน</p>
                                <p><strong>ราคาต่อคืน:</strong> <?php echo number_format($booking['price'], 2); ?> บาท</p>
                                <p class="h5"><strong>ราคารวม:</strong> <span class="text-danger"><?php echo number_format($total, 2); ?> บาท</span></p>
                                <small class="text-muted">จองเมื่อ: <?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <h4>คุณยังไม่มีการจอง</h4>
                <a href="index.php" class="btn btn-primary mt-3">เลือกห้องพัก</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>