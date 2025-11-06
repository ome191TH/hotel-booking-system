<?php
include '../config/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // ตรวจสอบอีเมลซ้ำ
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        $error = "อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // default role
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password_hash, $role);
        
        if ($stmt->execute()) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก กรุณาลองใหม่";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-user-plus"></i> สมัครสมาชิก</h4>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> ชื่อผู้ใช้</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> อีเมล</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> รหัสผ่าน</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> สมัครสมาชิก</button>
                        </form>
                        <hr>
                        <p class="text-center">มีบัญชีอยู่แล้ว? <a href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></p>
                        <p class="text-center"><a href="index.php"><i class="fas fa-home"></i> กลับหน้าแรก</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>