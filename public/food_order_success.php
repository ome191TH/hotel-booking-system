<?php
session_start();
require_once('../config/db.php');

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามี order_id
if (!isset($_GET['order_id'])) {
    header("Location: food_menu.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลออเดอร์
$stmt = $conn->prepare("SELECT * FROM food_orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: my_food_orders.php");
    exit();
}

// ดึงรายการอาหาร
$items_stmt = $conn->prepare("SELECT * FROM food_order_items WHERE order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งอาหารสำเร็จ - ช้องนาง เรสซิเดนซ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-hero {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .success-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .order-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            background: #ffc107;
            color: #000;
        }

        .order-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .total-price {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-purple);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 30px;
        }

        .action-btn {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
        }

        .contact-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .timeline {
            margin-top: 30px;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-purple);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
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
                        <a class="nav-link" href="food_menu.php"><i class="fas fa-utensils"></i> สั่งอาหาร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_food_orders.php"><i class="fas fa-receipt"></i> ออเดอร์อาหาร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Success Hero -->
    <div class="success-hero">
        <div class="container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>สั่งอาหารสำเร็จ!</h1>
            <p class="lead">ขอบคุณที่ใช้บริการ อาหารจะถูกจัดส่งในเร็วๆ นี้</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="order-card">
                    <div class="order-header text-center">
                        <div class="order-number">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                        <div class="mt-2">
                            <span class="status-badge">
                                <i class="fas fa-clock"></i> รอดำเนินการ
                            </span>
                        </div>
                        <small class="d-block mt-2">
                            <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?> น.
                        </small>
                    </div>

                    <!-- ข้อมูลการจัดส่ง -->
                    <div class="mb-4">
                        <h5><i class="fas fa-user"></i> ข้อมูลผู้สั่ง</h5>
                        <p class="mb-1"><strong>ชื่อ:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p class="mb-1"><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        <?php if ($order['room_number']): ?>
                            <p class="mb-1"><strong>ห้องพัก:</strong> <?php echo htmlspecialchars($order['room_number']); ?></p>
                        <?php endif; ?>
                        <?php if ($order['delivery_address']): ?>
                            <p class="mb-1"><strong>ที่อยู่:</strong> <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                        <?php endif; ?>
                        <?php if ($order['special_note']): ?>
                            <p class="mb-0"><strong>หมายเหตุ:</strong> <?php echo nl2br(htmlspecialchars($order['special_note'])); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- รายการอาหาร -->
                    <div class="mb-4">
                        <h5><i class="fas fa-receipt"></i> รายการอาหาร</h5>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <div class="order-item">
                                <div class="d-flex justify-content-between align-items-start">
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
                                    <div class="text-right">
                                        <strong>฿<?php echo number_format($item['subtotal'], 0); ?></strong>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- ยอดรวม -->
                    <div class="total-section text-center">
                        <h5>ยอดรวมทั้งหมด</h5>
                        <div class="total-price">฿<?php echo number_format($order['total_price'], 0); ?></div>
                        <small class="text-muted">ชำระเงินปลายทาง</small>
                    </div>

                    <!-- Timeline -->
                    <div class="timeline">
                        <h6 class="mb-3"><i class="fas fa-tasks"></i> สถานะการดำเนินการ</h6>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="fas fa-check"></i></div>
                            <div>รับออเดอร์แล้ว</div>
                        </div>
                        <div class="timeline-item text-muted">
                            <div class="timeline-icon" style="background: #ccc;"><i class="fas fa-utensils"></i></div>
                            <div>กำลังเตรียมอาหาร</div>
                        </div>
                        <div class="timeline-item text-muted">
                            <div class="timeline-icon" style="background: #ccc;"><i class="fas fa-motorcycle"></i></div>
                            <div>จัดส่งแล้ว</div>
                        </div>
                    </div>

                    <!-- ข้อมูลติดต่อ -->
                    <div class="contact-info">
                        <i class="fas fa-phone-alt"></i> <strong>ติดต่อสอบถาม:</strong> 064-992-3586 (พี่กวาง)<br>
                        <small>ระยะเวลาจัดส่ง: ประมาณ 20-30 นาที</small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="food_menu.php" class="btn btn-primary action-btn">
                        <i class="fas fa-utensils"></i> สั่งอาหารเพิ่ม
                    </a>
                    <a href="my_food_orders.php" class="btn btn-outline-primary action-btn">
                        <i class="fas fa-list"></i> ดูออเดอร์ทั้งหมด
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary action-btn">
                        <i class="fas fa-home"></i> กลับหน้าแรก
                    </a>
                </div>
            </div>
        </div>
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
        // Clear cart from localStorage after successful order
        localStorage.removeItem('foodCart');
    </script>
</body>
</html>
