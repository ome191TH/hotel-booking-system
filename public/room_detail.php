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
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🏨 Hotel Booking</a>
            <div class="ml-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-white mr-3">สวัสดี, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="my_bookings.php" class="btn btn-outline-light btn-sm mr-2">การจองของฉัน</a>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a href="admin.php" class="btn btn-outline-warning btn-sm mr-2">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm mr-2">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-outline-success btn-sm">สมัครสมาชิก</a>
                <?php endif; ?>
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

    <script src="../assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>