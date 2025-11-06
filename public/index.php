<?php
session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking System - ระบบจองห้องพักออนไลน์</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php"><i class="fas fa-bed"></i> จองห้องพัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attractions.php"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยว</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="my_bookings.php"><i class="fas fa-list"></i> การจองของฉัน</a>
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

    <!-- Hero Section -->
    <div class="hero" style="padding: 120px 0;">
        <div class="container text-center">
            <h1 class="display-3 mb-4">ยินดีต้อนรับสู่โรงแรมของเรา</h1>
            <p class="lead mb-5">ประสบการณ์พักผ่อนสุดพิเศษ ในราคาที่คุณคู่ควร</p>
            <a href="rooms.php" class="btn btn-light btn-lg px-5 py-3">
                <i class="fas fa-bed mr-2"></i> เลือกห้องพักของคุณ
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container my-5 py-5">
        <h2 class="text-center mb-5">✨ ทำไมต้องเลือกเรา</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3" style="font-size: 3rem;">🏨</div>
                        <h4>ห้องพักหลากหลาย</h4>
                        <p class="text-muted">มีห้องพักให้เลือกหลายประเภท ตอบโจทย์ทุกความต้องการ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3" style="font-size: 3rem;">💰</div>
                        <h4>ราคาย่อมเยา</h4>
                        <p class="text-muted">ราคาที่เป็นธรรม คุ้มค่ากับทุกการเข้าพัก</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3" style="font-size: 3rem;">📱</div>
                        <h4>จองง่าย สะดวกรวดเร็ว</h4>
                        <p class="text-muted">จองออนไลน์ได้ทุกที่ ทุกเวลา ง่ายๆ ผ่านเว็บไซต์</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-4">🌟 เกี่ยวกับเรา</h2>
                    <h4 class="text-primary mb-3">ช้องนาง เรสซิเดนซ์</h4>
                    <p class="lead">โรงแรมของเราให้บริการห้องพักที่สะอาด สะดวกสบาย พร้อมสิ่งอำนวยความสะดวกครบครัน</p>
                    <p>เรามุ่งมั่นที่จะมอบประสบการณ์การพักผ่อนที่ดีที่สุดให้กับลูกค้าทุกท่าน ด้วยบริการที่เป็นมิตร และราคาที่เหมาะสม</p>
                    
                    <div class="mt-4">
                        <h5 class="mb-3">📍 ที่อยู่</h5>
                        <p class="mb-1">146 พิศิษฐ์พยาบาล</p>
                        <p class="mb-1">ตำบลท่าตะเภา อำเภอเมืองชุมพร</p>
                        <p class="mb-1">จังหวัดชุมพร 86000</p>
                        <p class="mb-3">ประเทศไทย</p>
                        <p class="mb-1">📞 โทร: 077511218</p>
                    </div>
                    
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> ห้องพักสะอาด ทันสมัย</li>
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> สิ่งอำนวยความสะดวกครบถ้วน</li>
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Wi-Fi ฟรี ความเร็วสูง</li>
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> บริการอาหารเช้า</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600" 
                         alt="Hotel" 
                         class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Attractions Section -->
    <div class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4"><i class="fas fa-map-marked-alt"></i> สถานที่ท่องเที่ยวใกล้โรงแรม</h2>
            <p class="text-center lead mb-5">ค้นพบความงามของจังหวัดชุมพร เมืองท่าแห่งอันดามัน</p>
            
            <div class="row">
                <!-- หาดทุ่งวุ้าแล้ว -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center">
                            <div class="mb-3" style="font-size: 3rem; color: #7B2CBF;">
                                <i class="fas fa-umbrella-beach"></i>
                            </div>
                            <h5 class="card-title" style="color: #7B2CBF;">หาดทุ่งวุ้าแล้ว</h5>
                            <p class="card-text text-muted">ชายหาดที่สวยงาม น้ำทะเลใส ทรายขาวนุ่ม เหมาะสำหรับการเล่นน้ำและพักผ่อน</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt text-danger"></i> <strong>5 กม.</strong> (10 นาที)</p>
                        </div>
                    </div>
                </div>

                <!-- อุทยานแห่งชาติหมู่เกาะชุมพร -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center">
                            <div class="mb-3" style="font-size: 3rem; color: #7B2CBF;">
                                <i class="fas fa-fish"></i>
                            </div>
                            <h5 class="card-title" style="color: #7B2CBF;">หมู่เกาะชุมพร</h5>
                            <p class="card-text text-muted">หมู่เกาะสวยงามกว่า 40 เกาะ เหมาะสำหรับดำน้ำดูปะการังและนั่งเรือชมธรรมชาติ</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt text-danger"></i> <strong>15 กม.</strong> (25 นาที)</p>
                        </div>
                    </div>
                </div>

                <!-- เขาแม่เจดีย์ -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center">
                            <div class="mb-3" style="font-size: 3rem; color: #7B2CBF;">
                                <i class="fas fa-mountain"></i>
                            </div>
                            <h5 class="card-title" style="color: #7B2CBF;">เขาแม่เจดีย์</h5>
                            <p class="card-text text-muted">จุดชมวิวที่สูงที่สุดในชุมพร มองเห็นทะเลอันดามันและอ่าวไทยในเวลาเดียวกัน</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt text-danger"></i> <strong>22 กม.</strong> (40 นาที)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="attractions.php" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-map-marked-alt mr-2"></i> ดูสถานที่ท่องเที่ยวทั้งหมด
                </a>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="container text-center my-5 py-5">
        <h2 class="mb-4">พร้อมจองห้องพักแล้วหรือยัง?</h2>
        <p class="lead mb-4">เลือกห้องพักที่ใช่สำหรับคุณวันนี้</p>
        <a href="rooms.php" class="btn btn-primary btn-lg px-5 py-3">
            <i class="fas fa-calendar-check mr-2"></i> ดูห้องพักทั้งหมด
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
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