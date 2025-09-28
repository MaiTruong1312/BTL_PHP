<?php
session_start();
require_once __DIR__ . "/../config/connect.php"; 
$title = "Thanh toán";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../app/views/auth/login.php?redirect=checkout.php");
    exit();
}
include __DIR__ . "/../app/Views/layouts/header.php";

// Nếu giỏ hàng trống → quay về cart
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $payment  = $_POST['payment'] ?? 'cod';

    if ($fullname && $email && $phone && $address) {
        // Gom thông tin giao hàng vào 1 field
        $shipping_address = "$fullname - $phone - $email\n$address";

        // Tính tổng tiền
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $conn->beginTransaction();

        try {
            // 1. Insert vào bảng orders
           $stmt = $conn->prepare("
    INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method) 
    VALUES (:user_id, :total, 'pending', :shipping_address, :payment_method)
");

$userId = $_SESSION['user_id'] ?? null;

$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindParam(':total', $total, PDO::PARAM_INT);
$stmt->bindParam(':shipping_address', $shipping_address, PDO::PARAM_STR);
$stmt->bindParam(':payment_method', $payment, PDO::PARAM_STR);

$stmt->execute();
$orderId = $conn->lastInsertId();


            // 2. Insert vào bảng order_items
           $stmtItem = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
        VALUES (:order_id, :product_id, :qty, :price)
    ");

    foreach ($_SESSION['cart'] as $item) {
        $stmtItem->execute([
            ':order_id'   => $orderId,
            ':product_id' => $item['id'],
            ':qty'        => $item['qty'],
            ':price'      => $item['price'],
        ]);
    }

    $conn->commit();

            // Xóa giỏ hàng
            $_SESSION['cart'] = [];

            // Redirect cảm ơn
            header("Location: thank_you.php?order_id=$orderId");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "❌ Có lỗi xảy ra khi đặt hàng: " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Vui lòng điền đầy đủ thông tin giao hàng!";
    }
}
?>

<div class="container">
  <h2>📝 Thanh toán</h2>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="checkout.php" class="checkout-form">
    <label>Họ tên:</label>
    <input type="text" name="fullname" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Số điện thoại:</label>
    <input type="text" name="phone" required>

    <label>Địa chỉ giao hàng:</label>
    <textarea name="address" rows="3" required></textarea>

    <label>Phương thức thanh toán:</label>
    <select name="payment">
      <option value="cod">Thanh toán khi nhận hàng (COD)</option>
      <option value="bank">Chuyển khoản ngân hàng</option>
      <option value="paypal">PayPal</option>
      <option value="momo">MoMo</option>
    </select>

    <h3>🛒 Tóm tắt đơn hàng</h3>
    <table class="cart-table">
      <tr>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Thành tiền</th>
      </tr>
      <?php $total = 0; foreach ($_SESSION['cart'] as $item): 
        $subtotal = $item['price'] * $item['qty'];
        $total += $subtotal;
      ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= $item['qty'] ?></td>
          <td><?= number_format($subtotal, 0, ',', '.') ?> VNĐ</td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="2"><b>Tổng cộng</b></td>
        <td><b><?= number_format($total, 0, ',', '.') ?> VNĐ</b></td>
      </tr>
    </table>

    <button type="submit" class="btn checkout">✅ Đặt hàng</button>
  </form>
</div>

<?php include __DIR__ . "/../app/Views/layouts/footer.php"; ?>
