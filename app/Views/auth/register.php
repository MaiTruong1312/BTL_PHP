<?php
session_start();
require_once __DIR__ . "/../../../config/connect.php"; 

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // --- Kiểm tra hợp lệ ---
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không đúng định dạng.";
    } elseif (!preg_match('/^[0-9]{9,11}$/', $phone) && !empty($phone)) {
        $error = "Số điện thoại không hợp lệ (9-11 chữ số).";
    } elseif ($password !== $confirm) {
        $error = "Mật khẩu nhập lại không khớp.";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải ít nhất 6 ký tự.";
    } else {
        // --- Kiểm tra email đã tồn tại chưa ---
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            $error = "Email đã tồn tại. Vui lòng dùng email khác.";
        } else {
            // --- Lưu user mới ---
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password_hash, role) 
                                    VALUES (:name, :email, :phone, :address, :password_hash, 'user')");
            $result = $stmt->execute([
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'address'       => $address,
                'password_hash' => $hash
            ]);

            if ($result) {
                $success = "Đăng ký thành công! Bạn có thể <a href='login.php'>đăng nhập</a> ngay.";
            } else {
                $error = "Có lỗi xảy ra khi tạo tài khoản.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Đăng ký</title>
  <link rel="stylesheet" href="/project-root/public/assets/css/login.css" />
</head>
<body class="main">
  <div class="menu">
    <h2>Đăng Ký</h2>

    <?php if ($error): ?>
      <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p style="color: green; text-align: center;"><?= $success ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
      <input type="text" name="name" placeholder="Họ và tên" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="text" name="phone" placeholder="Số điện thoại" />
      <input type="text" name="address" placeholder="Địa chỉ" />
      <input type="password" name="password" placeholder="Mật khẩu" required />
      <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required />
      <button type="submit">Đăng ký</button>
    </form>

    <p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
  </div>
    <script src="/project-root/public/assets/js/login.js" defer></script>
    <div class="BAer-container"></div>
</body>
</html>
