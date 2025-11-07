<?php
session_start();
require_once('../config/db.php');

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: food_menu.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// รับข้อมูลจากฟอร์ม
$customer_name = trim($_POST['customer_name']);
$customer_phone = trim($_POST['customer_phone']);
$delivery_type = $_POST['delivery_type'];
$room_number = isset($_POST['room_number']) ? trim($_POST['room_number']) : null;
$delivery_address = isset($_POST['delivery_address']) ? trim($_POST['delivery_address']) : null;
$special_note = isset($_POST['special_note']) ? trim($_POST['special_note']) : null;
$cart_data = json_decode($_POST['cart_data'], true);

// Validation
if (empty($customer_name) || empty($customer_phone) || empty($cart_data)) {
    $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
    header("Location: food_checkout.php");
    exit();
}

if ($delivery_type === 'room' && empty($room_number)) {
    $_SESSION['error'] = "กรุณาระบุเลขห้องพัก";
    header("Location: food_checkout.php");
    exit();
}

if ($delivery_type === 'address' && empty($delivery_address)) {
    $_SESSION['error'] = "กรุณาระบุที่อยู่จัดส่ง";
    header("Location: food_checkout.php");
    exit();
}

// คำนวณราคารวม
$total_price = 0;
foreach ($cart_data as $item) {
    $total_price += $item['subtotal'];
}

// เริ่ม transaction
$conn->begin_transaction();

try {
    // บันทึกออเดอร์
    $stmt = $conn->prepare("INSERT INTO food_orders (user_id, customer_name, customer_phone, room_number, delivery_address, total_price, special_note, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("issssds", $user_id, $customer_name, $customer_phone, $room_number, $delivery_address, $total_price, $special_note);
    
    if (!$stmt->execute()) {
        throw new Exception("ไม่สามารถบันทึกออเดอร์ได้");
    }
    
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // บันทึกรายการอาหารในออเดอร์
    $item_stmt = $conn->prepare("INSERT INTO food_order_items (order_id, menu_id, menu_name, protein_type, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($cart_data as $item) {
        $menu_id = $item['menuId'];
        $menu_name = $item['menuName'];
        $protein = $item['protein'];
        $quantity = $item['quantity'];
        $unit_price = $item['unitPrice'];
        $subtotal = $item['subtotal'];
        
        $item_stmt->bind_param("iissids", $order_id, $menu_id, $menu_name, $protein, $quantity, $unit_price, $subtotal);
        
        if (!$item_stmt->execute()) {
            throw new Exception("ไม่สามารถบันทึกรายการอาหารได้");
        }
    }
    
    $item_stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // ส่ง email หรือ notification (optional - สามารถเพิ่มในอนาคต)
    
    // Clear cart from localStorage (จะทำผ่าน JavaScript)
    $_SESSION['success'] = "สั่งอาหารสำเร็จ! เลขที่ออเดอร์: " . $order_id;
    $_SESSION['order_id'] = $order_id;
    
    header("Location: food_order_success.php?order_id=" . $order_id);
    exit();
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    header("Location: food_checkout.php");
    exit();
}
?>
