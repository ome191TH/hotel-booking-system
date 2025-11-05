<?php
include '../config/db.php';

// Get room ID from URL
if (isset($_GET['id'])) {
    $room_id = intval($_GET['id']);
    
    // Fetch room details from the database
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
    } else {
        echo "Room not found.";
        exit;
    }
} else {
    echo "Invalid room ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room['name']; ?> - Room Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1><?php echo $room['name']; ?></h1>
        <img src="../assets/images/<?php echo $room['image']; ?>" alt="<?php echo $room['name']; ?>" class="img-fluid">
        <p><strong>Price:</strong> <?php echo number_format($room['price'], 2); ?> THB per night</p>
        <p><strong>Details:</strong> <?php echo $room['details']; ?></p>
        <p><strong>Available Rooms:</strong> <?php echo $room['available_rooms']; ?></p>
        <a href="booking_form.php?id=<?php echo $room['id']; ?>" class="btn btn-primary">Book Now</a>
        <a href="index.php" class="btn btn-secondary">Back to Rooms</a>
    </div>

    <script src="../assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>