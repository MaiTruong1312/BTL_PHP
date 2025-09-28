<?php
session_start();
require_once __DIR__ . "/../../../config/connect.php";
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Kiểm tra tài khoản theo email hoặc username
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Đúng mật khẩu
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['role'] = $user['role'];

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
        required
      />
      <input
        type="password"
        name="password"
        placeholder="Nhập mật khẩu"
        required
      />
      <button type="submit">Đăng nhập</button>
    </form>

    <a href="forgot_password.php" class="forgotpass">Bạn quên mật khẩu?</a><br />
    <a href="register.php" class="createacc">Bạn chưa có tài khoản?</a>
  </div>
  <div class="BAer-container"></div>
</body>
</html>
