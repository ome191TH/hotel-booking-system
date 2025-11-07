<?php
session_start();
require_once '../config/db.php';

// Query ห้องที่ยังมีห้องว่าง (เดี่ยวหรือแฝด)
$query = "SELECT * FROM rooms WHERE single_bed_rooms > 0 OR twin_bed_rooms > 0";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพัก - Hotel Booking System</title>
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
                    <li class="nav-item active">
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

    <!-- Page Header -->
    <div class="hero" style="padding: 60px 0;">
        <div class="container text-center">
            <h1>🏨 ห้องพักของเรา</h1>
            <p class="lead">เลือกห้องพักที่ใช่สำหรับคุณ</p>
        </div>
    </div>

    <!-- Rooms -->
    <div class="container my-5">
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($room = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="../assets/images/<?php echo htmlspecialchars($room['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($room['name']); ?>"
                                 onerror="this.src='https://via.placeholder.com/400x250?text=<?php echo urlencode($room['name']); ?>'">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($room['details']); ?></p>
                                <div class="mt-auto">
                                    <p class="h4 text-primary mb-2">฿<?php echo number_format($room['price'], 2); ?> <small class="text-muted">/ คืน</small></p>
                                    <p class="mb-3">
                                        <?php if ($room['single_bed_rooms'] > 0): ?>
                                            <span class="badge badge-info">🛏️ เดี่ยว: <?php echo $room['single_bed_rooms']; ?> ห้อง</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">🛏️ เดี่ยว: เต็ม</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($room['twin_bed_rooms'] > 0): ?>
                                            <span class="badge badge-info">🛏️🛏️ แฝด: <?php echo $room['twin_bed_rooms']; ?> ห้อง</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">🛏️🛏️ แฝด: เต็ม</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if(isset($_SESSION['user_id'])): ?>
                                        <a href="booking_form.php?id=<?php echo $room['id']; ?>" class="btn btn-primary btn-block">
                                            📝 จองห้องนี้
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-primary btn-block">
                                            เข้าสู่ระบบเพื่อจอง
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <h4>ขณะนี้ไม่มีห้องว่าง</h4>
                        <p>กรุณาติดต่อเรา หรือลองใหม่ในภายหลัง</p>
                    </div>
                </div>
            <?php endif; ?>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
