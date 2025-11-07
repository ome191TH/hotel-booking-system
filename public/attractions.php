<?php
session_start();
require_once('../config/db.php');
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานที่ท่องเที่ยว - ช้องนาง เรสซิเดนซ์</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .attractions-hero {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .attractions-hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .attractions-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .attraction-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(123, 44, 191, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            height: 100%;
        }

        .attraction-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(123, 44, 191, 0.2);
        }

        .attraction-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: linear-gradient(135deg, #E0AAFF 0%, #C77DFF 100%);
        }

        .attraction-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-purple);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .attraction-body {
            padding: 25px;
        }

        .attraction-title {
            color: var(--primary-purple);
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .attraction-description {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .attraction-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #666;
            font-size: 0.9rem;
        }

        .attraction-info i {
            color: var(--secondary-purple);
            margin-right: 10px;
            width: 20px;
        }

        .category-filter {
            margin-bottom: 30px;
            text-align: center;
        }

        .category-btn {
            background: white;
            border: 2px solid var(--primary-purple);
            color: var(--primary-purple);
            padding: 10px 25px;
            border-radius: 25px;
            margin: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .category-btn:hover,
        .category-btn.active {
            background: var(--primary-purple);
            color: white;
        }

        .map-container {
            margin-top: 50px;
            padding: 40px 0;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .map-container h3 {
            color: var(--primary-purple);
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
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
                        <a class="nav-link" href="attractions.php"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยว</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="food_menu.php"><i class="fas fa-utensils"></i> สั่งอาหาร</a>
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
    <div class="attractions-hero">
        <div class="container">
            <h1><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยวใกล้โรงแรม</h1>
            <p>ค้นพบความงามของจังหวัดชุมพร เมืองท่าแห่งอันดามัน</p>
        </div>
    </div>

    <div class="container">
        <!-- Category Filter -->
        <div class="category-filter">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-globe"></i> ทั้งหมด
            </button>
            <button class="category-btn" data-category="beach">
                <i class="fas fa-umbrella-beach"></i> ชายหาด
            </button>
            <button class="category-btn" data-category="nature">
                <i class="fas fa-tree"></i> ธรรมชาติ
            </button>
            <button class="category-btn" data-category="temple">
                <i class="fas fa-place-of-worship"></i> วัด/ศาสนสถาน
            </button>
            <button class="category-btn" data-category="activity">
                <i class="fas fa-hiking"></i> กิจกรรม
            </button>
        </div>

        <!-- Attractions Grid -->
        <div class="row" id="attractionsContainer">
            <!-- หาดทุ่งวุ้าแล้ว -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="beach">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-star"></i> แนะนำ</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">หาดทุ่งวุ้าแล้ว</h5>
                        <p class="attraction-description">
                            ชายหาดที่สวยงาม น้ำทะเลใส ทรายขาวนุ่ม เหมาะสำหรับการเล่นน้ำและพักผ่อน มีร้านอาหารทะเลสดใหม่ริมชายหาด
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 5 กม. (10 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: ตลอด 24 ชั่วโมง</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- อุทยานแห่งชาติหมู่เกอะห์สุรินทร์ -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="nature">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-leaf"></i> ธรรมชาติ</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">อุทยานแห่งชาติหมู่เกาะชุมพร</h5>
                        <p class="attraction-description">
                            หมู่เกาะสวยงามกว่า 40 เกาะ มีเกาะงาม เกาะหลักและเกาะแรด เหมาะสำหรับดำน้ำดูปะการัง และนั่งเรือชมธรรมชาติ
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 15 กม. (25 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: 08:00 - 17:00 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- หาดสายรี -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="beach">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-umbrella-beach"></i> ชายหาด</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">หาดสายรี</h5>
                        <p class="attraction-description">
                            ชายหาดที่เงียบสงบ เหมาะสำหรับผู้ที่ต้องการความสงบและพักผ่อน มีต้นสนริมชายหาดให้ร้มรื่น วิวพระอาทิตย์ตกสวยงาม
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 8 กม. (15 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: ตลอด 24 ชั่วโมง</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- วัดพระบรมธาตุไชยา -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="temple">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-place-of-worship"></i> ศาสนสถาน</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">วัดพระบรมธาตุไชยา</h5>
                        <p class="attraction-description">
                            วัดเก่าแก่มีประวัติศาสตร์ยาวนาน มีเจดีย์สถาปัตยกรรมสไตล์ศรีวิชัย เป็นแหล่งท่องเที่ยวเชิงวัฒนธรรมที่สำคัญ
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 12 กม. (20 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: 06:00 - 18:00 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- น้ำตกสวนสน -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="nature">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-water"></i> น้ำตก</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">น้ำตกสวนสน</h5>
                        <p class="attraction-description">
                            น้ำตกสวยงามท่ามกลางป่าเขาใหญ่ มีบรรยากาศร่มรื่น น้ำใส เหมาะสำหรับเล่นน้ำและปิกนิกกับครอบครัว
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 25 กม. (35 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: 08:00 - 17:00 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- อ่าวคุ้งกระเบน -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="activity">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-fish"></i> กิจกรรม</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">อ่าวคุ้งกระเบน</h5>
                        <p class="attraction-description">
                            สถานที่ดำน้ำดูปะการังและชีวิตใต้ทะเล มีบริการเรือนำเที่ยว อุปกรณ์ดำน้ำให้เช่า เหมาะสำหรับผู้ที่ชื่นชอบกิจกรรมทางน้ำ
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 18 กม. (30 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: 07:00 - 16:00 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- เขาแม่เจดีย์ -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="nature">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-mountain"></i> ยอดเขา</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">เขาแม่เจดีย์</h5>
                        <p class="attraction-description">
                            จุดชมวิวที่สูงที่สุดในชุมพร มองเห็นทะเลอันดามันและอ่าวไทยในเวลาเดียวกัน เหมาะสำหรับถ่ายภาพและชมพระอาทิตย์ขึ้น
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 22 กม. (40 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: 05:00 - 18:00 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ตลาดสดเทศบาล -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="activity">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-shopping-basket"></i> ตลาด</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">ตลาดสดเทศบาลชุมพร</h5>
                        <p class="attraction-description">
                            ตลาดสดที่มีสินค้าพื้นเมืองมากมาย อาหารทะเลสด ผลไม้ตามฤดูกาล ของฝากชุมพร เปิดตั้งแต่เช้าตรู่
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 3 กม. (7 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: 05:00 - 12:00 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- หาดอรุณโรจน์ -->
            <div class="col-md-6 col-lg-4 attraction-item" data-category="beach">
                <div class="card attraction-card">
                    <div class="position-relative">
                        <div class="attraction-image"></div>
                        <span class="attraction-badge"><i class="fas fa-sun"></i> Sunset</span>
                    </div>
                    <div class="attraction-body">
                        <h5 class="attraction-title">หาดอรุณโรจน์</h5>
                        <p class="attraction-description">
                            ชายหาดที่มีวิวพระอาทิตย์ตกสวยงาม มีทางเดินริมชายหาดยาว เหมาะสำหรับเดิน-วิ่งออกกำลังกาย และถ่ายภาพยามเย็น
                        </p>
                        <div class="attraction-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>ระยะทาง: 6 กม. (12 นาที)</span>
                        </div>
                        <div class="attraction-info">
                            <i class="fas fa-clock"></i>
                            <span>เปิด: ตลอด 24 ชั่วโมง</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-container">
            <div class="container">
                <h3><i class="fas fa-map"></i> แผนที่สถานที่ท่องเที่ยวชุมพร</h3>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> 
                            <strong>เคล็ดลับ:</strong> โรงแรมของเราตั้งอยู่ในทำเลที่สะดวกต่อการเดินทางไปยังสถานที่ท่องเที่ยวทุกแห่ง
                            พนักงานสามารถแนะนำเส้นทางและช่วยจัดรถเช่าให้ได้ค่ะ
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-purple"><i class="fas fa-car"></i> การเดินทาง</h5>
                        <ul>
                            <li>รถยนต์ส่วนตัว - สะดวกที่สุด</li>
                            <li>รถเช่า - มีบริการในเมืองชุมพร</li>
                            <li>รถจักรยานยนต์เช่า - 200-300 บาท/วัน</li>
                            <li>Taxi/Grab - เรียกได้ทุกเวลา</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-purple"><i class="fas fa-lightbulb"></i> คำแนะนำ</h5>
                        <ul>
                            <li>ควรออกเดินทางตั้งแต่เช้า</li>
                            <li>เตรียมครีมกันแดดและหมวก</li>
                            <li>นำน้ำดื่มติดตัวเสมอ</li>
                            <li>ตรวจสอบสภาพอากาศก่อนเดินทาง</li>
                        </ul>
                    </div>
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
                    <p>โทร: 077-123-456<br>อีเมล: info@chongnang.com</p>
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
            // Category filter
            $('.category-btn').click(function() {
                $('.category-btn').removeClass('active');
                $(this).addClass('active');
                
                const category = $(this).data('category');
                
                if (category === 'all') {
                    $('.attraction-item').fadeIn();
                } else {
                    $('.attraction-item').hide();
                    $('.attraction-item[data-category="' + category + '"]').fadeIn();
                }
            });

            // Smooth scroll
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.hash);
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 70
                    }, 800);
                }
            });
        });
    </script>
</body>
</html>
