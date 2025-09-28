<?php
session_start();
require_once __DIR__ . "/../../../config/connect.php";

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ.";
    } else {
        // Kiểm tra email có tồn tại
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = "Email không tồn tại trong hệ thống.";
        } else {
            // Tạo token reset
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Lưu token vào DB
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) 
                                    VALUES (:email, :token, :expires_at)");
            $stmt->execute([
                'email' => $email,
                'token' => $token,
                'expires_at' => $expires
            ]);
            $reset_link = "http://localhost/project-root/app/Views/auth/reset_password.php?token=$token";
            $success = "Link đặt lại mật khẩu đã được gửi tới email của bạn.<br>
                        (Demo: <a href='$reset_link'>$reset_link</a>)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quên mật khẩu</title>
</head>
<body>
  <div class="menu">
    <h2>Quên mật khẩu</h2>

    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" action="forgot_password.php">
      <input type="email" name="email" placeholder="Nhập email của bạn" required />
      <button type="submit">Gửi link đặt lại mật khẩu</button>
    </form>

    <p><a href="login.php">Quay lại đăng nhập</a></p>
  </div>
</body>
</html>
