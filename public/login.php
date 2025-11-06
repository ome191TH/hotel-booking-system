<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }
    $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                        <h4><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if(isset($_GET['success'])): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle"></i> สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ</div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> อีเมล</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> รหัสผ่าน</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</button>
                        </form>
                        <hr>
                        <p class="text-center">ยังไม่มีบัญชี? <a href="register.php"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a></p>
                        <p class="text-center"><a href="index.php"><i class="fas fa-home"></i> กลับหน้าแรก</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>