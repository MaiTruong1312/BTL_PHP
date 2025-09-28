<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/connect.php";
require_once __DIR__ . "/../vendor/autoload.php"; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$user    = $_SESSION['user'];
$step    = "form"; // form | otp-info | otp-pass
$success = $error = "";

/* ---------- HÀM HỖ TRỢ ---------- */
function generateOTP($len = 6) {
    $chars = '0123456789';
    $otp   = '';
    for ($i = 0; $i < $len; $i++) {
        $otp .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $otp;
}

function sendOTP($to, $otp, $type = "Cập nhật") {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "progamevip2310@gmail.com"; // sửa bằng app password
        $mail->Password   = "oesi zfoa xdnd pkuz";       // sửa bằng app password
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;

        $mail->setFrom("yourgmail@gmail.com", "Computer Store");
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = "Ma OTP $type";
        $mail->Body    = "<p>Mã OTP của bạn là: <b>$otp</b></p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/* ---------- FORM CẬP NHẬT THÔNG TIN ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_update_info'])) {
    $_SESSION['pending_update'] = [
        'type'    => 'info',
        'name'    => $_POST['name'],
        'email'   => $_POST['email'],
        'phone'   => $_POST['phone'],
        'address' => $_POST['address']
    ];
    $otp = generateOTP(6);
    $_SESSION['update_otp'] = $otp;

    if (sendOTP($user['email'], $otp, "thông tin cá nhân")) {
        $step = "otp-info";
    } else {
        $error = "Không thể gửi email. Vui lòng thử lại!";
    }
}

/* ---------- FORM ĐỔI MẬT KHẨU ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_update_pass'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Kiểm tra mật khẩu hiện tại
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id=?");
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($current, $row['password_hash'])) {
        $error = "Mật khẩu hiện tại không đúng!";
    } elseif ($new !== $confirm) {
        $error = "Mật khẩu mới và xác nhận không khớp!";
    } elseif (strlen($new) < 6) {
        $error = "Mật khẩu mới phải từ 6 ký tự!";
    } else {
        $_SESSION['pending_update'] = [
            'type'     => 'password',
            'password' => password_hash($new, PASSWORD_DEFAULT)
        ];
        $otp = generateOTP(6);
        $_SESSION['update_otp'] = $otp;

        if (sendOTP($user['email'], $otp, "đổi mật khẩu")) {
            $step = "otp-pass";
        } else {
            $error = "Không thể gửi email xác thực!";
        }
    }
}

/* ---------- XÁC NHẬN OTP ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    if ($_POST['otp'] === ($_SESSION['update_otp'] ?? '')) {
        $data = $_SESSION['pending_update'];

        if ($data['type'] === 'info') {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address'], $user['id']]);
            $_SESSION['user'] = array_merge($_SESSION['user'], $data);
            $success = "Cập nhật thông tin thành công!";
        } elseif ($data['type'] === 'password') {
            $stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
            $stmt->execute([$data['password'], $user['id']]);
            $success = "Đổi mật khẩu thành công!";
        }

        unset($_SESSION['update_otp'], $_SESSION['pending_update']);
        $step = "form";
    } else {
        $error = "Mã xác thực không đúng!";
    }
}
?>
<style>
body.main {
  margin: 0;
  background: linear-gradient(-45deg, #1e1e2f, #2a2a3a, #1a1a2a, #2e2e4a);
  background-size: 400% 400%;
  animation: gradientBG 15s ease infinite;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #fff;

}

@keyframes gradientBG {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.menu.contain {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(15px);
  padding: 30px 40px;
  border-radius: 20px;
  box-shadow: 0 0 40px rgba(0, 123, 255, 0.2);
  max-width: 600px;
  margin: 0 auto;
  text-align: left;
  border-width: 15px;
}

.menu.contain h2 {
  margin-bottom: 25px;
  font-size: 22px;
  text-align: center;
  color: #fff;
  text-shadow: 0 0 6px rgba(255, 255, 255, 0.2);
}

.menu.contain label {
  font-weight: 600;
  margin-top: 10px;
  margin-bottom: 5px;
  display: block;
  color: #ddd;
}

.menu.contain input[type="text"],
.menu.contain input[type="email"] {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: rgba(255,255,255,0.1);
  color: #fff;
  font-size: 15px;
  box-shadow: inset 0 0 10px rgba(0,0,0,0.3);
  transition: background 0.3s ease, box-shadow 0.3s ease;
  margin-bottom: 15px;
}

.menu.contain input:focus {
  background: rgba(255,255,255,0.2);
  box-shadow: 0 0 10px rgba(0,123,255,0.6);
  outline: none;
}

.menu.contain button {
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(to right, #0d6efd, #66b2ff);
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
  box-shadow: 0 0 15px rgba(0, 123, 255, 0.4);
  transition: all 0.3s ease;
  width: 100%;
}

.menu.contain button:hover {
  background: linear-gradient(to right, #1a75ff, #80ccff);
  box-shadow: 0 0 25px rgba(0,123,255,0.6);
}

p.success {
  color: #90ee90;
  font-weight: 600;
  text-align: center;
}
</style>


<?php include __DIR__ . "/../app/Views/layouts/header.php"; ?>
<body class="main">
<div class="menu contain">
  <h2>Thông tin người dùng</h2>

  <?php if ($success): ?>
    <p class="success"><?= $success ?></p>
  <?php endif; ?>
  <?php if ($error): ?>
    <p class="error"><?= $error ?></p>
  <?php endif; ?>

  <?php if ($step === "form"): ?>
    <!-- Bảng cập nhật thông tin -->
    <h3>📌 Cập nhật thông tin</h3>
    <form method="POST">
      <label for="name">Họ tên:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

      <label for="email">Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

      <label for="phone">Số điện thoại:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

      <label for="address">Địa chỉ:</label>
      <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">

      <button type="submit" name="start_update_info">Cập nhật</button>
    </form>

    <hr style="margin:25px 0; border-color:#444;">

    <!-- Bảng đổi mật khẩu -->
    <div class="card">
  <h3>Đổi mật khẩu</h3>

  <?php if ($error && isset($_POST['start_update_pass'])): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  
  <form method="POST">
    <label for="current_password">Mật khẩu hiện tại:</label>
    <input type="password" name="current_password" id="current_password" required>

    <label for="new_password">Mật khẩu mới:</label>
    <input type="password" name="new_password" id="new_password" required>

    <label for="confirm_password">Xác nhận mật khẩu mới:</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <button type="submit" name="start_update_pass">Đổi mật khẩu</button>
  </form>
</div>


  <?php elseif ($step === "otp-info" || $step === "otp-pass"): ?>
    <!-- Nhập OTP -->
    <form method="POST">
      <label>Nhập mã xác thực đã gửi về email:</label>
      <input type="text" name="otp" required>
      <button type="submit" name="verify_otp">Xác nhận</button>
    </form>
  <?php endif; ?>
</div>
</body>
<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
