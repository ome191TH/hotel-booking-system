<?php
session_start();
require_once('../config/db.php');

// ตรวจสอบว่ามีสินค้าในตะกร้าหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้ login ให้ redirect ไป login พร้อมกลับมาหน้านี้
    $_SESSION['redirect_after_login'] = 'food_checkout.php';
    header("Location: login.php?message=กรุณาเข้าสู่ระบบก่อนสั่งอาหาร");
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// ดึงการจองล่าสุดของผู้ใช้ (ถ้ามี)
$booking_stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND checkin_date <= CURDATE() AND checkout_date >= CURDATE() ORDER BY created_at DESC LIMIT 1");
$booking_stmt->bind_param("i", $user_id);
$booking_stmt->execute();
$current_booking = $booking_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการสั่งอาหาร - ช้องนาง เรสซิเดนซ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .checkout-hero {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .checkout-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.1);
        }

        .section-title {
            color: var(--primary-purple);
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-purple);
        }

        .order-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .order-summary-item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: bold;
        }

        .item-protein {
            color: #666;
            font-size: 0.9rem;
        }

        .total-row {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-purple);
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--primary-purple);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            border: none;
            padding: 15px 50px;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: bold;
            width: 100%;
        }

        .submit-btn:hover {
            transform: scale(1.02);
        }

        .delivery-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .delivery-option:hover {
            border-color: var(--primary-purple);
        }

        .delivery-option.active {
            border-color: var(--primary-purple);
            background: #f8f4fc;
        }

        .delivery-option input[type="radio"] {
            margin-right: 10px;
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

    <!-- Hero Section -->
    <div class="checkout-hero">
        <div class="container">
            <h1><i class="fas fa-check-circle"></i> ยืนยันการสั่งอาหาร</h1>
            <p>กรุณาตรวจสอบรายการและกรอกข้อมูลสำหรับจัดส่ง</p>
        </div>
    </div>

    <div class="container mb-5">
        <form id="checkoutForm" action="food_order_process.php" method="POST">
            <div class="row">
                <!-- ส่วนข้อมูลการจัดส่ง -->
                <div class="col-lg-7">
                    <div class="checkout-section">
                        <h4 class="section-title"><i class="fas fa-map-marker-alt"></i> ข้อมูลการจัดส่ง</h4>
                        
                        <!-- ตัวเลือกการจัดส่ง -->
                        <div class="form-group">
                            <label class="delivery-option active" id="roomOption">
                                <input type="radio" name="delivery_type" value="room" checked>
                                <i class="fas fa-door-open"></i> ส่งที่ห้องพัก
                            </label>
                            
                            <label class="delivery-option" id="addressOption">
                                <input type="radio" name="delivery_type" value="address">
                                <i class="fas fa-home"></i> ส่งที่อยู่อื่น
                            </label>
                        </div>

                        <!-- ฟอร์มข้อมูลลูกค้า -->
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> ชื่อผู้รับ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="customer_phone" 
                                   placeholder="เช่น 0812345678" required pattern="[0-9]{10}">
                        </div>

                        <!-- ส่วนเลขห้องพัก -->
                        <div id="roomSection">
                            <div class="form-group">
                                <label><i class="fas fa-door-closed"></i> เลขห้องพัก <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="room_number" 
                                       value="<?php echo $current_booking ? htmlspecialchars($current_booking['id']) : ''; ?>"
                                       placeholder="เช่น 101, 202">
                                <?php if ($current_booking): ?>
                                    <small class="form-text text-success">
                                        <i class="fas fa-info-circle"></i> พบการจองห้องของคุณ: 
                                        <?php echo date('d/m/Y', strtotime($current_booking['checkin_date'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($current_booking['checkout_date'])); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ส่วนที่อยู่จัดส่ง -->
                        <div id="addressSection" style="display:none;">
                            <div class="form-group">
                                <label><i class="fas fa-map-marked-alt"></i> ที่อยู่จัดส่ง <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="delivery_address" rows="3" 
                                          placeholder="บ้านเลขที่ ซอย ถนน ตำบล อำเภอ จังหวัด"></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> ส่งได้เฉพาะในพื้นที่ใกล้เคียงโรงแรม
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> หมายเหตุพิเศษ</label>
                            <textarea class="form-control" name="special_note" rows="2" 
                                      placeholder="เช่น ไม่ใส่ผักชี, เพิ่มน้ำจิ้ม, ฯลฯ"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ส่วนสรุปรายการ -->
                <div class="col-lg-5">
                    <div class="checkout-section">
                        <h4 class="section-title"><i class="fas fa-receipt"></i> สรุปรายการสั่งอาหาร</h4>
                        <div id="orderSummary">
                            <!-- Order items will be loaded here -->
                        </div>
                        <div class="order-summary-item total-row">
                            <span>ยอดรวมทั้งหมด:</span>
                            <span id="grandTotal">฿0</span>
                        </div>

                        <button type="submit" class="submit-btn mt-4">
                            <i class="fas fa-check-circle"></i> ยืนยันการสั่งอาหาร
                        </button>

                        <div class="text-center mt-3">
                            <a href="food_cart.php" class="text-muted">
                                <i class="fas fa-arrow-left"></i> กลับไปแก้ไขตะกร้า
                            </a>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> 
                            <small>การชำระเงิน: เก็บเงินปลายทาง<br>
                            ระยะเวลาจัดส่ง: ประมาณ 20-30 นาที</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden field for cart data -->
            <input type="hidden" name="cart_data" id="cartData">
        </form>
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
            // Load order summary
            loadOrderSummary();

            // Toggle delivery sections
            $('input[name="delivery_type"]').change(function() {
                $('.delivery-option').removeClass('active');
                $(this).parent().addClass('active');
                
                if ($(this).val() === 'room') {
                    $('#roomSection').show();
                    $('#addressSection').hide();
                    $('input[name="room_number"]').prop('required', true);
                    $('textarea[name="delivery_address"]').prop('required', false);
                } else {
                    $('#roomSection').hide();
                    $('#addressSection').show();
                    $('input[name="room_number"]').prop('required', false);
                    $('textarea[name="delivery_address"]').prop('required', true);
                }
            });

            // Form submission
            $('#checkoutForm').submit(function(e) {
                const cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
                if (cart.length === 0) {
                    e.preventDefault();
                    alert('ตะกร้าว่างเปล่า กรุณาเลือกเมนูอาหาร');
                    window.location.href = 'food_menu.php';
                    return false;
                }
                
                // Set cart data to hidden field
                $('#cartData').val(JSON.stringify(cart));
            });
        });

        function loadOrderSummary() {
            const cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
            
            if (cart.length === 0) {
                alert('ตะกร้าว่างเปล่า กรุณาเลือกเมนูอาหาร');
                window.location.href = 'food_menu.php';
                return;
            }
            
            let html = '';
            let grandTotal = 0;
            
            cart.forEach(item => {
                grandTotal += item.subtotal;
                html += `
                    <div class="order-summary-item">
                        <div>
                            <div class="item-name">${item.menuName} × ${item.quantity}</div>
                            ${item.proteinLabel ? `<div class="item-protein">${item.proteinLabel}</div>` : ''}
                        </div>
                        <div>฿${item.subtotal.toFixed(0)}</div>
                    </div>
                `;
            });
            
            $('#orderSummary').html(html);
            $('#grandTotal').text('฿' + grandTotal.toFixed(0));
        }
    </script>
</body>
</html>
