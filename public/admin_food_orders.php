<?php
session_start();
require_once('../config/db.php');

// ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// อัพเดทสถานะ
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'preparing', 'ready', 'delivered', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $update_stmt = $conn->prepare("UPDATE food_orders SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_status, $order_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success'] = "อัพเดทสถานะออเดอร์ #" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . " สำเร็จ";
        } else {
            $_SESSION['error'] = "ไม่สามารถอัพเดทสถานะได้: " . $conn->error;
        }
        $update_stmt->close();
    } else {
        $_SESSION['error'] = "สถานะไม่ถูกต้อง";
    }
    
    header("Location: admin_food_orders.php" . (isset($_GET['status']) ? "?status=" . $_GET['status'] : ""));
    exit();
}

// ฟิลเตอร์สถานะ
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// ดึงออเดอร์ทั้งหมด
if ($status_filter === 'all') {
    $orders_query = "SELECT fo.*, u.username FROM food_orders fo 
                     LEFT JOIN users u ON fo.user_id = u.id 
                     ORDER BY fo.order_date DESC";
    $orders = $conn->query($orders_query);
} else {
    $stmt = $conn->prepare("SELECT fo.*, u.username FROM food_orders fo 
                           LEFT JOIN users u ON fo.user_id = u.id 
                           WHERE fo.status = ? 
                           ORDER BY fo.order_date DESC");
    $stmt->bind_param("s", $status_filter);
    $stmt->execute();
    $orders = $stmt->get_result();
}

// นับจำนวนออเดอร์แต่ละสถานะ
$stats = [
    'all' => $conn->query("SELECT COUNT(*) as count FROM food_orders")->fetch_assoc()['count'],
    'pending' => $conn->query("SELECT COUNT(*) as count FROM food_orders WHERE status='pending'")->fetch_assoc()['count'],
    'preparing' => $conn->query("SELECT COUNT(*) as count FROM food_orders WHERE status='preparing'")->fetch_assoc()['count'],
    'ready' => $conn->query("SELECT COUNT(*) as count FROM food_orders WHERE status='ready'")->fetch_assoc()['count'],
    'delivered' => $conn->query("SELECT COUNT(*) as count FROM food_orders WHERE status='delivered'")->fetch_assoc()['count']
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการออเดอร์อาหาร - Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .stats-card {
            border: none;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .stats-card.active {
            box-shadow: 0 10px 30px rgba(123, 44, 191, 0.3);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .order-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .order-row {
            border-bottom: 1px solid #eee;
            padding: 15px;
            transition: all 0.3s;
        }

        .order-row:hover {
            background: #f8f9fa;
        }

        .order-row:last-child {
            border-bottom: none;
        }

        .status-select {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 5px 10px;
            font-weight: bold;
        }

        .status-select.pending {
            border-color: #ffc107;
            color: #856404;
        }

        .status-select.preparing {
            border-color: #0dcaf0;
            color: #084298;
        }

        .status-select.ready {
            border-color: #198754;
            color: #0f5132;
        }

        .status-select.delivered {
            border-color: #198754;
            color: #0f5132;
        }

        .status-select.cancelled {
            border-color: #dc3545;
            color: #842029;
        }

        .order-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-new {
            background: #ff6b6b;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .quick-actions {
            display: flex;
            gap: 5px;
        }

        .action-btn {
            padding: 5px 15px;
            border-radius: 20px;
            border: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-preparing {
            background: #cfe2ff;
            color: #084298;
        }

        .btn-ready {
            background: #d1e7dd;
            color: #0f5132;
        }

        .btn-delivered {
            background: #d1e7dd;
            color: #0f5132;
        }

        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            border: none;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.4);
            font-size: 1.3rem;
            cursor: pointer;
            z-index: 1000;
        }

        .refresh-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="fas fa-user-shield"></i> Admin Panel
            </a>
            <div class="ml-auto">
                <a href="admin.php" class="btn btn-outline-light mr-2">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php" class="btn btn-outline-light mr-2">
                    <i class="fas fa-home"></i> หน้าแรก
                </a>
                <span class="text-white mr-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <h2><i class="fas fa-utensils"></i> จัดการออเดอร์อาหาร</h2>
            <p class="mb-0">อัพเดทสถานะและจัดการออเดอร์ทั้งหมด</p>
        </div>
    </div>

    <div class="container mb-5">
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

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <a href="?status=all" class="stats-card <?php echo $status_filter === 'all' ? 'active' : ''; ?>" 
                   style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="stats-number"><?php echo $stats['all']; ?></div>
                    <div class="stats-label">ทั้งหมด</div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="?status=pending" class="stats-card <?php echo $status_filter === 'pending' ? 'active' : ''; ?>" 
                   style="background: #fff3cd;">
                    <div class="stats-number" style="color: #856404;"><?php echo $stats['pending']; ?></div>
                    <div class="stats-label" style="color: #856404;">รอดำเนินการ</div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="?status=preparing" class="stats-card <?php echo $status_filter === 'preparing' ? 'active' : ''; ?>" 
                   style="background: #cfe2ff;">
                    <div class="stats-number" style="color: #084298;"><?php echo $stats['preparing']; ?></div>
                    <div class="stats-label" style="color: #084298;">กำลังเตรียม</div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="?status=ready" class="stats-card <?php echo $status_filter === 'ready' ? 'active' : ''; ?>" 
                   style="background: #d1e7dd;">
                    <div class="stats-number" style="color: #0f5132;"><?php echo $stats['ready']; ?></div>
                    <div class="stats-label" style="color: #0f5132;">พร้อมแล้ว</div>
                </a>
            </div>
            <div class="col-md-2">
                <a href="?status=delivered" class="stats-card <?php echo $status_filter === 'delivered' ? 'active' : ''; ?>" 
                   style="background: #d1e7dd;">
                    <div class="stats-number" style="color: #0f5132;"><?php echo $stats['delivered']; ?></div>
                    <div class="stats-label" style="color: #0f5132;">จัดส่งแล้ว</div>
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="order-table">
            <?php if ($orders->num_rows > 0): ?>
                <?php while ($order = $orders->fetch_assoc()): 
                    // ดึงรายการอาหาร
                    $items_stmt = $conn->prepare("SELECT * FROM food_order_items WHERE order_id = ?");
                    $items_stmt->bind_param("i", $order['id']);
                    $items_stmt->execute();
                    $items = $items_stmt->get_result();
                    
                    // เช็คว่าเป็นออเดอร์ใหม่ (ภายใน 5 นาที)
                    $is_new = (time() - strtotime($order['order_date'])) < 300;
                ?>
                    <div class="order-row">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                <?php if ($is_new && $order['status'] === 'pending'): ?>
                                    <span class="order-badge badge-new">ใหม่!</span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                                </small>
                                <br>
                                <small class="text-primary">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?>
                                </small>
                            </div>
                            
                            <div class="col-md-3">
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                <small>
                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['customer_phone']); ?>
                                </small><br>
                                <?php if ($order['room_number']): ?>
                                    <small><i class="fas fa-door-closed"></i> ห้อง: <?php echo htmlspecialchars($order['room_number']); ?></small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-3">
                                <small>
                                    <?php 
                                    $item_count = $items->num_rows;
                                    $items->data_seek(0); // Reset pointer
                                    $first_items = [];
                                    while ($item = $items->fetch_assoc()) {
                                        $first_items[] = $item['menu_name'] . ' ×' . $item['quantity'];
                                        if (count($first_items) >= 2) break;
                                    }
                                    echo implode(', ', $first_items);
                                    if ($item_count > 2) {
                                        echo '... (+' . ($item_count - 2) . ')';
                                    }
                                    ?>
                                </small>
                                <br>
                                <strong class="text-success">฿<?php echo number_format($order['total_price'], 0); ?></strong>
                            </div>
                            
                            <div class="col-md-2">
                                <form method="POST" class="d-inline" onsubmit="return confirm('ต้องการเปลี่ยนสถานะออเดอร์นี้หรือไม่?');">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="status-select <?php echo $order['status']; ?>" 
                                            onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status']==='pending'?'selected':''; ?>>⏳ รอดำเนินการ</option>
                                        <option value="preparing" <?php echo $order['status']==='preparing'?'selected':''; ?>>👨‍🍳 กำลังเตรียม</option>
                                        <option value="ready" <?php echo $order['status']==='ready'?'selected':''; ?>>✅ พร้อมแล้ว</option>
                                        <option value="delivered" <?php echo $order['status']==='delivered'?'selected':''; ?>>🚚 จัดส่งแล้ว</option>
                                        <option value="cancelled" <?php echo $order['status']==='cancelled'?'selected':''; ?>>❌ ยกเลิก</option>
                                    </select>
                                </form>
                            </div>
                            
                            <div class="col-md-2 text-right">
                                <button class="btn btn-sm btn-info" data-toggle="modal" 
                                        data-target="#orderModal<?php echo $order['id']; ?>">
                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal รายละเอียดออเดอร์ -->
                    <div class="modal fade" id="orderModal<?php echo $order['id']; ?>">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header" style="background: var(--primary-purple); color: white;">
                                    <h5 class="modal-title">
                                        <i class="fas fa-receipt"></i> ออเดอร์ #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <h6>รายการอาหาร:</h6>
                                    <?php 
                                    $items->data_seek(0);
                                    while ($item = $items->fetch_assoc()): 
                                    ?>
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['menu_name']); ?></strong>
                                                <?php if ($item['protein_type'] !== 'none'): ?>
                                                    <br><small class="text-muted">
                                                        <?php 
                                                        $proteins = ['pork'=>'หมู', 'chicken'=>'ไก่', 'seafood'=>'ทะเล'];
                                                        echo $proteins[$item['protein_type']];
                                                        ?>
                                                    </small>
                                                <?php endif; ?>
                                                <br><small>฿<?php echo number_format($item['unit_price'], 0); ?> × <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <div><strong>฿<?php echo number_format($item['subtotal'], 0); ?></strong></div>
                                        </div>
                                    <?php endwhile; ?>
                                    
                                    <div class="mt-3 pt-3 border-top">
                                        <h5>ยอดรวม: <span class="text-success">฿<?php echo number_format($order['total_price'], 0); ?></span></h5>
                                    </div>

                                    <?php if ($order['special_note']): ?>
                                        <div class="alert alert-warning mt-3">
                                            <strong><i class="fas fa-comment"></i> หมายเหตุ:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($order['special_note'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #ddd;"></i>
                    <h4 class="mt-3">ไม่มีออเดอร์</h4>
                    <p class="text-muted">ไม่พบออเดอร์ในสถานะนี้</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="refresh-btn" onclick="location.reload();" title="รีเฟรช">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 ช้องนาง เรสซิเดนซ์ - Admin Panel</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh every 30 seconds for pending orders
        <?php if ($status_filter === 'pending' || $status_filter === 'preparing'): ?>
        setTimeout(function() {
            location.reload();
        }, 30000);
        <?php endif; ?>

        // Play sound on new order (optional)
        <?php if ($status_filter === 'pending' && $stats['pending'] > 0): ?>
        // You can add audio notification here
        <?php endif; ?>
    </script>
</body>
</html>
