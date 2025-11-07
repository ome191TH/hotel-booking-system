<?php
session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าอาหาร - ช้องนาง เรสซิเดนซ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .cart-hero {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .cart-item {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.3s;
        }

        .cart-item:hover {
            box-shadow: 0 5px 15px rgba(123, 44, 191, 0.1);
        }

        .cart-item-name {
            font-weight: bold;
            color: var(--primary-purple);
            font-size: 1.2rem;
        }

        .cart-item-protein {
            color: #666;
            font-size: 0.9rem;
        }

        .cart-item-price {
            color: #e91e63;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: var(--primary-purple);
            color: white;
            border-radius: 50%;
            cursor: pointer;
        }

        .quantity-display {
            width: 50px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .remove-btn {
            color: #e91e63;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .remove-btn:hover {
            color: #c2185b;
        }

        .cart-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            position: sticky;
            top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .summary-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-purple);
            border-top: 2px solid var(--primary-purple);
            padding-top: 15px;
            margin-top: 15px;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 20px;
        }

        .checkout-btn:hover {
            transform: scale(1.02);
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart i {
            font-size: 5rem;
            color: #ccc;
            margin-bottom: 20px;
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_food_orders.php"><i class="fas fa-receipt"></i> ออเดอร์อาหาร</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="cart-hero">
        <div class="container">
            <h1><i class="fas fa-shopping-cart"></i> ตะกร้าอาหาร</h1>
            <p>ตรวจสอบรายการสั่งอาหารของคุณ</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <div id="cartItems">
                    <!-- Cart items will be loaded here by JavaScript -->
                </div>
                <div id="emptyCart" class="empty-cart" style="display:none;">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>ตะกร้าว่างเปล่า</h3>
                    <p>ยังไม่มีรายการอาหารในตะกร้า</p>
                    <a href="food_menu.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-utensils"></i> ดูเมนูอาหาร
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary" id="cartSummary" style="display:none;">
                    <h4 class="mb-4"><i class="fas fa-file-invoice"></i> สรุปรายการ</h4>
                    <div class="summary-row">
                        <span>จำนวนรายการ:</span>
                        <span id="totalItems">0</span>
                    </div>
                    <div class="summary-row">
                        <span>ยอดรวม:</span>
                        <span id="subtotal">฿0</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>ยอดรวมทั้งหมด:</span>
                        <span id="grandTotal">฿0</span>
                    </div>
                    <button class="checkout-btn" onclick="proceedToCheckout()">
                        <i class="fas fa-check-circle"></i> ดำเนินการสั่งอาหาร
                    </button>
                    <a href="food_menu.php" class="btn btn-outline-primary btn-block mt-2">
                        <i class="fas fa-plus"></i> เพิ่มเมนูอีก
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
        $(document).ready(function() {
            loadCart();
        });

        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
            
            if (cart.length === 0) {
                $('#emptyCart').show();
                $('#cartSummary').hide();
                return;
            }
            
            $('#emptyCart').hide();
            $('#cartSummary').show();
            
            let html = '';
            let totalItems = 0;
            let grandTotal = 0;
            
            cart.forEach((item, index) => {
                totalItems += item.quantity;
                grandTotal += item.subtotal;
                
                html += `
                    <div class="cart-item">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="cart-item-name">${item.menuName}</div>
                                ${item.proteinLabel ? `<div class="cart-item-protein"><i class="fas fa-drumstick-bite"></i> ${item.proteinLabel}</div>` : ''}
                                <div class="cart-item-price">฿${item.unitPrice.toFixed(0)} × ${item.quantity} = ฿${item.subtotal.toFixed(0)}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <button class="quantity-btn" onclick="updateQuantity(${index}, -1)">-</button>
                                    <span class="quantity-display">${item.quantity}</span>
                                    <button class="quantity-btn" onclick="updateQuantity(${index}, 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <i class="fas fa-trash remove-btn" onclick="removeItem(${index})" title="ลบ"></i>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('#cartItems').html(html);
            $('#totalItems').text(totalItems + ' รายการ');
            $('#subtotal').text('฿' + grandTotal.toFixed(0));
            $('#grandTotal').text('฿' + grandTotal.toFixed(0));
        }

        function updateQuantity(index, change) {
            let cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            } else {
                cart[index].subtotal = cart[index].unitPrice * cart[index].quantity;
            }
            
            localStorage.setItem('foodCart', JSON.stringify(cart));
            loadCart();
        }

        function removeItem(index) {
            if (confirm('ต้องการลบรายการนี้ออกจากตะกร้า?')) {
                let cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
                cart.splice(index, 1);
                localStorage.setItem('foodCart', JSON.stringify(cart));
                loadCart();
            }
        }

        function proceedToCheckout() {
            const cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
            if (cart.length === 0) {
                alert('ตะกร้าว่างเปล่า กรุณาเลือกเมนูอาหาร');
                return;
            }
            window.location.href = 'food_checkout.php';
        }
    </script>
</body>
</html>
