<?php
session_start();
include '../config/db.php';

// Get room ID from URL
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $room_id = intval($_GET['id']);
    
    // Fetch room details from the database
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
    } else {
        // ไม่พบห้องพัก - redirect กลับหน้าแรก
        header("Location: index.php");
        exit;
    }
} else {
    // ไม่มี ID หรือ ID ไม่ถูกต้อง - redirect กลับหน้าแรก
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['name']); ?> - Hotel Booking</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php"><i class="fas fa-bed"></i> จองห้องพัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attractions.php"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยว</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="food_menu.php"><i class="fas fa-utensils"></i> สั่งอาหาร</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_bookings.php"><i class="fas fa-list"></i> การจองของฉัน</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_food_orders.php"><i class="fas fa-receipt"></i> ออเดอร์อาหาร</a>
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
                            <a class="btn btn-outline-light btn-sm ml-2" href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-success btn-sm ml-2" href="register.php"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">หน้าแรก</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($room['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-6">
                <img src="../assets/images/<?php echo htmlspecialchars($room['image']); ?>" 
                     alt="<?php echo htmlspecialchars($room['name']); ?>" 
                     class="img-fluid rounded shadow"
                     onerror="this.src='https://via.placeholder.com/500x300?text=<?php echo urlencode($room['name']); ?>'">
            </div>
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($room['name']); ?></h1>
                
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="text-primary">฿<?php echo number_format($room['price'], 2); ?> <small class="text-muted">/ คืน</small></h4>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>📋 รายละเอียดห้องพัก</strong>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($room['details'])); ?></p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <p class="mb-1"><strong>🏨 ห้องว่าง:</strong> 
                            <span class="badge badge-<?php echo $room['available_rooms'] > 0 ? 'success' : 'danger'; ?> badge-pill">
                                <?php echo $room['available_rooms']; ?> ห้อง
                            </span>
                        </p>
                    </div>
                </div>

                <?php if ($room['available_rooms'] > 0): ?>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="booking_form.php?id=<?php echo $room['id']; ?>" class="btn btn-primary btn-lg btn-block">
                            📅 จองห้องนี้เลย
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-lg btn-block">
                            เข้าสู่ระบบเพื่อจอง
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg btn-block" disabled>
                        ❌ ห้องเต็ม
                    </button>
                <?php endif; ?>
                
                <a href="index.php" class="btn btn-outline-secondary btn-block mt-2">
                    ← กลับหน้าแรก
                </a>
            </div>
        </div>
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

    <script src="../assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>