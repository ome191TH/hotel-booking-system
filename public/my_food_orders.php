<?php
session_start();
require_once('../config/db.php');

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ยืนยันรับอาหาร (เปลี่ยนสถานะจาก ready เป็น delivered)
if (isset($_POST['confirm_received']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    
    // ตรวจสอบว่าเป็นออเดอร์ของผู้ใช้คนนี้และสถานะเป็น ready
    $check_stmt = $conn->prepare("SELECT id FROM food_orders WHERE id = ? AND user_id = ? AND status = 'ready'");
    $check_stmt->bind_param("ii", $order_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // อัพเดทสถานะเป็น delivered
        $update_stmt = $conn->prepare("UPDATE food_orders SET status = 'delivered' WHERE id = ?");
        $update_stmt->bind_param("i", $order_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success'] = "ยืนยันรับอาหารเรียบร้อยแล้ว ขอบคุณที่ใช้บริการค่ะ! 😊";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการยืนยัน กรุณาลองใหม่อีกครั้ง";
        }
        $update_stmt->close();
    } else {
        $_SESSION['error'] = "ไม่สามารถยืนยันออเดอร์นี้ได้";
    }
    $check_stmt->close();
    
    header("Location: my_food_orders.php");
    exit();
}

// ดึงออเดอร์ทั้งหมดของผู้ใช้
$stmt = $conn->prepare("SELECT * FROM food_orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งอาหาร - ช้องนาง เรสซิเดนซ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .orders-hero {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .order-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .order-card:hover {
            box-shadow: 0 10px 30px rgba(123, 44, 191, 0.2);
            transform: translateY(-3px);
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 20px;
            cursor: pointer;
        }

        .order-number {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .order-date {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .order-body {
            padding: 20px;
            background: white;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-preparing {
            background: #cfe2ff;
            color: #084298;
        }

        .status-ready {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-delivered {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .order-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-purple);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .view-details-btn {
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s;
            display: inline-block;
            margin-top: 10px;
        }

        .view-details-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .order-info {
            font-size: 0.9rem;
            color: #666;
        }

        .collapse-arrow {
            transition: transform 0.3s;
        }

        .collapsed .collapse-arrow {
            transform: rotate(180deg);
        }

        /* ปุ่มยืนยันรับอาหาร */
        .confirm-received-alert {
            background: linear-gradient(135deg, #d1f2eb 0%, #d4edda 100%);
            border: 2px solid #28a745;
            border-radius: 15px;
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { border-color: #28a745; }
            50% { border-color: #20c997; }
        }

        .btn-confirm-received {
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s;
        }

        .btn-confirm-received:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hotel"></i> ช้องนาง เรสซิเดนซ์
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking_form.php"><i class="fas fa-calendar-check"></i> จองห้องพัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="food_menu.php"><i class="fas fa-utensils"></i> สั่งอาหาร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attractions.php"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยว</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_bookings.php"><i class="fas fa-list"></i> การจองของฉัน</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="my_food_orders.php"><i class="fas fa-receipt"></i> ออเดอร์อาหาร</a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php"><i class="fas fa-user-shield"></i> Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="orders-hero">
        <div class="container">
            <h1><i class="fas fa-receipt"></i> ประวัติการสั่งอาหาร</h1>
            <p>รายการออเดอร์อาหารทั้งหมดของคุณ</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- แจ้งเตือน -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($orders->num_rows > 0): ?>
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <a href="food_menu.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> สั่งอาหารเพิ่ม
                        </a>
                    </div>

                    <?php while ($order = $orders->fetch_assoc()): 
                        // ดึงรายการอาหาร
                        $items_stmt = $conn->prepare("SELECT * FROM food_order_items WHERE order_id = ?");
                        $items_stmt->bind_param("i", $order['id']);
                        $items_stmt->execute();
                        $items = $items_stmt->get_result();
                        
                        // กำหนดสถานะ
                        $status_class = 'status-' . $order['status'];
                        $status_text = [
                            'pending' => 'รอดำเนินการ',
                            'preparing' => 'กำลังเตรียม',
                            'ready' => 'พร้อมแล้ว',
                            'delivered' => 'จัดส่งแล้ว',
                            'cancelled' => 'ยกเลิก'
                        ];
                        $status_icon = [
                            'pending' => 'clock',
                            'preparing' => 'utensils',
                            'ready' => 'check-circle',
                            'delivered' => 'check-double',
                            'cancelled' => 'times-circle'
                        ];
                    ?>
                        <div class="order-card">
                            <div class="order-header" data-toggle="collapse" data-target="#order-<?php echo $order['id']; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="order-number">
                                            #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                        </div>
                                        <div class="order-date">
                                            <i class="fas fa-calendar"></i> 
                                            <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?> น.
                                        </div>
                                        <div class="mt-2">
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <i class="fas fa-<?php echo $status_icon[$order['status']]; ?>"></i>
                                                <?php echo $status_text[$order['status']]; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div style="font-size: 1.5rem; font-weight: bold;">
                                            ฿<?php echo number_format($order['total_price'], 0); ?>
                                        </div>
                                        <small>
                                            <i class="fas fa-chevron-down collapse-arrow"></i> ดูรายละเอียด
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="order-<?php echo $order['id']; ?>" class="collapse">
                                <div class="order-body">
                                    <!-- ข้อมูลการจัดส่ง -->
                                    <div class="mb-3">
                                        <h6><i class="fas fa-info-circle"></i> ข้อมูลการจัดส่ง</h6>
                                        <div class="order-info">
                                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                            <?php if ($order['room_number']): ?>
                                                <i class="fas fa-door-closed"></i> ห้อง: <?php echo htmlspecialchars($order['room_number']); ?><br>
                                            <?php endif; ?>
                                            <?php if ($order['delivery_address']): ?>
                                                <i class="fas fa-map-marker-alt"></i> <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?><br>
                                            <?php endif; ?>
                                            <?php if ($order['special_note']): ?>
                                                <i class="fas fa-comment"></i> <?php echo nl2br(htmlspecialchars($order['special_note'])); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- รายการอาหาร -->
                                    <h6><i class="fas fa-list"></i> รายการอาหาร</h6>
                                    <?php while ($item = $items->fetch_assoc()): ?>
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['menu_name']); ?></strong>
                                                    <?php if ($item['protein_type'] !== 'none'): ?>
                                                        <br><small class="text-muted">
                                                            <i class="fas fa-drumstick-bite"></i>
                                                            <?php 
                                                                $protein_names = [
                                                                    'pork' => 'หมู',
                                                                    'chicken' => 'ไก่',
                                                                    'seafood' => 'ทะเล'
                                                                ];
                                                                echo $protein_names[$item['protein_type']] ?? '';
                                                            ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <br><small>฿<?php echo number_format($item['unit_price'], 0); ?> × <?php echo $item['quantity']; ?></small>
                                                </div>
                                                <div>
                                                    <strong>฿<?php echo number_format($item['subtotal'], 0); ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>

                                    <!-- ยอดรวม -->
                                    <div class="order-total">
                                        <div class="d-flex justify-content-between">
                                            <span>ยอดรวมทั้งหมด:</span>
                                            <span>฿<?php echo number_format($order['total_price'], 0); ?></span>
                                        </div>
                                    </div>

                                    <!-- ปุ่มยืนยันรับอาหาร (แสดงเฉพาะสถานะ ready) -->
                                    <?php if ($order['status'] === 'ready'): ?>
                                        <div class="alert confirm-received-alert mt-3" role="alert">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div class="mb-2 mb-md-0">
                                                    <h5 class="mb-1">
                                                        <i class="fas fa-bell" style="color: #28a745;"></i> 
                                                        <strong>อาหารพร้อมแล้ว!</strong>
                                                    </h5>
                                                    <p class="mb-0">กรุณายืนยันเมื่อคุณได้รับอาหารเรียบร้อยแล้ว</p>
                                                </div>
                                                <form method="POST" class="mb-0" onsubmit="return confirm('✅ ยืนยันว่าคุณได้รับอาหารเรียบร้อยแล้วใช่หรือไม่?');">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <button type="submit" name="confirm_received" class="btn btn-success btn-confirm-received">
                                                        <i class="fas fa-check-double"></i> ยืนยันรับอาหารแล้ว
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- ติดต่อ -->
                                    <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                                        <div class="alert alert-info mt-3 mb-0">
                                            <i class="fas fa-phone-alt"></i> 
                                            <small>ติดต่อสอบถาม: 064-992-3586 (พี่กวาง)</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h3>ยังไม่มีประวัติการสั่งอาหาร</h3>
                <p class="text-muted">คุณยังไม่เคยสั่งอาหารกับเรา</p>
                <a href="food_menu.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-utensils"></i> สั่งอาหารเลย
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-hotel"></i> ช้องนาง เรสซิเดนซ์</h5>
                    <p>โรงแรมสะดวกสบาย ใจกลางเมืองชุมพร</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-map-marker-alt"></i> ที่อยู่</h5>
                    <p>146 ถนนพิศิษฐ์พยาบาล<br>ตำบลท่าตะเภา อำเภอเมือง<br>จังหวัดชุมพร 86000</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-phone"></i> ติดต่อเรา</h5>
                    <p>โทร: 077-123-456<br>สั่งอาหาร: 064-992-3586 (พี่กวาง)</p>
                </div>
            </div>
            <hr>
            <p class="text-center mb-0">&copy; 2025 ช้องนาง เรสซิเดนซ์</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle collapse arrow
            $('.order-header').on('click', function() {
                $(this).find('.collapse-arrow').toggleClass('collapsed');
            });
        });
    </script>
</body>
</html>
