<?php
session_start();
require_once '../config/db.php';

$query = "SELECT * FROM rooms WHERE available_rooms > 0";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking System</title>
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
                    <span class="text-white mr-3">สวัสดี, <?php echo $_SESSION['username']; ?></span>
                    <a href="my_bookings.php" class="btn btn-outline-light mr-2">การจองของฉัน</a>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a href="admin.php" class="btn btn-outline-warning mr-2">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline-danger">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light mr-2">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-outline-success">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1>Welcome to Our Hotel</h1>
            <p>Find your perfect room for a comfortable stay</p>
        </div>
    </div>

    <!-- Rooms -->
    <div class="container">
        <h2 class="text-center mb-5">Available Rooms</h2>
        <div class="row">
            <?php while ($room = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="../assets/images/<?php echo $room['image']; ?>" class="card-img-top" alt="<?php echo $room['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $room['name']; ?></h5>
                            <p class="card-text"><?php echo $room['details']; ?></p>
                            <p class="card-text"><strong>Price: </strong><?php echo number_format($room['price'], 2); ?> THB/night</p>
                            <p class="card-text"><small class="text-muted">Available: <?php echo $room['available_rooms']; ?> rooms</small></p>
                            <a href="room_detail.php?id=<?php echo $room['id']; ?>" class="btn btn-primary btn-block">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>