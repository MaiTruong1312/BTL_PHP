<?php
session_start();
require_once __DIR__ . "/../../../config/connect.php";
$error = '';

// Lấy email từ cookie nếu có
$email_cookie = $_COOKIE['remember_email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Lưu user vào session
        $_SESSION['user'] = [
            'id'      => $user['id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'role'    => $user['role'],
            'phone'   => $user['phone'],
            'address' => $user['address']
        ];

        // Nếu tick "ghi nhớ đăng nhập" thì lưu cookie
        if (!empty($_POST['remember'])) {
            setcookie("remember_email", $user['email'], time() + (86400 * 30), "/"); // 30 ngày
        } else {
            // Nếu không tick thì xoá cookie cũ (nếu có)
            setcookie("remember_email", "", time() - 3600, "/");
        }

        header("Location: ../../../public/index.php");
        exit();
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Đăng nhập</title>
  <link rel="stylesheet" href="/project-root/public/assets/css/login.css" />
  <script src="/project-root/public/assets/js/login.js" defer></script>
</head>
<body class="main">
  <div class="menu">
    <h2>Đăng Nhập</h2>

    <?php if ($error): ?>
      <p style="color: red; text-align: center;">
        <?= htmlspecialchars($error) ?>
      </p>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <input
        type="text"
        name="username"
        placeholder="Email đăng nhập"
        value="<?= htmlspecialchars($email_cookie) ?>"
        required
      />
      <input
        type="password"
        name="password"
        placeholder="Nhập mật khẩu"
        required
      />
      <label style="display:block; margin:10px 0; text-align:left; font-size:14px;">
        <input type="checkbox" name="remember" value="1"
          <?= $email_cookie ? 'checked' : '' ?>>
        Ghi nhớ đăng nhập
      </label>
      <button type="submit">Đăng nhập</button>
    </form>

    <a href="forgot_password.php" class="forgotpass">Bạn quên mật khẩu?</a><br />
    <a href="register.php" class="createacc">Bạn chưa có tài khoản?</a>
  </div>
  <div class="BAer-container"></div>
</body>
</html>
