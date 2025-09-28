<?php
require_once __DIR__ . "/../../../config/connect.php";

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token    = $_POST['token'];
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $error = "Mật khẩu nhập lại không khớp.";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải ít nhất 6 ký tự.";
    } else {
        // Kiểm tra token
        $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        if (!$row || strtotime($row['expires_at']) < time()) {
            $error = "Token không hợp lệ hoặc đã hết hạn.";
        } else {
            // Cập nhật mật khẩu
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = :pass WHERE email = :email");
            $stmt->execute(['pass' => $hash, 'email' => $row['email']]);

            // Xóa token sau khi reset thành công
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute(['email' => $row['email']]);

            $success = "Đặt lại mật khẩu thành công! <a href='login.php'>Đăng nhập ngay</a>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đặt lại mật khẩu</title>
</head>
<body>
  <div class="menu">
    <h2>Đặt lại mật khẩu</h2>

    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p style="color:green;"><?= $success ?></p>
    <?php else: ?>
    <form method="POST" action="">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
      <input type="password" name="password" placeholder="Mật khẩu mới" required />
      <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required />
      <button type="submit">Cập nhật mật khẩu</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>
