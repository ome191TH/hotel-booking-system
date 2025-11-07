<?php
session_start();
require_once('../config/db.php');

// ดึงเมนูอาหารแยกตามหมวดหมู่
$rice_menu = $conn->query("SELECT * FROM food_menu WHERE category = 'rice' AND is_available = 1 ORDER BY name");
$noodle_menu = $conn->query("SELECT * FROM food_menu WHERE category = 'noodle' AND is_available = 1 ORDER BY name");
$special_menu = $conn->query("SELECT * FROM food_menu WHERE category = 'special' AND is_available = 1 ORDER BY name");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งอาหาร - ช้องนาง เรสซิเดนซ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .food-hero {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .food-hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .menu-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(123, 44, 191, 0.2);
        }

        .menu-card-body {
            padding: 20px;
        }

        .menu-name {
            color: var(--primary-purple);
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .menu-price {
            color: #e91e63;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .protein-options {
            margin-bottom: 15px;
        }

        .protein-btn {
            padding: 5px 15px;
            margin: 3px;
            border: 2px solid var(--primary-purple);
            background: white;
            color: var(--primary-purple);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .protein-btn.active,
        .protein-btn:hover {
            background: var(--primary-purple);
            color: white;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            border: none;
            background: var(--primary-purple);
            color: white;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .quantity-btn:hover {
            background: var(--secondary-purple);
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 2px solid var(--primary-purple);
            border-radius: 10px;
            margin: 0 10px;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .add-to-cart-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(123, 44, 191, 0.3);
        }

        .cart-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .cart-button {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            border: none;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.4);
            font-size: 1.5rem;
            position: relative;
            transition: all 0.3s;
        }

        .cart-button:hover {
            transform: scale(1.1);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e91e63;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .category-section {
            margin-bottom: 50px;
        }

        .category-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: inline-block;
        }

        .category-header i {
            margin-right: 10px;
        }

        .protein-note {
            font-size: 0.85rem;
            color: #666;
            font-style: italic;
            margin-bottom: 10px;
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
                    <li class="nav-item active">
                        <a class="nav-link" href="food_menu.php"><i class="fas fa-utensils"></i> สั่งอาหาร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attractions.php"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยว</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_bookings.php"><i class="fas fa-list"></i> การจองของฉัน</a>
                        </li>
                        <li class="nav-item">
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
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="food-hero">
        <div class="container">
            <h1><i class="fas fa-utensils"></i> สั่งอาหารออนไลน์</h1>
            <p>อาหารอร่อย ส่งตรงถึงห้อง</p>
            <p class="mb-0"><i class="fas fa-phone"></i> ติดต่อ: 064-992-3586 (พี่กวาง)</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- เมนูข้าว -->
        <div class="category-section">
            <div class="category-header">
                <h3 class="mb-0"><i class="fas fa-bowl-rice"></i> เมนูข้าว</h3>
            </div>
            <p class="protein-note">* เลือกโปรตีน: หมู / ไก่ / ทะเล (+5 บาท)</p>
            <div class="row">
                <?php while ($menu = $rice_menu->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card menu-card">
                            <div class="menu-card-body">
                                <h5 class="menu-name"><?php echo htmlspecialchars($menu['name']); ?></h5>
                                <div class="menu-price">฿<?php echo number_format($menu['base_price'], 0); ?></div>
                                <?php if ($menu['description']): ?>
                                    <p class="text-muted small"><?php echo htmlspecialchars($menu['description']); ?></p>
                                <?php endif; ?>
                                
                                <?php if ($menu['has_protein_option']): ?>
                                    <div class="protein-options">
                                        <button class="protein-btn active" data-protein="pork" data-price="0">หมู</button>
                                        <button class="protein-btn" data-protein="chicken" data-price="0">ไก่</button>
                                        <button class="protein-btn" data-protein="seafood" data-price="<?php echo $menu['protein_extra_price']; ?>">ทะเล +฿<?php echo $menu['protein_extra_price']; ?></button>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="quantity-control">
                                    <button class="quantity-btn minus">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="99">
                                    <button class="quantity-btn plus">+</button>
                                </div>
                                
                                <button class="add-to-cart-btn" 
                                        data-id="<?php echo $menu['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                        data-base-price="<?php echo $menu['base_price']; ?>"
                                        data-has-protein="<?php echo $menu['has_protein_option']; ?>">
                                    <i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- เมนูเส้น -->
        <div class="category-section">
            <div class="category-header">
                <h3 class="mb-0"><i class="fas fa-bowl-food"></i> เมนูเส้น</h3>
            </div>
            <p class="protein-note">* เลือกโปรตีน: หมู / ไก่ / ทะเล (+5 บาท)</p>
            <div class="row">
                <?php while ($menu = $noodle_menu->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card menu-card">
                            <div class="menu-card-body">
                                <h5 class="menu-name"><?php echo htmlspecialchars($menu['name']); ?></h5>
                                <div class="menu-price">฿<?php echo number_format($menu['base_price'], 0); ?></div>
                                <?php if ($menu['description']): ?>
                                    <p class="text-muted small"><?php echo htmlspecialchars($menu['description']); ?></p>
                                <?php endif; ?>
                                
                                <?php if ($menu['has_protein_option']): ?>
                                    <div class="protein-options">
                                        <button class="protein-btn active" data-protein="pork" data-price="0">หมู</button>
                                        <button class="protein-btn" data-protein="chicken" data-price="0">ไก่</button>
                                        <button class="protein-btn" data-protein="seafood" data-price="<?php echo $menu['protein_extra_price']; ?>">ทะเล +฿<?php echo $menu['protein_extra_price']; ?></button>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="quantity-control">
                                    <button class="quantity-btn minus">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="99">
                                    <button class="quantity-btn plus">+</button>
                                </div>
                                
                                <button class="add-to-cart-btn" 
                                        data-id="<?php echo $menu['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                        data-base-price="<?php echo $menu['base_price']; ?>"
                                        data-has-protein="<?php echo $menu['has_protein_option']; ?>">
                                    <i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- เมนูพิเศษ -->
        <div class="category-section">
            <div class="category-header">
                <h3 class="mb-0"><i class="fas fa-star"></i> เมนูพิเศษ</h3>
            </div>
            <div class="row">
                <?php while ($menu = $special_menu->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card menu-card">
                            <div class="menu-card-body">
                                <h5 class="menu-name"><?php echo htmlspecialchars($menu['name']); ?></h5>
                                <div class="menu-price">฿<?php echo number_format($menu['base_price'], 0); ?></div>
                                <?php if ($menu['description']): ?>
                                    <p class="text-muted small"><?php echo htmlspecialchars($menu['description']); ?></p>
                                <?php endif; ?>
                                
                                <div class="quantity-control">
                                    <button class="quantity-btn minus">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="99">
                                    <button class="quantity-btn plus">+</button>
                                </div>
                                
                                <button class="add-to-cart-btn" 
                                        data-id="<?php echo $menu['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($menu['name']); ?>"
                                        data-base-price="<?php echo $menu['base_price']; ?>"
                                        data-has-protein="0">
                                    <i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <div class="cart-float">
        <button class="cart-button" id="cartButton" onclick="window.location.href='food_cart.php'">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge" id="cartBadge">0</span>
        </button>
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
            <p class="text-center mb-0">&copy; 2025 ช้องนาง เรสซิเดนซ์ - Hotel Booking System</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load cart count from localStorage
            updateCartBadge();

            // Protein button selection
            $('.protein-btn').click(function() {
                $(this).siblings('.protein-btn').removeClass('active');
                $(this).addClass('active');
            });

            // Quantity controls
            $('.minus').click(function() {
                const input = $(this).siblings('.quantity-input');
                const value = parseInt(input.val());
                if (value > 1) {
                    input.val(value - 1);
                }
            });

            $('.plus').click(function() {
                const input = $(this).siblings('.quantity-input');
                const value = parseInt(input.val());
                if (value < 99) {
                    input.val(value + 1);
                }
            });

            // Add to cart
            $('.add-to-cart-btn').click(function() {
                const card = $(this).closest('.menu-card-body');
                const menuId = $(this).data('id');
                const menuName = $(this).data('name');
                const basePrice = parseFloat($(this).data('base-price'));
                const hasProtein = $(this).data('has-protein');
                const quantity = parseInt(card.find('.quantity-input').val());
                
                let protein = 'none';
                let proteinPrice = 0;
                let proteinLabel = '';
                
                if (hasProtein) {
                    const activeProtein = card.find('.protein-btn.active');
                    protein = activeProtein.data('protein');
                    proteinPrice = parseFloat(activeProtein.data('price')) || 0;
                    
                    if (protein === 'pork') proteinLabel = 'หมู';
                    else if (protein === 'chicken') proteinLabel = 'ไก่';
                    else if (protein === 'seafood') proteinLabel = 'ทะเล';
                }
                
                const unitPrice = basePrice + proteinPrice;
                const subtotal = unitPrice * quantity;
                
                // Create cart item
                const cartItem = {
                    menuId: menuId,
                    menuName: menuName,
                    protein: protein,
                    proteinLabel: proteinLabel,
                    quantity: quantity,
                    unitPrice: unitPrice,
                    subtotal: subtotal
                };
                
                // Get existing cart
                let cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
                
                // Check if item already exists
                const existingIndex = cart.findIndex(item => 
                    item.menuId === menuId && item.protein === protein
                );
                
                if (existingIndex >= 0) {
                    cart[existingIndex].quantity += quantity;
                    cart[existingIndex].subtotal = cart[existingIndex].unitPrice * cart[existingIndex].quantity;
                } else {
                    cart.push(cartItem);
                }
                
                // Save cart
                localStorage.setItem('foodCart', JSON.stringify(cart));
                
                // Update badge
                updateCartBadge();
                
                // Show feedback
                $(this).html('<i class="fas fa-check"></i> เพิ่มแล้ว!');
                setTimeout(() => {
                    $(this).html('<i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า');
                }, 1000);
                
                // Reset quantity
                card.find('.quantity-input').val(1);
            });

            function updateCartBadge() {
                const cart = JSON.parse(localStorage.getItem('foodCart') || '[]');
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                $('#cartBadge').text(totalItems);
                
                if (totalItems === 0) {
                    $('#cartBadge').hide();
                } else {
                    $('#cartBadge').show();
                }
            }
        });
    </script>
</body>
</html>
