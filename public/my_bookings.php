<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT b.id, r.name AS room_name, r.price, b.bed_type, b.name, b.phone, b.email, b.checkin_date, b.checkout_date, b.special_requests, b.created_at,
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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองของฉัน - Hotel Booking</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🏨 Hotel Booking</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php"><i class="fas fa-bed"></i> จองห้องพัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attractions.php"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยว</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="my_bookings.php"><i class="fas fa-list"></i> การจองของฉัน</a>
                        </li>
                        <?php if($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php"><i class="fas fa-user-shield"></i> Admin</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <span class="nav-link text-white"><i class="fas fa-user"></i> สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-danger btn-sm ml-2" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ml-2" href="login.php">เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-success btn-sm ml-2" href="register.php">สมัครสมาชิก</a>
                        </li>
                    <?php endif; ?>
                </ul>
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
                                <h5>
                                    <?php echo htmlspecialchars($booking['room_name']); ?>
                                    <span class="badge badge-light ml-2">
                                        <?php echo $booking['bed_type'] == 'single' ? '🛏️ เตียงเดี่ยว' : '🛏️🛏️ เตียงแฝด'; ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p><strong>ชื่อผู้เข้าพัก:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
                                <p><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                                <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                                <hr>
                                <p><strong>ประเภทเตียง:</strong> 
                                    <span class="badge badge-<?php echo $booking['bed_type'] == 'single' ? 'primary' : 'info'; ?>">
                                        <?php echo $booking['bed_type'] == 'single' ? 'เตียงเดี่ยว (1 เตียง)' : 'เตียงแฝด (2 เตียง)'; ?>
                                    </span>
                                </p>
                                <p><strong>วันเช็คอิน:</strong> <?php echo date('d/m/Y', strtotime($booking['checkin_date'])); ?></p>
                                <p><strong>วันเช็คเอาท์:</strong> <?php echo date('d/m/Y', strtotime($booking['checkout_date'])); ?></p>
                                <p><strong>จำนวนคืน:</strong> <?php echo $booking['nights']; ?> คืน</p>
                                <p><strong>ราคาต่อคืน:</strong> <?php echo number_format($booking['price'], 2); ?> บาท</p>
                                <p class="h5"><strong>ราคารวม:</strong> <span class="text-danger"><?php echo number_format($total, 2); ?> บาท</span></p>
                                <?php if (!empty($booking['special_requests'])): ?>
                                    <hr>
                                    <p><strong>คำขอพิเศษ:</strong><br><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></p>
                                <?php endif; ?>
                                <small class="text-muted">จองเมื่อ: <?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <h4>คุณยังไม่มีการจอง</h4>
                <a href="rooms.php" class="btn btn-primary mt-3">เลือกห้องพัก</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <h5 class="mb-3">ช้องนาง เรสซิเดนซ์</h5>
            <p class="mb-1">📍 146 พิศิษฐ์พยาบาล ตำบลท่าตะเภา อำเภอเมืองชุมพร</p>
            <p class="mb-3">จังหวัดชุมพร 86000 ประเทศไทย</p>
            <p class="mb-2">📞 โทร: 077511218</p>
            <hr class="bg-light my-3">
            <p class="mb-0">© 2025 Hotel Booking System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>