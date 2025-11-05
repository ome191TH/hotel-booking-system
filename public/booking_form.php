<?php
// booking_form.php

// Include database connection
include '../config/db.php';

// Fetch available rooms for the dropdown
$query = "SELECT * FROM rooms WHERE available_rooms > 0";
$result = mysqli_query($conn, $query);
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Booking Form</h2>
        <form action="booking_save.php" method="POST">
            <div class="form-group">
                <label for="room">Select Room:</label>
                <select class="form-control" id="room" name="room_id" required>
                    <option value="">Choose a room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo $room['id']; ?>"><?php echo $room['name']; ?> - $<?php echo $room['price']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="phone">Your Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="checkin_date">Check-in Date:</label>
                <input type="date" class="form-control" id="checkin_date" name="checkin_date" required>
            </div>
            <div class="form-group">
                <label for="checkout_date">Check-out Date:</label>
                <input type="date" class="form-control" id="checkout_date" name="checkout_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Book Now</button>
        </form>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>